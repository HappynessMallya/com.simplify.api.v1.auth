<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230815185844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(/** @lang sql */
            'alter table company drop key UNIQ_4FBF094FB28C852F'
        );

        $this->addSql(/** @lang sql */
            'alter table company add constraint UNIQ_serial_key unique (serial);'
        );

        $this->addSql(/** @lang sql */
            'alter table certificate add serial VARCHAR(50);'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
