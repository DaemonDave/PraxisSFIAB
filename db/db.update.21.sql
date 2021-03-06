ALTER TABLE `config` ADD `category` VARCHAR( 32 ) NOT NULL AFTER `val` , ADD `ord` INT NOT NULL AFTER `category` ;
UPDATE `config` SET `category` = 'Special', ord='0' WHERE `year`=0;
UPDATE `config` SET `category` = 'Global', ord='100' WHERE var='fairname';
UPDATE `config` SET `category` = 'Global', ord='200' WHERE var='default_language';
UPDATE `config` SET `category` = 'Global', ord='300' WHERE var='fairmanageremail';
UPDATE `config` SET `category` = 'Global', ord='400' WHERE var='filterdivisionbycategory';
UPDATE `config` SET `category` = 'Global', ord='500' WHERE var='committee_publiclayout';
UPDATE `config` SET `category` = 'Global', ord='600' WHERE var='project_num_format';
UPDATE `config` SET `category` = 'Judge Scheduler', ord='100' WHERE var='JSCHEDULER_effort';
UPDATE `config` SET `category` = 'Judge Scheduler', ord='200' WHERE var='JSCHEDULER_min_judges_per_team';
UPDATE `config` SET `category` = 'Judge Scheduler', ord='300' WHERE var='JSCHEDULER_max_judges_per_team';
UPDATE `config` SET `category` = 'Judge Scheduler', ord='400' WHERE var='JSCHEDULER_max_projects_per_team';
UPDATE `config` SET `category` = 'Judge Scheduler', ord='500' WHERE var='JSCHEDULER_times_judged';
UPDATE `config` SET `category` = 'Judge Registration', ord='100' WHERE var='judge_registration_type';
UPDATE `config` SET `category` = 'Judge Registration', ord='200' WHERE var='judge_registration_singlepassword';
UPDATE `config` SET `category` = 'Judge Registration', ord='300' WHERE var='judges_password_expiry_days';
UPDATE `config` SET `category` = 'Judge Registration', ord='400' WHERE var='minjudgeage';
UPDATE `config` SET `category` = 'Judge Registration', ord='500' WHERE var='maxjudgeage';
UPDATE `config` SET `category` = 'Participant Registration', ord='100' WHERE var='participant_registration_type';
UPDATE `config` SET `category` = 'Participant Registration', ord='200' WHERE var='participant_registration_singlepassword';
UPDATE `config` SET `category` = 'Participant Registration', ord='300' WHERE var='regfee';
UPDATE `config` SET `category` = 'Participant Registration', ord='400' WHERE var='regfee_per';
UPDATE `config` SET `category` = 'Participant Registration', ord='500' WHERE var='minage';
UPDATE `config` SET `category` = 'Participant Registration', ord='501' WHERE var='maxage';
UPDATE `config` SET `category` = 'Participant Registration', ord='600' WHERE var='mingrade';
UPDATE `config` SET `category` = 'Participant Registration', ord='601' WHERE var='maxgrade';
UPDATE `config` SET `category` = 'Participant Registration', ord='700' WHERE var='minmentorsperproject';
UPDATE `config` SET `category` = 'Participant Registration', ord='701' WHERE var='maxmentorsperproject';
UPDATE `config` SET `category` = 'Participant Registration', ord='800' WHERE var='minstudentsperproject';
UPDATE `config` SET `category` = 'Participant Registration', ord='801' WHERE var='maxstudentsperproject';
UPDATE `config` SET `category` = 'Participant Registration', ord='900' WHERE var='maxspecialawardsperproject';
UPDATE `config` SET `category` = 'Participant Registration', ord='1000' WHERE var='participant_student_personal';
UPDATE `config` SET `category` = 'Participant Registration', ord='1100' WHERE var='participant_project_summary_wordmax';
UPDATE `config` SET `category` = 'Participant Registration', ord='1200' WHERE var='participant_student_foodreq';
UPDATE `config` SET `category` = 'Participant Registration', ord='1300' WHERE var='participant_student_tshirt';
UPDATE `config` SET `category` = 'Participant Registration', ord='1400' WHERE var='specialawardnomination';
UPDATE `config` SET `category` = 'Participant Registration', ord='1500' WHERE var='usedivisionselector';

UPDATE `config` SET var='effort' WHERE var='JSCHEDULER_effort';
UPDATE `config` SET var='min_judges_per_team' WHERE var='JSCHEDULER_min_judges_per_team';
UPDATE `config` SET var='max_judges_per_team' WHERE var='JSCHEDULER_max_judges_per_team';
UPDATE `config` SET var='max_projects_per_team' WHERE var='JSCHEDULER_max_projects_per_team';
UPDATE `config` SET var='times_judged' WHERE var='JSCHEDULER_times_judged';

