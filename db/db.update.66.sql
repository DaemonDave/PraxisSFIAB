CREATE TABLE `award_sources` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`name` VARCHAR( 128 ) NOT NULL ,
	`url` VARCHAR( 255 ) NOT NULL ,
	`username` VARCHAR( 32 ) NOT NULL ,
	`password` VARCHAR( 32 ) NOT NULL ,
	PRIMARY KEY ( `id` ) 
) TYPE = MYISAM ;
