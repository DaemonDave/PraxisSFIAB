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

//
/// MODIFIED DRE 2018
//
// added applicable timeslot types
 $round_str = array('timeslot' => 'Judging Timeslot',
					'junior' => 'Junior Projects',
					'intermediate' => 'Intermediate Projects',
					'senior' => 'Senior Projects',
 					'divisional1' => 'Divisional Round 1',
 					'divisional2' => 'Divisional Round 2',
					'grand' => 'Grand Awards',
					'special' => 'Special Awards' );
					
 if(array_key_exists('action',$_POST)) $action = $_POST['action'];
 else if(array_key_exists('action',$_GET)) $action = $_GET['action'];
 else $action = '';

 if(array_key_exists('round_id',$_POST)) $round_id = intval($_POST['round_id']);
 else if(array_key_exists('round_id',$_GET)) $round_id = intval($_GET['round_id']);
 else $round_id = 0;

 if(array_key_exists('timeslot_id',$_POST)) $timeslot_id = intval($_POST['timeslot_id']);
 else if(array_key_exists('timeslot_id',$_GET)) $timeslot_id = intval($_GET['timeslot_id']);
 else $timeslot_id = 0;

 if($action == 'saveround') {
	$save = true;
	/* Sanity check all the values */
	$y = intval($_POST['date_year']);
	$m = intval($_POST['date_month']);
	$d = intval($_POST['date_day']);
	if($y && $m && $d) $date = "$y-$m-$d";
	else {
		$save = false;
		message_push(error(i18n("Date is required")));
	}

	if(array_key_exists('starttime_hour', $_POST) && array_key_exists('starttime_minute', $_POST)) {
		$starttime = sprintf("%02d:%02d:00", intval($_POST['starttime_hour']), intval($_POST['starttime_minute']));
	} else {
		$save = false;
		message_push( error(i18n("Start Time is required")));
	}

	if(array_key_exists('endtime_hour', $_POST) && array_key_exists('endtime_minute', $_POST)) {
		$endtime = sprintf("%02d:%02d:00", intval($_POST['endtime_hour']), intval($_POST['endtime_minute']));
	} else {
		$save = false;
		message_push( error(i18n("End Time is required")));
	}

	$type = $_POST['type'];
	if(!array_key_exists($type, $round_str)) {
		$save = false;
		message_push(error(i18n('Invalid type specified')));
	}

	$name = mysql_escape_string(stripslashes($_POST['name']));

	if($save == true) {
		if($round_id == 0) {
			/* New entry */
			mysql_query("INSERT INTO judges_timeslots (round_id,year) VALUES('0','{$config['FAIRYEAR']}')");
			$round_id = mysql_insert_id();
		}

		mysql_query("UPDATE judges_timeslots SET `date`='$date', 
								starttime='$starttime', endtime='$endtime',
								`name`='$name',
								`type`='$type' WHERE id='$round_id'");

		echo mysql_error();
		message_push(happy(i18n("Round successfully saved")));
		$action = '';
	}
	
 }

 if($action == 'deleteround') {
 	mysql_query("DELETE FROM judges_timeslots WHERE id='$round_id'");
	/* Also delete all timeslots */
 	mysql_query("DELETE FROM judges_timeslots WHERE round_id='$round_id'");
	message_push(happy(i18n("Round successfully removed")));
	$action = '';
 }
 if($action == 'deletetimeslot') {
 	mysql_query("DELETE FROM judges_timeslots WHERE id='$timeslot_id'");
	message_push(happy(i18n("Timeslot successfully removed")));
	$action = '';
 } 

 if($action == 'savetimeslot') {
	$save = true;

	$q = mysql_query("SELECT * FROM judges_timeslots WHERE id='$round_id'");
	$round_data = mysql_fetch_assoc($q);

	$date = $round_data['date'];

	if(array_key_exists('starttime_hour', $_POST) && array_key_exists('starttime_minute', $_POST)) {
		$starttime = sprintf("%02d:%02d:00", intval($_POST['starttime_hour']), intval($_POST['starttime_minute']));
	} else {
		$save = false;
		message_push( error(i18n("Start Time is required")));
	}

	if(array_key_exists('endtime_hour', $_POST) && array_key_exists('endtime_minute', $_POST)) {
		$endtime = sprintf("%02d:%02d:00", intval($_POST['endtime_hour']), intval($_POST['endtime_minute']));
	} else {
		$save = false;
		message_push( error(i18n("End Time is required")));
	}

	if($save == true) {
		if($timeslot_id == 0) {
			/* New entry */
			mysql_query("INSERT INTO judges_timeslots (round_id,date,type,year) VALUES('$round_id',
								'$date','timeslot','{$config['FAIRYEAR']}')");
			$timeslot_id = mysql_insert_id();
		}

		mysql_query("UPDATE judges_timeslots SET starttime='$starttime', endtime='$endtime'
								WHERE id='$timeslot_id'");

		echo mysql_error();
		message_push(happy(i18n("Timeslot successfully saved")));
		$action = '';
	}
 }
	
 if($action=='savemultiple') {
 	$save = true;
	
	$addnum = intval($_POST['addnum']);
	$duration = intval($_POST['duration'] );
	$break = intval($_POST['break']);

	if(array_key_exists('starttime_hour', $_POST) && array_key_exists('starttime_minute',$_POST) && $addnum && $duration) {
	    	
		$q = mysql_query("SELECT * FROM judges_timeslots WHERE id='$round_id'");
		$round_data = mysql_fetch_assoc($q);

		$date = $round_data['date'];

		$hr=intval($_POST['starttime_hour']);
		$min=intval($_POST['starttime_minute']);

		$tt=$duration+$break;

		for($x=0;$x<$addnum;$x++) {
			$q=mysql_query("SELECT 	DATE_ADD('$date $hr:$min:00', INTERVAL $duration MINUTE) AS endtime, 
						DATE_ADD('$date $hr:$min:00', INTERVAL $tt MINUTE) AS startnext ");
			echo mysql_error();
			$r=mysql_fetch_object($q);
			list($ed,$et)=split(" ",$r->endtime);
			list($nd,$nt)=split(" ",$r->startnext);

			$starttime = sprintf("%02d:%02d:00", $hr, $min);

			mysql_query("INSERT INTO judges_timeslots (date,type,round_id,starttime,endtime,year) VALUES (
					'$date','timeslot','{$round_data['id']}',
					'$starttime', '$et',
					'{$config['FAIRYEAR']}')");
			echo mysql_error();
			$date=$nd;
			list($s_h,$s_m,$s_s)=split(":",$nt);
			list($e_h,$e_m,$e_s)=split(":",$et);
			message_push(happy(i18n("Adding timeslot: %1",array("$date $hr:$min - $e_h:$e_m"))));
			$hr=$s_h;
			$min=$s_m;
		}
		$action = '';
	} else {
		message_push(error(i18n("All fields are required to add multiple timeslots")));
	}
  }



 if($action == '') {
	 send_header("Judging Rounds and Timeslots",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judges' => 'admin/judges.php'));
 } else {
	 send_header("Judging Rounds and Timeslots",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judges' => 'admin/judges.php',
			'Judging Rounds and Timeslots' => 'admin/judges_timeslots.php'));
 }
 echo "<br />";


 if($action == 'addround' || $action == 'editround') {
	echo "<form method=\"post\" action=\"judges_timeslots.php\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"saveround\">\n";
	echo "<input type=\"hidden\" name=\"round_id\" value=\"$round_id\">\n";

	if($action == 'addround') {
		echo "<h3>Add New Judging Round</h3>";
		$r = array();
		$r['date'] = $config['dates']['fairdate'];
	} else {
		echo "<h3>Edit Judging Round</h3>";
		$q=mysql_query("SELECT * FROM judges_timeslots WHERE id='$round_id'");
		if(mysql_num_rows($q) != 1) {
		    	echo "UNKNOWN ROUND $round_id";
			exit;
		}
		$r = mysql_fetch_assoc($q);
	}

	echo "<table>";
	echo "<tr><td>".i18n('Round Type').":</td><td>";
	echo "<select name=\"type\">";
	foreach($round_str as $k=>$v) {
	    	if($k == 'timeslot') continue;  /* Don't let them add a timeslot directly */
	    	$s = ($r['type'] == $k) ? 'selected="selected"' : '';
		echo "<option value=\"$k\" $s>$v</option>";
	}
	echo "</select>";

	echo "<tr><td>".i18n("Name").":</td><td>";
	echo "<input type=\"textbox\" name=\"name\" value=\"{$r['name']}\" width=\"60\" /></td></tr>";

	echo "<tr><td>".i18n("Date").":</td><td>";
	emit_date_selector("date",$r['date']);

	echo "</td></tr>";
	echo "<tr><td>".i18n("Start Time").":</td><td>";
	emit_time_selector("starttime",$r['starttime']);

	echo "</td></tr>";
	echo "<tr><td>".i18n("End Time").":</td><td>";
	emit_time_selector("endtime",$r['endtime']);

	echo "</td></tr>";
	echo "</table>";

	echo "<input type=\"submit\" value=\"".i18n('Save')."\" />";
	echo "</form>";
 }

 if($action == 'addtimeslot' || $action == 'edittimeslot') {
	echo "<form method=\"post\" action=\"judges_timeslots.php\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"savetimeslot\">\n";
	echo "<input type=\"hidden\" name=\"round_id\" value=\"$round_id\">\n";
	echo "<input type=\"hidden\" name=\"timeslot_id\" value=\"$timeslot_id\">\n";

	$q = mysql_query("SELECT * FROM judges_timeslots WHERE id='$round_id'");
	$round_data = mysql_fetch_assoc($q);

	if($action == 'addtimeslot') {
		echo "<h3>Add New Judging Timeslot</h3>";
		$r = array();
		$r['date'] = $round_data['date'];
	} else {
		echo "<h3>Edit Judging Timeslot</h3>";
		$q=mysql_query("SELECT * FROM judges_timeslots WHERE id='$timeslot_id'");
		if(mysql_num_rows($q) != 1) {
		    	echo "UNKNOWN ROUND $round_id";
			exit;
		}
		$r = mysql_fetch_assoc($q);
	}

	echo "<table>";
	echo "<tr><td>".i18n('Round Type').":</td><td>{$round_str[$round_data['type']]}</td></tr>";
	echo "<tr><td>".i18n("Name").":</td><td>{$round_data['name']}</td></tr>";

	echo "<tr><td>".i18n("Start Time").":</td><td>";
	emit_time_selector("starttime",$r['starttime']);

	echo "</td></tr>";
	echo "<tr><td>".i18n("End Time").":</td><td>";
	emit_time_selector("endtime",$r['endtime']);

	echo "</td></tr>";
	echo "</table>";

	echo "<input type=\"submit\" value=\"".i18n('Save')."\" />";
	echo "</form>";
 }

 if($action == 'addmultiple') {

	echo "<h3>Add Multiple New Judging Timeslots</h3>";

	echo "<form method=\"post\" action=\"judges_timeslots.php\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"savemultiple\">\n";
	echo "<input type=\"hidden\" name=\"round_id\" value=\"$round_id\">\n";
	echo "<input type=\"hidden\" name=\"timeslot_id\" value=\"$timeslot_id\">\n";

	$q = mysql_query("SELECT * FROM judges_timeslots WHERE id='$round_id'");
	$round_data = mysql_fetch_assoc($q);

	echo "<table border=\"0\">";
	echo "<tr><td>".i18n('Round Type').":</td><td>{$round_str[$round_data['type']]}</td></tr>";
	echo "<tr><td>".i18n("Name").":</td><td>{$round_data['name']}</td></tr>";
	echo "<tr><td>".i18n("Add")."</td><td>";
	echo "<input type=\"text\" name=\"addnum\" size=\"4\">&nbsp;";
	echo i18n("new timeslots");
	echo "</td></tr>";
	echo "<tr><td>".i18n("Starting timeslots at")."</td><td>";
	emit_time_selector("starttime");
	echo "</td></tr>";
	echo "<tr><td>".i18n("With a duration of")."</td><td>";
	echo "<input type=\"text\" name=\"duration\" size=\"4\">&nbsp;";
	echo i18n("minutes")."</td></tr>";
	echo "<tr><td>".i18n("And a break of")."</td><td>";
	echo "<input type=\"text\" name=\"break\" size=\"4\">&nbsp;";
	echo i18n("minutes")."</td></tr>";

	echo "<tr><td colspan=\"2\">";
	echo "<input type=\"submit\" value=\"".i18n("Add these timeslots")."\">";
	echo "</td></tr>";
	echo "</table>";

	echo "</form>";
 }

 if($action == '') {
	echo "<A href=\"judges_timeslots.php?action=addround&round_id=0\">".i18n("Add new round")."</a> <br />";
	echo "<br />";
	echo "<table class=\"summarytable\">";
	echo "<tr>";
	echo "<th>".i18n("Date")."</th>";
	echo "<th>".i18n("Start Time")."</th>";
	echo "<th>".i18n("End Time")."</th>";
	echo "<th>".i18n("Judging Round")."</th>";
	echo "<th>".i18n("Actions")."</th>";
	echo "</tr>";

	$q=mysql_query("SELECT * FROM judges_timeslots WHERE year='{$config['FAIRYEAR']}' AND `type`!='timeslot' ORDER BY date,starttime");
	while($r=mysql_fetch_object($q)) {
		echo "<tr>";
		$qq = mysql_query("SELECT * FROM judges_timeslots WHERE round_id='{$r->id}' ORDER BY `date`,`starttime`");
		$c = mysql_num_rows($qq) +1;

		echo "<td rowspan=\"$c\"><b>".format_date($r->date)."</b></td>";
		echo "<td align=\"center\"><b>".format_time($r->starttime)."</b><br/>";
		
		echo "</td>";
		echo "<td align=\"center\"><b>".format_time($r->endtime)."</b></td>";
		echo "<td align=\"center\"><b>{$r->name}  (".i18n($round_str[$r->type]).")</b></td>";
		echo " <td align=\"center\">";
		echo "<a href=\"judges_timeslots.php?action=editround&round_id={$r->id}\"><img border=\"0\" src=\"{$config['SFIABDIRECTORY']}/images/16/edit.{$config['icon_extension']}\"></a>";
		echo "&nbsp;";
		echo "<a onclick=\"return confirmClick('Are you sure you want to remove this round?')\" href=\"judges_timeslots.php?action=deleteround&round_id={$r->id}\"><img border=\"0\" src=\"{$config['SFIABDIRECTORY']}/images/16/button_cancel.{$config['icon_extension']}\"></a>";

		echo "<A href=\"judges_timeslots.php?action=addtimeslot&round_id={$r->id}\">(new)</a>  ";
		echo "<A href=\"judges_timeslots.php?action=addmultiple&round_id={$r->id}\">(multiple)</a><br />";
		echo " </td>\n";

		echo "</tr>";

		while($rr = mysql_fetch_object($qq)) {
			echo "<tr>";
//			echo "<td></td>";
			echo "<td align=\"right\">".format_time($rr->starttime)."</td>";
			echo "<td align=\"right\">".format_time($rr->endtime)."</td>";
			echo "<td align=\"center\">".i18n($round_str[$rr->type])."</td>";


			echo " <td align=\"center\">";
			echo "<a href=\"judges_timeslots.php?action=edittimeslot&round_id={$r->id}&timeslot_id={$rr->id}\"><img border=\"0\" src=\"{$config['SFIABDIRECTORY']}/images/16/edit.{$config['icon_extension']}\"></a>";
			echo "&nbsp;";
			echo "<a onclick=\"return confirmClick('Are you sure you want to remove this timeslot?')\" href=\"judges_timeslots.php?action=deletetimeslot&timeslot_id={$rr->id}\"><img border=\"0\" src=\"{$config['SFIABDIRECTORY']}/images/16/button_cancel.{$config['icon_extension']}\"></a>";

			echo " </td>\n";
			echo "</tr>";
		}
	}
	echo "</table>";
 }

 send_footer();
?>
