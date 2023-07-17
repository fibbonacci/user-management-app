<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionPayload;
use App\Domain\User\UserRepository;
use App\Domain\User\UserEntity;
use App\Infrastructure\Persistence\User\DatabaseUserRepository;
use DateTimeImmutable;
use DI\Container;
use Doctrine\ORM\Exception\NotSupported;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;
use Tests\Traits\DatabaseTestTrait;

class ListUserActionTest extends TestCase
{
    use DatabaseTestTrait;

    private UserRepository $userRepository;

    /**
     * @throws NotSupported
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = new DatabaseUserRepository($this->getEntityManager());
    }

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
            ->setEmail('test@test.com')
            ->setName('test')
            ->setNotes('test')
            ->setCreated(new DateTimeImmutable('now'));

        $this->userRepository->save($user);

        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepositoryProphecy
            ->findAll()
            ->willReturn([$user])
            ->shouldBeCalledOnce();

        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/users');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedPayload = new ActionPayload(200, [$user]);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}
