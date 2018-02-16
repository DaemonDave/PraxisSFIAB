INSERT INTO `config` (`var`, `val`, `category`, `type`, `type_values`, `ord`, `description`, `year`) VALUES
('fair_stats_info', 'yes', 'Science Fairs', 'yesno', '', 600, 'Gather Stats: Information about the fair (date, location, budget, charity info).', -1),
('fair_stats_next_chair', 'yes', 'Science Fairs', 'yesno', '', 700, 'Gather Stats: Chairperson name and contact info for the next year', -1),
('fair_stats_scholarships', 'yes', 'Science Fairs', 'yesno', '', 800, 'Gather Stats: Scholarships given out by the fair', -1),
('fair_stats_delegates', 'yes', 'Science Fairs', 'yesno', '', '900', 'Gather Stats: CWSF Delegate names/email/jacket size', '-1');


ALTER TABLE `fairs` ADD `website` TINYTEXT NOT NULL AFTER `url` ;

ALTER TABLE `fairs` ADD `enable_stats` ENUM( 'no', 'yes' ) NOT NULL ,
	ADD `enable_awards` ENUM( 'no', 'yes' ) NOT NULL ,
	ADD `enable_winners` ENUM( 'no', 'yes' ) NOT NULL ;
