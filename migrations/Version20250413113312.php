<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413113312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D35DA0FB8');
        $this->addSql('DROP INDEX IDX_161498D35DA0FB8 ON card');
        $this->addSql('ALTER TABLE card DROP template_id, DROP path');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card ADD template_id INT NOT NULL, ADD path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D35DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_161498D35DA0FB8 ON card (template_id)');
    }
}
