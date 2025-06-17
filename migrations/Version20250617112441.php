<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250617112441 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card_shared_with (card_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_C5B2D60C4ACC9A20 (card_id), INDEX IDX_C5B2D60CA76ED395 (user_id), PRIMARY KEY(card_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE card_shared_with ADD CONSTRAINT FK_C5B2D60C4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE card_shared_with ADD CONSTRAINT FK_C5B2D60CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card_shared_with DROP FOREIGN KEY FK_C5B2D60C4ACC9A20');
        $this->addSql('ALTER TABLE card_shared_with DROP FOREIGN KEY FK_C5B2D60CA76ED395');
        $this->addSql('DROP TABLE card_shared_with');
    }
}
