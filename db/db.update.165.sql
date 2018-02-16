ALTER TABLE `projects` ADD `floornumber` INT NOT NULL AFTER `projectsort_seq` ;

CREATE TABLE `exhibithall` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`name` VARCHAR( 32 ) NOT NULL ,
	`type` ENUM( 'wall', 'exhibithall', 'project' ) NOT NULL ,
	`x` FLOAT NOT NULL ,
	`y` FLOAT NOT NULL ,
	`w` FLOAT NOT NULL ,
	`h` FLOAT NOT NULL ,
	`orientation` INT NOT NULL ,
	`exhibithall_id` INT NOT NULL ,
	`floornumber` INT NOT NULL ,
	`divs` TINYTEXT NOT NULL ,
	`cats` TINYTEXT NOT NULL ,
	`has_electricity` ENUM( 'no', 'yes' ) NOT NULL
) ENGINE = MYISAM ;
