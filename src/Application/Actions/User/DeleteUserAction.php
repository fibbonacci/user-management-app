<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

class DeleteUserAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $userId = (int)$this->resolveArg('id');
        $userEntity = $this->userService->getUserById($userId);

        $this->userService->softDeleteUser($userEntity);

        $this->logger->info("User with id $userId was deleted.");

        return $this->respondWithData(statusCode: 204);
    }
}
