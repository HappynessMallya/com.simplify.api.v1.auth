<?php

namespace App\Application\User\V1\QueryHandler;

use App\Application\User\V1\Query\GetUsersByCompanyIdQuery;
use App\Domain\Model\User\UserId;
use App\Domain\Repository\CompanyByUserRepository;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Model\Company\CompanyId;
use Psr\Log\LoggerInterface;

class GetUsersByCompanyIdHandler
{
    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var CompanyRepository */
    private CompanyRepository $companyRepository;

    /** @var CompanyByUserRepository */
    private CompanyByUserRepository $companyByUserRepository;

    /**
     * @param LoggerInterface $logger
     * @param UserRepository $userRepository
     * @param CompanyRepository $companyRepository
     * @param CompanyByUserRepository $companyByUserRepository
     */
    public function __construct(
        LoggerInterface $logger,
        UserRepository $userRepository,
        CompanyRepository $companyRepository,
        CompanyByUserRepository $companyByUserRepository
    ) {
        $this->logger = $logger;
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->companyByUserRepository = $companyByUserRepository;
    }

    public function __invoke(GetUsersByCompanyIdQuery $query): array
    {
        $companyId = CompanyId::fromString($query->getCompanyId());
        $company = $this->companyRepository->get($companyId);
        if (!$company) {
            throw new \Exception('Company not found', 404);
        }

        $companyByUser = $this->companyByUserRepository->getOperatorsByCompany($companyId);

        $users = [];
        foreach ($companyByUser as $user) {
            $user = $this->userRepository->get(UserId::fromString($user['user_id']));
            $users[] = [
                'userId' => $user->userId()->toString(),
                'companyId' => $user->companyId()->toString(),
                'fullName' => $user->firstName().' '.$user->lastName(),
                'email' => $user->email(),
                'status' => $user->status()->toString(),
                'type' => $user->getUserType()->toString(),
            ];
        }

        return $users;
    }
}