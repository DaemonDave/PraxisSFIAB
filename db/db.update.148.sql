ALTER TABLE `fundraising_donations` DROP `users_uid` ;
ALTER TABLE `fundraising_donations` ADD `receiptrequired` ENUM( 'no', 'yes' ) NOT NULL AFTER `thanked` ;
ALTER TABLE `fundraising_donations` ADD `receiptsent` ENUM( 'no', 'yes' ) NOT NULL AFTER `receiptrequired` ;

