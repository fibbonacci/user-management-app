<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\UserEntity;
use App\Domain\User\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class DatabaseUserRepository implements UserRepository
{
    private EntityRepository $repository;

    /**
     * @throws NotSupported
     */
    public function __construct(private readonly EntityManager $entityManager)
    {
        $this->repository = $this->entityManager->getRepository(UserEntity::class);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findById(int $id): ?UserEntity
    {
        return $this->repository->find($id);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAllByAttribute(string $attribute, $value): int
    {
        $query = $this->entityManager->createQueryBuilder();
        $query
            ->select($query->expr()->count('u'))
            ->from(UserEntity::class, 'u')
            ->andWhere("u.$attribute = :value")
            ->andWhere("u.deleted IS NULL")
            ->setParameter('value', $value);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws ORMException
     */
    public function save(UserEntity $userEntity): void
    {
        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();
    }
}
