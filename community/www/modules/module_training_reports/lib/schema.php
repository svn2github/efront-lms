<?php

/* Install queries */
$_TIME_REPORTS_INSTALL_QUERIES = array();

$_TIME_REPORTS_INSTALL_QUERIES[] = '
    CREATE TABLE IF NOT EXISTS `module_time_reports` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(128) NOT NULL,
        `from_date` int(10) NOT NULL,
        `to_date` int(10) NOT NULL,
        `separated_by` varchar(16) NOT NULL,
        PRIMARY KEY (`id`)
    ) DEFAULT CHARSET=utf8;
';

$_TIME_REPORTS_INSTALL_QUERIES[] = '
    CREATE TABLE IF NOT EXISTS `module_time_reports_fields` (
        `reports_ID` int(11) NOT NULL,
        `name` varchar(64) NOT NULL,
        `position` tinyint(3) unsigned NOT NULL,
        PRIMARY KEY(`reports_ID`, `name`, `position`)
    ) DEFAULT CHARSET=utf8;
';

$_TIME_REPORTS_INSTALL_QUERIES[] = '
    CREATE TABLE IF NOT EXISTS `module_time_reports_courses` (
        `reports_ID` int(11) NOT NULL,
        `courses_ID` int(11) NOT NULL,
        `position` tinyint(3) NOT NULL,
        PRIMARY KEY(`reports_ID`, `courses_ID`, `position`)
    ) DEFAULT CHARSET=utf8;
';
/* Uninstall queries */
$_TIME_REPORTS_UNINSTALL_QUERIES = array();
$_TIME_REPORTS_UNINSTALL_QUERIES[] = 'DROP TABLE IF EXISTS `module_time_reports`';
$_TIME_REPORTS_UNINSTALL_QUERIES[] = 'DROP TABLE IF EXISTS `module_time_reports_fields`';
$_TIME_REPORTS_UNINSTALL_QUERIES[] = 'DROP TABLE IF EXISTS `module_time_reports_courses`';
?>
