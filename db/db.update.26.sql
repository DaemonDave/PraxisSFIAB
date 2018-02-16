ALTER TABLE `schools` ADD `principal` VARCHAR( 64 ) NOT NULL AFTER `postalcode` ;
ALTER TABLE `schools` ADD `schoolemail` VARCHAR( 128 ) NOT NULL AFTER `principal` ;
ALTER TABLE `schools` ADD `schoollang` VARCHAR( 2 ) NOT NULL AFTER `school` ;
ALTER TABLE `schools` ADD `schoollevel` VARCHAR( 32 ) NOT NULL AFTER `schoollang` ;
