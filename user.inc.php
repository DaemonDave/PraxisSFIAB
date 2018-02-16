<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>
   Copyright (C) 2007 David Grant <dave@lightbox.org>

   This program is free software; you can redistribute it and/or
   modify it under the terms of the GNU General Public
   License as published by the Free Software Foundation, version 2.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; see the file COPYING.  If not, write to
   the Free Software Foundation, Inc., 59 Temple Place - Suite 330,
   Boston, MA 02111-1307, USA.
*/
?>
<?

$user_what =  array('student'=>'Participant', 'judge' => 'Judge',
			'committee'=>'Committee Member','volunteer' => 'Volunteer',
			'fair'=>'Science Fair','sponsor' => 'Sponsor Contact',
			'principal' => 'Principal',
			'teacher' => 'Teacher',
			'parent' => 'Parent',
			'alumni' => 'Alumni',
			'mentor' => 'Mentor');
$user_types = array_keys($user_what);

function user_valid_type($type)
{
	global $user_types;
	if(is_array($type)) {
		foreach($type as $t) {
			if(!in_array($t, $user_types)) return false;
		}
	} else {
		if(!in_array($type, $user_types)) return false;
	}
	return true;
}

/* Duplicate of common.inc.php:generatePassword, which will be deleted 
 * eventually when ALL users are handled through this file */
function user_generate_password($pwlen=8)
{
        //these are good characters that are not easily confused with other characters :)
        $available="ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789";
        $len=strlen($available) - 1;

        $key="";
        for($x=0;$x<$pwlen;$x++)
                $key.=$available{rand(0,$len)};
        return $key;
}



/* Separate user_load_type functions, these could make additional database
 * calls if required */
function user_load_fair(&$u)
{
	$u['fair_active'] = ($u['fair_active'] == 'yes') ? 'yes' : 'no';
	$u['fair_complete'] = ($u['fair_complete'] == 'yes') ? 'yes' : 'no';
//	$u['fair_name'] = $u['fair_name'];
//	$u['fair_abbrv'] = $u['fair_abbrv'];
	return true;
}

function user_load_student(&$u)
{
//	$u['student_active'] = ($u['student_active'] == 'yes') ? 'yes' : 'no';
//	$u['student_complete'] = ($u['student_complete'] == 'yes') ? 'yes' : 'no';
	return false;
}
function user_load_judge(&$u)
{
	$u['judge_active'] = ($u['judge_active'] == 'yes') ? 'yes' : 'no';
	$u['judge_complete'] = ($u['judge_complete'] == 'yes') ? 'yes' : 'no';
	$u['years_school'] = intval($u['years_school']);
	$u['years_regional'] = intval($u['years_regional']);
	$u['years_national'] = intval($u['years_national']);
	$u['willing_chair'] = ($u['willing_chair'] == 'yes') ? 'yes' : 'no';
	$u['special_award_only'] = ($u['special_award_only'] == 'yes') ? 'yes' : 'no';
	$u['cat_prefs'] = unserialize($u['cat_prefs']);
	$u['div_prefs'] = unserialize($u['div_prefs']);
	$u['divsub_prefs'] = unserialize($u['divsub_prefs']);
//	$u['expertise_other'] = $u['expertise_other'];
	$u['languages'] = unserialize($u['languages']);
//	$u['highest_psd'] = $u['highest_psd'];

	/* Sanity check the arrays, make sure they are arrays */
	$should_be_arrays = array('cat_prefs','div_prefs',
				'divsub_prefs','languages');
	foreach($should_be_arrays as $k) {
		if(!is_array($u[$k])) $u[$k] = array();
	}

	return true;
}

function user_load_committee(&$u)
{
	$u['committee_active'] = $u['committee_active'];
	$u['emailprivate'] = $u['emailprivate'];
	$u['ord'] = intval($u['ord']);
	$u['displayemail'] = ($u['displayemail'] == 'yes') ? 'yes' : 'no';
	$u['access_admin'] = ($u['access_admin'] == 'yes') ? 'yes' : 'no';
	$u['access_config'] = ($u['access_config'] == 'yes') ? 'yes' : 'no';
	$u['access_super'] = ($u['access_super'] == 'yes') ? 'yes' : 'no';
	$u['committee_complete'] = ($u['committee_complete'] == 'yes') ? 'yes' : 'no';
	return true;
}

