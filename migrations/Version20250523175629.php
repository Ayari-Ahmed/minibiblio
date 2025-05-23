<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523175629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE livre ADD publication_year INT DEFAULT NULL, ADD language VARCHAR(255) DEFAULT NULL, ADD publisher VARCHAR(255) DEFAULT NULL, ADD number_of_pages INT DEFAULT NULL, DROP updated_at, CHANGE image genre VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE livre ADD image VARCHAR(255) DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, DROP publication_year, DROP genre, DROP language, DROP publisher, DROP number_of_pages
        SQL);
    }
}
