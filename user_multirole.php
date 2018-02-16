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


 if(!isset($_SESSION['users_id'])) {
 	echo "AUTH required, this shoudln't happen.";
 	exit;
 }

 $u = user_load($_SESSION['users_id']);

 $action = $_GET['action'];

 function show_role($type, $u) 
 {
 	global $user_what;
 	if(user_add_role_allowed($type, $u) && !in_array($type, $u['types'])) {
		echo "<li><a href=\"user_new.php?type=$type\">{$user_what[$type]}</a>";
//onClick=\"return confirm('Are you sure you want to also be a {$user_what[$type]}?')\"
		echo '</li>';
		return 1;
	}
	return 0;
 }

 if($action == 'add') {
 	send_header("Select Additional Roles");

	//only display the named greeting if we have their name
	echo i18n("Hello <b>%1</b>",array($_SESSION['name']));
	echo "<br />";
	echo "<br />";

	echo i18n('Your account is currently in the following roles').':';
	echo '<ul>';
	foreach($u['types'] as $t) echo "<li>{$user_what[$t]}</li>";
	echo '</ul>';

	
	echo i18n('Adding a role to your account WILL NOT delete anything in
	your account or any existing roles you have.  It will only add a new
	role to your account.');
	echo '<br /><br />';
	echo i18n('When you add a new role to your account you will be
	automatically logged out.  To complete the process please log back in
	using your existing email and password.'); 
	echo '<br /><br />';
	echo i18n('Select a Role to add to your account');
	echo ':';

	echo '<ul>';
	$x += show_role('volunteer', $u);
	$x += show_role('judge', $u);
	if($x == 0) {
		echo '<li>';
		echo i18n('There are no more roles that can be added to your account');
		echo '</li>';
	}
	echo '</ul>';
	send_footer();
	exit;
 }

 if(count($u['types']) <= 1) {
 	/* This user doesn't have multiple roles, send them to their 
	 * proper page */
	header("location: {$_SESSION['users_type']}_main.php");
	exit;
 }

 if($action == 'switch') {
	/* Validate the input */
	$type = $_GET['type'];
	if(!in_array($type, $user_types)) {
		header("location: {$_SESSION['users_type']}_main.php");
		exit;
	} 
	/* Make sure the user is actually allowed to be in the
	 * requested role */
	if(!in_array($type, $u['types'])) {
		header('location: user_multirole.php');
		exit;
	}

	/* Switch roles, and forward the user to the
	 * appropriate mainpage */
	$_SESSION['users_type'] = $type;
	header("location: {$type}_main.php");
	exit;
 }


 send_header("Choose a Role");

 switch($_GET['notice']) {
 case 'already_logged_in':
 	echo error(i18n('You are already logged in, please use the [Logout] link in the upper right to logout before logging in as a different user'));
	break;
 }
 //only display the named greeting if we have their name
 echo i18n("Hello <b>%1</b>",array($_SESSION['name']));
 echo "<br />";
 echo "<br />";

 echo i18n('Your account has more than one role associated with it, please select a role from the links below.');
 echo "<br />";
 echo "<br />";

 foreach($user_types as $t) {
 	if(in_array($t, $u['types'])) {
	 	echo "<a href=\"user_multirole.php?action=switch&type=$t\">{$user_what[$t]}</a><br />";
		echo "<br />";
	}
 }

 send_footer();
?>