function user_load_volunteer(&$u)
{
	$u['volunteer_active'] = ($u['volunteer_active'] == 'yes') ? 'yes' : 'no';
	$u['volunteer_complete'] = ($u['volunteer_complete'] == 'yes') ? 'yes' : 'no';
	return true;
}

function user_load_sponsor(&$u)
{
	$u['sponsors_id'] = intval($u['sponsors_id']);
	$u['sponsor_complete'] = ($u['sponsor_complete'] == 'yes') ? 'yes' : 'no';
	$u['sponsor_active'] = ($u['sponsor_active'] == 'yes') ? 'yes' : 'no';
	if($u['sponsors_id']) {
		$q=mysql_query("SELECT * FROM sponsors WHERE id='{$u['sponsors_id']}'");
		$u['sponsor']=mysql_fetch_assoc($q);
	}
	return true;
}

function user_load_principal(&$u)
{
	return true;
}

function user_load_teacher(&$u)
{
	return true;
}

function user_load_mentor(&$u)
{
	return true;
}
function user_load_parent(&$u)
{
	return true;
}

function user_load_alumni(&$u)
{
	return true;
}

function user_load($user, $uid = false)
{
	/* So, it turns out that doing one big load is faster than loading just
	 * from the users table then loading only the specific types the user
	 * has.. go figure. */
	$query = "SELECT * FROM `users`
			LEFT JOIN `users_committee` ON `users_committee`.`users_id`=`users`.`id`
			LEFT JOIN `users_judge` ON `users_judge`.`users_id`=`users`.`id`
			LEFT JOIN `users_volunteer` ON `users_volunteer`.`users_id`=`users`.`id`
			LEFT JOIN `users_fair` ON `users_fair`.`users_id`=`users`.`id`
			LEFT JOIN `users_sponsor` ON `users_sponsor`.`users_id`=`users`.`id` 
			LEFT JOIN `users_principal` ON `users_principal`.`users_id`=`users`.`id` 
			LEFT JOIN `users_teacher` ON `users_teacher`.`users_id`=`users`.`id` 
			LEFT JOIN `users_parent` ON `users_parent`.`users_id`=`users`.`id` 
			LEFT JOIN `users_mentor` ON `users_mentor`.`users_id`=`users`.`id` 
			LEFT JOIN `users_alumni` ON `users_alumni`.`users_id`=`users`.`id` 
			WHERE ";
	if($uid != false) {
		$uid = intval($uid);
		$query .= "`users`.`uid`='$uid' ORDER BY `users`.`year` DESC LIMIT 1";
	} else {
		$id = intval($user);
		$query .= "	`users`.`id`='$id'";
	}
	$q=mysql_query($query);

	if(mysql_num_rows($q)!=1) {
//		echo "Query [$query] returned ".mysql_num_rows($q)." rows\n";
//		echo "<pre>";
//		print_r(debug_backtrace());
		return false;
	}

	$ret = mysql_fetch_assoc($q);

	/* Make sure they're not deleted, we don't want to do this in the query, because loading by $uid would
	 * simply return the previous year (where deleted=no) */
	if($ret['deleted'] != 'no') {
		/* User is deleted */
		return false;
	}
		
	/* Do we need to do number conversions? */
	$ret['id'] = intval($ret['id']);
	$ret['uid'] = intval($ret['uid']);
	$ret['year'] = intval($ret['year']);

	/* Turn the type into an array, because there could be more than one */
	$ts = explode(',', $ret['types']);
	$ret['types'] = $ts; /* Now we can use in_array('judge', $ret['types']) ; */

	/* Convenience */
	$ret['name'] = ($ret['firstname'] ? "{$ret['firstname']} " : '').$ret['lastname'];

	/* Email recipient for "to" field on emails */
	if( ($ret['firstname'] || $ret['lastname']) && $ret['email']) {
		//use their full name if we have it
		//if the name contains anything non-standard, we need to quote it.
		if(eregi("[^a-z0-9 ]",$ret['name']))
			$ret['emailrecipient']="\"{$ret['name']}\" <{$ret['email']}>";
		else
			$ret['emailrecipient']="{$ret['name']} <{$ret['email']}>";
	}
	else if($ret['email']) {
		//otherwise, just their email address
		$ret['emailrecipient']=$ret['email'];
	}
	else
		$ret['emailrecipient']="";

	foreach($ret['types'] as $t) {
		/* These all pass $ret by reference, and can modify
		 * $ret */
		$r = call_user_func("user_load_$t", &$ret);
		if($r != true) {
			echo "user_load_$t returned false!\n";
			return false;
		}

		/* It is important that each type database doesn't
		have conflicting column names */
/*		foreach($r as $k=>$v) {
			if(array_key_exists($k, $ret)) {
				echo "DATABASE DESIGN ERROR, duplicate user key $k";
				exit;
			}
		}
		$ret = array_merge($ret, $r);
*/
	}

	/* Do this assignment without recursion :) */
	unset($ret['orig']);
	$orig = $ret;
	$ret['orig'] = $orig;

/*	echo "<pre>User load returning: \n";
	print_r($ret);
	echo "</pre>";
*/
	return $ret;
}

