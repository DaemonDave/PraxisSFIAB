<?
require_once('db.update.129.user.inc.php');

function db_update_129_pre()
{
	/* Load all external award sources */
	$source_map = array();
	$q = mysql_query("SELECT * FROM award_sources");
	while($r = mysql_fetch_assoc($q)) {

		/* Make a user, use the password generator to get 
		 * a random username */
		$u = db129_user_create('fair', db129_user_generate_password());

		/* Add a Fair Entry */
		$name = mysql_escape_string($r['name']);
		$url = mysql_escape_string($r['url']);
		$website = mysql_escape_string($r['website']);
		$username = mysql_escape_string($r['username']);
		$password = mysql_escape_string($r['password']);
		$en = ($r['enabled'] == 'no') ? 'no' : 'yes';

		mysql_query("INSERT INTO fairs (`id`,`name`,`abbrv`,`type`,
		`url`,`website`,`username`,`password`,`enable_stats`,
		`enable_awards`,`enable_winners`) VALUES (
			'', '$name', '', 'ysf', '$url', '$web',
			'$username','$password','no','$en','$en')");

		/* Link the fair to the user */
		$u['fairs_id'] = mysql_insert_id();

		/* Record the old sources_id to new sources_id mapping */
		$source_map[$r['id']] = $u['fairs_id'];

		db129_user_save($u);
	}

	/* Map all awards to their new source IDs */
	$q = mysql_query("SELECT * FROM award_awards");
	$keys = array_keys($source_map);
	while($r = mysql_fetch_assoc($q)) {
		$old_id = $r['award_sources_id'];
		if(!in_array($old_id, $keys)) continue;

		$qq = mysql_query("UPDATE award_awards SET award_sources_id='{$source_map[$old_id]}'
					WHERE id='{$r['id']}'");
	}

	
}

?>
