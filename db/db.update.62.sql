ALTER TABLE `users_committee` CHANGE `displayemail` `displayemail` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no',
CHANGE `access_admin` `access_admin` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no',
CHANGE `access_config` `access_config` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no',
CHANGE `access_super` `access_super` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no';

ALTER TABLE `committees_link` ADD `users_id` INT NOT NULL AFTER `committees_members_id` ;

INSERT INTO `emails` ( `id` , `val` , `name` , `description` , `from` , `subject` , `body` , `type` )
VALUES (
'', 'committee_recover_password', 'Committee Members - Recover Password', 'Recover the password for a committee member if they submit a ''forgot password'' request', '', 'Committee Member for [FAIRNAME]', 'We have received a request for the recovery of your password from this email address. Please find your new password below:\n\nCommittee Member Email Address: [EMAIL]\nCommittee Member Password: [PASSWORD] ', 'system'
);

