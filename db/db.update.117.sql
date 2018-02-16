-- complete has been moved inside each users_* table
ALTER TABLE `users` DROP `complete`;

-- drop the old judge tables, all this info is now in the new user system (converted in the 116 update)
DROP TABLE `judges`,`judges_catpref`,`judges_expertise`,`judges_languages`,`judges_years` ;

-- questions table should use users_id now (which is what was being saved in the registrations_id)
ALTER TABLE `question_answers` CHANGE `registrations_id` `users_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0';

-- The answer has been linked to a users_id that is unique per-year, so we don't need to duplicate the year storage
ALTER TABLE `question_answers` DROP `year`;

-- Use users_id instead of judges_id now, the judges_id was converted to the proper users_id in the 116 update
ALTER TABLE `judges_specialaward_sel` CHANGE `judges_id` `users_id` INT( 11 ) NOT NULL DEFAULT '0';
ALTER TABLE `judges_teams_link` CHANGE `judges_id` `users_id` INT( 11 ) NOT NULL DEFAULT '0' ;

-- The users_id is linked with the year, don't need to store it here too
ALTER TABLE `judges_specialaward_sel` DROP `year`;

ALTER TABLE users ADD UNIQUE (username,year);

