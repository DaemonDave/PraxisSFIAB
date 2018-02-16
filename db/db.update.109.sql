ALTER TABLE `pagetext` ADD `lang` VARCHAR( 2 ) DEFAULT 'en' NOT NULL ;
ALTER TABLE `pagetext` DROP INDEX `textname`;
ALTER TABLE pagetext ADD UNIQUE(`textname`,`year`,`lang`); 
