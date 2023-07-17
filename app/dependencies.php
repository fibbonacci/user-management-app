<?php

declare(strict_types=1);

use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get(SettingsInterface::class);

            $loggerSettings = $settings->get('logger');
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
        EntityManager::class => static function (ContainerInterface $c): EntityManager {
            $settings = $c->get(SettingsInterface::class);

            // Use the ArrayAdapter or the FilesystemAdapter depending on the value of the 'dev_mode' setting
            // You can substitute the FilesystemAdapter for any other cache you prefer from the symfony/cache library
            $cache = $settings->get('doctrine')['dev_mode'] ?
                new ArrayAdapter() :
                new FilesystemAdapter(directory: $settings['doctrine']['cache_dir']);

            $config = ORMSetup::createAttributeMetadataConfiguration(
                $settings->get('doctrine')['metadata_dirs'],
                $settings->get('doctrine')['dev_mode'],
                null,
                $cache
            );

            return EntityManager::create($settings->get('doctrine')['connection'][$_ENV['DB_CONNECTION']], $config);
        },
    ]);
};