function user_load_by_uid($uid)
{
	return user_load(0, $uid);
}

function user_load_by_email($email)
{
	/* Find the most recent uid for the email, regardless of deleted status */
	$e = mysql_real_escape_string($email);
	$q = mysql_query("SELECT uid FROM users WHERE email='$e' OR username='$e' ORDER BY year DESC LIMIT 1");

	if(mysql_num_rows($q) == 1) {
			$i = mysql_fetch_assoc($q);
			return user_load_by_uid($i['uid']);
	}
	return false;
}

function user_load_by_uid_year($uid, $year)
{
	$q = mysql_query("SELECT id FROM users WHERE uid='$uid' AND year <= '$year'");
	if(!mysql_num_rows($q)) return false;
	$i = mysql_fetch_assoc($q);
	return user_load($i['id']);
}

function user_set_password($id, $password = NULL)
{
	/* pass $u by reference so we can update it */
	$save_old = false;
	if($password == NULL) {
		$q = mysql_query("SELECT passwordset FROM users WHERE id='$id'");
		$u = mysql_fetch_assoc($q);
		/* Generate a new password */
		$password = user_generate_password(12);
		/* save the old password only if it's not an auto-generated one */
		if($u['passwordset'] != '0000-00-00') $save_old = true;
		/* Expire the password */
		$save_set = "'0000-00-00'";
	} else {
		/* Set the password, no expiry, save the old */
		$save_old = true;
		$save_set = 'NOW()';
	}

	$p = mysql_escape_string($password);
	$set = ($save_old == true) ? 'oldpassword=password, ' : '';
	$set .= "password='$p', passwordset=$save_set ";

	$query = "UPDATE users SET $set WHERE id='$id'";
	mysql_query($query);
	echo mysql_error();

	return $password;
}

function user_save_type_list($u, $db, $fields)
{
/*	echo "<pre> save type list $db";
	print_r($u);
	echo "</pre>";*/
	$set = '';

	foreach($fields as $f) {
		/* == even works on arrays in PHP */
		if($u[$f] == $u['orig'][$f]) continue;

		if($set != '') $set .=',';

		if($u[$f] == NULL) {
			$set .= "$f=NULL";
			continue;
		}

		if(is_array($u[$f])) 
			$data = mysql_escape_string(serialize($u[$f]));
		else 
			$data = mysql_escape_string(stripslashes($u[$f]));

		$set .= "`$f`='$data'";
	}
	if($set != "") {
		$query = "UPDATE $db SET $set WHERE users_id='{$u['id']}'";
		mysql_query($query);
		if(mysql_error()) {
			echo mysql_error();
			echo error("Full query: $query");
		}
	}
}

function user_save_volunteer($u)
{
	$fields = array('volunteer_active','volunteer_complete');
	user_save_type_list($u, 'users_volunteer', $fields);
}

function user_save_committee($u)
{
	$fields = array('committee_complete','committee_active','emailprivate','ord','displayemail','access_admin',
			'access_config','access_super');
	user_save_type_list($u, 'users_committee', $fields);
	committee_status_update($u);
}

function user_save_judge($u)
{
	$fields = array('judge_active','judge_complete','years_school','years_regional','years_national',
			'willing_chair','special_award_only',
			'cat_prefs','div_prefs','divsub_prefs',
			'expertise_other','languages', 'highest_psd');
	user_save_type_list($u, 'users_judge', $fields);
}

