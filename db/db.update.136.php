<?


function db_update_136_pre()
{
	global $config;
	mysql_query("UPDATE fairs SET `name` = 'Youth Science Canada',
				`abbrv` = 'YSC',
				`website` = 'http://apps.ysf-fsj.ca/awarddownloader/help.php',
				`enable_stats` = 'yes',
				`enable_awards` = 'yes',
				`enable_winners` = 'yes',
				`username` = '{$config['ysf_region_id']}',
				`password` = '{$config['ysf_region_password']}'

			WHERE 
				`url`='https://secure.ysf-fsj.ca/awarddownloader/index.php'");

	mysql_query("UPDATE fairs SET `abbrv` = 'STO',
				`website` = 'http://www.scitechontario.org/awarddownloader/help.php',
				`enable_stats` = 'yes',
				`enable_awards` = 'yes',
				`enable_winners` = 'yes'
			WHERE 
				`url`='http://www.scitechontario.org/awarddownloader/index.php'");
}
	

function db_update_136_post()
{
}


?>
