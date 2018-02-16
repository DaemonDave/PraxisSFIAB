ALTER TABLE `committees_link` DROP `committees_members_id` ;

DROP TABLE `committees_members` ;

ALTER TABLE `users` ADD `oldpassword` VARCHAR( 32 ) NOT NULL AFTER `passwordexpiry` ;







