ALTER TABLE `reports` CHANGE `type` `type` ENUM( 'student', 'judge', 'award' ) NOT NULL DEFAULT 'student';

INSERT INTO `reports` (`id`, `name`, `desc`, `creator`, `type`) VALUES
	(20, 'Judges -- Sorted by Last Name', 'A list of judge contact info, sorted by last name', 'The Grant Brothers', 'judge'),
	(21, 'Judges -- Judging Teams', 'A list of all the judges, sorted by team number.', 'The Grant Brothers', 'judge'),
	(22, 'Awards -- Grouped by Judging Team', 'List of each judging team, and the awards they are judging', 'The Grant Brothers', 'award'),
	(23, 'Awards -- Judging Teams grouped by Award', 'A list of each award, and the judging teams that will assign it', 'The Grant Brothers', 'award');

INSERT INTO `reports_items` (`id`, `reports_id`, `field`, `type`, `value`, `order`) VALUES
      ('', 20, 'type', 'option', 'pdf', 0),
      ('', 20, 'phone_work', 'col', '', 3),
      ('', 20, 'phone_home', 'col', '', 2),
      ('', 20, 'email', 'col', '', 1),
      ('', 20, 'name', 'sort', '', 0),
      ('', 20, 'group_new_page', 'option', 'no', 1),
      ('', 20, 'name', 'col', '', 0),
      ('', 20, 'complete', 'col', '', 4),
      ('', 20, 'allow_multiline', 'option', 'no', 2),
      ('', 21, 'type', 'option', 'pdf', 0),
      ('', 21, 'namefl', 'col', '', 3),
      ('', 21, 'captain', 'col', '', 2),
      ('', 21, 'team', 'col', '', 1),
      ('', 21, 'namefl', 'sort', '', 1),
      ('', 21, 'teamnum', 'sort', '', 0),
      ('', 21, 'group_new_page', 'option', 'no', 1),
      ('', 21, 'teamnum', 'col', '', 0),
      ('', 21, 'allow_multiline', 'option', 'no', 2),
      ('', 22, 'type', 'col', '', 1),
      ('', 22, 'allow_multiline', 'option', 'yes', 2),
      ('', 22, 'group_new_page', 'option', 'no', 1),
      ('', 22, 'type', 'option', 'pdf', 0),
      ('', 22, 'judgeteamnum', 'group', '', 0),
      ('', 22, 'name', 'col', '', 0),
      ('', 22, 'judgeteamname', 'group', '', 1),
      ('', 23, 'group_new_page', 'option', 'no', 1),
      ('', 23, 'judgeteamname', 'col', '', 1),
      ('', 23, 'type', 'group', '', 0),
      ('', 23, 'judgeteamnum', 'sort', '', 0),
      ('', 23, 'allow_multiline', 'option', 'yes', 2),
      ('', 23, 'name', 'group', '', 1),
      ('', 23, 'judgeteamnum', 'col', '', 0),
      ('', 23, 'type', 'option', 'pdf', 0);

