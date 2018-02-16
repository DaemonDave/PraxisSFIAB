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
) TYPE=MyISAM;


-- 
-- Dumping data for table `config`
-- 

INSERT INTO `config` (`var`, `val`, `description`, `year`) VALUES 
('JSCHEDULER_max_projects_per_team', '7', 'The maximum number of projects that a judging team can judge.', -1),
('JSCHEDULER_times_judged', '1', 'The number of times each project must be judged by different judging teams.', -1),
('JSCHEDULER_min_judges_per_team', '3', 'The minimum number of judges that can be on a judging team.', -1),
('JSCHEDULER_max_judges_per_team', '3', 'The maximum number of judges that can be on a judging team.', -1),
('JSCHEDULER_effort', '10000', 'This number controls how long and hard the judge scheduler will look for a scheduling solution.  Smaller numbers are lower effort.  100 is practically no effort, 1000 is moderate effort, 10000 is high effort.  It can take several tens of minutes to run the scheduler with high effort, but it gives a very good solution.', -1);

