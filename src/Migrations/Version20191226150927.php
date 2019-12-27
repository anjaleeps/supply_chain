<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191226150927 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE store_manager (id INT AUTO_INCREMENT NOT NULL, store_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_E32C045EE7927C74 (email), INDEX IDX_E32C045EB092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE store_manager ADD CONSTRAINT FK_E32C045EB092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE customer CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE driver CHANGE roles roles JSON NOT NULL, CHANGE work_hours work_hours TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE driver_assistant CHANGE roles roles JSON NOT NULL, CHANGE work_hours work_hours TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE manager CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE orders CHANGE date_completed date_completed DATE DEFAULT NULL');
        $this->addSql('DROP INDEX category ON product');
        $this->addSql('ALTER TABLE product CHANGE picture picture VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE transports ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE truck_schedule CHANGE start_time start_time DATETIME DEFAULT NULL, CHANGE end_time end_time DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE store_manager');
        $this->addSql('ALTER TABLE customer CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE driver CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE work_hours work_hours TIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE driver_assistant CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE work_hours work_hours TIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE manager CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE orders CHANGE date_completed date_completed DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE product CHANGE picture picture VARCHAR(500) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE INDEX category ON product (category)');
        $this->addSql('ALTER TABLE transports DROP status');
        $this->addSql('ALTER TABLE truck_schedule CHANGE start_time start_time DATETIME DEFAULT \'NULL\', CHANGE end_time end_time DATETIME DEFAULT \'NULL\'');
    }
}
