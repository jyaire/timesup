<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200418133353 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE word (id INT AUTO_INCREMENT NOT NULL, word VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE round (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, word_id INT NOT NULL, round1winner_id INT DEFAULT NULL, round2winner_id INT DEFAULT NULL, round3winner_id INT DEFAULT NULL, INDEX IDX_C5EEEA34E48FD905 (game_id), INDEX IDX_C5EEEA34E357438D (word_id), INDEX IDX_C5EEEA341931EF2E (round1winner_id), INDEX IDX_C5EEEA34F206542D (round2winner_id), INDEX IDX_C5EEEA341DC43F13 (round3winner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA341931EF2E FOREIGN KEY (round1winner_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34F206542D FOREIGN KEY (round2winner_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA341DC43F13 FOREIGN KEY (round3winner_id) REFERENCES team (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34E357438D');
        $this->addSql('DROP TABLE word');
        $this->addSql('DROP TABLE round');
    }
}
