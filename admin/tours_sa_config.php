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
 require_once("../common.inc.php");
 require_once("../user.inc.php");
 require_once("../config_editor.inc.php");
 user_auth_required('committee', 'admin');

 if($_GET['action']=="launch") {
	exec("nice php tours_sa.php >/dev/null 2>&1 &");
	usleep(1000000); // 1 second to allow the judges_sa to update the % status to 0% otherwise the status page will think its not running if it gets there too soon
	header("Location: tours_sa_status.php");
	exit;
 }

 $action = config_editor_handle_actions("Tour Assigner", $config['FAIRYEAR'], "var");
 if($action == 'update') {
 	header('Location: tours_sa_config.php');
        exit;
 }

 send_header("Automatic Tour Assignment Configuration",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Tours' => 'admin/tours.php')
				);

 config_editor("Tour Assigner", $config['FAIRYEAR'], "var", $_SERVER['PHP_SELF']);

 echo "<hr />";

 function tours_check_tours()
 {
 	global $config;
	$q = mysql_query("SELECT * FROM tours WHERE year='{$config['FAIRYEAR']}'");
	return mysql_num_rows($q);
 }

 function tours_check_students()
 {
 	global $config;
	$q=mysql_query("SELECT students.id
		FROM students
			LEFT JOIN tours_choice ON (tours_choice.students_id=students.id)
			LEFT JOIN registrations ON (registrations.id=students.registrations_id)
		WHERE
			students.year='{$config['FAIRYEAR']}'
			AND tours_choice.year='{$config['FAIRYEAR']}'
			AND registrations.status='complete'
		ORDER BY
			students.id, tours_choice.rank
			");
	return mysql_num_rows($q);
 }

 if($_GET['action']=="reset") {
 	mysql_query("UPDATE config SET `val`='-1' WHERE `var`='tours_assigner_percent' AND `year`=0");
	$config['tours_assigner_percent']=="-1";
	echo happy(i18n("Judge assigner status forcibly reset"));
 }

if($config['tours_assigner_percent']=="-1") {
        $ok = 1;

	$tours = tours_check_tours();
	if($tours > 0) {
		echo happy(i18n("There are %1 tours defined, good", array($tours)));
	} else {
		echo error(i18n("There are no tours defined."));
		$ok = 0;
	}

	$x = tours_check_students();
	if($x > 0) {
		echo happy(i18n("There are %1 student-tour rankings, good", array($x)));
	} else {
		echo error(i18n("There are no student-tour rankings."));
		$ok = 0;
	}

	if($ok) {
		echo i18n("Everything looks in order, we're ready to automatically
		assign the students to the tours.  Click link below to start the process.  
		Please be patient as it may take several minutes find a good solution.");

		echo "<br />";
		echo "<br />";

		echo "<a href=\"tours_sa_config.php?action=launch\">".i18n("Automatically Assign Students to Tours")."</a>";
	}

 } else	{
	echo "<br />";
	echo "<b>";
	echo i18n("Automatic assignemnts are currently in progress");
	echo "</b>";
	echo "<br />";
	echo "<br />";
	echo "<a href=\"tours_sa_status.php\">".i18n("Click here to check the tour assignment progress")."</a>";
	echo "<br />";
	echo "<br />";
	echo "<br />";
	echo i18n("If it is not running (and you are 100% sure that it is not!) click the link below to reset the status");
	echo "<br />";
	echo "<a href=\"tours_sa_config.php?action=reset\">".i18n("Reset automatic tour assignment status")."</a>";;
 }

echo "<br />";
echo "<br />";
echo "<br />";

 send_footer();


?>
