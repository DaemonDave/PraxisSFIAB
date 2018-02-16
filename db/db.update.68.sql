ALTER TABLE `users` ADD `birthdate` DATE NOT NULL AFTER `organization` ;
ALTER TABLE `users` ADD `lang` VARCHAR( 2 ) NOT NULL AFTER `birthdate` ;
ALTER TABLE `users` ADD `sex` ENUM( 'male', 'female' ) NOT NULL AFTER `lastname` ;

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'volunteer_personal_fields', 'phonehome,phonecell,org', 
'Volunteer Registration', 'multisel',
'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 
'500', 'Personal Information to ask for on the Volunteer personal information page (in addition to Name and Email)', '-1');


INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'volunteer_personal_required', '', 
'Volunteer Registration', 'multisel',
'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 
'600', 'Required Personal Information on the Volunteer personal information page (Name and Email is always required)', '-1');

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'committee_personal_fields', 'phonehome,phonecell,phonework,fax,org', 
'Committee Members', 'multisel',
'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 
'500', 'Personal Information to ask for on the Committee Member profile page (in addition to Name and Email)', '-1');


INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'committee_personal_required', '', 
'Committee Members', 'multisel',
'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 
'600', 'Required Personal Information on the Committee Member profile page (Name and Email is always required)', '-1');


