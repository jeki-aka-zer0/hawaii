<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220115204557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create EAV::value table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE value (value_id UUID NOT NULL, entity_id UUID NOT NULL, attribute_id UUID NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(value_id))');
        $this->addSql('CREATE INDEX IDX_1D77583481257D5D ON value (entity_id)');
        $this->addSql('CREATE INDEX IDX_1D775834B6E62EFA ON value (attribute_id)');
        $this->addSql('COMMENT ON COLUMN value.value_id IS \'(DC2Type:value_id)\'');
        $this->addSql('COMMENT ON COLUMN value.entity_id IS \'(DC2Type:entity_id)\'');
        $this->addSql('COMMENT ON COLUMN value.attribute_id IS \'(DC2Type:attribute_id)\'');
        $this->addSql('COMMENT ON COLUMN value.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN value.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE value ADD CONSTRAINT FK_1D77583481257D5D FOREIGN KEY (entity_id) REFERENCES entity (entity_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE value ADD CONSTRAINT FK_1D775834B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (attribute_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE value');
    }
}
