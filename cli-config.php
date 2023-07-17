<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

$container = require_once __DIR__ . '/src/bootstrap.php';

return ConsoleRunner::createHelperSet($container->get(EntityManager::class));