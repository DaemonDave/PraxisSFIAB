INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES
	('', '42', 'Winners -- Award Ceremony Presentation Data', 'A CSV dump of all the winners and their prizes.  Useful for importing into an award ceremony presentation, or a document.', 'The Grant Brothers', 'student');
INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES 
	('', LAST_INSERT_ID(), 'option', 0, 'type', 'csv', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'no', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'option', 6, 'stock', 'fullpage', 0, 0, 0, 0, 0, '', ''),
	('', LAST_INSERT_ID(), 'col', 0, 'division', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 1, 'fr_division', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 2, 'category', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 3, 'fr_category', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 4, 'award_name', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 5, 'award_prize_name', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 6, 'award_prize_cash', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 7, 'award_prize_scholarship', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 8, 'award_prize_value', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 9, 'pn', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 10, 'title', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 11, 'namefl', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 12, 'partnerfl', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 13, 'school', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 14, 'school_city', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 15, 'school_province', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 16, 'school_board', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'col', 17, 'school_postal', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'sort', 0, 'order', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'distinct', 0, 'pn', '', 0, 0, 0, 0, 1, '', ' '),
	('', LAST_INSERT_ID(), 'filter', 0, 'award_excludefromac', 'no', 0, 0, 0, 0, 1, '', ' ');

SELECT @id:=id FROM reports WHERE system_report_id='42';
UPDATE reports_committee SET reports_id=@id WHERE reports_id='-9';

