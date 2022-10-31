<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221013021025 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(/** @lang sql */
            "ALTER TABLE company MODIFY COLUMN tra_registration longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL NULL COMMENT '(DC2Type:array)';");
        $this->addSql(/** @lang sql */
            'CREATE INDEX IDX_company_id USING BTREE ON auth_user (company_id);
        ');

        $this->addSql(/** @lang sql */'ALTER TABLE auth_user DROP PRIMARY KEY;');
        $this->addSql(/** @lang sql */
            'ALTER TABLE auth_user ADD CONSTRAINT `primary_key` PRIMARY KEY (user_id,company_id);'
        );


        $this->addSql(/** @lang sql */
            'CREATE INDEX IDX_refresh_token_username USING BTREE ON refresh_tokens (username);'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
