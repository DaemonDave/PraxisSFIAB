<?
require_once('db.update.146.user.inc.php');

function db_update_146_pre() 
{
}


function db_update_146_handle($name, $email, $phone, $type)
{
	$un = $email;
	list($first, $last) = split(' ', $name, 2);

	/* Find the user */
	if($email != '') {
		$u = db146_user_load_by_email($email);
	} else {
		$u = false;
		/* Random username */
		$un = "$first$last".db146_user_generate_password();
	}

	if($u != false) {
		/* Found the user */
		$u['types'][] = $type;
	} else {
		/* Create the user */
		$u = db146_user_create($type, db146_user_generate_password());
		$u['firstname'] = $first;
		$u['lastname'] = $last;
		$u['email'] = $email;
		$u['username'] = $un;
		$u['phone'] = $phone;
	}
	/* Save the user */
	$uid = $u['uid'];
	db146_user_save($u);
	return $u;
}

function db_update_146_post() 
{
	global $config;
    $q = mysql_query("SELECT * FROM schools WHERE year='{$config['FAIRYEAR']}'");
    while($s = mysql_fetch_assoc($q)) {
		/* Science head */
		if(trim($s['sciencehead']) != '') {
			$u = db_update_146_handle($s['sciencehead'],
								$s['scienceheademail'],
								$s['scienceheadphone'],
								'teacher');
			if($u != false) {
				mysql_query("UPDATE schools SET sciencehead_uid='{$u['uid']}' WHERE id='{$s['id']}'");
			}
		}

		/* Now the principal */
		if(trim($s['principal']) != '') {
			$u = db_update_146_handle($s['principal'],
								$s['schoolemail'],
								$s['phone'],
								'principal');
			if($u != false) {
				mysql_query("UPDATE schools SET principal_uid='{$u['uid']}' WHERE id='{$s['id']}'");
			}
		}
    }
}
?>

