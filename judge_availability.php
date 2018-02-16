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
}

$u = user_load($eid);

$times = array();

/* Load the judging rounds */
$q = mysql_query("SELECT date,starttime,endtime,name FROM judges_timeslots WHERE round_id='0' AND year='{$config['FAIRYEAR']}' ORDER BY starttime,type");
$x = 0;
while($r = mysql_fetch_object($q)) {
	$found = false;
	foreach($times as $xx => $t) {
		if($t['date'] == $r->date && $t['starttime'] == $r->starttime && $t['endtime'] == $r->endtime) {
			$times[$xx]['name'] .= ", {$r->name}";
			$found = true;
			break;
		}
	}
	if(!$found) {
		$times[$x] = array( 'date' => $r->date,
				'starttime' => $r->starttime,
				'endtime' => $r->endtime,
				'name' => $r->name);
		$x++;
	}
}

switch($_GET['action']) {
case 'save':
	mysql_query("DELETE FROM judges_availability WHERE users_id='{$u['id']}'");

	if(is_array($_POST['time']) ) {	
		foreach($_POST['time'] as $x) {
			if(trim($times[$x]['starttime']) == '') continue;

			mysql_query("INSERT INTO judges_availability (users_id, `date`,`start`,`end`) 
					VALUES ('{$u['id']}',
					'{$times[$x]['date']}',
					'{$times[$x]['starttime']}','{$times[$x]['endtime']}')");
		}
	}
	happy_("Time Availability preferences successfully saved");
	exit;
}

if($_SESSION['embed'] == true) {
	display_messages();
	echo "<h4>".i18n('Time Availability')."</h4>";
	echo "<br />";
} else {
	//send the header
	send_header('Time Availability', 
		array('Judge Registration' => 'judge_main.php')
		);
}

?>
<script type="text/javascript">
function judgeavailability_save()
{
	$("#debug").load("<?=$config['SFIABDIRECTORY']?>/judge_availability.php?action=save", $("#judgeavailability_form").serializeArray());
        return false;
}
</script>
<?


judge_status_update($u);

if($_SESSION['embed'] != true) {
	//output the current status
	$newstatus=judge_status_availability($u);
	if($newstatus!='complete')
		echo error(i18n("Time Availability Preferences Incomplete"));
	else
		echo happy(i18n("Time Availability Preferences Complete"));
}

?>
<form id="judgeavailability_form" >
<input type="hidden" name="users_id" value="<?=$u['id']?>" />
<br />
<table>
<?
/* Get all their available times */
$q = mysql_query("SELECT * FROM judges_availability WHERE users_id=\"{$u['id']}\" ORDER BY `start`");

$sel = array();
while($r=mysql_fetch_object($q)) {
	foreach($times as $x=>$t) {
		if($r->start == $t['starttime'] && $r->end == $t['endtime'] && $r->date == $t['date']) {
			$sel[$x] = true;
		}
	}
}

if(count($times) > 1) {
	echo i18n("Please Note, you will be scheduled to judge in ALL (not just one) judging timeslots you select.");
	echo '<br /><br />';
}

foreach($times as $x=>$t) {
	$ch = $sel[$x] == true ? 'checked="checked"' : '';
	echo "<tr><td>";
	echo "<input onclick=\"checkboxclicked(this)\" $ch type=\"checkbox\" name=\"time[]\" value=\"$x\" />";
	$st = substr($t['starttime'], 0, 5);
	$end = substr($t['endtime'], 0, 5);
	echo "</td><td><b>{$times[$x]['date']} $st - $end</b></td></tr>";
	echo "<tr><td></td><td><p>{$t['name']}</td>";
	echo "</tr>";
}
?>
</table>
<br />
<br />

<input type="submit" onclick="judgeavailability_save();return false;" value="<?=i18n("Save Time Availability Preferences")?>" />
</form>

<?
if($_SESSION['embed'] != true) send_footer();
?>
