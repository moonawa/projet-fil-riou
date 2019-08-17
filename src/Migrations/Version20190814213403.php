<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190814213403 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE utilisateur ADD image_name VARCHAR(255) NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260A4AEAFEA');
        $this->addSql('DROP INDEX IDX_CFF65260A4AEAFEA ON compte');
        $this->addSql('ALTER TABLE compte ADD entreprise VARCHAR(255) NOT NULL, DROP entreprise_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE compte ADD entreprise_id INT NOT NULL, DROP entreprise');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_CFF65260A4AEAFEA ON compte (entreprise_id)');
        $this->addSql('ALTER TABLE utilisateur DROP image_name, DROP updated_at');
    }
}
