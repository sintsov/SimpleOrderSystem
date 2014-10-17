/*
 *  Project DB structure
 *  Author: Sintsov Roman <roman_spb@mail.ru>
 */

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` char(60) NOT NULL,
  `avatar` varchar(50) DEFAULT NULL,
  `role_id` tinyint unsigned NOT NULL,
  `account_id` int(10) unsigned DEFAULT NULL,
  `time_visited` int(18) unsigned DEFAULT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  `modified_at` int(18) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `users_role`;
CREATE TABLE `users_role` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Denormolization (performence)
DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `balance` decimal(7, 2) NOT NULL,
  `reserve` decimal(7, 2) NOT NULL, -- need reserve fund for custom
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES users(`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Secutiry: we could create a user account in system, but i suppose it's insecure
DROP TABLE IF EXISTS `system_account`;
CREATE TABLE `system_account` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(7, 2) NOT NULL,
  `comission` decimal(7, 2) NOT NULL,
  `transaction_id` int(10) unsigned,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint unsigned NOT NULL,
  `customer` int(10) unsigned NOT NULL,
  `employess` int(10) unsigned DEFAULT NULL,
  `cost` decimal(7, 2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `text` varchar(4096) NOT NULL,
  `created_at` int(18) unsigned DEFAULT NULL,
  `modified_at` int(18) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`status`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `order_status`;
CREATE TABLE `order_status` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255),
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE `transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `credit`int(10) unsigned NOT NULL,
  `debit` int(10) unsigned NOT NULL,
  `amount` decimal(7, 2) NOT NULL,
  `date` int(18) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- fot external transaction (example: payment systems)
DROP TABLE IF EXISTS `external_transactions`;
CREATE TABLE `external_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `credit` int(10) unsigned NOT NULL,
  `payment_system` int(10) unsigned NOT NULL,
  `amount` decimal(7, 2) NOT NULL,
  `date` int(18) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;