<?

function db_update_142_post() {
	$q=mysql_query("SELECT * FROM config WHERE var='FISCALYEAR'");
	if(mysql_num_rows($q)) {
		//great its there, do nothing, it must have been inserted by the installer when doing a fresh install
	}
	else {
		//its not there.. this must be an update to an existing system, so lets insert it
		//try to guess a fiscal that makes sense
		$month=date("m");
		if($month>6) $fiscalyearsuggest=date("Y")+1;
		else $fiscalyearsuggest=date("Y");
		mysql_query("INSERT INTO `config` ( `var` , `val` , `category` , `type` , `type_values` , `ord` , `description` , `year`) VALUES ( 'FISCALYEAR', '$fiscalyearsuggest', 'Special', '', '', '0', 'The current fiscal year that the fundraising module is using', '0')");
	}
	
}

?>
