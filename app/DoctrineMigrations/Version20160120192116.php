<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160120192116 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE download_session_picture');
        $this->addSql('ALTER TABLE picture ADD download_session_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE picture ADD CONSTRAINT FK_16DB4F8958FDD5A2 FOREIGN KEY (download_session_id) REFERENCES download_session (id)');
        $this->addSql('CREATE INDEX IDX_16DB4F8958FDD5A2 ON picture (download_session_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE download_session_picture (download_session_id INT NOT NULL, picture_id INT NOT NULL, INDEX IDX_14CD911258FDD5A2 (download_session_id), INDEX IDX_14CD9112EE45BDBF (picture_id), PRIMARY KEY(download_session_id, picture_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE download_session_picture ADD CONSTRAINT FK_14CD9112EE45BDBF FOREIGN KEY (picture_id) REFERENCES picture (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE download_session_picture ADD CONSTRAINT FK_14CD911258FDD5A2 FOREIGN KEY (download_session_id) REFERENCES download_session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE picture DROP FOREIGN KEY FK_16DB4F8958FDD5A2');
        $this->addSql('DROP INDEX IDX_16DB4F8958FDD5A2 ON picture');
        $this->addSql('ALTER TABLE picture DROP download_session_id');
    }
}
