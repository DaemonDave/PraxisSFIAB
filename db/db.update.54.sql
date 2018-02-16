CREATE TABLE `documents` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`date` DATE NOT NULL ,
	`title` VARCHAR( 128 ) NOT NULL ,
	`sel_category` VARCHAR( 128 ) NOT NULL ,
	`filename` VARCHAR( 128 ) DEFAULT NULL ,
	PRIMARY KEY ( `id` ) 
) TYPE = MYISAM ;
