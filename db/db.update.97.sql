ALTER TABLE `projects` ADD `shorttitle` VARCHAR( 255 ) NOT NULL AFTER `title` ;

UPDATE `config` SET `ord`='500' WHERE `var`='regfee_show_info';
UPDATE `config` SET `ord`='600',`type`='number' WHERE `var`='minage';
UPDATE `config` SET `ord`='700',`type`='number' WHERE `var`='maxage';
UPDATE `config` SET `ord`='800',`type`='number' WHERE `var`='mingrade';
UPDATE `config` SET `ord`='900',`type`='number' WHERE `var`='maxgrade';
UPDATE `config` SET `ord`='1000',`type`='number' WHERE `var`='minmentorsperproject';
UPDATE `config` SET `ord`='1100',`type`='number' WHERE `var`='maxmentorsperproject';
UPDATE `config` SET `ord`='1200',`type`='number' WHERE `var`='minstudentsperproject';
UPDATE `config` SET `ord`='1300',`type`='number' WHERE `var`='maxstudentsperproject';
UPDATE `config` SET `ord`='1400',`type`='number' WHERE `var`='maxspecialawardsperproject';
UPDATE `config` SET `ord`='1500' WHERE `var`='participant_student_personal';
UPDATE `config` SET `ord`='1600' WHERE `var`='participant_student_pronunciation';
UPDATE `config` SET `ord`='1700' WHERE `var`='participant_mentor';
UPDATE `config` SET `ord`='1800',`type`='number' WHERE `var`='participant_project_summary_wordmax';
UPDATE `config` SET `ord`='1900',`type`='number' WHERE `var`='participant_project_summary_wordmin';
UPDATE `config` SET `ord`='2000',`type`='number' WHERE `var`='participant_project_title_charmax';
UPDATE `config` SET `ord`='2300' WHERE `var`='participant_project_table';
UPDATE `config` SET `ord`='2400' WHERE `var`='participant_project_electricity';
UPDATE `config` SET `ord`='2500' WHERE `var`='participant_student_foodreq';
UPDATE `config` SET `ord`='2600' WHERE `var`='participant_student_tshirt';
UPDATE `config` SET `ord`='2700' WHERE `var`='participant_student_tshirt_cost';
UPDATE `config` SET `ord`='2800' WHERE `var`='specialawardnomination_aftersignatures';
UPDATE `config` SET `ord`='2900' WHERE `var`='specialawardnomination';
UPDATE `config` SET `ord`='3000' WHERE `var`='usedivisionselector';

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES (
'participant_short_title_charmax', '50', 'Participant Registration', 'number', '', '2200', 'The maximum number of characters acceptable in the short project title (Max 255)', '-1');
INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES (
'participant_short_title_enable', 'no', 'Participant Registration', 'yesno', '', '2100', 'Ask the participants for a short project title as well as their full title.', '-1');
