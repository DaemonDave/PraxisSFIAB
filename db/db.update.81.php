<?
function db_update_81_post() 
{
	$q = mysql_query("SELECT DISTINCT award_sponsors_id FROM award_contacts");
	while($i = mysql_fetch_object($q)) {
		$asid = $i->award_sponsors_id;
		mysql_query("UPDATE award_contacts SET `primary`='yes' WHERE award_sponsors_id='$asid' LIMIT 1");
	}
}
?>

