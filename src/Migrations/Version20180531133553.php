<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180531133553 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answer ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A253DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_DADD4A253DA5256D ON answer (image_id)');
        $this->addSql('ALTER TABLE question ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_B6F7494E3DA5256D ON question (image_id)');
        $this->addSql('ALTER TABLE quizz ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE quizz ADD CONSTRAINT FK_7C77973D3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('CREATE INDEX IDX_7C77973D3DA5256D ON quizz (image_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A253DA5256D');
        $this->addSql('DROP INDEX IDX_DADD4A253DA5256D ON answer');
        $this->addSql('ALTER TABLE answer DROP image_id');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E3DA5256D');
        $this->addSql('DROP INDEX IDX_B6F7494E3DA5256D ON question');
        $this->addSql('ALTER TABLE question DROP image_id');
        $this->addSql('ALTER TABLE quizz DROP FOREIGN KEY FK_7C77973D3DA5256D');
        $this->addSql('DROP INDEX IDX_7C77973D3DA5256D ON quizz');
        $this->addSql('ALTER TABLE quizz DROP image_id');
    }
}
