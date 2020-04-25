<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200425180949 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE round DROP FOREIGN KEY FK_C5EEEA34E357438D');
        $this->addSql('DROP TABLE word');
        $this->addSql('DROP INDEX IDX_C5EEEA34E357438D ON round');
        $this->addSql('ALTER TABLE round DROP word_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE word (id INT AUTO_INCREMENT NOT NULL, word VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE round ADD word_id INT NOT NULL');
        $this->addSql('ALTER TABLE round ADD CONSTRAINT FK_C5EEEA34E357438D FOREIGN KEY (word_id) REFERENCES word (id)');
        $this->addSql('CREATE INDEX IDX_C5EEEA34E357438D ON round (word_id)');
    }
}
