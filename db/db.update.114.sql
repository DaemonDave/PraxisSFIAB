CREATE TABLE `users_years` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`users_id` INT NOT NULL ,
	`type` ENUM( 'student', 'judge', 'committee', 'volunteer', 'fair' ) NOT NULL ,
	`year` INT NOT NULL ,
	PRIMARY KEY ( `id` ) ,
	INDEX ( `users_id` )
) ENGINE = MYISAM ;

ALTER TABLE `users_committee` ADD `active` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no' AFTER `users_id` ;
ALTER TABLE `users_fair` ADD `active` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no' AFTER `users_id` ;
ALTER TABLE `users_volunteer` ADD `active` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no' AFTER `users_id` ;

CREATE TABLE `users_judge` (
	`users_id` INT NOT NULL ,
	`active` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
	`years_school` TINYINT NOT NULL ,
	`years_regional` TINYINT NOT NULL ,
	`years_national` TINYINT NOT NULL ,
	`willing_chair` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
	`special_award_only` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'no',
	PRIMARY KEY ( `users_id` )
) ENGINE = MYISAM ;

