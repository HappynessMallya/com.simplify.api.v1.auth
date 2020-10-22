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
    private $companyRepository;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function handle(UpdateCompanyCommand $command): ?bool
    {
        $companyId = CompanyId::fromString($command->getCompanyId());
        $company = $this->companyRepository->get($companyId);

        if (empty($company)) {
            throw new Exception('No Company found by Id ' . $companyId, 404);
        }

        $company->update([
            'name' => $command->getName(),
            'email' => $command->getEmail(),
            'address' => $command->getAddress(),
        ]);

        return $this->companyRepository->save($company);
    }
}