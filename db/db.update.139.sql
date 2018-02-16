ALTER TABLE `award_awards` ADD `external_additional_materials` BOOL NOT NULL AFTER `external_postback`;
ALTER TABLE `award_awards` ADD `external_register_winners` BOOL NOT NULL AFTER `external_additional_materials` ;
