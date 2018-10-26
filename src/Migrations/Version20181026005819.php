<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181026005819 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE articles ADD background_image_path VARCHAR(256) DEFAULT NULL COMMENT \'Article background image path\'');
        $this->addSql('ALTER TABLE settings RENAME INDEX uniq_e545a0c58a90aba9 TO UNIQ_E545A0C54E645A7E');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE articles DROP background_image_path');
        $this->addSql('ALTER TABLE settings RENAME INDEX uniq_e545a0c54e645a7e TO UNIQ_E545A0C58A90ABA9');
    }
}
