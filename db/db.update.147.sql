ALTER TABLE `reports` CHANGE `type` `type` ENUM( 'student', 'judge', 'award', 'committee', 'school', 'volunteer', 'tour', 'fair', 'fundraising' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'student';

INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '47', 'Labels -- Fundraising Campaign Mailing Labels', 'Mailing labels for all the contacts attached to a fundraising campaign', 'The Grant Brothers', 'fundraising');
INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'fit_columns', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'field_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 7, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 8, 'stock', '5163', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'namefl', '', 5, 5, 95, 12, 1, '', 'left vcenter'),
	('', LAST_INSERT_ID(), 'col', 1, 'address', '', 5, 30, 95, 24, 2, '', 'left vcenter'),
	('', LAST_INSERT_ID(), 'col', 2, 'city_prov', '', 5, 60, 95, 12, 1, '', 'left vcenter'),
	('', LAST_INSERT_ID(), 'col', 3, 'postal', '', 5, 80, 95, 12, 1, '', 'left vcenter'),
	('', LAST_INSERT_ID(), 'col', 4, 'user_filter', '', 99, 99, 1, 1, 1, '', 'center vcenter');


