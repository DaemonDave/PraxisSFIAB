INSERT INTO `config` (category,ord,var,val,description,year) VALUES ('Participant Registration','1160','participant_project_table','yes','Ask if the project requires a table (yes/no)',-1);
INSERT INTO `config` (category,ord,var,val,description,year) VALUES ('Participant Registration','1170','participant_project_electricity','yes','Ask if the project requires electricity (yes/no)',-1);
UPDATE `config` SET description = 'The type of Participant Registration to use: open | singlepassword | schoolpassword | invite | openorinvite' WHERE `var` = 'participant_registration_type'
