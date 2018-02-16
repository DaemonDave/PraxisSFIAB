ALTER TABLE `award_awards` ADD `cwsfaward` BOOL DEFAULT '0' NOT NULL AFTER `excludefromac` ;
INSERT INTO `config` (`var`, `val`, `category`, `ord`, `description`, `year`) VALUES ('ysf_region_id', '', 'CWSF Registration', 100, 'Your YSF Assigned Region Identifier', -1);
INSERT INTO `config` (`var`, `val`, `category`, `ord`, `description`, `year`) VALUES ('ysf_region_password', '', 'CWSF Registration', 200, 'Your YSF Assigned Region Password', -1);
ALTER TABLE `projectdivisions` ADD `cwsfdivisionid` INT DEFAULT NULL AFTER `division_shortform` ;
ALTER TABLE `projects` ADD `cwsfdivisionid` INT DEFAULT NULL AFTER `projectdivisions_id` ;
