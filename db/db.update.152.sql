INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'judge_personal_fields', 'phonehome,phonecell,phonework,org,address,city,province,lang', 
'Judge Registration', 'multisel',
'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 
'500', 'Personal Information to ask for on the Judge personal information page (in addition to Name and Email)', '-1');


INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` )
VALUES (
'judge_personal_required', 'phonehome,address,city,province', 
'Judge Registration', 'multisel',
'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 
'600', 'Required Personal Information on the Judge personal information page (Name and Email is always required)', '-1');


