<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230721200732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create users table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('users');

        $table->addColumn('id', Types::INTEGER, ['autoincrement' => true]);
        $table->addColumn('name', Types::STRING, ['length' => 64]);
        $table->addColumn('email', Types::STRING);
        $table->addColumn('notes', Types::TEXT, ['notnull' => false]);
        $table->addColumn('created', Types::DATETIMETZ_IMMUTABLE);
        $table->addColumn('deleted', Types::DATETIMETZ_IMMUTABLE, ['notnull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name', 'deleted']);
        $table->addUniqueIndex(['email', 'deleted']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
