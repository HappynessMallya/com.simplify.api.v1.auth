<?php
declare(strict_types=1);

namespace App\Application\Company\CommandHandler;

use App\Application\Company\Command\CreateCompanyCommand;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyRepository;
use Psr\Log\LoggerInterface;

/**
 * Class CreateCompanyHandler
 * @package App\Application\Company\CommandHandler
 */
class CreateCompanyHandler
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(CompanyRepository $companyRepository, LoggerInterface $logger)
    {
        $this->companyRepository = $companyRepository;
        $this->logger = $logger;
    }

    public function handle(CreateCompanyCommand $command): ?string
    {
        $company = Company::create(
            CompanyId::generate(),
            $command->getName(),
            $command->getAddress(),
            $command->getEmail(),
            new \DateTime()
        );

        if ($this->companyRepository->save($company)) {
            return $company->companyId()->toString();
        }

        return null;
    }
}