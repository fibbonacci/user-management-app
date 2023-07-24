<?php

declare(strict_types=1);

namespace App\Domain\User;

use DateTimeImmutable;

class UserService
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    /**
     * @return UserEntity[]
     */
    public function listUsers(): array
    {
        return $this->userRepository->findAll();
    }

    /**
     * @throws UserNotFoundException
     */
    public function getUserById(int $id): UserEntity
    {
        $user = $this->userRepository->findById($id);

        if (null === $user) {
            throw new UserNotFoundException();
        }

        return $user;
    }

    public function countUsersByAttribute(string $attribute, $value): int
    {
        return $this->userRepository->countAllByAttribute($attribute, $value);
    }

    public function createOrUpdateUser(
        string $name,
        string $email,
        ?string $notes = null,
        ?UserEntity $userEntity = null
    ): UserEntity {
        $userEntity = $userEntity ?? new UserEntity();
        $userEntity
            ->setName($name)
            ->setEmail($email)
            ->setNotes($notes)
            ->setCreated(new DateTimeImmutable('now'));

        $this->userRepository->save($userEntity);

        return $userEntity;
    }

    public function softDeleteUser(UserEntity $userEntity): void
    {
        $userEntity->setDeleted(new DateTimeImmutable('now'));

        $this->userRepository->save($userEntity);
    }
}
