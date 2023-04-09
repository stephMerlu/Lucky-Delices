<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409160211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649C06CBE41');
        $this->addSql('ALTER TABLE newsletter_recipe DROP FOREIGN KEY FK_60D2EA7E22DB1917');
        $this->addSql('ALTER TABLE newsletter_recipe DROP FOREIGN KEY FK_60D2EA7E59D8A214');
        $this->addSql('DROP TABLE newsletter_recipe');
        $this->addSql('DROP TABLE newsletter');
        $this->addSql('DROP INDEX IDX_8D93D649C06CBE41 ON user');
        $this->addSql('ALTER TABLE user DROP subscribe_newsletter_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE newsletter_recipe (newsletter_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_60D2EA7E22DB1917 (newsletter_id), INDEX IDX_60D2EA7E59D8A214 (recipe_id), PRIMARY KEY(newsletter_id, recipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE newsletter (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, send_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE newsletter_recipe ADD CONSTRAINT FK_60D2EA7E22DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE newsletter_recipe ADD CONSTRAINT FK_60D2EA7E59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD subscribe_newsletter_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649C06CBE41 FOREIGN KEY (subscribe_newsletter_id) REFERENCES newsletter (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D649C06CBE41 ON user (subscribe_newsletter_id)');
    }
}
