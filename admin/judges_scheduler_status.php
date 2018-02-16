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
   along with this pr<input type=\"submit\" value=\"".i18n("Save Configuration")."\" />\n";
ogram; see the file COPYING.  If not, write to
   the Free Software Foundation, Inc., 59 Temple Place - Suite 330,
   Boston, MA 02111-1307, USA.
*/
?>
<?
 require("../common.inc.php");
 send_header("Scheduler Status",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judges' => 'admin/judges.php')
				);
?>

<script type="text/javascript">
var starttime=0;
var startpercent=0;
var deltatime=0;
var deltapercent=0;
var avgtimeperpercent=0;
var remainingpercent=0;
var remainingtime=0;

$(document).ready(function() {
	updateStatus();
});

function updateStatus() {
	var url="judges_scheduler_status_output.php";
	$("#updatestatus").html("Updating...");
	$.get(url,null,function(data) { 
			var obj=data.split(":");
			$("#schedulerstatus").html(obj[1]);
			if(obj[0]=="-1") {
				$("#schedulerpercent").html("100%");
				$("#updatestatus").html("Scheduling Complete");
				$("#schedulereta").html("Complete");
			}
			else {
				$("#schedulerpercent").html(obj[0]+"%");
				setTimeout('updateStatus()',5000);
				$("#updatestatus").html("Updating... Done!");
				setTimeout('clearUpdatingMessage()',500);

				var currentTime=new Date();
				if(starttime==0) {
					starttime=currentTime.getTime();
					startpercent=obj[0];
				}
				deltatime=currentTime.getTime()-starttime;
				deltapercent=obj[0]-startpercent;

				avgtimeperpercent=deltatime/deltapercent;
				remainingpercent=100-obj[0];
				remainingtime=remainingpercent*avgtimeperpercent;
				if(remainingtime>0 && remainingtime!="Infinity" && obj[0]>0) {
					$("#schedulereta").html(format_duration(Math.round(remainingtime/1000)));
				}
				else
					$("#schedulereta").html("Calculating...");
			}
	});
}

function clearUpdatingMessage() {
	$("#updatestatus").html("Waiting...");
}

function format_duration(seconds) {
/*
	'1 year|:count years' => 31536000,
	'1 week|:count weeks' => 604800,
	'1 day|:count days' => 86400,
	'1 hour|:count hours' => 3600,
	'1 min|:count min' => 60,
	'1 sec|:count sec' => 1);
*/

	var s=seconds;
	var output='';
	var pl='';
	if(s>86400) {
		var days=Math.floor(s/86400)
		s-=days*86400;
		if(days>1) pl='s'; else pl='';
		output+=days+' day'+pl+' ';
	}
	if(s>3600) {
		var hours=Math.floor(s/3600)
		s-=hours*3600;
		if(hours>1) pl='s'; else pl='';
		output+=hours+' hour'+pl+' ';
	}
	if(s>60) {
		var minutes=Math.floor(s/60)
		s-=minutes*60;
		if(minutes>1) pl='s'; else pl='';
		output+=minutes+' minute'+pl+' ';
	}
	if(s>1) pl='s'; else pl='';
	output+=s+' second'+pl

	return output;
}


</script>

<?
if($config['judge_scheduler_percent']=="-1") {
	echo i18n("The judge scheduler is not currently running");
	echo "<br />";
	echo "<br />";
	echo "<a href=\"judges_schedulerconfig.php\">".i18n("Judges Scheduler Configuration")."</a>";

}
else {
 echo "<table>";
 echo "<tr><td>".i18n("Scheduler status").":</td><td><div id=\"schedulerstatus\" style=\"font-weight: bold;\"></div></td></tr>";
 echo "<tr><td>".i18n("Scheduler percent").":</td><td><div id=\"schedulerpercent\" style=\"font-weight: bold;\"></div></td></tr>";
 echo "<tr><td>".i18n("Scheduler ETA").":</td><td><div id=\"schedulereta\" style=\"font-weight: bold;\"></div></td></tr>";
 echo "<tr><td align=\"center\" colspan=\"2\"><div id=\"updatestatus\" style=\"font-weight: bold; text-align: center;\"></div></td></tr>";
 echo "</table>";

 echo "<br />";
 echo i18n("When scheduling is finished, the following links will be useful");
 echo "<br />";
echo "<a href=\"judges_teams.php\">".i18n("Manage Judge Teams")."</a>";
echo "<br />";
echo "<a href=\"judges_teams_members.php\">".i18n("Manage Judge Members")."</a>";
echo "<br />";
echo "<a href=\"reports.php\">".i18n("Print/Export Reports")."</a>";
echo "<br />";
echo "<br />";
}
 send_footer();

?>
