ALTER TABLE `judges_timeslots` ADD `round_id` INT NOT NULL AFTER `id` ;
ALTER TABLE `judges_timeslots` ADD `name` TINYTEXT NOT NULL AFTER `endtime` ;
ALTER TABLE `judges_timeslots` ADD `type` ENUM( 'timeslot','divisional1', 'divisional2', 'grand', 'special' ) NOT NULL AFTER `round_id` ;

UPDATE `judges_timeslots` SET `type`='divisional1' WHERE allowdivisional='yes';
UPDATE `judges_timeslots` SET `type`='special' WHERE allowdivisional='no';

ALTER TABLE `judges_timeslots` DROP `allowdivisional`  ;


