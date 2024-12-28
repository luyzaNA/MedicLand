<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241223201744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add unique constraint for the cnp field';
    }

    public function up(Schema $schema): void
    {
        // Create the table if it does not exist
        $this->addSql('CREATE TABLE IF NOT EXISTS patient (
            id INT AUTO_INCREMENT NOT NULL, 
            cnp VARCHAR(50) NOT NULL, 
            first_name VARCHAR(50) NOT NULL, 
            last_name VARCHAR(255) NOT NULL, 
            birth_date DATE NOT NULL, 
            age INT NOT NULL, 
            adress VARCHAR(255) NOT NULL, 
            email VARCHAR(255) NOT NULL, 
            phone VARCHAR(255) NOT NULL, 
            PRIMARY KEY(id),
            UNIQUE INDEX cnp_unique (cnp)  -- Add UNIQUE constraint for cnp
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // Drop the patient table if it exists
        $this->addSql('DROP TABLE IF EXISTS patient');
    }
}
