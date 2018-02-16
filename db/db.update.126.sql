CREATE TABLE `fairs_stats` (
  `id` int(11) NOT NULL auto_increment,
  `users_uid` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  `start_date` date NOT NULL default '0000-00-00',
  `end_date` date NOT NULL default '0000-00-00',
  `address` text NOT NULL,
  `budget` float NOT NULL default '0',
  `ysf_affiliation_complete` enum('Y','N') NOT NULL default 'Y',
  `charity` tinytext NOT NULL,
  `male_1` int(11) NOT NULL default '0',
  `male_4` int(11) NOT NULL default '0',
  `male_7` int(11) NOT NULL default '0',
  `male_9` int(11) NOT NULL default '0',
  `male_11` int(11) NOT NULL default '0',
  `female_1` int(11) NOT NULL default '0',
  `female_4` int(11) NOT NULL default '0',
  `female_7` int(11) NOT NULL default '0',
  `female_9` int(11) NOT NULL default '0',
  `female_11` int(11) NOT NULL default '0',
  `projects_1` int(11) NOT NULL default '0',
  `projects_4` int(11) NOT NULL default '0',
  `projects_7` int(11) NOT NULL default '0',
  `projects_9` int(11) NOT NULL default '0',
  `projects_11` int(11) NOT NULL default '0',
  `publicschools` int(11) NOT NULL default '0',
  `privateschools` int(11) NOT NULL default '0',
  `schooldistricts` int(11) NOT NULL default '0',
  `studentsvisiting` int(11) NOT NULL default '0',
  `publicvisiting` int(11) NOT NULL default '0',
  `firstnations` int(11) NOT NULL default '0',
  `innercity` int(11) NOT NULL default '0',
  `teacherssupporting` int(11) NOT NULL default '0',
  `increasedinterest` int(11) NOT NULL default '0',
  `consideringcareer` int(11) NOT NULL default '0',
  `next_chair_name` varchar(128) NOT NULL default '',
  `next_chairemail` varchar(64) NOT NULL default '',
  `next_chair_hphone` varchar(32) NOT NULL default '',
  `next_chair_bphone` varchar(32) NOT NULL default '',
  `next_chair_fax` varchar(32) NOT NULL default '',
  `scholarships` text NOT NULL,
  `delegate1` varchar(64) NOT NULL default '',
  `delegate2` varchar(64) NOT NULL default '',
  `delegate3` varchar(64) NOT NULL default '',
  `delegate4` varchar(64) NOT NULL default '',
  `delegate1_email` tinytext NOT NULL,
  `delegate2_email` tinytext NOT NULL,
  `delegate3_email` tinytext NOT NULL,
  `delegate4_email` tinytext NOT NULL,
  `delegate1_size` enum('small','medium','large','xlarge') NOT NULL default 'small',
  `delegate2_size` enum('small','medium','large','xlarge') NOT NULL default 'small',
  `delegate3_size` enum('small','medium','large','xlarge') NOT NULL default 'small',
  `delegate4_size` enum('small','medium','large','xlarge') NOT NULL default 'small',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM ;

ALTER TABLE `schools` ADD `atrisk` ENUM( 'no', 'yes' ) NOT NULL default 'no';

-- Update the designate into '', public, indpendent, and home, more useful for
-- SFIAB.  This pre-conversions are for everyone in BC who imported the science
-- world school list
UPDATE schools SET designate='public' WHERE designate='Standard';
UPDATE schools SET designate='independent' WHERE designate='Independent';
UPDATE schools SET designate='public' WHERE designate='Alternate';
UPDATE schools SET designate='public' WHERE designate='PRP';
UPDATE schools SET designate='public' WHERE designate='Continuing Education';
UPDATE schools SET designate='public' WHERE designate='Youth Containment Ctr';
UPDATE schools SET designate='home' WHERE designate='Distributed Learning';
UPDATE schools SET designate='independent' WHERE designate='Offshore';
ALTER TABLE `schools` CHANGE `designate` `designate` ENUM( '', 'public', 'independent', 'home' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ;

