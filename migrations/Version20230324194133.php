<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230324194133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE newsletter_recipe (newsletter_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_60D2EA7E22DB1917 (newsletter_id), INDEX IDX_60D2EA7E59D8A214 (recipe_id), PRIMARY KEY(newsletter_id, recipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipe_ingredient (recipe_id INT NOT NULL, ingredient_id INT NOT NULL, INDEX IDX_22D1FE1359D8A214 (recipe_id), INDEX IDX_22D1FE13933FE08C (ingredient_id), PRIMARY KEY(recipe_id, ingredient_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE newsletter_recipe ADD CONSTRAINT FK_60D2EA7E22DB1917 FOREIGN KEY (newsletter_id) REFERENCES newsletter (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE newsletter_recipe ADD CONSTRAINT FK_60D2EA7E59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE1359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_ingredient ADD CONSTRAINT FK_22D1FE13933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE liked_by');
        $this->addSql('ALTER TABLE comment ADD comment_recipe_id INT DEFAULT NULL, ADD user_comment_id INT DEFAULT NULL, DROP recipe_id, DROP user_id');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB0A64342 FOREIGN KEY (comment_recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C5F0EBBFF FOREIGN KEY (user_comment_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_9474526CB0A64342 ON comment (comment_recipe_id)');
        $this->addSql('CREATE INDEX IDX_9474526C5F0EBBFF ON comment (user_comment_id)');
        $this->addSql('ALTER TABLE ingredient DROP recipe_id');
        $this->addSql('ALTER TABLE media CHANGE picture image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C59D8A214 ON media (recipe_id)');
        $this->addSql('ALTER TABLE newsletter DROP recipe_id');
        $this->addSql('ALTER TABLE recipe ADD people INT NOT NULL, DROP nbr_person, DROP ingredient_id, DROP liked_by, CHANGE time image VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE role roles JSON NOT NULL, CHANGE is_subscribed is_subscribe TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE liked_by (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recipe_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE newsletter_recipe DROP FOREIGN KEY FK_60D2EA7E22DB1917');
        $this->addSql('ALTER TABLE newsletter_recipe DROP FOREIGN KEY FK_60D2EA7E59D8A214');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE1359D8A214');
        $this->addSql('ALTER TABLE recipe_ingredient DROP FOREIGN KEY FK_22D1FE13933FE08C');
        $this->addSql('DROP TABLE newsletter_recipe');
        $this->addSql('DROP TABLE recipe_ingredient');
        $this->addSql('ALTER TABLE newsletter ADD recipe_id INT NOT NULL');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10C59D8A214');
        $this->addSql('DROP INDEX IDX_6A2CA10C59D8A214 ON media');
        $this->addSql('ALTER TABLE media CHANGE image picture VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE recipe ADD ingredient_id INT NOT NULL, ADD liked_by INT NOT NULL, CHANGE image time VARCHAR(255) NOT NULL, CHANGE people nbr_person INT NOT NULL');
        $this->addSql('ALTER TABLE ingredient ADD recipe_id INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles role JSON NOT NULL, CHANGE is_subscribe is_subscribed TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB0A64342');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C5F0EBBFF');
        $this->addSql('DROP INDEX IDX_9474526CB0A64342 ON comment');
        $this->addSql('DROP INDEX IDX_9474526C5F0EBBFF ON comment');
        $this->addSql('ALTER TABLE comment ADD recipe_id INT NOT NULL, ADD user_id INT NOT NULL, DROP comment_recipe_id, DROP user_comment_id');
    }
}
