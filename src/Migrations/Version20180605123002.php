<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180605123002 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question ADD quizz_id INT NOT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494EBA934BCD FOREIGN KEY (quizz_id) REFERENCES quizz (id)');
        $this->addSql('CREATE INDEX IDX_B6F7494EBA934BCD ON question (quizz_id)');
        $this->addSql('ALTER TABLE quizz DROP FOREIGN KEY FK_7C77973D3DA5256D');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973D3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494EBA934BCD');
        $this->addSql('DROP INDEX IDX_B6F7494EBA934BCD ON question');
        $this->addSql('ALTER TABLE question DROP quizz_id');
        $this->addSql('ALTER TABLE quizz DROP FOREIGN KEY FK_7C77973D3DA5256D');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973D3DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON UPDATE CASCADE ON DELETE SET NULL');
    }
}
