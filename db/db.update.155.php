<?

function db_update_155_post() {
	//we need to query the stuff from the table
	$q=mysql_query("SELECT * FROM emails");
	while($r=mysql_fetch_object($q)) {
		echo "Updating email id $r->id\n";
		mysql_query("UPDATE emails SET
		body='".mysql_real_escape_string(iconv("ISO-8859-1","UTF-8//TRANSLIT",$r->body))."' ,
		bodyhtml='".mysql_real_escape_string(iconv("ISO-8859-1","UTF-8//TRANSLIT",$r->bodyhtml))."' ,
		subject='".mysql_real_escape_string(iconv("ISO-8859-1","UTF-8//TRANSLIT",$r->subject))."'
		WHERE id='$r->id'");
	}
}

?>
