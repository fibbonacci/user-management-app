<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\UserEntity;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;

class InMemoryUserRepository implements UserRepository
{
    /**
     * @var UserEntity[]
     */
    private array $users;

    /**
     * @param UserEntity[]|null $users
     */
    public function __construct(array $users = null)
    {
        $this->users = $users ?? [
            1 => new UserEntity(1, 'bill.gates', 'Bill', 'Gates'),
            2 => new UserEntity(2, 'steve.jobs', 'Steve', 'Jobs'),
            3 => new UserEntity(3, 'mark.zuckerberg', 'Mark', 'Zuckerberg'),
            4 => new UserEntity(4, 'evan.spiegel', 'Evan', 'Spiegel'),
            5 => new UserEntity(5, 'jack.dorsey', 'Jack', 'Dorsey'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values($this->users);
    }

    /**
     * {@inheritdoc}
     */
    public function findUserOfId(int $id): UserEntity
    {
        if (!isset($this->users[$id])) {
            throw new UserNotFoundException();
        }

        return $this->users[$id];
    }
}
