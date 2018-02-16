<?
function db_update_111_post()
{
	global $config;
	//grab the index page
	$q=mysql_query("SELECT * FROM pagetext WHERE textname='index' AND year='{$config['FAIRYEAR']}'");
    if(!mysql_num_rows($q)) {
        $q=mysql_query("SELECT * FROM pagetext WHERE textname='index' AND year='-1'");
    }
	while($r=mysql_fetch_object($q)) {
		//insert it into the CMS under index.html
		mysql_query("INSERT INTO cms (filename,dt,lang,text,showlogo) VALUES ('index.html','$r->lastupdate','$r->lang','".mysql_escape_string($r->text)."','1')");
	}
	//and remove it from the pagetext
	mysql_query("DELETE FROM pagetext WHERE textname='index'");
}
?>
