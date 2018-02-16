CREATE TABLE `judges_catpref` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`judges_id` INT NOT NULL ,
	`projectcategories_id` INT NOT NULL ,
	`rank` INT NOT NULL ,
	`year` INT NOT NULL ,
	PRIMARY KEY ( `id` ) 
) TYPE = MYISAM ;
CREATE TABLE `judges_schedulerconfig` (
	`var` VARCHAR( 64 ) NOT NULL DEFAULT '',
	`val` TEXT NOT NULL ,
	`description` TEXT NOT NULL ,
	`year` INT( 11 ) NOT NULL DEFAULT '0',
	UNIQUE KEY `var` ( `var` , `year` ) 
) TYPE = MYISAM ;
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'timeslot_length', '12', 'The length of time (in minutes) that a judging timeslot should be', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'timeslot_break', '3', 'The length of time (in minutes) between timeslots to allow judges to move between projects', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'num_times_judged', '3', 'The number of times that each project must be judged (by different judging teams)', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'num_timeslots', '20', 'The number of timeslots available during the judging period', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'max_projects_per_team', '5', 'The maximum number of projects that a team can judge', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'min_judges_per_team', '2', 'The minimum number of judges that should be on a judging team', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'max_judges_per_team', '4', 'The maximum number of judges that should be on a judging team', '-1');
ALTER TABLE `judges_teams` ADD `autocreate_type_id` INT DEFAULT NULL ;
INSERT INTO `emails` ( `id` , `val` , `name` , `description` , `from` , `subject` , `body` , `type` ) VALUES ('', 'register_judges_resend_password', 'Judges Registration - Resend Password', 'Resend the password to the judge if they submit a ''forgot password'' request', 'website@sfiab.ca', 'Judge Registration for [FAIRNAME]', 'We have received a request for the retrieval of your password from this email address. Please find your existing password below Judge Email Address: [EMAIL] Judge Registration Password: [PASSWORD] ', 'system');
