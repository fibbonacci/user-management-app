<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\ActionPayload;
use App\Application\Validation\User\UserRequest;
use Psr\Http\Message\ResponseInterface as Response;
use ReflectionException;
use Yiisoft\Validator\Validator;

class CreateUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     * @throws ReflectionException
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();

        $userRequest = new UserRequest(
            userService: $this->userService,
            name: $body['name'],
            email: $body['email'],
            notes: $body['notes'] ?? null
        );

        $result = (new Validator())->validate($userRequest);

        if (false === $result->isValid()) {
            return $this->respond(
                new ActionPayload(statusCode: 422, errors: $result->getErrorMessagesIndexedByAttribute())
            );
        }

        $userEntity = $this->userService->createOrUpdateUser(
            name: $userRequest->name,
            email: $userRequest->email,
            notes: $userRequest->notes
        );

        $this->logger->info("New users was created.");

        return $this->respondWithData($userEntity, 201);
    }
}
