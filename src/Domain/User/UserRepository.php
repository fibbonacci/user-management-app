<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    /**
     * @return UserEntity[]
     */
    public function findAll(): array;

    /**
     * @param int $id
     * @return UserEntity
     * @throws UserNotFoundException
     */
    public function findUserOfId(int $id): UserEntity;
}
