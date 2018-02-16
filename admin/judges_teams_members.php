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

 send_header("Judging Team Members",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judges' => 'admin/judges.php')
				);
?>
<script language="javascript" type="text/javascript">
function addbuttonclicked(team)
{
	document.forms.judges.action.value="add";
	document.forms.judges.team_num.value=team;
	document.forms.judges.submit();
}

function switchjudgeinfo()
{
	if(document.forms.judges["judgelist[]"].selectedIndex != -1)
	{
		currentname=document.forms.judges["judgelist[]"].options[document.forms.judges["judgelist[]"].selectedIndex].text;
		currentid=document.forms.judges["judgelist[]"].options[document.forms.judges["judgelist[]"].selectedIndex].value;

		document.forms.judges.judgeinfobutton.disabled=false;
		document.forms.judges.judgeinfobutton.value=currentname;

	}
	else
	{
		document.forms.judges.judgeinfobutton.disabled=true;
		document.forms.judges.judgeinfobutton.value="<? echo i18n("Judge Info")?>";
	}

}

var mousex = 0, mousey = 0;
var selectedMemberId;

function showMemberDetails(judgeId){
	if(judgeId == undefined){
		judgeId = document.forms.judges["judgelist[]"].options[document.forms.judges["judgelist[]"].selectedIndex].value;
	}
	$('#infodiv').load("judges_info.php?id=" + judgeId,
		function(){ eval('doShowMemberDetails(' + judgeId + ');'); }
	);
}

function editMember(memberId){
	if(memberId == undefined) memberId = selectedMemberId;
	hideMemberDetails();
	window.open("user_editor_window.php?id="+memberId,"UserEditor","location=no,menubar=no,directories=no,toolbar=no,width=770,height=500,scrollbars=yes");
}

function hideMemberDetails(){
	$('#infodiv').css("display", "none");
	$('#infodivcover').css("display", "none");
}

function doShowMemberDetails(judgeId){
	selectedMemberId = judgeId;
	$('#infodiv').css("top", mousey + 5);
	$('#infodiv').css("left", mousex + 20);
	$('#infodiv').css("display", "inline");
	$('#infodivcover').css("top", mousey + 5);
	$('#infodivcover').css("left", mousex + 20);
	$('#infodivcover').css("display", "inline");
	$('#infodivcover').css("width", $('#infodiv').width());
	$('#infodivcover').css("height", $('#infodiv').height());
}

jQuery(document).ready(function(){
	$('#infodivcover').click(function(){ editMember(); });
	$(document).mousemove(function(e){
		mousex = e.pageX;
		mousey = e.pageY;
	}); 
});

