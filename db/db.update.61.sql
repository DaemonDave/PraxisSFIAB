CREATE TABLE `volunteer_positions` (
`id` INT NOT NULL AUTO_INCREMENT ,
`name` VARCHAR( 128 ) NOT NULL ,
`desc` TINYTEXT NOT NULL ,
`meet_place` TINYTEXT NOT NULL ,
`start` DATETIME NOT NULL ,
`end` DATETIME NOT NULL ,
`year` INT NOT NULL ,
PRIMARY KEY ( `id` )
) TYPE = MYISAM ;

CREATE TABLE `volunteer_positions_signup` (
`id` INT NOT NULL AUTO_INCREMENT ,
`users_id` INT NOT NULL ,
`volunteer_positions_id` INT NOT NULL ,
`year` INT NOT NULL ,
PRIMARY KEY ( `id` )
) TYPE = MYISAM ;

ALTER TABLE `reports` CHANGE `type` `type` ENUM( 'student', 'judge', 'award', 'committee', 'school', 'volunteer' ) NOT NULL DEFAULT 'student';

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'volunteer_enable', 'no', 'Volunteer Registration', 'yesno', '', '100', 'Allow Volunteers to create accounts and sign up for volunteer positions (positions are configurable in the admin section)', '-1');

