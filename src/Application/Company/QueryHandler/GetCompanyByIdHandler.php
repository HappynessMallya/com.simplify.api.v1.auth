<?php
declare(strict_types=1);

namespace App\Application\Company\QueryHandler;

use App\Application\Company\Query\GetCompanyByIdQuery;
use App\Domain\Model\Company\Company;
use App\Domain\Model\Company\CompanyId;
use App\Domain\Repository\CompanyRepository;
use Psr\Log\LoggerInterface;

/**
 * Class GetCompanyByIdHandler
 * @package App\Application\Company\QueryHandler
 */
class GetCompanyByIdHandler
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

    public function handle(GetCompanyByIdQuery $command): ?Company
    {
        try {
            return $this->companyRepository->find(CompanyId::fromString($command->companyId()));
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage(), [__METHOD__]);
        }

        return null;
    }
}