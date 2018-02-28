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
 require_once("common.inc.php");
 require_once("user.inc.php");

 function try_login($user, $pass)
 {
	/* Ensure sanity of inputs, user should be an email address, but it's stored
	 * in the username field */
	/* FIXME: this should be user_valid_email, but can't be yet, because
 	 * we copy the usernames from the email field, and that field may 
	 * contain a name too */
	if(!isEmailAddress($user)) {
		/* It's possible that it's a username */
		if(!user_valid_user($user)) return false;
	}

	//we cannot check for a valid_password here, because converted users dont enforce password length of 6 which user_valid_password does.
	//all we can do is check if its a length >0
	//$x = user_valid_password($pass);
	if(!strlen($pass))
		return false;

	$user = mysql_escape_string($user);
	$q = mysql_query("SELECT id,username,password,year,deleted
				FROM users 
				WHERE username='$user'
				AND deleted='no'
				ORDER BY year DESC LIMIT 1");
	if(mysql_num_rows($q) < 1) return false;

	$r = mysql_fetch_object($q);

	/* See if the user account has been deleted */
	if($r->deleted == 'yes') return false;

	/* See if the password matches */
	if($r->password != $pass) return false;

	/* Login successful */
	return $r->id;
 }

 /* If there is no session, accept a type from the URL, else, 
  * if there is a session, always take the session's type.   The idea is
  * eventually, you'll never be able to see a login page if you're already
  * logged in. */
 $type = false;
 if(isset($_SESSION['users_type'])) 
 {
		/* They're already logged in  */
		$type = $_SESSION['users_type'];
		/* If they're not trying to logout, don't let them see the login page */
		if($_GET['action'] != 'logout') 
		{
			message_push(error(i18n('You are already logged in, please use the [Logout] link in the upper right to logout before logging in as different user')));
			header("location: {$type}_main.php");
			exit;
		}
	}
	else
	{
		$type = $_GET['type'];
		/* user_types is in user.inc.php */
		if(!in_array($type, $user_types)) $type = false;
	}

	$notice=$_GET['notice'];
	$redirect = $_GET['redirect'];
	$redirect_data = $_GET['redirectdata'];

	switch($redirect) 
	{
		case 'roleadd':
			$redirect_url = "&redirect=$redirect&redirectdata=$redirectdata";
			break;
		case 'roleattached':
			$redirect_url = "&redirect=$redirect";
			break;
		default:
			$redirect_url = '';
			break;
	}

 switch($type) {
 case 'volunteer':
	// returns "notopenyet", "closed", or "open"
	$reg_open = user_volunteer_registration_status(); 
 	break;
 case 'committee':
	$reg_open = 'notpermitted';
	break;
 case 'judge':
	$reg_open = user_judge_registration_status();
	break;
 case 'fair':
 	$reg_open = 'notpermitted';
	break;
 case 'sponsor':
 	$reg_open = 'notpermitted';
	break;
 case 'parent': case 'alumni': case 'principal': case 'mentor':
 	/* Always open, because they could have been auto-created */
 	$reg_open = 'open';
	break;
 case 'student':
 default:
	 if($_GET['action']!="logout")
		exit;
	$reg_open = 'closed';
	break;
 }
 if($_POST['action']=="login" )
 {
 	if($_POST['pass'] && $_POST['user'])
	{
		$id = try_login($_POST['user'], $_POST['pass']);
		if($id == false) {
			message_push(error(i18n("Invalid Email/Password")));
			header("location: user_login.php?type=$type$redirect_url");
			exit;
		} 

		$u = user_load($id);

		/* Make sure the user we loaded is actually for the current year, if not, 
		 * we need to duplicate the user */
		if($u['year'] != $config['FAIRYEAR']) {
			$id = user_dupe($u, $config['FAIRYEAR']);
			$u = user_load($id);
		}

		/* Make sure $type is in their types */
		if(!in_array($type, $u['types'])) {
			/* Huh, someone is fudging with the HTML, get
			 * out before touching the session */
			header("location: index.php");
			exit;
		}

		$_SESSION['name']="{$u['firstname']} {$u['lastname']}";
		$_SESSION['username']=$u['username'];
		$_SESSION['email']=$u['email'];
		$_SESSION['users_id']=$u['id'];
		$_SESSION['users_uid']=$u['uid'];
		$_SESSION['users_type']=$type;

		/* Load the password expiry for each user type, and
		 * find the longest expiry, which is the one we'll use
		 * for this user to determine if the passwd has
		 * expired. */
		$longest_expiry = 0;
		foreach($u['types'] as $t) {
			$e = $config["{$t}_password_expiry_days"];
			if($e == 0) {
				/* Catch a never expire case. */
				$longest_expiry = 0;
				break;
			} else if($e > $longest_expiry) {
				$longest_expiry = $e;
			}
		}
		if($u['passwordset'] == '0000-00-00') {
			/* Force the password to expire */
			$_SESSION['password_expired'] = true;
		} else if($longest_expiry == 0) {
			/* Never expires */
			unset($_SESSION['password_expired']);
		} else {
			/* Check expiry */
			$expires = date('Y-m-d', strtotime("{$u['passwordset']} +$longest_expiry days"));
			$now = date('Y-m-d');
			if($now > $expires) {
				$_SESSION['password_expired'] = true;
			} else {
					unset($_SESSION['password_expired']);
			}
		}
		/* If password_expired == true, the main page (or any
		 * other user page) will catch this and require
		 * them to set a password */

		/* Call login functions for each type, so multirole
		 * users can easily switch */
		foreach($u['types'] as $t) {
			if(is_callable("user_{$t}_login")) {
				call_user_func_array("user_{$t}_login", array($u));
			}
		}

		mysql_query("UPDATE users SET lastlogin=NOW() 
				WHERE id={$u['id']}");

		/* Setup multirole so a multirole user can switch if they want to
		 * without logging in/out */
		if(count($u['types']) > 1) {
		$_SESSION['multirole'] = true;
		} else  {
			$_SESSION['multirole'] = false;
		}

		/* See if there is a redirect, and do that instead of
		 * taking them to their main page */
		if($redirect != '') {
			switch($redirect) {
			case 'roleadd':
				if(!in_array($multirole_data, $user_types)) 
					$multirole_data = '';

				header("location: user_multirole.php?action=add&type=$multirole_data");
				exit;
			case 'roleattached':
				message_push(happy(i18n('The %1 role has been attached to your account', array($user_what[$type]))));
				message_push(notice(i18n('Use the [Switch Roles] link in the upper right to change roles while you are logged in')));
				header("location: {$type}_main.php");
				exit;
				
			}
		}

		/* Is there a saved requesT_uri from a failed login attempt?, if so 
		 * take them there */
		if(array_key_exists('request_uri', $_SESSION)) {
			header("location: {$_SESSION['request_uri']}");
			unset($_SESSION['request_uri']);
			exit;
		}
		header("location: {$type}_main.php");
		exit;
	}

	message_push(error(i18n("Invalid Email/Password")));
	header("location: user_login.php?type=$type");
	exit;
 }
 else if($_GET['action']=="logout")
 {
 	/* Session keys to skip on logout */
 	$skip = array('debug', 'lang', 'messages');

 	/* Do these explicitly because i'm paranoid */
	unset($_SESSION['name']);
	unset($_SESSION['username']);
	unset($_SESSION['email']);
	unset($_SESSION['users_id']);
	unset($_SESSION['users_type']);
	/* Take care of anything else */
 	$keys = array_diff(array_keys($_SESSION), $skip);
	foreach($keys as $k) unset($_SESSION[$k]);

	message_push(notice(i18n("You have been successfully logged out")));
	if($type != '') 
		header("Location: user_login.php?type={$type}{$redirect_url}");
	else
		header("Location: user_login.php{$redirect_url}");
	exit;
 }
 else if($_GET['action']=="recover")
 {
	send_header("{$user_what[$type]} - Password Recovery",
			array("{$user_what[$type]} Login" => "user_login.php?type=$type"));

	$recover_link = "user_login.php?type=$type&action=recover";

	?>
	 <br />
	 <?=i18n('Password recovery will reset your password to a new random password, and then email you that password.  Enter your name and email address below, then click on the \'Reset\' button.  The name and email must exactly match the ones you used to register.  Sometimes the email takes a few minutes to send so be patient.')?><br />
	 <br />
	 <form method="post" action="user_login.php?type=<?=$type?>">
	 <input type="hidden" name="action" value="recoverconfirm" />
	 <table>
	 <tr><td>
	 <?=i18n("First Name")?>:</td><td><input type="text" size="20" name="fn" />
	 </td></tr>
	 <tr><td>
	 <?=i18n("Last Name")?>:</td><td><input type="text" size="20" name="ln" />
	 </td></tr>
	 <tr><td>
	 <?=i18n("Email")?>:</td><td><input type="text" size="20" name="email" />
	 </td></tr>
	 <tr><td colspan="2">
	 <input type="submit" value="<?=i18n("Reset my password")?>" />
	 </td></tr></table>
	 <br />
	 </form>
	 <br />
	<div style="font-size: 0.75em;">
	<?=i18n('If you didn\'t register using an email address and you have lost your password, please contact the committee to have your password reset.')?></div><br />
  	<?	
 }
 else if($_POST['action'] == "recoverconfirm")
 {
 	/* Process a recover */
	$email = $_POST['email'];
	if(user_valid_email($email)) {
		/* valid email address */
		$e = mysql_escape_string($email);
		$q=mysql_query("SELECT * FROM users WHERE (username='$e' OR email='$e') ORDER BY year DESC LIMIT 1");
		$r=mysql_fetch_object($q);
		if($r) {
			$fn = trim($_POST['fn']);
			$ln = trim($_POST['ln']);

			/* Check name match */
			if(strcasecmp($r->firstname, $fn)!=0  ||  strcasecmp($r->lastname, $ln)!=0) {
				message_push(error(i18n("The name you entered does not match the one in your account")));
				header("Location: user_login.php?type=$type");
				exit;
			}

			/* Reset the password, and force it to expire */
			$password = user_set_password($r->id, NULL);

			/* volunteer_recover_password, judge_recover_password, student_recover_password,
				committee_recover_password */
			email_send("{$type}_recover_password",
					$email, 
					array("FAIRNAME"=>i18n($config['fairname'])),
					array(	"PASSWORD"=>$password,
						"EMAIL"=>$email)
				);

			message_push(notice(i18n("Your password has been sent to your email address")));
			header("Location: user_login.php?type=$type");
			exit;
		} else {
			message_push(error(i18n("Could not find your email address for recovery")));
			header("Location: user_login.php?type=$type");
			exit;
		}
	}
	message_push(error(i18n("Email address error")));
	header("Location: user_login.php?type=$type");
	exit;
 }
 else
 {
	send_header("{$user_what[$type]} - Login", array());

	$recover_link = "user_login.php?type=$type&action=recover";
	$new_link = "user_new.php?type=$type";

	?>
	 <form method="post" action="user_login.php?type=<?="$type$redirect_url"?>">
	 <input type="hidden" name="action" value="login" />
	 <table><tr><td>
	 <?=i18n("Email")?>:</td><td><input type="text" size="20" name="user" />
	 </td></tr><tr><td>
	 <?=i18n("Password")?>:</td><td><input type="password" size="20" name="pass" />
	 </td></tr>
	 <tr><td colspan=2>
	 <input type="submit" value=<?=i18n("Login")?> />
	 </td></tr>
	 </table>
	 </form>

	<br />
	<div style="font-size: 0.75em;">
	<?=i18n("If you have lost or forgotten your password, or have misplaced the email with your initial password, please <a href=\"$recover_link\">click here to recover it</a> If you use Microsoft Outlook or Hotmail and didn't receive an email, please <a href=\"http://seab-sciencefair.com/mediawiki/index.php/WARNING_TO_MICROSOFT_OUTLOOK_AND_HOTMAIL_USERS\">see this webpage for how to get registration email.</a> ")?>.</div><br />
	<br />
<?
	switch($reg_open) 
	{
			case 'notopenyet':
	      echo i18n("Registration for the %1 %2 has not yet opened", array(	$config['FAIRYEAR'], 	$config['fairname']), array("Fair year","Fair name")	);
				break;
			case 'open':
				echo i18n("If you would like to register as a new {$user_what[$type]}, <a href=\"$new_link\">click here</a>.<br />");
				break;
			case 'closed':
        echo i18n("Registration for the %1 %2 is now closed", array(	$config['FAIRYEAR'], 	$config['fairname']), array("Fair year","Fair name") 	);
				break;
			case 'notpermitted':
			default:
			break;
	}

 }

 send_footer();
?>

