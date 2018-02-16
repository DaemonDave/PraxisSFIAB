SELECT @id:=id FROM reports WHERE system_report_id='18';
UPDATE `reports_items` SET `field`='grade_str' WHERE `field`='grade' AND `reports_id`=@id;


