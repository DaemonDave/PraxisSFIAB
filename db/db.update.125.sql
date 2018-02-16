DROP TABLE `fairs`;
ALTER TABLE `users_fair` CHANGE `fairs_id` `fair_name` TINYTEXT NOT NULL;
ALTER TABLE `users_fair` ADD `fair_abbrv` VARCHAR( 16 ) NOT NULL AFTER `fair_name` ;