function user_save_student($u)
{
//	$fields = array('student_active','student_complete');
//	user_save_type_list($u, 'users_student', $fields);
}

function user_save_fair($u)
{
	$fields = array('fair_active','fairs_id');
	user_save_type_list($u, 'users_fair', $fields);
}

function user_save_sponsor($u)
{
	$fields = array('sponsors_id','sponsor_active','sponsor_complete','primary','position','notes');
	user_save_type_list($u, 'users_sponsor', $fields);
}

function user_save_teacher($u)
{
}

function user_save_principal($u)
{
}

function user_save_mentor($u)
{
}

function user_save_alumni($u)
{
}

function user_save_parent($u)
{
}

function user_save(&$u)
{
	/* Add any new types */
	$added = array_diff($u['types'], $u['orig']['types']);
	foreach($added as $t) {
		if(!user_add_role_allowed($t, $u)) {
			echo "HALT: user can't add this type";
			exit;
		}
        //give em a record, the primary key on the table takes care of uniqueness
   	    $q=mysql_query("INSERT INTO users_$t (users_id) VALUES ('{$u['id']}')");
	}



	$fields = array('salutation','firstname','lastname','username',
			'email',
			'phonehome','phonework','phonecell','fax','organization',
			'address','address2','city','province','postalcode','sex',
			'firstaid', 'cpr', 'types','lang');

	$set = "";
	foreach($fields as $f) {
		if($u[$f] == $u['orig'][$f]) continue;

		if($set != "") $set .=',';

		if($f == 'types') 
			$set .= "$f='".implode(',', $u[$f])."'";
		else {
			$data = mysql_escape_string(stripslashes($u[$f]));
			$set .= "$f='$data'";
		}
	}
//	echo "<pre>";
//	print_r($u);
//	echo "</pre>";
	if($set != "") {
		$query = "UPDATE users SET $set WHERE id='{$u['id']}'";
		mysql_query($query);
//		echo "query=[$query]";
		echo mysql_error();
	}

	/* Save the password if it changed */
	if($u['password'] != $u['orig']['password']) 
		user_set_password($u['id'], $u['password']);

	/* Save types */
	foreach($u['types'] as $t) {
		call_user_func("user_save_$t", $u);
	}

	/* Should we do this? */
	/* Record all the data in orig that we saved */
	unset($u['orig']);
	$orig = $u;
	$u['orig'] = $orig; 

//	print_r($u);
}

/* Delete functions. These mark a user as deleted, and delete references to other
 * tables */

function user_delete_committee($u)
{
	mysql_query("DELETE FROM committees_link WHERE users_uid='{$u['uid']}'");
}

function user_delete_volunteer($u)
{
}

function user_delete_judge($u)
{
	global $config;
	$id = $u['id'];
	mysql_query("DELETE FROM judges_teams_link WHERE users_id='$id'");
	mysql_query("DELETE FROM judges_specialawards_sel WHERE users_id='$id'");
}

function user_delete_fair($u)
{
}

function user_delete_student($u)
{
}

function user_delete_sponsor($u)
{
}

function user_delete_principal($u)
{
}

function user_delete_teacher($u)
{
}

function user_delete_parent($u)
{
}

function user_delete_mentor($u)
{
}

function user_delete_alumni($u)
{
}


function user_delete($u, $type=false)
{
	$finish_delete = false;

	if(!is_array($u)) {
		$u = user_load($u);
	}  
	if($type != false) {
		if(!in_array($type, $u['types'])) {
			/* Hum, type specified, but the user is not this type,
			 * so, i guess we're done. */
			return;
		}
		if(count($u['types']) > 1) {
			/* Don't delete the whole user */
			$types='';
			foreach($u['types'] as $t) {
				if($t == $type) continue;
				if($types != '') $types .= ',';
				$types .= $t;
			}
			mysql_query("UPDATE users SET types='$types' WHERE id='{$u['id']}'");
		} else {
			$finish_delete = true;
		}
		call_user_func("user_delete_$type", $u);
	} else {
		/* Delete the whole user */
		if(is_array($u['types'])) {
			foreach($u['types'] as $t) call_user_func("user_delete_$t", $u);
		}
		$finish_delete = true;
	}
	if($finish_delete == true) {
		mysql_query("UPDATE users SET deleted='yes', deleteddatetime=NOW() WHERE id='{$u['id']}'");
	}
}


