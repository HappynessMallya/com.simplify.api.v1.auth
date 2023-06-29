<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20230616100457
 * @package DoctrineMigrations
 */
class Version20230616100457 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql(/** @lang sql */
            'ALTER TABLE organization ADD owner_name VARCHAR(200) NOT NULL AFTER name'
        );

        $this->addSql(/** @lang sql */
            'ALTER TABLE organization ADD owner_email VARCHAR(50) NOT NULL AFTER owner_name'
        );

        $this->addSql(/** @lang sql */
            'ALTER TABLE organization ADD owner_phone_number VARCHAR(50) NOT NULL AFTER owner_email'
        );

        $this->addSql(/** @lang sql */
            'ALTER TABLE organization ADD CONSTRAINT unique_name UNIQUE (name)'
        );

        $this->addSql(/** @lang sql */
            'ALTER TABLE auth_user ADD updated_at DATETIME AFTER created_at'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void {}
}
