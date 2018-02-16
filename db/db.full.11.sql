-- phpMyAdmin SQL Dump
-- version 2.6.0-rc2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: May 26, 2005 at 04:29 PM
-- Server version: 4.0.24
-- PHP Version: 4.3.11
-- 
-- Database: `sfiab`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `award_awards`
-- 

CREATE TABLE `award_awards` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_sponsors_id` int(10) unsigned NOT NULL default '0',
  `award_types_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `criteria` text NOT NULL,
  `presenter` varchar(128) NOT NULL default '',
  `order` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `award_sponsors_id` (`award_sponsors_id`),
  KEY `award_types_id` (`award_types_id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `award_awards_projectcategories`
-- 

CREATE TABLE `award_awards_projectcategories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned NOT NULL default '0',
  `projectcategories_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `year` (`year`),
  KEY `award_awards_id` (`award_awards_id`),
  KEY `projectcategories_id` (`projectcategories_id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `award_awards_projectdivisions`
-- 

CREATE TABLE `award_awards_projectdivisions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned NOT NULL default '0',
  `projectdivisions_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `award_awards_id` (`award_awards_id`),
  KEY `projectdivisions_id` (`projectdivisions_id`),
  KEY `year` (`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `award_contacts`
-- 

CREATE TABLE `award_contacts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_sponsors_id` int(10) unsigned NOT NULL default '0',
  `firstname` varchar(32) NOT NULL default '',
  `lastname` varchar(32) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `phonehome` varchar(32) NOT NULL default '',
  `phonework` varchar(32) NOT NULL default '',
  `phonecell` varchar(32) NOT NULL default '',
  `fax` varchar(32) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  `notes` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `award_sponsors_id` (`award_sponsors_id`),
  KEY `year` (`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `award_prizes`
-- 

CREATE TABLE `award_prizes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned NOT NULL default '0',
  `cash` int(11) NOT NULL default '0',
  `scholarship` int(11) NOT NULL default '0',
  `prize` varchar(128) NOT NULL default '',
  `number` int(11) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `award_awards_id` (`award_awards_id`),
  KEY `year` (`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `award_sponsors`
-- 

CREATE TABLE `award_sponsors` (
  `id` int(11) NOT NULL auto_increment,
  `organization` varchar(128) NOT NULL default '',
  `phone` varchar(32) NOT NULL default '',
  `fax` varchar(32) NOT NULL default '',
  `email` varchar(32) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  `address` varchar(128) NOT NULL default '',
  `city` varchar(64) NOT NULL default '',
  `province_code` char(2) NOT NULL default '',
  `postalcode` varchar(8) NOT NULL default '',
  `notes` text NOT NULL,
  `confirmed` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `award_types`
-- 

CREATE TABLE `award_types` (
  `id` int(10) unsigned NOT NULL,
  `type` varchar(64) NOT NULL default '',
  `order` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  UNIQUE (id,year)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `committees`
-- 

CREATE TABLE `committees` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `committees_link`
-- 

CREATE TABLE `committees_link` (
  `committees_id` int(10) unsigned NOT NULL default '0',
  `committees_members_id` int(10) unsigned NOT NULL default '0',
  `title` varchar(128) NOT NULL default '',
  `ord` tinyint(3) unsigned NOT NULL default '0'
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `committees_members`
-- 

CREATE TABLE `committees_members` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `organization` varchar(128) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `emailprivate` varchar(128) NOT NULL default '',
  `phonehome` varchar(32) NOT NULL default '',
  `phonework` varchar(32) NOT NULL default '',
  `phonecell` varchar(32) NOT NULL default '',
  `fax` varchar(32) NOT NULL default '',
  `ord` int(11) NOT NULL default '0',
  `displayemail` enum('N','Y') NOT NULL default 'N',
  `access_admin` enum('N','Y') NOT NULL default 'Y',
  `access_config` enum('N','Y') NOT NULL default 'N',
  `access_super` enum('N','Y') NOT NULL default 'N',
  `deleted` enum('N','Y') NOT NULL default 'N',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `config`
-- 

CREATE TABLE `config` (
  `var` varchar(64) NOT NULL default '',
  `val` text NOT NULL,
  `description` text NOT NULL,
  `year` int(11) NOT NULL default '0'
) TYPE=MyISAM;
ALTER TABLE `config` ADD UNIQUE (`var`,`year`);

-- --------------------------------------------------------

-- 
-- Table structure for table `dates`
-- 

CREATE TABLE `dates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(32) NOT NULL default '',
  `description` varchar(64) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `emails`
-- 

CREATE TABLE `emails` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `val` varchar(64) NOT NULL default '',
  `name` varchar(128) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `from` varchar(128) NOT NULL default '',
  `subject` varchar(128) NOT NULL default '',
  `body` text NOT NULL,
  `type` enum('system','user') NOT NULL default 'system',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `val` (`val`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `emergencycontact`
-- 

CREATE TABLE `emergencycontact` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `students_id` int(10) unsigned NOT NULL default '0',
  `firstname` varchar(64) NOT NULL default '',
  `lastname` varchar(64) NOT NULL default '',
  `relation` varchar(64) NOT NULL default '',
  `phone1` varchar(32) NOT NULL default '',
  `phone2` varchar(32) NOT NULL default '',
  `phone3` varchar(32) NOT NULL default '',
  `phone4` varchar(32) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges`
-- 

CREATE TABLE `judges` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `firstname` varchar(32) NOT NULL default '',
  `lastname` varchar(32) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `passwordexpiry` date default NULL,
  `phonehome` varchar(32) NOT NULL default '',
  `phonework` varchar(32) NOT NULL default '',
  `phoneworkext` varchar(16) NOT NULL default '',
  `phonecell` varchar(32) NOT NULL default '',
  `organization` varchar(64) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `address` varchar(64) NOT NULL default '',
  `address2` varchar(64) NOT NULL default '',
  `city` varchar(64) NOT NULL default '',
  `province` varchar(32) NOT NULL default '',
  `postalcode` varchar(8) NOT NULL default '',
  `catpref` int(10) unsigned default NULL,
  `divpref` int(10) unsigned default NULL,
  `highest_psd` varchar(128) NOT NULL default '',
  `professional_quals` varchar(128) NOT NULL default '',
  `years_school` tinyint(3) unsigned NOT NULL default '0',
  `years_regional` tinyint(3) unsigned NOT NULL default '0',
  `years_national` tinyint(3) unsigned NOT NULL default '0',
  `willing_chair` enum('no','yes') NOT NULL default 'no',
  `attending_lunch` enum('no','yes') NOT NULL default 'yes',
  `expertise_other` text,
  `deleted` enum('no','yes') NOT NULL default 'no',
  `deleteddatetime` datetime default NULL,
  `complete` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_expertise`
-- 

CREATE TABLE `judges_expertise` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `judges_id` int(10) unsigned NOT NULL default '0',
  `projectdivisions_id` int(10) unsigned default NULL,
  `projectsubdivisions_id` int(10) unsigned default NULL,
  `val` tinyint(3) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_languages`
-- 

CREATE TABLE `judges_languages` (
  `judges_id` int(10) unsigned NOT NULL default '0',
  `languages_lang` char(2) NOT NULL default '',
  PRIMARY KEY  (`judges_id`,`languages_lang`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_teams`
-- 

CREATE TABLE `judges_teams` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `num` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `autocreate_type_id` int(11) default NULL,
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `num` (`num`,`year`),
  UNIQUE KEY `name` (`name`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_teams_awards_link`
-- 

CREATE TABLE `judges_teams_awards_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned NOT NULL default '0',
  `judges_teams_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `award_awards_id` (`award_awards_id`,`judges_teams_id`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_teams_link`
-- 

CREATE TABLE `judges_teams_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `judges_id` int(11) NOT NULL default '0',
  `judges_teams_id` int(11) NOT NULL default '0',
  `captain` enum('no','yes') NOT NULL default 'no',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_teams_timeslots_link`
-- 

CREATE TABLE `judges_teams_timeslots_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `judges_teams_id` int(10) unsigned NOT NULL default '0',
  `judges_timeslots_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `judges_teams_id` (`judges_teams_id`,`judges_timeslots_id`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_teams_timeslots_projects_link`
-- 

CREATE TABLE `judges_teams_timeslots_projects_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `judges_teams_id` int(10) unsigned NOT NULL default '0',
  `judges_timeslots_id` int(10) unsigned NOT NULL default '0',
  `projects_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `judges_teams_id` (`judges_teams_id`,`judges_timeslots_id`,`projects_id`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_timeslots`
-- 

CREATE TABLE `judges_timeslots` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `starttime` time NOT NULL default '00:00:00',
  `endtime` time NOT NULL default '00:00:00',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_years`
-- 

CREATE TABLE `judges_years` (
  `judges_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`judges_id`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `languages`
-- 

CREATE TABLE `languages` (
  `lang` char(2) NOT NULL default '',
  `langname` varchar(32) NOT NULL default '',
  `active` enum('N','Y') NOT NULL default 'N',
  UNIQUE KEY `lang` (`lang`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `mentors`
-- 

CREATE TABLE `mentors` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  `firstname` varchar(64) NOT NULL default '',
  `lastname` varchar(64) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `phone` varchar(32) NOT NULL default '',
  `organization` varchar(128) NOT NULL default '',
  `position` varchar(128) NOT NULL default '',
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `pagetext`
-- 

CREATE TABLE `pagetext` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `textname` varchar(64) NOT NULL default '',
  `text` text NOT NULL,
  `lastupdate` DATETIME NOT NULL,
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY (`textname`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `project_specialawards_link`
-- 

CREATE TABLE `project_specialawards_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned NOT NULL default '0',
  `projects_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `projectcategories`
-- 

CREATE TABLE `projectcategories` (
  `id` int(10) unsigned NOT NULL default '0',
  `category` varchar(64) NOT NULL default '',
  `mingrade` tinyint(4) NOT NULL default '0',
  `maxgrade` tinyint(4) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `projectdivisions`
-- 

CREATE TABLE `projectdivisions` (
  `id` int(10) unsigned NOT NULL default '0',
  `division` varchar(64) NOT NULL default '',
  `division_shortform` char(3) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `projectdivisionsselector`
-- 

CREATE TABLE `projectdivisionsselector` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` varchar(255) NOT NULL default '',
  `yes` int(10) unsigned NOT NULL default '0',
  `yes_type` enum('question','division') NOT NULL default 'question',
  `no` int(10) unsigned NOT NULL default '0',
  `no_type` enum('question','division') NOT NULL default 'question',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `projects`
-- 

CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `projectnumber` varchar(16) default NULL,
  `projectcategories_id` tinyint(4) NOT NULL default '0',
  `projectdivisions_id` tinyint(4) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `summary` text NOT NULL,
  `year` int(11) NOT NULL default '0',
  `req_electricity` enum('no','yes') NOT NULL default 'no',
  `req_table` enum('no','yes') NOT NULL default 'yes',
  `req_special` varchar(128) NOT NULL default '',
  `language` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `projectsubdivisions`
-- 

CREATE TABLE `projectsubdivisions` (
  `id` int(10) unsigned NOT NULL default '0',
  `year` int(11) unsigned NOT NULL default '0',
  `projectdivisions_id` int(10) unsigned NOT NULL default '0',
  `subdivision` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `provinces`
-- 

CREATE TABLE `provinces` (
  `code` char(2) NOT NULL default '',
  `province` varchar(32) NOT NULL default '',
  UNIQUE KEY `code` (`code`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `registrations`
-- 

CREATE TABLE `registrations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `num` varchar(8) NOT NULL default '',
  `email` varchar(64) NOT NULL default '',
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` enum('new','open','paymentpending','complete') NOT NULL default 'new',
  `end` datetime NOT NULL default '0000-00-00 00:00:00',
  `year` int(11) NOT NULL default '0',
  `nummentors` tinyint(4) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `safety`
-- 

CREATE TABLE `safety` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `safetyquestions_id` int(10) unsigned NOT NULL default '0',
  `answer` varchar(32) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `safetyquestions`
-- 

CREATE TABLE `safetyquestions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` int(10) unsigned NOT NULL default '0',
  `question` text NOT NULL,
  `type` enum('check','yesno') NOT NULL default 'check',
  `required` enum('no','yes') NOT NULL default 'yes',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `schools`
-- 

CREATE TABLE `schools` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `school` varchar(64) NOT NULL default '',
  `phone` varchar(16) NOT NULL default '',
  `fax` varchar(16) NOT NULL default '',
  `address` varchar(64) NOT NULL default '',
  `city` varchar(32) NOT NULL default '',
  `province_code` char(2) NOT NULL default '',
  `postalcode` varchar(7) NOT NULL default '',
  `sciencehead` varchar(64) NOT NULL default '',
  `scienceheademail` varchar(128) NOT NULL default '',
  `scienceheadphone` varchar(32) NOT NULL default '',
  `accesscode` varchar(32) NOT NULL default '',
  `year` int(10) unsigned NOT NULL default '0',
  `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `junior` tinyint(4) NOT NULL default '0',
  `intermediate` tinyint(4) NOT NULL default '0',
  `senior` tinyint(4) NOT NULL default '0',
  `registration_password` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `students`
-- 

CREATE TABLE `students` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `firstname` varchar(64) NOT NULL default '',
  `lastname` varchar(64) NOT NULL default '',
  `sex` enum('male','female') NOT NULL default 'male',
  `address` varchar(255) NOT NULL default '',
  `city` varchar(64) NOT NULL default '',
  `province` varchar(32) NOT NULL default '',
  `postalcode` varchar(8) NOT NULL default '',
  `phone` varchar(64) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `grade` tinyint(3) unsigned NOT NULL default '0',
  `dateofbirth` date NOT NULL default '0000-00-00',
  `age` tinyint(3) unsigned NOT NULL default '0',
  `lang` char(2) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  `schools_id` int(10) unsigned NOT NULL default '0',
  `tshirt` enum('small','medium','large','xlarge') NOT NULL default 'medium',
  `medicalalert` varchar(255) NOT NULL default '',
  `foodreq` varchar(255) NOT NULL default '',
  `teachername` varchar(64) NOT NULL default '',
  `teacheremail` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `translations`
-- 

CREATE TABLE `translations` (
  `lang` char(2) NOT NULL default '',
  `strmd5` varchar(32) NOT NULL default '',
  `str` text NOT NULL,
  `val` text NOT NULL,
  PRIMARY KEY  (`strmd5`),
  KEY `strmd5` (`strmd5`),
  KEY `lang` (`lang`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `winners`
-- 

CREATE TABLE `winners` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `awards_prizes_id` int(10) unsigned NOT NULL default '0',
  `projects_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `awards_prizes_id` (`awards_prizes_id`,`projects_id`,`year`)
) TYPE=MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_catpref`
-- 

CREATE TABLE `judges_catpref` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
	`judges_id` INT NOT NULL ,
	`projectcategories_id` INT NOT NULL ,
	`rank` INT NOT NULL ,
	`year` INT NOT NULL ,
	PRIMARY KEY ( `id` )
) TYPE = MYISAM ;

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_schedulerconfig`
-- 

CREATE TABLE `judges_schedulerconfig` (
	`var` VARCHAR( 64 ) NOT NULL DEFAULT '',
	`val` TEXT NOT NULL ,
	`description` TEXT NOT NULL ,
	`year` INT( 11 ) NOT NULL DEFAULT '0',
	UNIQUE KEY `var` ( `var` , `year` )
) TYPE = MYISAM ;


-- Now insert everything

INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('fairname', '', 'Name of the fair', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('default_language', 'en', 'The default language if no language has yet been specified', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('minstudentsperproject', '1', 'The minimum number of students that can work on a project (usually 1)', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('maxstudentsperproject', '2', 'The maximum number of students that can work on a project (Usually 2)', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('mingrade', '7', 'The minimum school grade that can enter a project', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('maxgrade', '12', 'The maximum school grade that can enter a project', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('minage', '10', 'The minimum age of the students', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('maxage', '21', 'The maximum age of the students', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('maxmentorsperproject', '5', 'The maximum number of mentors that can help with a project', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('minmentorsperproject', '0', 'The minimum number of mentors that can help with a project (usually 0)', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('usedivisionselector', 'yes', 'Specify whether to use the division selector flowchart questions to help decide on the division (yes/no)', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('minjudgeage', '21', 'The minimum age that a person must be in order to be a judge.', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('maxjudgeage', '100', 'The maximum age that a person can be in order to be a judge', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('participant_student_foodreq', 'yes', 'Ask for students special food requirements (yes/no). Should be yes if you plan on providing lunch', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('regfee', '', 'Registration Fee', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('regfee_per', 'student', 'Registration fee is per student, or per project? (student/project)', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('project_num_format', 'CDN', 'C=Category, D=Divison, N=2 digit Number', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('committee_publiclayout', '<tr><td>   <b>name</b></td><td>title</td><td>email</td></tr>', 'The layout (html table row) used to display the committee members on the public committee page', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('judges_password_expiry_days', '365', 'Judges passwords expire and they are forced to choose a new one after this many days. (0 for no expiry)', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('maxspecialawardsperproject', '7', 'The maximum number of self-nominated special awards a project can sign-up for', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('specialawardnomination', 'date', 'Self nominations for special awards are due either with registration or on a specific date. (date|registration).  If "date" is used, it must be configured under "Important Dates" section.', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ('fairmanageremail', '', 'The email address of the ''fair manager''.  Any important emails etc generated by the system will be sent here', -1);
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ( 'participant_registration_type', 'open', 'The type of Participant Registration to use: open | singlepassword | schoolpassword | invite', '-1');
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ( 'judge_registration_type', 'open', 'The type of Judge Registration to use: open | singlepassword | invite', '-1');
INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES ( 'participant_registration_singlepassword', '', 'The single password to use for participant registration if participant_registration_type is singlepassword.  Leave blank if not using singlepassword participant registration','-1');
INSERT INTO `config` (`var`, `val` , `description` , `year` ) VALUES ( 'judge_registration_singlepassword', '', 'The single password to use for judge registration if judge_registration_type is singlepassword.  Leave blank if not using singlepassword judge registraiton', '-1');
INSERT INTO `dates` (`date`, `name`, `description`, `year`) VALUES ('', 'fairdate', 'Date of the fair', -1);
INSERT INTO `dates` (`date`, `name`, `description`, `year`) VALUES ('', 'regopen', 'Registration system opens', -1);
INSERT INTO `dates` (`date`, `name`, `description`, `year`) VALUES ('', 'regclose', 'Registration system closes', -1);
INSERT INTO `dates` (`date`, `name`, `description`, `year`) VALUES ('', 'postparticipants', 'Registered participants are posted on the website', -1);
INSERT INTO `dates` (`date`, `name`, `description`, `year`) VALUES ('', 'postwinners', 'Winners are posted on the website', -1);
INSERT INTO `dates` (`date`, `name`, `description`, `year`) VALUES ('', 'judgeregopen', 'Judges registration opens', -1);
INSERT INTO `dates` (`date`, `name`, `description`, `year`) VALUES ('', 'judgeregclose', 'Judges registration closes', -1);
INSERT INTO `dates` (`date`, `name`, `description`, `year`) VALUES ('', 'specawardregopen', 'Special Awards self-nomination opens', -1);
INSERT INTO `dates` (`date`, `name`, `description`, `year`) VALUES ('', 'specawardregclose', 'Special Awards self-nomination closes', -1);

INSERT INTO `languages` (`lang`, `langname`, `active`) VALUES ('en', 'English', 'Y');
INSERT INTO `languages` (`lang`, `langname`, `active`) VALUES ('fr', 'Français', 'Y');

INSERT INTO `provinces` (`code`, `province`) VALUES ('AB', 'Alberta');
INSERT INTO `provinces` (`code`, `province`) VALUES ('BC', 'British Columbia');
INSERT INTO `provinces` (`code`, `province`) VALUES ('MB', 'Manitoba');
INSERT INTO `provinces` (`code`, `province`) VALUES ('NB', 'New Brunswick');
INSERT INTO `provinces` (`code`, `province`) VALUES ('NF', 'Newfoundland and Labrador');
INSERT INTO `provinces` (`code`, `province`) VALUES ('NT', 'Northwest Territories');
INSERT INTO `provinces` (`code`, `province`) VALUES ('NS', 'Nova Scotia');
INSERT INTO `provinces` (`code`, `province`) VALUES ('NU', 'Nunavut');
INSERT INTO `provinces` (`code`, `province`) VALUES ('ON', 'Ontario');
INSERT INTO `provinces` (`code`, `province`) VALUES ('PE', 'Prince Edward Island');
INSERT INTO `provinces` (`code`, `province`) VALUES ('QC', 'Québec');
INSERT INTO `provinces` (`code`, `province`) VALUES ('SK', 'Saskatchewan');
INSERT INTO `provinces` (`code`, `province`) VALUES ('YK', 'Yukon Territory');

INSERT INTO `award_types` VALUES (1, 'Divisional', 1, -1);
INSERT INTO `award_types` VALUES (2, 'Special', 2, -1);
INSERT INTO `award_types` VALUES (3, 'Interdisciplinary', 3, -1);
INSERT INTO `award_types` VALUES (4, 'Grand', 5, -1);
INSERT INTO `award_types` VALUES (5, 'Other', 4, -1);

INSERT INTO `pagetext` (`textname`,`text`,`year`) VALUES ('register_participants_main_instructions', 'Once all sections are complete, please print the signature page, obtain the required signatures, and mail the signature form, along with any required registration fees to:\r\nInsert address here\r\n\r\nYour forms must be received, post marked by <b>insert date here</b>.  Late entries will not be accepted', -1);
INSERT INTO `pagetext` (`textname`,`text`,`year`) VALUES ('index', 'Welcome to the online registration and management system for the fair.  Using the links on the left the public can register as a participant or register as a judge. \r\n\r\nThe committee can use the Fair Administration link to manage the fair, see who''s registered, generate reports, etc.  \r\n\r\nThe SFIAB configuration link is for the committee webmaster to manage the configuration of the Science Fair In A Box for the fair.\r\n', -1);

INSERT INTO `emails` VALUES ('', 'register_participants_resend_regnum', 'Participant Registration - Resend Registration Number', 'Resend the password to the participant if they submit a ''forgot regnum'' request', 'website@sfiab.ca', 'Registration for [FAIRNAME]', 'We have received a request for the retrieval of your registration number from this email address.  Please find your existing registration number below\r\n\r\nRegistration Number: [REGNUM]\r\n', 'system');
INSERT INTO `emails` VALUES ('', 'new_participant', 'New Participant', 'Email that new participants receive when they are added to the system', 'website@sfiab.ca', 'Registration for [FAIRNAME]', 'A new registration account has been created for you.  To access your registration account, please enter the following registration number into the registration website:\r\n\r\nRegistration Number: [REGNUM]\r\n', 'system');
INSERT INTO `emails` VALUES ('', 'new_judge_invite', 'New Judge Invitation', 'This is sent to a new judge when they are invited using the invite judges administration section, only available when judge_registraiton_type=invite', 'registration@sfiab.ca', 'Judge Registration for [FAIRNAME]', 'You have been invited to be a judge for the [FAIRNAME].  An account has been created for you to login with and complete your information.  You can login to the judge registration site with:\r\n\r\nEmail Address: [EMAIL]\r\nPassword: [PASSWORD]\r\n\r\nYou can change your password once you login.', 'system');
INSERT INTO `emails` VALUES ('', 'register_judges_resend_password', 'Judges Registration - Resend Password', 'Resend the password to the judge if they submit a ''forgot password'' request', 'website@sfiab.ca', 'Judge Registration for [FAIRNAME]', 'We have received a request for the retrieval of your password from this email address. Please find your existing password below Judge Email Address: [EMAIL] Judge Registration Password: [PASSWORD] ', 'system');


INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'timeslot_length', '12', 'The length of time (in minutes) that a judging timeslot should be', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'timeslot_break', '3', 'The length of time (in minutes) between timeslots to allow judges to move between projects', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'num_times_judged', '3', 'The number of times that each project must be judged (by different judging teams)', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'num_timeslots', '20', 'The number of timeslots available during the judging period', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'max_projects_per_team', '5', 'The maximum number of projects that a team can judge', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'min_judges_per_team', '2', 'The minimum number of judges that should be on a judging team', '-1');
INSERT INTO `judges_schedulerconfig` ( `var` , `val` , `description` , `year` ) VALUES ( 'max_judges_per_team', '4', 'The maximum number of judges that should be on a judging team', '-1');

INSERT INTO `config` (`var`,`val`,`year`) VALUES ('DBVERSION','11','0');

