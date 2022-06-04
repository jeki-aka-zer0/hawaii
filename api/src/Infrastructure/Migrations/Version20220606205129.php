<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220606205129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add value to EAV::value table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE value ADD value VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE value DROP value');
    }
}
