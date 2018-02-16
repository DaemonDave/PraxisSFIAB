DROP TABLE award_contacts;

CREATE TABLE `fundraising` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`type` VARCHAR( 32 ) NOT NULL ,
	`name` VARCHAR( 128 ) NOT NULL ,
	`description` TEXT NULL DEFAULT NULL ,
	`system` ENUM( 'no', 'yes' ) DEFAULT 'no' NOT NULL ,
	`goal` INT UNSIGNED NOT NULL ,
	`year` INT NOT NULL ,
	PRIMARY KEY ( `id` )
) TYPE = MYISAM ;

ALTER TABLE `fundraising` ADD UNIQUE (type,year);
INSERT INTO `fundraising` ( `id` , `type` , `name` , `description`, `system` , `goal` , `year` ) VALUES ( '', 'general', 'General Funds', 'General funds donated to the fair may be allocated as the fair organizers see fit','yes', '0', '-1');
INSERT INTO `fundraising` ( `id` , `type` , `name` , `description`, `system` , `goal` , `year` ) VALUES ( '', 'awards', 'Award Sponsorships', 'Award Sponsorships are provided to allow an organization to sponsor a specific award that is given out at the fair', 'yes', '0', '-1');

ALTER TABLE `award_sponsors` RENAME `sponsors` ;
ALTER TABLE `sponsors` DROP `confirmed`;
ALTER TABLE `sponsors` ADD `tollfree` VARCHAR( 32 ) NOT NULL AFTER `phone` ;
ALTER TABLE `sponsors` ADD `website` VARCHAR( 128 ) NOT NULL AFTER `email` ;
ALTER TABLE `sponsors` ADD `donationpolicyurl` VARCHAR( 255 ) NOT NULL AFTER `notes` ;
ALTER TABLE `sponsors` ADD `fundingselectiondate` DATE NULL DEFAULT NULL AFTER `donationpolicyurl` ;
ALTER TABLE `sponsors` ADD `logo` VARCHAR (128 ) NULL DEFAULT NULL AFTER `fundingselectiondate` ;
ALTER TABLE `sponsors` ADD `waiveraccepted` ENUM ( 'no' , 'yes' ) DEFAULT 'no' NOT NULL AFTER `logo`;
ALTER TABLE `sponsors` ADD `taxreceiptrequired` ENUM ( 'no' , 'yes' ) DEFAULT 'no' NOT NULL AFTER `waiveraccepted`;

ALTER TABLE `award_awards` CHANGE `award_sponsors_id` `sponsors_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

CREATE TABLE `sponsors_logs` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`sponsors_id` INT NOT NULL ,
	`dt` DATETIME NOT NULL ,
	`users_id` INT NOT NULL ,
	`log` TEXT NOT NULL ,
	PRIMARY KEY ( `id` )
) TYPE = MYISAM ;


CREATE TABLE `sponsorships` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`sponsors_id` INT NOT NULL ,
	`fundraising_type` VARCHAR( 32 ) NOT NULL ,
	`value` INT NOT NULL ,
	`status` ENUM( 'pending', 'confirmed', 'received' ) NOT NULL ,
	`probability` INT NOT NULL ,
	`year` INT NOT NULL ,
	PRIMARY KEY ( `id` )
) TYPE = MYISAM ;

CREATE TABLE `sponsorships_levels` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`level` VARCHAR( 64 ) NOT NULL ,
	`min` INT NOT NULL ,
	`max` INT NOT NULL ,
	`description` TEXT NOT NULL ,
	`year` INT NOT NULL ,
	PRIMARY KEY ( `id` )
) TYPE = MYISAM ;

INSERT INTO sponsorships_levels (`level`,`min`,`max`,`year`) VALUES ('Bronze','100','499',-1);
INSERT INTO sponsorships_levels (`level`,`min`,`max`,`year`) VALUES ('Silver','500','999',-1);
INSERT INTO sponsorships_levels (`level`,`min`,`max`,`year`) VALUES ('Gold','1000','10000',-1);


