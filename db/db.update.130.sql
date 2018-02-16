ALTER TABLE `award_awards` CHANGE `award_sources_id` `award_source_fairs_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `fairs` ADD `award_awards_ids` TEXT NOT NULL;

