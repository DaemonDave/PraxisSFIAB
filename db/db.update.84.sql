INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`)
	VALUES 
	( 'tours_assigner_activity', 'Done', 'Tour Assigner', '', '', '99999', '', '0'),
	( 'tours_assigner_percent', '-1', 'Tour Assigner', '', '', '99999', '', '0'),
	( 'tours_assigner_effort', '10000', 'Tour Assigner', '', '', '99999', 'This number controls how long and hard the tour assigner will look for a quality solution. Smaller numbers are lower effort. 100 is practically no effort, 1000 is moderate effort, 10000 is high effort. It can take several tens of minutes to run the assigner with high effort, but it gives a very good solution.', '-1');

ALTER TABLE `reports` CHANGE `type` `type` ENUM( 'student', 'judge', 'award', 'committee', 'school', 'volunteer', 'tour' ) NOT NULL DEFAULT 'student' ;
