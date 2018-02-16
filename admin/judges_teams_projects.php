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
 require("judges.inc.php");
 require("../projects.inc.php");

 send_header("Judging Teams Projects",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judges' => 'admin/judges.php')
				);
 ?>
<script language="javascript" type="text/javascript">
function assign(ts)
{
	document.forms.teamsprojects.timeslot.value=ts;
	document.forms.teamsprojects.submit();
}

function eligibleclick()
{
	if(document.forms.teamsprojects.showeligible.checked)
	{
		window.location.href="judges_teams_projects.php?action=edit&edit="+document.forms.teamsprojects.edit.value+"&judges_projects_list_eligible=true";
	}
	else
	{
		window.location.href="judges_teams_projects.php?action=edit&edit="+document.forms.teamsprojects.edit.value+"&judges_projects_list_eligible=false";
	}
}
</script>

 <?

 	echo "<br />";

 if($_GET['action']) $action=$_GET['action'];
 else if($_POST['action']) $action=$_POST['action'];

 if($_GET['edit']) $edit=$_GET['edit'];
 else if($_POST['edit']) $edit=$_POST['edit'];

if(!$_SESSION['viewstate']['judges_projects_list_show'])
	$_SESSION['viewstate']['judges_projects_list_show']='unassigned';
//now update the judges_teams_list_show viewstate
if($_GET['judges_projects_list_show'])
	$_SESSION['viewstate']['judges_projects_list_show']=$_GET['judges_projects_list_show'];

if(!$_SESSION['viewstate']['judges_projects_list_eligible'])
	$_SESSION['viewstate']['judges_projects_list_eligible']='true';
//now update the judges_teams_list_show viewstate
if($_GET['judges_projects_list_eligible'])
	$_SESSION['viewstate']['judges_projects_list_eligible']=$_GET['judges_projects_list_eligible'];


if($_GET['action']=="delete" && $_GET['delete'] && $_GET['edit'])
{
	mysql_query("DELETE FROM judges_teams_timeslots_projects_link WHERE id='".$_GET['delete']."'");
	echo happy(i18n("Judging team project successfully removed"));
	$action="edit";
}


if($_POST['action']=="assign" && $_POST['edit'] && $_POST['timeslot'] && $_POST['project_id'])
{
	mysql_query("INSERT INTO judges_teams_timeslots_projects_link (judges_teams_id,judges_timeslots_id,projects_id,year) VALUES ('".$_POST['edit']."','".$_POST['timeslot']."','".$_POST['project_id']."','".$config['FAIRYEAR']."')");
	echo happy(i18n("Project assigned to team timeslot"));
}

$q=mysql_query("SELECT DISTINCT(date) AS d FROM judges_timeslots WHERE year='".$config['FAIRYEAR']."'");
if(mysql_num_rows($q)>1)
	$show_date=true;
else
	$show_date=false;


