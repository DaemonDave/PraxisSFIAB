ALTER TABLE `reports` CHANGE `type` `type` ENUM( 'student', 'judge', 'award', 'committee', 'school' ) NOT NULL DEFAULT 'student'
