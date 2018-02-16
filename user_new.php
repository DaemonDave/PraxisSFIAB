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

 $type = $_GET['type'];
 if(!in_array($type, $user_types)) {
 	send_header("Registration");
	echo i18n("Invalid new registration\n");
	send_footer();
	exit;
 }

 $action = $_GET['action'];
 if($action == '') $action = $_POST['action'];

 switch($type) {
 case 'volunteer':
	// returns "notopenyet", "closed", or "open"
	$reg_open = user_volunteer_registration_status(); 
	$reg_mode = $config['volunteer_registration_type'];
	$reg_single_password = $config['volunteer_registration_singlepassword'];
	$password_expiry_days = $config['volunteer_password_expiry_days'];
	$welcome_email = "volunteer_welcome";
 	break;
 case 'committee':
	$reg_open = 'notpermitted';
	$reg_mode = 'closed';
	$reg_single_password = '';
	$password_expiry_days = 0;
	$welcome_email = false;
	break;
 case 'judge':
	$reg_open = user_judge_registration_status();
	$reg_mode = $config['judge_registration_type'];
	$reg_single_password = $config['judge_registration_singlepassword'];
	$password_expiry_days = $config['judges_password_expiry_days'];
	$welcome_email = "judge_welcome";
	break;
 case 'student':
	$reg_open = 'closed';
//	$reg_mode = $config['judge_registration_type'];
//	$reg_single_password = $config['judge_registration_singlepassword'];
	$password_expiry_days = 0;
	$welcome_email = "register_students_welcome";
	break;
 default:
 	exit;
 }

 $data_fn = '';
 $data_ln = '';
 $data_email = '';

 if($reg_open != "open") {
 	send_header("{$user_what[$type]} Registration",
		array("{$user_what[$type]} Login" => "user_login.php?type=$type") );
	echo i18n("{$user_what[$type]} registration is not open");
	echo "<br />";
	send_footer();
	exit;
 }

 if($reg_mode == 'invite') {
 	send_header("{$user_what[$type]} Registration",
		array("{$user_what[$type]} Login" => "user_login.php?type=$type") );

	output_page_text("register_{$type}_invite");
	echo "<br />";
	send_footer();
	exit;
 }
 
 /* We're going to use a case statement here, so we can break out of 
  * it at any moment.. there are a lot of reasons why we don't want
  * to create a new account.. too many to nest inside IF statements..
  * this is the one time I wish php had a goto statement. */
 switch($action) {
 case 'new':
	$data_fn = mysql_escape_string(stripslashes($_POST['fn']));
	$data_ln = mysql_escape_string(stripslashes($_POST['ln']));
	$data_email = stripslashes($_POST['email']);
	$sql_email = mysql_escape_string($data_email);
	$registrationpassword = $_POST['registrationpassword'];

	/* Check the registration singlepassword */
	if($reg_mode == 'singlepassword') {
		if($reg_single_password != $_POST['registrationpassword']) {
			message_push(error(i18n("The {$user_what[$type]} Registration password you have entered is incorrect.")));
			break; /* Don't want to create an account */
		}
	}

	/* See if this email already exists */
	$q = mysql_query("SELECT id,types,MAX(year) AS year,deleted FROM users WHERE (email='$sql_email' OR username='$sql_email' )");
	//select *, max(year) from users where username=sql_email
	//if deleted and year = current yera - just undelete
	//if deleted and year != current yera - proceed normally and recreate the user


	if(mysql_num_rows($q) > 0) {
		/* It already exists, make sure they're not already in this role */
		$r = mysql_fetch_object($q);
		$types = split(',', $r->types);

		if($r->year==$config['FAIRYEAR'] && $r->deleted=='yes') {
			mysql_query("UPDATE users SET deleted='no' WHERE id='$r->id'");
			message_push(happy(i18n("Your account has been undeleted")));
			message_push(notice(i18n("Use the 'recover password' option on the %1 {$user_what[$type]} login page %2 if you have forgotten your password",
					array("<a href=\"user_login.php?type=$type\">", "</a>"))));
			header("Location: user_login.php?type=$type");
			exit;
		}
		else if($r->deleted=='no') {
			if(in_array($type, $types)) {
				message_push(error(i18n("That email address has an existing {$user_what[$type]} registration")));
				message_push(notice(i18n("Use the 'recover password' option on the %1 {$user_what[$type]} login page %2 if you have forgotten your password",
					array("<a href=\"user_login.php?type=$type\">", "</a>"))));
				break; /* Don't want to create an account */
			} else {
				/* If they're already logged in, we can go ahead and
				 * add this role.  We've passed all the required checks
				 * for creating a new user of this role.
				 * The user has already been warned about being logged
				 * out. */
				if(isset($_SESSION['users_id'])) {
					/* user_create does last minute checks, like
					 * ensuring a student doesn't try to also
					 * register as a judge */
					$u = user_load($_SESSION['users_id']);
					$u = user_create($type, $u['username'], $u);
					$_SESSION['users_type'] = $type;
					message_push(notice(i18n("Login to finish adding the new role to your account")));

					header("location: user_login.php?action=logout&redirect=roleattached");
					exit;
				} 
				/* forward the user to the login page for whatever role
				 * they already have (it doesn't matter), and 
				 * setup a login role_add redirect */
				message_push(notice(i18n("Your email address already exists.  Please login to your existing account below and you will be redirected to the multi-role creation page to complete your registration request.")));
				header("location: user_login.php?type={$types[0]}&redirect=roleadd&redirectdata=$type");
				exit;
			}
		}
		else {
			//deletes = yes but year!=fairyear, so go ahead and let it create the new account
		}
	}

	/* Strict validate the email */
	if(!user_valid_email($data_email)) {
		message_push(error(i18n("The email address is invalid")));
		$data_email = '';
		break; /* Don't want to create an account */
	}

	/* Check the names */
	if($data_fn == '' or $data_ln == '') {
		message_push(error(i18n("You must enter your first and last name")));
		break; /* Don't want to create an account */
	}

	/* If we havne't encountered a break; or an exit; yet, then go ahead
	 * and create the account */

	/* Add the user, user_create sets a random/expired password,
	 * so we'll just use that */
	$u = user_create($type, $data_email);
	$u['firstname'] = $data_fn;
	$u['lastname'] = $data_ln;
	$u['email'] = $data_email;
	user_save($u);

	/* Send the email */
	email_send($welcome_email, $data_email,
			array("FAIRNAME"=>i18n($config['fairname'])),
			array("PASSWORD"=>$u['password'],
				"EMAIL"=>$data_email)
		);

	/* now redirect to the login page */
	message_push(happy(i18n("Your new password has been sent to your email address.  Please check your email and use the password to login")));
	header("Location: user_login.php?type=$type");
	exit;
 }

 send_header("{$user_what[$type]} Registration",
		array("{$user_what[$type]} Login" => "user_login.php?type=$type") );

	?>
	<form method="post" action="user_new.php?type=<?=$type?>">
	<input type="hidden" name="action" value="new" />
	<? /* If the user is already logged in, don't show this stuff */
	if(!isset($_SESSION['users_id'])) { ?>
		<table><tr><td>
		<?=i18n("First Name")?>:</td><td><input type="text" size="20" name="fn" value="<?=$data_fn?>" />
		</td></tr><tr><td>
		<?=i18n("Last Name")?>:</td><td><input type="text" size="20" name="ln" value="<?=$data_ln?>" />
		</td></tr><tr><td>
		<?=i18n("Email")?>:</td><td><input type="text" size="20" name="email" value="<?=$data_email?>"  />
		</td></tr>
		</table>
	<? } else {
		echo "<br />";
		echo i18n("Remember, once you click the Register button below, you will be logged out.  Log back in to complete the registration.");
		echo "<br />";
		echo "<input type=\"hidden\" name=\"email\" value=\"{$_SESSION['email']}\"";
	}	
	if($reg_mode == 'singlepassword') {
		echo '<br />';
		echo i18n("{$user_what[$type]} Registration is protected by a password.  You must know the <b>{$user_what[$type]} Registration Password</b> in order to create an account.  Please contact the committee to obtain the password if you wish to register.");
		echo "<br />";
		echo i18n("{$user_what[$type]} Password").":<input type=\"password\" size=\"20\" name=\"registrationpassword\" />";
		echo "<br />";
	}		
	?>
	<br /><input type="submit" value=<?=i18n("Register")?> />
	</form>
	<?

	if(!isset($_SESSION['users_id'])) {
		echo "<br />";
		echo i18n("When you click the 'Register' button, your password will be randomly created and emailed to you.  When you login for the first time you will be prompted to change your password.  It can sometimes take several minutes for the email to send, so be patient.");
		echo "<br />";
	} 
	 send_footer();
?>
