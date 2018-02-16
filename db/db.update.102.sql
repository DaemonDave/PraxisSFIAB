SELECT @id:=id FROM reports WHERE system_report_id='44';
UPDATE reports_items SET `value`='letter_4up' WHERE `field`='stock' AND `reports_id`=@id;


