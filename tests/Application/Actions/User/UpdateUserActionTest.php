<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionPayload;
use App\Domain\User\Enums\RestrictedDomainsEnum;
use App\Domain\User\Enums\RestrictedNamesEnum;
use App\Domain\User\UserEntity;
use App\Domain\User\UserService;
use DateTimeImmutable;
use DI\Container;
use Exception;
use Prophecy\Argument;
use Tests\TestCase;

class UpdateUserActionTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testAction()
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $name = 'validname';
        $email = 'validemail@test.com';
        $notes = 'Some notes';

        $user = new UserEntity();
        $user
            ->setId(1)
            ->setName($name)
            ->setEmail($email)
            ->setNotes($notes)
            ->setCreated(new DateTimeImmutable('now'));

        $userServiceProphecy = $this->prophesize(UserService::class);

        $userServiceProphecy
            ->getUserById(1)
            ->willReturn($user);

        $userServiceProphecy
            ->countUsersByAttribute(Argument::type('string'), Argument::type('string'))
            ->willReturn(0);

        $userServiceProphecy
            ->createOrUpdateUser($name, $email, $notes, $user)
            ->willReturn($user);

        $container->set(UserService::class, $userServiceProphecy->reveal());

        $request = $this->createRequest('PUT', '/users/1');
        $request = $request->withParsedBody(['name' => $name, 'email' => $email, 'notes' => $notes]);
        $response = $app->handle($request);
        $payload = (string) $response->getBody();

        $expectedPayload = new ActionPayload(200, $user);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }

    /**
     * @dataProvider userProvider
     * @throws Exception
     */
    public function testActionWithNotValidData($name, $email)
    {
        $app = $this->getAppInstance();

        /** @var Container $container */
        $container = $app->getContainer();

        $userServiceProphecy = $this->prophesize(UserService::class);

        $user = new UserEntity();
        $user
            ->setId(1)
            ->setName('validname')
            ->setEmail('validemail@test.com')
            ->setNotes('Some notes')
            ->setCreated(new DateTimeImmutable('now'));

        $userServiceProphecy
            ->getUserById(1)
            ->willReturn($user);

        $userServiceProphecy
            ->countUsersByAttribute(Argument::type('string'), Argument::type('string'))
            ->will(function () use ($name, $email) {
                if (($name && str_contains($name, 'existing')) || ($email && str_contains($email, 'existing'))) {
                    return 1;
                }

                return 0;
            });

        $container->set(UserService::class, $userServiceProphecy->reveal());

        $request = $this->createRequest('PUT', '/users/1');
        $request = $request->withParsedBody(['name' => $name, 'email' => $email]);
        $response = $app->handle($request);

        $this->assertEquals(422, $response->getStatusCode());
    }

    public function userProvider(): array
    {
        return [
            [null, 'validemail@test.com'],
            ['short', 'validemail@test.com'],
            ['Capitalized', 'validemail@test.com'],
            ['Capitalized', 'validemail@test.com'],
            ['existingname', 'validemail@test.com'],
            [RestrictedNamesEnum::Restricted1->value, 'validemail@test.com'],
            ['validname', null],
            ['validname', 'notvalidemail'],
            ['validname', 'existingemail@test.com'],
            ['validname', sprintf('validemail@%s', RestrictedDomainsEnum::Domain1->value)],
        ];
    }
}
