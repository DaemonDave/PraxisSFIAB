ALTER TABLE `config` CHANGE `type` `type` ENUM( '', 'yesno', 'number', 'text', 'enum', 'multisel', 'language', 'theme' ) NOT NULL;

UPDATE `config` SET `type` = 'theme', `type_values` = 'theme', `description` = 'Theme for colours' WHERE `config`.`var` = 'theme';

INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`)
	VALUES ( 'theme_icons', 'icons_default', 'Global', 'theme', 'icons', '860', 'Icon set', '-1');


INSERT INTO `config` (`var`, `val`, `category`, `type`, `type_values`, `ord`, `description`, `year`) VALUES
('fairs_allow_login', 'no', 'Science Fairs', 'yesno', '', 200, 'Allow feeder fairs to login an enter stats and winners.  If set to ''no'', they will only be able to download and upload awards using the SFIAB award download/upload mechanism.', -1),
('fairs_name', 'Science', 'Feeder Fairs', 'text', '', 300, 'What level the feeder fairs are.  For example, ''School'' , ''Regional'', or just ''Science'' for a generic ''Science Fair''', -1),
('fairs_enable', 'no', 'Science Fairs', 'yesno', '', 100, 'Enable the Science Fair.  Science Fairs can download awards tagged as ''downloadable'', and can upload winners of those awards directly into this system (optionally creating accounts for all students).  There are also options to collect stats from these fairs.', -1);

