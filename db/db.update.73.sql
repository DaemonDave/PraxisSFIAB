INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '36', 'School -- Access Codes', 'List of access codes and registration passwords for all schools in the database.', 'The Grant Brothers', 'school');
INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'school', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 1, 'school_city', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 2, 'school_accesscode', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 3, 'school_registration_password', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 4, 'school_board', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'sort', 0, 'school', '', 0, 0, 0, 0, 0, '', ' ');
