ALTER TABLE `pagetext` ADD `textdescription` VARCHAR( 255 ) NOT NULL AFTER `textname` ;
UPDATE `pagetext` SET `textdescription`='Participant registration main page instructions' WHERE textname='register_participants_main_instructions';
UPDATE `pagetext` SET `textdescription`='Judge registration instructions for Invite-Only mode' WHERE textname='register_judges_invite';
UPDATE `pagetext` SET `textdescription`='Volunteer registration instructions for Invite-Only mode' WHERE textname='register_volunteer_invite';
UPDATE `pagetext` SET `textdescription`='School access login page' WHERE textname='schoolaccess';
