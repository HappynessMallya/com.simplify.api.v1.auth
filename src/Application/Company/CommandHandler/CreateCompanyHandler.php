<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\CreateCompanyCommand;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Model\Company\CompanyStatus;
use App\Domain\Repository\CompanyRepository;

/**
 * Class CreateCompanyHandler
 * @package App\Application\Company\CommandHandler
 */
class CreateCompanyHandler
{
    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function handle(CreateCompanyCommand $command): ?string
    {
        $company = Company::create(
            CompanyId::generate(),
            $command->getName(),
            (int) $command->getTin(),
            $command->getAddress(),
            $command->getEmail(),
            $command->getPhone(),
            new \DateTime(),
            CompanyStatus::STATUS_ACTIVE(),
            $command->getSerial()
        );

        if ($this->companyRepository->save($company)) {
            return $company->companyId()->toString();
        }

        return null;
    }
}
