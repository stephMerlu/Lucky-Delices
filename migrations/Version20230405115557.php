<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230405115557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, subscribe_newsletter_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649C06CBE41 (subscribe_newsletter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C06CBE41 FOREIGN KEY (subscribe_newsletter_id) REFERENCES newsletter (id)');
        $this->addSql('ALTER TABLE ingredient DROP recipe_id');
        $this->addSql('ALTER TABLE media CHANGE picture image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C59D8A214 ON media (recipe_id)');
        $this->addSql('ALTER TABLE newsletter DROP recipe_id');
        $this->addSql('ALTER TABLE recipe ADD people INT NOT NULL, DROP nbr_person, DROP ingredient_id, DROP liked_by, CHANGE time image VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C06CBE41');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE newsletter ADD recipe_id INT NOT NULL');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C59D8A214');
        $this->addSql('DROP INDEX IDX_6A2CA10C59D8A214 ON media');
        $this->addSql('ALTER TABLE media CHANGE image picture VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE recipe ADD ingredient_id INT NOT NULL, ADD liked_by INT NOT NULL, CHANGE people nbr_person INT NOT NULL, CHANGE image time VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ingredient ADD recipe_id INT NOT NULL');
    }
}
