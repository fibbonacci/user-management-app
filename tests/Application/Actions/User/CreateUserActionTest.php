<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Domain\User\Enums\RestrictedNamesEnum;
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

class CreateUserActionTest extends TestCase
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
        $this->createUser('existingname', 'existing@test.com');
        $this->createUser('existingemail', 'existingemail@test.com');
    }

    /**
     * @dataProvider userProvider
     * @throws Exception
     */
    public function testAction($name, $email, $notes, $statusCode)
    {
        $app = $this->getAppInstance();

        $request = $this->createRequest('POST', '/users');
        $request = $request->withParsedBody(['name' => $name, 'email' => $email, 'notes' => $notes]);
        $response = $app->handle($request);
        $payload = (string) $response->getBody();

        $decodedPayload = json_decode($payload);

        $this->assertEquals($statusCode, $decodedPayload->statusCode);

        if ($statusCode === 201) {
            $this->assertEquals($name, $decodedPayload->data->name);
            $this->assertEquals($email, $decodedPayload->data->email);
            $this->assertEquals($notes, $decodedPayload->data->notes);
        }
    }

    public function userProvider(): array
    {
        return [
            ['validname', 'validemail@test.com', 'valid note', 'statusCode' => 201],
            [null, 'validemail@test.com', null, 'statusCode' => 422],
            ['short', 'validemail@test.com', null, 'statusCode' => 422],
            ['Capitalized', 'validemail@test.com', null, 'statusCode' => 422],
            ['Capitalized', 'validemail@test.com', null, 'statusCode' => 422],
            ['existingname', 'validemail@test.com', null, 'statusCode' => 422],
            [RestrictedNamesEnum::Restricted1->value, 'validemail@test.com', null, 'statusCode' => 422],
            ['validname', null, null, 'statusCode' => 422],
            ['validname', 'notvalidemail', null, 'statusCode' => 422],
            ['validname', 'existingemail@test.com', null, 'statusCode' => 422],
            ['validname', 'existingemail@domain1.com', null, 'statusCode' => 422],
        ];
    }

    /**
     * @throws ORMException
     */
    private function createUser(string $name, string $email, ?string $notes = null): UserEntity
    {
        $userEntity = new UserEntity();
        $userEntity
            ->setName($name)
            ->setEmail($email)
            ->setNotes($notes)
            ->setCreated(new DateTimeImmutable('now'));

        $this->userRepository->save($userEntity);

        return $userEntity;
    }
}
