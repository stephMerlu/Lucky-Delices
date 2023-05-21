<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230509173723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE liked (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, recipe_id INT DEFAULT NULL, INDEX IDX_CA19CBBAA76ED395 (user_id), INDEX IDX_CA19CBBA59D8A214 (recipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE liked ADD CONSTRAINT FK_CA19CBBAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE liked ADD CONSTRAINT FK_CA19CBBA59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE liked DROP FOREIGN KEY FK_CA19CBBAA76ED395');
        $this->addSql('ALTER TABLE liked DROP FOREIGN KEY FK_CA19CBBA59D8A214');
        $this->addSql('DROP TABLE liked');
    }
}
