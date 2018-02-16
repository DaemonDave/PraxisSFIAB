<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>

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
 require_once('common.inc.php');
 require_once('user.inc.php');
 require_once('user_page.inc.php');
 require_once('judge.inc.php');

 user_auth_required('judge');

 $u = user_load($_SESSION['users_id']);

 if($u['judge_active'] == 'no') {
	message_push(notice(i18n("Your judge role is not active.  If you would like to participate as a judge for the %1 %2 please click the '<b>Activate Role</b>' button in the Judge section below",array($config['FAIRYEAR'],$config['fairname']))));
	header('Location: user_activate.php');
	exit;
 }

 send_header("Judge Registration", array());


 //only display the named greeting if we have their name
 if($u['firstname']) {
	echo i18n("Hello <b>%1</b>",array($u['firstname']));
 	echo "<br />";
 }
 echo "<br />";

 $scheduleok=false;
 if($config['dates']['judgescheduleavailable'] && $config['dates']['judgescheduleavailable']!="0000-00-00 00:00:00") {
	$q=mysql_query("SELECT (NOW()>'".$config['dates']['judgescheduleavailable']."') AS test");
	$r=mysql_fetch_object($q);
	$scheduleok=$r->test;
 }
 else {
	 $scheduleok=true;
 }

 if($scheduleok) {
	 /* Check for any judging team assignment this year for this judge, 
	  * if there is one, print the judge scheule link in an obvious place, 
	  * it's less obvious below */
	 $q = mysql_query("SELECT id FROM judges_teams_link WHERE
				users_id='{$u['id']}' AND year='{$config['FAIRYEAR']}'");
	 if(mysql_num_rows($q) > 0) {
		echo '<span style="font-size: 1.2em; font-weight: bold;">';
		echo i18n("You have been assigned to a judging team.  %1Click here%2 to view the judging schedule",
			array("<a href=\"judge_schedule.php\">","</a>"));
		echo '</span>';
		echo '<br/><br/>';
	 }
 }



 //first, we need to see if they havec the current FAIRYEAR activated, if not, we'll keep their acocunt 'dormant' and it wont
 //be used for anything, but will still be available for them to login in the following years.


 echo i18n("Please use the checklist below to complete your data. Click on an item in the table to edit that information.  When you have entered all information, the <b>Status</b> field will change to <b>Complete</b>");
 echo "<br />";
 echo "<br />";

 $overallstatus="complete";

 user_page_summary_begin();
 user_page_summary_item("Contact Information", 
	"user_personal.php", "user_personal_info_status", array($u));
 user_page_summary_item("Other Information", 
	"judge_other.php", "judge_status_other", array($u));
 user_page_summary_item("Areas of Expertise", 
	"judge_expertise.php", "judge_status_expertise", array($u));

 if($config['judges_availability_enable'] == 'yes') {
	 user_page_summary_item("Time Availability", 
		"judge_availability.php", "judge_status_availability", array($u));
 }

 if($config['judges_specialaward_enable'] == 'yes' || $u['special_award_only'] == 'yes') {
	user_page_summary_item("Special Award Preferences", 
		"judge_special_awards.php", "judge_status_special_awards", array($u));
	}
//	user_page_summary_item("Areas of Expertise", 
 //		"register_judges_expertise.php", "expertiseStatus", array($u));

 $overallstatus = user_page_summary_end(true);

 judge_status_update($u);
 echo '<br /><br />';

 if($overallstatus!="complete")
 	echo error(i18n("You will not be marked as an active judge until your \"Overall Status\" is \"Complete\""));
 else
	echo happy(i18n("Thank you for completing the judge registration process.  We look forward to seeing you at the fair"));

 echo "<br />";

 echo i18n('Other Options and Things To Do').':<br />';
 echo '<ul>';
 echo '<li><a href="judge_schedule.php">'.i18n('Check the Judging Schedule').'</a> - '.i18n('Look at the judging team(s) you have been assigned to, and the projects you will judge.').'</li>';
 echo '<li><a href="user_password.php">'.i18n('Change Password').'</a> - '.i18n('Change your password').'</li>';
 echo '<li><a href="user_activate.php">'.i18n('Activate/Deactivate Role').'</a> - '.
		i18n('Activate/Deactiate/Remove/Delete roles or your entire account').
		'</li>';
 echo '<li>'.i18n('To logout, use the [Logout] link in the upper-right of the page').'</li>';
 echo '</ul>';

 send_footer();
?>
