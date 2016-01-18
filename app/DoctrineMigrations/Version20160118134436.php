<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160118134436 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user ADD firstname VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL, ADD galaxy_username VARCHAR(255) NOT NULL, ADD galaxy_roles LONGTEXT DEFAULT NULL, ADD galaxy_email VARCHAR(255) NOT NULL, ADD nationality VARCHAR(255) NOT NULL, ADD picture VARCHAR(255) DEFAULT NULL, ADD birthday VARCHAR(255) DEFAULT NULL, ADD gender VARCHAR(1) NOT NULL, ADD telephone VARCHAR(15) DEFAULT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD country VARCHAR(255) DEFAULT NULL, ADD section_name VARCHAR(255) NOT NULL, ADD section_code VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user DROP firstname, DROP lastname, DROP galaxy_username, DROP galaxy_roles, DROP galaxy_email, DROP nationality, DROP picture, DROP birthday, DROP gender, DROP telephone, DROP address, DROP country, DROP section_name, DROP section_code');
    }
}
