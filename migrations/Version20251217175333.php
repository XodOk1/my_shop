<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251217175333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car (id INT AUTO_INCREMENT NOT NULL, brand VARCHAR(255) NOT NULL, model VARCHAR(255) NOT NULL, license_plate VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE driver (id INT AUTO_INCREMENT NOT NULL, car_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, snils VARCHAR(14) DEFAULT NULL, license_driver VARCHAR(20) NOT NULL, license_driver_date DATE DEFAULT NULL, INDEX IDX_11667CD9C3C6F69F (car_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fuel_card (id INT AUTO_INCREMENT NOT NULL, trip_list_id INT NOT NULL, liters DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_66BC0C2B425326 (trip_list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trip (id INT AUTO_INCREMENT NOT NULL, order_number VARCHAR(255) NOT NULL, address VARCHAR(500) NOT NULL, distance DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trip_list (id INT AUTO_INCREMENT NOT NULL, car_id INT DEFAULT NULL, trip_id INT DEFAULT NULL, driver_id INT DEFAULT NULL, user_id INT DEFAULT NULL, start_point VARCHAR(255) DEFAULT NULL, type_message VARCHAR(255) DEFAULT NULL, fuel_start DOUBLE PRECISION DEFAULT NULL, fuel_end DOUBLE PRECISION DEFAULT NULL, fuel_used DOUBLE PRECISION DEFAULT NULL, km_start DOUBLE PRECISION DEFAULT NULL, km_end DOUBLE PRECISION DEFAULT NULL, fuel_start_fact DOUBLE PRECISION DEFAULT NULL, fuel_end_fact DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6FF06B73C3C6F69F (car_id), INDEX IDX_6FF06B73A5BC2E0E (trip_id), INDEX IDX_6FF06B73C3423909 (driver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trip_order (id INT AUTO_INCREMENT NOT NULL, trip_sheet_id INT NOT NULL, number_order INT NOT NULL, address VARCHAR(255) NOT NULL, INDEX IDX_2F6213DB234335B5 (trip_sheet_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // $this->addSql('CREATE TABLE trip_sheet (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, route VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE driver ADD CONSTRAINT FK_11667CD9C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE fuel_card ADD CONSTRAINT FK_66BC0C2B425326 FOREIGN KEY (trip_list_id) REFERENCES trip_list (id)');
        $this->addSql('ALTER TABLE trip_list ADD CONSTRAINT FK_6FF06B73C3C6F69F FOREIGN KEY (car_id) REFERENCES car (id)');
        $this->addSql('ALTER TABLE trip_list ADD CONSTRAINT FK_6FF06B73A5BC2E0E FOREIGN KEY (trip_id) REFERENCES trip (id)');
        $this->addSql('ALTER TABLE trip_list ADD CONSTRAINT FK_6FF06B73C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('ALTER TABLE trip_order ADD CONSTRAINT FK_2F6213DB234335B5 FOREIGN KEY (trip_sheet_id) REFERENCES trip_sheet (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE driver DROP FOREIGN KEY FK_11667CD9C3C6F69F');
        $this->addSql('ALTER TABLE fuel_card DROP FOREIGN KEY FK_66BC0C2B425326');
        $this->addSql('ALTER TABLE trip_list DROP FOREIGN KEY FK_6FF06B73C3C6F69F');
        $this->addSql('ALTER TABLE trip_list DROP FOREIGN KEY FK_6FF06B73A5BC2E0E');
        $this->addSql('ALTER TABLE trip_list DROP FOREIGN KEY FK_6FF06B73C3423909');
        $this->addSql('ALTER TABLE trip_order DROP FOREIGN KEY FK_2F6213DB234335B5');
        $this->addSql('DROP TABLE car');
        $this->addSql('DROP TABLE driver');
        $this->addSql('DROP TABLE fuel_card');
        $this->addSql('DROP TABLE trip');
        $this->addSql('DROP TABLE trip_list');
        $this->addSql('DROP TABLE trip_order');
        $this->addSql('DROP TABLE trip_sheet');
    }
}
