<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102120708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6917116F6');
        $this->addSql('DROP INDEX IDX_964685A6917116F6 ON consultation');
        $this->addSql('ALTER TABLE consultation DROP specialization_name');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation ADD specialization_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6917116F6 FOREIGN KEY (specialization_name) REFERENCES specialization (name) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_964685A6917116F6 ON consultation (specialization_name)');
    }
}
