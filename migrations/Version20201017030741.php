<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201017030741 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
        'CREATE TABLE IF NOT EXISTS company (
            id CHAR(36) NOT NULL COMMENT \'(DC2Type:CompanyId)\',
            name VARCHAR(255) NOT NULL,
            address VARCHAR(255) DEFAULT NULL,
            email VARCHAR(255) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            tin VARCHAR(255) NOT NULL,
            tra_registration LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\',
            phone VARCHAR(20) charset utf8 NULL,
            enable TINYINT(1) DEFAULT 1 NULL,
            UNIQUE INDEX UNIQ_4FBF094F5E237E06 (name),
            UNIQUE INDEX UNIQ_4FBF094FB28C852F (tin),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE IF NOT EXISTS auth_user (
            user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:UserId)\',
            company_id CHAR(36) NOT NULL COMMENT \'(DC2Type:CompanyId)\',
            username VARCHAR(180) DEFAULT NULL,
            email VARCHAR(180) NOT NULL,
            enabled TINYINT(1) DEFAULT NULL,
            salt VARCHAR(255) DEFAULT NULL,
            password VARCHAR(255) NOT NULL,
            last_login DATETIME DEFAULT NULL,
            confirmation_token VARCHAR(180) DEFAULT NULL,
            password_requested_at DATETIME DEFAULT NULL,
            roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
            status VARCHAR(50) NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_8D93D649F85E0688 (company_id),
            UNIQUE INDEX UNIQ_8D93D649F85E0677 (username),
            UNIQUE INDEX UNIQ_8D93D649E7927C74 (email),
            UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token),
            PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(/** @lang sql */
            'CREATE TABLE IF NOT EXISTS refresh_tokens (
                id int auto_increment primary key,
                refresh_token varchar(128) not null,
                username varchar(255) not null,
                valid datetime not null,
                constraint UNIQ_9BACE7E1C74F2195 unique (refresh_token)
            ) collate = utf8mb4_unicode_ci'
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE IF EXISTS company');
        $this->addSql('DROP TABLE IF EXISTS auth_user');
    }
}
