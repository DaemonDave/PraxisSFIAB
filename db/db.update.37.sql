-- Add type and type_values fields to the config editor.
ALTER TABLE `config` ADD `type` ENUM( '', 'yesno', 'number', 'text', 'enum' ) NOT NULL AFTER `category` ,
ADD `type_values` TINYTEXT NOT NULL AFTER `type` ;

UPDATE `config` SET `type` = 'yesno',
`description` = 'Ask for students special food requirements. Should be ''Yes'' if you plan on providing food to the students.' WHERE `var`='participant_student_foodreq';

UPDATE `config` set `type` = 'yesno', `description` = 'Specify whether to use the division selector flowchart questions to help decide on the division' WHERE `var`='usedivisionselector';
UPDATE `config` set `type` = 'yesno' WHERE `var`='participant_student_personal';
UPDATE `config` set `type` = 'yesno', `description` = 'Ask for students their T-Shirt size' WHERE `var`='participant_student_tshirt';
UPDATE `config` set `type` = 'yesno', `description` = 'Ask for mentorship information' WHERE `var`='participant_mentor';
UPDATE `config` set `type` = 'yesno', `description` = 'Ask if the project requires a table' WHERE `var`='participant_project_table';
UPDATE `config` set `type` = 'yesno', `description` = 'Ask if the project requires electricity' WHERE `var`='participant_project_electricity';
UPDATE `config` set `type` = 'yesno' WHERE `var`='tours_enable';
UPDATE `config` set `type` = 'yesno', `description` = 'Allows for the setup of different divisions for each category' WHERE `var`='filterdivisionbycategory';

UPDATE `config` SET `type` = 'enum', `type_values` = 'student=Student|project=Project', `description` = 'Registration fee is per student, or per project?' WHERE `var` = 'regfee_per';

UPDATE `config` SET `type` = 'enum', `type_values` = 'open=Open|singlepassword=Single Password|schoolpassword=School Password|invite=Invite|openorinvite=Open or Invite', `description`='The type of Participant Registration to use' WHERE `var` = 'participant_registration_type';

UPDATE `config` SET `type` = 'enum', `type_values` = 'open=Open|singlepassword=Single Password|invite=Invite', `description` = 'The type of Judge Registration to use' WHERE `var` = 'judge_registration_type'; 

UPDATE `config` SET `type` = 'enum', `type_values` = 'open=Open|payment_pending=Payment Pending|complete=Complete', `description` = 'The status a project must have have to be considered eligible for judge scheduling. ' WHERE `var` = 'project_status' ;

UPDATE `config` SET `type` = 'enum', `type_values` = 'none=None|date=By Date|registration=With Registration', `description` = 'Self nominations for special awards are due either with registration ("With Registration"), or on a specific date. If "By Date" is used, it must be configured under "Important Dates" section. If you do not wish to allow students to self-nominate for special awards, set to "None"' WHERE `var` = 'specialawardnomination'; 




