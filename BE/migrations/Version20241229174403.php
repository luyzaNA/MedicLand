<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229174403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doctor MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_USER_EMAIL ON doctor');
        $this->addSql('DROP INDEX `primary` ON doctor');
        $this->addSql('ALTER TABLE doctor ADD cnp VARCHAR(50) NOT NULL, ADD first_name VARCHAR(50) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD specialization VARCHAR(255) NOT NULL, ADD role VARCHAR(50) NOT NULL, DROP id, DROP roles');
        $this->addSql('ALTER TABLE doctor ADD PRIMARY KEY (cnp)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE doctor ADD id INT AUTO_INCREMENT NOT NULL, ADD roles JSON NOT NULL, DROP cnp, DROP first_name, DROP last_name, DROP specialization, DROP role, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USER_EMAIL ON doctor (email)');
    }
}
