INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES (
'winners_show_prize_amounts', 'yes', 'Global', 'yesno', '', '700', 'Show the dollar amounts of the cash/scholarship prizes on the publicly viewable winners page.', '-1');

UPDATE `config` SET `type` = 'enum', `type_values` = '100=Low|1000=Medium|10000=High',
`description` = 'This controls how long and hard the judge scheduler will look for a scheduling solution. Low effort will finish almost instantly but give a very poor result. High effort can take several tens of minutes to run, but it gives a very good solution.' WHERE `var` = 'effort' ;

UPDATE `config` SET `type` = 'enum', `type_values` = '100=Low|1000=Medium|10000=High',
`description` = 'This controls how long and hard the tour assigner will look for a quality solution. Low effort will finish almost instantly but give a very poor result. High effort can take several minutes to run, but it gives a very good solution. ' WHERE `var` = 'tours_assigner_effort' ;


