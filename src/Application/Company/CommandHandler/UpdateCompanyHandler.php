<?php

declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\UpdateCompanyCommand;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyRepository;
use Exception;

/**
 * Class UpdateCompanyHandler
 * @package App\Application\Company\CommandHandler
 */
class UpdateCompanyHandler
{
    /**
     * @var CompanyRepository
     */
    private CompanyRepository $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @throws Exception
     */
    public function handle(UpdateCompanyCommand $command): ?bool
    {
        $companyId = CompanyId::fromString($command->getCompanyId());
        $company = $this->companyRepository->get($companyId);

        if (empty($company)) {
            throw new Exception('No Company found by Id ' . $companyId, 404);
        }

        if ($command->getEnable() === false) {
            $company->disable();

            return $this->companyRepository->save($company);
        }

        $company->update([
            'name' => $command->getName(),
            'email' => $command->getEmail(),
            'tin' => (int) $command->getTin(),
            'address' => $command->getAddress(),
            'phone' => $command->getPhone(),
            'traRegistration' => $command->getTraRegistration()
        ]);

        return $this->companyRepository->save($company);
    }
}
