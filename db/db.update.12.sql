UPDATE `config` SET `description` = 'Self nominations for special awards are due either with registration or on a specific date.  If "date" is used, it must be configured under "Important Dates" section.  If you do not wish to allow students to self-nominate for special awards, set to "none" (none|date|registration)' WHERE `var` = 'specialawardnomination';
ALTER TABLE `schools` ADD `projectlimit` INT NOT NULL , ADD `projectlimitper` ENUM( 'total', 'agecategory' ) NOT NULL ;
INSERT INTO `config` ( `var` , `val` , `description` , `year` ) VALUES ( 'participant_student_tshirt', 'yes', 'Ask for students their T-Shirt size (yes/no).', '-1');
ALTER TABLE `translations` ADD `argsdesc` TEXT DEFAULT NULL ;
