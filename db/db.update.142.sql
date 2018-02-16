CREATE TABLE `fundraising_campaigns` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 128 ) NOT NULL ,
`type` VARCHAR( 64 ) NOT NULL ,
`startdate` DATE NOT NULL,
`enddate` DATE NOT NULL,
`active` ENUM( 'no', 'yes' ) NOT NULL ,
`target` INT NOT NULL,
`fundraising_goals_id` INT UNSIGNED NOT NULL,
`fiscalyear` INT NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `fundraising_campaigns_segments` (
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`fundraising_campaigns_id` INT UNSIGNED NOT NULL ,
`segment` VARCHAR( 128 ) NOT NULL
) ENGINE = MYISAM ;

RENAME TABLE `sponsorships_levels`  TO `fundraising_donor_levels`;
ALTER TABLE `fundraising_donor_levels` CHANGE `year` `fiscalyear` INT( 11 ) NOT NULL DEFAULT '0';
RENAME TABLE `fundraising`  TO `fundraising_goals`;
ALTER TABLE `fundraising_goals` CHANGE `year` `fiscalyear` INT( 11 ) NOT NULL DEFAULT '0';
RENAME TABLE `sponsors_logs`  TO `fundraising_donor_logs`;
RENAME TABLE `sponsorships`  TO `fundraising_donations`;
ALTER TABLE `fundraising_donations` CHANGE `year` `fiscalyear` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `fundraising_goals` CHANGE `goal` `budget` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `fundraising_goals` CHANGE `type` `goal` VARCHAR( 32 ) NOT NULL; 
ALTER TABLE `fundraising_donations` CHANGE `fundraising_type` `fundraising_goal` VARCHAR( 32 ) NOT NULL; 
ALTER TABLE `fundraising_goals` ADD `deadline` DATE NOT NULL; 
ALTER TABLE `fundraising_donations` ADD `fundraising_campaigns_id` INT NOT NULL AFTER `fundraising_goal`; 
