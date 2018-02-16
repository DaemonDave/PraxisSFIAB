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
 require_once("user_page.inc.php");
 require_once("volunteer.inc.php");

 user_auth_required('volunteer');

 $u = user_load($_SESSION['users_id']);

 if($u['volunteer_active'] == 'no') {
	message_push(notice(i18n("Your volunteer role is not active.  If you would like to participate as a volunteer for the %1 %2 please click the '<b>Activate Role</b>' button in the Volunteer section below",array($config['FAIRYEAR'],$config['fairname']))));
	header('Location: user_activate.php');
	exit;
 }

 send_header("Volunteer Main", array());

 //only display the named greeting if we have their name
 echo i18n("Hello <b>%1</b>",array($_SESSION['name']));
 echo "<br />";
 echo "<br />";

 echo i18n("Please use the checklist below to complete your data. Click on an item in the table to edit that information.  When you have entered all information, the <b>Status</b> field will change to <b>Complete</b>");
 echo "<br />";
 echo "<br />";

 user_page_summary_begin();
 user_page_summary_item("Contact Information", 
 		"user_personal.php", "user_personal_info_status", array($u));
 user_page_summary_item("Volunteer Positions", 
 		"volunteer_position.php", "volunteer_status_position", array($u));
 $overallstatus = user_page_summary_end(true);

 /* Update volunteer_status */
 volunteer_status_update($u);

 echo "<br />";
 echo "<br />";

 if($overallstatus!='complete')
 {
	echo error(i18n("You will not be marked as an active volunteer until your \"Overall Status\" is \"Complete\""));
 }
 else
 {
	echo happy(i18n("Thank you for completing the volunteer registration process.  We look forward to seeing you at the fair"));
 }
 echo "<br />";
 echo i18n('Other Options and Things To Do').':<br />';
 echo '<ul>';
 echo '<li><a href="user_password.php">'.i18n('Change Password').'</a> - '.i18n('Change your password').'</li>';
 echo '<li><a href="user_activate.php">'.i18n('Activate/Deactivate Roles').'</a> - '.
		i18n('Activate/Deactiate/Remove/Delete roles or your entire account').
		'</li>';
 echo '<li>'.i18n('To logout, use the [Logout] link in the upper-right of the page').'</li>';
 echo '</ul>';

 send_footer();
?>