/* Purge functions. These completely eliminate all traces of a user from the
 * database.  This action cannot be undone.  We prefer the committee to use the
 * "delete" functions, which simply mark the account as "deleted". */

function user_purge($u, $type=false)
{
	$finish_purge = false;

	if(!is_array($u)) {
		$u = user_load($u);
	}  
	if($type != false) {
		if(!in_array($type, $u['types'])) {
			/* Hum, type specified, but the user is not this type,
			 * so, i guess we're done. */
			return;
		}
		if(count($u['types']) > 1) {
			/* Don't delete the whole user */
			$types='';
			foreach($u['types'] as $t) {
				if($t == $type) continue;
				if($types != '') $types .= ',';
				$types .= $t;
			}
			mysql_query("UPDATE users SET types='$types' WHERE id='{$u['id']}'");
		} else {
			$finish_purge = true;
		}
		/* Call the delete func to deal with table linking, then completely wipe
		 * out the entry */
		call_user_func("user_delete_$type", $u);
//		call_user_func("user_purge_$type", $u);
		mysql_query("DELETE FROM users_$type WHERE users_id='{$u['id']}'");
	} else {
		/* Delete the whole user */
		foreach($u['types'] as $t) {
			call_user_func("user_delete_$t", $u);
//			call_user_func("user_purge_$t", $u);
			mysql_query("DELETE FROM users_$t WHERE users_id='{$u['id']}'");
		}
		$finish_purge = true;
	}
	if($finish_purge == true) {
		mysql_query("DELETE FROM users WHERE id='{$u['id']}'");
	}
}


