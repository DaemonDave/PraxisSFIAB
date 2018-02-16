SELECT @id:=id FROM reports WHERE system_report_id='19';
INSERT INTO `reports_items` ( `id` , `reports_id` , `type` , `ord` , `field` , `value` , `x` , `y` , `w` , `h` , `lines` , `face` , `align`) VALUES 
	( NULL , @id, 'distinct', '0', 'pn', '', '0', '0', '0', '0', '0', '', '');

UPDATE reports_items SET value='fullpage' WHERE field='stock' AND value='letter';
UPDATE reports_items SET value='5164' WHERE field='stock' AND value='5964';
UPDATE reports_items SET value='5161' WHERE field='stock' AND value='5961';

