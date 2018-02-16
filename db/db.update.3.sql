ALTER TABLE `dates` CHANGE `date` `date` DATETIME DEFAULT '0000-00-00' NOT NULL;
INSERT INTO `dates` ( `id` , `date` , `name` , `description` , `year` ) VALUES ( '', '2005-01-01 00:00:00', 'judgeregopen', 'Judges registration opens', '2005');
INSERT INTO `dates` ( `id` , `date` , `name` , `description` , `year` ) VALUES ( '', '2005-03-31 00:00:00', 'judgeregclose', 'Judges registration closes', '2005');
ALTER TABLE `judges_teams` ADD `name` VARCHAR( 64 ) NOT NULL AFTER `num` ;
ALTER TABLE `judges_teams` ADD UNIQUE ( `name` , `year` );
