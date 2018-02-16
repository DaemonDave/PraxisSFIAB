ALTER TABLE `judges` ADD `typepref` VARCHAR( 8 ) NOT NULL ;

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` ) VALUES ( 'judges_specialaward_enable', 'no', 'Judge Registration', 'yesno', '', '1000', 'Allow judges to specify their special award judging preferences (in addition to the divisional judging preferences)', '-1');
INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` ) VALUES ( 'judges_specialaward_only_enable', 'no', 'Judge Registration', 'yesno', '', '1100', 'Allow judges to specify that they are a judge for a specific special award.  If a judge selects this, it disables their divisional preference selection entirely', '-1');
INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` ) VALUES ( 'judges_specialaward_min', '1', 'Judge Registration', 'number', '', '1200', 'Minimum number of special awards a judge must select when specifying special award preferences', '-1');
INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` ) VALUES ( 'judges_specialaward_max', '6', 'Judge Registration', 'number', '', '1300', 'Maximum number of special awards a judge must select when specifying special award preferences', '-1');

CREATE TABLE `judges_specialaward_sel` (
`id` INT NOT NULL AUTO_INCREMENT,
`judges_id` INT NOT NULL ,
`award_awards_id` INT NOT NULL ,
`year` INT NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE = MyISAM ;


