<?

require_once("../common.inc.php");
require_once("../user.inc.php");

user_auth_required('committee', 'admin');

$q = mysql_query("SELECT * FROM judges WHERE passwordexpiry IS NULL");
while($i = mysql_fetch_object($q)) {
	echo "Autocompleting Judge {$i->email}<br />";
	$id = $i->id;

	$p = generatePassword(12);
	mysql_query("UPDATE judges SET password='$p',complete='yes'");
	echo mysql_error();
	mysql_query("DELETE FROM judges_years WHERE judges_id='$id'");
	echo mysql_error();
	mysql_query("INSERT INTO judges_years (`judges_id`,`year`) VALUES ('$id','{$config['FAIRYEAR']}')");
	echo mysql_error();
}

?>
