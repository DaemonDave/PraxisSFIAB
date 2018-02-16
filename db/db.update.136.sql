ALTER TABLE `fairs` CHANGE `type` `type` ENUM( 'feeder', 'sfiab', 'ysc' ) NOT NULL ;

UPDATE fairs SET `type`='ysc' WHERE `type`='';

DELETE FROM config WHERE var='ysf_region_id';
DELETE FROM config WHERE var='ysf_region_password';
