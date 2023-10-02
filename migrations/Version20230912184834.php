<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230912184834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB0A64342');
        $this->addSql('DROP INDEX IDX_9474526CB0A64342 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE comment_recipe_id recipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9474526C59D8A214 ON comment (recipe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C59D8A214');
        $this->addSql('DROP INDEX IDX_9474526C59D8A214 ON comment');
        $this->addSql('ALTER TABLE comment CHANGE recipe_id comment_recipe_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB0A64342 FOREIGN KEY (comment_recipe_id) REFERENCES recipe (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9474526CB0A64342 ON comment (comment_recipe_id)');
    }
}
