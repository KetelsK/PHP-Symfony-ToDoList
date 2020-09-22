<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200826151559 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE to_do_item ADD createdby_id INT NOT NULL');
        $this->addSql('ALTER TABLE to_do_item ADD CONSTRAINT FK_11B395EAF0B5AF0B FOREIGN KEY (createdby_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_11B395EAF0B5AF0B ON to_do_item (createdby_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE to_do_item DROP FOREIGN KEY FK_11B395EAF0B5AF0B');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP INDEX IDX_11B395EAF0B5AF0B ON to_do_item');
        $this->addSql('ALTER TABLE to_do_item DROP createdby_id');
    }
}
