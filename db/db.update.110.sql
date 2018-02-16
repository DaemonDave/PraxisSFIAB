UPDATE `config` SET category='Localization', ord='100' WHERE var='provincestate';
UPDATE `config` SET category='Localization', ord='110' WHERE var='postalzip';
INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES (
'dateformat', 'Y-m-d', 'Localization', 'text', '', '200', 'Date format (<a href="http://www.php.net/manual/en/function.date.php" target="_blank">formatting options</a>)', '-1');
INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES (
'timeformat', 'H:i:s', 'Localization', 'text', '', '210', 'Time format (<a href="http://www.php.net/manual/en/function.date.php" target="_blank">formatting options</a>)', '-1');

