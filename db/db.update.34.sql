-- --------------------------------------------------------

-- 
-- Table structure for table `tours` and `tours_choice`
-- 

CREATE TABLE `tours` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` int(10) unsigned NOT NULL default '0',
  `name` tinytext NOT NULL,
  `description` text NOT NULL,
  `capacity` int(11) NOT NULL default '0',
  `grade_min` int(11) NOT NULL default '7',
  `grade_max` int(11) NOT NULL default '12',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;


CREATE TABLE `tours_choice` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `students_id` int(10) unsigned NOT NULL default '0',
  `registrations_id` int(10) unsigned NOT NULL default '0',
  `tour_id` int(10) unsigned NOT NULL default '0',
  `year` int(11) NOT NULL default '0',
  `rank` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM; 


-- 
-- Dumping data for table `config`
-- 

INSERT INTO `config` (`var`, `val`, `category`, `ord`, `description`, `year`) VALUES  

('tours_enable', 'no', 'Tours', 0, 'Enable the "tours" module.  Set to "yes" to allow participants to select tours', -1),
('tours_choices_min', '1', 'Tours', 100, 'Minimum number of tours a participant must select', -1),
('tours_choices_max', '3', 'Tours', 200, 'Maximum number of tours a participant may select', -1);



