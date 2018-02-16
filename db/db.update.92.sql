INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES 
( 'project_sort_format', '', 'Global', 'text', '', '610', 'Project Sorting Format. This format will be used to sort the projects on lists and in reports. Use the same letters as the Project Number Format (C, D, N, etc.). If left blank, the project number format will also be used to sort the projects.', '-1');

UPDATE `config` SET `description` = 'Project Numbering Format: C=Category ID, c=Category shortform, D=Division ID, d=Division shortform, N, N1, N2, ..., N9=intra division digit sequence number, zero padded to 1-9 digits, or 2 digits if just N is used. X, X1, X2, ..., N9=global sequence number, zero padded to 1-9 digits, or 3 digits if just X is used.' WHERE `var`='project_num_format' ;

ALTER TABLE `projects` ADD `projectsort` VARCHAR( 16 ) NULL AFTER `projectnumber` ;
ALTER TABLE `projects` ADD `projectnumber_seq` INT NOT NULL AFTER `projectsort` ;
ALTER TABLE `projects` ADD `projectsort_seq` INT NOT NULL AFTER `projectnumber_seq` ;

UPDATE projects SET projectsort=projectnumber;

UPDATE reports_items SET field='pn' WHERE field='gvrsf_tn';

