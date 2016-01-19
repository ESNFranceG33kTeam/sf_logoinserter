<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160118204650 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE download_session (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, INDEX IDX_7C3AD4117E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE download_session_logo (download_session_id INT NOT NULL, logo_id INT NOT NULL, INDEX IDX_E76B2C4858FDD5A2 (download_session_id), INDEX IDX_E76B2C48F98F144A (logo_id), PRIMARY KEY(download_session_id, logo_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE download_session ADD CONSTRAINT FK_7C3AD4117E3C61F9 FOREIGN KEY (owner_id) REFERENCES fos_user (id)');
        $this->addSql('ALTER TABLE download_session_logo ADD CONSTRAINT FK_E76B2C4858FDD5A2 FOREIGN KEY (download_session_id) REFERENCES download_session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE download_session_logo ADD CONSTRAINT FK_E76B2C48F98F144A FOREIGN KEY (logo_id) REFERENCES logo (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE download_session_logo DROP FOREIGN KEY FK_E76B2C4858FDD5A2');
        $this->addSql('DROP TABLE download_session');
        $this->addSql('DROP TABLE download_session_logo');
    }
}
