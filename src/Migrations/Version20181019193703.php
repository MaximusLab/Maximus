<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181019193703 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE articles (id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT \'Article ID\', author_id INT UNSIGNED DEFAULT NULL COMMENT \'Author ID\', title VARCHAR(128) NOT NULL COMMENT \'Article title\', alias VARCHAR(128) NOT NULL COMMENT \'Article alias name, use alias to create article URL\', markdown_content LONGTEXT NOT NULL COMMENT \'Article Markdown content\', html_content LONGTEXT NOT NULL COMMENT \'Article HTML content\', published TINYINT(1) NOT NULL COMMENT \'Article published or not\', published_at DATETIME DEFAULT NULL COMMENT \'Article published datetime\', created_at DATETIME NOT NULL COMMENT \'Article created datetime\', UNIQUE INDEX UNIQ_BFDD3168E16C6B94 (alias), INDEX IDX_BFDD3168F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE authors (id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT \'Author ID\', name VARCHAR(128) NOT NULL COMMENT \'Author name\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT \'Setting ID\', `key` VARCHAR(32) NOT NULL COMMENT \'Setting key\', value LONGTEXT NOT NULL COMMENT \'Setting value\', UNIQUE INDEX UNIQ_E545A0C58A90ABA9 (`key`), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT \'Tag ID\', title VARCHAR(128) NOT NULL COMMENT \'Tag title\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article_tag_mapping (tag_id INT UNSIGNED NOT NULL COMMENT \'Tag ID\', article_id INT UNSIGNED NOT NULL COMMENT \'Article ID\', INDEX IDX_E1546B47BAD26311 (tag_id), INDEX IDX_E1546B477294869C (article_id), PRIMARY KEY(tag_id, article_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168F675F31B FOREIGN KEY (author_id) REFERENCES authors (id)');
        $this->addSql('ALTER TABLE article_tag_mapping ADD CONSTRAINT FK_E1546B47BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id)');
        $this->addSql('ALTER TABLE article_tag_mapping ADD CONSTRAINT FK_E1546B477294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article_tag_mapping DROP FOREIGN KEY FK_E1546B477294869C');
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168F675F31B');
        $this->addSql('ALTER TABLE article_tag_mapping DROP FOREIGN KEY FK_E1546B47BAD26311');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE authors');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE article_tag_mapping');
    }
}
