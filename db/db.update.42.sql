ALTER TABLE `students` ADD `pronunciation` VARCHAR( 64 ) NOT NULL AFTER `lastname`;

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year` ) VALUES ( 'participant_student_pronunciation', 'no', 'Participant Registration', 'yesno', '', '1020', 'Ask the student for a pronunciation key for their name (for award ceremonies)', '-1');

