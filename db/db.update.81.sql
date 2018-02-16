ALTER TABLE `award_contacts` ADD `primary` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no' AFTER `notes` ;

INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '37', 'Awards -- Award Sponsor Information', 'Sponsor information for each award with the primary contact.  This is a large report so the default format is CSV.', 'The Grant Brothers', 'award');
INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'name', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 1, 'sponsor_organization', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 2, 'sponsor_phone', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 3, 'sponsor_fax', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 4, 'sponsor_address', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 5, 'sponsor_city', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 6, 'sponsor_province', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 7, 'sponsor_postal', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 8, 'sponsor_notes', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 9, 'sponsor_confirmed', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 10, 'pcontact_salutation', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 11, 'pcontact_namefl', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 12, 'pcontact_position', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 13, 'pcontact_email', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 14, 'pcontact_hphone', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 15, 'pcontact_wphone', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 16, 'pcontact_cphone', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 17, 'pcontact_fax', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'col', 18, 'pcontact_notes', '', 0, 0, 0, 0, 0, '', ' '),
	('', LAST_INSERT_ID(), 'sort', 0, 'name', '', 0, 0, 0, 0, 0, '', ' ');

