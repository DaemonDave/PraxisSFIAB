-- --------------------------------------------------------

-- 
-- Table structure for table `questions`
-- 

CREATE TABLE `questions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` int(11) NOT NULL default '0',
  `section` varchar(32) NOT NULL,
  `db_heading` varchar(64) NOT NULL,
  `question` text NOT NULL,
  `type` enum('check','yesno','int','text') NOT NULL default 'check',
  `required` enum('no','yes') NOT NULL default 'yes',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE = MyISAM;

-- --------------------------------------------------------

-- 
-- Table structure for table `question_answers`
-- 

CREATE TABLE `question_answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `questions_id` int(10) unsigned NOT NULL default '0',
  `answer` varchar(32) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE = MyISAM;

-- Now insert everything
INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Years School', 'Years of judging experience at a School level:', 'int', 'yes', 1);
INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Years Regional', 'Years of judging experience at a Regional level:', 'int', 'yes', 2);
INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Years National', 'Years of judging experience at a National (CWSF) level:', 'int', 'yes', 3);
INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Attending Lunch', 'Will you be attending the Judge''s Lunch?', 'yesno', 'yes', 4);
INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES ('', -1, 'judgereg', 'Willing Chair', 'Are you willing to be the lead for your judging team?', 'yesno', 'yes', 5);