if( ($action=="edit" || $action=="assign" ) && $edit)
 {
	echo "<a href=\"judges_teams_projects.php\">Back to Judging Teams Projects List</a>";
 	echo "<form name=\"teamsprojects\" method=\"post\" action=\"judges_teams_projects.php\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"assign\">\n";
	echo "<input type=\"hidden\" name=\"edit\" value=\"$edit\">\n";
	echo "<input type=\"hidden\" name=\"timeslot\" value=\"\">\n";
	$team=getJudgingTeam($edit);

	echo "<b>".$team['name']." (#".$team['num'].")</b><br />";
	if(count($team['members']))
	{
		$memberlist="&nbsp;&nbsp;";
		foreach($team['members'] AS $member)
		{
			if($member['captain']=="yes")
				$memberlist.="<i>";
			$memberlist.=$member['firstname']." ".$member['lastname'];
			if($member['captain']=="yes")
				$memberlist.="</i>";
			$memberlist.=", ";
		}
		echo "<b>".i18n("Judging Team Members").": </b>";
		$memberlist=substr($memberlist,0,-2);
		echo "<br />";
	}
	else
		$memberlist=error(i18n("Team has no members assigned to it.  <a href=\"judges_teams_members.php\">Assign Judges Here</a>"));
	echo $memberlist;
	echo "<br />";
	echo "<br />";

	//we need award_ids for use below to get the eligible projects, so lets build the array here while we're displaying the awards
	$award_ids=array();
	if(count($team['awards']))
	{
		$awardlist="&nbsp;&nbsp;";
		foreach($team['awards'] AS $award)
		{
			$awardlist.=$award['name'];
			$awardlist.=", ";
			$award_ids[]=$award['id'];
		}
		echo "<b>".i18n("Judging Team Awards").": </b>";
		$awardlist=substr($awardlist,0,-2);
		echo "<br />";
	}
	else
		$awardlist=error(i18n("Team has no awards assigned to it.  <a href=\"judges_teams.php\">Assign Awards Here</a>"));
	echo $awardlist;

	//get the timeslots that this team has.
	$q=mysql_query("SELECT 
				judges_timeslots.id,
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
	if($numslots)
	{

		echo "<br />";
		echo "<br />";
		if($_SESSION['viewstate']['judges_projects_list_eligible']=='true')
			$ch="checked=\"checked\"";
		else
			$ch="";

		echo "<input $ch onclick=\"eligibleclick()\" type=\"checkbox\" name=\"showeligible\"> ".i18n("Only show projects eligible/nominated for awards assigned to this team");
		echo "<table>";
		echo "</tr>";

		echo "<tr>";
		if($_SESSION['viewstate']['judges_projects_list_show']=='all')
		{
			echo "<td align=left><a href=\"judges_teams_projects.php?action=$action&edit=$edit&judges_projects_list_show=unassigned\">".i18n("show unassigned")."</a></td>";
			echo "<td align=right><b>".i18n("show all")."</b></td>";
		}
		else
		{
			echo "<td align=left><b>".i18n("show unassigned")."</b></td>";
			echo "<td align=right><a href=\"judges_teams_projects.php?action=$action&edit=$edit&judges_projects_list_show=all\">".i18n("show all")."</a></td>";
		}
		echo "<td>&nbsp;</td>";
		echo "</tr>";
		echo "<tr><td colspan=2>";

		if($_SESSION['viewstate']['judges_projects_list_show']=='all')
		{
			$querystr="SELECT 
					projects.id,
					projects.projectnumber,
					projects.title,
					registrations.status
				FROM
					projects,
					registrations
				WHERE
					projectnumber is not null 
					" . getJudgingEligibilityCode(). " AND
					projects.registrations_id=registrations.id AND
					projects.year='".$config['FAIRYEAR']."'
				ORDER BY
					projectnumber";
		}
		else if($_SESSION['viewstate']['judges_projects_list_show']=='unassigned')
		{
			$querystr="SELECT 
					projects.id,
					projects.projectnumber,
					projects.title,
					registrations.status
				FROM
					projects
					LEFT JOIN judges_teams_timeslots_projects_link ON projects.id = judges_teams_timeslots_projects_link.projects_id, 
					registrations
				WHERE
					projectnumber is not null 
					" . getJudgingEligibilityCode(). " AND
					projects.registrations_id=registrations.id AND
					judges_teams_timeslots_projects_link.projects_id IS NULL AND
					projects.year='".$config['FAIRYEAR']."'
				ORDER BY
					projectnumber";
		}
		$pq=mysql_query($querystr);
		echo mysql_error();
	
		$eligibleprojects=getProjectsEligibleOrNominatedForAwards($award_ids);
//		echo nl2br(print_r($eligibleprojects,true));
		//the keys are the project numbers, so lets get an array of those too so we can use in_array below
		$eligibleprojectsnumbers=array_keys($eligibleprojects);
//		echo nl2br(print_r($eligibleprojects,true));

		$numprojects=0;
		echo "<select name=\"project_id\">";
		echo "<option value=\"\">".i18n("Choose Project to Assign to Timeslot")."</option>\n";
		while($pr=mysql_fetch_object($pq)) {
			if($_SESSION['viewstate']['judges_projects_list_eligible']=='true') {
				if(in_array($pr->projectnumber,$eligibleprojectsnumbers)) {
					echo "<option value=\"$pr->id\">$pr->projectnumber - $pr->title</option>\n";
					$numprojects++;
				}
			}
			else {
				echo "<option value=\"$pr->id\">$pr->projectnumber - $pr->title</option>\n";
				$numprojects++;
			}
		}
		echo "</select>";
		echo "</td><td>";
		echo i18n("%1 projects listed",array($numprojects));
		echo "</td></tr>";
		echo "</table>";

		echo "<br />";
		echo "<br />";

		echo "<table class=\"summarytable\">";
		echo "<tr>";
		echo "<th>".i18n("Timeslot")."</th>";
		echo "<th>".i18n("Project")."</th>";
		echo "</tr>";


		while($r=mysql_fetch_object($q)) {
			echo "<tr><td>";

			echo "<nobr>";
			if($show_date)
				echo format_date($r->date)."&nbsp;";
			echo format_time($r->starttime);
			echo " - ";
			echo format_time($r->endtime);
			echo "</nobr>";
			echo "</td><td>";
			
			$projq=mysql_query("SELECT
						judges_teams_timeslots_projects_link.id AS link_id,
						projects.projectnumber,
						projects.id,
						projects.title
					FROM
						projects,
						judges_teams_timeslots_projects_link
					WHERE
						judges_teams_timeslots_projects_link.judges_timeslots_id='$r->id' AND
						judges_teams_timeslots_projects_link.judges_teams_id='".$team['id']."' AND
						judges_teams_timeslots_projects_link.projects_id=projects.id AND
						judges_teams_timeslots_projects_link.year='".$config['FAIRYEAR']."'
					ORDER BY
						projectnumber
					");

		echo mysql_Error();
			while($proj=mysql_fetch_object($projq)) {
				echo "<a onclick=\"return confirmClick('Are you sure you want to remove this project from this team timeslot?')\" href=\"judges_teams_projects.php?action=delete&delete=".$proj->link_id."&edit=".$team['id']."\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
				echo "$proj->projectnumber - $proj->title <br />";

			}
			echo "<input name=\"assignbtn[$r->id]\" type=\"button\" onclick=\"assign('$r->id')\" value=\"".i18n("Assign")."\">";


			echo "</td></tr>";
		}
		echo "</table>";
	}
	else {
		echo error(i18n("Team has no timeslots assigned to it.  <a href=\"judges_teams_timeslots.php\">Assign Timeslots Here</a>"));
	}

	echo "</form>";
 }
 else {
	echo "<input type=\"hidden\" name=\"action\" value=\"assign\">";

	echo "<table class=\"tableview\">";
	echo "<thead><tr>";
	echo "<th>".i18n("Team")."</th>";
	echo "<th>".i18n("Timeslots and Projects")."</th>";
	echo "</tr></thead>";

	$teams=getJudgingTeams();
	foreach($teams AS $team) {
		echo "<tr>";
		echo "<td width=\"200\">";
		echo "<b>".$team['name']." (#".$team['num'].")</b><br />";
		$memberlist="";
		if(count($team['members'])) {
			foreach($team['members'] AS $member) {
				echo "&nbsp;&nbsp;";
				$err=false;
				foreach($team['languages_projects'] AS $projectlang) {
					if(!in_array($projectlang, $member['languages_array'])) { 
						$err=true; 
						break; 
					}
				}
				if($err) echo "<span class=\"error\">";
				if($member['captain']=="yes")
					echo "<i>";
				echo $member['firstname']." ".$member['lastname']." (".$member['languages'].")<br />";
				if($member['captain']=="yes")
					echo "</i>";
				if($err) echo "</span>";
			}
		}
		echo "</td>";
		echo "<td>";
		//get the timeslots that this team has.
		$q=mysql_query("SELECT 
					judges_timeslots.id,
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

		echo "<a href=\"judges_teams_projects.php?action=edit&edit=".$team['id']."\">".i18n("Edit team project assignments")."</a>";

		echo "<table class=\"tableview\" style=\"margin-left: 0px; width: 100%; font-size: 1.0em;\">";

		while($r=mysql_fetch_object($q)) {
			echo "<tr><td width=\"100\" align=\"center\">";

			echo "<nobr>";
			if($show_date)
				echo format_date($r->date)."&nbsp;";
			echo format_time($r->starttime);
			echo " - ";
			echo format_time($r->endtime);
			echo "</nobr>";
			echo "</td><td>";

			$projq=mysql_query("SELECT
					projects.projectnumber,
					projects.id,
					projects.title,
					projects.language
				FROM
					projects,
					judges_teams_timeslots_projects_link
				WHERE
					judges_teams_timeslots_projects_link.judges_timeslots_id='$r->id' AND
					judges_teams_timeslots_projects_link.judges_teams_id='".$team['id']."' AND
					judges_teams_timeslots_projects_link.projects_id=projects.id AND
					judges_teams_timeslots_projects_link.year='".$config['FAIRYEAR']."'
				ORDER BY
					projectnumber
				");

			echo mysql_error();
			while($proj=mysql_fetch_object($projq)) {
                if(!in_array($proj->language,$team['languages_members'])) 
                    echo "<span class=\"error\">";

				echo "$proj->projectnumber - $proj->title ($proj->language)";

                if(!in_array($proj->language,$team['languages'])) 
                    echo "</span>\n";
                echo "<br />";
			}
			echo "</td></tr>";
		}
		echo "</table>";

		echo "</td>";
		echo "</tr>";
	}

	echo "</table>";

 }

 send_footer();
?>
