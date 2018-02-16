CREATE TABLE `reports_committee` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`users_id` INT NOT NULL ,
	`reports_id` INT NOT NULL ,
	`category` VARCHAR( 128 ) NOT NULL ,
	`comment` TEXT NOT NULL ,
	`format` VARCHAR( 64 ) NOT NULL ,
	`stock` VARCHAR( 64 ) NOT NULL
) ENGINE = MYISAM

