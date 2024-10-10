<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241008154400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, token VARCHAR(36) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE characters CHANGE birth_date birth_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE characters RENAME INDEX equipment_id TO IDX_3A29410E517FE9FE');
        $this->addSql('ALTER TABLE characters RENAME INDEX faction_id TO IDX_3A29410E4448F8DA');
        $this->addSql('ALTER TABLE factions CHANGE description description LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE `characters` CHANGE birth_date birth_date DATE NOT NULL');
        $this->addSql('ALTER TABLE `characters` RENAME INDEX idx_3a29410e517fe9fe TO equipment_id');
        $this->addSql('ALTER TABLE `characters` RENAME INDEX idx_3a29410e4448f8da TO faction_id');
        $this->addSql('ALTER TABLE `factions` CHANGE description description TEXT NOT NULL');
    }
}
