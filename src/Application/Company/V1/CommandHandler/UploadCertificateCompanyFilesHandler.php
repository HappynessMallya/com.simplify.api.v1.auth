<?php

declare(strict_types=1);

namespace App\Application\Company\V1\CommandHandler;

use App\Application\Company\V1\Command\RegisterCompanyToTraCommand;
use App\Application\Company\V1\Command\UploadCertificateCompanyFilesCommand;
use App\Domain\Model\Company\Certificate;
use App\Domain\Model\Company\CertificateId;
use App\Domain\Model\Company\CertificatePassword;
use App\Domain\Model\Company\Serial;
use App\Domain\Model\Company\TaxIdentificationNumber;
use App\Domain\Repository\CertificateRepository;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Services\CertificateDataService;
use App\Domain\Services\FileUploaderService;
use App\Domain\Services\TraIntegrationService;
use App\Domain\Services\UploadCertificateToTraRegistrationRequest;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Class UploadCertificateCompanyFilesHandler
 * @package App\Application\Company\V1\CommandHandler
 */
class UploadCertificateCompanyFilesHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var TraIntegrationService */
    private TraIntegrationService $traIntegrationService;

    /** @var FileUploaderService */
    private FileUploaderService $fileUploaderService;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var CertificateRepository */
    private CertificateRepository $certificateRepository;

    /** @var MessageBusInterface */
    private MessageBusInterface $messageBus;

    /** @var CertificateDataService */
    private CertificateDataService $certificateDataService;

    /**
     * @param LoggerInterface $logger
     * @param FileUploaderService $fileUploaderService
     * @param CompanyRepository $companyRepository
     * @param CertificateRepository $certificateRepository
     * @param TraIntegrationService $traIntegrationService
     * @param MessageBusInterface $messageBus
     * @param CertificateDataService $certificateDataService
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
        $tin = new TaxIdentificationNumber($command->getTin());
        $serial = new Serial($command->getSerial());
        $certificatePassword = new CertificatePassword($command->getCertificatePassword());

        $files = $command->getCompanyFiles();

        $company = $this->companyRepository->findOneBy(
            [
                'tin' => $tin->value(),
                'serial' => $serial->value()
            ]
        );

        if (empty($company)) {
            $this->logger->critical(
                'Company could not be found',
                [
                    'tin' => $tin->value(),
                    'serial' => $serial->value(),
                    'method' => __METHOD__
                ]
            );

            throw new Exception(
                'Company could not be found',
                Response::HTTP_NOT_FOUND
            );
        }

        $filesPath = [];
        $filesPack = [];

        $certificateValues = [];
        foreach ($files as $file) {
            $certificateId = CertificateId::generate();
            $filepath = $this->fileUploaderService->uploadFile($file, $tin, $serial);

            $certificateDataPath = $this->certificateDataService->createCertificateData($filepath, $certificatePassword);
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
                    $certificateDataPath,
                    $serial,
                    $certificatePassword
                );
                $filesPack[] = $fileCertificate;
            }

            $file = new Certificate(
                $certificateId,
                $tin,
                $filepath,
                $serial,
                $certificatePassword
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

                throw new Exception(
                    'File is not unique',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $filesPath[] = $filepath;
            $filesPack[] = $file;
        }

        $this->certificateRepository->save($filesPack);

        $uploadCertificateRequest = new UploadCertificateToTraRegistrationRequest(
            $tin->value(),
            $filesPath,
            $serial->value()
        );

        $uploadCertificateResponse = $this->traIntegrationService->uploadCertificateToTraRegistration(
            $uploadCertificateRequest
        );

        if (!$uploadCertificateResponse->isSuccess()) {
            $this->logger->critical(
                'An internal error has been occurred when upload the certificate',
                [
                    'tin' => $tin->value(),
                    'errorMessage' => $uploadCertificateResponse->getErrorMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'An internal error has been occurred when upload the certificate',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        try {
            $dto = new RegisterCompanyToTraCommand(
                $certificateValues['tin'],
                $certificateValues['certificateKey'],
                $certificateValues['certificateSerial'],
                $certificateValues['certificatePassword']
            );

            $this->messageBus->dispatch($dto);
        } catch (Exception $exception) {
            $this->logger->critical(
                'An internal error has been occurred when attempt register company on TRA',
                [
                    'tin' => $tin->value(),
                    'code' => $exception->getCode(),
                    'errorMessage' => $exception->getMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception(
                'An internal error has been occurred when attempt register company on TRA',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return [
            'tin' => $tin->value(),
            'serial' => $serial->value(),
            'filesPath' => $filesPath,
        ];
    }
}
