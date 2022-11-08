<?php

declare(strict_types=1);

namespace App\Application\User\CommandHandler;

use App\Application\User\Command\LoginUssdCommand;
use App\Domain\Repository\CompanyRepository;
use PHPUnit\Util\Exception;
use Psr\Log\LoggerInterface;

/**
 * Class LoginUssdHandler
 * @package App\Application\User\CommandHandler
 */
class LoginUssdHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /**
     * LoginUssdHandler constructor
     * @param LoggerInterface $logger
     * @param CompanyRepository $companyRepository
     */
    public function __construct(
        LoggerInterface $logger,
        CompanyRepository $companyRepository
    ) {
        $this->logger = $logger;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param LoginUssdCommand $command
     * @return void
     */
    public function __invoke(LoginUssdCommand $command): void
    {
        $criteria = [
            'tin' => $command->getTin(),
        ];

        $company = $this->companyRepository->findOneBy($criteria);

        if (empty($company)) {
            $this->logger->error(
                'Company not found by criteria',
                [
                    'criteria' => $criteria,
                    'method' => __METHOD__,
                ]
            );

            throw new Exception('Company not found by criteria');
        }
    }
}
