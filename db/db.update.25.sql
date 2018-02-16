ALTER TABLE `award_contacts` ADD `position` VARCHAR( 64 ) NOT NULL AFTER `lastname` ;
ALTER TABLE `award_prizes` ADD `value` INT NOT NULL AFTER `scholarship` ;
UPDATE `award_prizes` SET `value` = `cash` + `scholarship`;
