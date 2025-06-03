<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250603140553 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Set user roles as JSONB type';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ALTER roles TYPE JSONB
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ALTER roles TYPE JSON
        SQL);
    }
}
