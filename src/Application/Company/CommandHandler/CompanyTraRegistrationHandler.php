<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\CompanyTraRegistrationCommand;
use App\Domain\Repository\CompanyRepository;
use Exception;

/**
 * Class CompanyTraRegistrationHandler
 * @package App\Application\Company\CommandHandler
 */
class CompanyTraRegistrationHandler
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function handle(CompanyTraRegistrationCommand $command): ?bool
    {
        $company = $this->companyRepository->findOneBy(['tin' => $command->getTin()]);

        if (empty($company)) {
            throw new Exception('No Company found by Tin ' . $command->getTin(), 404);
        }

        $company->updateTraRegistration(json_decode($command->getTraRegistration(), true));

        return $this->companyRepository->save($company);
    }
}
