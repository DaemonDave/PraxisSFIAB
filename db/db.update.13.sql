INSERT INTO `config` ( `var` , `val` , `description` , `year` ) VALUES ( 'participant_project_summary_wordmax', '100', 'The maximum number of words acceptable in the project summary', '-1');
INSERT INTO `config` ( `var` , `val` , `description` , `year` ) VALUES ( 'participant_project_summary_wordmax', '100', 'The maximum number of words acceptable in the project summary', '2006');
ALTER TABLE `projects` ADD `summarycountok` TINYINT( 1 ) DEFAULT '1' NOT NULL AFTER `summary` ;
ALTER TABLE `judges_timeslots` ADD `allowdivisional` ENUM( 'no', 'yes' ) DEFAULT 'no' NOT NULL AFTER `endtime` ;
ALTER TABLE `schools` ADD `board` VARCHAR( 64 ) NOT NULL AFTER `school`;
ALTER TABLE `schools` ADD `district` VARCHAR( 64 ) NOT NULL AFTER `board`;
DELETE FROM `judges_schedulerconfig` WHERE var='timeslot_length';
DELETE FROM `judges_schedulerconfig` WHERE var='timeslot_break';
ALTER TABLE `award_awards` ADD `excludefromac` TINYINT( 1 ) DEFAULT '0' NOT NULL;
ALTER TABLE `award_prizes` ADD `excludefromac` TINYINT( 1 ) DEFAULT '0' NOT NULL;
UPDATE `config` SET `description`='C=Category, D=Division, N=2 digit Number' WHERE `var`='project_num_format';
INSERT INTO `config` VALUES ('filterdivisionbycategory', 'no', 'Allows for setup of divisions on a categorical basis.  Students can then only choose divisions that apply to their category.  Only use if you want to offer a different set of divisions to each age category (no, yes).', -1);
CREATE TABLE `projectcategoriesdivisions_link` (
	`id` int(10) unsigned NOT NULL auto_increment,
	`projectdivisions_id` int(10) unsigned NOT NULL default '0',
	`projectcategories_id` int(10) unsigned NOT NULL default '0',
	`year` int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (`id`),
	KEY `categories_id` (`projectcategories_id`)
) TYPE=MyISAM;
