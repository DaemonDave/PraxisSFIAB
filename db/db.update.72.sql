INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '35', 'School -- All Schools', 'List of all schools in the database.  Name, address, contact person (Principal or Science Head) and a contact phone (school phone or science head phone)', 'The Grant Brothers', 'school');
INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'pdf', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'yes', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'school', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 1, 'schooladdr', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 2, 'school_contact', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 3, 'school_contactphone', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'sort', 0, 'school', '', 0, 0, 0, 0, 0, '', ' ');

UPDATE `reports` SET `desc` = 'School Mailing Addresses ONLY for schools attached to registered students (NOT ALL SCHOOLS) with a blank spot for the teacher''s name, since each student apparently spells their teacher''s name differently.' WHERE `system_report_id` =24;

