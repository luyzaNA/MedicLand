<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250101212334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consultation_disease (consultation_id INT NOT NULL, disease_name VARCHAR(255) NOT NULL, INDEX IDX_4A389B2362FF6CDF (consultation_id), INDEX IDX_4A389B232C4ECEAF (disease_name), PRIMARY KEY(consultation_id, disease_name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consultation_disease ADD CONSTRAINT FK_4A389B2362FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE consultation_disease ADD CONSTRAINT FK_4A389B232C4ECEAF FOREIGN KEY (disease_name) REFERENCES disease (name)');
        $this->addSql('ALTER TABLE consultation DROP diagnosis');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation_disease DROP FOREIGN KEY FK_4A389B2362FF6CDF');
        $this->addSql('ALTER TABLE consultation_disease DROP FOREIGN KEY FK_4A389B232C4ECEAF');
        $this->addSql('DROP TABLE consultation_disease');
        $this->addSql('ALTER TABLE consultation ADD diagnosis VARCHAR(255) NOT NULL');
    }
}
