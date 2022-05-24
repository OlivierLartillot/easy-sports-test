<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220311083320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE test_tag (test_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_7AF46B441E5D0459 (test_id), INDEX IDX_7AF46B44BAD26311 (tag_id), PRIMARY KEY(test_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE test_tag ADD CONSTRAINT FK_7AF46B441E5D0459 FOREIGN KEY (test_id) REFERENCES test (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE test_tag ADD CONSTRAINT FK_7AF46B44BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE tag_test');
        $this->addSql('ALTER TABLE tag ADD is_primary TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag_test (id INT AUTO_INCREMENT NOT NULL, tag_id INT NOT NULL, test_id INT NOT NULL, is_primary INT NOT NULL, INDEX IDX_3670C1BABAD26311 (tag_id), INDEX IDX_3670C1BA1E5D0459 (test_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE tag_test ADD CONSTRAINT FK_3670C1BABAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('ALTER TABLE tag_test ADD CONSTRAINT FK_3670C1BA1E5D0459 FOREIGN KEY (test_id) REFERENCES test (id)');
        $this->addSql('DROP TABLE test_tag');
        $this->addSql('ALTER TABLE tag DROP is_primary');
    }
}
