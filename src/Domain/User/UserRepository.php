<?php

declare(strict_types=1);

namespace App\Domain\User;

interface UserRepository
{
    /**
     * @return UserEntity[]
     */
    public function findAll(): array;

    public function findById(int $id): ?UserEntity;

    public function save(UserEntity $userEntity): void;

    public function countAllByAttribute(string $attribute, $value): int;
}
