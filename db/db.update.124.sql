CREATE TABLE `judges_availability` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`users_id` INT NOT NULL ,
	`date` DATE NOT NULL ,
	`start` TIME NOT NULL ,
	`end` TIME NOT NULL ,
	PRIMARY KEY ( `id` )
) ENGINE = MYISAM ;

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`)
VALUES ( 'judges_availability_enable', 'no', 'Judge Registration', 'yesno', '', '950', 'Allow judges to specify their time availability on the fair day based on the defined judging rounds/timeslots. The scheduler will then use this judge availability data when assigning judges to teams and projects.', '-1');
