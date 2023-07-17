<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Domain\User\UserEntity;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\DatabaseUserRepository;
use DateTimeImmutable;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;
use Tests\Traits\DatabaseTestTrait;

class DeleteUserActionTest extends TestCase
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws Exception
     */
    public function testAction()
    {
        $userEntity = new UserEntity();
        $userEntity
            ->setName('validname')
            ->setEmail('validemail@test.com')
            ->setNotes(null)
            ->setCreated(new DateTimeImmutable('now'));

        $this->userRepository->save($userEntity);

        $app = $this->getAppInstance();

        $request = $this->createRequest('DELETE', '/users/' . $userEntity->getId());
        $response = $app->handle($request);

        $userEntity = $this->getEntityManager()->getRepository(UserEntity::class)->find($userEntity->getId());

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertNotNull($userEntity->getDeleted());
    }
}
