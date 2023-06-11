<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230611165816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, isbn VARCHAR(13) NOT NULL, title VARCHAR(255) DEFAULT NULL, author VARCHAR(255) DEFAULT NULL, published_date DATE DEFAULT NULL, book_description LONGTEXT DEFAULT NULL, average_rating DOUBLE PRECISION DEFAULT NULL, ratings_count INT DEFAULT NULL, cover_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, commenter_id INT NOT NULL, book_id INT NOT NULL, message LONGTEXT DEFAULT NULL, time_stamp DATETIME NOT NULL, INDEX IDX_9474526CB4D5A9E2 (commenter_id), INDEX IDX_9474526C16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscribe (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, book_id INT NOT NULL, status TINYINT(1) NOT NULL, time_stamp DATETIME NOT NULL, INDEX IDX_68B95F3EA76ED395 (user_id), INDEX IDX_68B95F3E16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, gender VARCHAR(10) DEFAULT NULL, birthday DATE DEFAULT NULL, city VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB4D5A9E2 FOREIGN KEY (commenter_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscribe ADD CONSTRAINT FK_68B95F3E16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB4D5A9E2');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C16A2B381');
        $this->addSql('ALTER TABLE subscribe DROP FOREIGN KEY FK_68B95F3EA76ED395');
        $this->addSql('ALTER TABLE subscribe DROP FOREIGN KEY FK_68B95F3E16A2B381');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE subscribe');
        $this->addSql('DROP TABLE user');
    }
}
