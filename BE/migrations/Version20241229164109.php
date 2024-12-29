<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241229164109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON patient');
        $this->addSql('ALTER TABLE patient DROP id, CHANGE adress address VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE patient ADD PRIMARY KEY (cnp)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient ADD id INT AUTO_INCREMENT NOT NULL, CHANGE address adress VARCHAR(255) NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
