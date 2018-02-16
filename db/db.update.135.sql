ALTER TABLE `sponsorships` ADD `users_uid` INT NULL DEFAULT NULL AFTER `sponsors_id` ;
ALTER TABLE `sponsorships` CHANGE `sponsors_id` `sponsors_id` INT( 11 ) NULL DEFAULT NULL 
