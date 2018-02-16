ALTER TABLE `emails` CHANGE `body` `body` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emails` CHANGE `bodyhtml` `bodyhtml` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emails` CHANGE `subject` `subject` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emailqueue` CHANGE `body` `body` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emailqueue` CHANGE `bodyhtml` `bodyhtml` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emailqueue` CHANGE `subject` `subject` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emailqueue_recipients` CHANGE `replacements` `replacements` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
