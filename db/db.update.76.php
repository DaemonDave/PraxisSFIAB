<?

function db_update_76_pre()
{
	/* Find all users that exist multiple times and merge them, fixing the
	 * types link.  Right now this can only happen with committee members
	 * and volunteers */

	$q = mysql_query("SELECT DISTINCT username FROM users WHERE 1");
	while($r = mysql_fetch_assoc($q)) {
		$user = $r['username'];
		if($user == '') continue;

		$qq = mysql_query("SELECT * FROM users WHERE username='$user'");
		if(mysql_num_rows($qq) <= 1) continue;

		/* Fix $user */

		/* Load all their data */
		while($rr = mysql_fetch_assoc($qq)) {
			$types = explode(',', $rr['types']);
			foreach($types as $t) {
				$u[$t] = $rr;
			}
		}

		/* Make sure we have what we think we have */
		$cid = intval($u['committee']['id']);
		$vid = intval($u['volunteer']['id']);

		if($cid == 0 || $vid == 0) {
			echo "\n\n\nDATABASE ERROR:  User $user exists multiple
			times, but I was unable to fix it.  Please visit
			www.sfiab.ca and send us an email so we can help sort
			out your database.  It is likely that user $user will
			experience problems logging in\n\n\n";
			continue;
		}
			
		/* Copy everything into the committee entry */
		$fields = array('firstname','lastname','username','password',
			'email',
			'phonehome','phonework','phonecell','fax','organization',
			'address','address2','city','province','postalcode');

		$query = "`types`='committee,volunteer'";
		foreach($fields as $f) {
			if($u['committee'][$f] == '' && $u['volunteer'][$f] != '') {
				$v = mysql_escape_string($u['volunteer'][$f]);
				$query .= ",`$f`='$v'";
			}
		}

		$query = "UPDATE users SET $query WHERE id='$cid'";
		echo "$query\n";
		mysql_query($query);

		/* Now fix the volunteers links */
		$query = "UPDATE volunteer_positions_signup SET users_id='$cid' WHERE users_id='$vid'";
		echo "$query\n";
		mysql_query($query);

		/* The user_volunteer table is empty, we should just delete it,
		 * no need to update it */

		/* Delete the old user */
		$query = "DELETE FROM users WHERE id='$vid'";
		echo "$query\n";
		mysql_query($query);
	}
	
}

?>
