ALTER TABLE `award_awards` ADD `self_nominate` ENUM('yes', 'no') NOT NULL DEFAULT 'yes' AFTER `cwsfaward` ;
ALTER TABLE `award_awards` ADD `schedule_judges` ENUM('yes', 'no') NOT NULL DEFAULT 'yes' AFTER `self_nominate` ;

UPDATE award_awards SET self_nominate='yes' WHERE 1;
UPDATE award_awards SET schedule_judges='yes' WHERE 1;

