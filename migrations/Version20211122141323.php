<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211122141323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, task_id INT NOT NULL, action VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9218FF79A76ED395 (user_id), INDEX IDX_9218FF798DB60186 (task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, next_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, duration INT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, INDEX IDX_64C19C1A76ED395 (user_id), INDEX IDX_64C19C1AA23F6C8 (next_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE gantt_task (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, slots VARCHAR(255) DEFAULT NULL, INDEX IDX_1F25B7DDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task (id INT AUTO_INCREMENT NOT NULL, reporter_id INT NOT NULL, assignee_id INT DEFAULT NULL, parent_task_id INT DEFAULT NULL, category_id INT NOT NULL, project_id INT DEFAULT NULL, priority INT DEFAULT NULL, expected_duration DOUBLE PRECISION DEFAULT NULL, actual_duration DOUBLE PRECISION DEFAULT NULL, description LONGTEXT DEFAULT NULL, title VARCHAR(255) NOT NULL, wanted_duration DOUBLE PRECISION NOT NULL, due_at DATETIME DEFAULT NULL, INDEX IDX_527EDB25E1CFE6F5 (reporter_id), INDEX IDX_527EDB2559EC7D60 (assignee_id), INDEX IDX_527EDB25FFFE75C0 (parent_task_id), INDEX IDX_527EDB2512469DE2 (category_id), INDEX IDX_527EDB25166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE task_category (task_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_468CF38D8DB60186 (task_id), INDEX IDX_468CF38D12469DE2 (category_id), PRIMARY KEY(task_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time_date (id INT AUTO_INCREMENT NOT NULL, time_detail_id INT NOT NULL, user_id INT NOT NULL, date DATE NOT NULL, duration DOUBLE PRECISION NOT NULL, INDEX IDX_7233E574AA60863 (time_detail_id), INDEX IDX_7233E57A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time_detail (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, time_report_id INT NOT NULL, INDEX IDX_77F030658DB60186 (task_id), INDEX IDX_77F03065D9A4AF18 (time_report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE time_report (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', starting_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ending_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, manager_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit ADD CONSTRAINT FK_9218FF79A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE audit ADD CONSTRAINT FK_9218FF798DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1AA23F6C8 FOREIGN KEY (next_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE gantt_task ADD CONSTRAINT FK_1F25B7DDA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25E1CFE6F5 FOREIGN KEY (reporter_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2559EC7D60 FOREIGN KEY (assignee_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25FFFE75C0 FOREIGN KEY (parent_task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB2512469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25166D1F9C FOREIGN KEY (project_id) REFERENCES gantt_task (id)');
        $this->addSql('ALTER TABLE task_category ADD CONSTRAINT FK_468CF38D8DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE task_category ADD CONSTRAINT FK_468CF38D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE time_date ADD CONSTRAINT FK_7233E574AA60863 FOREIGN KEY (time_detail_id) REFERENCES time_detail (id)');
        $this->addSql('ALTER TABLE time_date ADD CONSTRAINT FK_7233E57A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE time_detail ADD CONSTRAINT FK_77F030658DB60186 FOREIGN KEY (task_id) REFERENCES task (id)');
        $this->addSql('ALTER TABLE time_detail ADD CONSTRAINT FK_77F03065D9A4AF18 FOREIGN KEY (time_report_id) REFERENCES time_report (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649783E3463 FOREIGN KEY (manager_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1AA23F6C8');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2512469DE2');
        $this->addSql('ALTER TABLE task_category DROP FOREIGN KEY FK_468CF38D12469DE2');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25166D1F9C');
        $this->addSql('ALTER TABLE audit DROP FOREIGN KEY FK_9218FF798DB60186');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25FFFE75C0');
        $this->addSql('ALTER TABLE task_category DROP FOREIGN KEY FK_468CF38D8DB60186');
        $this->addSql('ALTER TABLE time_detail DROP FOREIGN KEY FK_77F030658DB60186');
        $this->addSql('ALTER TABLE time_date DROP FOREIGN KEY FK_7233E574AA60863');
        $this->addSql('ALTER TABLE time_detail DROP FOREIGN KEY FK_77F03065D9A4AF18');
        $this->addSql('ALTER TABLE audit DROP FOREIGN KEY FK_9218FF79A76ED395');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1A76ED395');
        $this->addSql('ALTER TABLE gantt_task DROP FOREIGN KEY FK_1F25B7DDA76ED395');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25E1CFE6F5');
        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB2559EC7D60');
        $this->addSql('ALTER TABLE time_date DROP FOREIGN KEY FK_7233E57A76ED395');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649783E3463');
        $this->addSql('DROP TABLE audit');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE gantt_task');
        $this->addSql('DROP TABLE task');
        $this->addSql('DROP TABLE task_category');
        $this->addSql('DROP TABLE time_date');
        $this->addSql('DROP TABLE time_detail');
        $this->addSql('DROP TABLE time_report');
        $this->addSql('DROP TABLE `user`');
    }
}
