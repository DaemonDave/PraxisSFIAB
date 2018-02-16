<?

/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2007 James Grant <james@lightbox.org>
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
 require_once('admin/stats.inc.php');


/* Sort out who we're editting */
if($_POST['users_id']) 
	$eid = intval($_POST['users_id']); /* From a save form */
else if(array_key_exists('embed_edit_id', $_SESSION))
	$eid = $_SESSION['embed_edit_id']; /* From the embedded editor */
else 
	$eid = $_SESSION['users_id'];   /* Regular entry */

if($eid != $_SESSION['users_id']) {
	/* Not editing ourself, we had better be
	* a committee member */
	user_auth_required('committee','admin');
}
$u = user_load($eid);

switch($_GET['action']) {
case 'save':
	$stats = $_POST['stats'];
	$year = intval($_POST['year']);

	foreach($stats as $k=>$v) {
		$stats[$k] = mysql_escape_string($stats[$k]);
	}

	//  $str = join(',',$stats);
	$keys = '`fairs_id`,`year`,`'.join('`,`', array_keys($stats)).'`';
	$vals = "'{$u['fairs_id']}','$year','".join("','", array_values($stats))."'";
	mysql_query("DELETE FROM fairs_stats WHERE fairs_id='{$u['fairs_id']}' AND year='$year'");
	echo mysql_error();
	mysql_query("INSERT INTO fairs_stats (`id`,$keys) VALUES ('',$vals)");
	echo mysql_error();

	happy_("Fair Information Saved.");
	exit;
}


if($_SESSION['embed'] == true) {
	echo "<br/>";
	display_messages();
	echo "<h3>".i18n("Fair Information and Statistics")."</h3>";
	echo "<br/>";
} else {
	send_header("Fair Information and Statistics",
 		array('Fair Main' => 'fair_main.php'),
            "fair_stats"
			);
}

?>
<script type="text/javascript">
function stats_save()
{
	    $("#debug").load("<?=$config['SFIABDIRECTORY']?>/fair_stats.php?action=save", $("#stats_form").serializeArray());
		        return false;
}
</script>

<?

/* This was the remote upload code, seems silly to change the config names.  server_config really isn't 
* from the server here, it's just our local name */

/* SFIAB config options server side */
$server_config = array();
$server_config['participation'] = false;
$server_config['schools_ext'] = false;
$server_config['minorities'] = false;
$server_config['guests'] = false;
$server_config['sffbc_misc'] = false;
$server_config['info'] = false;
$server_config['next_chair'] = false;
$server_config['scholarships'] = false;
$server_config['delegates'] = false;
$server_any_stats = false;

$year = intval($_POST['year']);
if($year < 1900) $year = $config['FAIRYEAR'];

 /* Get the stats we want from this fair */
$q = mysql_query("SELECT * FROM fairs WHERE id='{$u['fairs_id']}'");
echo mysql_error();
$fair = mysql_fetch_assoc($q);

$s = split(',', $fair['gather_stats']);
foreach($s as $k) {
	if(trim($k) == '') continue;
	$server_config[$k] = true;
	$server_any_stats = true;
}


/*
$s = ($_SESSION['embed'] == true) ? $_SESSION['embed_submit_url'] : 'fair_stats.php';
echo "<form id=\"year_form\" name=\"year_form\" method=\"post\" action=\"$s\">";
echo i18n('Select Year').": ";
$q = mysql_query("SELECT DISTINCT year FROM config WHERE year>1000 ORDER BY year DESC");
echo "<select name=\"year\" id=\"year\" onchange=\"this.form.submit()\">";
while($i = mysql_fetch_assoc($q)) {
	$y = $i['year'];
	$sel = ($config['FAIRYEAR'] == $y) ? 'selected=\"selected\"' : '';
	echo "<option value=\"$y\" $sel>$y</option>";
}
echo "</select>";
echo "</form>";
*/
echo "<br />";

