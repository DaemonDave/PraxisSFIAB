<?
$campaign_types=array("Mail","Email","Phone","Personal Visit","Event","Other");
$salutations=array("Mr.","Mrs.","Ms","Dr.","Professor");

function getGoal($goal) {
	global $config;
	$q=mysql_query("SELECT * FROM fundraising_goals WHERE goal='$goal' AND fiscalyear='{$config['FISCALYEAR']}' LIMIT 1");
	return mysql_fetch_object($q);
}

?>
