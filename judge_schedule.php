<?
/* 
  This file is part of the 'Science Fair In A Box' project
  SFIAB Website: http://www.sfiab.ca

  Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
  Copyright (C) 2005 James Grant <james@lightbox.org>
  Copyright (C) 2009 David Grant <dave@lightbox.org>

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
require_once('judge.inc.php');
require_once('projects.inc.php');

/* Sort out who we're editting */
if($_POST['users_id']) 
	$eid = intval($_POST['users_id']); /* From a save form */
else if(array_key_exists('embed_edit_id', $_SESSION))
	$eid = $_SESSION['embed_edit_id']; /* From the embedded editor */
else 
	$eid = $_SESSION['users_id'];	/* Regular entry */

if($eid != $_SESSION['users_id']) {
	/* Not editing ourself, we had better be
	 * a committee member */
	user_auth_required('committee','admin');
} else {
	user_auth_required('judge');
}

$u = user_load($eid);

 send_header("Schedule", 
                array('Judge Main' => 'judge_main.php',
                        ),
            "judge_schedule"
                        );

 $scheduleok=false;
 if($config['dates']['judgescheduleavailable'] && $config['dates']['judgescheduleavailable']!="0000-00-00 00:00:00") {
	$q=mysql_query("SELECT (NOW()>'".$config['dates']['judgescheduleavailable']."') AS test");
	$r=mysql_fetch_object($q);
	$scheduleok=$r->test;
 }
 else {
	 $scheduleok=true;
 }


if(!$scheduleok) {
	echo i18n("Your judging assignments and schedule will be available on %1",array(format_datetime($config['dates']['judgescheduleavailable'])));
	send_footer();
	if($_SESSION['embed'] != true) send_footer();
	exit;
}


/* Find all judging teams this judge is on */
$q = mysql_query("SELECT * FROM judges_teams_link
			LEFT JOIN judges_teams ON judges_teams.id=judges_teams_link.judges_teams_id
			WHERE judges_teams_link.users_id='{$u['id']}'
			AND judges_teams_link.year='{$config['FAIRYEAR']}'");
$teams = array();
while($t = mysql_fetch_assoc($q)) {
	/* Load timeslot data for this team (team -> judges_timeslots_link -> timeslot -> parent timeslot */
	$qq = mysql_query("SELECT T.* FROM judges_teams_timeslots_link
				LEFT JOIN judges_timeslots ON judges_timeslots.id=judges_teams_timeslots_link.judges_timeslots_id
				LEFT JOIN judges_timeslots AS T ON T.id=judges_timeslots.round_id
				WHERE judges_teams_timeslots_link.judges_teams_id={$t['judges_teams_id']}");
	$tt = mysql_fetch_assoc($qq);
	echo mysql_error();
	$t['timeslot'] = $tt;

	/* Load award */
	$qq = mysql_query("SELECT award_awards.*,T.type FROM judges_teams_awards_link
				LEFT JOIN award_awards ON award_awards.id=judges_teams_awards_link.award_awards_id
				LEFT JOIN award_types as T ON T.id=award_awards.award_types_id
				WHERE judges_teams_awards_link.judges_teams_id={$t['judges_teams_id']}");
	echo mysql_error();
	$aa = mysql_fetch_assoc($qq);
	$t['award'] = $aa;

	/* Load team members */
	$qq = mysql_query("SELECT * FROM judges_teams_link 
				LEFT JOIN users ON users.id=judges_teams_link.users_id
				WHERE judges_teams_link.judges_teams_id={$t['judges_teams_id']}
				ORDER BY judges_teams_link.captain,users.lastname,users.firstname");
	$t['members'] = array();
	while(($mm = mysql_fetch_assoc($qq))) {
		$t['members'][] = $mm;
	}

	/* Load projects */
	$qq = mysql_query("SELECT projects.id,projects.projectnumber,projects.title FROM judges_teams_timeslots_projects_link
				LEFT JOIN projects ON projects.id=judges_teams_timeslots_projects_link.projects_id
				WHERE judges_teams_id={$t['judges_teams_id']}");
	$p = array();
	while(($pp = mysql_fetch_assoc($qq)))
		$p[] = $pp;
	/* If no project and it's a special award, get all nominated */
	if(count($p) == 0 && $aa['type'] == 'Special') {
		$p = getProjectsNominatedForSpecialAward($aa['id']);
	} 

	$t['projects'] = $p;
	$teams[] = $t;
}

foreach($teams as $t) {
	$d = format_date($t['timeslot']['date']);
	$t1 = format_time($t['timeslot']['starttime']);
	$t2 = format_time($t['timeslot']['endtime']);
	echo "<h3>$d $t1 - $t2</h3>";
	echo "<h4>".i18n('Team')." {$t['num']} - {$t['name']}</h4>";
	echo "<table><tr><td><b>".i18n('Team Members').'</b>:';
	echo '</td><td>';
	foreach($t['members'] as $m) {
		echo "{$m['firstname']} {$m['lastname']}";
		if($m['captain'] == 'yes') echo '('.i18n('captain').')';
		echo '<br />';
	}
	echo '</td></tr></table>';

	echo "<table><tr><td><b>".i18n('Projects').'</b>:';
	echo '</td><td>';

	if(count($t['projects'])== 0) {
		echo i18n("No projects assigned.");
	} else {
		echo '<table>';
		foreach($t['projects'] as $p) {
			$pn = urlencode($p['projectnumber']);
			echo "<tr><td>{$p['projectnumber']}</td><td>-</td><td><a href=\"judge_project_summary.php?pn=$pn\" target=\"_blank\">{$p['title']}</a></td>";
			echo "</tr>";
		}
		echo '</table>';
	}

	echo '</td></tr></table>';

	echo '<br /><br />';
}

if(count($teams) == 0) {
	echo i18n("You have not been assigned to a judging team.  This could be
	because the organizers haven't completed assignments.  Contact the fair
	organizers if you believe this is incorrect.  For most fairs, you can safely just show up to the
	fair anyway, there is always a need for judges."); 
}

//echo "<pre>";
//print_R($teams);

if($_SESSION['embed'] != true) send_footer();
?>
