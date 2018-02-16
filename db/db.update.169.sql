ALTER TABLE `reports_items` ADD `fontname` VARCHAR( 32 ) NOT NULL AFTER `face` ,
	ADD `fontstyle` SET( 'bold', 'italic', 'underline', 'strikethrough') NOT NULL AFTER `fontname` ,
	ADD `fontsize` FLOAT NOT NULL AFTER `fontstyle` ;

ALTER TABLE `reports_items` ADD `valign` ENUM( 'top', 'middle', 'bottom' ) NOT NULL;

ALTER TABLE `reports_items` ADD `on_overflow` ENUM( 'truncate', '...', 'scale' ) NOT NULL;

