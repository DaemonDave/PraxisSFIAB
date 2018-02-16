<?
function db_update_87_post()
{
	global $config;

	$q = mysql_query("SELECT id,types,passwordset FROM users");
	while($i = mysql_fetch_object($q)) {
		$id = $i->id;
		$types = explode(',', $i->types);
		$expiry = $i->passwordset;

		if($expiry == NULL) {
			$newval = 'created';
		} else if($expiry == '0000-00-00') {
			$newval = false;
		} else {
			/* Find the expiry based on the type */
			$longest_expiry = 0;
			foreach($types as $t) {
				$e = $config["{$t}_password_expiry_days"];
				if($e == 0) {
					/* Catch a never expire case. */
					$longest_expiry = 0;
					break;
				} else if($e > $longest_expiry) {
					$longest_expiry = $e;
				}
			}
			if($longest_expiry == 0) {
				/* Password never expires, set the password
				 * set time to the creation time */
				$newval = 'created';
			} else {
				/* Compute when the password was set */
				$newval = date('Y-m-d',
					strtotime("$expiry -$longest_expiry days"));
				$newval = "'$newval'";
			}
		}
		if($newval != false) {
			$query = "UPDATE users SET passwordset=$newval WHERE id='$id'";
			echo "$query\n";
			mysql_query($query);
		}
	}
}
?>
