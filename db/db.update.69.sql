ALTER TABLE `award_awards` ADD `external_identifier` VARCHAR( 32 ) DEFAULT NULL ,
 ADD `external_postback` VARCHAR( 128 ) DEFAULT NULL ;
ALTER TABLE `award_prizes` ADD `external_identifier` VARCHAR( 32 ) DEFAULT NULL ; 
