<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191214093835 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE customer (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, customer_type VARCHAR(50) NOT NULL, place_no VARCHAR(10) NOT NULL, street VARCHAR(100) NOT NULL, city VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_81398E09E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE driver (id INT AUTO_INCREMENT NOT NULL, store_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, work_hours TIME DEFAULT NULL, status VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_11667CD9E7927C74 (email), INDEX IDX_11667CD9B092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE driver_assistant (id INT AUTO_INCREMENT NOT NULL, store_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, work_hours TIME DEFAULT NULL, status VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_EDBB77C8E7927C74 (email), INDEX IDX_EDBB77C8B092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manager (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_FA2425B9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_product (orders_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_2530ADE6CFFE9AD6 (orders_id), INDEX IDX_2530ADE64584665A (product_id), PRIMARY KEY(orders_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, route_id INT NOT NULL, order_status VARCHAR(10) NOT NULL, date_placed DATE NOT NULL, date_completed DATE DEFAULT NULL, INDEX IDX_E52FFDEE9395C3F3 (customer_id), INDEX IDX_E52FFDEE34ECB4E6 (route_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE phone_number (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, phone_number VARCHAR(15) NOT NULL, INDEX IDX_6B01BC5B9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, unit_price NUMERIC(8, 2) NOT NULL, size NUMERIC(6, 3) NOT NULL, picture VARCHAR(500) DEFAULT NULL, category VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE route (id INT AUTO_INCREMENT NOT NULL, store_id INT NOT NULL, decription VARCHAR(500) NOT NULL, max_time TIME NOT NULL, INDEX IDX_2C42079B092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE store (id INT AUTO_INCREMENT NOT NULL, city VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE train_schedule (id INT AUTO_INCREMENT NOT NULL, destination VARCHAR(50) NOT NULL, capacity NUMERIC(6, 3) NOT NULL, start_time TIME NOT NULL, journey_time TIME NOT NULL, day VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transports (train_schedule_id INT NOT NULL, orders_id INT NOT NULL, date DATE NOT NULL, INDEX IDX_C7BE69E52AAD00B3 (train_schedule_id), INDEX IDX_C7BE69E5CFFE9AD6 (orders_id), PRIMARY KEY(train_schedule_id, orders_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE truck (id INT AUTO_INCREMENT NOT NULL, store_id INT NOT NULL, truck_no VARCHAR(20) NOT NULL, used_hours TIME NOT NULL, status VARCHAR(10) NOT NULL, INDEX IDX_CDCCF30AB092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE truck_order (orders_id INT NOT NULL, truck_schedule_id INT NOT NULL, INDEX IDX_74F22242CFFE9AD6 (orders_id), INDEX IDX_74F222422BAB9317 (truck_schedule_id), PRIMARY KEY(orders_id, truck_schedule_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE truck_schedule (id INT AUTO_INCREMENT NOT NULL, truck_id INT NOT NULL, driver_id INT NOT NULL, driver_assistant_id INT NOT NULL, route_id INT NOT NULL, start_time DATETIME DEFAULT NULL, end_time DATETIME DEFAULT NULL, status VARCHAR(10) NOT NULL, INDEX IDX_36F93539C6957CCE (truck_id), INDEX IDX_36F93539C3423909 (driver_id), INDEX IDX_36F93539C5308710 (driver_assistant_id), INDEX IDX_36F9353934ECB4E6 (route_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE driver ADD CONSTRAINT FK_11667CD9B092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE driver_assistant ADD CONSTRAINT FK_EDBB77C8B092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE6CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_product ADD CONSTRAINT FK_2530ADE64584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE34ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id)');
        $this->addSql('ALTER TABLE phone_number ADD CONSTRAINT FK_6B01BC5B9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C42079B092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE transports ADD CONSTRAINT FK_C7BE69E52AAD00B3 FOREIGN KEY (train_schedule_id) REFERENCES train_schedule (id)');
        $this->addSql('ALTER TABLE transports ADD CONSTRAINT FK_C7BE69E5CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE truck ADD CONSTRAINT FK_CDCCF30AB092A811 FOREIGN KEY (store_id) REFERENCES store (id)');
        $this->addSql('ALTER TABLE truck_order ADD CONSTRAINT FK_74F22242CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE truck_order ADD CONSTRAINT FK_74F222422BAB9317 FOREIGN KEY (truck_schedule_id) REFERENCES truck_schedule (id)');
        $this->addSql('ALTER TABLE truck_schedule ADD CONSTRAINT FK_36F93539C6957CCE FOREIGN KEY (truck_id) REFERENCES truck (id)');
        $this->addSql('ALTER TABLE truck_schedule ADD CONSTRAINT FK_36F93539C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('ALTER TABLE truck_schedule ADD CONSTRAINT FK_36F93539C5308710 FOREIGN KEY (driver_assistant_id) REFERENCES driver_assistant (id)');
        $this->addSql('ALTER TABLE truck_schedule ADD CONSTRAINT FK_36F9353934ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE9395C3F3');
        $this->addSql('ALTER TABLE phone_number DROP FOREIGN KEY FK_6B01BC5B9395C3F3');
        $this->addSql('ALTER TABLE truck_schedule DROP FOREIGN KEY FK_36F93539C3423909');
        $this->addSql('ALTER TABLE truck_schedule DROP FOREIGN KEY FK_36F93539C5308710');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE6CFFE9AD6');
        $this->addSql('ALTER TABLE transports DROP FOREIGN KEY FK_C7BE69E5CFFE9AD6');
        $this->addSql('ALTER TABLE truck_order DROP FOREIGN KEY FK_74F22242CFFE9AD6');
        $this->addSql('ALTER TABLE order_product DROP FOREIGN KEY FK_2530ADE64584665A');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE34ECB4E6');
        $this->addSql('ALTER TABLE truck_schedule DROP FOREIGN KEY FK_36F9353934ECB4E6');
        $this->addSql('ALTER TABLE driver DROP FOREIGN KEY FK_11667CD9B092A811');
        $this->addSql('ALTER TABLE driver_assistant DROP FOREIGN KEY FK_EDBB77C8B092A811');
        $this->addSql('ALTER TABLE route DROP FOREIGN KEY FK_2C42079B092A811');
        $this->addSql('ALTER TABLE truck DROP FOREIGN KEY FK_CDCCF30AB092A811');
        $this->addSql('ALTER TABLE transports DROP FOREIGN KEY FK_C7BE69E52AAD00B3');
        $this->addSql('ALTER TABLE truck_schedule DROP FOREIGN KEY FK_36F93539C6957CCE');
        $this->addSql('ALTER TABLE truck_order DROP FOREIGN KEY FK_74F222422BAB9317');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE driver');
        $this->addSql('DROP TABLE driver_assistant');
        $this->addSql('DROP TABLE manager');
        $this->addSql('DROP TABLE order_product');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE phone_number');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE store');
        $this->addSql('DROP TABLE train_schedule');
        $this->addSql('DROP TABLE transports');
        $this->addSql('DROP TABLE truck');
        $this->addSql('DROP TABLE truck_order');
        $this->addSql('DROP TABLE truck_schedule');
    }
}
