ALTER TABLE `reports` ADD `system_report_id` INT NOT NULL AFTER `id` ;

UPDATE reports SET system_report_id = id WHERE id <=34;


