<?php
try {
 $db -> Execute("alter table users add last_login int(10) unsigned default NULL");
 $db->Execute("update users u set last_login=(select max(timestamp) from logs where users_LOGIN=u.login and action='login')");
} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
try {
 $db->Execute("alter table lessons add access_limit int(10) default 0");
} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
try {
 $db->Execute("alter table users_to_lessons add access_counter int(10) default 0");
} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
try {
 $db->Execute("alter table user_profile add field_order int(10) default null");
} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
try {
 $db->Execute("alter table completed_tests engine=innodb");
 $db->Execute("
CREATE TABLE `completed_tests_blob` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `completed_tests_ID` mediumint(8) unsigned NOT NULL,
  `test` longblob,
  PRIMARY KEY (`id`),
  KEY `ibfk_completed_tests_blob_1` (`completed_tests_ID`),
  CONSTRAINT `ibfk_completed_tests_blob_1` FOREIGN KEY (`completed_tests_ID`) REFERENCES `completed_tests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8");	
 $db->Execute("insert into completed_tests_blob (completed_tests_ID, test) select id, test from completed_tests");
 $db->Execute("alter table completed_tests drop test");
} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
try {

} catch (Exception $e) {
 $failed_queries[] = $e->getMessage();
}
?>
