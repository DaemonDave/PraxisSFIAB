ALTER TABLE `judges_teams_link` ADD `captain` ENUM( 'no', 'yes' ) NOT NULL AFTER `judges_teams_id` ;
CREATE TABLE `judges_timeslots` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`date` DATE NOT NULL ,
	`starttime` TIME NOT NULL ,
	`endtime` TIME NOT NULL ,
	`year` INT NOT NULL ,
	PRIMARY KEY ( `id` ) 
);
CREATE TABLE `judges_teams_timeslots_link` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`judges_teams_id` INT UNSIGNED NOT NULL ,
	`judges_timeslots_id` INT UNSIGNED NOT NULL ,
	`year` INT UNSIGNED NOT NULL ,
	PRIMARY KEY ( `id` ) 
);
ALTER TABLE judges_teams_timeslots_link ADD UNIQUE (judges_teams_id,judges_timeslots_id,year);
CREATE TABLE `judges_teams_timeslots_projects_link` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`judges_teams_id` INT UNSIGNED NOT NULL ,
	`judges_timeslots_id` INT UNSIGNED NOT NULL ,
	`projects_id` INT UNSIGNED NOT NULL ,
	`year` INT UNSIGNED NOT NULL ,
	PRIMARY KEY ( `id` ) 
);
ALTER TABLE `judges_teams_timeslots_projects_link` ADD UNIQUE (judges_teams_id,judges_timeslots_id,projects_id,year);
ALTER TABLE `projects` CHANGE `projectnumber` `projectnumber` VARCHAR( 16 ) DEFAULT NULL;
