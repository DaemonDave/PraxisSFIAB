<?

/* This file may contain 2 functions, a db_update_$ver_pre() and a
 * db_update_$ver_post(). _pre() is called before the SQL patch is
 *applied, and as expected, _post() is called after.
 * 
 * These functions are called from the main db_update.php file, and included
 * once, so any global variables declared in here WILL REMAIN across both
 * calls.  meaning you can pull some stuff out of the database in _pre(), and
 * then the patch will be applied, and they it can be inserted back into the
 * database in _post(). 
 * Also note that MULTIPLE php scripts could be included if the db update is 
 * large, so global variable names probably shouldn't conflict... put the version
 * number in them*/

$update_62_committee = array();
function db_update_62_pre()
{
	global $update_62_committee;
	$q = mysql_query("SELECT * FROM committees_members");
	while($r = mysql_fetch_assoc($q)) {
		$update_62_committee[] = $r;
	}
}

function db_update_62_post()
{
	global $update_62_committee;
	global $config;

	foreach($update_62_committee as $c) {
		list($fn, $ln) = split(' ', $c['name'], 2);
		$username = $c['email'];
		$fn = mysql_escape_string($fn);
		$ln = mysql_escape_string($ln);
		if($config['committee_password_expiry_days'] > 0) {
			$passwordexpiry = "DATE_ADD(CURDATE(),
				INTERVAL {$config['committee_password_expiry_days']} DAY)";
		} else {
			$passwordexpiry = "NULL";
		}

		$deleted = ($c['deleted'] == 'Y') ? 'yes' : 'no';
		$q = "INSERT INTO users
			(`types`,`firstname`,`lastname`,`username`,`password`,`passwordexpiry`,
			`email`,`phonehome`,`phonework`,`phonecell`,`fax`,`organization`,
			`created`,`deleted`) 
			VALUES ('committee','$fn', '$ln', '$username',
				'".mysql_escape_string($c['password'])."',
				$passwordexpiry,
				'{$c['email']}',
				'{$c['phonehome']}',
				'{$c['phonework']}',
				'{$c['phonecell']}',
				'{$c['fax']}',
				'".mysql_escape_string($c['organization'])."',
				NOW(),
				'$deleted')";
		mysql_query($q);
		echo "$q\n";
		$id = mysql_insert_id();

		$access_admin = ($c['access_admin'] == 'Y') ? 'yes' : 'no';
		$access_config = ($c['access_config'] == 'Y') ? 'yes' : 'no';
		$access_super = ($c['access_super'] == 'Y') ? 'yes' : 'no';
		$displayemail = ($c['displayemail'] == 'Y') ? 'yes' : 'no';
		$q = "INSERT INTO users_committee(`users_id`,`emailprivate`,
				`ord`,`displayemail`,`access_admin`,`access_config`,
				`access_super`) VALUES (
					'$id', '{$c['emailprivate']}',
					'{$c['ord']}',
					'$displayemail',
					'$access_admin',
					'$access_config',
					'$access_super')";
		mysql_query($q);
		echo "$q\n";
		echo mysql_error();
					
		/* Update committee links */
		$q = "UPDATE committees_link SET users_id='$id'
				WHERE committees_members_id='{$c['id']}'";
		mysql_query($q);
		echo "$q\n";

	}
}


?>
