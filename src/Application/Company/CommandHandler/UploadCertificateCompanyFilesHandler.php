<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\RegisterCompanyToTraCommand;
use App\Application\Company\Command\UploadCertificateCompanyFilesCommand;
use App\Domain\Model\Company\Certificate;
use App\Domain\Model\Company\CertificateId;
use App\Domain\Model\Company\TaxIdentificationNumber;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Services\CertificateDataService;
use App\Domain\Services\FileUploaderService;
use App\Domain\Services\TraIntegrationService;
use App\Domain\Repository\CertificateRepository;
use App\Domain\Services\UploadCertificateToTraRegistrationRequest;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UploadCertificateCompanyFilesHandler
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var TraIntegrationService
     */
    private TraIntegrationService $traIntegrationService;

    /**
     * @var FileUploaderService
     */
    private FileUploaderService $fileUploaderService;

    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * @var CertificateRepository
     */
    private CertificateRepository $certificateRepository;

    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $messageBus;

    /** @var CertificateDataService  */
    private CertificateDataService $certificateDataService;

    /**
     * @param LoggerInterface $logger
     * @param FileUploaderService $fileUploaderService
     * @param CompanyRepository $companyRepository
     * @param CertificateRepository $certificateRepository
     * @param TraIntegrationService $traIntegrationService
     * @param MessageBusInterface $messageBus
     */
    public function __construct(
        LoggerInterface $logger,
        FileUploaderService $fileUploaderService,
        CompanyRepository $companyRepository,
        CertificateRepository $certificateRepository,
        TraIntegrationService $traIntegrationService,
        MessageBusInterface $messageBus,
        CertificateDataService $certificateDataService
    ) {
        $this->logger = $logger;
        $this->fileUploaderService = $fileUploaderService;
        $this->companyRepository = $companyRepository;
        $this->certificateRepository = $certificateRepository;
        $this->traIntegrationService = $traIntegrationService;
        $this->messageBus = $messageBus;
        $this->certificateDataService = $certificateDataService;
    }

    /**
     * @param UploadCertificateCompanyFilesCommand $command
     * @return array
     * @throws Exception
     */
    public function __invoke(UploadCertificateCompanyFilesCommand $command): array
    {
        $files = $command->getCompanyFiles();
        $tin = new TaxIdentificationNumber($command->getTin());

        $company = $this->companyRepository->findOneBy(['tin' => $tin->value()]);
        if (empty($company)) {
            $this->logger->critical(
                'Company not found',
                [
                    'tin' => $tin->value(),
                    'method' => __METHOD__
                ]
            );
            throw new Exception('Company not found');
        }

        $filesPath = [];
        $filesPack = [];

        $certificateValues = [];
        foreach ($files as $file) {
            $certificateId = CertificateId::generate();
            $filepath = $this->fileUploaderService->uploadFile($file, $tin);

            $certificateDataPath = $this->certificateDataService->createCertificateData($filepath);
            if (!empty($certificateDataPath)) {
                $values = json_decode(file_get_contents($certificateDataPath), true);
                $certificateValues = [
                    'certificateKey' => $company->serial(),
                    'certificatePassword' => $values['password'],
                    'tin' => $tin->value(),
                    'certificateSerial' => $values['serial'],
                ];

                $filesPath[] = $certificateDataPath;
                $fileCertificate = new Certificate(
                    CertificateId::generate(),
                    $tin,
                    $certificateDataPath
                );
                $filesPack[] = $fileCertificate;
            }

            $file = new Certificate(
                $certificateId,
                $tin,
                $filepath
            );

            $fileFound = $this->certificateRepository->findByFilePath($filepath);

            if (!empty($fileFound)) {
                $this->logger->critical(
                    'File is not unique',
                    [
                        'certificate_id' => $fileFound->getCertificateId(),
                        'tin' => $fileFound->getTin()->value(),
                        'file_path' => $fileFound->getFilepath(),
                        'method' => __METHOD__,
                    ]
                );

                throw new Exception('File is not unique');
            }

            $filesPath[] = $filepath;
            $filesPack[] = $file;
        }

        $this->certificateRepository->save($filesPack);

        $uploadCertificateRequest = new UploadCertificateToTraRegistrationRequest(
            $tin->value(),
            $filesPath
        );

        $uploadCertificateResponse = $this->traIntegrationService->uploadCertificateToTraRegistration(
            $uploadCertificateRequest
        );
        if (!$uploadCertificateResponse->isSuccess()) {
            $this->logger->critical(
                'An error has been occurred when upload the certificate',
                [
                    'tin' => $tin->value(),
                    'errorMessage' => $uploadCertificateResponse->getErrorMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('An error has been occurred when upload the certificate', 500);
        }


        try {
            $dto = new RegisterCompanyToTraCommand(
                $certificateValues['tin'],
                $certificateValues['certificateKey'],
                $certificateValues['certificateSerial'],
                $certificateValues['certificatePassword'],
            );

            $this->messageBus->dispatch($dto);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An error has been occurred when attempt register company on TRA',
                [
                    'tin' => $tin->value(),
                    'code' => $exception->getCode(),
                    'errorMessage' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('An error has been occurred when attempt register company on TRA');
        }

        return [
            'tin' => $tin->value(),
            'filesPath' => $filesPath
        ] ;
    }
}
