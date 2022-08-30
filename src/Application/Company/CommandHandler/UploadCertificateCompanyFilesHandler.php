<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\UploadCertificateCompanyFilesCommand;
use App\Domain\Company\CertificateId;
use App\Domain\Model\Certificate;
use App\Domain\Model\Company\TaxIdentificationNumber;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Services\FileUploaderService;
use App\Domain\Services\TraIntegrationService;
use App\Domain\Repository\CertificateRepository;
use App\Domain\Services\UploadCertificateToTraRegistrationRequest;
use Exception;
use Psr\Log\LoggerInterface;

final class UploadCertificateCompanyFilesHandler
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
     * @param LoggerInterface $logger
     * @param FileUploaderService $fileUploaderService
     * @param CompanyRepository $companyRepository
     * @param CertificateRepository $certificateRepository
     * @param TraIntegrationService $traIntegrationService
     */
    public function __construct(
        LoggerInterface $logger,
        FileUploaderService $fileUploaderService,
        CompanyRepository $companyRepository,
        CertificateRepository $certificateRepository,
        TraIntegrationService $traIntegrationService
    ) {
        $this->logger = $logger;
        $this->fileUploaderService = $fileUploaderService;
        $this->companyRepository = $companyRepository;
        $this->certificateRepository = $certificateRepository;
        $this->traIntegrationService = $traIntegrationService;
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

        foreach ($files as $file) {
            $certificateId = CertificateId::generate();
            $filepath = $this->fileUploaderService->uploadFile($file, $tin);

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
                    ]
                );

                throw new Exception('File is not unique');
            }

            $filesPath[] = $filepath;
            $filesPack[] = $file;
        }

        $this->certificateRepository->save($filesPack);


        $request = new UploadCertificateToTraRegistrationRequest(
            $tin->value(),
            $filesPath
        );

        $response = $this->traIntegrationService->uploadCertificateToTraRegistration($request);
        if (!$response->isSuccess()) {
            $this->logger->critical(
                'An error has been occurred when upload the certificate',
                [
                    'tin' => $tin->value(),
                    'errorMessage' => $response->getErrorMessage(),
                    'method' => __METHOD__
                ]
            );

            throw new Exception('An error has been occurred when upload the certificate', 500);
        }

        return [
            'tin' => $tin->value(),
            'filesPath' => $filesPath
        ] ;
    }
}
