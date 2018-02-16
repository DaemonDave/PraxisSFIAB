<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

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
 require_once('../common.inc.php');
 require_once('../user.inc.php');
 require_once('../fair.inc.php');

 $fair_type = array('feeder' => 'Feeder Fair', 'sfiab' => 'SFIAB Upstream', 'ysc' => 'YSC/CWSF Upstream');
 $stats = array('participation' => 'Particpation Numbers',
 		'schools_ext' => 'Extra school participation data, number of public/private school students',
		'minorities' => 'Data on minority group participation',
		'guests' => 'Number of student, public guests',
		'sffbc_misc' => 'Number of teachers supporting science, number of students interested in careers in science',
		'info' => 'Fair address, dates, budget, charity info',
		'next_chair' => 'The chair of the regional fair next year',
		'scholarships' => 'Information about scholarships available to be won',
		'delegates' => 'Delegate information/jacket size for CWSF',
		);

 user_auth_required('committee', 'admin');


 switch($_GET['action']) {
 case 'save':
 	print_r($_POST);
 	$id = intval($_POST['fairs_id']);
	if(!is_array($_POST['stats'])) $_POST['stats'] = array();
	foreach($_POST['stats'] as $k=>$s) {
		if(!array_key_exists($s, $stats)) {
			echo "Undefined stat $s, abort.\n";
			exit;
		}
	}
	$s = join(',', $_POST['stats']);
	$q = mysql_query("UPDATE fairs SET gather_stats='$s' WHERE id='$id'");
	echo mysql_error();
	echo "UPDATE fairs SET gather_stats='$s' WHERE id='$id'";
	happy_("Saved");
	exit;

 }
 /* Load the user we're editting */
 $u = user_load($_SESSION['embed_edit_id']);
 /* Load the fair attached to the user */
 $q = mysql_query("SELECT * FROM fairs WHERE id={$u['fairs_id']}");
 $f = mysql_fetch_assoc($q);

?>

 <h4><?=i18n('Fair Stats Gathering')?></h3>

<script type="text/javascript">

function stats_save()
{
	$("#debug").load("fair_stats_select.php?action=save", $("#gather_stats").serializeArray());
	return 0;
}

 </script>
 
<?
 if($f['type'] == 'feeder') {
	 echo '<p>'.i18n('Select which statistics to request from the feeder fair').'</p>';
 } else {
	 echo '<p>'.i18n('Not supported for upstream fairs').'</p>';
	 exit;
 }

?>
 <form id="gather_stats">
 <input type="hidden" name="fairs_id" value="<?=$f['id']?>" />
 <table class="editor">
<?
 $selected_stats =  split(',', $f['gather_stats']);
 foreach($stats as $s=>$d) {
 	$ch = in_array($s, $selected_stats) ? 'checked="checked"' : '';
	echo "<tr><td class=\"left\"><input type=\"checkbox\" id=\"stats_$s\" name=\"stats[]\" value=\"$s\" $ch /></td>";
	echo "<td class=\"right\">".i18n($d)."</td></tr>";
 }
?>

 </table>
 <br />
 <input type="submit" onClick="stats_save();return false;" value="Save" />
 </form>
 


