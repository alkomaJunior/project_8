<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201115230704 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Add anonymous user and attach old task to it
        $this->addSql('INSERT INTO `user` (`id`, `username`, `password`, `email`) VALUES (NULL, \'anonymous\', \'$argon2id$v=19$m=65536,t=4,p=1$1EZx6ZHXDTRk9IgBN7cQ2w$iXArlkQb4Po299S8qGXWTIqwywbtj+IpiiWCFrfMgbo\', \'anonymous@todolist.de\')');
        $this->addSql('ALTER TABLE task ADD user_id INT NOT NULL DEFAULT 1');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_527EDB25A76ED395 ON task (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25A76ED395');
        $this->addSql('DROP INDEX IDX_527EDB25A76ED395 ON task');
        $this->addSql('ALTER TABLE task DROP user_id');
    }
}
