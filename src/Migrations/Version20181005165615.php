<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181005165615 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql(
            'CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE province (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE waypoint (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, province_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, lat NUMERIC(10, 6) NOT NULL, lon NUMERIC(10, 6) NOT NULL, INDEX IDX_B3DC588112469DE2 (category_id), INDEX IDX_B3DC5881E946114A (province_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE waypoint ADD CONSTRAINT FK_B3DC588112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)'
        );
        $this->addSql(
            'ALTER TABLE waypoint ADD CONSTRAINT FK_B3DC5881E946114A FOREIGN KEY (province_id) REFERENCES province (id)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE waypoint DROP FOREIGN KEY FK_B3DC588112469DE2');
        $this->addSql('ALTER TABLE waypoint DROP FOREIGN KEY FK_B3DC5881E946114A');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE province');
        $this->addSql('DROP TABLE waypoint');
    }
}
