<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307153053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(/** @lang sql */
            'CREATE TABLE IF NOT EXISTS company_by_user (
                user_id VARCHAR(36) NOT NULL,
                company_id VARCHAR(36) NOT NULL,
                status VARChAR(20) NOT NULL,
                created_at DATETIME DEFAULT NOW() NOT NULL,
                updated_at DATETIME,
                PRIMARY KEY (user_id, company_id)
            )'
        );

        $this->addSql(/** @lang sql */
            'CREATE TABLE IF NOT EXISTS organization (
                organization_id VARCHAR(36) NOT NULL,
                name VARCHAR(255) NOT NULL,
                status VARCHAR(20) NOT NULL,
                created_at DATETIME DEFAULT NOW() NOT NULL,
                updated_at DATETIME,
                PRIMARY KEY (organization_id)
            )'
        );

        $this->addSql(/** @lang sql */
            'ALTER TABLE auth_user ADD user_type VARCHAR(50) NOT NULL'
        );

        $this->addSql(/** @lang sql */
            'ALTER TABLE company ADD organization_id VARCHAR(36) after id'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE company_by_user');
    }
}
