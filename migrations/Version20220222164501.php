<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220222164501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE waypoint DROP FOREIGN KEY FK_B3DC5881E946114A');
        $this->addSql('ALTER TABLE waypoint DROP FOREIGN KEY FK_B3DC588112469DE2');
        $this->addSql('DROP INDEX IDX_B3DC588112469DE2 ON waypoint');
        $this->addSql('DROP INDEX IDX_B3DC5881E946114A ON waypoint');
        $this->addSql('ALTER TABLE waypoint DROP category_id, DROP province_id, DROP description, DROP city');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE province CHANGE name name VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE waypoint ADD category_id INT DEFAULT NULL, ADD province_id INT DEFAULT NULL, ADD description VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD city VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE guid guid VARCHAR(100) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE waypoint ADD CONSTRAINT FK_B3DC5881E946114A FOREIGN KEY (province_id) REFERENCES province (id)');
        $this->addSql('ALTER TABLE waypoint ADD CONSTRAINT FK_B3DC588112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_B3DC588112469DE2 ON waypoint (category_id)');
        $this->addSql('CREATE INDEX IDX_B3DC5881E946114A ON waypoint (province_id)');
    }
}
