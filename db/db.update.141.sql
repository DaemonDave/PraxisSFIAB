INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES ( 'fiscal_yearend', '', 'Global', 'text', '', '200', 'Your organization''s fiscal year end. Specified in format MM-DD. Must be set in order for the Fundraising Module to function.', '-1');
ALTER TABLE `sponsors` ADD `donortype` ENUM( 'organization', 'individual' ) NOT NULL DEFAULT 'organization';
ALTER TABLE `sponsors` ADD `address2` VARCHAR( 128 ) NOT NULL AFTER `address` ;
