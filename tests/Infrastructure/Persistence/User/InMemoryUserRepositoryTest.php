<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\User;

use App\Domain\User\UserEntity;
use App\Domain\User\UserNotFoundException;
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
use Tests\TestCase;

class InMemoryUserRepositoryTest extends TestCase
{
    public function testFindAll()
    {
        $user = new UserEntity(1, 'bill.gates', 'Bill', 'Gates');

        $userRepository = new InMemoryUserRepository([1 => $user]);

        $this->assertEquals([$user], $userRepository->findAll());
    }

    public function testFindAllUsersByDefault()
    {
        $users = [
            1 => new UserEntity(1, 'bill.gates', 'Bill', 'Gates'),
            2 => new UserEntity(2, 'steve.jobs', 'Steve', 'Jobs'),
            3 => new UserEntity(3, 'mark.zuckerberg', 'Mark', 'Zuckerberg'),
            4 => new UserEntity(4, 'evan.spiegel', 'Evan', 'Spiegel'),
            5 => new UserEntity(5, 'jack.dorsey', 'Jack', 'Dorsey'),
        ];

        $userRepository = new InMemoryUserRepository();

        $this->assertEquals(array_values($users), $userRepository->findAll());
    }

    public function testFindUserOfId()
    {
        $user = new UserEntity(1, 'bill.gates', 'Bill', 'Gates');

        $userRepository = new InMemoryUserRepository([1 => $user]);

        $this->assertEquals($user, $userRepository->findUserOfId(1));
    }

    public function testFindUserOfIdThrowsNotFoundException()
    {
        $userRepository = new InMemoryUserRepository([]);
        $this->expectException(UserNotFoundException::class);
        $userRepository->findUserOfId(1);
    }
}
