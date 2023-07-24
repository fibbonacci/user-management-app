<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Domain\User\UserEntity;
use App\Domain\User\UserService;
use DateTimeImmutable;
use DI\Container;
use Exception;
use Tests\TestCase;

class DeleteUserActionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $user = new UserEntity();
        $user
            ->setId(1)
            ->setName('validname')
            ->setEmail('validemail@test.com')
            ->setNotes('Some notes')
            ->setCreated(new DateTimeImmutable('now'));

        $userServiceProphecy = $this->prophesize(UserService::class);

        $userServiceProphecy
            ->getUserById(1)
            ->willReturn($user);

        $userServiceProphecy
            ->softDeleteUser($user);

        $container->set(UserService::class, $userServiceProphecy->reveal());

        $request = $this->createRequest('DELETE', '/users/1');
        $response = $app->handle($request);

        $this->assertEquals(204, $response->getStatusCode());
    }
}