/* Duplicate a row in the users table, or any one of the users_* tables. */
function user_dupe_row($db, $key, $val, $newval)
{
	global $config;
	$nullfields = array('deleteddatetime'); /* Fields that can be null */
	$q = mysql_query("SELECT * FROM $db WHERE $key='$val'");
	if(mysql_num_rows($q) != 1) {
		echo "ERROR duplicating row in $db: $key=$val NOT FOUND.\n";
		exit;
	}
	$i = mysql_fetch_assoc($q);
	$i[$key] = $newval;

	foreach($i as $k=>$v) {
		if($v == NULL && in_array($k, $nullfields)) 
			$i[$k] = 'NULL';
		else if($k == 'year') 
			$i[$k] = $config['FAIRYEAR'];
		else
			$i[$k] = '\''.mysql_escape_string($v).'\'';
	}

	$keys = '`'.join('`,`', array_keys($i)).'`';
	$vals = join(',', array_values($i));

	$q = "INSERT INTO $db ($keys) VALUES ($vals)";
//	echo "Dupe Query: [$q]";
	$r = mysql_query($q);
	echo mysql_error();

	$id = mysql_insert_id();
	return $id;
}
/* Used by the login scripts to copy one user from one year to another */
function user_dupe($u, $new_year)
{
	/* Dupe a user if:
	 * - They don't exist in the current year
	 *	(users->year != the target year (passed in so we can use it in the rollover script) )
	 * - They have a previous year entry
	 * 	(users->year DESC LIMIT 1 == 1 row)
	 * - That previous entry has deleted=no */

	/* Find the last entry */
	$q = mysql_query("SELECT id,uid,year,deleted FROM users WHERE uid='{$u['uid']}'
				ORDER BY year DESC LIMIT 1");
	$r = mysql_fetch_object($q);
	if($r->deleted == 'yes') {
		echo "Cannot duplicate user ID {$u['id']}, they are deleted.  Undelete them first.\n";
		exit;
	}
	if($r->year == $new_year) {
		echo "Cannot duplicate user ID {$u['id']}, they already exist in year $new_year\n";
		exit;
	}

	$id = user_dupe_row('users', 'id', $u['id'], NULL);
	$q = mysql_query("UPDATE users SET year='$new_year' WHERE id='$id'");

	/* Load the new user */
	$u2 = user_load($id);

	foreach($u2['types'] as $t) {
		user_dupe_row("users_$t", 'users_id', $u['id'], $id);
	}
	/* Return the ID of the new user */
	return $id;
}

/* Returns true if loaded user ($u) is allowed to add role type $type to their
 * profile.  THis is intended as a last-stop mechanism, preventing, for example
 * a student from co-existing with any other account type. */
function user_add_role_allowed($type, $u)
{
	/* For example, a committee member can add a volunteer or judge role to
	 * their account.  */
	$allowed = array(
		'committee' => array('volunteer', 'judge', 'sponsor','principal','teacher','parent','mentor','alumni'),
		'volunteer' => array('judge', 'committee', 'sponsor','principal','teacher','parent','mentor','alumni'),
		'judge' => array('volunteer', 'committee', 'sponsor','principal','teacher','parent','mentor','alumni'),
		'student' => array(),
		'fair' => array(),
		'sponsor' => array('volunteer','judge', 'sponsor','principal','teacher','parent','mentor','alumni'),
		'principal' => array('volunteer','judge', 'sponsor','committee','teacher','parent','mentor','alumni'),
		'teacher' => array('volunteer','judge', 'sponsor','principal','committee','parent','mentor','alumni'),
		'parent' => array('volunteer','judge', 'sponsor','principal','teacher','committee','mentor','alumni'),
		'mentor' => array('volunteer','judge', 'sponsor','principal','teacher','parent','committee','alumni'),
		'alumni' => array('volunteer','judge', 'sponsor','principal','teacher','parent','mentor','committee'),
		 );

	foreach($u['types'] as $ut) {
		$allowed_array = $allowed[$ut];
		if(in_array($type, $allowed[$ut])) return true;
	}
	return false;
}

function user_create($type, $username, $u = NULL)
{
	global $config;
	if(!is_array($u)) {
		mysql_query("INSERT INTO users (`types`,`username`,`passwordset`,`created`,`year`) 
			VALUES ('$type','$username','0000-00-00', NOW(), '{$config['FAIRYEAR']}')");
		echo mysql_error();
		$uid = mysql_insert_id();
        if(user_valid_email($username)) {
            mysql_query("UPDATE users SET email='$username' WHERE id='$uid'");
        }
		mysql_query("UPDATE users SET uid='$uid' WHERE id='$uid'");
		echo mysql_error();
		user_set_password($uid, NULL);
		/* Since the user already has a type, user_save won't create this
		 * entry for us, so do it here */
		mysql_query("INSERT INTO users_$type (users_id) VALUES('$uid')");
		echo mysql_error();
		/* Load the complete user */
		$u = user_load($uid);
	} else {
		/* The user has been specified and already exists,
		 * just add a role */
		$uid = $u['uid'];
		if(!user_add_role_allowed($type, $u)) {
			/* If we get in here, someone is hand crafting URLs */
			echo "HALT: invalid role add specified for operation.";
			exit;
		}
		/* Ensure we have a full user, and add the type to the existing user */
		$u = user_load_by_uid($uid);
		$u['types'][] = $type;
	}
	/* Activate the new type, and save, then return the user */
	$u["{$type}_active"] = 'yes';
	user_save($u);
	return $u;
}


function user_valid_user($user)
{
	/* Find any character that doesn't match the valid username characters
	 * (^ inverts the matching remember */
	$x = preg_match('[^a-zA-Z0-9@.-_]',$user);

	/* If x==1, a match was found, and the input is bad */
	return ($x == 1) ? false : true;
}

function user_valid_password($pass)
{
	/* Same as user, but allow more characters */
	$x = preg_match('[^a-zA-Z0-9 ~!@#$%^&*()-_=+|;:,<.>/?]',$pass);

	/* If x==1, a match was found, and the input is bad */
	if($x == 1) return false;

	if(strlen($pass) < 6) return false;

	return true;
}

/* A more strict version of isEmailAddress() */
function user_valid_email($str)
{
	if(eregi('^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $str)) 
		return true;
	return false;
}


/* Perform some checks.  Make sure the person is logged in, and that their
 * password hasn't expired (the password_expired var is set in the login page) 
 */
function user_auth_required($type, $access='')
{
	global $config;

	unset($_SESSION['request_uri']);
	if(!isset($_SESSION['users_type'])) {
		message_push(error(i18n("You must login to view that page")));
		$_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
		header("location: {$config['SFIABDIRECTORY']}/user_login.php?type=$type");
		exit;
	}

	/* Turn $type into an array */
	if(!is_array($type)) $type = array($type);

	/* Iterate over all the allowed types and see if this user matches */
	$auth_type = false;
	foreach($type as $t) {
		if($_SESSION['users_type'] == $t) {
			$auth_type = $t;
			break;
		}
	}

	/* No match, no access */
	if($auth_type == false) {
		message_push(error(i18n("You do not have permission to view that page")));
		header("location: {$config['SFIABDIRECTORY']}/user_login.php?type=$type");
		exit;
	}

	/* Forward to password expired, remember the target URI */
	if($_SESSION['password_expired'] == true) {
		$_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
		header("location: {$config['SFIABDIRECTORY']}/user_password.php");
		exit;
	}


	/* Check committee sub-access */
	if($auth_type == 'committee' && $access != '') {
		if(committee_auth_has_access($access) == false) {
			message_push(error(i18n('You do not have permission to view that page')));
			header("Location: {$config['SFIABDIRECTORY']}/committee_main.php");
			exit;
		}
	}
	return $auth_type;	
}


function user_volunteer_registration_status()
{
	global $config;
//	$now = date('Y-m-d H:i:s');
  //      if($now < $config['dates']['judgeregopen']) return "notopenyet";
//	if($now > $config['dates']['judgeregclose']) return "closed";
	return "open";
}

function user_judge_registration_status()
{
	global $config;
	$now = date('Y-m-d H:i:s');
        if($now < $config['dates']['judgeregopen']) return "notopenyet";
	if($now > $config['dates']['judgeregclose']) return "closed";
	return "open";
}

$user_personal_fields_map = array(
	'salutation' => array('salutation'),
	'name' => array('firstname','lastname'),
	'email' => array('email'),
	'sex' => array('sex'),
	'phonehome' => array('phonehome'),
	'phonework' => array('phonework'),
	'phonecell' => array('phonecell'),
	'fax' => array('fax'),
	'org' => array('organization'),
	'birthdate' => array('birthdate'),
	'lang' => array('lang'),
	'address' => array('address', 'address2', 'postalcode'),
	'city' => array('city'),
	'province' => array('province'),
	'firstaid' => array('firstaid','cpr'));

function user_personal_fields($type)
{
	global $config, $user_personal_fields_map;
	$ret = array('firstname','lastname','email');
	$fields = $config["{$type}_personal_fields"];
	if($fields != '') {
		$fields = split(',', $fields);
		foreach($fields as $f) {
			$ret = array_merge($ret, $user_personal_fields_map[$f]);
		}
	}
	return $ret;
}

function user_personal_required_fields($type)
{
	global $config, $user_personal_fields_map;
	$ret = array('firstname','lastname','email');
	$required = $config["{$type}_personal_required"];
	if($required != '') {
		$fields = split(',', $required);
		foreach($fields as $f) {
			$ret = array_merge($ret, $user_personal_fields_map[$f]);
		}
	}
	/* Filter some elements that are never required.  
	 *	- address2
	 */
	$ret = array_diff($ret, array('address2'));
	return $ret;
}

function user_personal_info_status(&$u)
{
	$required = array();
	foreach($u['types'] as $t) {
		$required = array_merge($required, 
				user_personal_required_fields($t));
	}
	foreach($required as $r) {
		$val = trim($u[$r]);

		if(strlen($val) > 0) {
			/* Ok */
		} else {
			return 'incomplete';
		}
	}
	/* FIXME: somehow call the $type _status_update() function to update
	 * the individual $type _complete entry? */
	return 'complete';
}

/* user_{$type}_login() is called with a full $u loaded */
function user_committee_login($u)
{
	/* Double check, make sure the user is of this type */
	if(!in_array('committee', $u['types'])) {
		echo "ERROR: attempted to login committee on a non-committee user\n";
		exit;
	}

	$_SESSION['access_admin'] = $u['access_admin'];// == 'yes') ? true : false;
	$_SESSION['access_config'] = $u['access_config'];// == 'yes') ? true : false;
	$_SESSION['access_super'] = $u['access_super'];// == 'yes') ? true : false;
}

function user_fair_login($u) 
{
	/* Double check, make sure the user is of this type */
	if(!in_array('fair', $u['types'])) {
		echo "ERROR: attempted to login fair on a non-fair user\n";
		exit;
	}

	$_SESSION['fairs_id'] = $u['fairs_id'];// == 'yes') ? true : false;
}

?>
