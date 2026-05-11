<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260419092416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oauth_identity (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, provider VARCHAR(255) NOT NULL, provider_id BIGINT NOT NULL, INDEX IDX_4AE96AD8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE oauth_identity ADD CONSTRAINT FK_4AE96AD8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
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
        $this->addSql('ALTER TABLE oauth_identity DROP FOREIGN KEY FK_4AE96AD8A76ED395');
        $this->addSql('DROP TABLE oauth_identity');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE trip_order DROP FOREIGN KEY FK_2F6213DB234335B5');
        $this->addSql('ALTER TABLE fuel_card DROP FOREIGN KEY FK_66BC0C2B425326');
        $this->addSql('ALTER TABLE trip_list DROP FOREIGN KEY FK_6FF06B73C3C6F69F');
        $this->addSql('ALTER TABLE trip_list DROP FOREIGN KEY FK_6FF06B73A5BC2E0E');
        $this->addSql('ALTER TABLE trip_list DROP FOREIGN KEY FK_6FF06B73C3423909');
        $this->addSql('ALTER TABLE driver DROP FOREIGN KEY FK_11667CD9C3C6F69F');
    }
}
