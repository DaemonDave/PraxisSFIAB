ALTER TABLE `award_prizes` 
	ADD `trophystudentkeeper` BOOL DEFAULT '0' NOT NULL ,
	ADD `trophystudentreturn` BOOL DEFAULT '0' NOT NULL ,
	ADD `trophyschoolkeeper` BOOL DEFAULT '0' NOT NULL ,
	ADD `trophyschoolreturn` BOOL DEFAULT '0' NOT NULL ;
ALTER TABLE `award_awards` ADD `description` TEXT NOT NULL AFTER `criteria` ;	
