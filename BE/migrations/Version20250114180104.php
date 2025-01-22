<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250114180104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE consultation (id INT AUTO_INCREMENT NOT NULL, patient_cnp VARCHAR(13) NOT NULL, doctor_email VARCHAR(255) NOT NULL, date DATETIME NOT NULL, medication LONGTEXT DEFAULT NULL, symptoms LONGTEXT DEFAULT NULL, INDEX IDX_964685A65CCDB3E7 (patient_cnp), INDEX IDX_964685A6AA669C12 (doctor_email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consultation_disease (consultation_id INT NOT NULL, disease_name VARCHAR(255) NOT NULL, INDEX IDX_4A389B2362FF6CDF (consultation_id), INDEX IDX_4A389B232C4ECEAF (disease_name), PRIMARY KEY(consultation_id, disease_name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE disease (name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (cnp VARCHAR(13) NOT NULL, email VARCHAR(100) DEFAULT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, age INT NOT NULL, locality VARCHAR(50) NOT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) NOT NULL, blood_group VARCHAR(255) DEFAULT NULL, rh VARCHAR(255) DEFAULT NULL, weight DOUBLE PRECISION NOT NULL, height DOUBLE PRECISION NOT NULL, allergies LONGTEXT DEFAULT NULL, occupation VARCHAR(100) NOT NULL, record_date DATE NOT NULL, sex VARCHAR(1) NOT NULL, UNIQUE INDEX UNIQ_1ADAD7EBE7927C74 (email), PRIMARY KEY(cnp)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient_disease (patient_cnp VARCHAR(13) NOT NULL, disease_name VARCHAR(255) NOT NULL, INDEX IDX_52583F2A5CCDB3E7 (patient_cnp), INDEX IDX_52583F2A2C4ECEAF (disease_name), PRIMARY KEY(patient_cnp, disease_name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialization (name VARCHAR(255) NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (email VARCHAR(255) NOT NULL, specialization_name VARCHAR(255) DEFAULT NULL, patient_email VARCHAR(100) DEFAULT NULL, password VARCHAR(255) NOT NULL, roles LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', cnp VARCHAR(50) DEFAULT NULL, first_name VARCHAR(50) DEFAULT NULL, last_name VARCHAR(50) DEFAULT NULL, INDEX IDX_1483A5E9917116F6 (specialization_name), UNIQUE INDEX UNIQ_1483A5E972A4A7BB (patient_email), PRIMARY KEY(email)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A65CCDB3E7 FOREIGN KEY (patient_cnp) REFERENCES patient (cnp)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6AA669C12 FOREIGN KEY (doctor_email) REFERENCES users (email)');
        $this->addSql('ALTER TABLE consultation_disease ADD CONSTRAINT FK_4A389B2362FF6CDF FOREIGN KEY (consultation_id) REFERENCES consultation (id)');
        $this->addSql('ALTER TABLE consultation_disease ADD CONSTRAINT FK_4A389B232C4ECEAF FOREIGN KEY (disease_name) REFERENCES disease (name)');
        $this->addSql('ALTER TABLE patient_disease ADD CONSTRAINT FK_52583F2A5CCDB3E7 FOREIGN KEY (patient_cnp) REFERENCES patient (cnp)');
        $this->addSql('ALTER TABLE patient_disease ADD CONSTRAINT FK_52583F2A2C4ECEAF FOREIGN KEY (disease_name) REFERENCES disease (name)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9917116F6 FOREIGN KEY (specialization_name) REFERENCES specialization (name)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E972A4A7BB FOREIGN KEY (patient_email) REFERENCES patient (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A65CCDB3E7');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6AA669C12');
        $this->addSql('ALTER TABLE consultation_disease DROP FOREIGN KEY FK_4A389B2362FF6CDF');
        $this->addSql('ALTER TABLE consultation_disease DROP FOREIGN KEY FK_4A389B232C4ECEAF');
        $this->addSql('ALTER TABLE patient_disease DROP FOREIGN KEY FK_52583F2A5CCDB3E7');
        $this->addSql('ALTER TABLE patient_disease DROP FOREIGN KEY FK_52583F2A2C4ECEAF');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9917116F6');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E972A4A7BB');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE consultation_disease');
        $this->addSql('DROP TABLE disease');
        $this->addSql('DROP TABLE patient');
        $this->addSql('DROP TABLE patient_disease');
        $this->addSql('DROP TABLE specialization');
        $this->addSql('DROP TABLE users');
    }
}
