<?php

declare(strict_types=1);

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Domain\User\UserRepository;
use App\Domain\User\UserService;
use Psr\Log\LoggerInterface;

abstract class UserAction extends Action
{
    protected UserService $userService;

    public function __construct(LoggerInterface $logger, UserService $userService)
    {
        parent::__construct($logger);
        $this->userService = $userService;
    }
}
