CREATE TABLE `regfee_items` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `year` INT NOT NULL ,
 `name` VARCHAR( 64 ) NOT NULL ,
 `description` TEXT NOT NULL ,
 `cost` FLOAT NOT NULL ,
 `per` ENUM( 'student', 'project') NOT NULL ,
 PRIMARY KEY ( `id` )
 ) ENGINE = MYISAM ;

CREATE TABLE `regfee_items_link` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `students_id` INT NOT NULL ,
 `regfee_items_id` INT NOT NULL ,
 PRIMARY KEY ( `id` )
 ) ENGINE = MYISAM ;

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES (
'participant_regfee_items_enable', 'no', 'Participant Registration', 'yesno', '', '2750', 'Ask the participants for registration fee item options.  Enabling this item also enables a Registration Fee Item Manager in the Administration section.  Use this manager to add optional registration items (that have a fee) for a student to select.', '-1');
