CREATE TABLE `emailqueue` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`val` VARCHAR( 64 ) NOT NULL ,
	`name` VARCHAR( 128 ) NOT NULL ,
	`users_uid` INT NOT NULL ,
	`from` VARCHAR( 128 ) NOT NULL ,
	`subject` VARCHAR( 128 ) NOT NULL ,
	`body` TEXT NOT NULL ,
	`bodyhtml` TEXT NOT NULL ,
	`type` ENUM( 'system', 'user', 'fundraising' ) NOT NULL ,
	`fundraising_campaigns_id` INT NULL ,
	`started` DATETIME NOT NULL ,
	`finished` DATETIME NULL ,
	`numtotal` INT NOT NULL ,
	`numsent` INT NOT NULL
) ENGINE = MYISAM ;

CREATE TABLE `emailqueue_recipients` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`emailqueue_id` INT UNSIGNED NOT NULL ,
	`toemail` VARCHAR( 128 ) NOT NULL ,
	`toname` VARCHAR( 128 ) NOT NULL ,
	`replacements` TEXT NOT NULL ,
	`sent` DATETIME NULL
) ENGINE = MYISAM ;

INSERT INTO `config` (`var`, `val`, `category`, `type`, `type_values`, `ord`, `description`, `year`) VALUES ('emailqueue_lock', '', 'Special', '', '', '', 'The current lock datetime of the email sending queue, or empty if not locked', '0');
