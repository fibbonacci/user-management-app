<?php

declare(strict_types=1);

namespace Tests\Domain\User;

use App\Domain\User\UserEntity;
use Tests\TestCase;

class UserEntityTest extends TestCase
{
    public function userProvider(): array
    {
        return [
            ['Bill Gates', 'bill@test.com'],
            ['Steve Jobs', 'steve@test.com'],
            ['Mark Zuckerberg', 'mark@test.com'],
            ['Evan Spiegel', 'evan@test.com'],
            ['Jack Dorsey', 'jack@test.com'],
        ];
    }

    /**
     * @dataProvider userProvider
     */
    public function testGetters(string $name, string $email)
    {
        $user = new UserEntity();
        $user
            ->setName($name)
            ->setEmail($email);

        $this->assertEquals($name, $user->getName());
        $this->assertEquals($email, $user->getEmail());
    }
}
