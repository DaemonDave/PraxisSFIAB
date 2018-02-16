DELETE FROM reports_items WHERE field = 'sponsor_confirmed';

ALTER TABLE `fairs_stats` CHANGE `next_chairemail` `next_chair_email` VARCHAR( 64 ) NOT NULL ;
UPDATE `reports_items` SET field = 'fairstats_next_chair_email' WHERE field = 'fairstats_next_chairemail';

