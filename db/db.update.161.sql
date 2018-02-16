ALTER TABLE `emailqueue` ADD `numfailed` INT NOT NULL DEFAULT '0';
ALTER TABLE `emailqueue` ADD `numbounced` INT NOT NULL DEFAULT '0';
ALTER TABLE `emailqueue_recipients` ADD `result` ENUM( 'ok', 'failed' ) NULL DEFAULT NULL 
