-- Example how to add a new report to the system, without knowing the
-- report ID.  This adds report #34 to the system, if applied directly
-- after the last SQL update

INSERT INTO `reports` (`id`, `name`, `desc`, `creator`, `type`) VALUES 
('', 'Labels -- Table Labels (small)', 'Labels to go on each table', 'The Grant Brothers', 'student');

INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES
('', LAST_INSERT_ID(), 'col', 3, 'categorydivision', '', 1, 85, 98, 7, 1, '', 'center'),
('', LAST_INSERT_ID(), 'col', 2, 'pn', '', 1, 20, 98, 35, 1, '', 'center'),
('', LAST_INSERT_ID(), 'col', 1, 'title', '', 1, 5, 98, 24, 3, '', 'center'),
('', LAST_INSERT_ID(), 'sort', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
('', LAST_INSERT_ID(), 'option', 4, 'label_fairname', 'yes', 0, 0, 0, 0, 0, '', ''),
('', LAST_INSERT_ID(), 'option', 3, 'label_box', 'yes', 0, 0, 0, 0, 0, '', ''),
('', LAST_INSERT_ID(), 'option', 2, 'allow_multiline', 'no', 0, 0, 0, 0, 0, '', ''),
('', LAST_INSERT_ID(), 'col', 0, 'bothnames', '', 1, 70, 98, 14, 2, '', 'center'),
('', LAST_INSERT_ID(), 'distinct', 0, 'pn', '', 0, 0, 0, 0, 0, '', ''),
('', LAST_INSERT_ID(), 'option', 5, 'label_logo', 'yes', 0, 0, 0, 0, 0, '', ''),
('', LAST_INSERT_ID(), 'option', 1, 'group_new_page', 'no', 0, 0, 0, 0, 0, '', ''),
('', LAST_INSERT_ID(), 'option', 0, 'type', 'label', 0, 0, 0, 0, 0, '', ''),
('', LAST_INSERT_ID(), 'option', 6, 'stock', '5964', 0, 0, 0, 0, 0, '', '');

