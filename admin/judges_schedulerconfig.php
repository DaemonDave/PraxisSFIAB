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
 require_once("../user.inc.php");
 require("../config_editor.inc.php");
 user_auth_required('committee', 'admin');
 require("judges.inc.php");
 require("judges_schedulerconfig_check.inc.php");

 $action = config_editor_handle_actions("Judge Scheduler", $config['FAIRYEAR'], "var");
 if($action == 'update') {
 	header("Location: judges_schedulerconfig.php");
	exit;
 }
 	

 send_header("Judge Scheduler Configuration",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judges' => 'admin/judges.php')
				);

 config_editor("Judge Scheduler", $config['FAIRYEAR'], "var", $_SERVER['PHP_SELF']);
 echo "<hr />";

 if($_GET['action']=="reset")
 {
 	mysql_query("UPDATE config SET `val`='-1' WHERE `var`='judge_scheduler_percent' AND `year`=0");
	$config['judge_scheduler_percent']="-1";
	echo happy(i18n("Judge scheduler status forcibly reset"));
 }

if($config['judge_scheduler_percent']=="-1")
{

 $ok = 1;

	echo "<table class='headertable'><tr><td><h3>Timeslots</h3></td>";
	echo "<td> - <a href=\"judges_timeslots.php\">".i18n("Timeslot Manager")."</a>";
	echo "</td></tr></table>";

	$timeslots = judges_scheduler_check_timeslots();
	if($timeslots > 0) {
		echo happy(i18n("There are %1 timeslot(s) defined for divisional judging, good", array($timeslots)));
	} else {
		echo error(i18n("There are no timeslots defined for divisional judging"));
		$ok = 0;
	}

	if($config['scheduler_enable_sa_scheduling'] == 'yes') {
		$timeslots = judges_scheduler_check_timeslots_sa();
		if($timeslots > 0) {
			echo happy(i18n("There are %1 timeslot(s) defined for special awards judging, good", array($timeslots)));
		} else {
			echo error(i18n("There are no timeslots defined for special awards judging (but the scheduler is configured to do special awards judging)"));
			$ok = 0;
		}
	}

	echo "<table class='headertable'><tr><td><h3>Awards</h3></td>";
	echo "<td> - <a href=\"awards.php\">".i18n("Awards Manager")."</a>";
	echo "</td></tr></table>";

	$missing_awards = judges_scheduler_check_awards();
	if(count($missing_awards) == 0) {
		echo happy(i18n("There is a single divisional award for each division/category, good"));
	} else {
		echo "<br />The following divisional awards problems were identified:<br /><ul>";
		for($x=0; $x<count($missing_awards); $x++) {
			print($missing_awards[$x]."<br />");
		}
		echo "</ul>";
		echo error(i18n("There needs to be exactly one award for each division/category"));
		$ok = 0;
	}

	echo "<table class='headertable'><tr><td><h3>Divisional Judging Groupings</h3></td>";
	echo "<td> - <a href=\"judges_jdiv.php\">".i18n("Divisional Judging Groupings Manager")."</a>";
	echo "</td></tr></table>";

	$jdivs = judges_scheduler_check_jdivs();
	if($jdivs > 1) {
		echo happy(i18n("There are %1 divisional groups defined for divisional judging, good", array($jdivs)));
	} else {
		echo error(i18n("There is not more than 1 divisional groups defined for divisional judging.  Please assign ALL categories/divisions/languages to judging groupings before continuing"));
		$ok = 0;
	}

	echo "<h3>Projects and Judges</h3><br />";
	
	$k=judges_scheduler_check_judges();

	if(!$k) $ok=0;

if($ok)
{
	echo i18n("Everything looks in order, we're ready to create the
	divisional awards judging teams.  Click link below to start the scheduler.  
	Please be patient as it may take several minutes find an good solution to 
	the judging team assignments.");

	echo "<br />";
	echo "<br />";

	echo "<a href=\"judges_sa_launcher.php\">".i18n("Start the judging scheduler to create judging teams and judging schedule")."</a>";

}
else {
	echo "<br />";
	echo "<br />";

	echo "<a href=\"judges_sa_launcher.php\">".i18n("Something above looks bad, but you can start the judging scheduler anyways with the understanding that results will NOT be optimal, or in fact, the scheduler may not work at all!")."</a>";
}

}
else
{
	echo "<br />";
	echo "<b>";
	echo i18n("The scheduler is currently running");
	echo "</b>";
	echo "<br />";
	echo "<br />";
	echo "<a href=\"judges_scheduler_status.php\">".i18n("Click here to check the judging scheduler progress")."</a>";
	echo "<br />";
	echo "<br />";
	echo "<br />";
	echo i18n("If the scheduler is not running (and you are 100% sure that it is not!) click the link below to reset the scheduler status");
	echo "<br />";
	echo "<a href=\"judges_schedulerconfig.php?action=reset\">".i18n("Reset judge scheduler status")."</a>";;


}

echo "<br />";
echo "<br />";
echo "<br />";

 send_footer();


?>
