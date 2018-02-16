ALTER TABLE `winners` ADD `fairs_id` INT NOT NULL;
ALTER TABLE `projects` ADD `fairs_id` INT NOT NULL;
ALTER TABLE `students` ADD `fairs_id` INT NOT NULL AFTER `schools_id` ;
