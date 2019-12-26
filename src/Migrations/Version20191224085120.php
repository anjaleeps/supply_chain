<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191224085120 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE driver CHANGE roles roles JSON NOT NULL, CHANGE work_hours work_hours TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE driver_assistant CHANGE roles roles JSON NOT NULL, CHANGE work_hours work_hours TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE manager CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE date_completed date_completed DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD status VARCHAR(100) NOT NULL, CHANGE picture picture VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE truck_schedule CHANGE start_time start_time DATETIME DEFAULT NULL, CHANGE end_time end_time DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE driver CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE work_hours work_hours TIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE driver_assistant CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE work_hours work_hours TIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE manager CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE orders CHANGE date_completed date_completed DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE product DROP status, CHANGE picture picture VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE truck_schedule CHANGE start_time start_time DATETIME DEFAULT \'NULL\', CHANGE end_time end_time DATETIME DEFAULT \'NULL\'');
    }
}
