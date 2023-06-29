<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Model\User\User;
use App\Domain\Model\User\UserId;
use App\Domain\Model\User\UserStatus;
use App\Domain\Repository\UserRepository;
use App\Infrastructure\Symfony\Security\UserEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ObjectRepository;
use Exception;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DoctrineUserRepository
 * @package App\Infrastructure\Repository
 */
class DoctrineUserRepository implements UserRepository, UserLoaderInterface, ObjectRepository
{
    /** @var EntityManager */
    private $em;

    /** @var EntityRepository */
    private $repository;

    /**
     * @param EntityManagerInterface $em
     * @throws NotSupported
     */
    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
        $this->repository = $this->em->getRepository(User::class);
    }

    /**
     * @param mixed $userId
     * @return User|object|null
     */
    public function find($userId)
    {
        return $this->repository->find($userId);
    }

    /**
     * @return User[]|array|object[]
     */
    public function findAll()
    {
        return $this->repository->findBy(['status' => UserStatus::ACTIVE()]);
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return User[]|array|object[]|null
     */
    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        if (empty($criteria)) {
            return null;
        }

        $criteria['status'] = UserStatus::ACTIVE();

        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param array $criteria
     * @return UserEntity|null
     */
    public function findOneBy(array $criteria): ?UserEntity
    {
        $criteria['status'] = [UserStatus::ACTIVE(), UserStatus::CHANGE_PASSWORD()];
        $criteria['enabled'] = true;

        /** @var User $user */
        $user = $this->repository->findOneBy($criteria);

        if (empty($user)) {
            return null;
        }

        return new UserEntity(
            $user->userId(),
            $user->companyId(),
            $user->email(),
            $user->username(),
            $user->password(),
            $user->salt(),
            $user->status(),
            $user->roles(),
            $user->getUserType()
        );
    }

    /**
     * @param array $criteria
     * @return array|null
     */
    public function findByCriteria(array $criteria): ?array
    {
        if (empty($criteria['status'])) {
            $criteria['status'] = [
                UserStatus::ACTIVE(),
                UserStatus::CHANGE_PASSWORD(),
                UserStatus::SUSPENDED(),
            ];
        }

        return $this->repository->findBy($criteria);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return 'DoctrineUserRepository';
    }

    /**
     * @param UserId $userId
     * @return User|null
     */
    public function get(UserId $userId): ?User
    {
        return $this->find($userId);
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function getByUsername(string $username): ?User
    {
        $users = $this->findBy(
            [
                'email' => $username,
            ]
        );

        return empty($users) ? null : $users[0];
    }

    /**
     * @param User $user
     * @return bool
     * @throws Exception
     */
    public function save(User $user): bool
    {
        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (Exception $exception) {
            throw new Exception('Exception error trying to save user: ' . $exception->getMessage());
        }

        return true;
    }

    /**
     * @param User $user
     * @return bool
     * @throws Exception
     */
    public function remove(User $user): bool
    {
        $user->suspend();

        try {
            $this->em->persist($user);
            $this->em->flush();
        } catch (Exception $exception) {
            throw new Exception('Exception error trying to suspend user: ' . $exception->getMessage());
        }

        return true;
    }

    /**
     * @param string $username
     * @return UserEntity|UserInterface|null
     */
    public function loadUserByUsername(string $username): ?UserEntity
    {
        try {
            /** @var User $user */
            $user = $this->em->createQuery("
                    SELECT u FROM App\Model\User\User u
                    WHERE (u.email = :uname OR u.username = :uname)
                    AND (u.status = 'ACTIVE' OR u.status = 'CHANGE_PASSWORD') AND u.enabled = 1
                ")
                ->setParameter('uname', $username)
                ->getOneOrNullResult();

            if (empty($user)) {
                return null;
            }

            return UserEntity::create(
                $user->userId(),
                $user->companyId(),
                $user->email(),
                $user->username(),
                $user->password(),
                $user->salt(),
                $user->status(),
                $user->roles(),
                $user->userType()
            );
        } catch (
            NonUniqueResultException
            | Exception $exception
        ) {
            return null;
        }
    }

    /**
     * @param UserId $userId
     * @throws Exception
     */
    public function login(UserId $userId): void
    {
        $user = $this->find($userId);

        $user->login();
        $this->save($user);
    }
}
