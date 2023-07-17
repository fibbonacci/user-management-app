<?php

declare(strict_types=1);

namespace Tests\Traits;

use Doctrine\DBAL\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait DatabaseTestTrait
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     */
    protected function setUpDatabase(string $schemaPath): void
    {
        $this->getEntityManager()->getConnection()->executeStatement('SET unique_checks=0; SET foreign_key_checks=0;');
        $this->getEntityManager()->getConnection()->executeStatement(
            (string)file_get_contents($schemaPath)
        );
        $this->getEntityManager()->getConnection()->executeStatement('SET unique_checks=1; SET foreign_key_checks=1;');
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     */
    protected function tearDownDatabase(): void
    {
        $tables = $this->getEntityManager()->getConnection()->executeQuery(
            'SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = database()'
        )->fetchAllAssociative();

        foreach ($tables as $table) {
            $this->getEntityManager()->getConnection()->executeStatement(
                sprintf('DROP TABLE IF EXISTS `%s`;', $table['TABLE_NAME'])
            );
        }
    }
}
