<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220829153527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql(/** @lang sql */'
            CREATE TABLE IF NOT EXISTS certificate (
                certificate_id          VARCHAR(36) NOT NULL,
                tin                     VARCHAR(9) NOT NULL,
                filepath                VARCHAR(200) NOT NULL,
                created_at              DATETIME NOT NULL,
                updated_at              DATETIME NOT NULL,
                PRIMARY KEY     (certificate_id, tin)
            )'
        );

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(/** @lang sql */'DROP TABLE certificate');
    }
}
