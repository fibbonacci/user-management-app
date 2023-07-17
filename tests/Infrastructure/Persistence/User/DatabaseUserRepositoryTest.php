<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\User;

use App\Domain\User\UserEntity;
use App\Domain\User\UserRepository;
use App\Infrastructure\Persistence\User\DatabaseUserRepository;
use DateTimeImmutable;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Tests\TestCase;
use Tests\Traits\DatabaseTestTrait;

class DatabaseUserRepositoryTest extends TestCase
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
     * @throws ORMException
     */
    public function testFindAll(): void
    {
        $user = new UserEntity();
        $user
            ->setEmail('test@test.com')
            ->setName('test')
            ->setNotes('test')
            ->setCreated(new DateTimeImmutable('now'));

        $this->userRepository->save($user);
        $this->assertEquals([$user], $this->userRepository->findAll());
    }

    /**
     * @throws ORMException
     */
    public function testFindUserById(): void
    {
        $user = new UserEntity();
        $user
            ->setEmail('test@test.com')
            ->setName('test')
            ->setNotes('test')
            ->setCreated(new DateTimeImmutable('now'));

        $this->userRepository->save($user);

        $this->assertEquals($user, $this->userRepository->findById($user->getId()));
    }
}
