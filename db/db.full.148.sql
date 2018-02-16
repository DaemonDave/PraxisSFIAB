-- phpMyAdmin SQL Dump
-- version 3.2.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 22, 2009 at 12:56 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.6-3ubuntu4.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sfiab`
--

-- --------------------------------------------------------

--
-- Table structure for table `award_awards`
--

CREATE TABLE IF NOT EXISTS `award_awards` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sponsors_id` int(10) unsigned NOT NULL default '0',
  `award_types_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `criteria` text NOT NULL,
  `description` text NOT NULL,
  `presenter` varchar(128) NOT NULL default '',
  `order` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  `excludefromac` tinyint(1) NOT NULL default '0',
  `cwsfaward` tinyint(1) NOT NULL default '0',
  `self_nominate` enum('yes','no') NOT NULL default 'yes',
  `schedule_judges` enum('yes','no') NOT NULL default 'yes',
  `external_identifier` varchar(32) default NULL,
  `external_postback` varchar(128) default NULL,
  `external_additional_materials` tinyint(1) NOT NULL,
  `external_register_winners` tinyint(1) NOT NULL,
  `award_source_fairs_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `award_sponsors_id` (`sponsors_id`),
  KEY `award_types_id` (`award_types_id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `award_awards`
--


-- --------------------------------------------------------

--
-- Table structure for table `award_awards_projectcategories`
--

CREATE TABLE IF NOT EXISTS `award_awards_projectcategories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned NOT NULL default '0',
  `projectcategories_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `year` (`year`),
  KEY `award_awards_id` (`award_awards_id`),
  KEY `projectcategories_id` (`projectcategories_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `award_awards_projectcategories`
--


-- --------------------------------------------------------

--
-- Table structure for table `award_awards_projectdivisions`
--

CREATE TABLE IF NOT EXISTS `award_awards_projectdivisions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned NOT NULL default '0',
  `projectdivisions_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `award_awards_id` (`award_awards_id`),
  KEY `projectdivisions_id` (`projectdivisions_id`),
  KEY `year` (`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `award_awards_projectdivisions`
--


-- --------------------------------------------------------

--
-- Table structure for table `award_prizes`
--

CREATE TABLE IF NOT EXISTS `award_prizes` (
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
  `trophystudentkeeper` tinyint(1) NOT NULL default '0',
  `trophystudentreturn` tinyint(1) NOT NULL default '0',
  `trophyschoolkeeper` tinyint(1) NOT NULL default '0',
  `trophyschoolreturn` tinyint(1) NOT NULL default '0',
  `external_identifier` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `award_awards_id` (`award_awards_id`),
  KEY `year` (`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `award_prizes`
--


-- --------------------------------------------------------

--
-- Table structure for table `award_sources`
--

CREATE TABLE IF NOT EXISTS `award_sources` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `url` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `enabled` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `award_sources`
--

INSERT INTO `award_sources` (`id`, `name`, `url`, `website`, `username`, `password`, `enabled`) VALUES
(1, 'Sci-Tech Ontario', 'http://www.scitechontario.org/awarddownloader/index.php', 'http://www.scitechontario.org/awarddownloader/help.php', '', '', 'no'),
(2, 'Youth Science Foundation', 'https://secure.ysf-fsj.ca/awarddownloader/index.php', 'http://apps.ysf-fsj.ca/awarddownloader/help.php', '', '', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `award_types`
--

CREATE TABLE IF NOT EXISTS `award_types` (
  `id` int(10) unsigned NOT NULL default '0',
  `type` varchar(64) NOT NULL default '',
  `order` int(11) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  UNIQUE KEY `id` (`id`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `award_types`
--

INSERT INTO `award_types` (`id`, `type`, `order`, `year`) VALUES
(1, 'Divisional', 1, -1),
(2, 'Special', 2, -1),
(3, 'Interdisciplinary', 3, -1),
(4, 'Grand', 5, -1),
(5, 'Other', 4, -1);

-- --------------------------------------------------------

--
-- Table structure for table `cms`
--

CREATE TABLE IF NOT EXISTS `cms` (
  `id` int(11) NOT NULL auto_increment,
  `filename` varchar(128) NOT NULL,
  `dt` datetime NOT NULL,
  `lang` varchar(2) NOT NULL,
  `title` varchar(128) NOT NULL,
  `text` text NOT NULL,
  `showlogo` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `cms`
--

INSERT INTO `cms` (`id`, `filename`, `dt`, `lang`, `title`, `text`, `showlogo`) VALUES
(1, 'index.html', '0000-00-00 00:00:00', 'en', '', 'Welcome to the online registration and management system for the fair.  Using the links on the left the public can register as a participant or register as a judge. \r\n\r\nThe committee can use the Fair Administration link to manage the fair, see who''s registered, generate reports, etc.  \r\n\r\nThe SFIAB configuration link is for the committee webmaster to manage the configuration of the Science Fair In A Box for the fair.\r\n If you want to find resources to help young scientists achieve better then consult the Open Science Fair Wiki. <a href="http://seab-sciencefair.com/mediawiki/index.php/Main_Page" title="Praxis Society Open Science Fair Wikimedia">Praxis Society Open Science Fair Wikimedia</a> ', -1);

-- --------------------------------------------------------

--
-- Table structure for table `committees`
--

CREATE TABLE IF NOT EXISTS `committees` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `committees`
--


-- --------------------------------------------------------

--
-- Table structure for table `committees_link`
--

CREATE TABLE IF NOT EXISTS `committees_link` (
  `committees_id` int(10) unsigned NOT NULL default '0',
  `users_uid` int(11) NOT NULL default '0',
  `title` varchar(128) NOT NULL default '',
  `ord` tinyint(3) unsigned NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `committees_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `var` varchar(64) NOT NULL default '',
  `val` text NOT NULL,
  `category` varchar(64) NOT NULL default '',
  `type` enum('','yesno','number','text','enum','multisel','language') NOT NULL,
  `type_values` tinytext NOT NULL,
  `ord` int(11) NOT NULL default '0',
  `description` text NOT NULL,
  `year` int(11) NOT NULL default '0',
  UNIQUE KEY `var` (`var`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`var`, `val`, `category`, `type`, `type_values`, `ord`, `description`, `year`) VALUES
('fairname', '', 'Global', '', '', 100, 'Name of the fair', -1),
('default_language', 'en', 'Global', 'language', '', 200, 'The default language if no language has yet been specified', -1),
('minstudentsperproject', '1', 'Participant Registration', 'number', '', 1200, 'The minimum number of students that can work on a project (usually 1)', -1),
('maxstudentsperproject', '2', 'Participant Registration', 'number', '', 1300, 'The maximum number of students that can work on a project (Usually 2)', -1),
('mingrade', '7', 'Participant Registration', 'number', '', 800, 'The minimum school grade that can enter a project', -1),
('maxgrade', '12', 'Participant Registration', 'number', '', 900, 'The maximum school grade that can enter a project', -1),
('minage', '10', 'Participant Registration', 'number', '', 600, 'The minimum age of the students', -1),
('maxage', '21', 'Participant Registration', 'number', '', 700, 'The maximum age of the students', -1),
('maxmentorsperproject', '5', 'Participant Registration', 'number', '', 1100, 'The maximum number of mentors that can help with a project', -1),
('minmentorsperproject', '0', 'Participant Registration', 'number', '', 1000, 'The minimum number of mentors that can help with a project (usually 0)', -1),
('usedivisionselector', 'yes', 'Participant Registration', 'yesno', '', 3000, 'Specify whether to use the division selector flowchart questions to help decide on the division', -1),
('minjudgeage', '21', 'Judge Registration', '', '', 400, 'The minimum age that a person must be in order to be a judge.', -1),
('maxjudgeage', '100', 'Judge Registration', '', '', 500, 'The maximum age that a person can be in order to be a judge', -1),
('participant_student_foodreq', 'yes', 'Participant Registration', 'yesno', '', 2500, 'Ask for students special food requirements. Should be ''Yes'' if you plan on providing food to the students.', -1),
('regfee', '', 'Participant Registration', 'number', '', 300, 'Registration Fee', -1),
('regfee_per', 'student', 'Participant Registration', 'enum', 'student=Student|project=Project', 400, 'Registration fee is per student, or per project?', -1),
('project_num_format', 'CDN', 'Global', '', '', 600, 'Project Numbering Format: C=Category ID, c=Category shortform, D=Division ID, d=Division shortform, N, N1, N2, ..., N9=intra division digit sequence number, zero padded to 1-9 digits, or 2 digits if just N is used. X, X1, X2, ..., N9=global sequence number, zero padded to 1-9 digits, or 3 digits if just X is used.', -1),
('committee_publiclayout', '<tr><td>   <b>name</b></td><td>title</td><td>email</td></tr>', 'Global', '', '', 500, 'The layout (html table row) used to display the committee members on the public committee page', -1),
('judges_password_expiry_days', '365', 'Judge Registration', '', '', 300, 'Judges passwords expire and they are forced to choose a new one after this many days. (0 for no expiry)', -1),
('maxspecialawardsperproject', '7', 'Participant Registration', 'number', '', 1400, 'The maximum number of self-nominated special awards a project can sign-up for', -1),
('specialawardnomination', 'date', 'Participant Registration', 'enum', 'none=None|date=By Date|registration=With Registration', 2900, 'Select when students may self nominate for special awards.<br> <ul><li><b>None</b> - Students may not self-nominate for special awards. <li><b>By Date</b> - Between specific dates, specified in the "Important Dates" section. <li><b>With Registration</b> - During the same time as registration is open. </ul> ', -1),
('fairmanageremail', '', 'Global', '', '', 300, 'The email address of the ''fair manager''.  Any important emails etc generated by the system will be sent here', -1),
('participant_registration_type', 'open', 'Participant Registration', 'enum', 'open=Open|singlepassword=Single Password|schoolpassword=School Password|invite=Invite|openorinvite=Open or Invite', 100, 'The type of Participant Registration to use', -1),
('judge_registration_type', 'open', 'Judge Registration', 'enum', 'open=Open|singlepassword=Single Password|invite=Invite', 100, 'The type of Judge Registration to use', -1),
('participant_registration_singlepassword', '', 'Participant Registration', '', '', 200, 'The single password to use for participant registration if participant_registration_type is singlepassword.  Leave blank if not using singlepassword participant registration', -1),
('judge_registration_singlepassword', '', 'Judge Registration', '', '', 200, 'The single password to use for judge registration if judge_registration_type is singlepassword.  Leave blank if not using singlepassword judge registration', -1),
('participant_student_tshirt', 'no', 'Participant Registration', 'yesno', '', 2600, 'Ask for students their T-Shirt size', -1),
('participant_project_summary_wordmax', '100', 'Participant Registration', 'number', '', 1800, 'The maximum number of words acceptable in the project summary', -1),
('filterdivisionbycategory', 'no', 'Global', 'yesno', '', 400, 'Allows for the setup of different divisions for each category', -1),
('participant_student_personal', 'yes', 'Participant Registration', 'yesno', '', 1500, 'Collect personal information about the students, such as phone number, address, gender, etc.', -1),
('max_projects_per_team', '7', 'Judge Scheduler', '', '', 400, 'The maximum number of projects that a judging team can judge.', -1),
('times_judged', '1', 'Judge Scheduler', '', '', 500, 'The number of times each project must be judged by different judging teams.', -1),
('min_judges_per_team', '3', 'Judge Scheduler', '', '', 200, 'The minimum number of judges that can be on a judging team.', -1),
('max_judges_per_team', '3', 'Judge Scheduler', '', '', 300, 'The maximum number of judges that can be on a judging team.', -1),
('effort', '10000', 'Judge Scheduler', 'enum', '100=Low|1000=Medium|10000=High', 100, 'This controls how long and hard the judge scheduler will look for a scheduling solution. Low effort will finish almost instantly but give a very poor result. High effort can take several tens of minutes to run, but it gives a very good solution.', -1),
('project_status', 'payment_pending', 'Judge Scheduler', 'enum', 'open=Open|payment_pending=Payment Pending|complete=Complete', 600, 'The status a project must have to be considered eligible for judge scheduling. ', -1),
('DBVERSION', '148', 'Special', '', '', 0, '', 0),
('fiscal_yearend', '', 'Fundraising', 'text', '', 200, 'Your organization''s fiscal year end. Specified in format MM-DD. Must be set in order for the Fundraising Module to function.', -1),
('participant_mentor', 'yes', 'Participant Registration', 'yesno', '', 1700, 'Ask for mentorship information', -1),
('participant_project_title_charmax', '100', 'Participant Registration', 'number', '', 2000, 'The maximum number of characters acceptable in the project title (Max 255)', -1),
('participant_project_table', 'yes', 'Participant Registration', 'yesno', '', 2300, 'Ask if the project requires a table', -1),
('participant_project_electricity', 'yes', 'Participant Registration', 'yesno', '', 2400, 'Ask if the project requires electricity', -1),
('tours_enable', 'no', 'Tours', 'yesno', '', 0, 'Enable the "tours" module.  Set to "yes" to allow participants to select tours', -1),
('tours_choices_min', '1', 'Tours', '', '', 100, 'Minimum number of tours a participant must select', -1),
('tours_choices_max', '3', 'Tours', '', '', 200, 'Maximum number of tours a participant may select', -1),
('scheduler_enable_sa_scheduling', 'no', 'Judge Scheduler', 'yesno', '', 900, 'Allow the scheduler to automatically create a judging team for each special award, and assigned unused divisional judges to special awards.', -1),
('participant_student_tshirt_cost', '0.00', 'Participant Registration', 'number', '', 2700, 'The cost of each T-Shirt. If this is non-zero, a "None" option is added to the T-Shirt size selection box, and a note is added indicating the cost of each T-Shirt', -1),
('regfee_show_info', 'no', 'Participant Registration', 'yesno', '', 500, 'Show a breakdown of the total Registration Fee calculation on the main student registration page', -1),
('specialawardnomination_aftersignatures', 'yes', 'Participant Registration', 'yesno', '', 2800, 'Does the signature page need to be received BEFORE students are allowed to self nominate for special awards?', -1),
('judges_specialaward_enable', 'no', 'Judge Registration', 'yesno', '', 1000, 'Allow judges to specify their special award judging preferences (in addition to the divisional judging preferences)', -1),
('judges_specialaward_only_enable', 'no', 'Judge Registration', 'yesno', '', 1100, 'Allow judges to specify that they are a judge for a specific special award.  If a judge selects this, it disables their divisional preference selection entirely', -1),
('judges_specialaward_min', '1', 'Judge Registration', 'number', '', 1200, 'Minimum number of special awards a judge must select when specifying special award preferences', -1),
('judges_specialaward_max', '6', 'Judge Registration', 'number', '', 1300, 'Maximum number of special awards a judge must select when specifying special award preferences', -1),
('participant_student_pronunciation', 'no', 'Participant Registration', 'yesno', '', 1600, 'Ask the student for a pronunciation key for their name (for award ceremonies)', -1),
('projects_per_special_award_judge', '20', 'Judge Scheduler', 'number', '', 1000, 'The maximum number of projects that each special awards judge can judge.', -1),
('volunteer_password_expiry_days', '365', 'Volunteer Registration', 'number', '', 300, 'Volunteer passwords expire and they are forced to choose a new one after this many days. (0 for no expiry)', -1),
('volunteer_enable', 'no', 'Volunteer Registration', 'yesno', '', 100, 'Allow Volunteers to create accounts and sign up for volunteer positions (positions are configurable in the admin section)', -1),
('volunteer_personal_fields', 'phonehome,phonecell,org', 'Volunteer Registration', 'multisel', 'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province|firstaid=First Aid and CPR', 500, 'Personal Information to ask for on the Volunteer personal information page (in addition to Name and Email)', -1),
('volunteer_personal_required', '', 'Volunteer Registration', 'multisel', 'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province|firstaid=First Aid and CPR', 600, 'Required Personal Information on the Volunteer personal information page (Name and Email is always required)', -1),
('committee_personal_fields', 'phonehome,phonecell,phonework,fax,org', 'Committee Members', 'multisel', 'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province|firstaid=First Aid and CPR', 500, 'Personal Information to ask for on the Committee Member profile page (in addition to Name and Email)', -1),
('committee_personal_required', '', 'Committee Members', 'multisel', 'sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province|firstaid=First Aid and CPR', 600, 'Required Personal Information on the Committee Member profile page (Name and Email is always required)', -1),
('volunteer_registration_type', 'open', 'Volunteer Registration', 'enum', 'open=Open|singlepassword=Single Password|invite=Invite', 150, 'The type of Volunteer Registration to use', -1),
('volunteer_registration_singlepassword', '', 'Volunteer Registration', 'text', '', 160, 'The single password to use if using Single Password Volunteer Registration (the option above this one). Ignored if not using Single Password volunteer registration.', -1),
('reports_show_unawarded_awards', 'yes', 'Reports', 'yesno', '', 100, 'Display awards that were not awarded in the Award Ceremony script.', -1),
('reports_show_unawarded_prizes', 'yes', 'Reports', 'yesno', '', 200, 'Display prizes that were not awarded in the Award Ceremony script.', -1),
('participant_project_summary_wordmin', '0', 'Participant Registration', 'number', '', 1900, 'The minimum number of words acceptable in the project summary', -1),
('tours_assigner_activity', 'Done', 'Tour Assigner', '', '', 99999, '', 0),
('tours_assigner_percent', '-1', 'Tour Assigner', '', '', 99999, '', 0),
('tours_assigner_effort', '10000', 'Tour Assigner', 'enum', '100=Low|1000=Medium|10000=High', 99999, 'This controls how long and hard the tour assigner will look for a quality solution. Low effort will finish almost instantly but give a very poor result. High effort can take several minutes to run, but it gives a very good solution. ', -1),
('project_sort_format', '', 'Global', 'text', '', 610, 'Project Sorting Format. This format will be used to sort the projects on lists and in reports. Use the same letters as the Project Number Format (C, D, N, etc.). If left blank, the project number format will also be used to sort the projects.', -1),
('winners_show_prize_amounts', 'yes', 'Global', 'yesno', '', 700, 'Show the dollar amounts of the cash/scholarship prizes on the publicly viewable winners page.', -1),
('participant_short_title_charmax', '50', 'Participant Registration', 'number', '', 2200, 'The maximum number of characters acceptable in the short project title (Max 255)', -1),
('participant_short_title_enable', 'no', 'Participant Registration', 'yesno', '', 2100, 'Ask the participants for a short project title as well as their full title.', -1),
('participant_regfee_items_enable', 'no', 'Participant Registration', 'yesno', '', 2750, 'Ask the participants for registration fee item options.  Enabling this item also enables a Registration Fee Item Manager in the Administration section.  Use this manager to add optional registration items (that have a fee) for a student to select.', -1),
('judge_scheduler_percent', '-1', 'Judge Scheduler', '', '', 99999, '', 0),
('judge_scheduler_activity', 'Done', 'Judge Scheduler', '', '', 99999, '', 0),
('provincestate', 'Province', 'Localization', 'enum', 'Province=Province|State=State', 100, 'Use Province or State?', -1),
('postalzip', 'Postal Code', 'Localization', 'enum', 'Postal Code=Postal Code|Zip Code=Zip Code', 110, 'Use Postal Code or Zip Code?', -1),
('theme', 'default', 'Global', 'text', '', 850, 'Theme for colours/icons', -1),
('dateformat', 'Y-m-d', 'Localization', 'text', '', 200, 'Date format (<a href="http://www.php.net/manual/en/function.date.php" target="_blank">formatting options</a>)', -1),
('timeformat', 'H:i:s', 'Localization', 'text', '', 210, 'Time format (<a href="http://www.php.net/manual/en/function.date.php" target="_blank">formatting options</a>)', -1),
('country', 'CA', 'Localization', 'text', '', 90, 'Country code (<a href="http://www.iso.org/iso/country_codes/iso_3166_code_lists/english_country_names_and_code_elements.htm" target="_blank">look up 2 letter code</a>)', -1),
('sponsor_personal_fields', 'phonecell,phonework,fax,org', 'Sponsors', 'multisel', 'salutation=Salutation|sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 500, 'Personal Information to ask for on the Sponsor Contact profile page (in addition to Name and Email)', -1),
('sponsor_personal_required', '', 'Sponsors', 'multisel', 'salutation=Salutation|sex=Gender|phonehome=Home Phone|phonework=Work Phone|phonecell=Cell Phone|fax=Fax|org=Organization|birthdate=Birthdate|lang=Preferred Language|address=Address and PostalCode|city=City|province=Province', 600, 'Required Personal Information on the Sponsor Contact profile page (Name and Email is always required)', -1),
('judges_availability_enable', 'no', 'Judge Registration', 'yesno', '', 950, 'Allow judges to specify their time availability on the fair day based on the defined judging rounds/timeslots. The scheduler will then use this judge availability data when assigning judges to teams and projects.', -1),
('fair_stats_participation', 'yes', 'Science Fairs', 'yesno', '', 100, 'Gather Stats: Student and School Participation (students, gender, and projects) by age group.', -1),
('fair_stats_schools_ext', 'yes', 'Science Fairs', 'yesno', '', 200, 'Gather Stats: Extended school participation data.<ul>\r\n<li>Number of at-risk schools and students<li>Number of public schools and students<li>Number of private/independent schools and students</ul>', -1),
('fair_stats_minorities', 'firstnations', 'Science Fairs', 'multisel', 'firstnations=Number of First Nation students|disabled=Number of Disabled students', 300, 'Gather Stats: Participant minority demographics (must be filled in manually by the fair)', -1),
('fair_stats_guests', 'yes', 'Science Fairs', 'yesno', '', 400, 'Gather Stats: Number of student and public guests (must be filled in manually by the fair)', -1),
('fair_stats_sffbc_misc', 'yes', 'Science Fairs', 'yesno', '', 500, 'Gather Stats: Misc. SFFBC Data<ul> <li>Supporting teachers <li>Students with increased interest in sci and tech <li>Students considering a career in science</ul>', -1),
('fair_stats_info', 'yes', 'Science Fairs', 'yesno', '', 600, 'Gather Stats: Information about the fair (date, location, budget, charity info).', -1),
('fair_stats_next_chair', 'yes', 'Science Fairs', 'yesno', '', 700, 'Gather Stats: Chairperson name and contact info for the next year', -1),
('fair_stats_scholarships', 'yes', 'Science Fairs', 'yesno', '', 800, 'Gather Stats: Scholarships given out by the fair', -1),
('fair_stats_delegates', 'yes', 'Science Fairs', 'yesno', '', 900, 'Gather Stats: CWSF Delegate names/email/jacket size', -1),
('FISCALYEAR', '2010', 'Special', '', '', 0, 'The current fiscal year that the fundraising module is using', 0),
('registered_charity', 'no', 'Fundraising', 'yesno', '', 100, 'Is your organization a registered charity?', -1),
('charity_number', '', 'Fundraising', 'text', '', 200, 'Charity Registration Number', -1);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `code` varchar(2) NOT NULL,
  `country` varchar(64) NOT NULL,
  UNIQUE KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`code`, `country`) VALUES
('AF', 'AFGHANISTAN'),
('AX', 'ÅLAND ISLANDS'),
('AL', 'ALBANIA'),
('DZ', 'ALGERIA'),
('AS', 'AMERICAN SAMOA'),
('AD', 'ANDORRA'),
('AO', 'ANGOLA'),
('AI', 'ANGUILLA'),
('AQ', 'ANTARCTICA'),
('AG', 'ANTIGUA AND BARBUDA'),
('AR', 'ARGENTINA'),
('AM', 'ARMENIA'),
('AW', 'ARUBA'),
('AU', 'AUSTRALIA'),
('AT', 'AUSTRIA'),
('AZ', 'AZERBAIJAN'),
('BS', 'BAHAMAS'),
('BH', 'BAHRAIN'),
('BD', 'BANGLADESH'),
('BB', 'BARBADOS'),
('BY', 'BELARUS'),
('BE', 'BELGIUM'),
('BZ', 'BELIZE'),
('BJ', 'BENIN'),
('BM', 'BERMUDA'),
('BT', 'BHUTAN'),
('BO', 'BOLIVIA'),
('BA', 'BOSNIA AND HERZEGOVINA'),
('BW', 'BOTSWANA'),
('BV', 'BOUVET ISLAND'),
('BR', 'BRAZIL'),
('IO', 'BRITISH INDIAN OCEAN TERRITORY'),
('BN', 'BRUNEI DARUSSALAM'),
('BG', 'BULGARIA'),
('BF', 'BURKINA FASO'),
('BI', 'BURUNDI'),
('KH', 'CAMBODIA'),
('CM', 'CAMEROON'),
('CA', 'CANADA'),
('CV', 'CAPE VERDE'),
('KY', 'CAYMAN ISLANDS'),
('CF', 'CENTRAL AFRICAN REPUBLIC'),
('TD', 'CHAD'),
('CL', 'CHILE'),
('CN', 'CHINA'),
('CX', 'CHRISTMAS ISLAND'),
('CC', 'COCOS (KEELING) ISLANDS'),
('CO', 'COLOMBIA'),
('KM', 'COMOROS'),
('CG', 'CONGO'),
('CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE'),
('CK', 'COOK ISLANDS'),
('CR', 'COSTA RICA'),
('CI', 'CÔTE D''IVOIRE'),
('HR', 'CROATIA'),
('CU', 'CUBA'),
('CY', 'CYPRUS'),
('CZ', 'CZECH REPUBLIC'),
('DK', 'DENMARK'),
('DJ', 'DJIBOUTI'),
('DM', 'DOMINICA'),
('DO', 'DOMINICAN REPUBLIC'),
('EC', 'ECUADOR'),
('EG', 'EGYPT'),
('SV', 'EL SALVADOR'),
('GQ', 'EQUATORIAL GUINEA'),
('ER', 'ERITREA'),
('EE', 'ESTONIA'),
('ET', 'ETHIOPIA'),
('FK', 'FALKLAND ISLANDS (MALVINAS)'),
('FO', 'FAROE ISLANDS'),
('FJ', 'FIJI'),
('FI', 'FINLAND'),
('FR', 'FRANCE'),
('GF', 'FRENCH GUIANA'),
('PF', 'FRENCH POLYNESIA'),
('TF', 'FRENCH SOUTHERN TERRITORIES'),
('GA', 'GABON'),
('GM', 'GAMBIA'),
('GE', 'GEORGIA'),
('DE', 'GERMANY'),
('GH', 'GHANA'),
('GI', 'GIBRALTAR'),
('GR', 'GREECE'),
('GL', 'GREENLAND'),
('GD', 'GRENADA'),
('GP', 'GUADELOUPE'),
('GU', 'GUAM'),
('GT', 'GUATEMALA'),
('GG', 'GUERNSEY'),
('GN', 'GUINEA'),
('GW', 'GUINEA-BISSAU'),
('GY', 'GUYANA'),
('HT', 'HAITI'),
('HM', 'HEARD ISLAND AND MCDONALD ISLANDS'),
('VA', 'HOLY SEE (VATICAN CITY STATE)'),
('HN', 'HONDURAS'),
('HK', 'HONG KONG'),
('HU', 'HUNGARY'),
('IS', 'ICELAND'),
('IN', 'INDIA'),
('ID', 'INDONESIA'),
('IR', 'IRAN, ISLAMIC REPUBLIC OF'),
('IQ', 'IRAQ'),
('IE', 'IRELAND'),
('IM', 'ISLE OF MAN'),
('IL', 'ISRAEL'),
('IT', 'ITALY'),
('JM', 'JAMAICA'),
('JP', 'JAPAN'),
('JE', 'JERSEY'),
('JO', 'JORDAN'),
('KZ', 'KAZAKHSTAN'),
('KE', 'KENYA'),
('KI', 'KIRIBATI'),
('KP', 'KOREA, DEMOCRATIC PEOPLE''S REPUBLIC OF'),
('KR', 'KOREA, REPUBLIC OF'),
('KW', 'KUWAIT'),
('KG', 'KYRGYZSTAN'),
('LA', 'LAO PEOPLE''S DEMOCRATIC REPUBLIC'),
('LV', 'LATVIA'),
('LB', 'LEBANON'),
('LS', 'LESOTHO'),
('LR', 'LIBERIA'),
('LY', 'LIBYAN ARAB JAMAHIRIYA'),
('LI', 'LIECHTENSTEIN'),
('LT', 'LITHUANIA'),
('LU', 'LUXEMBOURG'),
('MO', 'MACAO'),
('MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF'),
('MG', 'MADAGASCAR'),
('MW', 'MALAWI'),
('MY', 'MALAYSIA'),
('MV', 'MALDIVES'),
('ML', 'MALI'),
('MT', 'MALTA'),
('MH', 'MARSHALL ISLANDS'),
('MQ', 'MARTINIQUE'),
('MR', 'MAURITANIA'),
('MU', 'MAURITIUS'),
('YT', 'MAYOTTE'),
('MX', 'MEXICO'),
('FM', 'MICRONESIA, FEDERATED STATES OF'),
('MD', 'MOLDOVA, REPUBLIC OF'),
('MC', 'MONACO'),
('MN', 'MONGOLIA'),
('ME', 'MONTENEGRO'),
('MS', 'MONTSERRAT'),
('MA', 'MOROCCO'),
('MZ', 'MOZAMBIQUE'),
('MM', 'MYANMAR'),
('NA', 'NAMIBIA'),
('NR', 'NAURU'),
('NP', 'NEPAL'),
('NL', 'NETHERLANDS'),
('AN', 'NETHERLANDS ANTILLES'),
('NC', 'NEW CALEDONIA'),
('NZ', 'NEW ZEALAND'),
('NI', 'NICARAGUA'),
('NE', 'NIGER'),
('NG', 'NIGERIA'),
('NU', 'NIUE'),
('NF', 'NORFOLK ISLAND'),
('MP', 'NORTHERN MARIANA ISLANDS'),
('NO', 'NORWAY'),
('OM', 'OMAN'),
('PK', 'PAKISTAN'),
('PW', 'PALAU'),
('PS', 'PALESTINIAN TERRITORY, OCCUPIED'),
('PA', 'PANAMA'),
('PG', 'PAPUA NEW GUINEA'),
('PY', 'PARAGUAY'),
('PE', 'PERU'),
('PH', 'PHILIPPINES'),
('PN', 'PITCAIRN'),
('PL', 'POLAND'),
('PT', 'PORTUGAL'),
('PR', 'PUERTO RICO'),
('QA', 'QATAR'),
('RE', 'REUNION'),
('RO', 'ROMANIA'),
('RU', 'RUSSIAN FEDERATION'),
('RW', 'RWANDA'),
('BL', 'SAINT BARTHÉLEMY'),
('SH', 'SAINT HELENA'),
('KN', 'SAINT KITTS AND NEVIS'),
('LC', 'SAINT LUCIA'),
('MF', 'SAINT MARTIN'),
('PM', 'SAINT PIERRE AND MIQUELON'),
('VC', 'SAINT VINCENT AND THE GRENADINES'),
('WS', 'SAMOA'),
('SM', 'SAN MARINO'),
('ST', 'SAO TOME AND PRINCIPE'),
('SA', 'SAUDI ARABIA'),
('SN', 'SENEGAL'),
('RS', 'SERBIA'),
('SC', 'SEYCHELLES'),
('SL', 'SIERRA LEONE'),
('SG', 'SINGAPORE'),
('SK', 'SLOVAKIA'),
('SI', 'SLOVENIA'),
('SB', 'SOLOMON ISLANDS'),
('SO', 'SOMALIA'),
('ZA', 'SOUTH AFRICA'),
('GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS'),
('ES', 'SPAIN'),
('LK', 'SRI LANKA'),
('SD', 'SUDAN'),
('SR', 'SURINAME'),
('SJ', 'SVALBARD AND JAN MAYEN'),
('SZ', 'SWAZILAND'),
('SE', 'SWEDEN'),
('CH', 'SWITZERLAND'),
('SY', 'SYRIAN ARAB REPUBLIC'),
('TW', 'TAIWAN, PROVINCE OF CHINA'),
('TJ', 'TAJIKISTAN'),
('TZ', 'TANZANIA, UNITED REPUBLIC OF'),
('TH', 'THAILAND'),
('TL', 'TIMOR-LESTE'),
('TG', 'TOGO'),
('TK', 'TOKELAU'),
('TO', 'TONGA'),
('TT', 'TRINIDAD AND TOBAGO'),
('TN', 'TUNISIA'),
('TR', 'TURKEY'),
('TM', 'TURKMENISTAN'),
('TC', 'TURKS AND CAICOS ISLANDS'),
('TV', 'TUVALU'),
('UG', 'UGANDA'),
('UA', 'UKRAINE'),
('AE', 'UNITED ARAB EMIRATES'),
('GB', 'UNITED KINGDOM'),
('US', 'UNITED STATES'),
('UM', 'UNITED STATES MINOR OUTLYING ISLANDS'),
('UY', 'URUGUAY'),
('UZ', 'UZBEKISTAN'),
('VU', 'VANUATU'),
('VE', 'VENEZUELA'),
('VN', 'VIET NAM'),
('VG', 'VIRGIN ISLANDS, BRITISH'),
('VI', 'VIRGIN ISLANDS, U.S.'),
('WF', 'WALLIS AND FUTUNA'),
('EH', 'WESTERN SAHARA'),
('YE', 'YEMEN'),
('ZM', 'ZAMBIA'),
('ZW', 'ZIMBABWE');

-- --------------------------------------------------------

--
-- Table structure for table `dates`
--

CREATE TABLE IF NOT EXISTS `dates` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(32) NOT NULL default '',
  `description` varchar(64) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `dates`
--

INSERT INTO `dates` (`id`, `date`, `name`, `description`, `year`) VALUES
(1, '0000-00-00 00:00:00', 'fairdate', 'Date of the fair', -1),
(2, '0000-00-00 00:00:00', 'regopen', 'Registration system opens', -1),
(3, '0000-00-00 00:00:00', 'regclose', 'Registration system closes', -1),
(4, '0000-00-00 00:00:00', 'postparticipants', 'Registered participants are posted on the website', -1),
(5, '0000-00-00 00:00:00', 'postwinners', 'Winners are posted on the website', -1),
(6, '0000-00-00 00:00:00', 'judgeregopen', 'Judges registration opens', -1),
(7, '0000-00-00 00:00:00', 'judgeregclose', 'Judges registration closes', -1),
(8, '0000-00-00 00:00:00', 'specawardregopen', 'Special Awards self-nomination opens', -1),
(9, '0000-00-00 00:00:00', 'specawardregclose', 'Special Awards self-nomination closes', -1);

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE IF NOT EXISTS `documents` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `date` date NOT NULL,
  `title` varchar(128) NOT NULL,
  `sel_category` varchar(128) NOT NULL,
  `filename` varchar(128) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `documents`
--


-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `val` varchar(64) NOT NULL default '',
  `name` varchar(128) NOT NULL default '',
  `description` varchar(255) NOT NULL default '',
  `from` varchar(128) NOT NULL default '',
  `subject` varchar(128) NOT NULL default '',
  `body` text NOT NULL,
  `bodyhtml` text,
  `type` enum('system','user','fundraising') NOT NULL default 'system',
  `fundraising_campaigns_id` int(10) unsigned default NULL,
  `lastsent` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`id`, `val`, `name`, `description`, `from`, `subject`, `body`, `bodyhtml`, `type`, `fundraising_campaigns_id`, `lastsent`) VALUES
(1, 'register_participants_resend_regnum', 'Participant Registration - Resend Registration Number', 'Resend the password to the participant if they submit a ''forgot regnum'' request', 'webmaster@seab-sciencefair.com', 'Registration for [FAIRNAME]', 'We have received a request for the retrieval of your registration number from this email address.  Please find your existing registration number below\r\n\r\nRegistration Number: [REGNUM]\r\n', NULL, 'system', NULL, NULL),
(2, 'new_participant', 'New Participant', 'Email that new participants receive when they are added to the system', 'webmaster@seab-sciencefair.com', 'Registration for [FAIRNAME]', 'A new registration account has been created for you.  To access your registration account, please enter the following registration number into the registration website:\r\n\r\nEmail Address: [EMAIL]\r\nRegistration Number: [REGNUM]\r\n', NULL, 'system', NULL, NULL),
(5, 'register_participants_received', 'Participant Registration - Form Received', 'Sent to the participant when the admin flags their signature form as received', '', 'Registration for [FAIRNAME] Complete', 'Dear [FIRSTNAME],\r\nYour registration for the [FAIRNAME] is now complete.\r\nYour project number is [PROJECTNUMBER].  Please write down your project number and bring it with you to the fair in order to expedite the check-in process.\r\n\r\nSincerely,\r\n [FAIRNAME]', NULL, 'system', NULL, NULL),
(6, 'register_participants_paymentpending', 'Participant Registration - Payment Pending', 'Sent to the participant when the admin flags their signature form as received but payment pending', '', 'Registration for [FAIRNAME] Not Complete - Payment Pending', 'Dear [FIRSTNAME],\r\nYour registration for the [FAIRNAME] is not yet complete.  We received your registration form however it was missing the required registration fee.  Please send the required registration fee in aso soon as possible in order to complete your registration.\r\n\r\nYour project number is [PROJECTNUMBER].  Please write down your project number and bring it with you to the fair in order to expedite the check-in process.\r\n\r\nSincerely,\r\n [FAIRNAME]', NULL, 'system', NULL, NULL),
(7, 'volunteer_welcome', 'Volunteer Registration - Welcome', 'Welcome email sent to a volunteer after they have registered for the first time. This email includes their temporary password.', '', 'Volunteer Registration for [FAIRNAME]', 'Thank you for registering as a volunteer at our fair. Please find your temporary password below. After logging in for the first time you will be prompted to change your password.\n\nVolunteer Email Address: [EMAIL]\nVolunteer Password: [PASSWORD]', NULL, 'system', NULL, NULL),
(8, 'volunteer_recover_password', 'Volunteer Registration - Recover Password', 'Recover the password for a volunteer if they submit a ''forgot password'' request', '', 'Volunteer Registration for [FAIRNAME]', 'We have received a request for the recovery of your password from this email address. Please find your new password below:\n\nVolunteer Email Address: [EMAIL]\nVolunteer Password: [PASSWORD] ', NULL, 'system', NULL, NULL),
(9, 'committee_recover_password', 'Committee Members - Recover Password', 'Recover the password for a committee member if they submit a ''forgot password'' request', '', 'Committee Member for [FAIRNAME]', 'We have received a request for the recovery of your password from this email address. Please find your new password below:\n\nCommittee Member Email Address: [EMAIL]\nCommittee Member Password: [PASSWORD] ', NULL, 'system', NULL, NULL),
(10, 'volunteer_new_invite', 'Volunteers - New Volunteer Invitation', 'This is sent to a new volunteer when they are invited using the invite volunteers administration section, only available when the Volunteer Registration Type is set to Invite', '', 'Volunteer Registration for [FAIRNAME]', 'You have been invited to be a volunteer for the [FAIRNAME].  An account has been created for you to login with and complete your information.  You can login to the volunteer registration site with:\n\nEmail Address: [EMAIL]\nPassword: [PASSWORD]\n\nYou can change your password once you login.', NULL, 'system', NULL, NULL),
(11, 'volunteer_add_invite', 'Volunteers - Add Volunteer Invitation', 'This is sent to existing users when they are invited using the invite volunteers administration section, only available when the Volunteer Registration Type is set to Invite', '', 'Volunteer Registration for [FAIRNAME]', 'The role of volunteer for the [FAIRNAME] has been added to your account by a committee member.  When you login again, there will be a [Switch Roles] link in the upper right hand area of the page.  Clicking on [Switch Roles] will let you switch between being a Volunteer and your other roles without needing to logout.\n', NULL, 'system', NULL, NULL),
(12, 'judge_recover_password', 'Judges - Recover Password', 'Recover the password for a judge if they submit a ''forgot password'' request', '', 'Password Recovery for [FAIRNAME]', 'We have received a request for the recovery of your password from this email address. Please find your new password below:\n\nJudge Email Address: [EMAIL]\nJudge Password: [PASSWORD] ', NULL, 'system', NULL, NULL),
(13, 'judge_welcome', 'Judges - Welcome', 'Welcome email sent to a judge after they have registered for the first time. This email includes their temporary password.', '', 'Judge Registration for [FAIRNAME]', 'Thank you for registering as a judge at our fair. Please find your temporary password below. After logging in for the first time you will be prompted to change your password.\n\nJudge Email Address: [EMAIL]\nJudge Password: [PASSWORD]', NULL, 'system', NULL, NULL),
(14, 'judge_new_invite', 'Judges - New Judge Invitation', 'This is sent to a new judge when they are invited using the invite users  administration option.', '', 'Judge Registration for [FAIRNAME]', 'You have been invited to be a judge for the [FAIRNAME].  An account has been created for you to login with and complete your information.  You can login to the judge registration site at [WEBSITE] with:\n\nEmail Address: [EMAIL]\nPassword: [PASSWORD]\nYou can change your password once you login.', NULL, 'system', NULL, NULL),
(15, 'judge_add_invite', 'Judges - Add Judge Invitation', 'This is sent to existing users when they are invited using the invite users administration option.', '', 'Judge Registration for [FAIRNAME]', 'The role of judge for the [FAIRNAME] has been added to your account by a committee member.  When you login again, there will be a [Switch Roles] link in the upper right hand area of the page.  Clicking on [Switch Roles] will let you switch between being a Judge and your other roles without needing to logout.\n', NULL, 'system', NULL, NULL),
(16, 'judge_activate_reminder', 'Judges - Activation Reminder', 'This is sent to existing judges who have not yet activated their account for the current fair year.', '', 'Judge Registration for [FAIRNAME]', 'This message is to let you know that Judge registration for the [FAIRNAME] is now open.  If you would like to participate in the fair this year please log in to the registration site using your email address ([EMAIL]) an\n', NULL, 'system', NULL, NULL),
(17, 'volunteer_activate_reminder', 'Volunteer Registration - Activation Reminder', 'This is sent to existing volunteers who have not yet activated their account for the current fair year.', '', 'Volunteer Registration for [FAIRNAME]', 'This message is to let you know that Volunteer registration for the [FAIRNAME] is now open.  If you would like to participate in the fair this year please log in to the registration site using your email address ([EMAIL]).\n', NULL, 'system', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `emergencycontact`
--

CREATE TABLE IF NOT EXISTS `emergencycontact` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `emergencycontact`
--


-- --------------------------------------------------------

--
-- Table structure for table `fairs`
--

CREATE TABLE IF NOT EXISTS `fairs` (
  `id` int(11) NOT NULL auto_increment,
  `name` tinytext NOT NULL,
  `abbrv` varchar(16) NOT NULL,
  `type` enum('feeder','sfiab','ysc') NOT NULL,
  `url` tinytext NOT NULL,
  `website` tinytext NOT NULL,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `enable_stats` enum('no','yes') NOT NULL,
  `enable_awards` enum('no','yes') NOT NULL,
  `enable_winners` enum('no','yes') NOT NULL,
  `gather_stats` set('participation','schools_ext','minorities','guests','sffbc_misc','info','next_chair','scholarships','delegates') NOT NULL,
  `catmap` tinytext NOT NULL,
  `divmap` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `fairs`
--

INSERT INTO `fairs` (`id`, `name`, `abbrv`, `type`, `url`, `website`, `username`, `password`, `enable_stats`, `enable_awards`, `enable_winners`, `gather_stats`, `catmap`, `divmap`) VALUES
(1, 'Sci-Tech Ontario', 'STO', 'ysc', 'http://www.scitechontario.org/awarddownloader/index.php', 'http://www.scitechontario.org/awarddownloader/help.php', '', '', 'yes', 'yes', 'yes', '', '', ''),
(2, 'Youth Science Canada', 'YSC', 'ysc', 'https://secure.ysf-fsj.ca/awarddownloader/index.php', 'http://apps.ysf-fsj.ca/awarddownloader/help.php', '', '', 'yes', 'yes', 'yes', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `fairs_awards_link`
--

CREATE TABLE IF NOT EXISTS `fairs_awards_link` (
  `id` int(11) NOT NULL auto_increment,
  `fairs_id` int(11) NOT NULL,
  `award_awards_id` int(11) NOT NULL,
  `download_award` enum('no','yes') NOT NULL,
  `upload_winners` enum('no','yes') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fairs_awards_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `fairs_stats`
--

CREATE TABLE IF NOT EXISTS `fairs_stats` (
  `id` int(11) NOT NULL auto_increment,
  `fairs_id` int(11) NOT NULL default '0',
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
  `students_total` int(11) NOT NULL,
  `schools_total` int(11) NOT NULL,
  `schools_active` int(11) NOT NULL,
  `students_public` int(11) NOT NULL,
  `schools_public` int(11) NOT NULL default '0',
  `students_private` int(11) NOT NULL,
  `schools_private` int(11) NOT NULL default '0',
  `schools_districts` int(11) NOT NULL default '0',
  `studentsvisiting` int(11) NOT NULL default '0',
  `publicvisiting` int(11) NOT NULL default '0',
  `firstnations` int(11) NOT NULL default '0',
  `students_atrisk` int(11) NOT NULL default '0',
  `schools_atrisk` int(11) NOT NULL,
  `teacherssupporting` int(11) NOT NULL default '0',
  `increasedinterest` int(11) NOT NULL default '0',
  `consideringcareer` int(11) NOT NULL default '0',
  `committee_members` int(11) NOT NULL,
  `judges` int(11) NOT NULL,
  `next_chair_name` varchar(128) NOT NULL default '',
  `next_chair_email` varchar(64) NOT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fairs_stats`
--


-- --------------------------------------------------------

--
-- Table structure for table `fundraising_campaigns`
--

CREATE TABLE IF NOT EXISTS `fundraising_campaigns` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `type` varchar(64) NOT NULL,
  `startdate` date NOT NULL,
  `enddate` date NOT NULL,
  `followupdate` date default NULL,
  `active` enum('no','yes') NOT NULL,
  `target` int(11) NOT NULL,
  `fundraising_goal` varchar(32) NOT NULL,
  `filterparameters` varchar(255) default NULL,
  `fiscalyear` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fundraising_campaigns`
--


-- --------------------------------------------------------

--
-- Table structure for table `fundraising_campaigns_users_link`
--

CREATE TABLE IF NOT EXISTS `fundraising_campaigns_users_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fundraising_campaigns_id` int(10) unsigned NOT NULL,
  `users_uid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fundraising_campaigns_users_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `fundraising_donations`
--

CREATE TABLE IF NOT EXISTS `fundraising_donations` (
  `id` int(11) NOT NULL auto_increment,
  `sponsors_id` int(11) default NULL,
  `fundraising_goal` varchar(32) NOT NULL,
  `fundraising_campaigns_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  `status` enum('pending','confirmed','received') NOT NULL,
  `probability` int(11) NOT NULL,
  `fiscalyear` int(11) NOT NULL default '0',
  `thanked` enum('no','yes') NOT NULL default 'no',
  `receiptrequired` enum('no','yes') NOT NULL,
  `receiptsent` enum('no','yes') NOT NULL,
  `datereceived` date default NULL,
  `supporttype` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fundraising_donations`
--


-- --------------------------------------------------------

--
-- Table structure for table `fundraising_donor_levels`
--

CREATE TABLE IF NOT EXISTS `fundraising_donor_levels` (
  `id` int(11) NOT NULL auto_increment,
  `level` varchar(64) NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `description` text NOT NULL,
  `fiscalyear` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `fundraising_donor_levels`
--

INSERT INTO `fundraising_donor_levels` (`id`, `level`, `min`, `max`, `description`, `fiscalyear`) VALUES
(1, 'Bronze', 100, 499, '', -1),
(2, 'Silver', 500, 999, '', -1),
(3, 'Gold', 1000, 10000, '', -1);

-- --------------------------------------------------------

--
-- Table structure for table `fundraising_donor_logs`
--

CREATE TABLE IF NOT EXISTS `fundraising_donor_logs` (
  `id` int(11) NOT NULL auto_increment,
  `sponsors_id` int(11) NOT NULL,
  `dt` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `log` text NOT NULL,
  `type` varchar(32) NOT NULL,
  `fundraising_campaigns_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `fundraising_donor_logs`
--


-- --------------------------------------------------------

--
-- Table structure for table `fundraising_goals`
--

CREATE TABLE IF NOT EXISTS `fundraising_goals` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `goal` varchar(32) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text,
  `system` enum('no','yes') NOT NULL default 'no',
  `budget` int(10) unsigned NOT NULL default '0',
  `fiscalyear` int(11) NOT NULL default '0',
  `deadline` date NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `type` (`goal`,`fiscalyear`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `fundraising_goals`
--

INSERT INTO `fundraising_goals` (`id`, `goal`, `name`, `description`, `system`, `budget`, `fiscalyear`, `deadline`) VALUES
(1, 'sfgeneral', 'Science Fair - General Funds', 'General funds donated to the science fair may be allocated as the science fair organizers see fit.', 'yes', 0, -1, '0000-00-00'),
(2, 'sfawards', 'Science Fair - Awards', 'Award Sponsorships are provided to allow an sponsor/donor to give a specific award.', 'yes', 0, -1, '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `judges_availability`
--

CREATE TABLE IF NOT EXISTS `judges_availability` (
  `id` int(11) NOT NULL auto_increment,
  `users_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start` time NOT NULL,
  `end` time NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `judges_availability`
--


-- --------------------------------------------------------

--
-- Table structure for table `judges_jdiv`
--

CREATE TABLE IF NOT EXISTS `judges_jdiv` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `jdiv_id` int(11) NOT NULL default '0',
  `projectdivisions_id` int(11) NOT NULL default '0',
  `projectcategories_id` int(11) NOT NULL default '0',
  `lang` char(2) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `judges_jdiv`
--


-- --------------------------------------------------------

--
-- Table structure for table `judges_schedulerconfig`
--

CREATE TABLE IF NOT EXISTS `judges_schedulerconfig` (
  `var` varchar(64) NOT NULL default '',
  `val` text NOT NULL,
  `description` text NOT NULL,
  `year` int(11) NOT NULL default '0',
  UNIQUE KEY `var` (`var`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `judges_schedulerconfig`
--

INSERT INTO `judges_schedulerconfig` (`var`, `val`, `description`, `year`) VALUES
('num_times_judged', '3', 'The number of times that each project must be judged (by different judging teams)', -1),
('num_timeslots', '20', 'The number of timeslots available during the judging period', -1),
('max_projects_per_team', '5', 'The maximum number of projects that a team can judge', -1),
('min_judges_per_team', '2', 'The minimum number of judges that should be on a judging team', -1),
('max_judges_per_team', '4', 'The maximum number of judges that should be on a judging team', -1);

-- --------------------------------------------------------

--
-- Table structure for table `judges_specialaward_sel`
--

CREATE TABLE IF NOT EXISTS `judges_specialaward_sel` (
  `id` int(11) NOT NULL auto_increment,
  `users_id` int(11) NOT NULL default '0',
  `award_awards_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `judges_specialaward_sel`
--


-- --------------------------------------------------------

--
-- Table structure for table `judges_teams`
--

CREATE TABLE IF NOT EXISTS `judges_teams` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `num` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `autocreate_type_id` int(11) default NULL,
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `num` (`num`,`year`),
  UNIQUE KEY `name` (`name`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `judges_teams`
--


-- --------------------------------------------------------

--
-- Table structure for table `judges_teams_awards_link`
--

CREATE TABLE IF NOT EXISTS `judges_teams_awards_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned NOT NULL default '0',
  `judges_teams_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `award_awards_id` (`award_awards_id`,`judges_teams_id`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `judges_teams_awards_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `judges_teams_link`
--

CREATE TABLE IF NOT EXISTS `judges_teams_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `users_id` int(11) NOT NULL default '0',
  `judges_teams_id` int(11) NOT NULL default '0',
  `captain` enum('no','yes') NOT NULL default 'no',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `judges_teams_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `judges_teams_timeslots_link`
--

CREATE TABLE IF NOT EXISTS `judges_teams_timeslots_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `judges_teams_id` int(10) unsigned NOT NULL default '0',
  `judges_timeslots_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `judges_teams_id` (`judges_teams_id`,`judges_timeslots_id`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `judges_teams_timeslots_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `judges_teams_timeslots_projects_link`
--

CREATE TABLE IF NOT EXISTS `judges_teams_timeslots_projects_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `judges_teams_id` int(10) unsigned NOT NULL default '0',
  `judges_timeslots_id` int(10) unsigned NOT NULL default '0',
  `projects_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `judges_teams_id` (`judges_teams_id`,`judges_timeslots_id`,`projects_id`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `judges_teams_timeslots_projects_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `judges_timeslots`
--

CREATE TABLE IF NOT EXISTS `judges_timeslots` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `round_id` int(11) NOT NULL,
  `type` enum('timeslot','divisional1','divisional2','grand','special') NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `starttime` time NOT NULL default '00:00:00',
  `endtime` time NOT NULL default '00:00:00',
  `name` tinytext NOT NULL,
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `judges_timeslots`
--


-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `lang` char(2) NOT NULL default '',
  `langname` varchar(32) NOT NULL default '',
  `active` enum('N','Y') NOT NULL default 'N',
  UNIQUE KEY `lang` (`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`lang`, `langname`, `active`) VALUES
('en', 'English', 'Y'),
('fr', 'Français', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `mentors`
--

CREATE TABLE IF NOT EXISTS `mentors` (
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `mentors`
--


-- --------------------------------------------------------

--
-- Table structure for table `pagetext`
--

CREATE TABLE IF NOT EXISTS `pagetext` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `textname` varchar(64) NOT NULL default '',
  `textdescription` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `lastupdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `year` int(11) NOT NULL default '0',
  `lang` varchar(2) NOT NULL default 'en',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `textname` (`textname`,`year`,`lang`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `pagetext`
--

INSERT INTO `pagetext` (`id`, `textname`, `textdescription`, `text`, `lastupdate`, `year`, `lang`) VALUES
(1, 'register_participants_main_instructions', 'Participant registration main page instructions', 'Once all sections are complete, please print the signature page, obtain the required signatures, and mail the signature form, along with any required registration fees to:\r\nInsert address here\r\n\r\nYour forms must be received, post marked by <b>insert date here</b>.  Late entries will not be accepted', '0000-00-00 00:00:00', -1, 'en'),
(3, 'register_judges_invite', 'Judge registration instructions for Invite-Only mode', 'Thank you for volunteering as a judge for the fair.  Judge registration is by invitation only.  To get started, please contact the chief judge. We will then send you an email with instructions on how to complete your registration. This extra step is only required for first time judges.  We are confident that you will find the experience sufficiently enriching that you will continue to serve as a judge in future years.  Thanks again for your willingness to participate.', '2009-10-22 12:56:05', -1, 'en'),
(4, 'register_volunteer_invite', 'Volunteer registration instructions for Invite-Only mode', 'Thank you for volunteering for the fair.  Volunteer registration is by invitation only.<br /><br />Please contact the fair and request to be invited as a volunteer.  We will then send you an email with instructions on how to complete your volunteer registration.<br /><br />If you have been invited already, you need to login using the same email address that you were invited with.', '0000-00-00 00:00:00', -1, 'en'),
(5, 'schoolaccess', 'School access login page', 'Welcome to the School Access Page.  This page allows your school to provide several key pieces of information for the fair, as well as feedback about the schools experience with/at the fair.', '0000-00-00 00:00:00', -1, 'en');

-- --------------------------------------------------------

--
-- Table structure for table `projectcategories`
--

CREATE TABLE IF NOT EXISTS `projectcategories` (
  `id` int(10) unsigned NOT NULL default '0',
  `category` varchar(64) NOT NULL default '',
  `category_shortform` char(3) NOT NULL default '',
  `mingrade` tinyint(4) NOT NULL default '0',
  `maxgrade` tinyint(4) NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `projectcategories`
--


-- --------------------------------------------------------

--
-- Table structure for table `projectcategoriesdivisions_link`
--

CREATE TABLE IF NOT EXISTS `projectcategoriesdivisions_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `projectdivisions_id` int(10) unsigned NOT NULL default '0',
  `projectcategories_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `projectcategoriesdivisions_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `projectdivisions`
--

CREATE TABLE IF NOT EXISTS `projectdivisions` (
  `id` int(10) unsigned NOT NULL default '0',
  `division` varchar(64) NOT NULL default '',
  `division_shortform` char(3) NOT NULL default '',
  `cwsfdivisionid` int(11) default NULL,
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `projectdivisions`
--


-- --------------------------------------------------------

--
-- Table structure for table `projectdivisionsselector`
--

CREATE TABLE IF NOT EXISTS `projectdivisionsselector` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `question` varchar(255) NOT NULL default '',
  `yes` int(10) unsigned NOT NULL default '0',
  `yes_type` enum('question','division') NOT NULL default 'question',
  `no` int(10) unsigned NOT NULL default '0',
  `no_type` enum('question','division') NOT NULL default 'question',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `projectdivisionsselector`
--


-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `projectnumber` varchar(16) default NULL,
  `projectsort` varchar(16) default NULL,
  `projectnumber_seq` int(11) NOT NULL,
  `projectsort_seq` int(11) NOT NULL,
  `projectcategories_id` tinyint(4) NOT NULL default '0',
  `projectdivisions_id` tinyint(4) NOT NULL default '0',
  `cwsfdivisionid` int(11) default NULL,
  `title` varchar(255) NOT NULL default '',
  `shorttitle` varchar(255) NOT NULL,
  `summarycountok` tinyint(1) NOT NULL default '1',
  `summary` text NOT NULL,
  `year` int(11) NOT NULL default '0',
  `req_electricity` enum('no','yes') NOT NULL default 'no',
  `req_table` enum('no','yes') NOT NULL default 'yes',
  `req_special` varchar(128) NOT NULL default '',
  `language` char(2) NOT NULL default '',
  `fairs_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `projects`
--


-- --------------------------------------------------------

--
-- Table structure for table `projectsubdivisions`
--

CREATE TABLE IF NOT EXISTS `projectsubdivisions` (
  `id` int(10) unsigned NOT NULL default '0',
  `year` int(11) unsigned NOT NULL default '0',
  `projectdivisions_id` int(10) unsigned NOT NULL default '0',
  `subdivision` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `projectsubdivisions`
--


-- --------------------------------------------------------

--
-- Table structure for table `project_specialawards_link`
--

CREATE TABLE IF NOT EXISTS `project_specialawards_link` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `award_awards_id` int(10) unsigned default '0',
  `projects_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `project_specialawards_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE IF NOT EXISTS `provinces` (
  `code` char(2) NOT NULL default '',
  `province` varchar(32) NOT NULL default '',
  `countries_code` varchar(2) NOT NULL,
  UNIQUE KEY `countries_code` (`countries_code`,`code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`code`, `province`, `countries_code`) VALUES
('AB', 'Alberta', 'CA'),
('BC', 'British Columbia', 'CA'),
('MB', 'Manitoba', 'CA'),
('NB', 'New Brunswick', 'CA'),
('NF', 'Newfoundland and Labrador', 'CA'),
('NT', 'Northwest Territories', 'CA'),
('NS', 'Nova Scotia', 'CA'),
('NU', 'Nunavut', 'CA'),
('ON', 'Ontario', 'CA'),
('PE', 'Prince Edward Island', 'CA'),
('QC', 'Québec', 'CA'),
('SK', 'Saskatchewan', 'CA'),
('YK', 'Yukon Territory', 'CA'),
('AL', 'Alabama', 'US'),
('AK', 'Alaska', 'US'),
('AS', 'American Samoa', 'US'),
('AZ', 'Arizona', 'US'),
('AR', 'Arkansas', 'US'),
('CA', 'California', 'US'),
('CO', 'Colorado', 'US'),
('CT', 'Connecticut', 'US'),
('DE', 'Delaware', 'US'),
('DC', 'District of Columbia', 'US'),
('FM', 'Federated States of Micronesia', 'US'),
('FL', 'Florida', 'US'),
('GA', 'Georgia', 'US'),
('GU', 'Guam', 'US'),
('HI', 'Hawaii', 'US'),
('ID', 'Idaho', 'US'),
('IL', 'Illinois', 'US'),
('IN', 'Indiana', 'US'),
('IA', 'Iowa', 'US'),
('KS', 'Kansas', 'US'),
('KY', 'Kentucky', 'US'),
('LA', 'Louisiana', 'US'),
('ME', 'Maine', 'US'),
('MH', 'Marshall Islands', 'US'),
('MD', 'Maryland', 'US'),
('MA', 'Massachusetts', 'US'),
('MI', 'Michigan', 'US'),
('MN', 'Minnesota', 'US'),
('MS', 'Mississippi', 'US'),
('MO', 'Missouri', 'US'),
('MT', 'Montana', 'US'),
('NE', 'Nebraska', 'US'),
('NV', 'Nevada', 'US'),
('NH', 'New Hampshire', 'US'),
('NJ', 'New Jersey', 'US'),
('NM', 'New Mexico', 'US'),
('NY', 'New York', 'US'),
('NC', 'North Carolina', 'US'),
('ND', 'North Dakota', 'US'),
('MP', 'Northern Mariana Islands', 'US'),
('OH', 'Ohio', 'US'),
('OK', 'Oklahoma', 'US'),
('OR', 'Oregon', 'US'),
('PW', 'Palau', 'US'),
('PA', 'Pennsylvania', 'US'),
('PR', 'Puerto Rico', 'US'),
('RI', 'Rhode Island', 'US'),
('SC', 'South Carolina', 'US'),
('SD', 'South Dakota', 'US'),
('TN', 'Tennessee', 'US'),
('TX', 'Texas', 'US'),
('UT', 'Utah', 'US'),
('VT', 'Vermont', 'US'),
('VI', 'Virgin Islands', 'US'),
('VA', 'Virginia', 'US'),
('WA', 'Washington', 'US'),
('WV', 'West Virginia', 'US'),
('WI', 'Wisconsin', 'US'),
('WY', 'Wyoming', 'US');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` int(11) NOT NULL default '0',
  `section` varchar(32) NOT NULL default '',
  `db_heading` varchar(64) NOT NULL default '',
  `question` text NOT NULL,
  `type` enum('check','yesno','int','text') NOT NULL default 'check',
  `required` enum('no','yes') NOT NULL default 'yes',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `year`, `section`, `db_heading`, `question`, `type`, `required`, `ord`) VALUES
(14, -1, 'judgereg', 'Attending Lunch', 'Will you be attending the Judge''s Lunch?', 'yesno', 'yes', 4);

-- --------------------------------------------------------

--
-- Table structure for table `question_answers`
--

CREATE TABLE IF NOT EXISTS `question_answers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `users_id` int(10) unsigned NOT NULL default '0',
  `questions_id` int(10) unsigned NOT NULL default '0',
  `answer` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `question_answers`
--


-- --------------------------------------------------------

--
-- Table structure for table `regfee_items`
--

CREATE TABLE IF NOT EXISTS `regfee_items` (
  `id` int(11) NOT NULL auto_increment,
  `year` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `cost` float NOT NULL,
  `per` enum('student','project') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `regfee_items`
--


-- --------------------------------------------------------

--
-- Table structure for table `regfee_items_link`
--

CREATE TABLE IF NOT EXISTS `regfee_items_link` (
  `id` int(11) NOT NULL auto_increment,
  `students_id` int(11) NOT NULL,
  `regfee_items_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `regfee_items_link`
--


-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--
--- DRE 2018
--- 1: id of the registration
--- 2: Registration Number or REGNUM
--- 3: email
--- 4: email of person's contact
--- 5: start of registration
--- 6: end for registration
--- 7: Fairyear 
--- 8: # of mentors to participant
--- 9: Feeder School ID# 
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `num` varchar(8) NOT NULL default '',
  `email` varchar(64) NOT NULL default '',
  `emailcontact` varchar(64) default NULL,
  `start` datetime NOT NULL default '0000-00-00 00:00:00',
  `status` enum('new','open','paymentpending','complete') NOT NULL default 'new',
  `end` datetime NOT NULL default '0000-00-00 00:00:00',
  `year` int(11) NOT NULL default '0',
  `nummentors` tinyint(4) default NULL,
  `schools_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `registrations`
--


-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL auto_increment,
  `system_report_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL default '',
  `desc` tinytext NOT NULL,
  `creator` varchar(128) NOT NULL default '',
  `type` enum('student','judge','award','committee','school','volunteer','tour','fair','fundraising') character set utf8 NOT NULL default 'student',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=48 ;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
(1, 1, 'Student+Project -- Sorted by Last Name', 'Student Name, Project Number and Title, Category, Division short form sorted by Last Name', 'The Grant Brothers', 'student'),
(2, 2, 'Student+Project -- Sorted by Project Number', 'Student Name, Project Number and Title, Category sorted by Project Number', 'The Grant Brothers', 'student'),
(3, 3, 'Student+Project -- Grouped by Category', 'Student Name, Project Number and Title sorted by Last Name, grouped by Category', 'The Grant Brothers', 'student'),
(4, 4, 'Student+Project -- School Names sorted by Last Name', 'Student Name, Project Num, School Name sorted by Last Name', 'The Grant Brothers', 'student'),
(5, 5, 'Student+Project -- Grouped by School sorted by Last Name', 'Student Name, Project Number and Name sorted by Last Name, grouped by School Name', 'The Grant Brothers', 'student'),
(6, 6, 'Teacher -- Name and School Info sorted by Teacher Name', 'Teacher, School Info sorted by Teacher Name', 'The Grant Brothers', 'student'),
(7, 7, 'Teacher -- Names and Contact for each Student by School', 'Student Name, Teacher Name, Teacher Email, School Phone and Fax grouped by School Name with Addresses', 'The Grant Brothers', 'student'),
(8, 8, 'Awards -- Special Awards Nominations Data', 'listing of special award nominations for each project, lots of data for excel so you can slice and dice (and check additional requirements)', 'Ceddy', 'student'),
(9, 9, 'Check-in Lists', 'List of students and partners, project number and name, division, registration fees, tshirt size, sorted by project number, grouped by age category', 'The Grant Brothers', 'student'),
(10, 10, 'Student+Project -- Student (and Partner) grouped by School', 'Student Pairs, Project Name/Num Grouped by School', 'The Grant Brothers', 'student'),
(11, 11, 'Student+Project -- Grouped by School sorted by Project Number', 'Individual Students, Project Name/Num Grouped by School', 'The Grant Brothers', 'student'),
(12, 12, 'Student -- T-Shirt List by School', 'Individual Students, Project Num, TShirt, Grouped by School', 'The Grant Brothers', 'student'),
(13, 13, 'Media -- Program Guide', 'Project Number, Both student names, and Project Title, grouped by School', 'The Grant Brothers', 'student'),
(14, 14, 'Projects -- Titles and Grades from each School', 'Project Name/Num, Grade Grouped by School', 'The Grant Brothers', 'student'),
(15, 15, 'Media -- Award Winners List', 'Project Number, Student Name and Contact info, by each Award', 'The Grant Brothers', 'student'),
(16, 16, 'Projects -- Logistical Display Requirements', 'Project Number, Students, Electricity, Table, and special needs', 'The Grant Brothers', 'student'),
(17, 17, 'Emergency Contact Information', 'Emergency Contact Names, Relationship, and Phone Numbers for each student.', 'The Grant Brothers', 'student'),
(18, 18, 'Student -- Grouped by Grade and Gender (YSF Stats)', 'A list of students grouped by Grade and Gender.  A quick way to total up the info for the YSF regional stats page.', 'The Grant Brothers', 'student'),
(19, 19, 'Student+Project -- Grouped by School, 1 per page', 'Both students names grouped by school, each school list begins on a new page.', 'The Grant Brothers', 'student'),
(20, 20, 'Judges -- Sorted by Last Name', 'A list of judge contact info, sorted by last name', 'The Grant Brothers', 'judge'),
(21, 21, 'Judges -- Judging Teams', 'A list of all the judges, sorted by team number.', 'The Grant Brothers', 'judge'),
(22, 22, 'Awards -- Grouped by Judging Team', 'List of each judging team, and the awards they are judging', 'The Grant Brothers', 'award'),
(23, 23, 'Awards -- Judging Teams grouped by Award', 'A list of each award, and the judging teams that will assign it', 'The Grant Brothers', 'award'),
(24, 24, 'Labels -- School Mailing Addresses', 'School Mailing Addresses ONLY for schools attached to registered students (NOT ALL SCHOOLS) with a blank spot for the teacher''s name, since each student apparently spells their teacher''s name differently.', 'The Grant Brothers', 'student'),
(25, 25, 'Labels -- Student Name and Project Number', 'Just the students names and project name/number on a label.', 'The Grant Brothers', 'student'),
(26, 26, 'Name Tags -- Students', 'Name Tags for Students', 'The Grant Brothers', 'student'),
(27, 27, 'Name Tags -- Judges', 'Name Tags for Judges', 'The Grant Brothers', 'judge'),
(28, 28, 'Name Tags -- Committee Members', 'Name Tags for Committee Members', 'The Grant Brothers', 'committee'),
(29, 29, 'Labels -- Project Identification (for judging sheets)', 'Project identification labels for judging sheets', 'The Grant Brothers', 'student'),
(30, 30, 'Labels -- Table Labels', 'Labels to go on each table, fullpage landscape version', 'The Grant Brothers', 'student'),
(31, 31, 'Awards -- Special Awards Nominations', 'Special award nominations for each project, grouped by special award, one award per page.', 'The Grant Brothers', 'student'),
(32, 32, 'Student+Project -- Grouped by School Board ID', 'Student Name, Project Number and Name sorted by Last Name, grouped by School Board ID', 'The Grant Brothers', 'student'),
(33, 33, 'Certificates -- Participation Certificates', 'A certificate template for each student with name, project name, fair name, and project number at the bottom', 'The Grant Brothers', 'student'),
(34, 34, 'Labels -- Table Labels (small)', 'Labels to go on each table', 'The Grant Brothers', 'student'),
(35, 35, 'School -- All Schools', 'List of all schools in the database. Name, address, principal and phone.', 'The Grant Brothers', 'school'),
(36, 36, 'School -- Access Codes', 'List of access codes and registration passwords for all schools in the database.', 'The Grant Brothers', 'school'),
(37, 37, 'Awards -- Award Sponsor Information', 'Sponsor information for each award with the primary contact.  This is a large report so the default format is CSV.', 'The Grant Brothers', 'award'),
(38, 38, 'Tours -- All Tour Information', 'A listing of just the tours and all related info, no student assignments or anything.', 'The Grant Brothers', 'tour'),
(39, 39, 'Tours -- Available Tours', 'A list of just the tour names and numbers for fair day', 'The Grant Brothers', 'tour'),
(40, 40, 'Tours -- Student Emergency Contact Information', 'Emergency contact information for each tour, each tour starting on a new page.', 'The Grant Brothers', 'student'),
(41, 41, 'Tours -- Student Tour Assignments', 'Participant and Tour Assignments, grouped by age category, sorted by project number', 'The Grant Brothers', 'student'),
(42, 42, 'Winners -- Award Ceremony Presentation Data', 'A CSV dump of all the winners and their prizes.  Useful for importing into an award ceremony presentation, or a document.', 'The Grant Brothers', 'student'),
(43, 43, 'T-Shirt Size Count', 'A list of tshirt sizes (the blank entry is those students who have selected "none"), and the number of tshirts of each size.', 'The Grant Brothers', 'student'),
(44, 44, 'Labels -- Table Labels (with special award nominations)', 'Labels for each project.  This report includes the first 5 projects the students have self-nominated for.  There are boxes for judges to initial too.  We realize that each fair may have a different number of projects.  This reports serves as an example of', 'The Grant Brothers', 'student'),
(45, 45, 'School -- All school information for SFIAB CSV import', 'Generates a CSV file that can be used by another SFIAB to import the school list', 'The Grant Brothers', 'school'),
(46, 46, 'Feeder Fairs -- All Stats', 'All feeder fair statistics in CSV', 'The Grant Brothers', 'fair'),
(47, 47, 'Labels -- Fundraising Campaign Mailing Labels', 'Mailing labels for all the contacts attached to a fundraising campaign', 'The Grant Brothers', 'fundraising');

-- --------------------------------------------------------

--
-- Table structure for table `reports_committee`
--

CREATE TABLE IF NOT EXISTS `reports_committee` (
  `id` int(11) NOT NULL auto_increment,
  `users_id` int(11) NOT NULL,
  `reports_id` int(11) NOT NULL,
  `category` varchar(128) NOT NULL,
  `comment` text NOT NULL,
  `format` varchar(64) NOT NULL,
  `stock` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `reports_committee`
--


-- --------------------------------------------------------

--
-- Table structure for table `reports_items`
--

CREATE TABLE IF NOT EXISTS `reports_items` (
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
  `align` varchar(64) NOT NULL default 'vtop center',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=660 ;

--
-- Dumping data for table `reports_items`
--

INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES
(1, 1, 'col', 5, 'grade', '', 0, 0, 0, 0, 0, '', 'center'),
(2, 1, 'col', 4, 'div', '', 0, 0, 0, 0, 0, '', 'center'),
(3, 1, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', 'center'),
(4, 2, 'col', 3, 'category', '', 0, 0, 0, 0, 0, '', 'center'),
(5, 2, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(6, 2, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(7, 3, 'col', 3, 'div', '', 0, 0, 0, 0, 0, '', 'center'),
(8, 4, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(9, 3, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', 'center'),
(10, 3, 'group', 0, 'category', '', 0, 0, 0, 0, 0, '', 'center'),
(11, 4, 'col', 3, 'grade', '', 0, 0, 0, 0, 0, '', 'center'),
(12, 4, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(13, 4, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(14, 4, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', 'center'),
(15, 5, 'col', 3, 'category', '', 0, 0, 0, 0, 0, '', 'center'),
(16, 5, 'col', 4, 'div', '', 0, 0, 0, 0, 0, '', 'center'),
(17, 5, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', 'center'),
(18, 5, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(19, 6, 'col', 2, 'school_phone', '', 0, 0, 0, 0, 0, '', 'center'),
(20, 6, 'col', 1, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(21, 6, 'col', 0, 'teacher', '', 0, 0, 0, 0, 0, '', 'center'),
(22, 6, 'sort', 0, 'teacher', '', 0, 0, 0, 0, 0, '', 'center'),
(23, 6, 'distinct', 0, 'teacher', '', 0, 0, 0, 0, 0, '', 'center'),
(24, 11, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(25, 11, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(26, 11, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(27, 7, 'col', 5, 'school_fax', '', 0, 0, 0, 0, 0, '', 'center'),
(28, 7, 'col', 4, 'school_phone', '', 0, 0, 0, 0, 0, '', 'center'),
(29, 7, 'col', 3, 'teacheremail', '', 0, 0, 0, 0, 0, '', 'center'),
(30, 7, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(31, 9, 'col', 6, 'div', '', 0, 0, 0, 0, 0, '', 'center'),
(32, 9, 'col', 5, 'tshirt', '', 0, 0, 0, 0, 0, '', 'center'),
(33, 9, 'col', 3, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(34, 9, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(35, 9, 'group', 0, 'category', '', 0, 0, 0, 0, 0, '', 'center'),
(36, 9, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(37, 10, 'col', 2, 'partner', '', 0, 0, 0, 0, 0, '', 'center'),
(38, 10, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(39, 10, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(40, 10, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(41, 10, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(42, 10, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(43, 2, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(44, 11, 'col', 4, 'div', '', 0, 0, 0, 0, 0, '', 'center'),
(45, 11, 'col', 3, 'category', '', 0, 0, 0, 0, 0, '', 'center'),
(46, 11, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(47, 11, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(48, 12, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(49, 12, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(50, 12, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(51, 12, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(52, 13, 'col', 1, 'bothnames', '', 0, 0, 0, 0, 0, '', 'center'),
(53, 13, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(54, 13, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(55, 13, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(56, 13, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(57, 14, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(58, 14, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(59, 14, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(60, 14, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(61, 15, 'col', 5, 'postal', '', 0, 0, 0, 0, 0, '', 'center'),
(62, 15, 'col', 4, 'province', '', 0, 0, 0, 0, 0, '', 'center'),
(63, 15, 'col', 3, 'city', '', 0, 0, 0, 0, 0, '', 'center'),
(64, 15, 'col', 2, 'address', '', 0, 0, 0, 0, 0, '', 'center'),
(65, 15, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(66, 15, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(67, 15, 'group', 0, 'awards', '', 0, 0, 0, 0, 0, '', 'center'),
(68, 1, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', 'center'),
(69, 1, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(70, 1, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(71, 1, 'col', 3, 'category', '', 0, 0, 0, 0, 0, '', 'center'),
(72, 1, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(73, 3, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(74, 3, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(75, 3, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(76, 9, 'col', 4, 'partner', '', 0, 0, 0, 0, 0, '', 'center'),
(77, 9, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(78, 9, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(79, 9, 'col', 1, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(80, 9, 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', 'center'),
(81, 5, 'col', 5, 'grade', '', 0, 0, 0, 0, 0, '', 'center'),
(82, 5, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(83, 5, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(84, 3, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(85, 4, 'col', 2, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(86, 7, 'col', 2, 'teacher', '', 0, 0, 0, 0, 0, '', 'center'),
(87, 7, 'group', 1, 'schooladdr', '', 0, 0, 0, 0, 0, '', 'center'),
(88, 7, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(89, 11, 'col', 5, 'grade', '', 0, 0, 0, 0, 0, '', 'center'),
(90, 2, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(91, 2, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(92, 2, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(93, 12, 'col', 2, 'tshirt', '', 0, 0, 0, 0, 0, '', 'center'),
(94, 12, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(95, 7, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(96, 12, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(97, 12, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(98, 7, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(99, 7, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(100, 7, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(101, 15, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(102, 15, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(103, 15, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(104, 15, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(105, 13, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(106, 13, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(107, 13, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(108, 13, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(109, 14, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(110, 14, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(111, 14, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(112, 14, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(113, 16, 'col', 3, 'req_table', '', 0, 0, 0, 0, 0, '', ''),
(114, 16, 'col', 2, 'req_elec', '', 0, 0, 0, 0, 0, '', ''),
(115, 16, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', ''),
(116, 16, 'group', 0, 'category', '', 0, 0, 0, 0, 0, '', ''),
(117, 16, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(118, 16, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(119, 16, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(120, 16, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(121, 16, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(122, 17, 'col', 4, 'emerg_phone', '', 0, 0, 0, 0, 0, '', ''),
(123, 17, 'col', 3, 'emerg_relation', '', 0, 0, 0, 0, 0, '', ''),
(124, 17, 'col', 2, 'emerg_name', '', 0, 0, 0, 0, 0, '', ''),
(125, 17, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', ''),
(126, 7, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(127, 14, 'col', 2, 'grade', '', 0, 0, 0, 0, 0, '', 'center'),
(128, 17, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(129, 6, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(130, 6, 'col', 3, 'school_fax', '', 0, 0, 0, 0, 0, '', 'center'),
(131, 17, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(132, 17, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', ''),
(133, 6, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(134, 6, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(135, 9, 'col', 0, 'paid', '', 0, 0, 0, 0, 0, '', 'center'),
(136, 1, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(137, 2, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(138, 3, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(139, 3, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(140, 4, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(141, 4, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(142, 10, 'col', 3, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(143, 10, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(144, 10, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(145, 10, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(146, 5, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(147, 5, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(148, 5, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(149, 5, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(150, 11, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(151, 11, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(152, 11, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(153, 18, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(154, 18, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(155, 18, 'col', 2, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(156, 18, 'group', 0, 'grade_str', '', 0, 0, 0, 0, 0, '', 'center'),
(157, 18, 'group', 1, 'gender', '', 0, 0, 0, 0, 0, '', 'center'),
(158, 18, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(159, 18, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(160, 18, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(161, 18, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(162, 3, 'col', 4, 'grade', '', 0, 0, 0, 0, 0, '', 'center'),
(163, 1, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(164, 2, 'col', 4, 'div', '', 0, 0, 0, 0, 0, '', 'center'),
(165, 2, 'col', 5, 'grade', '', 0, 0, 0, 0, 0, '', 'center'),
(166, 19, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(167, 19, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', 'center'),
(168, 19, 'col', 2, 'bothnames', '', 0, 0, 0, 0, 0, '', 'center'),
(169, 19, 'group', 0, 'school', '', 0, 0, 0, 0, 0, '', 'center'),
(170, 19, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', 'center'),
(171, 19, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(172, 19, 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', 'center'),
(173, 19, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(174, 21, 'sort', 1, 'namefl', '', 0, 0, 0, 0, 0, '', 'center'),
(175, 21, 'col', 1, 'team', '', 0, 0, 0, 0, 0, '', 'center'),
(176, 21, 'col', 2, 'captain', '', 0, 0, 0, 0, 0, '', 'center'),
(177, 21, 'col', 3, 'namefl', '', 0, 0, 0, 0, 0, '', 'center'),
(178, 21, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(179, 20, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(180, 20, 'col', 4, 'complete', '', 0, 0, 0, 0, 0, '', 'center'),
(181, 20, 'col', 0, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(182, 20, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(183, 20, 'sort', 0, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(184, 20, 'col', 1, 'email', '', 0, 0, 0, 0, 0, '', 'center'),
(185, 20, 'col', 2, 'phone_home', '', 0, 0, 0, 0, 0, '', 'center'),
(186, 20, 'col', 3, 'phone_work', '', 0, 0, 0, 0, 0, '', 'center'),
(187, 20, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(188, 21, 'sort', 0, 'teamnum', '', 0, 0, 0, 0, 0, '', 'center'),
(189, 21, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(190, 21, 'col', 0, 'teamnum', '', 0, 0, 0, 0, 0, '', 'center'),
(191, 21, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', 'center'),
(192, 22, 'col', 1, 'type', '', 0, 0, 0, 0, 0, '', 'center'),
(193, 22, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', 'center'),
(194, 22, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(195, 22, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(196, 22, 'group', 0, 'judgeteamnum', '', 0, 0, 0, 0, 0, '', 'center'),
(197, 22, 'col', 0, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(198, 22, 'group', 1, 'judgeteamname', '', 0, 0, 0, 0, 0, '', 'center'),
(199, 23, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', 'center'),
(200, 23, 'col', 1, 'judgeteamname', '', 0, 0, 0, 0, 0, '', 'center'),
(201, 23, 'group', 0, 'type', '', 0, 0, 0, 0, 0, '', 'center'),
(202, 23, 'sort', 0, 'judgeteamnum', '', 0, 0, 0, 0, 0, '', 'center'),
(203, 23, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', 'center'),
(204, 23, 'group', 1, 'name', '', 0, 0, 0, 0, 0, '', 'center'),
(205, 23, 'col', 0, 'judgeteamnum', '', 0, 0, 0, 0, 0, '', 'center'),
(206, 23, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', 'center'),
(207, 16, 'col', 4, 'req_special', '', 0, 0, 0, 0, 0, '', ''),
(208, 16, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
(209, 16, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(210, 16, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(211, 16, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(212, 16, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(213, 17, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(214, 25, 'option', 6, 'stock', '5164', 0, 0, 0, 0, 0, '', ''),
(215, 25, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', ''),
(216, 24, 'col', 2, 'school_city_prov', '', 5, 50, 95, 8, 1, '', 'left'),
(217, 24, 'col', 1, 'school_address', '', 5, 40, 95, 16, 2, '', 'left'),
(218, 24, 'col', 0, 'school', '', 5, 5, 95, 16, 2, '', 'left'),
(219, 24, 'option', 6, 'stock', '5164', 0, 0, 0, 0, 0, '', ''),
(220, 24, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(221, 24, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(222, 24, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
(223, 24, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(224, 24, 'sort', 0, 'school', '', 0, 0, 0, 0, 0, '', ''),
(225, 25, 'col', 4, 'school', '', 1, 90, 98, 5, 1, '', 'center'),
(226, 24, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(227, 24, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(228, 25, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', ''),
(229, 25, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
(230, 25, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(231, 25, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(232, 25, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(233, 25, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(234, 8, 'col', 7, 'nom_awards', '', 0, 0, 0, 0, 0, '', ''),
(235, 25, 'col', 3, 'categorydivision', '', 1, 80, 98, 12, 2, '', 'center'),
(236, 25, 'col', 2, 'pn', '', 1, 68, 98, 8, 1, '', 'center'),
(237, 27, 'sort', 0, 'namefl', '', 0, 0, 0, 0, 0, '', ''),
(238, 25, 'col', 1, 'title', '', 1, 35, 98, 27, 3, '', 'center'),
(239, 25, 'col', 0, 'namefl', '', 5, 5, 90, 28, 2, '', 'center'),
(240, 26, 'col', 2, 'categorydivision', '', 1, 70, 98, 14, 2, '', 'center'),
(241, 26, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(242, 26, 'option', 6, 'stock', 'nametag', 0, 0, 0, 0, 0, '', ''),
(243, 26, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', ''),
(244, 26, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', ''),
(245, 26, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
(246, 26, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(247, 26, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(248, 26, 'col', 1, 'title', '', 1, 35, 98, 27, 3, '', 'center'),
(249, 26, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(250, 26, 'col', 0, 'namefl', '', 5, 5, 90, 28, 2, 'bold', 'center'),
(251, 27, 'col', 1, 'static_text', 'Judge', 1, 40, 98, 10, 1, '', 'center'),
(252, 27, 'col', 0, 'namefl', '', 1, 15, 98, 24, 2, 'bold', 'center'),
(253, 27, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', ''),
(254, 27, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
(255, 27, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(256, 27, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(257, 27, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(258, 28, 'col', 1, 'static_text', 'Committee', 1, 40, 98, 10, 1, '', 'center'),
(259, 28, 'col', 0, 'name', '', 1, 15, 98, 24, 2, 'bold', 'center'),
(260, 28, 'sort', 0, 'name', '', 0, 0, 0, 0, 0, '', ''),
(261, 28, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', ''),
(262, 28, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
(263, 28, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(264, 28, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(265, 28, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(266, 30, 'option', 6, 'stock', 'fullpage_landscape', 0, 0, 0, 0, 0, '', ''),
(267, 29, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(268, 29, 'col', 1, 'categorydivision', '', 1, 30, 98, 18, 1, '', 'left'),
(269, 8, 'col', 6, 'school_city', '', 0, 0, 0, 0, 0, '', ''),
(270, 29, 'col', 0, 'pn', '', 1, 5, 98, 20, 1, '', 'left'),
(271, 29, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(272, 29, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(273, 29, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
(274, 29, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(275, 29, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(276, 29, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(277, 30, 'col', 3, 'categorydivision', '', 1, 85, 98, 5, 1, '', 'center'),
(278, 30, 'col', 2, 'pn', '', 1, 20, 98, 35, 1, '', 'center'),
(279, 30, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(280, 30, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', ''),
(281, 30, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(282, 30, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(283, 30, 'col', 1, 'title', '', 1, 5, 98, 15, 3, '', 'center'),
(284, 30, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(285, 31, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(286, 8, 'col', 5, 'birthdate', '', 0, 0, 0, 0, 0, '', ''),
(287, 8, 'col', 4, 'gender', '', 0, 0, 0, 0, 0, '', ''),
(288, 31, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(289, 31, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(290, 31, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(291, 31, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(292, 31, 'col', 5, 'age', '', 0, 0, 0, 0, 0, '', ''),
(293, 31, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(294, 17, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
(295, 31, 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', ''),
(296, 31, 'col', 4, 'gender', '', 0, 0, 0, 0, 0, '', ''),
(297, 31, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(298, 31, 'col', 3, 'grade', '', 0, 0, 0, 0, 0, '', ''),
(299, 31, 'group', 0, 'nom_awards', '', 0, 0, 0, 0, 0, '', ''),
(300, 32, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(301, 32, 'col', 4, 'school', '', 0, 0, 0, 0, 0, '', ''),
(302, 32, 'col', 3, 'grade', '', 0, 0, 0, 0, 0, '', ''),
(303, 32, 'col', 2, 'title', '', 0, 0, 0, 0, 0, '', ''),
(304, 32, 'group', 0, 'school_board', '', 0, 0, 0, 0, 0, '', ''),
(305, 32, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(306, 32, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(307, 32, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(308, 32, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(309, 32, 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', ''),
(310, 32, 'col', 1, 'name', '', 0, 0, 0, 0, 0, '', ''),
(311, 32, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(312, 32, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(313, 32, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(314, 17, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(315, 17, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(316, 17, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(317, 17, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(318, 31, 'col', 2, 'namefl', '', 0, 0, 0, 0, 0, '', ''),
(319, 31, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', ''),
(320, 31, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(321, 33, 'col', 5, 'static_text', 'Chair', 5, 85, 30, 2, 1, '', 'center'),
(322, 33, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(323, 8, 'col', 2, 'namefl', '', 0, 0, 0, 0, 0, '', ''),
(324, 8, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(325, 33, 'col', 6, 'static_text', 'Chief Judge', 60, 85, 30, 2, 1, '', 'center'),
(326, 33, 'col', 4, 'fair_year', '', 5, 25, 30, 6, 1, '', 'center'),
(327, 33, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(328, 33, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(329, 33, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(330, 33, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
(331, 33, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(332, 33, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(333, 33, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(334, 33, 'col', 3, 'pn', '', 3, 97, 94, 1, 1, '', 'right'),
(335, 33, 'col', 0, 'fair_name', '', 1, 36, 98, 4, 1, '', 'center'),
(336, 33, 'col', 1, 'namefl', '', 1, 56, 98, 8, 2, '', 'center'),
(337, 33, 'col', 2, 'title', '', 1, 65, 98, 12, 3, '', 'center'),
(338, 24, 'col', 3, 'school_postal', '', 5, 60, 95, 8, 1, '', 'left'),
(339, 30, 'col', 0, 'bothnames', '', 1, 70, 98, 10, 2, '', 'center'),
(340, 30, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(341, 26, 'col', 3, 'pn', '', 1, 85, 98, 8, 1, '', 'center'),
(342, 27, 'col', 2, 'organization', '', 1, 70, 98, 16, 2, '', 'center'),
(343, 27, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', ''),
(344, 27, 'option', 6, 'stock', 'nametag', 0, 0, 0, 0, 0, '', ''),
(345, 27, 'filter', 0, 'complete', 'yes', 0, 0, 0, 0, 0, '', ''),
(346, 28, 'col', 2, 'organization', '', 1, 70, 98, 16, 2, '', 'center'),
(347, 28, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', ''),
(348, 28, 'option', 6, 'stock', 'nametag', 0, 0, 0, 0, 0, '', ''),
(349, 29, 'col', 2, 'title', '', 1, 55, 98, 40, 2, '', 'left'),
(350, 29, 'option', 6, 'stock', '5161', 0, 0, 0, 0, 0, '', ''),
(351, 30, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', ''),
(352, 30, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(353, 8, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(354, 8, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(355, 8, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(356, 8, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(357, 8, 'col', 3, 'grade', '', 0, 0, 0, 0, 0, '', ''),
(358, 8, 'col', 1, 'title', '', 0, 0, 0, 0, 0, '', ''),
(359, 8, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(360, 8, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(361, 8, 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', ''),
(362, 8, 'sort', 0, 'nom_awards', '', 0, 0, 0, 0, 0, '', ''),
(363, 8, 'sort', 1, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(364, 34, 'col', 3, 'categorydivision', '', 1, 85, 98, 7, 1, '', 'center'),
(365, 34, 'col', 2, 'pn', '', 1, 20, 98, 35, 1, '', 'center'),
(366, 34, 'col', 1, 'title', '', 1, 5, 98, 24, 3, '', 'center'),
(367, 34, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(368, 34, 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', ''),
(369, 34, 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
(370, 34, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(371, 34, 'col', 0, 'bothnames', '', 1, 70, 98, 14, 2, '', 'center'),
(372, 34, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(373, 34, 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', ''),
(374, 34, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(375, 34, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(376, 34, 'option', 6, 'stock', '5164', 0, 0, 0, 0, 0, '', ''),
(377, 35, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(378, 35, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(379, 35, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
(380, 35, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(381, 35, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(382, 35, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(383, 35, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(384, 35, 'col', 0, 'school', '', 0, 0, 0, 0, 0, '', ''),
(385, 35, 'col', 1, 'schooladdr', '', 0, 0, 0, 0, 0, '', ''),
(386, 35, 'col', 2, 'school_principal', '', 0, 0, 0, 0, 0, '', ''),
(387, 35, 'col', 3, 'school_phone', '', 0, 0, 0, 0, 0, '', ''),
(388, 35, 'sort', 0, 'school', '', 0, 0, 0, 0, 0, '', ''),
(389, 36, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(390, 36, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(391, 36, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
(392, 36, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(393, 36, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(394, 36, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(395, 36, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(396, 36, 'col', 0, 'school', '', 0, 0, 0, 0, 0, '', ''),
(397, 36, 'col', 1, 'school_city', '', 0, 0, 0, 0, 0, '', ''),
(398, 36, 'col', 2, 'school_accesscode', '', 0, 0, 0, 0, 0, '', ''),
(399, 36, 'col', 3, 'school_registration_password', '', 0, 0, 0, 0, 0, '', ''),
(400, 36, 'col', 4, 'school_board', '', 0, 0, 0, 0, 0, '', ''),
(401, 36, 'sort', 0, 'school', '', 0, 0, 0, 0, 0, '', ''),
(402, 37, 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', ''),
(403, 37, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(404, 37, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(405, 37, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(406, 37, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(407, 37, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(408, 37, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(409, 37, 'col', 0, 'name', '', 0, 0, 0, 0, 0, '', ''),
(410, 37, 'col', 1, 'sponsor_organization', '', 0, 0, 0, 0, 0, '', ''),
(411, 37, 'col', 2, 'sponsor_phone', '', 0, 0, 0, 0, 0, '', ''),
(412, 37, 'col', 3, 'sponsor_fax', '', 0, 0, 0, 0, 0, '', ''),
(413, 37, 'col', 4, 'sponsor_address', '', 0, 0, 0, 0, 0, '', ''),
(414, 37, 'col', 5, 'sponsor_city', '', 0, 0, 0, 0, 0, '', ''),
(415, 37, 'col', 6, 'sponsor_province', '', 0, 0, 0, 0, 0, '', ''),
(416, 37, 'col', 7, 'sponsor_postal', '', 0, 0, 0, 0, 0, '', ''),
(417, 37, 'col', 8, 'sponsor_notes', '', 0, 0, 0, 0, 0, '', ''),
(419, 37, 'col', 10, 'pcontact_salutation', '', 0, 0, 0, 0, 0, '', ''),
(420, 37, 'col', 11, 'pcontact_namefl', '', 0, 0, 0, 0, 0, '', ''),
(421, 37, 'col', 12, 'pcontact_position', '', 0, 0, 0, 0, 0, '', ''),
(422, 37, 'col', 13, 'pcontact_email', '', 0, 0, 0, 0, 0, '', ''),
(423, 37, 'col', 14, 'pcontact_hphone', '', 0, 0, 0, 0, 0, '', ''),
(424, 37, 'col', 15, 'pcontact_wphone', '', 0, 0, 0, 0, 0, '', ''),
(425, 37, 'col', 16, 'pcontact_cphone', '', 0, 0, 0, 0, 0, '', ''),
(426, 37, 'col', 17, 'pcontact_fax', '', 0, 0, 0, 0, 0, '', ''),
(427, 37, 'col', 18, 'pcontact_notes', '', 0, 0, 0, 0, 0, '', ''),
(428, 37, 'sort', 0, 'name', '', 0, 0, 0, 0, 0, '', ''),
(429, 38, 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', ''),
(430, 38, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(431, 38, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(432, 38, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(433, 38, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(434, 38, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(435, 38, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(436, 38, 'col', 0, 'tour_num', '', 0, 0, 0, 0, 0, '', ''),
(437, 38, 'col', 1, 'tour_name', '', 0, 0, 0, 0, 0, '', ''),
(438, 38, 'col', 2, 'tour_capacity', '', 0, 0, 0, 0, 0, '', ''),
(439, 38, 'col', 3, 'tour_mingrade', '', 0, 0, 0, 0, 0, '', ''),
(440, 38, 'col', 4, 'tour_maxgrade', '', 0, 0, 0, 0, 0, '', ''),
(441, 38, 'col', 5, 'tour_desc', '', 0, 0, 0, 0, 0, '', ''),
(442, 38, 'col', 6, 'tour_location', '', 0, 0, 0, 0, 0, '', ''),
(443, 38, 'col', 7, 'tour_contact', '', 0, 0, 0, 0, 0, '', ''),
(444, 38, 'sort', 0, 'tour_name', '', 0, 0, 0, 0, 0, '', ''),
(445, 39, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(446, 39, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(447, 39, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
(448, 39, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(449, 39, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(450, 39, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(451, 39, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(452, 39, 'col', 0, 'tour_num', '', 0, 0, 0, 0, 0, '', ''),
(453, 39, 'col', 1, 'tour_name', '', 0, 0, 0, 0, 0, '', ''),
(454, 39, 'sort', 0, 'tour_id', '', 0, 0, 0, 0, 0, '', ''),
(455, 40, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(456, 40, 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', ''),
(457, 40, 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
(458, 40, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(459, 40, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(460, 40, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(461, 40, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(462, 40, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(463, 40, 'col', 1, 'namefl', '', 0, 0, 0, 0, 0, '', ''),
(464, 40, 'col', 2, 'emerg_name', '', 0, 0, 0, 0, 0, '', ''),
(465, 40, 'col', 3, 'emerg_relation', '', 0, 0, 0, 0, 0, '', ''),
(466, 40, 'col', 4, 'emerg_phone', '', 0, 0, 0, 0, 0, '', ''),
(467, 40, 'group', 0, 'tour_assign_numname', '', 0, 0, 0, 0, 0, '', ''),
(468, 40, 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', ''),
(469, 41, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(470, 41, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(471, 41, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(472, 41, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(473, 41, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(474, 41, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(475, 41, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(476, 41, 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(477, 41, 'col', 1, 'namefl', '', 0, 0, 0, 0, 0, '', ''),
(478, 41, 'col', 2, 'tour_assign_numname', '', 0, 0, 0, 0, 0, '', ''),
(479, 41, 'group', 0, 'category', '', 0, 0, 0, 0, 0, '', ''),
(480, 41, 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(481, 19, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
(482, 42, 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', ''),
(483, 42, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(484, 42, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(485, 42, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(486, 42, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(487, 42, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(488, 42, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(489, 42, 'col', 0, 'division', '', 0, 0, 0, 0, 1, '', ''),
(490, 42, 'col', 1, 'fr_division', '', 0, 0, 0, 0, 1, '', ''),
(491, 42, 'col', 2, 'category', '', 0, 0, 0, 0, 1, '', ''),
(492, 42, 'col', 3, 'fr_category', '', 0, 0, 0, 0, 1, '', ''),
(493, 42, 'col', 4, 'award_name', '', 0, 0, 0, 0, 1, '', ''),
(494, 42, 'col', 5, 'award_prize_name', '', 0, 0, 0, 0, 1, '', ''),
(495, 42, 'col', 6, 'award_prize_cash', '', 0, 0, 0, 0, 1, '', ''),
(496, 42, 'col', 7, 'award_prize_scholarship', '', 0, 0, 0, 0, 1, '', ''),
(497, 42, 'col', 8, 'award_prize_value', '', 0, 0, 0, 0, 1, '', ''),
(498, 42, 'col', 9, 'pn', '', 0, 0, 0, 0, 1, '', ''),
(499, 42, 'col', 10, 'title', '', 0, 0, 0, 0, 1, '', ''),
(500, 42, 'col', 11, 'namefl', '', 0, 0, 0, 0, 1, '', ''),
(501, 42, 'col', 12, 'partnerfl', '', 0, 0, 0, 0, 1, '', ''),
(502, 42, 'col', 13, 'school', '', 0, 0, 0, 0, 1, '', ''),
(503, 42, 'col', 14, 'school_city', '', 0, 0, 0, 0, 1, '', ''),
(504, 42, 'col', 15, 'school_province', '', 0, 0, 0, 0, 1, '', ''),
(505, 42, 'col', 16, 'school_board', '', 0, 0, 0, 0, 1, '', ''),
(506, 42, 'col', 17, 'school_postal', '', 0, 0, 0, 0, 1, '', ''),
(507, 42, 'sort', 0, 'order', '', 0, 0, 0, 0, 1, '', ''),
(508, 42, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 1, '', ''),
(509, 42, 'filter', 0, 'award_excludefromac', 'no', 0, 0, 0, 0, 1, '', ''),
(510, 43, 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
(511, 43, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(512, 43, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(513, 43, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(514, 43, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(515, 43, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(516, 43, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(517, 43, 'col', 0, 'tshirt', '', 0, 0, 0, 0, 1, '', ''),
(518, 43, 'col', 1, 'special_tshirt_count', '', 0, 0, 0, 0, 1, '', ''),
(519, 43, 'sort', 0, 'tshirt', '', 0, 0, 0, 0, 1, '', ''),
(520, 43, 'filter', 0, 'tshirt', 'none', 5, 0, 0, 0, 1, '', ''),
(521, 44, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(522, 44, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(523, 44, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(524, 44, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(525, 44, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(526, 44, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(527, 44, 'option', 6, 'stock', 'letter_4up', 0, 0, 0, 0, 0, '', ''),
(528, 44, 'col', 0, 'pn', '', 5, 5, 30, 5, 1, '', ''),
(529, 44, 'col', 1, 'nom_awards_name_1', '', 5, 45, 50, 10, 3, '', ''),
(530, 44, 'col', 2, 'nom_awards_name_2', '', 5, 56, 50, 10, 3, '', ''),
(531, 44, 'col', 3, 'nom_awards_name_3', '', 5, 67, 50, 10, 3, '', ''),
(532, 44, 'col', 4, 'nom_awards_name_4', '', 5, 78, 50, 10, 3, '', ''),
(533, 44, 'col', 5, 'nom_awards_name_5', '', 5, 89, 50, 10, 3, '', ''),
(534, 44, 'col', 6, 'static_text', 'Judge 1', 5, 22, 30, 4, 1, '', ''),
(535, 44, 'col', 7, 'static_text', 'Judge 2', 5, 34, 30, 4, 1, '', ''),
(536, 44, 'col', 8, 'static_text', 'Safety Check', 42, 6, 12, 6, 2, '', ''),
(537, 44, 'col', 9, 'static_text', 'Judges: Please initial box when judging of project is complete', 70, 23, 28, 12, 4, '', ''),
(538, 44, 'col', 10, 'static_box', '', 0, 0, 100, 100, 1, '', ''),
(539, 44, 'col', 11, 'static_box', '', 55, 5, 40, 8, 1, '', ''),
(540, 44, 'col', 12, 'static_box', '', 22, 20, 40, 8, 1, '', ''),
(541, 44, 'col', 13, 'static_box', '', 22, 32, 40, 8, 1, '', ''),
(542, 44, 'col', 14, 'static_box', '', 55, 46, 40, 8, 1, '', ''),
(543, 44, 'col', 15, 'static_box', '', 55, 57, 40, 8, 1, '', ''),
(544, 44, 'col', 16, 'static_box', '', 55, 68, 40, 8, 1, '', ''),
(545, 44, 'col', 17, 'static_box', '', 55, 79, 40, 8, 1, '', ''),
(546, 44, 'col', 18, 'static_box', '', 55, 90, 40, 8, 1, '', ''),
(547, 44, 'col', 19, 'static_box', '', 0, 15, 100, 27, 1, '', ''),
(548, 44, 'sort', 0, 'pn', '', 0, 0, 0, 0, 1, '', ''),
(549, 44, 'distinct', 0, 'pn', '', 0, 0, 0, 0, 1, '', ''),
(550, 45, 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', ''),
(551, 45, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(552, 45, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(553, 45, 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(554, 45, 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(555, 45, 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(556, 45, 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(557, 45, 'col', 0, 'school', '', 0, 0, 0, 0, 1, '', ''),
(558, 45, 'col', 1, 'school_lang', '', 0, 0, 0, 0, 1, '', ''),
(559, 45, 'col', 2, 'school_level', '', 0, 0, 0, 0, 1, '', ''),
(560, 45, 'col', 3, 'school_board', '', 0, 0, 0, 0, 1, '', ''),
(561, 45, 'col', 4, 'school_district', '', 0, 0, 0, 0, 1, '', ''),
(562, 45, 'col', 5, 'school_phone', '', 0, 0, 0, 0, 1, '', ''),
(563, 45, 'col', 6, 'school_fax', '', 0, 0, 0, 0, 1, '', ''),
(564, 45, 'col', 7, 'school_address', '', 0, 0, 0, 0, 1, '', ''),
(565, 45, 'col', 8, 'school_city', '', 0, 0, 0, 0, 1, '', ''),
(566, 45, 'col', 9, 'school_province', '', 0, 0, 0, 0, 1, '', ''),
(567, 45, 'col', 10, 'school_postal', '', 0, 0, 0, 0, 1, '', ''),
(568, 45, 'col', 11, 'school_principal', '', 0, 0, 0, 0, 1, '', ''),
(569, 45, 'col', 12, 'school_email', '', 0, 0, 0, 0, 1, '', ''),
(570, 45, 'col', 13, 'school_sh', '', 0, 0, 0, 0, 1, '', ''),
(571, 45, 'col', 14, 'school_shemail', '', 0, 0, 0, 0, 1, '', ''),
(572, 45, 'col', 15, 'school_shphone', '', 0, 0, 0, 0, 1, '', ''),
(573, 45, 'col', 16, 'school_accesscode', '', 0, 0, 0, 0, 1, '', ''),
(574, 45, 'col', 17, 'school_registration_password', '', 0, 0, 0, 0, 1, '', ''),
(575, 45, 'col', 18, 'school_project_limit', '', 0, 0, 0, 0, 1, '', ''),
(576, 45, 'col', 19, 'school_project_limit_per', '', 0, 0, 0, 0, 1, '', ''),
(577, 46, 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', ''),
(578, 46, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(579, 46, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(580, 46, 'option', 3, 'fit_columns', 'no', 0, 0, 0, 0, 0, '', ''),
(581, 46, 'option', 4, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
(582, 46, 'option', 5, 'field_box', 'no', 0, 0, 0, 0, 0, '', ''),
(583, 46, 'option', 6, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(584, 46, 'option', 7, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(585, 46, 'option', 8, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
(586, 46, 'col', 0, 'fair_name', '', 0, 0, 0, 0, 1, '', ' '),
(587, 46, 'col', 1, 'fairstats_year', '', 0, 0, 0, 0, 1, '', ' '),
(588, 46, 'col', 2, 'fairstats_start_date', '', 0, 0, 0, 0, 1, '', ' '),
(589, 46, 'col', 3, 'fairstats_end_date', '', 0, 0, 0, 0, 1, '', ' '),
(590, 46, 'col', 4, 'fairstats_budget', '', 0, 0, 0, 0, 1, '', ' '),
(591, 46, 'col', 5, 'fairstats_address', '', 0, 0, 0, 0, 1, '', ' '),
(592, 46, 'col', 6, 'fairstats_ysf_affiliation_complete', '', 0, 0, 0, 0, 1, '', ' '),
(593, 46, 'col', 7, 'fairstats_charity', '', 0, 0, 0, 0, 1, '', ' '),
(594, 46, 'col', 8, 'fairstats_scholarships', '', 0, 0, 0, 0, 1, '', ' '),
(595, 46, 'col', 9, 'fairstats_male_1', '', 0, 0, 0, 0, 1, '', ' '),
(596, 46, 'col', 10, 'fairstats_male_4', '', 0, 0, 0, 0, 1, '', ' '),
(597, 46, 'col', 11, 'fairstats_male_7', '', 0, 0, 0, 0, 1, '', ' '),
(598, 46, 'col', 12, 'fairstats_male_9', '', 0, 0, 0, 0, 1, '', ' '),
(599, 46, 'col', 13, 'fairstats_male_11', '', 0, 0, 0, 0, 1, '', ' '),
(600, 46, 'col', 14, 'fairstats_female_1', '', 0, 0, 0, 0, 1, '', ' '),
(601, 46, 'col', 15, 'fairstats_female_4', '', 0, 0, 0, 0, 1, '', ' '),
(602, 46, 'col', 16, 'fairstats_female_7', '', 0, 0, 0, 0, 1, '', ' '),
(603, 46, 'col', 17, 'fairstats_female_9', '', 0, 0, 0, 0, 1, '', ' '),
(604, 46, 'col', 18, 'fairstats_female_11', '', 0, 0, 0, 0, 1, '', ' '),
(605, 46, 'col', 19, 'fairstats_projects_1', '', 0, 0, 0, 0, 1, '', ' '),
(606, 46, 'col', 20, 'fairstats_projects_4', '', 0, 0, 0, 0, 1, '', ' '),
(607, 46, 'col', 21, 'fairstats_projects_7', '', 0, 0, 0, 0, 1, '', ' '),
(608, 46, 'col', 22, 'fairstats_projects_9', '', 0, 0, 0, 0, 1, '', ' '),
(609, 46, 'col', 23, 'fairstats_projects_11', '', 0, 0, 0, 0, 1, '', ' '),
(610, 46, 'col', 24, 'fairstats_firstnations', '', 0, 0, 0, 0, 1, '', ' '),
(611, 46, 'col', 25, 'fairstats_students_atrisk', '', 0, 0, 0, 0, 1, '', ' '),
(612, 46, 'col', 26, 'fairstats_schools_atrisk', '', 0, 0, 0, 0, 1, '', ' '),
(613, 46, 'col', 27, 'fairstats_students_total', '', 0, 0, 0, 0, 1, '', ' '),
(614, 46, 'col', 28, 'fairstats_schools_total', '', 0, 0, 0, 0, 1, '', ' '),
(615, 46, 'col', 29, 'fairstats_schools_active', '', 0, 0, 0, 0, 1, '', ' '),
(616, 46, 'col', 30, 'fairstats_students_public', '', 0, 0, 0, 0, 1, '', ' '),
(617, 46, 'col', 31, 'fairstats_schools_public', '', 0, 0, 0, 0, 1, '', ' '),
(618, 46, 'col', 32, 'fairstats_students_private', '', 0, 0, 0, 0, 1, '', ' '),
(619, 46, 'col', 33, 'fairstats_schools_private', '', 0, 0, 0, 0, 1, '', ' '),
(620, 46, 'col', 34, 'fairstats_schools_districts', '', 0, 0, 0, 0, 1, '', ' '),
(621, 46, 'col', 35, 'fairstats_studentsvisiting', '', 0, 0, 0, 0, 1, '', ' '),
(622, 46, 'col', 36, 'fairstats_publicvisiting', '', 0, 0, 0, 0, 1, '', ' '),
(623, 46, 'col', 37, 'fairstats_teacherssupporting', '', 0, 0, 0, 0, 1, '', ' '),
(624, 46, 'col', 38, 'fairstats_increasedinterest', '', 0, 0, 0, 0, 1, '', ' '),
(625, 46, 'col', 39, 'fairstats_consideringcareer', '', 0, 0, 0, 0, 1, '', ' '),
(626, 46, 'col', 40, 'fairstats_committee_members', '', 0, 0, 0, 0, 1, '', ' '),
(627, 46, 'col', 41, 'fairstats_judges', '', 0, 0, 0, 0, 1, '', ' '),
(628, 46, 'col', 42, 'fairstats_next_chair_name', '', 0, 0, 0, 0, 1, '', ' '),
(629, 46, 'col', 43, 'fairstats_next_chair_email', '', 0, 0, 0, 0, 1, '', ' '),
(630, 46, 'col', 44, 'fairstats_next_chair_hphone', '', 0, 0, 0, 0, 1, '', ' '),
(631, 46, 'col', 45, 'fairstats_next_chair_bphone', '', 0, 0, 0, 0, 1, '', ' '),
(632, 46, 'col', 46, 'fairstats_next_chair_fax', '', 0, 0, 0, 0, 1, '', ' '),
(633, 46, 'col', 47, 'fairstats_delegate1', '', 0, 0, 0, 0, 1, '', ' '),
(634, 46, 'col', 48, 'fairstats_delegate1_email', '', 0, 0, 0, 0, 1, '', ' '),
(635, 46, 'col', 49, 'fairstats_delegate1_size', '', 0, 0, 0, 0, 1, '', ' '),
(636, 46, 'col', 50, 'fairstats_delegate2', '', 0, 0, 0, 0, 1, '', ' '),
(637, 46, 'col', 51, 'fairstats_delegate2_email', '', 0, 0, 0, 0, 1, '', ' '),
(638, 46, 'col', 52, 'fairstats_delegate2_size', '', 0, 0, 0, 0, 1, '', ' '),
(639, 46, 'col', 53, 'fairstats_delegate3', '', 0, 0, 0, 0, 1, '', ' '),
(640, 46, 'col', 54, 'fairstats_delegate3_email', '', 0, 0, 0, 0, 1, '', ' '),
(641, 46, 'col', 55, 'fairstats_delegate3_size', '', 0, 0, 0, 0, 1, '', ' '),
(642, 46, 'col', 56, 'fairstats_delegate4', '', 0, 0, 0, 0, 1, '', ' '),
(643, 46, 'col', 57, 'fairstats_delegate4_email', '', 0, 0, 0, 0, 1, '', ' '),
(644, 46, 'col', 58, 'fairstats_delegate4_size', '', 0, 0, 0, 0, 1, '', ' '),
(645, 46, 'sort', 0, 'fair_name', '', 0, 0, 0, 0, 1, '', ' '),
(646, 47, 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
(647, 47, 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
(648, 47, 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
(649, 47, 'option', 3, 'fit_columns', 'no', 0, 0, 0, 0, 0, '', ''),
(650, 47, 'option', 4, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
(651, 47, 'option', 5, 'field_box', 'no', 0, 0, 0, 0, 0, '', ''),
(652, 47, 'option', 6, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
(653, 47, 'option', 7, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
(654, 47, 'option', 8, 'stock', '5163', 0, 0, 0, 0, 0, '', ''),
(655, 47, 'col', 0, 'namefl', '', 5, 5, 95, 12, 1, '', 'left vcenter'),
(656, 47, 'col', 1, 'address', '', 5, 30, 95, 24, 2, '', 'left vcenter'),
(657, 47, 'col', 2, 'city_prov', '', 5, 60, 95, 12, 1, '', 'left vcenter'),
(658, 47, 'col', 3, 'postal', '', 5, 80, 95, 12, 1, '', 'left vcenter'),
(659, 47, 'col', 4, 'user_filter', '', 99, 99, 1, 1, 1, '', 'center vcenter');

-- --------------------------------------------------------

--
-- Table structure for table `safety`
--

CREATE TABLE IF NOT EXISTS `safety` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `safetyquestions_id` int(10) unsigned NOT NULL default '0',
  `answer` varchar(32) NOT NULL default '',
  `year` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `safety`
--


-- --------------------------------------------------------

--
-- Table structure for table `safetyquestions`
--

CREATE TABLE IF NOT EXISTS `safetyquestions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` int(10) unsigned NOT NULL default '0',
  `question` text NOT NULL,
  `type` enum('check','yesno') NOT NULL default 'check',
  `required` enum('no','yes') NOT NULL default 'yes',
  `ord` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `safetyquestions`
--


-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE IF NOT EXISTS `schools` (
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
  `designate` enum('','public','independent','home') character set utf8 NOT NULL,
  `principal` varchar(64) NOT NULL default '',
  `principal_uid` int(11) default NULL,
  `sciencehead_uid` int(11) default NULL,
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
  `atrisk` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `schools`
--


-- --------------------------------------------------------

--
-- Table structure for table `signaturepage`
--

CREATE TABLE IF NOT EXISTS `signaturepage` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL default '',
  `use` tinyint(4) NOT NULL default '1',
  `text` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `signaturepage`
--

INSERT INTO `signaturepage` (`id`, `name`, `use`, `text`) VALUES
(1, 'exhibitordeclaration', 1, 'The following section is to be read and signed by the exhibitor(s).\r\n\r\nI/We certify that:\r\n - The preparation of this project is mainly my/our own work.\r\n - I/We have read the rules and regulations and agree to abide by them.\r\n - I/We agree agree that the decision of the judges will be final.'),
(2, 'parentdeclaration', 1, 'The following is to be read and signed by the exhibitor(s) parent(s)/guardian(s).\r\nAs a parent/guardian I certify to the best of my knowledge and believe the information contained in this application is correct, and the project is the work of the student.  I also understand that the material used in the project is the responsibility of the student and that neither the school, the teacher, nor the regional fair can be held responsible for loss, damage, or theft, however caused. I further understand that all exhibits entered must be left on display until the end of the Fair. If my son/daughter does not remove the exhibit at the end of the Fair, the fair organizers or the owner of the exhibition hall cannot be responsible for the disposal of the exhibit.\r\n\r\nIf my son/daughter is awarded the honour of having his/her exhibit chosen for presentation at the Canada-Wide Science Fair, I consent to having him/her journey to the Fair, and will not hold the Fair responsible for any accident or mishap to the student or the exhibit.'),
(3, 'teacherdeclaration', 0, 'The following section is to be read and signed by the teacher.\r\n\r\nI certify that:\r\n - The preparation of this project is mainly the student(s)'' own work.\r\n - The student(s) have read the rules and regulations and agree to abide by them.\r\n - I agree that the decision of the judges will be final.'),
(4, 'postamble', 0, 'Please send the signed signature form and any required payment to: \n\n[Insert Address Here]'),
(5, 'regfee', 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `sponsors`
--

CREATE TABLE IF NOT EXISTS `sponsors` (
  `id` int(11) NOT NULL auto_increment,
  `organization` varchar(128) NOT NULL default '',
  `phone` varchar(32) NOT NULL default '',
  `tollfree` varchar(32) NOT NULL,
  `fax` varchar(32) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `website` varchar(128) NOT NULL,
  `year` int(11) NOT NULL default '0',
  `address` varchar(128) NOT NULL default '',
  `address2` varchar(128) NOT NULL,
  `city` varchar(64) NOT NULL default '',
  `province_code` char(2) NOT NULL default '',
  `postalcode` varchar(8) NOT NULL default '',
  `notes` text NOT NULL,
  `donationpolicyurl` varchar(255) NOT NULL,
  `fundingselectiondate` date default NULL,
  `logo` varchar(128) default NULL,
  `waiveraccepted` enum('no','yes') NOT NULL default 'no',
  `donortype` enum('organization','individual') NOT NULL default 'organization',
  `proposalsubmissiondate` date NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `sponsors`
--


-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE IF NOT EXISTS `students` (
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
  `fairs_id` int(11) NOT NULL,
  `tshirt` varchar(32) NOT NULL default 'medium',
  `medicalalert` varchar(255) NOT NULL default '',
  `foodreq` varchar(255) NOT NULL default '',
  `teachername` varchar(64) NOT NULL default '',
  `teacheremail` varchar(128) NOT NULL default '',
  `webfirst` enum('no','yes') NOT NULL default 'yes',
  `weblast` enum('no','yes') NOT NULL default 'yes',
  `webphoto` enum('no','yes') NOT NULL default 'yes',
  `namecheck_complete` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `students`
--


-- --------------------------------------------------------

--
-- Table structure for table `tours`
--

CREATE TABLE IF NOT EXISTS `tours` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` int(10) unsigned NOT NULL default '0',
  `name` tinytext NOT NULL,
  `num` varchar(16) NOT NULL,
  `description` text NOT NULL,
  `capacity` int(11) NOT NULL default '0',
  `grade_min` int(11) NOT NULL default '7',
  `grade_max` int(11) NOT NULL default '12',
  `contact` tinytext NOT NULL,
  `location` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tours`
--


-- --------------------------------------------------------

--
-- Table structure for table `tours_choice`
--

CREATE TABLE IF NOT EXISTS `tours_choice` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `students_id` int(10) unsigned NOT NULL default '0',
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `tour_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  `rank` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `tours_choice`
--


-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE IF NOT EXISTS `translations` (
  `lang` char(2) NOT NULL default '',
  `strmd5` varchar(32) NOT NULL default '',
  `str` text NOT NULL,
  `val` text NOT NULL,
  `argsdesc` text,
  PRIMARY KEY  (`strmd5`),
  UNIQUE KEY `lang` (`lang`,`strmd5`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `translations`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `types` set('student','judge','committee','volunteer','fair','sponsor','principal','teacher','parent','mentor','alumni') NOT NULL,
  `salutation` varchar(8) NOT NULL,
  `firstname` varchar(32) NOT NULL default '',
  `lastname` varchar(32) NOT NULL default '',
  `sex` enum('male','female') default NULL,
  `username` varchar(128) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `passwordset` date default NULL,
  `oldpassword` varchar(32) NOT NULL,
  `email` varchar(128) NOT NULL default '',
  `year` int(11) NOT NULL,
  `phonehome` varchar(32) NOT NULL default '',
  `phonework` varchar(32) NOT NULL default '',
  `phonecell` varchar(32) NOT NULL default '',
  `fax` varchar(32) NOT NULL default '',
  `organization` varchar(64) NOT NULL default '',
  `birthdate` date NOT NULL,
  `lang` varchar(2) NOT NULL,
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `lastlogin` datetime NOT NULL default '0000-00-00 00:00:00',
  `address` varchar(64) NOT NULL default '',
  `address2` varchar(64) NOT NULL default '',
  `city` varchar(64) NOT NULL default '',
  `province` varchar(32) NOT NULL default '',
  `postalcode` varchar(8) NOT NULL default '',
  `firstaid` enum('no','yes') NOT NULL default 'no',
  `cpr` enum('no','yes') NOT NULL default 'no',
  `deleted` enum('no','yes') NOT NULL default 'no',
  `deleteddatetime` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`,`year`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `uid`, `types`, `salutation`, `firstname`, `lastname`, `sex`, `username`, `password`, `passwordset`, `oldpassword`, `email`, `year`, `phonehome`, `phonework`, `phonecell`, `fax`, `organization`, `birthdate`, `lang`, `created`, `lastlogin`, `address`, `address2`, `city`, `province`, `postalcode`, `firstaid`, `cpr`, `deleted`, `deleteddatetime`) VALUES
(1, 1, 'fair', '', '', '', NULL, 'kvGbxRTM', '5kyYcbBAmf4Y', '0000-00-00', '', '', 0, '', '', '', '', '', '0000-00-00', '', '2009-10-22 12:56:09', '0000-00-00 00:00:00', '', '', '', '', '', 'no', 'no', 'no', NULL),
(2, 2, 'fair', '', '', '', NULL, 'k5HPLPGm', 'EUuqF2J5HbGD', '0000-00-00', '', '', 0, '', '', '', '', '', '0000-00-00', '', '2009-10-22 12:56:09', '0000-00-00 00:00:00', '', '', '', '', '', 'no', 'no', 'no', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users_alumni`
--

CREATE TABLE IF NOT EXISTS `users_alumni` (
  `users_id` int(11) NOT NULL,
  `alumni_active` enum('no','yes') NOT NULL,
  `alumni_complete` enum('no','yes') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_alumni`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_committee`
--

CREATE TABLE IF NOT EXISTS `users_committee` (
  `users_id` int(11) NOT NULL,
  `committee_active` enum('no','yes') NOT NULL default 'no',
  `committee_complete` enum('no','yes') NOT NULL default 'no',
  `emailprivate` varchar(128) NOT NULL,
  `ord` int(11) NOT NULL,
  `displayemail` enum('no','yes') NOT NULL default 'no',
  `access_admin` enum('no','yes') NOT NULL default 'no',
  `access_config` enum('no','yes') NOT NULL default 'no',
  `access_super` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_committee`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_fair`
--

CREATE TABLE IF NOT EXISTS `users_fair` (
  `users_id` int(11) NOT NULL default '0',
  `fair_active` enum('no','yes') NOT NULL default 'no',
  `fair_complete` enum('no','yes') NOT NULL default 'no',
  `fairs_id` int(11) NOT NULL,
  PRIMARY KEY  (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_fair`
--

INSERT INTO `users_fair` (`users_id`, `fair_active`, `fair_complete`, `fairs_id`) VALUES
(1, 'yes', 'no', 1),
(2, 'yes', 'no', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users_judge`
--

CREATE TABLE IF NOT EXISTS `users_judge` (
  `users_id` int(11) NOT NULL,
  `judge_active` enum('no','yes') NOT NULL default 'no',
  `judge_complete` enum('no','yes') NOT NULL default 'no',
  `years_school` tinyint(4) NOT NULL,
  `years_regional` tinyint(4) NOT NULL,
  `years_national` tinyint(4) NOT NULL,
  `willing_chair` enum('yes','no') NOT NULL default 'no',
  `special_award_only` enum('yes','no') NOT NULL default 'no',
  `cat_prefs` tinytext NOT NULL,
  `div_prefs` tinytext NOT NULL,
  `divsub_prefs` tinytext NOT NULL,
  `languages` tinytext NOT NULL,
  `highest_psd` tinytext NOT NULL,
  `expertise_other` tinytext NOT NULL,
  PRIMARY KEY  (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_judge`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_mentor`
--

CREATE TABLE IF NOT EXISTS `users_mentor` (
  `users_id` int(11) NOT NULL,
  `mentor_active` enum('no','yes') NOT NULL,
  `mentor_complete` enum('no','yes') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_mentor`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_parent`
--

CREATE TABLE IF NOT EXISTS `users_parent` (
  `users_id` int(11) NOT NULL,
  `parent_active` enum('no','yes') NOT NULL,
  `parent_complete` enum('no','yes') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_parent`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_principal`
--

CREATE TABLE IF NOT EXISTS `users_principal` (
  `users_id` int(11) NOT NULL,
  `principal_active` enum('no','yes') NOT NULL,
  `principal_complete` enum('no','yes') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_principal`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_sponsor`
--

CREATE TABLE IF NOT EXISTS `users_sponsor` (
  `users_id` int(11) NOT NULL default '0',
  `sponsors_id` int(11) NOT NULL default '0',
  `sponsor_complete` enum('no','yes') NOT NULL default 'no',
  `sponsor_active` enum('no','yes') NOT NULL default 'no',
  `primary` enum('no','yes') NOT NULL default 'no',
  `position` varchar(64) NOT NULL default '',
  `notes` text NOT NULL,
  PRIMARY KEY  (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_sponsor`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_teacher`
--

CREATE TABLE IF NOT EXISTS `users_teacher` (
  `users_id` int(11) NOT NULL,
  `teacher_active` enum('no','yes') NOT NULL,
  `teacher_complete` enum('no','yes') NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_teacher`
--


-- --------------------------------------------------------

--
-- Table structure for table `users_volunteer`
--

CREATE TABLE IF NOT EXISTS `users_volunteer` (
  `users_id` int(11) NOT NULL,
  `volunteer_active` enum('no','yes') NOT NULL default 'no',
  `volunteer_complete` enum('no','yes') NOT NULL default 'no',
  PRIMARY KEY  (`users_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_volunteer`
--


-- --------------------------------------------------------

--
-- Table structure for table `volunteer_positions`
--

CREATE TABLE IF NOT EXISTS `volunteer_positions` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `desc` text NOT NULL,
  `meet_place` text NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `volunteer_positions`
--


-- --------------------------------------------------------

--
-- Table structure for table `volunteer_positions_signup`
--

CREATE TABLE IF NOT EXISTS `volunteer_positions_signup` (
  `id` int(11) NOT NULL auto_increment,
  `users_id` int(11) NOT NULL,
  `volunteer_positions_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `volunteer_positions_signup`
--


-- --------------------------------------------------------

--
-- Table structure for table `winners`
--

CREATE TABLE IF NOT EXISTS `winners` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `awards_prizes_id` int(10) unsigned NOT NULL default '0',
  `projects_id` int(10) unsigned NOT NULL default '0',
  `year` int(10) unsigned NOT NULL default '0',
  `fairs_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `awards_prizes_id` (`awards_prizes_id`,`projects_id`,`year`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `winners`
--

