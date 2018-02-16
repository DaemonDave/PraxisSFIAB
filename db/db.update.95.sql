INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '43', 'T-Shirt Size Count', 'A list of tshirt sizes (the blank entry is those students who have selected \"none\"), and the number of tshirts of each size.', 'The Grant Brothers', 'student');

INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'tshirt', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 1, 'special_tshirt_count', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'sort', 0, 'tshirt', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'filter', 0, 'tshirt', 'none', 5, 0, 0, 0, 1, '', ' ');
