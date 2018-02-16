ALTER TABLE `translations` DROP INDEX `strmd5`;
ALTER TABLE `translations` DROP INDEX `lang`;
ALTER TABLE `translations` ADD UNIQUE KEY ( `lang`, `strmd5` );

