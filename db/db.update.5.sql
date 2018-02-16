ALTER TABLE `award_awards` ADD `presenter` VARCHAR( 128 ) NOT NULL AFTER `criteria` ;
CREATE TABLE `winners` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`awards_prizes_id` INT UNSIGNED NOT NULL ,
	`projects_id` INT UNSIGNED NOT NULL ,
	`year` INT UNSIGNED NOT NULL ,
	PRIMARY KEY ( `id` ) 
);
ALTER TABLE winners ADD UNIQUE (
	awards_prizes_id,
	projects_id,
	year
);
