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
 require_once('../common.inc.php');
 require_once('../user.inc.php');
 user_auth_required('committee', 'admin');
 require_once('judges.inc.php');

 $round_str = array('timeslot' => 'Judging Timeslot',
 					'divisional1' => 'Divisional Round 1',
 					'divisional2' => 'Divisional Round 2',
					'grand' => 'Grand Awards',
					'special' => 'Special Awards' );
 if(array_key_exists('action',$_GET)) 
	 $action = $_GET['action'];
 if(array_key_exists('action',$_POST)) 
	 $action = $_POST['action'];
 

 if($action == 'delete' && array_key_exists('delete', $_GET)) {
	$id = intval($_GET['delete']);
 	mysql_query("DELETE FROM judges_teams_timeslots_link WHERE id='$id'");
	message_push(happy(i18n("Judging team timeslot successfully removed")));
 }

 if($action == 'empty' && array_key_exists('empty',$_GET)) {
	$id = intval($_GET['empty']);
 	mysql_query("DELETE FROM judges_teams_timeslots_link WHERE judges_teams_id='$id'");
	message_push(happy(i18n("Judging team timeslots successfully removed")));
 }

 if($action == 'assign') {
	//the db handles the uniqueness (to ensure the same timeslot isnt assigned to the same team more than once)
	//so all we'll do here is just mass insert without regards for whats already there.
	if(count($_POST['teams']) && count($_POST['timeslots'])) {
		foreach($_POST['teams'] AS $tm) {
			foreach($_POST['timeslots'] AS $ts) {
				mysql_query("INSERT INTO judges_teams_timeslots_link (judges_teams_id,judges_timeslots_id,year) 
						VALUES ('$tm','$ts','{$config['FAIRYEAR']}')");

			}
		}
		message_push(happy(i18n("%1 Timeslots assigned to %2 teams",array(count($_POST['timeslots']),count($_POST['teams'])))));
	} else {
		message_push(error(i18n("You must select both team(s) and timeslot(s) to assign")));
	}
 }



  send_header("Judging Teams Timeslots",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judges' => 'admin/judges.php')
				);

 ?>
<script language="javascript" type="text/javascript">
function checkall(what)
{
	for(i=0;i<document.forms.teamstimeslots.elements.length;i++) {
		if(document.forms.teamstimeslots.elements[i].name==what+"[]")
			document.forms.teamstimeslots.elements[i].checked=true;

	}


	return false;
}
function checknone(what)
{
	for(i=0;i<document.forms.teamstimeslots.elements.length;i++) {
		if(document.forms.teamstimeslots.elements[i].name==what+"[]")
			document.forms.teamstimeslots.elements[i].checked=false;

	}

	return false;
}
function checkinvert(what)
{

	for(i=0;i<document.forms.teamstimeslots.elements.length;i++) {
		if(document.forms.teamstimeslots.elements[i].name==what+"[]")
			document.forms.teamstimeslots.elements[i].checked=!document.forms.teamstimeslots.elements[i].checked;

	}

	return false;
}
</script>

 <?

 	echo "<br />";

 	echo "<form name=\"teamstimeslots\" method=\"post\" action=\"judges_teams_timeslots.php\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"assign\">";

	echo "Choose timeslots to assign: <br />";
	echo "<a href=\"\" onclick=\"return checkall('timeslots')\">select all</a>";
	echo "&nbsp;|&nbsp";
	echo "<a href=\"\" onclick=\"return checknone('timeslots')\">select none</a>";
	echo "&nbsp;|&nbsp";
	echo "<a href=\"\" onclick=\"return checkinvert('timeslots')\">invert selection</a>";


	$q=mysql_query("SELECT DISTINCT(date) AS d FROM judges_timeslots WHERE year='".$config['FAIRYEAR']."'");
	if(mysql_num_rows($q)>1)
		$show_date=true;
	else
		$show_date=false;

	echo "<table class=\"summarytable\">";
	echo "<tr>";
	echo "<th>&nbsp;</th>";
	if($show_date)	echo "<th>".i18n("Date")."</th>";
	echo "<th>".i18n("Start Time")."</th>";
	echo "<th>".i18n("End Time")."</th>";
	echo "</tr>\n";
	
	$q=mysql_query("SELECT * FROM judges_timeslots 
			WHERE year='{$config['FAIRYEAR']}' 
			AND round_id='0' ORDER BY date,starttime");
	while($r=mysql_fetch_object($q)) {
		echo "<tr>";
		$span = $show_date ? 4 : 3;
		echo "<td colspan=\"$span\">{$r->name} (".$round_str[$r->type].")</td>";
		$qq = mysql_query("SELECT * FROM judges_timeslots 
					WHERE round_id='{$r->id}' ORDER BY date,starttime");
		while($rr = mysql_fetch_object($qq)) {
			echo "<tr>";
			echo "<td><input type=\"checkbox\" name=\"timeslots[]\" value=\"{$rr->id}\" /></td>";
			if($show_date)	echo "<td>".format_date($r->date)."</td>";
			echo "<td align=\"center\">".format_time($rr->starttime)."</td>";
			echo "<td align=\"center\">".format_time($rr->endtime)."</td>";
			echo "</tr>\n";
		}
	}
	echo "</table>";

	echo "<br />";
	echo "<br />";
	echo "Choose teams to assign the above selected timeslots to:";
	echo "<br />";

	echo "<a href=\"\" onclick=\"return checkall('teams')\">select all</a>";
	echo "&nbsp;|&nbsp";
	echo "<a href=\"\" onclick=\"return checknone('teams')\">select none</a>";
	echo "&nbsp;|&nbsp";
	echo "<a href=\"\" onclick=\"return checkinvert('teams')\">invert selection</a>";

	echo "<table class=\"summarytable\">";
	echo "<tr>";
	echo "<th>&nbsp;</th>";
	echo "<th>".i18n("Team")."</th>";
	echo "<th>".i18n("Timeslots")."</th>";
	echo "</tr>";

	$teams=getJudgingTeams();
	foreach($teams AS $team)
	{
		echo "<tr>";
		echo "<td><input type=\"checkbox\" name=\"teams[]\" value=\"".$team['id']."\" /></td>";
		echo "<td>";
		echo "<b>".$team['name']." (#".$team['num'].")</b><br />";
		$memberlist="";
		if(count($team['members']))
		{
			foreach($team['members'] AS $member)
			{
				echo "&nbsp;&nbsp;";
				if($member['captain']=="yes")
					echo "<i>";
				echo $member['firstname']." ".$member['lastname']."<br />";
				if($member['captain']=="yes")
					echo "</i>";

			}
		}
		echo "</td>";
		echo "<td>";
		//get the timeslots that this team has.
		$q=mysql_query("SELECT 
					judges_teams_timeslots_link.id,
					judges_timeslots.date,
					judges_timeslots.starttime,
					judges_timeslots.endtime
				FROM
					judges_timeslots,
					judges_teams,
					judges_teams_timeslots_link
				WHERE
					judges_teams.id='".$team['id']."' AND
					judges_teams.id=judges_teams_timeslots_link.judges_teams_id AND
					judges_timeslots.id=judges_teams_timeslots_link.judges_timeslots_id
				ORDER BY
					date,starttime
				");
		$numslots=mysql_num_rows($q);

		while($r=mysql_fetch_object($q))
		{
			echo "<nobr>";
			if($show_date)
				echo format_date($r->date);
			echo format_time($r->starttime);
			echo " - ";
			echo format_time($r->endtime);
			echo "&nbsp;&nbsp;<a onclick=\"return confirmClick('Are you sure you want to remove this timeslot from the team?')\" href=\"judges_teams_timeslots.php?action=delete&delete=$r->id\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
			echo "</nobr>";
			echo "<br />";
		}
		if($numslots)
			echo "&nbsp; <a onclick=\"return confirmClick('Are you sure you want to remove all timeslots from the team?')\" href=\"judges_teams_timeslots.php?action=empty&empty=".$team['id']."\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"> remove all</a>";

		echo "</td>";
		echo "</tr>";
	}

 echo "</table>";

 echo "<br />";
 echo "<br />";
 echo "<input type=\"submit\" value=\"".i18n("Assign selected timeslots to selected teams")."\">";
 echo "</form>";


 send_footer();
?>
