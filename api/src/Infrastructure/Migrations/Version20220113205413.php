<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220113205413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add type to attribute table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE attribute ADD type SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('COMMENT ON COLUMN attribute.type IS \'(DC2Type:attribute_type)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE attribute DROP type');
    }
}
