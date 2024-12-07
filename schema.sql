DROP DATABASE IF EXISTS dolphin_crm;
CREATE DATABASE dolphin_crm;
USE dolphin_crm;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`(
    `id` INTEGER(12) NOT NULL auto_increment,
    `firstname` VARCHAR(32) DEFAULT NULL,
    `lastname` VARCHAR(32) DEFAULT NULL,
    `password` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(32) DEFAULT NULL,
    `role` enum('Member','Admin') NOT NULL DEFAULT 'Member',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);


DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts`(
    `id` INTEGER(12) NOT NULL auto_increment,
    `title` VARCHAR(32) DEFAULT NULL,
    `firstname` VARCHAR(32) DEFAULT NULL,
    `lastname` VARCHAR(32) DEFAULT NULL,
    `email` VARCHAR(32) DEFAULT NULL,
    `telephone` VARCHAR(16) DEFAULT NULL,
    `company` VARCHAR(64) DEFAULT NULL,
    `type` enum('Sales Lead','Support') NOT NULL DEFAULT 'Sales Lead',
    `assigned_to` INTEGER(12) NOT NULL,
    `created_by` INTEGER(12) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);

DROP TABLE IF EXISTS `notes`;
CREATE TABLE `notes`(
    `id` INTEGER(12) NOT NULL auto_increment,
    `contact_id` INTEGER(12) NOT NULL,
    `comment` TEXT(65535) DEFAULT NULL,
    `created_by` INTEGER(12) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
);