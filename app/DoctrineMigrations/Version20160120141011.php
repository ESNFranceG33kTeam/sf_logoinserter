<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160120141011 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE download_session_picture (download_session_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_14CD911258FDD5A2 (download_session_id), INDEX IDX_14CD9112EE45BDBF (picture_id), PRIMARY KEY(download_session_id, picture_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE picture (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE download_session_picture ADD CONSTRAINT FK_14CD911258FDD5A2 FOREIGN KEY (download_session_id) REFERENCES download_session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE download_session_picture ADD CONSTRAINT FK_14CD9112EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE download_session_logo');
        $this->addSql('ALTER TABLE download_session ADD logo_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE download_session ADD CONSTRAINT FK_7C3AD411F98F144A FOREIGN KEY (logo_id) REFERENCES logo (id)');
        $this->addSql('CREATE INDEX IDX_7C3AD411F98F144A ON download_session (logo_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE download_session_picture DROP FOREIGN KEY FK_14CD9112EE45BDBF');
        $this->addSql('CREATE TABLE download_session_logo (download_session_id INT NOT NULL, logo_id INT NOT NULL, INDEX IDX_E76B2C4858FDD5A2 (download_session_id), INDEX IDX_E76B2C48F98F144A (logo_id), PRIMARY KEY(download_session_id, logo_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE download_session_logo ADD CONSTRAINT FK_E76B2C4858FDD5A2 FOREIGN KEY (download_session_id) REFERENCES download_session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE download_session_logo ADD CONSTRAINT FK_E76B2C48F98F144A FOREIGN KEY (logo_id) REFERENCES logo (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE download_session_picture');
        $this->addSql('DROP TABLE picture');
        $this->addSql('ALTER TABLE download_session DROP FOREIGN KEY FK_7C3AD411F98F144A');
        $this->addSql('DROP INDEX IDX_7C3AD411F98F144A ON download_session');
        $this->addSql('ALTER TABLE download_session DROP logo_id');
    }
}
