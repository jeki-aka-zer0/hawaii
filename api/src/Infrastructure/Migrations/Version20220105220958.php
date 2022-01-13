<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220105220958 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create EAV::attribute table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE attribute (attribute_id UUID NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(attribute_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA7AEFFB5E237E06 ON attribute (name)');
        $this->addSql('COMMENT ON COLUMN attribute.attribute_id IS \'(DC2Type:attribute_id)\'');
        $this->addSql('COMMENT ON COLUMN attribute.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN attribute.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE attribute');
    }
}
