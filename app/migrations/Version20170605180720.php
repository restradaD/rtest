<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170605180720 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_preferences (id INT AUTO_INCREMENT NOT NULL, receive_daily_mail TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role_permission (id INT AUTO_INCREMENT NOT NULL, permission_id INT DEFAULT NULL, role VARCHAR(150) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6F7DF886FED90CCA (permission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_preferences (id INT AUTO_INCREMENT NOT NULL, notification_channel_id INT NOT NULL, user_preferences_id INT NOT NULL, notification_type_id INT NOT NULL, INDEX IDX_3CAA95B489870488 (notification_channel_id), INDEX IDX_3CAA95B41748B0EE (user_preferences_id), INDEX IDX_3CAA95B4D0520624 (notification_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_type (id INT NOT NULL, name VARCHAR(255) NOT NULL, text_template LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_34E21C135E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification_channel (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_B7E704F05E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, expired TINYINT(1) DEFAULT NULL, expires_at DATETIME DEFAULT NULL, about LONGTEXT DEFAULT NULL, notes LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, UNIQUE INDEX UNIQ_4FBF094F5E237E06 (name), UNIQUE INDEX UNIQ_4FBF094F989D9B62 (slug), UNIQUE INDEX UNIQ_4FBF094FE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE settings (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, url VARCHAR(255) DEFAULT NULL, faye_url VARCHAR(200) NOT NULL, api_url VARCHAR(255) DEFAULT NULL, INDEX IDX_E545A0C5979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, user_preference_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', first_name VARCHAR(150) DEFAULT NULL, last_name VARCHAR(150) DEFAULT NULL, api_key VARCHAR(250) DEFAULT NULL, profile_picture VARCHAR(255) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_from_ip VARCHAR(45) DEFAULT NULL, updated_from_ip VARCHAR(45) DEFAULT NULL, locale VARCHAR(100) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical), UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_8D93D649C05FB297 (confirmation_token), INDEX IDX_8D93D649979B1AD6 (company_id), UNIQUE INDEX UNIQ_8D93D649369E8F90 (user_preference_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, notification_type_id INT NOT NULL, from_id INT NOT NULL, to_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, path VARCHAR(255) DEFAULT NULL, parameters LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', url VARCHAR(255) DEFAULT NULL, seen TINYINT(1) DEFAULT NULL, checked TINYINT(1) DEFAULT NULL, response LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_BF5476CAD0520624 (notification_type_id), INDEX IDX_BF5476CA78CED90B (from_id), INDEX IDX_BF5476CA30354A65 (to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notifications_has_channels (notification_id INT NOT NULL, notification_channel_id INT NOT NULL, INDEX IDX_90A5917EF1A9D84 (notification_id), INDEX IDX_90A591789870488 (notification_channel_id), PRIMARY KEY(notification_id, notification_channel_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lexik_trans_unit (id INT AUTO_INCREMENT NOT NULL, key_name VARCHAR(255) NOT NULL, domain VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX key_domain_idx (key_name, domain), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lexik_translation_file (id INT AUTO_INCREMENT NOT NULL, domain VARCHAR(255) NOT NULL, locale VARCHAR(10) NOT NULL, extention VARCHAR(10) NOT NULL, path VARCHAR(255) NOT NULL, hash VARCHAR(255) NOT NULL, UNIQUE INDEX hash_idx (hash), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lexik_trans_unit_translations (id INT AUTO_INCREMENT NOT NULL, file_id INT DEFAULT NULL, trans_unit_id INT DEFAULT NULL, locale VARCHAR(10) NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B0AA394493CB796C (file_id), INDEX IDX_B0AA3944C3C583C9 (trans_unit_id), UNIQUE INDEX trans_unit_locale_idx (trans_unit_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT FK_6F7DF886FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id)');
        $this->addSql('ALTER TABLE notification_preferences ADD CONSTRAINT FK_3CAA95B489870488 FOREIGN KEY (notification_channel_id) REFERENCES notification_channel (id)');
        $this->addSql('ALTER TABLE notification_preferences ADD CONSTRAINT FK_3CAA95B41748B0EE FOREIGN KEY (user_preferences_id) REFERENCES user_preferences (id)');
        $this->addSql('ALTER TABLE notification_preferences ADD CONSTRAINT FK_3CAA95B4D0520624 FOREIGN KEY (notification_type_id) REFERENCES notification_type (id)');
        $this->addSql('ALTER TABLE settings ADD CONSTRAINT FK_E545A0C5979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649369E8F90 FOREIGN KEY (user_preference_id) REFERENCES user_preferences (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAD0520624 FOREIGN KEY (notification_type_id) REFERENCES notification_type (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA78CED90B FOREIGN KEY (from_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA30354A65 FOREIGN KEY (to_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notifications_has_channels ADD CONSTRAINT FK_90A5917EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('ALTER TABLE notifications_has_channels ADD CONSTRAINT FK_90A591789870488 FOREIGN KEY (notification_channel_id) REFERENCES notification_channel (id)');
        $this->addSql('ALTER TABLE lexik_trans_unit_translations ADD CONSTRAINT FK_B0AA394493CB796C FOREIGN KEY (file_id) REFERENCES lexik_translation_file (id)');
        $this->addSql('ALTER TABLE lexik_trans_unit_translations ADD CONSTRAINT FK_B0AA3944C3C583C9 FOREIGN KEY (trans_unit_id) REFERENCES lexik_trans_unit (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE notification_preferences DROP FOREIGN KEY FK_3CAA95B41748B0EE');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649369E8F90');
        $this->addSql('ALTER TABLE role_permission DROP FOREIGN KEY FK_6F7DF886FED90CCA');
        $this->addSql('ALTER TABLE notification_preferences DROP FOREIGN KEY FK_3CAA95B4D0520624');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAD0520624');
        $this->addSql('ALTER TABLE notification_preferences DROP FOREIGN KEY FK_3CAA95B489870488');
        $this->addSql('ALTER TABLE notifications_has_channels DROP FOREIGN KEY FK_90A591789870488');
        $this->addSql('ALTER TABLE settings DROP FOREIGN KEY FK_E545A0C5979B1AD6');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649979B1AD6');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA78CED90B');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA30354A65');
        $this->addSql('ALTER TABLE notifications_has_channels DROP FOREIGN KEY FK_90A5917EF1A9D84');
        $this->addSql('ALTER TABLE lexik_trans_unit_translations DROP FOREIGN KEY FK_B0AA3944C3C583C9');
        $this->addSql('ALTER TABLE lexik_trans_unit_translations DROP FOREIGN KEY FK_B0AA394493CB796C');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE user_preferences');
        $this->addSql('DROP TABLE role_permission');
        $this->addSql('DROP TABLE permission');
        $this->addSql('DROP TABLE notification_preferences');
        $this->addSql('DROP TABLE notification_type');
        $this->addSql('DROP TABLE notification_channel');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE settings');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE notifications_has_channels');
        $this->addSql('DROP TABLE lexik_trans_unit');
        $this->addSql('DROP TABLE lexik_translation_file');
        $this->addSql('DROP TABLE lexik_trans_unit_translations');
    }
}
