ALTER TABLE `fairs` DROP `award_awards_ids` ;

CREATE TABLE `fairs_awards_link` (
	`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`fairs_id` INT NOT NULL ,
	`award_awards_id` INT NOT NULL ,
	`download_award` ENUM( 'no', 'yes' ) NOT NULL ,
	`upload_winners` ENUM( 'no', 'yes' ) NOT NULL
) ENGINE = MYISAM ;

