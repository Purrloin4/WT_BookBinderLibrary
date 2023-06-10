<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230607100928 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD published_date DATETIME NULL, ADD average_rating DOUBLE PRECISION DEFAULT NULL, ADD ratings_count INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP published_date, DROP average_rating, DROP ratings_count');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP published_date, DROP average_rating, DROP ratings_count');
        $this->addSql('ALTER TABLE user ADD published_date DATETIME DEFAULT NULL, ADD average_rating DOUBLE PRECISION DEFAULT NULL, ADD ratings_count INT DEFAULT NULL');
    }
}
