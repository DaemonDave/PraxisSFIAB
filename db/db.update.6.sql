CREATE TABLE `judges_teams_awards_link` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`award_awards_id` INT UNSIGNED NOT NULL ,
	`judges_teams_id` INT UNSIGNED NOT NULL ,
	`year` INT NOT NULL ,
	PRIMARY KEY ( `id` ) 
);
ALTER TABLE judges_teams_awards_link ADD UNIQUE (award_awards_id,judges_teams_id,year);
ALTER TABLE `judges_teams` CHANGE `name` `name` VARCHAR( 255 ) NOT NULL;
CREATE TABLE `project_specialawards_link` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`award_awards_id` INT UNSIGNED NOT NULL ,
	`projects_id` INT UNSIGNED NOT NULL ,
	`year` INT NOT NULL ,
	PRIMARY KEY ( `id` )
);
ALTER TABLE `schools` CHANGE `province` `province_code` VARCHAR( 2 ) NOT NULL;
