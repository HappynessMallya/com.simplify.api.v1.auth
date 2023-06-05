<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20230228210849
 * @package DoctrineMigrations
 */
class Version20230228210849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Added new columns to table auth_user';
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql(/** @lang sql */
            'ALTER TABLE auth_user ADD first_name VARCHAR(100) NOT NULL;'
        );

        $this->addSql(/** @lang sql */
            'ALTER TABLE auth_user ADD last_name VARCHAR(100) NOT NULL;'
        );

        $this->addSql(/** @lang sql */
            'ALTER TABLE auth_user ADD mobile_number VARCHAR(20) NULL;'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema): void {}
}
