<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191006064711 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'initialize user database';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("CREATE DATABASE IF NOT EXISTS `authorization`");
        $this->addSql("ALTER DATABASE `authorization` COLLATE 'utf8mb4_general_ci';");

        $this->addSql("CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(32) CHARACTER SET ascii NOT NULL,
  `password` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->addSql("CREATE TABLE `user_meta_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meta_hash` varchar(64) NOT NULL,
  `php_session_id` varchar(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `ip_address` varchar(15) CHARACTER SET ascii NOT NULL,
  `user_agent` text CHARACTER SET ascii NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_meta_hash` (`meta_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->addSql("CREATE TABLE `user_session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_meta_info_id` int(10) unsigned NOT NULL,
  `is_active` tinyint(3) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_meta_info_id` (`user_meta_info_id`),
  CONSTRAINT `user_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `user_session_ibfk_2` FOREIGN KEY (`user_meta_info_id`) REFERENCES `user_meta_info` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->addSql("CREATE TABLE `user_login_fails` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_meta_info_id` int(10) unsigned NOT NULL,
  `activity_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_meta_info_id` (`user_meta_info_id`),
  CONSTRAINT `user_login_fails_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `user_login_fails_ibfk_2` FOREIGN KEY (`user_meta_info_id`) REFERENCES `user_meta_info` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->addSql("CREATE TABLE `user_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_session_id` int(10) unsigned NOT NULL,
  `activity_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_session_id` (`user_session_id`),
  CONSTRAINT `user_activity_ibfk_1` FOREIGN KEY (`user_session_id`) REFERENCES `user_session` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
