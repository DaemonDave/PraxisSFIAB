
ALTER TABLE `users` ADD `uid` INT NOT NULL AFTER `id` ;
ALTER TABLE `users` CHANGE `sex` `sex` ENUM( 'male', 'female' ) NULL DEFAULT NULL ;

ALTER TABLE `users_committee` CHANGE `active` `committee_active` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no';
ALTER TABLE `users_committee` ADD `committee_complete` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no' AFTER `committee_active` ;

ALTER TABLE `users_fair` CHANGE `active` `fair_active` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no';
ALTER TABLE `users_fair` ADD `fair_complete` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no' AFTER `fair_active` ;

ALTER TABLE `users_judge` CHANGE `active` `judge_active` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no';
ALTER TABLE `users_judge` ADD `judge_complete` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no' AFTER `judge_active` ;
ALTER TABLE `users_judge` ADD `cat_prefs` TINYTEXT NOT NULL AFTER `special_award_only` ;
ALTER TABLE `users_judge` ADD `div_prefs` TINYTEXT NOT NULL AFTER `cat_prefs` ;
ALTER TABLE `users_judge` ADD `divsub_prefs` TINYTEXT NOT NULL AFTER `div_prefs` ;
ALTER TABLE `users_judge` ADD `languages` TINYTEXT NOT NULL AFTER `divsub_prefs` ;
ALTER TABLE `users_judge` ADD `highest_psd` TINYTEXT NOT NULL AFTER `languages` ;
ALTER TABLE `users_judge` ADD `expertise_other` TINYTEXT NOT NULL AFTER `highest_psd` ;

ALTER TABLE `users_volunteer` CHANGE `active` `volunteer_active` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no';
ALTER TABLE `users_volunteer` ADD `volunteer_complete` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no' AFTER `volunteer_active` ;
ALTER TABLE `users_volunteer` DROP `tmp`;

DROP TABLE users_years;

-- Add new judge emails, delete old
INSERT INTO `emails` ( `id` , `val` , `name` , `description` , `from` , `subject` , `body` , `type` ) VALUES 
	('', 'judge_recover_password', 'Judges - Recover Password', 'Recover the password for a judge if they submit a ''forgot password'' request', '', 'Password Recovery for [FAIRNAME]', 'We have received a request for the recovery of your password from this email address. Please find your new password below:\n\nJudge Email Address: [EMAIL]\nJudge Password: [PASSWORD] ', 'system'),
	('', 'judge_welcome', 'Judges - Welcome', 'Welcome email sent to a judge after they have registered for the first time. This email includes their temporary password.', '', 'Judge Registration for [FAIRNAME]', 'Thank you for registering as a judge at our fair. Please find your temporary password below. After logging in for the first time you will be prompted to change your password.\n\nJudge Email Address: [EMAIL]\nJudge Password: [PASSWORD]', 'system'),
	('', 'judge_new_invite', 'Judges - New Judge Invitation', 'This is sent to a new judge when they are invited using the invite users  administration option.', '', 'Judge Registration for [FAIRNAME]', 'You have been invited to be a judge for the [FAIRNAME].  An account has been created for you to login with and complete your information.  You can login to the judge registration site with:\n\nEmail Address: [EMAIL]\nPassword: [PASSWORD]\nYou can change your password once you login.', 'system'),
	('', 'judge_add_invite', 'Judges - Add Judge Invitation', 'This is sent to existing users when they are invited using the invite users administration option.', '', 'Judge Registration for [FAIRNAME]', 'The role of judge for the [FAIRNAME] has been added to your account by a committee member.  When you login again, there will be a [Switch Roles] link in the upper right hand area of the page.  Clicking on [Switch Roles] will let you switch between being a Judge and your other roles without needing to logout.\n', 'system'),
	('', 'judge_activate_reminder', 'Judges - Activation Reminder', 'This is sent to existing judges who have not yet activated their account for the current fair year.', '', 'Judge Registration for [FAIRNAME]', 'This message is to let you know that Judge registration for the [FAIRNAME] is now open.  If you would like to participate in the fair this year please log in to the registration site using your email address ([EMAIL]) an\n', 'system'),
	('', 'volunteer_activate_reminder', 'Volunteer Registration - Activation Reminder', 'This is sent to existing volunteers who have not yet activated their account for the current fair year.', '', 'Volunteer Registration for [FAIRNAME]', 'This message is to let you know that Volunteer registration for the [FAIRNAME] is now open.  If you would like to participate in the fair this year please log in to the registration site using your email address ([EMAIL]).\n', 'system');

DELETE FROM `emails` WHERE `val`='new_judge_invite';
DELETE FROM `emails` WHERE `val`='register_judges_resend_password';

-- Fix the names of these emails
UPDATE `emails` SET `name` = 'Volunteers - New Volunteer Invitation' WHERE `val`='volunteer_new_invite';
UPDATE `emails` SET `name` = 'Volunteers - Add Volunteer Invitation' WHERE `val`='volunteer_add_invite';
