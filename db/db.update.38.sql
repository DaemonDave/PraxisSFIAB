
INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` ) VALUES ( 'participant_student_tshirt_cost', '0.00', 'Participant Registration', 'number', '', '1310', 'The cost of each T-Shirt. If this is non-zero, a "None" option is added to the T-Shirt size selection box, and a note is added indicating the cost of each T-Shirt', '-1');

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` ) VALUES ( 'regfee_show_info', 'no', 'Participant Registration', 'yesno', '', '410', 'Show a breakdown of the total Registration Fee calculation on the main student registration page', '-1');

ALTER TABLE `students` CHANGE `tshirt` `tshirt` VARCHAR( 32 ) NOT NULL DEFAULT 'medium';
