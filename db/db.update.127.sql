INSERT INTO `config` (`var`, `val`, `category`, `type`, `type_values`, `ord`, `description`, `year`) VALUES
('fair_stats_participation', 'yes', 'Science Fairs', 'yesno', '', 100, 'Gather Stats: Student and School Participation (students, gender, and projects) by age group.', -1),
('fair_stats_schools_ext', 'yes', 'Science Fairs', 'yesno', '', 200, 'Gather Stats: Extended school participation data.<ul>\r\n<li>Number of at-risk schools and students<li>Number of public schools and students<li>Number of private/independent schools and students</ul>', -1),
('fair_stats_minorities', 'firstnations', 'Science Fairs', 'multisel', 'firstnations=Number of First Nation students|disabled=Number of Disabled students', 300, 'Gather Stats: Participant minority demographics (must be filled in manually by the fair)', -1),
('fair_stats_guests', 'yes', 'Science Fairs', 'yesno', '', 400, 'Gather Stats: Number of student and public guests (must be filled in manually by the fair)', -1),
('fair_stats_sffbc_misc', 'yes', 'Science Fairs', 'yesno', '', '500', 'Gather Stats: Misc. SFFBC Data<ul> <li>Supporting teachers <li>Students with increased interest in sci and tech <li>Students considering a career in science</ul>', '-1');

ALTER TABLE `fairs_stats` CHANGE `publicschools` `schools_public` INT( 11 ) NOT NULL DEFAULT '0',
	CHANGE `privateschools` `schools_private` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `fairs_stats` ADD `students_public` INT NOT NULL AFTER `projects_11` ;
ALTER TABLE `fairs_stats` ADD `students_private` INT NOT NULL AFTER `schools_public` ;
ALTER TABLE `fairs_stats` CHANGE `users_uid` `fairs_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `fairs_stats` ADD `students_total` INT NOT NULL AFTER `projects_11` ,
	ADD `schools_total` INT NOT NULL AFTER `students_total` ;
ALTER TABLE `fairs_stats` CHANGE `innercity` `students_atrisk` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `fairs_stats` ADD `schools_atrisk` INT NOT NULL AFTER `students_atrisk` ;
ALTER TABLE `fairs_stats` ADD `schools_active` INT NOT NULL AFTER `schools_total` ;
ALTER TABLE `fairs_stats` ADD `committee_members` INT NOT NULL AFTER `consideringcareer` ,
	ADD `judges` INT NOT NULL AFTER `committee_members` ;
ALTER TABLE `fairs_stats` CHANGE `schooldistricts` `schools_districts` INT( 11 ) NOT NULL DEFAULT '0';

CREATE TABLE `fairs` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`name` TINYTEXT NOT NULL ,
	`abbrv` VARCHAR( 16 ) NOT NULL ,
	`type` ENUM( 'feeder', 'sfiab', 'ysf' ) NOT NULL ,
	`url` TINYTEXT NOT NULL ,
	`username` varchar( 32 ) NOT NULL ,
	`password` varchar( 32 ) NOT NULL 
) ENGINE = MYISAM ;

ALTER TABLE `users_fair` CHANGE `fair_name` `fairs_id` INT NOT NULL;
ALTER TABLE `users_fair` DROP `fair_abbrv` ;