/* Load stats */
$q = mysql_query("SELECT * FROM fairs_stats WHERE fairs_id='{$u['fairs_id']}'
	                   AND year='$year'");
$stats = mysql_fetch_assoc($q);

/* Print stats */


 /* Print all blocks the server requests */

 echo "<form id=\"stats_form\" name=\"stats_form\">";
 echo "<input type=\"hidden\" name=\"year\" value=\"$year\" />";

 if($server_config['info']) {
 	echo '<h3>'.i18n('%1 Fair information', array($year)).'</h3>';
	echo '<table>';
	echo '<tr><td>'.i18n('Fair Start Date').':</td>';
	echo "<td><input type=\"text\" size=\"12\" name=\"stats[start_date]\" value=\"{$stats['start_date']}\">(YYYY-MM-DD)</td></tr>";
	echo '<tr><td>'.i18n('Fair End Date').':</td>';
	echo "<td><input type=\"text\" size=\"12\" name=\"stats[end_date]\" value=\"{$stats['end_date']}\">(YYYY-MM-DD)</td></tr>";
	echo '<tr><td>'.i18n('Fair Location/Address').':</td>';
	echo '<td><textarea name="address" rows="4" cols="60">'.htmlspecialchars($stats['address']).'</textarea></td>';
	echo '<tr><td>'.i18n('Fair Budget').':</td>';
	echo "<td>$<input type=text name=\"stats[budget]\" value=\"{$stats['budget']}\"></td></tr>";
	echo '<tr><td>'.i18n('Youth Science Canada Affiliation Complete').'?</td>';
	echo '<td><select name="ysf_affiliation_complete">';
	$sel = $stats['ysf_affiliation_complete'] == 'N' ? 'selected="selected"' : '';
	echo " <option value=\"N\" $sel >No</option>";
	$sel = $stats['ysf_affiliation_complete'] == 'Y' ? 'selected="selected"' : '';
	echo " <option value=\"Y\" $sel >Yes</option>";
	echo '</select></td></tr>';
	echo '<tr><td>'.i18n('Charity Number or Information').'?</td>';
	echo "<td><input type=text size=\"40\" name=\"stats[charity]\" value=\"{$stats['charity']}\"></td></tr>";
	echo '</table>';
	echo '<br />';
	echo '<br />';
 }

	
 if($server_config['next_chair']) {
 	echo '<h3>'.i18n('%1 - %2 Chairperson (if known)', array($year, $year+1)).'</h3>';
	echo '<table>';
	echo '<tr><td>'.i18n('Name').': </td>';
	echo "<td><input type=text name=\"stats[next_chair_name]\" value=\"{$stats['next_chair_name']}\"></td>";
	echo '<td>'.i18n('Email').': </td>';
	echo "<td><input type=text name=\"stats[next_chair_email]\" value=\"{$stats['next_chair_email']}\"></td></tr>";
	echo '<tr><td>'.i18n('Tel. Bus').': </td>';
	echo "<td><input type=text name=\"stats[next_chair_bphone]\" value=\"{$stats['next_chair_bphone']}\"></td>";
	echo '<td>'.i18n('Tel. Home').': </td>';
	echo "<td><input type=text name=\"stats[next_chair_hphone]\" value=\"{$stats['next_chair_hphone']}\"></td></tr>";
	echo '<tr><td>'.i18n('Fax').': </td>';
	echo "<td><input type=text name=\"stats[next_chair_fax]\" value=\"{$stats['next_chair_fax']}\"></td>";
	echo '</tr>';

	echo '</table>';
	echo '<br /><br />';
 }

 if($server_config['delegates']) {
 	echo '<h3>'.i18n('%1 CWSF Delegates and Alternatives', array($year)).'</h3>';
	echo '<table>';
	echo '<tr><td>'.i18n('Delegate Name(s)').'</td><td>'.i18n('Email').'</td><td>'.i18n('Jacket Size').'<td></tr>';
	for($x=1;$x<=3;$x++) {
		$sizes = array('small'=>'Small', 'medium'=>'Medium', 'large'=>'Large', 'xlarge'=>'X-Large');
		echo "<td><input type=text size=\"25\" name=\"delegate$x\" value=\"{$stats["delegate$x"]}\"></td>";
		echo "<td><input type=text size=\"25\" name=\"delegate{$x}_email\" value=\"{$stats["delegate{$x}_email"]}\"></td>";
		echo "<td><select name=\"delegate{$x}_size\">";
		$sz = $stats["delegate{$x}_size"];
		foreach($sizes as $s=>$t) {
			$sel = ($sz == $s) ? 'selected="selected"' : '';
			echo "   <option value=\"$s\" $sel >".i18n($t).'</option>';
		}
		echo '</select></td></tr>';
	}
	echo '</table>';
	echo i18n('Remember, the jackets fit smaller than normal sizes.');
	echo '<br /><br />';
 }

 if($server_config['scholarships']) {
 	echo '<h3>'.i18n('%1 Scholarships', array($year)).'</h3>';
	echo 'How many university/college scholarships are available at your fair?  (use a format like: <br /><b>6 - University of British Columbia - Entrance Scholarships</b><br />';
	echo '<textarea name="stats[scholarships]" rows="4\" cols="80">'.htmlspecialchars($stats['scholarships']).'</textarea>';
	echo '<br /><br />';
 }

 if($server_config['participation']) {
 	$rangemap = array(1=>'1-3', 4=>'4-6', 7=>'7-8', 9=>'9-10', 11=>'11-12');
 	echo '<h3>'.i18n('%1 Fair participation', array($year)).'</h3>';
	echo '<br />';
	echo i18n("Number of students").": ";
	echo "<input type=text name=\"stats[students_total]\" size=\"5\" value=\"{$stats['students_total']}\">";
	echo '<table><tr><td></td><td></td><td></td><td align=\"center\">'.i18n('Grade').'</td><td></td><td></td></tr>';
	echo '<tr><td></td>';
	foreach($rangemap as $k=>$v) echo "<td align=\"center\" width=\"50px\" >$v</td>";
	echo '</tr><tr>';
	echo '<td>'.i18n('Male').'</td>';
	foreach($rangemap as $k=>$v) echo "<td align=\"right\"><input type=text size=\"4\" name=\"stats[male_$k]\" value=\"{$stats["male_$k"]}\"></td>";
	echo '</tr><tr>';
	echo '<td>'.i18n('Female').'</td>';
	foreach($rangemap as $k=>$v) echo "<td align=\"right\"><input type=text size=\"4\" name=\"stats[female_$k]\" value=\"{$stats["female_$k"]}\"></td>";
	echo '</tr><tr>';
	echo '<td>'.i18n('Projects').'</td>';
	foreach($rangemap as $k=>$v) echo "<td align=\"right\"><input type=text size=\"4\" name=\"stats[projects_$k]\" value=\"{$stats["projects_$k"]}\"></td>";
	echo '</tr>';
	echo '</table>';
	echo '<br />';
	echo i18n("Number of schools").": <input type=text size=\"5\" name=\"stats[schools_total]\" value=\"{$stats['schools_total']}\">";
	echo '<br />';
	echo i18n("Number of active schools").": <input type=text size=\"5\" name=\"stats[schools_active]\" value=\"{$stats['schools_active']}\">";
	echo '<br />';
	echo '<br />';
	echo i18n("Number of committee members").": <input type=text size=\"5\" name=\"stats[committee_members]\" value=\"{$stats['committee_members']}\">";
	echo '<br />';
	echo i18n("Number of judges").": <input type=text size=\"5\" name=\"stats[judges]\" value=\"{$stats['judges']}\">";
	echo '<br />';
	echo '<br />';
	echo '<br />';
 }

 if($server_config['schools_ext']) {
 	echo '<h3>'.i18n('%1 Extended School/Participant data', array($year)).'</h3>';
	echo '<br />';
	?>
	<table><tr>
		<td><?=i18n('Public schools')?>:</td> 
		<td><input type=text size="5" name="stats[schools_public]" value="<?=$stats['schools_public']?>"></td>
		<td><?=i18n('Public school Students')?>:</td> 
		<td><input type=text size="5" name="stats[students_public]" value="<?=$stats['students_public']?>"></td>
	</tr><tr>
		<td><?=i18n('Private/Independent schools')?>:</td> 
		<td><input type=text size="5" name="stats[schools_private]" value="<?=$stats['schools_private']?>"></td>
		<td><?=i18n('Private/Independent school Students')?>:</td> 
		<td><input type=text size="5" name="stats[students_private]" value="<?=$stats['students_private']?>"></td>
	</tr><tr>
		<td><?=i18n('At-risk/inner city schools')?>:</td> 
		<td><input type=text size="5" name="stats[schools_atrisk]" value="<?=$stats['schools_atrisk']?>"></td>
		<td><?=i18n('At-risk/inner city school Students')?>:</td> 
		<td><input type=text size="5" name="stats[students_atrisk]" value="<?=$stats['students_atrisk']?>"></td>
	</tr><tr>
		<td><?=i18n('Number of school boards/distrcits')?>:</td> 
		<td><input type=text size="5" name="stats[schools_districts]" value="<?=$stats['schools_districts']?>"></td>
		<td></td><td></td>
	</tr></table>
	<br /> <br /> <br />
	<?
 }
 if($server_config['minorities']) {
 	echo '<h3>'.i18n('%1 Data on minority groups', array($year)).'</h3>';
	echo '<br />';
	echo '<table>';
	echo '<tr><td>'.i18n('Number of First Nations students');
	echo ": </td><td><input type=\"text\" name=\"stats[firstnations]\" value=\"{$stats['firstnations']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '</table>';
	echo '<br />';
	echo '<br />';
 }
 if($server_config['guests'] ) {
 	echo '<h3>'.i18n('%1 Guests visiting the fair', array($year)).'</h3>';
	echo '<br />';
	echo '<table>';
	echo '<tr><td>'.i18n('Number of Students that visited the fair (tours, etc.)');
	echo ": </td><td><input type=\"text\" name=\"stats[studentsvisiting]\" value=\"{$stats['studentsvisiting']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '<tr><td>'.i18n('Number of Public Guests that visited the fair');
	echo ": </td><td><input type=\"text\" name=\"stats[publicvisiting]\" value=\"{$stats['publicvisiting']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '</table>';
	echo '<br />';
	echo '<br />';
 }

 if($server_config['sffbc_misc']) {
 	echo '<h3>'.i18n('%1 Misc. SFFBC Questions', array($year)).'</h3>';
	echo '<br />';
	echo '<table>';
	echo '<tr><td>'.i18n('Number of Teachers supporting student projects');
	echo ": </td><td><input type=\"text\" name=\"stats[teacherssupporting]\" value=\"{$stats['teacherssupporting']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '<tr><td>'.i18n('Number of Students indicating increased interest in science & technology');
	echo ": </td><td><input type=\"text\" name=\"stats[increasedinterest]\" value=\"{$stats['increasedinterest']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '<tr><td>'.i18n('Number of Students considering careers in science & technology');
	echo ": </td><td><input type=\"text\" name=\"stats[consideringcareer]\" value=\"{$stats['consideringcareer']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '</table>';
	echo '<br />';
	echo '<br />';
 }

if($server_any_stats == false) {
	/* Every condition below will fail, tell the user something */
	echo i18n("No stats to gather.  Contact the admins if you believe this is an error.");
} else {
	echo "<input type=\"submit\" onClick=\"stats_save(); return false;\"value=\"".i18n('Save Fair Information')."\" />";
}

 echo '</form>';
 echo "<br />";
 echo "<br />";

/*
 echo "<hr /><pre>";
 print_r($fair);
 print_r($server_config);
 print_r($stats);
 echo "</pre>";
 */
if($_SESSION['embed'] != true) {
	send_footer();
}

?>
