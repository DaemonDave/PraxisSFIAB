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

 if($_GET['action'] == 'output') {
	include "../data/config.inc.php";
	mysql_connect($DBHOST,$DBUSER,$DBPASS);
	mysql_select_db($DBNAME);
	$q=mysql_query("SELECT val FROM config WHERE year='0' AND var='tours_assigner_percent'");
	$r=mysql_fetch_object($q);
	$percent=$r->val;

	$q=mysql_query("SELECT val FROM config WHERE year='0' AND var='tours_assigner_activity'");
	$r=mysql_fetch_object($q);
	$status=$r->val;

	echo "$percent:$status\n";
	exit;
 }

 require("../common.inc.php");
 send_header("Scheduler Status",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Tours' => 'admin/tours.php')
				);
 require_once("../ajax.inc.php");
?>

<script type="text/javascript">
var starttime=0;
var startpercent=0;
var deltatime=0;
var deltapercent=0;
var avgtimeperpercent=0;
var remainingpercent=0;
var remainingtime=0;

function updateStatus()
{
	document.getElementById('updatestatus').innerHTML="Updating...";
	var url="tours_sa_status.php?action=output";
	http.open("GET",url,true);
	http.onreadystatechange=handleResponse;
	http.send(null);
}

function clearUpdatingMessage()
{
	document.getElementById('updatestatus').innerHTML="Working...";

}

function handleResponse()
{
	try {

		if(http.readyState==4)
		{
			var obj=http.responseText.split(":");
			document.getElementById('schedulerstatus').innerHTML=obj[1];
			if(obj[0]=="-1")
			{
				document.getElementById('schedulerpercent').innerHTML="100%";
				document.getElementById('updatestatus').innerHTML="Scheduling Complete";
				document.getElementById('schedulereta').innerHTML="Complete";
			}
			else
			{
				document.getElementById('schedulerpercent').innerHTML=obj[0]+"%";
				setTimeout('updateStatus()',5000);
				document.getElementById('updatestatus').innerHTML="Updating... Done!";
				setTimeout('clearUpdatingMessage()',500);

				var currentTime=new Date();
				if(starttime==0)
				{
					starttime=currentTime.getTime();
					startpercent=obj[0];
				}
				deltatime=currentTime.getTime()-starttime;
				deltapercent=obj[0]-startpercent;

				avgtimeperpercent=deltatime/deltapercent;
				remainingpercent=100-obj[0];
				remainingtime=remainingpercent*avgtimeperpercent;
				if(remainingtime && obj[0]>0)
					document.getElementById('schedulereta').innerHTML=Math.round(remainingtime/1000)+" seconds";
				else
					document.getElementById('schedulereta').innerHTML="Calculating...";
			}
		}
	}
	catch(e)
	{
		alert('caught error'+e);
	
	}
}

</script>

<?
if($config['tours_assigner_percent']=="-1")
{
	echo i18n("The tour scheduler is not currently running.  You can start it by going to the link below.");
	echo "<br />";
	echo "<br />";
	echo "<a href=\"tours_sa_config.php\">".i18n("Automatic Tour Assignment Configuration")."</a>";

}
else
{
 echo "<table>";
 echo "<tr><td>".i18n("Assignment status").":</td><td><div id=\"schedulerstatus\" style=\"font-weight: bold;\"></div></td></tr>";
 echo "<tr><td>".i18n("Assignment percent").":</td><td><div id=\"schedulerpercent\" style=\"font-weight: bold;\"></div></td></tr>";
 echo "<tr><td>".i18n("Assignment ETA").":</td><td><div id=\"schedulereta\" style=\"font-weight: bold;\"></div></td></tr>";
 echo "<tr><td align=\"center\" colspan=\"2\"><div id=\"updatestatus\" style=\"font-weight: bold; text-align: center;\"></div></td></tr>";
 echo "</table>";

 echo "<br />";
 echo i18n("When the assignments are complete, the following links will be useful:");
 echo "<br /><ul>";
echo "<li><a href=\"tours_assignments.php\">".i18n("Manage Student-Tour Assignments")."</a></li>";
echo "<li><a href=\"reports.php\">".i18n("Print/Export Reports")."</a></li>";
echo "</ul>";
?>
<script type="text/javascript">updateStatus()</script>
<?
}
 send_footer();

?>
