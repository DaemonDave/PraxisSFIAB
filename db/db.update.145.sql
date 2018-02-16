ALTER TABLE `fundraising_donor_logs` ADD `type` VARCHAR( 32 ) NOT NULL;
ALTER TABLE `fundraising_donor_logs` ADD `fundraising_campaigns_id` INT UNSIGNED NULL;
ALTER TABLE `fundraising_donations` ADD `thanked` ENUM( 'no', 'yes' ) NOT NULL DEFAULT 'no';
ALTER TABLE `fundraising_donations` ADD `datereceived` DATE NULL DEFAULT NULL;
ALTER TABLE `fundraising_campaigns` ADD `followupdate` DATE NULL DEFAULT NULL AFTER `enddate`; 
UPDATE `config` SET category='Fundraising' WHERE var='fiscal_yearend';
INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES ( 'registered_charity', 'no', 'Fundraising', 'yesno', '', '100', 'Is your organization a registered charity?', '-1');
INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES ( 'charity_number', '', 'Fundraising', 'text', '', '200', 'Charity Registration Number', '-1');

