CREATE TABLE `users_sponsor` (
  `users_id` int(11) NOT NULL default '0',
  `sponsors_id` int(11) NOT NULL default '0',
  `sponsor_complete` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no',
  `sponsor_active` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no',
  `primary` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no',
  `position` VARCHAR(64) NOT NULL default '',
  `notes` text NOT NULL,
   PRIMARY KEY  (`users_id`)
) TYPE=MyISAM;

ALTER TABLE `users` CHANGE `types` `types` SET( 'student', 'judge', 'committee', 'volunteer', 'fair', 'sponsor' ) NOT NULL ;
ALTER TABLE `users` ADD `salutation` VARCHAR( 8 ) NOT NULL AFTER `types` ;

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'sponsor_personal_fields', 'phonecell,phonework,fax,org', 
'Sponsors', 'multisel',
'salutation=Salutation|sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 
'500', 'Personal Information to ask for on the Sponsor Contact profile page (in addition to Name and Email)', '-1');

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'sponsor_personal_required', '', 
'Sponsors', 'multisel',
'salutation=Salutation|sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 
'600', 'Required Personal Information on the Sponsor Contact profile page (Name and Email is always required)', '-1');


