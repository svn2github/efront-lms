<?php

$_TEXTME_INSTALL_QUERIES = array();

$_TEXTME_INSTALL_QUERIES[] ='
    CREATE TABLE IF NOT EXISTS `module_textme_groups` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `lessons_ID` int(11) NOT NULL,
      `name` varchar(32) NOT NULL,
      PRIMARY KEY (`id`),
      INDEX `lessons_ID` (`lessons_ID`)
    ) DEFAULT CHARSET=utf8;
';

$_TEXTME_INSTALL_QUERIES[] ='
    CREATE TABLE IF NOT EXISTS `module_textme_group_has_subscribers` (
      `groups_ID` int(11) NOT NULL,
      `users_LOGIN` varchar(255) NOT NULL,
      PRIMARY KEY (`groups_ID`,`users_LOGIN`),
      INDEX `groups_ID` (`groups_ID`),
      INDEX `users_LOGIN` (`users_LOGIN`)
    ) DEFAULT CHARSET=utf8;
';

$_TEXTME_INSTALL_QUERIES[] ='
    CREATE TABLE IF NOT EXISTS `module_textme_lessons_settings` (
      `lessons_ID` int(11) NOT NULL,
      `alias` varchar(16) DEFAULT NULL,
      `credits` int(11) DEFAULT NULL,
      `credits_spent` int(11) NOT NULL,
      PRIMARY KEY (`lessons_ID`)
    ) DEFAULT CHARSET=utf8;
';

$_TEXTME_INSTALL_QUERIES[] ='
    CREATE TABLE IF NOT EXISTS `module_textme_messages` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `lessons_ID` int(11) NOT NULL,
      `users_LOGIN` varchar(255) NOT NULL,
      `text` text NOT NULL,
      `credits` decimal(10,2) NOT NULL,
      `send_at` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      INDEX `lessons_ID` (`lessons_ID`),
      INDEX `users_LOGIN` (`users_LOGIN`)
    ) DEFAULT CHARSET=utf8;
';

$_TEXTME_INSTALL_QUERIES[] ='
CREATE TABLE IF NOT EXISTS `module_textme_phonebook` (
  `users_LOGIN` varchar(255) NOT NULL,
  `mobile` varchar(32) NOT NULL,
  `vcode` char(5) NOT NULL,
  `is_verified` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`users_LOGIN`)
) DEFAULT CHARSET=utf8;
';

$_TEXTME_INSTALL_QUERIES[] ='
    CREATE TABLE IF NOT EXISTS `module_textme_gateways` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(32) NOT NULL,
      `type` varchar(32) NOT NULL,
      `data` text NOT NULL,
      `is_active` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`)
    ) DEFAULT CHARSET=utf8 ;
';
$_TEXTME_INSTALL_QUERIES[] ='
    CREATE TABLE IF NOT EXISTS `module_textme_recipients` (
      `messages_ID` int(11) NOT NULL,
      `users_LOGIN` varchar(255) NOT NULL,
      `identifier` varchar(64) NOT NULL,
      `status` enum(\'local\',\'delivered\',\'failed\',\'pending\') NOT NULL DEFAULT \'local\',
      `is_read` tinyint(1) NOT NULL DEFAULT \'0\',
      `is_deleted` tinyint(1) NOT NULL DEFAULT \'0\',
      PRIMARY KEY (`messages_ID`,`users_LOGIN`),
      INDEX `messages_ID` (`messages_ID`),
      INDEX `users_LOGIN` (`users_LOGIN`)
    ) DEFAULT CHARSET=utf8;
';

$_TEXTME_INSTALL_QUERIES[] ='
    CREATE TABLE IF NOT EXISTS `module_textme_subscribers` (
      `lessons_ID` int(11) NOT NULL,
      `users_LOGIN` varchar(255) NOT NULL,
      PRIMARY KEY (`lessons_ID`,`users_LOGIN`),
      INDEX `lessons_ID` (`lessons_ID`),
      INDEX `users_LOGIN` (`users_LOGIN`)
    ) DEFAULT CHARSET=utf8;
';


?>
