ALTER TABLE `users` ADD `firstaid` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no' AFTER `postalcode` ;
ALTER TABLE `users` ADD `cpr` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no' AFTER `firstaid` ;

UPDATE `config` SET `type_values` = 'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province|firstaid=First Aid and CPR' WHERE `var` = 'committee_personal_fields' ;
UPDATE `config` SET `type_values` = 'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province|firstaid=First Aid and CPR' WHERE `var` = 'committee_personal_required' ;

UPDATE `config` SET `type_values` = 'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province|firstaid=First Aid and CPR' WHERE `var` = 'volunteer_personal_fields' ;
UPDATE `config` SET `type_values` = 'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province|firstaid=First Aid and CPR' WHERE `var` = 'volunteer_personal_required' ;