</script>
<?

	if($_POST['action']=="add" && $_POST['team_num'] && count($_POST['judgelist'])>0)
	{
		//first check if this team exists.
		$q=mysql_query("SELECT id,name FROM judges_teams WHERE num='".$_POST['team_num']."' AND year='".$config['FAIRYEAR']."'");
		if(mysql_num_rows($q))
		{
			$r=mysql_fetch_object($q);
			$team_id=$r->id;
			$team_name=$r->name;

			//if the team is empty, we'll add the first person as the captain
			$team=getJudgingTeam($team_id);
			if(count($team['members']))
				$captain='no';
			else
				$captain='yes';
		}
		$added=0;

		foreach($_POST['judgelist'] AS $selectedjudge)
		{
			//before we insert them, we need to make sure they dont already belong to this team.  We can not have the same judge assigned to the same team multiple times.

			$q=mysql_query("SELECT * FROM judges_teams_link WHERE users_id='$selectedjudge' AND judges_teams_id='$team_id'");
			if(mysql_num_rows($q))
			{
				echo notice(i18n("Judge (%1) already belongs to judging team: %2",array($selectedjudge,$team_name)));

			}
			else
			{
				//lets make the first one we add a captain, the rest, non-captains :)
				mysql_query("INSERT INTO judges_teams_link (users_id,judges_teams_id,captain,year) VALUES ('$selectedjudge','$team_id','$captain','".$config['FAIRYEAR']."')");	
				$added++;
			}
			//if this is alreayd no, then who cares, but if its the first one that is going into the new team, then
			//captain will be yes, and we only want the first one assigned to a new team to be the captain
			//sno now we can set this back to no
			$captain='no';

		}
		if($added==1) $j=i18n("judge");
		else $j=i18n("judges");

		echo happy(i18n("%1 %2 added to team #%3 (%4)",array($added,$j,$_POST['team_num'],$team_name)));
	}

	if($_GET['action']=="del" && $_GET['team_num'] && $_GET['team_id'] && $_GET['users_id'])
	{
		mysql_query("DELETE FROM judges_teams_link WHERE users_id='".$_GET['users_id']."' AND judges_teams_id='".$_GET['team_id']."' AND year='".$config['FAIRYEAR']."'");
		echo happy(i18n("Removed judge from team #%1 (%2)",array($_GET['team_num'],$_GET['team_name'])));

		//if there is still members left in the team, make sure we have a captain still
		$q=mysql_query("SELECT * FROM judges_teams_link WHERE judges_teams_id='".$_GET['team_id']."' AND year='".$config['FAIRYEAR']."'");
		if(mysql_num_rows($q))
		{
			//make sure the team still has a captain!
			//FIXME: this might best come from the "i am willing to be a team captain" question under the judges profile
			$gotcaptain=false;
			$first=true;
			while($r=mysql_fetch_object($q))
			{
				if($first)
				{
					$firstjudge=$r->users_id;
					$first=false;
				}

				if($r->captain=="yes")
				{
					$gotcaptain=true;
					break;
				}
			}
			if(!$gotcaptain)
			{
				//make the first judge the captain
				mysql_query("UPDATE judges_teams_link SET captain='yes' WHERE judges_teams_id='".$_GET['team_id']."' AND users_id='$firstjudge' AND year='".$config['FAIRYEAR']."'");	
				echo notice(i18n("Team captain was removed. A new team captain has been automatically assigned"));
			}
		}
	}

	if($_GET['action']=="empty" && $_GET['team_num'] && $_GET['team_id'])
	{
		mysql_query("DELETE FROM judges_teams_link WHERE judges_teams_id='".$_GET['team_id']."' AND year='".$config['FAIRYEAR']."'");
		echo happy(i18n("Emptied all judges from team #%1 (%2)",array($_GET['team_num'],$_GET['team_name'])));
	}

	if($_POST['action']=="saveteamnames")
	{
		if(count($_POST['team_names']))
		{
			foreach($_POST['team_names'] AS $team_id=>$team_name)
			{
				mysql_query("UPDATE judges_teams SET name='".mysql_escape_string(stripslashes($team_name))."' WHERE id='$team_id'");
			}
			echo happy(i18n("Team names successfully saved"));
		}
		
	}

	if($_GET['action']=="addcaptain")
	{

		//teams can have as many captains as they want, so just add it.
		mysql_query("UPDATE judges_teams_link SET captain='yes' WHERE judges_teams_id='".$_GET['team_id']."' AND users_id='".$_GET['judge_id']."'");
		echo happy(i18n("Team captain assigned"));
	}

	if($_GET['action']=="removecaptain")
	{
		//teams must always have at least one captain, so if we only have one, and we are trying to remove it, dont let them!
		$q=mysql_query("SELECT * FROM judges_teams_link WHERE captain='yes' AND judges_teams_id='".$_GET['team_id']."'");
		if(mysql_num_rows($q)<2)
		{
			echo error(i18n("A judge team must always have at least one captain"));
		}
		else
		{
			mysql_query("UPDATE judges_teams_link SET captain='no' WHERE judges_teams_id='".$_GET['team_id']."' AND users_id='".$_GET['judge_id']."'");
			echo happy(i18n("Team captain removed"));
		}
	}

	if(!$_SESSION['viewstate']['judges_teams_list_show'])
		$_SESSION['viewstate']['judges_teams_list_show']='unassigned';
	//now update the judges_teams_list_show viewstate
	if($_GET['judges_teams_list_show'])
		$_SESSION['viewstate']['judges_teams_list_show']=$_GET['judges_teams_list_show'];

	echo "<form name=\"judges\" method=\"post\" action=\"judges_teams_members.php\">";
	echo "<input type=\"hidden\" name=\"action\">";
	echo "<input type=\"hidden\" name=\"team_id\">";
	echo "<input type=\"hidden\" name=\"team_num\">";
	echo "<input type=\"hidden\" name=\"team_name\">";
	echo "<input type=\"hidden\" name=\"users_id\">";
	echo "<table>";
	echo "<tr>";
	echo "<th>".i18n("Judges List");
	echo "<br />";
	echo "<input disabled=\"true\" name=\"judgeinfobutton\" id=\"judgeinfobutton\" onclick=\"showMemberDetails()\" type=\"button\" value=\"".i18n("Judge Info")."\">";
	echo "</th>";
	echo "<th>".i18n("Judge Teams")."</th>";
	echo "</tr>";
	echo "<tr><td valign=\"top\">";
	echo "<table width=\"100%\"><tr>";
	if($_SESSION['viewstate']['judges_teams_list_show']=='all')
	{
		echo "<td align=left><a href=\"judges_teams_members.php?judges_teams_list_show=unassigned\">".i18n("show unassigned")."</a></td>";
		echo "<td align=right><b>".i18n("show all")."</b></td>";
	}
	else
	{
		echo "<td align=left><b>".i18n("show unassigned")."</b></td>";
		echo "<td align=right><a href=\"judges_teams_members.php?judges_teams_list_show=all\">".i18n("show all")."</a></td>";

	}
	echo "</tr></table>";


	/* Load all the judges (judge_complete=yes, deleted=no, year=fairyear) */
	$judgelist = judges_load_all();

	/* Load all the teams */
	$teams = array();
	$q = mysql_query("SELECT * FROM judges_teams WHERE year='{$config['FAIRYEAR']}'");
	while($i = mysql_fetch_assoc($q)) {
		$teams[$i['id']] = $i;
	}

	/* And the links */
	$links = array();
	$q = mysql_query("SELECT * FROM judges_teams_link WHERE year='{$config['FAIRYEAR']}'");
	while($i = mysql_fetch_assoc($q)) {
		$judgelist[$i['users_id']]['teams_links'][] = $i;
	}

    $jlist = array();
	if($_SESSION['viewstate']['judges_teams_list_show']=='unassigned') {
		/* Remove all judges that have a link */
		foreach($judgelist as $j) {
			if(count($j['teams_links']) == 0) $jlist[] = $j['id'];
		}
	} else {
		$jlist = array_keys($judgelist);
	}

	echo "<center>";
	echo i18n("Listing %1 judges",array(count($jlist)));
	echo "<br />";
	echo "</center>";
	echo mysql_error();
	echo "<select name=\"judgelist[]\" onchange=\"switchjudgeinfo()\" multiple=\"multiple\" style=\"width: 250px; height: 600px;\">";

	foreach($jlist as $jid) {
		$u = &$judgelist[$jid];
		if($u['firstname'] && $u['lastname'])
			echo "<option value=\"$jid\">{$u['firstname']} {$u['lastname']} (" . implode(' ', $u['languages']) . ")</option>\n";
	}
	unset($u);

	echo "</select>";
	echo "</td>";
	echo "<td valign=\"top\">";

	$teams=getJudgingTeams();

	foreach($teams AS $team) {
		echo "<hr>";

		echo "<table width=\"100%\">";
		echo "<tr><td valign=top width=\"80\">";
		echo "<input onclick=\"addbuttonclicked('".$team['num']."')\" type=\"button\" value=\"Add &gt;&gt;\">";
		echo "</td><td>";

		echo "<table width=\"100%\">\n";
		echo "<tr><th colspan=\"2\" align=\"left\">#".$team['num'].": ";
		echo $team['name'];
		echo "</th></tr>\n";
		echo "<tr><td colspan=\"2\">";
		foreach($team['rounds'] as $ts) {
			echo "{$ts['name']}: ".format_time($ts['starttime'])." - ".format_time($ts['endtime'])."<br />";
		}
		echo "</td></tr>";

		if(count($team['members'])) {
			foreach($team['members'] AS $member) {
				$j = &$judgelist[$member['id']];
				echo "<tr><td>";

				$langerr=false;
				foreach($team['languages'] AS $teamlang) {
					if(!in_array($teamlang,$j['languages'])) {
						$langerr=true;
						break;
					}
				}

				echo "<a onclick=\"return confirmClick('Are you sure you want to remove this judge from this team?')\" href=\"judges_teams_members.php?action=del&team_id=".$team['id']."&team_num=".$team['num']."&users_id=".$member['id']."&team_name=".rawurlencode($team['name'])."\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
				echo "</td><td width=\"100%\">";
				if($langerr) echo "<span class=\"error\" style=\"width: 100%; display: block;\">";
				if($member['captain']=="yes") {
					echo "<a title=\"Captain - Click to remove captain status\" href=\"judges_teams_members.php?action=removecaptain&team_id=".$team['id']."&judge_id=".$member['id']."\">";
					echo "<img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/bookmark.".$config['icon_extension']."\">";
					echo "</a>&nbsp;";

				}
				else {
					echo "<a title=\"Non-Captain - Click to make a team captain\" href=\"judges_teams_members.php?action=addcaptain&team_id=".$team['id']."&judge_id=".$member['id']."\">";
					echo "<img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/bookmark_disabled.".$config['icon_extension']."\">";
					echo "</a>&nbsp;";

				}
				echo "<a  onclick=\"showMemberDetails(" . $member['id'] . ");\">";
				echo $member['firstname']." ".$member['lastname'];
				if(is_array($j['languages'])) 
					$l = is_array($j['languages']) ? join(' ',$j['languages']) : '';

				echo "</a>&nbsp;<span style=\"font-size: 1.0em;\">($l)</span>\n";
				if($langerr) echo "</span>\n";
				echo "</td></tr>";
			}

			echo "<tr><td colspan=\"2\">";
			echo "<a onclick=\"return confirmClick('Are you sure you want to empty all judges from this team?')\" href=\"judges_teams_members.php?action=empty&team_id=".$team['id']."&team_num=".$team['num']."&team_name=".rawurlencode($team['name'])."\">";
			echo " ".i18n("Empty All Members")." ";
			echo "<img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\">";
			echo "</a>";
			echo "</td></tr>";
		}
		else
		{
			echo "<tr><td colspan=\"2\">";
			echo error(i18n("Team has no members"),"inline");
			echo "</td></tr>";
		}

		echo "</table>";

		echo "</td></tr></table>";
	}

	echo "<br />";

	echo "</td></tr>";
	echo "</table>";
	echo "</form>";
	echo '<div id="infodiv" style="background-color: #DDF; border:solid;'
		. ' border-width:1px;'
		. ' border-color: #000;'
		. ' position:absolute;'
		. ' top: 0px; left:0px;'
		. ' overflow:hidden; display:none;"'
		. '></div>';
	echo '<div id="infodivcover" style="'
		. ' position:absolute;'
		. ' display:none;"'
		. ' onmouseout="hideMemberDetails();"'
		. '></div>';


	send_footer();



?>
