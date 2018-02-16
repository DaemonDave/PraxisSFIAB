-- phpMyAdmin SQL Dump
-- version 2.6.4-rc1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 09, 2007 at 03:21 PM
-- Server version: 4.0.24
-- PHP Version: 5.2.4
-- 
-- Database: `sfiab_temp`
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
  `excludefromac` tinyint(1) NOT NULL default '0',
  `cwsfaward` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `award_sponsors_id` (`award_sponsors_id`),
  KEY `award_types_id` (`award_types_id`),
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `award_awards`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `award_awards_projectcategories`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `award_awards_projectdivisions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `award_contacts`
-- 

CREATE TABLE `award_contacts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_sponsors_id` int(10) unsigned NOT NULL default '0',
  `salutation` varchar(8) NOT NULL default '',
  `firstname` varchar(32) NOT NULL default '',
  `lastname` varchar(32) NOT NULL default '',
  `position` varchar(64) NOT NULL default '',
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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `award_contacts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `award_prizes`
-- 

CREATE TABLE `award_prizes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned NOT NULL default '0',
  `cash` int(11) NOT NULL default '0',
  `scholarship` int(11) NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  `prize` varchar(128) NOT NULL default '',
  `number` int(11) NOT NULL default '0',
  `order` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  `excludefromac` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `award_awards_id` (`award_awards_id`),
  KEY `year` (`year`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `award_prizes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `award_sponsors`
-- 

