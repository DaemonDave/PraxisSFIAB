CREATE TABLE `fairs` (
  `id` int(11) NOT NULL default '0',
  `fairname` varchar(64) NOT NULL default '0',
   PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `users_fair` (
  `users_id` int(11) NOT NULL default '0',
  `fairs_id` int(11) NOT NULL default '0',
   PRIMARY KEY  (`users_id`)
) TYPE=MyISAM;

ALTER TABLE `users` CHANGE `types` `types` SET( 'student', 'judge', 'committee', 'volunteer', 'fair' ) NOT NULL ;

