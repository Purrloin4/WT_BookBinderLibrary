<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230610104514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subscribe (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_68B95F3EA76ED395 (user_id), INDEX IDX_68B95F3E16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3E16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_6834447016A2B381');
        $this->addSql('ALTER TABLE follow DROP FOREIGN KEY FK_68344470A76ED395');
        $this->addSql('DROP TABLE follow');
        $this->addSql('ALTER TABLE book CHANGE published_date published_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE follow (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_68344470A76ED395 (user_id), INDEX IDX_6834447016A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_6834447016A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE follow ADD CONSTRAINT FK_68344470A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE subscribe DROP FOREIGN KEY FK_68B95F3EA76ED395');
        $this->addSql('ALTER TABLE subscribe DROP FOREIGN KEY FK_68B95F3E16A2B381');
        $this->addSql('DROP TABLE subscribe');
        $this->addSql('ALTER TABLE book CHANGE published_date published_date DATE DEFAULT NULL');
    }
}
