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
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');

 send_header("Judges", 
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "judging_management"
			);
 echo "<br />";
 echo '<b>'.i18n('Judges').'</b><ul>';
 echo "<li><a href=\"../user_invite.php?type=judge\">".i18n("Invite Judges")."</a></li></li>";
 echo "<li><a href=\"user_list.php?show_types[]=judge\">".i18n("Manage Judges")."</a> - ".i18n("Add, Delete, Edit, and List judges").'</li>';
 echo '</ul>';
 echo '<b>'.i18n('Create the Judging Schedule').'</b><ul>';
 echo "<li><a href=\"judges_timeslots.php\">".i18n("Create/Edit Judging Timeslots")."</a></li>";
 echo "<li><a href=\"judges_jdiv.php\">".i18n("Create/Edit Divisional Judging Groupings")."</a></li>";
 echo "<li><a href=\"judges_schedulerconfig.php\">".i18n("Run the Automatic Judging Scheduler")."</a></li>";
 echo '</ul>';
 echo '<b>'.i18n('Edit the Judging Schedule').'</b><ul>';
 echo "<li><a href=\"judges_teams.php\">".i18n("Manage Judging Teams")."</a></li>";
 echo "<li><a href=\"judges_teams_members.php\">".i18n("Manage Judging Team Members")."</a></li>";
 echo "<li><a href=\"judges_teams_timeslots.php\">".i18n("Manage Judging Team Timeslot Assignments")."</a></li>";
 echo "<li><a href=\"judges_teams_projects.php\">".i18n("Manage Judging Team Project Assignments")."</a></li>";
 echo '</ul>';


 send_footer();

?>
