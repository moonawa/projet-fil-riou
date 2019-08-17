<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190816092422 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE utilisateur ADD compte_id INT DEFAULT NULL, ADD updated_at DATETIME NOT NULL, CHANGE photo image_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3F2C56620 ON utilisateur (compte_id)');
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
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3F2C56620');
        $this->addSql('DROP INDEX IDX_1D1C63B3F2C56620 ON utilisateur');
        $this->addSql('ALTER TABLE utilisateur DROP compte_id, DROP updated_at, CHANGE image_name photo VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
