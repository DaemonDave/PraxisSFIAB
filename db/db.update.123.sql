ALTER TABLE `config` CHANGE `type` `type` ENUM( '', 'yesno', 'number', 'text', 'enum', 'multisel', 'language' ) NOT NULL ;

UPDATE config SET `type`='language' WHERE `var`='default_language';
