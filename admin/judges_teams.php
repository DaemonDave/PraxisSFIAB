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
 include "judges.inc.php";

	if($_GET['edit']) $edit=$_GET['edit'];
	if($_POST['edit']) $edit=$_POST['edit'];
	if($_GET['action']) $action=$_GET['action'];
	if($_POST['action']) $action=$_POST['action'];

	if($action=="delete" && $_GET['delete'])
	{
		//ALSO DELETE: team members, timeslots, projects, awards
		mysql_query("DELETE FROM judges_teams_link WHERE judges_teams_id='".$_GET['delete']."' AND year='".$config['FAIRYEAR']."'");
		mysql_query("DELETE FROM judges_teams_timeslots_link WHERE judges_teams_id='".$_GET['delete']."' AND year='".$config['FAIRYEAR']."'");
		mysql_query("DELETE FROM judges_teams_timeslots_projects_link WHERE judges_teams_id='".$_GET['delete']."' AND year='".$config['FAIRYEAR']."'");
		mysql_query("DELETE FROM judges_teams_awards_link WHERE judges_teams_id='".$_GET['delete']."' AND year='".$config['FAIRYEAR']."'");
		mysql_query("DELETE FROM judges_teams WHERE id='".$_GET['delete']."' AND year='".$config['FAIRYEAR']."'");
		message_push(happy(i18n("Judge team successfully removed, and all of its corresponding members, timeslots, projects and awards unlinked from team")));
	}

	if($action=="deletealldivisional")
	{
		$q2=mysql_query("SELECT *
				FROM 	
					judges_teams
				WHERE 
					year='".$config['FAIRYEAR']."'
					AND autocreate_type_id='1'
					");
					echo mysql_error();
		$numdeleted=0;
		while($r2=mysql_fetch_object($q2))
		{
			//okay now we can start deleting things! whew!
			//first delete any linkings to the team
			mysql_query("DELETE FROM judges_teams_link WHERE judges_teams_id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			mysql_query("DELETE FROM judges_teams_timeslots_link WHERE judges_teams_id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			mysql_query("DELETE FROM judges_teams_timeslots_projects_link WHERE judges_teams_id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			mysql_query("DELETE FROM judges_teams_awards_link WHERE judges_teams_id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			mysql_query("DELETE FROM judges_teams WHERE id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			$numdeleted++;
		}
		if($numdeleted)
			message_push(happy(i18n("Successfully deleted %1 auto-created divisional team(s)",array($numdeleted))));
		else
			message_push(error(i18n("There were no auto-created divisional teams to delete")));
	}
	
	if($action=="deleteall")
	{
		$q2=mysql_query("SELECT *
				FROM 	judges_teams
				WHERE 
					year='".$config['FAIRYEAR']."'
					");
		$numdeleted=0;
		while($r2=mysql_fetch_object($q2))
		{
			//okay now we can start deleting things! whew!
			//first delete any linkings to the team
			mysql_query("DELETE FROM judges_teams_link WHERE judges_teams_id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			mysql_query("DELETE FROM judges_teams_timeslots_link WHERE judges_teams_id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			mysql_query("DELETE FROM judges_teams_timeslots_projects_link WHERE judges_teams_id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			mysql_query("DELETE FROM judges_teams_awards_link WHERE judges_teams_id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			mysql_query("DELETE FROM judges_teams WHERE id='$r2->id' AND year='".$config['FAIRYEAR']."'");
			$numdeleted++;
		}
		if($numdeleted)
			message_push(happy(i18n("Successfully deleted %1 team(s)",array($numdeleted))));
		else
			message_push(error(i18n("There were no teams to delete")));
	}

	if(($action=="save" || $action=="assign") && $edit)
	{
		//if we're updating or assigning, it doesnt matter, lets do the same thing (save record, add award
		//but when we're done, if we're "assign" then go back to edit that team
		//if we're save, then go back to the team list
		$err=false;
		$q=mysql_query("UPDATE judges_teams SET num='".$_POST['team_num']."', name='".mysql_escape_string(stripslashes($_POST['team_name']))."' WHERE id='$edit'");
		if(mysql_error())
		{
			$err=true;
			message_push(error(mysql_error()));
		}

		if($_POST['award'])
		{
			//we can only have 1 special award assigned to any given team so we'll be able to properly
			//manage the projects that we assign to the team.  If there was more than one special award
			//the judges wouldnt know which projects to judge for which award.  This doesnt apply for divisions
			//because the category/division is obvious based on project numbesr.  A divisional judge team could easily
			//be assigned to do all of Comp Sci - Junior, Intermediate and Senior without any problems.
			$q=mysql_query("SELECT award_types.type FROM award_awards, award_types WHERE award_awards.award_types_id=award_types.id AND award_awards.id='".$_POST['award']."'");
			$aw=mysql_fetch_object($q);

			$addaward=true;
			if($aw->type=="Special")
			{
				$q=mysql_query("SELECT COUNT(*) AS num FROM 
							judges_teams_awards_link,
							award_awards,
							award_types
						WHERE
							judges_teams_awards_link.judges_teams_id='$edit'
							AND judges_teams_awards_link.award_awards_id=award_awards.id
							AND award_awards.award_types_id=award_types.id
							AND award_types.type='Special'
						");
				$r=mysql_fetch_object($q);
				echo "special awards: $r->num";
				if($r->num)
				{
					$addaward=false;
					message_push(error(i18n("Sorry, only one Special Award can be assigned to a judging team")));
				}
				else
				{
					$addaward=true;
				}
			}
			
			if($addaward)
			{
				//link up the award
				mysql_query("INSERT INTO judges_teams_awards_link (award_awards_id,judges_teams_id,year) VALUES ('".$_POST['award']."','$edit','".$config['FAIRYEAR']."')");
				message_push(happy(i18n("Award assigned to team")));
			}
		}


		if($action=="assign")
			$action="edit";
		else if($action=="save")
		{
			if($err)
				$action="edit";
			else
			{
				message_push(happy(i18n("Team successfully saved")));
				unset($action);
				unset($edit);
			}
		}
	}

	if($action=="unassign")
	{
		mysql_query("DELETE FROM judges_teams_awards_link WHERE judges_teams_id='$edit' AND award_awards_id='".$_GET['unassign']."' AND year='".$config['FAIRYEAR']."'");
		message_push(happy(i18n("Award unassigned from judge team")));
		//keep editing the same team
		$action="edit";
	}

	if($action=="createall")
	{
		//first make sure we dont have any non-divisional award teams (dont want people hitting refresh and adding all the teams twice
		$q=mysql_query("SELECT COUNT(*) AS c FROM judges_teams WHERE autocreate_type_id!='1' AND year='".$config['FAIRYEAR']."'");
		$r=mysql_fetch_object($q);
		if($r->c)
		{
			message_push(error(i18n("Cannot 'Create All' teams when any divisional teams currently exist.  Try deleting all existing non-divisional teams first.")));
		}
		else
		{
			//grab all the awards
			$q=mysql_query("SELECT 
						award_awards.*, 
						award_types.type AS award_type, 
						award_types.order AS award_types_order 
					FROM 	
						award_awards,
						award_types 
					WHERE 	
						award_awards.award_types_id=award_types.id 
						AND award_awards.year='".$config['FAIRYEAR']."' 
						AND award_types.year='".$config['FAIRYEAR']."' 
						AND award_types_id!='1'
					ORDER BY 
						award_types_order, 
						award_awards.order,
						name");
			$num=1;
			while($r=mysql_fetch_object($q))
			{
//				print_r($r);
				$name=mysql_escape_string("($r->award_type) $r->name");
				mysql_query("INSERT INTO judges_teams(num,name,autocreate_type_id,year) VALUES ('$num','$name','$r->award_types_id','".$config['FAIRYEAR']."')");
				echo mysql_error();
				$team_id=mysql_insert_id();
				//now link the new team to the award
				mysql_query("INSERT INTO judges_teams_awards_link (award_awards_id,judges_teams_id,year) VALUES ('$r->id','$team_id','".$config['FAIRYEAR']."')");
				message_push(happy(i18n("Created team #%1: %2",array($num,$name))));
				$num++;
			}
		}
	}

	if($action=="add" && $_GET['num'])
	{
		mysql_query("INSERT INTO judges_teams(num,year) VALUES ('".$_GET['num']."','".$config['FAIRYEAR']."')");
		echo mysql_error();
		$edit=mysql_insert_id();
		$action="edit";
	}

	if($action=="edit" && $edit)
	{
		send_header("Edit Judging Team",
 			array('Committee Main' => 'committee_main.php',
				'Administration' => 'admin/index.php',
				'Judges' => 'admin/judges.php',
				'Manage Judging Teams' => 'admin/judges_teams.php'));
?>
<script language="javascript" type="text/javascript">
function addclicked()
{
	document.forms.judges.action.value="assign";
	document.forms.judges.submit();
}


</script>

<?
	
		echo "<br />";
		$team=getJudgingTeam($edit);

		if(!$_SESSION['viewstate']['judges_teams_awards_show'])
			$_SESSION['viewstate']['judges_teams_awards_show']='unassigned';
		//now update the judges_teams_awards_show viewstate
		if($_GET['judges_teams_awards_show'])
			$_SESSION['viewstate']['judges_teams_awards_show']=$_GET['judges_teams_awards_show'];

		echo "<form name=\"judges\" method=\"post\" action=\"judges_teams.php\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"save\">";
		echo "<input type=\"hidden\" name=\"edit\" value=\"$edit\">";

		echo "<table>";
		echo "<tr><td>".i18n("Team Number").":</td><td><input type=\"text\" size=\"4\" name=\"team_num\" value=\"".$team['num']."\"></td></tr>";
		echo "<tr><td>".i18n("Team Name").":</td><td><input type=\"text\" size=\"40\" name=\"team_name\" value=\"".$team['name']."\"></td></tr>";
		echo "<tr><td>".i18n("Awards").":</td><td>";

		if(count($team['awards']))
		{
			foreach($team['awards'] AS $award)
			{
				echo "<a onclick=\"return confirmClick('Are you sure you want to unassign this award from this team?')\" href=\"judges_teams.php?action=unassign&unassign=".$award['id']."&edit=".$team['id']."\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
				echo " (".$award['award_type'].") ".$award['name']." <br />";
			}
		}

		echo "<table><tr>";
		if($_SESSION['viewstate']['judges_teams_awards_show']=='all')
		{
			echo "<td align=left><a href=\"judges_teams.php?action=edit&edit=$edit&judges_teams_awards_show=unassigned\">".i18n("show unassigned")."</a></td>";
			echo "<td align=right><b>".i18n("show all")."</b></td>";
		}
		else
		{
			echo "<td align=left><b>".i18n("show unassigned")."</b></td>";
			echo "<td align=right><a href=\"judges_teams.php?action=edit&edit=$edit&judges_teams_awards_show=all\">".i18n("show all")."</a></td>";

		}
		echo "</tr>";

		if($_SESSION['viewstate']['judges_teams_awards_show']=='all')
		{
			$querystr="SELECT 
					award_awards.id, 
					award_awards.name, 
					award_types.type AS award_type,
					award_types.order AS award_type_order
				FROM 
					award_awards, 
					award_types
				WHERE 
					award_awards.year='".$config['FAIRYEAR']."'
					AND award_types.id=award_awards.award_types_id
					AND award_types.year='{$config['FAIRYEAR']}'
				ORDER BY 
					award_type_order,
					name
				";
		}
		else
		{
			$querystr="SELECT 
					award_awards.id, 
					award_awards.name, 
					award_types.type AS award_type,
					award_types.order AS award_type_order
				FROM 
					(
					award_awards,
					award_types
					)
					LEFT JOIN judges_teams_awards_link ON award_awards.id = judges_teams_awards_link.award_awards_id
				WHERE 
					award_awards.year='".$config['FAIRYEAR']."' AND 
					judges_teams_awards_link.award_awards_id IS NULL
					AND award_types.id=award_awards.award_types_id
					AND award_types.year='{$config['FAIRYEAR']}'
				ORDER BY 
					award_type_order,
					name";
		}

		echo "<tr><td colspan=2>";
		$q=mysql_query($querystr);

		echo mysql_error();
		echo "<select name=\"award\">";
		echo "<option value=\"\">".i18n("Choose award to assign to team")."</option>\n";

		while($r=mysql_fetch_object($q))
		{
			echo "<option value=\"$r->id\">($r->award_type) $r->name</option>\n";
		}

		echo "</select>";
		echo "<input type=\"button\" value=\"Add\" onclick=\"addclicked()\">";
		echo "</td></tr>";
		echo "</table>";

		echo "</td></tr>";
		echo "</table>";
		echo "<input type=submit value=\"".i18n("Save Changes")."\">";
		echo "</form>";


	}
	else
	{
		send_header("Manage Judging Teams",
 			array('Committee Main' => 'committee_main.php',
				'Administration' => 'admin/index.php',
				'Judges' => 'admin/judges.php'));
		echo "<br />";

		$teams=getJudgingTeams();
		//print_r($teams);

		if(!count($teams))
		{
			echo "<a href=\"judges_teams.php?action=createall\">".i18n("Automatically create one new team for every non-divisional award")."</a><br />";
			echo "<a href=\"judges_teams.php?action=add&num=1\">".i18n("Manually add individual team")."</a><br />";
		}
		else
		{
			//grab an array of all the current team numbers
			foreach($teams AS $team)
				$teamnumbers[$team['num']]=1;

			//start at 1, and find the next available team number
			$newteamnum=1;
			while($teamnumbers[$newteamnum]==1)
			{
				$newteamnum++;
			}


			echo "<table width=\"95%\">";
			echo "<tr><td>";
			echo "<a href=\"judges_teams.php?action=add&num=$newteamnum\">Add individual team</a><br />";
			echo "</td><td>";
			echo "<a onclick=\"return confirmClick('".i18n("Are you sure you want to delete all teams that are assigned to divisional awards?")."')\" href=\"judges_teams.php?action=deletealldivisional\">Delete all teams assigned to divisional awards</a>";
			echo "<br />";
			echo "<a onclick=\"return confirmClick('".i18n("Are you sure you want to delete all teams?")."')\" href=\"judges_teams.php?action=deleteall\">Delete all teams</a><br />";
			echo "</td></tr></table>";

			echo "<table class=\"summarytable\">\n";
			echo "<thead style=\"cursor:pointer\"><tr><th>Num</th>";
			echo "<th>Team Name</th>";
			echo "<th>Award(s)</th>";
			echo "<th>Actions</th>";
			echo "</tr></thead>";
			foreach($teams AS $team)
			{
				echo "<tr><td>#".$team['num']."</td><td>";
				echo $team['name'];
				echo "</td>";

				echo "<td>";
				if(count($team['awards']))
				{
					foreach($team['awards'] AS $award)
					{
						echo $award['name']." <br />";
					}
				}
				else
				{
					echo error(i18n("No award assigned to team"),"inline");
				}
				echo "</td>";

				echo " <td align=\"center\">";
				echo "<a href=\"judges_teams.php?action=edit&edit=".$team['id']."\"><img border=\"0\" src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\"></a>";
				echo "&nbsp;";
				echo "<a onclick=\"return confirmClick('Are you sure you want to remove this team?')\" href=\"judges_teams.php?action=delete&delete=".$team['id']."\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";

				echo " </td>\n";
				echo "</tr>\n";



			}
			echo "</table>";
			echo "<script type=\"text/javascript\">$('.summarytable').tablesorter();</script>";
			echo "<br />";
		}
	}
	send_footer();



?>
