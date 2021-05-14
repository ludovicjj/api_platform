<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210514075137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_key (id INT AUTO_INCREMENT NOT NULL, api_key VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD api_key_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498BE312B3 FOREIGN KEY (api_key_id) REFERENCES api_key (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6498BE312B3 ON user (api_key_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498BE312B3');
        $this->addSql('DROP TABLE api_key');
        $this->addSql('DROP INDEX UNIQ_8D93D6498BE312B3 ON user');
        $this->addSql('ALTER TABLE user DROP api_key_id');
    }
}
