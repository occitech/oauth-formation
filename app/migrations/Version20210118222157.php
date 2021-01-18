<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210118222157 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE oauth_access_token (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, expiry_date_time DATETIME NOT NULL, revoked TINYINT(1) NOT NULL, scopes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_auth_code (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, expiry_date_time DATETIME NOT NULL, revoked TINYINT(1) NOT NULL, redirect_uri LONGTEXT NOT NULL, scopes LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_refresh_token (id INT AUTO_INCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, expiry_date_time DATETIME NOT NULL, revoked TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE oauth_access_token');
        $this->addSql('DROP TABLE oauth_auth_code');
        $this->addSql('DROP TABLE oauth_refresh_token');
    }
}