CREATE TABLE `award_sponsors` (
  `id` int(11) NOT NULL auto_increment,
  `organization` varchar(128) NOT NULL default '',
  `phone` varchar(32) NOT NULL default '',
  `fax` varchar(32) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  `address` varchar(128) NOT NULL default '',
  `city` varchar(64) NOT NULL default '',
  `province_code` char(2) NOT NULL default '',
  `postalcode` varchar(8) NOT NULL default '',
  `notes` text NOT NULL,
  `confirmed` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `award_sponsors`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `award_types`
-- 

CREATE TABLE `award_types` (
  `id` int(10) unsigned NOT NULL default '0',
  `type` varchar(64) NOT NULL default '',
  `order` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`,`year`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `award_types`
-- 

INSERT INTO `award_types` VALUES (1, 'Divisional', 1, -1);
INSERT INTO `award_types` VALUES (2, 'Special', 2, -1);
INSERT INTO `award_types` VALUES (3, 'Interdisciplinary', 3, -1);
INSERT INTO `award_types` VALUES (4, 'Grand', 5, -1);
INSERT INTO `award_types` VALUES (5, 'Other', 4, -1);

-- --------------------------------------------------------

-- 
-- Table structure for table `committees`
-- 

CREATE TABLE `committees` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `committees`
-- 


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

-- 
-- Dumping data for table `committees_link`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `committees_members`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `config`
-- 

CREATE TABLE `config` (
  `var` varchar(64) NOT NULL default '',
  `val` text NOT NULL,
  `category` varchar(64) NOT NULL default '',
  `type` enum('','yesno','number','text','enum') NOT NULL default '',
  `type_values` tinytext NOT NULL,
  `ord` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `year` int(11) NOT NULL default '0',
  UNIQUE KEY `var` (`var`,`year`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `config`
-- 

INSERT INTO `config` VALUES ('fairname', '', 'Global', '', '', 100, 'Name of the fair', -1);
INSERT INTO `config` VALUES ('default_language', 'en', 'Global', '', '', 200, 'The default language if no language has yet been specified', -1);
INSERT INTO `config` VALUES ('minstudentsperproject', '1', 'Participant Registration', '', '', 800, 'The minimum number of students that can work on a project (usually 1)', -1);
INSERT INTO `config` VALUES ('maxstudentsperproject', '2', 'Participant Registration', '', '', 801, 'The maximum number of students that can work on a project (Usually 2)', -1);
INSERT INTO `config` VALUES ('mingrade', '7', 'Participant Registration', '', '', 600, 'The minimum school grade that can enter a project', -1);
INSERT INTO `config` VALUES ('maxgrade', '12', 'Participant Registration', '', '', 601, 'The maximum school grade that can enter a project', -1);
INSERT INTO `config` VALUES ('minage', '10', 'Participant Registration', '', '', 500, 'The minimum age of the students', -1);
INSERT INTO `config` VALUES ('maxage', '21', 'Participant Registration', '', '', 501, 'The maximum age of the students', -1);
INSERT INTO `config` VALUES ('maxmentorsperproject', '5', 'Participant Registration', '', '', 701, 'The maximum number of mentors that can help with a project', -1);
INSERT INTO `config` VALUES ('minmentorsperproject', '0', 'Participant Registration', '', '', 700, 'The minimum number of mentors that can help with a project (usually 0)', -1);
INSERT INTO `config` VALUES ('usedivisionselector', 'yes', 'Participant Registration', 'yesno', '', 1500, 'Specify whether to use the division selector flowchart questions to help decide on the division', -1);
INSERT INTO `config` VALUES ('minjudgeage', '21', 'Judge Registration', '', '', 400, 'The minimum age that a person must be in order to be a judge.', -1);
INSERT INTO `config` VALUES ('maxjudgeage', '100', 'Judge Registration', '', '', 500, 'The maximum age that a person can be in order to be a judge', -1);
INSERT INTO `config` VALUES ('participant_student_foodreq', 'yes', 'Participant Registration', 'yesno', '', 1200, 'Ask for students special food requirements. Should be ''Yes'' if you plan on providing food to the students.', -1);
INSERT INTO `config` VALUES ('regfee', '', 'Participant Registration', '', '', 300, 'Registration Fee', -1);
INSERT INTO `config` VALUES ('regfee_per', 'student', 'Participant Registration', 'enum', 'student=Student|project=Project', 400, 'Registration fee is per student, or per project?', -1);
INSERT INTO `config` VALUES ('project_num_format', 'CDN', 'Global', '', '', 600, 'C=Category ID, c=Category Shortform, D=Division ID, d=Division Shortform, N=2 digit Number', -1);
INSERT INTO `config` VALUES ('committee_publiclayout', '<tr><td>   <b>name</b></td><td>title</td><td>email</td></tr>', 'Global', '', '', 500, 'The layout (html table row) used to display the committee members on the public committee page', -1);
INSERT INTO `config` VALUES ('judges_password_expiry_days', '365', 'Judge Registration', '', '', 300, 'Judges passwords expire and they are forced to choose a new one after this many days. (0 for no expiry)', -1);
INSERT INTO `config` VALUES ('maxspecialawardsperproject', '7', 'Participant Registration', '', '', 900, 'The maximum number of self-nominated special awards a project can sign-up for', -1);
INSERT INTO `config` VALUES ('specialawardnomination', 'date', 'Participant Registration', 'enum', 'none=None|date=By Date|registration=With Registration', 1400, 'Select when students may self nominate for special awards.<br> <ul><li><b>None</b> - Students may not self-nominate for special awards. <li><b>By Date</b> - Between specific dates, specified in the "Important Dates" section. <li><b>With Registration</b> - During the same time as registration is open. </ul> ', -1);
INSERT INTO `config` VALUES ('fairmanageremail', '', 'Global', '', '', 300, 'The email address of the ''fair manager''.  Any important emails etc generated by the system will be sent here', -1);
INSERT INTO `config` VALUES ('participant_registration_type', 'open', 'Participant Registration', 'enum', 'open=Open|singlepassword=Single Password|schoolpassword=School Password|invite=Invite|openorinvite=Open or Invite', 100, 'The type of Participant Registration to use', -1);
INSERT INTO `config` VALUES ('judge_registration_type', 'open', 'Judge Registration', 'enum', 'open=Open|singlepassword=Single Password|invite=Invite', 100, 'The type of Judge Registration to use', -1);
INSERT INTO `config` VALUES ('participant_registration_singlepassword', '', 'Participant Registration', '', '', 200, 'The single password to use for participant registration if participant_registration_type is singlepassword.  Leave blank if not using singlepassword participant registration', -1);
INSERT INTO `config` VALUES ('judge_registration_singlepassword', '', 'Judge Registration', '', '', 200, 'The single password to use for judge registration if judge_registration_type is singlepassword.  Leave blank if not using singlepassword judge registration', -1);
INSERT INTO `config` VALUES ('participant_student_tshirt', 'no', 'Participant Registration', 'yesno', '', 1300, 'Ask for students their T-Shirt size', -1);
INSERT INTO `config` VALUES ('participant_project_summary_wordmax', '100', 'Participant Registration', '', '', 1100, 'The maximum number of words acceptable in the project summary', -1);
INSERT INTO `config` VALUES ('filterdivisionbycategory', 'no', 'Global', 'yesno', '', 400, 'Allows for the setup of different divisions for each category', -1);
INSERT INTO `config` VALUES ('participant_student_personal', 'yes', 'Participant Registration', 'yesno', '', 1000, 'Collect personal information about the students, such as phone number, address, gender, etc.', -1);
INSERT INTO `config` VALUES ('max_projects_per_team', '7', 'Judge Scheduler', '', '', 400, 'The maximum number of projects that a judging team can judge.', -1);
INSERT INTO `config` VALUES ('times_judged', '1', 'Judge Scheduler', '', '', 500, 'The number of times each project must be judged by different judging teams.', -1);
INSERT INTO `config` VALUES ('min_judges_per_team', '3', 'Judge Scheduler', '', '', 200, 'The minimum number of judges that can be on a judging team.', -1);
INSERT INTO `config` VALUES ('max_judges_per_team', '3', 'Judge Scheduler', '', '', 300, 'The maximum number of judges that can be on a judging team.', -1);
INSERT INTO `config` VALUES ('effort', '10000', 'Judge Scheduler', '', '', 100, 'This number controls how long and hard the judge scheduler will look for a scheduling solution.  Smaller numbers are lower effort.  100 is practically no effort, 1000 is moderate effort, 10000 is high effort.  It can take several tens of minutes to run the scheduler with high effort, but it gives a very good solution.', -1);
INSERT INTO `config` VALUES ('project_status', 'payment_pending', 'Judge Scheduler', 'enum', 'open=Open|payment_pending=Payment Pending|complete=Complete', 600, 'The status a project must have to be considered eligible for judge scheduling. ', -1);
INSERT INTO `config` VALUES ('DBVERSION', '52', 'Special', '', '', 0, '', 0);
INSERT INTO `config` VALUES ('ysf_region_id', '', 'CWSF Registration', '', '', 100, 'Your YSF Assigned Region Identifier', -1);
INSERT INTO `config` VALUES ('ysf_region_password', '', 'CWSF Registration', '', '', 200, 'Your YSF Assigned Region Password', -1);
INSERT INTO `config` VALUES ('participant_mentor', 'yes', 'Participant Registration', 'yesno', '', 1050, 'Ask for mentorship information', -1);
INSERT INTO `config` VALUES ('participant_project_title_charmax', '100', 'Participant Registration', '', '', 1150, 'The maximum number of characters acceptable in the project title (Max 255)', -1);
INSERT INTO `config` VALUES ('participant_project_table', 'yes', 'Participant Registration', 'yesno', '', 1160, 'Ask if the project requires a table', -1);
INSERT INTO `config` VALUES ('participant_project_electricity', 'yes', 'Participant Registration', 'yesno', '', 1170, 'Ask if the project requires electricity', -1);
INSERT INTO `config` VALUES ('tours_enable', 'no', 'Tours', 'yesno', '', 0, 'Enable the "tours" module.  Set to "yes" to allow participants to select tours', -1);
INSERT INTO `config` VALUES ('tours_choices_min', '1', 'Tours', '', '', 100, 'Minimum number of tours a participant must select', -1);
INSERT INTO `config` VALUES ('tours_choices_max', '3', 'Tours', '', '', 200, 'Maximum number of tours a participant may select', -1);
INSERT INTO `config` VALUES ('scheduler_enable_sa_scheduling', 'no', 'Judge Scheduler', 'yesno', '', 900, 'Allow the scheduler to automatically create a judging team for each special award, and assigned unused divisional judges to special awards.', -1);
INSERT INTO `config` VALUES ('participant_student_tshirt_cost', '0.00', 'Participant Registration', 'number', '', 1310, 'The cost of each T-Shirt. If this is non-zero, a "None" option is added to the T-Shirt size selection box, and a note is added indicating the cost of each T-Shirt', -1);
INSERT INTO `config` VALUES ('regfee_show_info', 'no', 'Participant Registration', 'yesno', '', 410, 'Show a breakdown of the total Registration Fee calculation on the main student registration page', -1);
INSERT INTO `config` VALUES ('specialawardnomination_aftersignatures', 'yes', 'Participant Registration', 'yesno', '', 1390, 'Does the signature page need to be received BEFORE students are allowed to self nominate for special awards?', -1);
INSERT INTO `config` VALUES ('judges_specialaward_enable', 'no', 'Judge Registration', 'yesno', '', 1000, 'Allow judges to specify their special award judging preferences (in addition to the divisional judging preferences)', -1);
INSERT INTO `config` VALUES ('judges_specialaward_only_enable', 'no', 'Judge Registration', 'yesno', '', 1100, 'Allow judges to specify that they are a judge for a specific special award.  If a judge selects this, it disables their divisional preference selection entirely', -1);
INSERT INTO `config` VALUES ('judges_specialaward_min', '1', 'Judge Registration', 'number', '', 1200, 'Minimum number of special awards a judge must select when specifying special award preferences', -1);
INSERT INTO `config` VALUES ('judges_specialaward_max', '6', 'Judge Registration', 'number', '', 1300, 'Maximum number of special awards a judge must select when specifying special award preferences', -1);
INSERT INTO `config` VALUES ('participant_student_pronunciation', 'no', 'Participant Registration', 'yesno', '', 1020, 'Ask the student for a pronunciation key for their name (for award ceremonies)', -1);
INSERT INTO `config` VALUES ('projects_per_special_award_judge', '20', 'Judge Scheduler', 'number', '', 1000, 'The maximum number of projects that each special awards judge can judge.', -1);

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
) TYPE=MyISAM AUTO_INCREMENT=10 ;

-- 
-- Dumping data for table `dates`
-- 

INSERT INTO `dates` VALUES (1, '0000-00-00 00:00:00', 'fairdate', 'Date of the fair', -1);
INSERT INTO `dates` VALUES (2, '0000-00-00 00:00:00', 'regopen', 'Registration system opens', -1);
INSERT INTO `dates` VALUES (3, '0000-00-00 00:00:00', 'regclose', 'Registration system closes', -1);
INSERT INTO `dates` VALUES (4, '0000-00-00 00:00:00', 'postparticipants', 'Registered participants are posted on the website', -1);
INSERT INTO `dates` VALUES (5, '0000-00-00 00:00:00', 'postwinners', 'Winners are posted on the website', -1);
INSERT INTO `dates` VALUES (6, '0000-00-00 00:00:00', 'judgeregopen', 'Judges registration opens', -1);
INSERT INTO `dates` VALUES (7, '0000-00-00 00:00:00', 'judgeregclose', 'Judges registration closes', -1);
INSERT INTO `dates` VALUES (8, '0000-00-00 00:00:00', 'specawardregopen', 'Special Awards self-nomination opens', -1);
INSERT INTO `dates` VALUES (9, '0000-00-00 00:00:00', 'specawardregclose', 'Special Awards self-nomination closes', -1);

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
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `emails`
-- 

INSERT INTO `emails` VALUES (1, 'register_participants_resend_regnum', 'Participant Registration - Resend Registration Number', 'Resend the password to the participant if they submit a ''forgot regnum'' request', 'website@sfiab.ca', 'Registration for [FAIRNAME]', 'We have received a request for the retrieval of your registration number from this email address.  Please find your existing registration number below\r\n\r\nRegistration Number: [REGNUM]\r\n', 'system');
INSERT INTO `emails` VALUES (2, 'new_participant', 'New Participant', 'Email that new participants receive when they are added to the system', 'website@sfiab.ca', 'Registration for [FAIRNAME]', 'A new registration account has been created for you.  To access your registration account, please enter the following registration number into the registration website:\r\n\r\nRegistration Number: [REGNUM]\r\n', 'system');
INSERT INTO `emails` VALUES (3, 'new_judge_invite', 'New Judge Invitation', 'This is sent to a new judge when they are invited using the invite judges administration section, only available when judge_registration_type=invite', 'registration@sfiab.ca', 'Judge Registration for [FAIRNAME]', 'You have been invited to be a judge for the [FAIRNAME].  An account has been created for you to login with and complete your information.  You can login to the judge registration site with:\r\n\r\nEmail Address: [EMAIL]\r\nPassword: [PASSWORD]\r\n\r\nYou can change your password once you login.', 'system');
INSERT INTO `emails` VALUES (4, 'register_judges_resend_password', 'Judges Registration - Resend Password', 'Resend the password to the judge if they submit a ''forgot password'' request', 'website@sfiab.ca', 'Judge Registration for [FAIRNAME]', 'We have received a request for the retrieval of your password from this email address. Please find your existing password below Judge Email Address: [EMAIL] Judge Registration Password: [PASSWORD] ', 'system');

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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `emergencycontact`
-- 


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
  `typepref` varchar(8) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `judges_catpref`
-- 

CREATE TABLE `judges_catpref` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `judges_id` int(11) NOT NULL default '0',
  `projectcategories_id` int(11) NOT NULL default '0',
  `rank` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_catpref`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_expertise`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `judges_jdiv`
-- 

CREATE TABLE `judges_jdiv` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `jdiv_id` int(11) NOT NULL default '0',
  `projectdivisions_id` int(11) NOT NULL default '0',
  `projectcategories_id` int(11) NOT NULL default '0',
  `lang` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_jdiv`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `judges_languages`
-- 

CREATE TABLE `judges_languages` (
  `judges_id` int(10) unsigned NOT NULL default '0',
  `languages_lang` char(2) NOT NULL default '',
  PRIMARY KEY  (`judges_id`,`languages_lang`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `judges_languages`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `judges_schedulerconfig`
-- 

CREATE TABLE `judges_schedulerconfig` (
  `var` varchar(64) NOT NULL default '',
  `val` text NOT NULL,
  `description` text NOT NULL,
  `year` int(11) NOT NULL default '0',
  UNIQUE KEY `var` (`var`,`year`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `judges_schedulerconfig`
-- 

INSERT INTO `judges_schedulerconfig` VALUES ('num_times_judged', '3', 'The number of times that each project must be judged (by different judging teams)', -1);
INSERT INTO `judges_schedulerconfig` VALUES ('num_timeslots', '20', 'The number of timeslots available during the judging period', -1);
INSERT INTO `judges_schedulerconfig` VALUES ('max_projects_per_team', '5', 'The maximum number of projects that a team can judge', -1);
INSERT INTO `judges_schedulerconfig` VALUES ('min_judges_per_team', '2', 'The minimum number of judges that should be on a judging team', -1);
INSERT INTO `judges_schedulerconfig` VALUES ('max_judges_per_team', '4', 'The maximum number of judges that should be on a judging team', -1);

-- --------------------------------------------------------

-- 
-- Table structure for table `judges_specialaward_sel`
-- 

CREATE TABLE `judges_specialaward_sel` (
  `id` int(11) NOT NULL auto_increment,
  `judges_id` int(11) NOT NULL default '0',
  `award_awards_id` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_specialaward_sel`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_teams`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_teams_awards_link`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_teams_link`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_teams_timeslots_link`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_teams_timeslots_projects_link`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `judges_timeslots`
-- 

CREATE TABLE `judges_timeslots` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` date NOT NULL default '0000-00-00',
  `starttime` time NOT NULL default '00:00:00',
  `endtime` time NOT NULL default '00:00:00',
  `allowdivisional` enum('no','yes') NOT NULL default 'no',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `judges_timeslots`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `judges_years`
-- 

CREATE TABLE `judges_years` (
  `judges_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`judges_id`,`year`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `judges_years`
-- 


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

-- 
-- Dumping data for table `languages`
-- 

INSERT INTO `languages` VALUES ('en', 'English', 'Y');
INSERT INTO `languages` VALUES ('fr', 'Français', 'Y');

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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `mentors`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pagetext`
-- 

CREATE TABLE `pagetext` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `textname` varchar(64) NOT NULL default '',
  `text` text NOT NULL,
  `lastupdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `textname` (`textname`,`year`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `pagetext`
-- 

INSERT INTO `pagetext` VALUES (1, 'register_participants_main_instructions', 'Once all sections are complete, please print the signature page, obtain the required signatures, and mail the signature form, along with any required registration fees to:\r\nInsert address here\r\n\r\nYour forms must be received, post marked by <b>insert date here</b>.  Late entries will not be accepted', '0000-00-00 00:00:00', -1);
INSERT INTO `pagetext` VALUES (2, 'index', 'Welcome to the online registration and management system for the fair.  Using the links on the left the public can register as a participant or register as a judge. \r\n\r\nThe committee can use the Fair Administration link to manage the fair, see who''s registered, generate reports, etc.  \r\n\r\nThe SFIAB configuration link is for the committee webmaster to manage the configuration of the Science Fair In A Box for the fair.\r\n', '0000-00-00 00:00:00', -1);

-- --------------------------------------------------------

-- 
-- Table structure for table `project_specialawards_link`
-- 

CREATE TABLE `project_specialawards_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned default '0',
  `projects_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `project_specialawards_link`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `projectcategories`
-- 

CREATE TABLE `projectcategories` (
  `id` int(10) unsigned NOT NULL default '0',
  `category` varchar(64) NOT NULL default '',
  `category_shortform` char(3) NOT NULL default '',
  `mingrade` tinyint(4) NOT NULL default '0',
  `maxgrade` tinyint(4) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`year`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `projectcategories`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `projectcategoriesdivisions_link`
-- 

CREATE TABLE `projectcategoriesdivisions_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `projectdivisions_id` int(10) unsigned NOT NULL default '0',
  `projectcategories_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `projectcategoriesdivisions_link`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `projectdivisions`
-- 

CREATE TABLE `projectdivisions` (
  `id` int(10) unsigned NOT NULL default '0',
  `division` varchar(64) NOT NULL default '',
  `division_shortform` char(3) NOT NULL default '',
  `cwsfdivisionid` int(11) default NULL,
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`year`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `projectdivisions`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `projectdivisionsselector`
-- 


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
  `cwsfdivisionid` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `summarycountok` tinyint(1) NOT NULL default '1',
  `summary` text NOT NULL,
  `year` int(11) NOT NULL default '0',
  `req_electricity` enum('no','yes') NOT NULL default 'no',
  `req_table` enum('no','yes') NOT NULL default 'yes',
  `req_special` varchar(128) NOT NULL default '',
  `language` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `projects`
-- 


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

-- 
-- Dumping data for table `projectsubdivisions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `provinces`
-- 

CREATE TABLE `provinces` (
  `code` char(2) NOT NULL default '',
  `province` varchar(32) NOT NULL default '',
  UNIQUE KEY `code` (`code`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `provinces`
-- 

INSERT INTO `provinces` VALUES ('AB', 'Alberta');
INSERT INTO `provinces` VALUES ('BC', 'British Columbia');
INSERT INTO `provinces` VALUES ('MB', 'Manitoba');
INSERT INTO `provinces` VALUES ('NB', 'New Brunswick');
INSERT INTO `provinces` VALUES ('NF', 'Newfoundland and Labrador');
INSERT INTO `provinces` VALUES ('NT', 'Northwest Territories');
INSERT INTO `provinces` VALUES ('NS', 'Nova Scotia');
INSERT INTO `provinces` VALUES ('NU', 'Nunavut');
INSERT INTO `provinces` VALUES ('ON', 'Ontario');
INSERT INTO `provinces` VALUES ('PE', 'Prince Edward Island');
INSERT INTO `provinces` VALUES ('QC', 'Québec');
INSERT INTO `provinces` VALUES ('SK', 'Saskatchewan');
INSERT INTO `provinces` VALUES ('YK', 'Yukon Territory');

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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `question_answers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `questions`
-- 

CREATE TABLE `questions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` int(11) NOT NULL default '0',
  `section` varchar(32) NOT NULL default '',
  `db_heading` varchar(64) NOT NULL default '',
  `question` text NOT NULL,
  `type` enum('check','yesno','int','text') NOT NULL default 'check',
  `required` enum('no','yes') NOT NULL default 'yes',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=16 ;

-- 
-- Dumping data for table `questions`
-- 

INSERT INTO `questions` VALUES (15, -1, 'judgereg', 'Willing Chair', 'Are you willing to be the lead for your judging team?', 'yesno', 'yes', 5);
INSERT INTO `questions` VALUES (14, -1, 'judgereg', 'Attending Lunch', 'Will you be attending the Judge''s Lunch?', 'yesno', 'yes', 4);
INSERT INTO `questions` VALUES (13, -1, 'judgereg', 'Years National', 'Years of judging experience at a National (CWSF) level:', 'int', 'yes', 3);
INSERT INTO `questions` VALUES (12, -1, 'judgereg', 'Years Regional', 'Years of judging experience at a Regional level:', 'int', 'yes', 2);
INSERT INTO `questions` VALUES (11, -1, 'judgereg', 'Years School', 'Years of judging experience at a School level:', 'int', 'yes', 1);

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
  `schools_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `registrations`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `reports`
-- 

CREATE TABLE `reports` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `desc` tinytext NOT NULL,
  `creator` varchar(128) NOT NULL default '',
  `type` enum('student','judge','award','committee','school') NOT NULL default 'student',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=35 ;

-- 
-- Dumping data for table `reports`
-- 

INSERT INTO `reports` VALUES (1, 'Student+Project -- Sorted by Last Name', 'Student Name, Project Number and Title, Category, Division short form sorted by Last Name', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (2, 'Student+Project -- Sorted by Project Number', 'Student Name, Project Number and Title, Category sorted by Project Number', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (3, 'Student+Project -- Grouped by Category', 'Student Name, Project Number and Title sorted by Last Name, grouped by Category', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (4, 'Student+Project -- School Names sorted by Last Name', 'Student Name, Project Num, School Name sorted by Last Name', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (5, 'Student+Project -- Grouped by School sorted by Last Name', 'Student Name, Project Number and Name sorted by Last Name, grouped by School Name', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (6, 'Teacher -- Name and School Info sorted by Teacher Name', 'Teacher, School Info sorted by Teacher Name', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (7, 'Teacher -- Names and Contact for each Student by School', 'Student Name, Teacher Name, Teacher Email, School Phone and Fax grouped by School Name with Addresses', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (8, 'Awards -- Special Awards Nominations Data', 'listing of special award nominations for each project, lots of data for excel so you can slice and dice (and check additional requirements)', 'Ceddy', 'student');
INSERT INTO `reports` VALUES (9, 'Check-in Lists', 'List of students and partners, project number and name, division, registration fees, tshirt size, sorted by project number, grouped by age category', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (10, 'Student+Project -- Student (and Partner) grouped by School', 'Student Pairs, Project Name/Num Grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (11, 'Student+Project -- Grouped by School sorted by Project Number', 'Individual Students, Project Name/Num Grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (12, 'Student -- T-Shirt List by School', 'Individual Students, Project Num, TShirt, Grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (13, 'Media -- Program Guide', 'Project Number, Both student names, and Project Title, grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (14, 'Projects -- Titles and Grades from each School', 'Project Name/Num, Grade Grouped by School', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (15, 'Media -- Award Winners List', 'Project Number, Student Name and Contact info, by each Award', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (16, 'Projects -- Logistical Display Requirements', 'Project Number, Students, Electricity, Table, and special needs', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (17, 'Emergency Contact Information', 'Emergency Contact Names, Relationship, and Phone Numbers for each student.', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (18, 'Student -- Grouped by Grade and Gender (YSF Stats)', 'A list of students grouped by Grade and Gender.  A quick way to total up the info for the YSF regional stats page.', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (19, 'Student+Project -- Grouped by School, 1 per page', 'Both students names grouped by school, each school list begins on a new page.', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (20, 'Judges -- Sorted by Last Name', 'A list of judge contact info, sorted by last name', 'The Grant Brothers', 'judge');
INSERT INTO `reports` VALUES (21, 'Judges -- Judging Teams', 'A list of all the judges, sorted by team number.', 'The Grant Brothers', 'judge');
INSERT INTO `reports` VALUES (22, 'Awards -- Grouped by Judging Team', 'List of each judging team, and the awards they are judging', 'The Grant Brothers', 'award');
INSERT INTO `reports` VALUES (23, 'Awards -- Judging Teams grouped by Award', 'A list of each award, and the judging teams that will assign it', 'The Grant Brothers', 'award');
INSERT INTO `reports` VALUES (24, 'Labels -- School Mailing Addresses', 'School Mailing Addresses with a blank spot for the teacher''s name, since each student apparently spells their teacher''s name differently.', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (25, 'Labels -- Student Name and Project Number', 'Just the students names and project name/number on a label.', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (26, 'Name Tags -- Students', 'Name Tags for Students', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (27, 'Name Tags -- Judges', 'Name Tags for Judges', 'The Grant Brothers', 'judge');
INSERT INTO `reports` VALUES (28, 'Name Tags -- Committee Members', 'Name Tags for Committee Members', 'The Grant Brothers', 'committee');
INSERT INTO `reports` VALUES (29, 'Labels -- Project Identification (for judging sheets)', 'Project identification labels for judging sheets', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (30, 'Labels -- Table Labels', 'Labels to go on each table, fullpage landscape version', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (31, 'Awards -- Special Awards Nominations', 'Special award nominations for each project, grouped by special award, one award per page.', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (32, 'Student+Project -- Grouped by School Board ID', 'Student Name, Project Number and Name sorted by Last Name, grouped by School Board ID', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (33, 'Certificates -- Participation Certificates', 'A certificate template for each student with name, project name, fair name, and project number at the bottom', 'The Grant Brothers', 'student');
INSERT INTO `reports` VALUES (34, 'Labels -- Table Labels (small)', 'Labels to go on each table', 'The Grant Brothers', 'student');

-- --------------------------------------------------------

-- 
-- Table structure for table `reports_items`
-- 

CREATE TABLE `reports_items` (
  `id` int(11) NOT NULL auto_increment,
  `reports_id` int(11) NOT NULL default '0',
  `type` enum('col','sort','group','distinct','option','filter') NOT NULL default 'col',
  `ord` int(11) NOT NULL default '0',
  `field` varchar(64) NOT NULL default '',
  `value` varchar(64) NOT NULL default '',
  `x` float NOT NULL default '0',
  `y` float NOT NULL default '0',
  `w` float NOT NULL default '0',
  `h` float NOT NULL default '0',
  `lines` float NOT NULL default '0',
  `face` enum('','bold') NOT NULL default '',
  `align` enum('center','left','right') NOT NULL default 'center',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=377 ;

-- 
-- Dumping data for table `reports_items`
-- 

INSERT INTO `reports_items` VALUES (1, 1, 'col', 5, 'grade', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (2, 1, 'col', 4, 'div', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (3, 1, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (4, 2, 'col', 3, 'category', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (5, 2, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (6, 2, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (7, 3, 'col', 3, 'div', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (8, 4, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (9, 3, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (10, 3, 'group', 0, 'category', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (11, 4, 'col', 3, 'grade', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (12, 4, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (13, 4, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (14, 4, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (15, 5, 'col', 3, 'category', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (16, 5, 'col', 4, 'div', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (17, 5, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (18, 5, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (19, 6, 'col', 2, 'school_phone', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (20, 6, 'col', 1, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (21, 6, 'col', 0, 'teacher', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (22, 6, 'sort', 0, 'teacher', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (23, 6, 'distinct', 0, 'teacher', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (24, 11, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (25, 11, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (26, 11, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (27, 7, 'col', 5, 'school_fax', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (28, 7, 'col', 4, 'school_phone', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (29, 7, 'col', 3, 'teacheremail', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (30, 7, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (31, 9, 'col', 6, 'div', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (32, 9, 'col', 5, 'tshirt', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (33, 9, 'col', 3, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (34, 9, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (35, 9, 'group', 0, 'category', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (36, 9, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (37, 10, 'col', 2, 'partner', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (38, 10, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (39, 10, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (40, 10, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (41, 10, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (42, 10, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (43, 2, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (44, 11, 'col', 4, 'div', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (45, 11, 'col', 3, 'category', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (46, 11, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (47, 11, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (48, 12, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (49, 12, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (50, 12, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (51, 12, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (52, 13, 'col', 1, 'bothnames', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (53, 13, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (54, 13, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (55, 13, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (56, 13, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (57, 14, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (58, 14, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (59, 14, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (60, 14, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (61, 15, 'col', 5, 'postal', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (62, 15, 'col', 4, 'province', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (63, 15, 'col', 3, 'city', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (64, 15, 'col', 2, 'address', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (65, 15, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (66, 15, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (67, 15, 'group', 0, 'awards', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (68, 1, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (69, 1, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (70, 1, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (71, 1, 'col', 3, 'category', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (72, 1, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (73, 3, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (74, 3, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (75, 3, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (76, 9, 'col', 4, 'partner', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (77, 9, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (78, 9, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (79, 9, 'col', 1, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (80, 9, 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (81, 5, 'col', 5, 'grade', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (82, 5, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (83, 5, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (84, 3, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (85, 4, 'col', 2, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (86, 7, 'col', 2, 'teacher', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (87, 7, 'group', 1, 'schooladdr', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (88, 7, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (89, 11, 'col', 5, 'grade', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (90, 2, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (91, 2, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (92, 2, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (93, 12, 'col', 2, 'tshirt', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (94, 12, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (95, 7, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (96, 12, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (97, 12, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (98, 7, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (99, 7, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (100, 7, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (101, 15, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (102, 15, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (103, 15, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (104, 15, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (105, 13, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (106, 13, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (107, 13, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (108, 13, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (109, 14, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (110, 14, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (111, 14, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (112, 14, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (113, 16, 'col', 3, 'req_table', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (114, 16, 'col', 2, 'req_elec', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (115, 16, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (116, 16, 'group', 0, 'category', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (117, 16, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (118, 16, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (119, 16, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (120, 16, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (121, 16, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (122, 17, 'col', 4, 'emerg_phone', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (123, 17, 'col', 3, 'emerg_relation', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (124, 17, 'col', 2, 'emerg_name', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (125, 17, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (126, 7, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (127, 14, 'col', 2, 'grade', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (128, 17, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (129, 6, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (130, 6, 'col', 3, 'school_fax', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (131, 17, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (132, 17, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (133, 6, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (134, 6, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (135, 9, 'col', 0, 'paid', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (136, 1, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (137, 2, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (138, 3, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (139, 3, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (140, 4, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (141, 4, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (142, 10, 'col', 3, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (143, 10, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (144, 10, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (145, 10, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (146, 5, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (147, 5, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (148, 5, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (149, 5, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (150, 11, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (151, 11, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (152, 11, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (153, 18, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (154, 18, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (155, 18, 'col', 2, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (156, 18, 'group', 0, 'grade', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (157, 18, 'group', 1, 'gender', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (158, 18, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (159, 18, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (160, 18, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (161, 18, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (162, 3, 'col', 4, 'grade', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (163, 1, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (164, 2, 'col', 4, 'div', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (165, 2, 'col', 5, 'grade', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (166, 19, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (167, 19, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (168, 19, 'col', 2, 'bothnames', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (169, 19, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (170, 19, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (171, 19, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (172, 19, 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (173, 19, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (174, 21, 'sort', 1, 'namefl', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (175, 21, 'col', 1, 'team', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (176, 21, 'col', 2, 'captain', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (177, 21, 'col', 3, 'namefl', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (178, 21, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (179, 20, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (180, 20, 'col', 4, 'complete', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (181, 20, 'col', 0, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (182, 20, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (183, 20, 'sort', 0, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (184, 20, 'col', 1, 'email', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (185, 20, 'col', 2, 'phone_home', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (186, 20, 'col', 3, 'phone_work', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (187, 20, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (188, 21, 'sort', 0, 'teamnum', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (189, 21, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (190, 21, 'col', 0, 'teamnum', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (191, 21, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (192, 22, 'col', 1, 'type', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (193, 22, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (194, 22, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (195, 22, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (196, 22, 'group', 0, 'judgeteamnum', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (197, 22, 'col', 0, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (198, 22, 'group', 1, 'judgeteamname', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (199, 23, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (200, 23, 'col', 1, 'judgeteamname', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (201, 23, 'group', 0, 'type', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (202, 23, 'sort', 0, 'judgeteamnum', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (203, 23, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (204, 23, 'group', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (205, 23, 'col', 0, 'judgeteamnum', '', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (206, 23, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center');
INSERT INTO `reports_items` VALUES (207, 16, 'col', 4, 'req_special', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (208, 16, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (209, 16, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (210, 16, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (211, 16, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (212, 16, 'option', 6, 'stock', 'letter', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (213, 17, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (214, 25, 'option', 6, 'stock', '5964', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (215, 25, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (216, 24, 'col', 2, 'school_city_prov', '', 5, 50, 95, 8, 1, '', 'left');
INSERT INTO `reports_items` VALUES (217, 24, 'col', 1, 'school_address', '', 5, 40, 95, 16, 2, '', 'left');
INSERT INTO `reports_items` VALUES (218, 24, 'col', 0, 'school', '', 5, 5, 95, 16, 2, '', 'left');
INSERT INTO `reports_items` VALUES (219, 24, 'option', 6, 'stock', '5964', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (220, 24, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (221, 24, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (222, 24, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (223, 24, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (224, 24, 'sort', 0, 'school', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (225, 25, 'col', 4, 'school', '', 1, 90, 98, 5, 1, '', 'center');
INSERT INTO `reports_items` VALUES (226, 24, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (227, 24, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (228, 25, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (229, 25, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (230, 25, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (231, 25, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (232, 25, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (233, 25, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (234, 8, 'col', 7, 'nom_awards', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (235, 25, 'col', 3, 'categorydivision', '', 1, 80, 98, 12, 2, '', 'center');
INSERT INTO `reports_items` VALUES (236, 25, 'col', 2, 'pn', '', 1, 68, 98, 8, 1, '', 'center');
INSERT INTO `reports_items` VALUES (237, 27, 'sort', 0, 'namefl', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (238, 25, 'col', 1, 'title', '', 1, 35, 98, 27, 3, '', 'center');
INSERT INTO `reports_items` VALUES (239, 25, 'col', 0, 'namefl', '', 5, 5, 90, 28, 2, '', 'center');
INSERT INTO `reports_items` VALUES (240, 26, 'col', 2, 'categorydivision', '', 1, 70, 98, 14, 2, '', 'center');
INSERT INTO `reports_items` VALUES (241, 26, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (242, 26, 'option', 6, 'stock', 'nametag', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (243, 26, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (244, 26, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (245, 26, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (246, 26, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (247, 26, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (248, 26, 'col', 1, 'title', '', 1, 35, 98, 27, 3, '', 'center');
INSERT INTO `reports_items` VALUES (249, 26, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (250, 26, 'col', 0, 'namefl', '', 5, 5, 90, 28, 2, 'bold', 'center');
INSERT INTO `reports_items` VALUES (251, 27, 'col', 1, 'static_text', 'Judge', 1, 40, 98, 10, 1, '', 'center');
INSERT INTO `reports_items` VALUES (252, 27, 'col', 0, 'namefl', '', 1, 15, 98, 24, 2, 'bold', 'center');
INSERT INTO `reports_items` VALUES (253, 27, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (254, 27, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (255, 27, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (256, 27, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (257, 27, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (258, 28, 'col', 1, 'static_text', 'Committee', 1, 40, 98, 10, 1, '', 'center');
INSERT INTO `reports_items` VALUES (259, 28, 'col', 0, 'name', '', 1, 15, 98, 24, 2, 'bold', 'center');
INSERT INTO `reports_items` VALUES (260, 28, 'sort', 0, 'name', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (261, 28, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (262, 28, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (263, 28, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (264, 28, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (265, 28, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (266, 30, 'option', 6, 'stock', 'fullpage_landscape', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (267, 29, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (268, 29, 'col', 1, 'categorydivision', '', 1, 30, 98, 18, 1, '', 'left');
INSERT INTO `reports_items` VALUES (269, 8, 'col', 6, 'school_city', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (270, 29, 'col', 0, 'pn', '', 1, 5, 98, 20, 1, '', 'left');
INSERT INTO `reports_items` VALUES (271, 29, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (272, 29, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (273, 29, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (274, 29, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (275, 29, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (276, 29, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (277, 30, 'col', 3, 'categorydivision', '', 1, 85, 98, 5, 1, '', 'center');
INSERT INTO `reports_items` VALUES (278, 30, 'col', 2, 'pn', '', 1, 20, 98, 35, 1, '', 'center');
INSERT INTO `reports_items` VALUES (279, 30, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (280, 30, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (281, 30, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (282, 30, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (283, 30, 'col', 1, 'title', '', 1, 5, 98, 15, 3, '', 'center');
INSERT INTO `reports_items` VALUES (284, 30, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (285, 31, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (286, 8, 'col', 5, 'birthdate', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (287, 8, 'col', 4, 'gender', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (288, 31, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (289, 31, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (290, 31, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (291, 31, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (292, 31, 'col', 5, 'age', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (293, 31, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (294, 17, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (295, 31, 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (296, 31, 'col', 4, 'gender', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (297, 31, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (298, 31, 'col', 3, 'grade', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (299, 31, 'group', 0, 'nom_awards', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (300, 32, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (301, 32, 'col', 4, 'school', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (302, 32, 'col', 3, 'grade', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (303, 32, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (304, 32, 'group', 0, 'school_board', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (305, 32, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (306, 32, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (307, 32, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (308, 32, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (309, 32, 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (310, 32, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (311, 32, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (312, 32, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (313, 32, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (314, 17, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (315, 17, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (316, 17, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (317, 17, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (318, 31, 'col', 2, 'namefl', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (319, 31, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (320, 31, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (321, 33, 'col', 5, 'static_text', 'Chair', 5, 85, 30, 2, 1, '', 'center');
INSERT INTO `reports_items` VALUES (322, 33, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (323, 8, 'col', 2, 'namefl', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (324, 8, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (325, 33, 'col', 6, 'static_text', 'Chief Judge', 60, 85, 30, 2, 1, '', 'center');
INSERT INTO `reports_items` VALUES (326, 33, 'col', 4, 'fair_year', '', 5, 25, 30, 6, 1, '', 'center');
INSERT INTO `reports_items` VALUES (327, 33, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (328, 33, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (329, 33, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (330, 33, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (331, 33, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (332, 33, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (333, 33, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (334, 33, 'col', 3, 'pn', '', 3, 97, 94, 1, 1, '', 'right');
INSERT INTO `reports_items` VALUES (335, 33, 'col', 0, 'fair_name', '', 1, 36, 98, 4, 1, '', 'center');
INSERT INTO `reports_items` VALUES (336, 33, 'col', 1, 'namefl', '', 1, 56, 98, 8, 2, '', 'center');
INSERT INTO `reports_items` VALUES (337, 33, 'col', 2, 'title', '', 1, 65, 98, 12, 3, '', 'center');
INSERT INTO `reports_items` VALUES (338, 24, 'col', 3, 'school_postal', '', 5, 60, 95, 8, 1, '', 'left');
INSERT INTO `reports_items` VALUES (339, 30, 'col', 0, 'bothnames', '', 1, 70, 98, 10, 2, '', 'center');
INSERT INTO `reports_items` VALUES (340, 30, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (341, 26, 'col', 3, 'pn', '', 1, 85, 98, 8, 1, '', 'center');
INSERT INTO `reports_items` VALUES (342, 27, 'col', 2, 'organization', '', 1, 70, 98, 16, 2, '', 'center');
INSERT INTO `reports_items` VALUES (343, 27, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (344, 27, 'option', 6, 'stock', 'nametag', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (345, 27, 'filter', 0, 'complete', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (346, 28, 'col', 2, 'organization', '', 1, 70, 98, 16, 2, '', 'center');
INSERT INTO `reports_items` VALUES (347, 28, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (348, 28, 'option', 6, 'stock', 'nametag', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (349, 29, 'col', 2, 'title', '', 1, 55, 98, 40, 2, '', 'left');
INSERT INTO `reports_items` VALUES (350, 29, 'option', 6, 'stock', '5961', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (351, 30, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (352, 30, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (353, 8, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (354, 8, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (355, 8, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (356, 8, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (357, 8, 'col', 3, 'grade', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (358, 8, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (359, 8, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (360, 8, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (361, 8, 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (362, 8, 'sort', 0, 'nom_awards', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (363, 8, 'sort', 1, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (364, 34, 'col', 3, 'categorydivision', '', 1, 85, 98, 7, 1, '', 'center');
INSERT INTO `reports_items` VALUES (365, 34, 'col', 2, 'pn', '', 1, 20, 98, 35, 1, '', 'center');
INSERT INTO `reports_items` VALUES (366, 34, 'col', 1, 'title', '', 1, 5, 98, 24, 3, '', 'center');
INSERT INTO `reports_items` VALUES (367, 34, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (368, 34, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (369, 34, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (370, 34, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (371, 34, 'col', 0, 'bothnames', '', 1, 70, 98, 14, 2, '', 'center');
INSERT INTO `reports_items` VALUES (372, 34, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (373, 34, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (374, 34, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (375, 34, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', '');
INSERT INTO `reports_items` VALUES (376, 34, 'option', 6, 'stock', '5964', 0, 0, 0, 0, 0, '', '');

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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `safety`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `safetyquestions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `schools`
-- 

CREATE TABLE `schools` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `school` varchar(64) NOT NULL default '',
  `schoollang` char(2) NOT NULL default '',
  `schoollevel` varchar(32) NOT NULL default '',
  `board` varchar(64) NOT NULL default '',
  `district` varchar(64) NOT NULL default '',
  `phone` varchar(16) NOT NULL default '',
  `fax` varchar(16) NOT NULL default '',
  `address` varchar(64) NOT NULL default '',
  `city` varchar(32) NOT NULL default '',
  `province_code` char(2) NOT NULL default '',
  `postalcode` varchar(7) NOT NULL default '',
  `principal` varchar(64) NOT NULL default '',
  `schoolemail` varchar(128) NOT NULL default '',
  `sciencehead` varchar(64) NOT NULL default '',
  `scienceheademail` varchar(128) NOT NULL default '',
  `scienceheadphone` varchar(32) NOT NULL default '',
  `accesscode` varchar(32) NOT NULL default '',
  `year` int(10) unsigned NOT NULL default '0',
  `lastlogin` datetime default NULL,
  `junior` tinyint(4) NOT NULL default '0',
  `intermediate` tinyint(4) NOT NULL default '0',
  `senior` tinyint(4) NOT NULL default '0',
  `registration_password` varchar(32) NOT NULL default '',
  `projectlimit` int(10) NOT NULL default '0',
  `projectlimitper` enum('total','agecategory') NOT NULL default 'total',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `schools`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `signaturepage`
-- 

CREATE TABLE `signaturepage` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  `use` tinyint(4) NOT NULL default '1',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `signaturepage`
-- 

INSERT INTO `signaturepage` VALUES (1, 'exhibitordeclaration', 1, 'The following section is to be read and signed by the exhibitor(s).\r\n\r\nI/We certify that:\r\n - The preparation of this project is mainly my/our own work.\r\n - I/We have read the rules and regulations and agree to abide by them.\r\n - I/We agree agree that the decision of the judges will be final.');
INSERT INTO `signaturepage` VALUES (2, 'parentdeclaration', 1, 'The following is to be read and signed by the exhibitor(s) parent(s)/guardian(s).\r\nAs a parent/guardian I certify to the best of my knowledge and believe the information contained in this application is correct, and the project is the work of the student.  I also understand that the material used in the project is the responsibility of the student and that neither the school, the teacher, nor the regional fair can be held responsible for loss, damage, or theft, however caused. I further understand that all exhibits entered must be left on display until the end of the Fair. If my son/daughter does not remove the exhibit at the end of the Fair, the fair organizers or the owner of the exhibition hall cannot be responsible for the disposal of the exhibit.\r\n\r\nIf my son/daughter is awarded the honour of having his/her exhibit chosen for presentation at the Canada-Wide Science Fair, I consent to having him/her journey to the Fair, and will not hold the Fair responsible for any accident or mishap to the student or the exhibit.');
INSERT INTO `signaturepage` VALUES (3, 'teacherdeclaration', 0, 'The following section is to be read and signed by the teacher.\r\n\r\nI certify that:\r\n - The preparation of this project is mainly the student(s)'' own work.\r\n - The student(s) have read the rules and regulations and agree to abide by them.\r\n - I agree that the decision of the judges will be final.');
INSERT INTO `signaturepage` VALUES (4, 'postamble', 0, 'Please send the signed signature form and any required payment to: \n\n[Insert Address Here]');

-- --------------------------------------------------------

-- 
-- Table structure for table `students`
-- 

CREATE TABLE `students` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `firstname` varchar(64) NOT NULL default '',
  `lastname` varchar(64) NOT NULL default '',
  `pronunciation` varchar(64) NOT NULL default '',
  `sex` enum('male','female') default NULL,
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
  `tshirt` varchar(32) NOT NULL default 'medium',
  `medicalalert` varchar(255) NOT NULL default '',
  `foodreq` varchar(255) NOT NULL default '',
  `teachername` varchar(64) NOT NULL default '',
  `teacheremail` varchar(128) NOT NULL default '',
  `webfirst` enum('no','yes') NOT NULL default 'yes',
  `weblast` enum('no','yes') NOT NULL default 'yes',
  `webphoto` enum('no','yes') NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `students`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tours`
-- 

CREATE TABLE `tours` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` int(10) unsigned NOT NULL default '0',
  `name` tinytext NOT NULL,
  `description` text NOT NULL,
  `capacity` int(11) NOT NULL default '0',
  `grade_min` int(11) NOT NULL default '7',
  `grade_max` int(11) NOT NULL default '12',
  `contact` tinytext NOT NULL,
  `location` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `tours`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tours_choice`
-- 

CREATE TABLE `tours_choice` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `students_id` int(10) unsigned NOT NULL default '0',
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `tour_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  `rank` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `tours_choice`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `translations`
-- 

CREATE TABLE `translations` (
  `lang` char(2) NOT NULL default '',
  `strmd5` varchar(32) NOT NULL default '',
  `str` text NOT NULL,
  `val` text NOT NULL,
  `argsdesc` text,
  PRIMARY KEY  (`strmd5`),
  UNIQUE KEY `lang` (`lang`,`strmd5`)
) TYPE=MyISAM;

-- 
-- Dumping data for table `translations`
-- 


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
) TYPE=MyISAM AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `winners`
-- 

