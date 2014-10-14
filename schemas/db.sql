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

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `balance` decimal(7, 2) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES users(`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Secutiry: we could create a user account in system, but i suppose it's insecure
DROP TABLE IF EXISTS `system_account`;
CREATE TABLE `system_account` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `amount` decimal(7, 2) NOT NULL,
  `transaction_id` int(10) unsigned,
  `balance` decimal(7, 2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
