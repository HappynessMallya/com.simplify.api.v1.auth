<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\CompanyTraRegistrationCommand;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Services\VerifyReceiptCodeRequest;
use App\Domain\Services\VerifyReceiptCodeService;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class CompanyTraRegistrationHandler
 * @package App\Application\Company\CommandHandler
 */
class CompanyTraRegistrationHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var VerifyReceiptCodeService */
    private VerifyReceiptCodeService $verifyReceiptCode;

    /**
     * CompanyTraRegistrationHandler constructor
     * @param LoggerInterface $logger
     * @param CompanyRepository $companyRepository
     * @param VerifyReceiptCodeService $verifyReceiptCode
     */
    public function __construct(
        LoggerInterface $logger,
        CompanyRepository $companyRepository,
        VerifyReceiptCodeService $verifyReceiptCode
    ) {
        $this->logger = $logger;
        $this->companyRepository = $companyRepository;
        $this->verifyReceiptCode = $verifyReceiptCode;
    }

    /**
     * @param CompanyTraRegistrationCommand $command
     * @return bool|null
     * @throws Exception
     */
    public function handle(CompanyTraRegistrationCommand $command): ?bool
    {
        $company = $this->companyRepository->findOneBy(['tin' => $command->getTin()]);

        if (empty($company)) {
            $this->logger->critical(
                'Company not found by TIN',
                [
                    'tin' => $command->tin(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('Company not found by TIN: ' . $command->getTin(), 404);
        }

        $company->updateTraRegistration(json_decode($command->getTraRegistration(), true));

        $request = new VerifyReceiptCodeRequest(
            json_decode($command->getTraRegistration(), true)['RECEIPTCODE']
        );

        $response = $this->verifyReceiptCode->onVerifyReceiptCode($request);

        if (!$response->isSuccess()) {
            $this->logger->critical(
                'Receipt code not verified',
                [
                    'error_message' => $response->getErrorMessage(),
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('Receipt code not verified. ' . $command->getTin(), 500);
        }

        return $this->companyRepository->save($company);
    }
}
