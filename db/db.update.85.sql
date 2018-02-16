ALTER TABLE `tours` ADD `num` VARCHAR( 16 ) NOT NULL AFTER `name` ;

INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '38', 'Tours -- All Tour Information', 'A listing of just the tours and all related info, no student assignments or anything.', 'The Grant Brothers', 'tour');
INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'tour_num', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 1, 'tour_name', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 2, 'tour_capacity', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 3, 'tour_mingrade', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 4, 'tour_maxgrade', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 5, 'tour_desc', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 6, 'tour_location', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 7, 'tour_contact', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'sort', 0, 'tour_name', '', 0, 0, 0, 0, 0, '', ' ');

INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '39', 'Tours -- Available Tours', 'A list of just the tour names and numbers for fair day', 'The Grant Brothers', 'tour');
INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'tour_num', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 1, 'tour_name', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'sort', 0, 'tour_id', '', 0, 0, 0, 0, 0, '', ' ');

INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '40', 'Tours -- Student Emergency Contact Information', 'Emergency contact information for each tour, each tour starting on a new page.', 'The Grant Brothers', 'student');
INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'yes', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 1, 'namefl', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 2, 'emerg_name', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 3, 'emerg_relation', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 4, 'emerg_phone', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'group', 0, 'tour_assign_numname', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'sort', 0, 'last_name', '', 0, 0, 0, 0, 0, '', ' ');

INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '41', 'Tours -- Student Tour Assignments', 'Participant and Tour Assignments, grouped by age category, sorted by project number', 'The Grant Brothers', 'student');
INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'pn', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 1, 'namefl', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 2, 'tour_assign_numname', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'group', 0, 'category', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ' ');

