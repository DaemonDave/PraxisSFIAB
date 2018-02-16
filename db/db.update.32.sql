INSERT INTO `config` (category,ord,var,val,description,year) VALUES ('Participant Registration','1050','participant_mentor','yes','Ask for mentorship information (yes/no)',-1);
ALTER TABLE `projectcategories` ADD `category_shortform` VARCHAR( 3 ) NOT NULL AFTER `category` ;
UPDATE `config` SET `description` = 'C=Category ID, c=Category Shortform, D=Division ID, d=Division Shortform, N=2 digit Number' WHERE `var` = 'project_num_format';
INSERT INTO `config` (category,ord,var,val,description,year) VALUES ('Participant Registration','1150','participant_project_title_charmax','100','The maximum number of characters acceptable in the project title (Max 255)',-1);

