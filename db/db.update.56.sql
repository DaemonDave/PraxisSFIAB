CREATE TABLE `users` (
`id` int( 10 ) unsigned NOT NULL AUTO_INCREMENT ,
`types` set( 'student', 'judge', 'committee', 'volunteer', 'region' ) NOT NULL,
`firstname` varchar( 32 ) NOT NULL default '',
`lastname` varchar( 32 ) NOT NULL default '',
`username` varchar( 128 ) NOT NULL default '',
`password` varchar( 32 ) NOT NULL default '',
`passwordexpiry` date default NULL ,
`email` varchar( 128 ) NOT NULL default '',
`phonehome` varchar( 32 ) NOT NULL default '',
`phonework` varchar( 32 ) NOT NULL default '',
`phonecell` varchar( 32 ) NOT NULL default '',
`fax` varchar( 32 ) NOT NULL default '',
`organization` varchar( 64 ) NOT NULL default '',
`created` datetime NOT NULL default '0000-00-00 00:00:00',
`lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
`address` varchar( 64 ) NOT NULL default '',
`address2` varchar( 64 ) NOT NULL default '',
`city` varchar( 64 ) NOT NULL default '',
`province` varchar( 32 ) NOT NULL default '',
`postalcode` varchar( 8 ) NOT NULL default '',
`deleted` enum( 'no', 'yes' ) NOT NULL default 'no',
`deleteddatetime` datetime default NULL ,
`complete` enum( 'no', 'yes' ) NOT NULL default 'no',
PRIMARY KEY ( `id` )
) TYPE = MYISAM ;

CREATE TABLE `users_volunteer` (
`users_id` INT NOT NULL ,
`tmp` INT NOT NULL ,
PRIMARY KEY ( `users_id` )
) TYPE = MYISAM ;

CREATE TABLE `users_committee` (
`users_id` INT NOT NULL ,
`emailprivate` VARCHAR( 128 ) NOT NULL ,
`ord` INT NOT NULL ,
`displayemail` ENUM( 'N', 'Y' ) NOT NULL ,
`access_admin` ENUM( 'N', 'Y' ) NOT NULL ,
`access_config` ENUM( 'N', 'Y' ) NOT NULL ,
`access_super` ENUM( 'N', 'Y' ) NOT NULL ,
PRIMARY KEY ( `users_id` )
) TYPE = MYISAM ;

INSERT INTO `emails` ( `id` , `val` , `name` , `description` , `from` , `subject` , `body` , `type` )
VALUES (
'', 'volunteer_welcome', 'Volunteer Registration - Welcome', 'Welcome email sent to a volunteer after they have registered for the first time. This email includes their temporary password.', '', 'Volunteer Registration for [FAIRNAME]', 'Thank you for registering as a volunteer at our fair. Please find your temporary password below. After logging in for the first time you will be prompted to change your password.\n\nVolunteer Email Address: [EMAIL]\nVolunteer Password: [PASSWORD]', 'system'
);

INSERT INTO `emails` ( `id` , `val` , `name` , `description` , `from` , `subject` , `body` , `type` )
VALUES (
'', 'volunteer_recover_password', 'Volunteer Registration - Recover Password', 'Recover the password for a volunteer if they submit a ''forgot password'' request', '', 'Volunteer Registration for [FAIRNAME]', 'We have received a request for the recovery of your password from this email address. Please find your new password below:\n\nVolunteer Email Address: [EMAIL]\nVolunteer Password: [PASSWORD] ', 'system'
);

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'volunteer_password_expiry_days', '365', 'Volunteer Registration', 'number', '', '300', 'Volunteer passwords expire and they are forced to choose a new one after this many days. (0 for no expiry)', '-1'
);
