ALTER TABLE `users` CHANGE `types` `types` SET( 'student', 'judge', 'committee', 'volunteer', 'fair', 'sponsor', 'principal', 'teacher', 'parent', 'mentor', 'alumni' ) NOT NULL;

CREATE TABLE `users_principal` (
    `users_id` INT NOT NULL ,
    `principal_active` ENUM( 'no', 'yes' ) NOT NULL ,
    `principal_complete` ENUM( 'no', 'yes' ) NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `users_teacher` (
    `users_id` INT NOT NULL ,
    `teacher_active` ENUM( 'no', 'yes' ) NOT NULL ,
    `teacher_complete` ENUM( 'no', 'yes' ) NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `users_parent` (
    `users_id` INT NOT NULL ,
    `parent_active` ENUM( 'no', 'yes' ) NOT NULL ,
    `parent_complete` ENUM( 'no', 'yes' ) NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `users_mentor` (
    `users_id` INT NOT NULL ,
    `mentor_active` ENUM( 'no', 'yes' ) NOT NULL ,
    `mentor_complete` ENUM( 'no', 'yes' ) NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `users_alumni` (
    `users_id` INT NOT NULL ,
    `alumni_active` ENUM( 'no', 'yes' ) NOT NULL ,
    `alumni_complete` ENUM( 'no', 'yes' ) NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `sponsors` DROP `taxreceiptrequired`;
ALTER TABLE `sponsors` ADD `proposalsubmissiondate` DATE NOT NULL;

ALTER TABLE `schools` ADD `principal_uid` INT NULL AFTER `principal` ,
	ADD `sciencehead_uid` INT NULL AFTER `principal_uid` ;

UPDATE `schools` SET `principal_uid`=NULL WHERE 1;
UPDATE `schools` SET `sciencehead_uid`=NULL WHERE 1;

DROP TABLE `fundraising_campaigns_segments`;

CREATE TABLE `fundraising_campaigns_users_link` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`fundraising_campaigns_id` INT UNSIGNED NOT NULL ,
	`users_uid` INT UNSIGNED NOT NULL
) ENGINE = MYISAM ;

ALTER TABLE `fundraising_campaigns` ADD `filterparameters` VARCHAR(255) NULL DEFAULT NULL AFTER `fundraising_goal`; 

UPDATE `reports_items` SET field = 'school_principal' WHERE field = 'school_contact';
UPDATE `reports_items` SET field = 'school_phone' WHERE field = 'school_contactphone';
UPDATE `reports_items` SET field = 'school_email' WHERE field = 'school_contactemail';

UPDATE `reports` SET `desc` = 'List of all schools in the database. Name, address, principal and phone.' WHERE `reports`.`system_report_id` =35;

ALTER TABLE `fundraising_donations` ADD `supporttype` VARCHAR( 255 ) NOT NULL;

ALTER TABLE `emails` CHANGE `type` `type` ENUM( 'system', 'user', 'fundraising' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'system';
ALTER TABLE `emails` ADD `fundraising_campaigns_id` INT UNSIGNED NULL DEFAULT NULL ,
ADD `lastsent` DATETIME NULL DEFAULT NULL;
ALTER TABLE `emails` ADD `bodyhtml` TEXT NULL DEFAULT NULL AFTER `body`;
ALTER TABLE `emails` DROP INDEX `val`; 
