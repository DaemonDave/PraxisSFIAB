ALTER TABLE `award_sources` ADD `enabled` ENUM( 'no', 'yes' ) DEFAULT 'no' NOT NULL ;
ALTER TABLE `award_sources` ADD `website` VARCHAR( 255 ) NOT NULL AFTER `url` ;
INSERT INTO `award_sources` VALUES ('', 'Sci-Tech Ontario', 'http://www.scitechontario.org/awarddownloader/index.php', 'http://www.scitechontario.org/awarddownloader/help.php', '', '', 'no');
INSERT INTO `award_sources` VALUES ('', 'Youth Science Foundation', 'https://secure.ysf-fsj.ca/awarddownloader/index.php', 'http://apps.ysf-fsj.ca/awarddownloader/help.php', '', '', 'no');
