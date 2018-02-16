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
 

 $type = false;
 if(isset($_SESSION['users_type'])) {
 	$type = $_SESSION['users_type'];
 } else {
	message_push(error(i18n("You must login to view that page")));
 	header("location: {$config['SFIABDIRECTORY']}/index.php");
 	exit;
 }

 /* Make sure the user is logged in, but don't check passwd expiry */
 if(!isset($_SESSION['users_type'])) {
	message_push(error(i18n("You must login to view that page")));
	header("location: {$config['SFIABDIRECTORY']}/user_login.php?type=$type");
	exit;
 }
 
 if($_SESSION['users_type'] != $type) {
	message_push(error(i18n("You must login to view that page")));
	header("location: {$config['SFIABDIRECTORY']}/user_login.php?type=$type");
	exit;
 }
											  

 if(array_key_exists('request_uri', $_SESSION))
 	$back_link = $_SESSION['request_uri'];
 else 
	 $back_link = "{$type}_main.php";
 unset($_SESSION['request_uri']);

 $password_expiry_days = $config["{$type}_password_expiry_days"];


 if($_POST['action']=="save")
 {
	$pass = mysql_escape_string($_POST['pass1']);
 	//first, lets see if they choosed the same password again (bad bad bad)
	$q=mysql_query("SELECT password FROM users WHERE 
				id='{$_SESSION['users_id']}' 
				AND password='$pass'");

	if(mysql_num_rows($q))
		message_push(error(i18n("You cannot choose the same password again.  Please choose a different password")));
	else if(!$_POST['pass1']) 
		message_push(error(i18n("New Password is required")));
	else if($_POST['pass1'] != $_POST['pass2']) 
		message_push(error(i18n("Passwords do not match")));
	else if(user_valid_password($_POST['pass1']) == false) 
		message_push(error(i18n("The password contains invalid characters or is not long enough")));
	else {
		user_set_password($_SESSION['users_id'], $pass);
		unset($_SESSION['password_expired']);

		message_push(happy(i18n('Your password has been successfully updated')));
		header("location: $back_link");
		exit;
	}
 }

 send_header("{$user_what[$type]} - Change Password",
                  array("{$user_what[$type]} Registration" => "{$type}_main.php")
				  ,"change_password"
		                  );

 if($_SESSION['password_expired'] == true)
 {
 	echo i18n('Your password has expired.  You must choose a new password now.');
 }

 echo "<form name=\"changepassform\" method=\"post\" action=\"user_password.php\">\n";
 echo "<input type=\"hidden\" name=\"action\" value=\"save\" />\n";
 echo "<table>\n";

		echo "<br />";
		echo "<table>";
		echo "<tr><td>";
		echo i18n("Enter New Password:");
		echo "</td><td>";
		echo "<input type=\"password\" size=\"10\" name=\"pass1\">";
		echo "</td></tr>";
		echo "<tr><td>";
		echo i18n("Confirm New Password:");
		echo "</td><td>";
		echo "<input type=\"password\" size=\"10\" name=\"pass2\">";
		echo "</td></tr>";

echo "</table>";
echo "<input type=\"submit\" value=\"".i18n("Change Password")."\" />\n";
echo "</form>";
echo "<br />";
echo "<div style=\"font-size: 0.75em;\">".i18n('Passwords must be be between 6 and 32 characters, and may NOT contain any quote or a backslash.')."</div>";


send_footer();
?>
