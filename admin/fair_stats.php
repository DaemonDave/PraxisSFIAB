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
 require_once('../common.inc.php');
 require_once('../user.inc.php');
 user_auth_required('committee', 'admin');
 require_once('xml.inc.php');
 require_once('stats.inc.php');
 require_once('curl.inc.php');

 /* Hack so we can jump right to YSC stats */
 if($_GET['abbrv'] == 'YSC') {
 	$q = mysql_query("SELECT id FROM fairs WHERE abbrv='YSC'");
	$r = mysql_fetch_assoc($q);
	$_GET['id'] = $r['id'];
 }


 function stats_to_ysc($fair, $stats) 
 {
	if($fair['type'] == 'ysc') {
		/* Map data into YSC tags */
	 	$y=array();
		$y["numschoolstotal"]=$stats['schools_total'];
		$y["numschoolsactive"]=$stats['schools_active'];
		$y["numstudents"]=$stats['students_total'];
		$y["numk6m"]=$stats['male_1'] + $stats['male_4'];
		$y["numk6f"]=$stats['female_1'] + $stats['female_4'];
		$y["num78m"]=$stats['male_7'];
		$y["num78f"]=$stats['female_7'];
		$y["num910m"]=$stats['male_9'];
		$y["num910f"]=$stats['female_9'];
		$y["num11upm"]=$stats['male_11'];
		$y["num11upf"]=$stats['female_11'];
		$y["projk6"]=$stats['projects_1'] + $stats['projects_4'];
		$y["proj78"]=$stats['projects_7'];
		$y["proj910"]=$stats['projects_9'];
		$y["proj11up"]=$stats['projects_11'];
		$y["committee"]=$stats['committee_members'];
		$y["judges"]=$stats['judges'];
		return $y;
	} 
	return $stats;
 }
	

 send_header("Upload Fair Statistics and Information",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "fair_stats"
			);
 echo "<br />";

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

 if($_GET['year']) $year=intval($_GET['year']);
 else $year=$config['FAIRYEAR'];

 if($_GET['id']) $fairs_id=intval($_GET['id']);
 else if($_POST['id']) $fairs_id=intval($_POST['id']);
 else $fairs_id = -1;

 if($fairs_id != -1) {
 	$q = mysql_query("SELECT * FROM fairs WHERE id='$fairs_id'");
	$fair = mysql_fetch_assoc($q);
 }

 $action = $_POST['action'];

 if($action == 'sendstats') {
 	foreach(array_keys($stats_data) as $k) {
		$stats[$k] = $_POST[$k];
	}
	$stats['year'] = $year;
	if($fair['type'] == 'ysc') {
	 	$st = stats_to_ysc($fair, $stats);
		$req = array('affiliation' => array(
				"ysf_region_id"=>$fair['username'],
				"ysf_region_password"=>$fair['password'],
				"year"=>$year,
				'stats'=>$st)
			);
	} else {
		$req = array('stats'=>$stats);
	}

 	if(function_exists('curl_init')) {
		$r = curl_query($fair, $req,
			'https://secure.ysf-fsj.ca/registration/xmlaffiliation.php');
		if($r['error'] == 0) 
			echo happy(i18n("The %1 Server said:", array($fair['name'])).' '.$r['message']);
		else 
			echo error(i18n("The %1 Server said:", array($fair['name'])).' '.$r['message']);
//		$fairs_id = -1;
//		$year = $config['FAIRYEAR'];
	} else {
		echo error("CURL Support Missing");
		echo i18n("Your PHP installation does not support CURL.  You will need to login to the YSC system as the regional coodinator and upload the XML data manually");
		send_footer();
		exit;
	}
 }


 echo "<form name=\"fairselect\" action=\"$PHPSELF\" method=\"get\">";
 $q=mysql_query("SELECT * FROM fairs WHERE `type`='sfiab' OR `type`='ysc' AND enable_stats='yes'");
 echo "<select name=\"id\">";
 echo "<option value=\"\">".i18n("Choose a destination")."</option>\n";
 while($r=mysql_fetch_object($q)) {
 	if($fairs_id==$r->id) $sel="selected=\"selected\""; else $sel="";
 	echo "<option $sel value=\"{$r->id}\">{$r->name} ({$r->abbrv})</option>\n";
 }
 echo "</select>\n";

 $q=mysql_query("SELECT DISTINCT(year) AS year FROM config WHERE year>0 ORDER BY year");
 echo "<select name=\"year\">";
 echo "<option value=\"\">".i18n("Choose a year")."</option>\n";
 while($r=mysql_fetch_object($q)) {
 	if($year==$r->year) $sel="selected=\"selected\""; else $sel="";
 	echo "<option $sel value=\"$r->year\">$r->year</option>\n";
 }
 echo "</select>\n";
 echo "<input type=\"submit\" name=\"submit\" value=\"".i18n('Prepare Stats')."\" />";
 echo "</form>";
 echo "<br />";
 echo "<hr />";

 if($fairs_id == -1) {
	echo i18n('Statistics will be shown below this line before being sent.  Please select a destination and year first.');
 	/* Wait for them to select somethign before generating stats */
 	send_footer();
	exit;
 } 

 $ok = true;
 if(trim($fair['username']) == '') {
 	echo error(i18n("You have not yet specified a username for this server.  Go to the <a href=\"sciencefairs.php\">Science Fair Management</a> page to set it"));
	$ok=false;
 }
 if(trim($fair['password']) == '') {
 	echo error(i18n("You have not yet specified a password for this server.  Go to the <a href=\"sciencefairs.php\">Science Fair Management</a> page to set it"));
	$ok=false;
 }


 if($fair['type'] == 'ysc') {
 	$data['statconfig'] = array('participation');
 } else {
	echo notice(i18n('Getting stats request and downloading existing stats from server %1', array($fair['url'])));
	/* Query the server to see what stats we need */
	$q=array('getstats' => array('year' => $year));

	$data = curl_query($fair, $q);

	if($data['error'] != 0) {
 		echo error("Server said: {$data['message']}<br />");
		send_footer();
		exit;
	}
	echo notice(i18n('Server said: Success'));
 }
 echo '<hr />';
 echo i18n('This server has requested the following stats for your %1 fair:', array($year));
 echo '<br /><br />';

 foreach($data['statconfig'] as $k) {
	$server_config[$k] = true;
 }

 /* Gather all stats, then we'll decide what to send */
 $stats = array();
 $stats['year'] = $year;

 /* Now, overwrite all the stats with what we pulled down from the server */
 if(is_array($data['stats'])) {
	 foreach($data['stats'] as $k=>$v) {
 		$stats[$k] = $v;
	 }
 }
// print_r($data['stats'][0]);

 /* And now, overwrite all the stuff we pulled down with stats we can compute */

 //number of schools
 $q=mysql_query("SELECT COUNT(id) AS num FROM schools WHERE year='$year'");
 $r=mysql_fetch_object($q);
 $stats['schools_total']=$r->num;

 //number of schools participating
 $q=mysql_query("SELECT DISTINCT(students.schools_id) AS sid, schools.*
 		 	FROM students 
				LEFT JOIN registrations ON students.registrations_id=registrations.id 
				LEFT JOIN schools ON students.schools_id=schools.id
			WHERE students.year='$year' 
				AND registrations.year='$year' 
				AND (registrations.status='complete' OR registrations.status='paymentpending')");
 $stats['schools_active']=mysql_num_rows($q);
 $stats['schools_public'] = 0;
 $stats['schools_private'] = 0;
 $stats['schools_atrisk'] = 0;
 $districts = array();
 while($si=mysql_fetch_assoc($q)) {
	if($si['designate'] == 'public') 
		 $stats['schools_public']++;
	if($si['designate'] == 'independent') 
		 $stats['schools_private']++;
	if($si['atrisk'] == 'yes') 
		 $stats['schools_atrisk']++;
	$bd = $si['board'].'~'.$si['district'];
	if(!in_array($bd, $districts)) $districts[] =$bd;
 }
 $stats['schools_districts'] = count($districts);

 //numbers of students:
 $q=mysql_query("SELECT students.*,schools.* 
	 		FROM students
				LEFT JOIN registrations ON students.registrations_id=registrations.id 
				LEFT JOIN schools on students.schools_id=schools.id
			WHERE students.year='$year' 
				AND registrations.year='$year' 
				AND (registrations.status='complete' OR registrations.status='paymentpending')");
 echo mysql_error();
 $stats['students_total'] = mysql_num_rows($q);
 $stats['students_public'] = 0;
 $stats['students_private'] = 0;
 $stats['students_atrisk'] = 0;
 $grademap = array(1=>1, 2=>1, 3=>1, 4=>4, 5=>4, 6=>4, 7=>7, 8=>7, 
				9=>9, 10=>9, 11=>11, 12=>11, 13=>11);
 foreach($grademap as $k=>$g) {
 	$stats["male_$g"] = 0;
 	$stats["female_$g"] = 0;
 	$stats["projects_$g"] = 0;
 }
 $unknown = array();
 while($s=mysql_fetch_assoc($q)) {
 	if(!in_array($s['sex'], array('male','female'))) 
		$unknown[$grademap[$s['grade']]]++;
	else 
 		$stats["{$s['sex']}_{$grademap[$s['grade']]}"]++;

	if($s['designate'] == 'public') 
		$stats['students_public']++;
	if($s['designate'] == 'independent') 
		$stats['students_private']++;
	if($s['atrisk'] == 'yes')
		$stats['students_atrisk']++;
 }

 foreach($unknown as $g=>$a) {
	$m = round($a/2);
	$f = $a - $m;
	$stats["male_$g"] += $m;
	$stats["female_$g"] += $f;
 }

 //projects
 $q=mysql_query("SELECT MAX(students.grade) AS grade FROM students
	 		LEFT JOIN registrations ON students.registrations_id=registrations.id 
			LEFT JOIN projects ON projects.registrations_id=registrations.id 
		WHERE  students.year='$year' 
			AND registrations.year='$year' 
			AND projects.year='$year' 
			AND (registrations.status='complete' OR registrations.status='paymentpending') 
			GROUP BY projects.id");
 echo mysql_error();
 while($r=mysql_fetch_assoc($q)) {
	$stats["projects_{$grademap[$r['grade']]}"]++;
 }


 $q=mysql_query("SELECT COUNT(id) AS num FROM users 
 				LEFT JOIN users_committee ON users_committee.users_id=users.id
	 		WHERE types LIKE '%committee%' 
				AND year='$year' 
				AND users_committee.committee_active='yes'
				AND deleted='no'");
 $r = mysql_fetch_object($q);
 $stats['committee_members'] = $r->num;

 $q=mysql_query("SELECT COUNT(id) AS num FROM users LEFT JOIN users_judge ON users_judge.users_id=users.id 
 					WHERE users.year='$year' 
						AND users.types LIKE '%judge%'
						AND users.deleted='no'
						AND users_judge.judge_complete='yes' 
						AND users_judge.judge_active='yes'");
 $r=mysql_fetch_object($q);
 $stats['judges'] = $r->num;


/* All stats have been gathered, print them */


 /* Print all blocks the server requests */
 echo "<form method=\"POST\" action=\"$PHPSELF\">";
 echo "<input type=\"hidden\" name=\"action\" value=\"sendstats\" />";

 if($server_config['info']) {
 	echo '<h3>'.i18n('%1 Fair information', array($year)).'</h3>';
	echo '<table>';
	echo '<tr><td>'.i18n('Fair Start Date').':</td>';
	echo "<td><input type=\"text\" size=\"12\" name=\"start_date\" value=\"{$stats['start_date']}\">(YYYY-MM-DD)</td></tr>";
	echo '<tr><td>'.i18n('Fair End Date').':</td>';
	echo "<td><input type=\"text\" size=\"12\" name=\"end_date\" value=\"{$stats['end_date']}\">(YYYY-MM-DD)</td></tr>";
	echo '<tr><td>'.i18n('Fair Location/Address').':</td>';
	echo '<td><textarea name="address" rows="4" cols="60">'.htmlspecialchars($stats['address']).'</textarea></td>';
	echo '<tr><td>'.i18n('Fair Budget').':</td>';
	echo "<td>$<input type=text name=\"budget\" value=\"{$stats['budget']}\"></td></tr>";
	echo '<tr><td>'.i18n('Youth Science Canada Affiliation Complete').'?</td>';
	echo '<td><select name="ysf_affiliation_complete">';
	$sel = $stats['ysf_affiliation_complete'] == 'N' ? 'selected="selected"' : '';
	echo " <option value=\"N\" $sel >No</option>";
	$sel = $stats['ysf_affiliation_complete'] == 'Y' ? 'selected="selected"' : '';
	echo " <option value=\"Y\" $sel >Yes</option>";
	echo '</select></td></tr>';
	echo '<tr><td>'.i18n('Charity Number or Information').'?</td>';
	echo "<td><input type=text size=\"40\" name=\"charity\" value=\"{$stats['charity']}\"></td></tr>";
	echo '</table>';
	echo '<br />';
	echo '<br />';
 }

	
 if($server_config['next_chair']) {
 	echo '<h3>'.i18n('%1 - %2 Chairperson (if known)', array($year, $year+1)).'</h3>';
	echo '<table>';
	echo '<tr><td>'.i18n('Name').': </td>';
	echo "<td><input type=text name=\"next_chair_name\" value=\"{$stats['next_chair_name']}\"></td>";
	echo '<td>'.i18n('Email').': </td>';
	echo "<td><input type=text name=\"next_chair_email\" value=\"{$stats['next_chair_email']}\"></td></tr>";
	echo '<tr><td>'.i18n('Tel. Bus').': </td>';
	echo "<td><input type=text name=\"next_chair_bphone\" value=\"{$stats['next_chair_bphone']}\"></td>";
	echo '<td>'.i18n('Tel. Home').': </td>';
	echo "<td><input type=text name=\"next_chair_hphone\" value=\"{$stats['next_chair_hphone']}\"></td></tr>";
	echo '<tr><td>'.i18n('Fax').': </td>';
	echo "<td><input type=text name=\"next_chair_fax\" value=\"{$stats['next_chair_fax']}\"></td>";
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
	echo '<textarea name="scholarships" rows="4\" cols="80">'.htmlspecialchars($stats['scholarships']).'</textarea>';
	echo '<br /><br />';
 }

 if($server_config['participation']) {
 	$rangemap = array(1=>'1-3', 4=>'4-6', 7=>'7-8', 9=>'9-10', 11=>'11-12');
 	echo '<h3>'.i18n('%1 Fair participation', array($year)).'</h3>';
	echo '<br />';
	echo i18n("Number of students").": <b>{$stats['students_total']}</b>";
	echo '<table><tr><td></td><td></td><td></td><td align=\"center\">'.i18n('Grade').'</td><td></td><td></td></tr>';
	echo '<tr><td></td>';
	foreach($rangemap as $k=>$v) echo "<td align=\"center\" width=\"50px\" >$v</td>";
	echo '</tr><tr>';
	echo '<td>'.i18n('Male').'</td>';
	foreach($rangemap as $k=>$v) echo "<td align=\"right\"><b>{$stats["male_$k"]}</b></td>";
	echo '</tr><tr>';
	echo '<td>'.i18n('Female').'</td>';
	foreach($rangemap as $k=>$v) echo "<td align=\"right\"><b>{$stats["female_$k"]}</b></td>";
	echo '</tr><tr>';
	echo '<td>'.i18n('Projects').'</td>';
	foreach($rangemap as $k=>$v) echo "<td align=\"right\"><b>{$stats["projects_$k"]}</b></td>";
	echo '</tr>';
	echo '</table>';
	echo '<br />';
	echo i18n("Number of schools").": <b>{$stats['schools_total']}</b>";
	echo '<br />';
	echo i18n("Number of active schools").": <b>{$stats['schools_active']}</b>";
	echo '<br />';
	echo '<br />';
	echo i18n("Number of committee members: <b>%1</b> (note: this is number of committee members who logged in to SFIAB for the year, anyone who was active but didn't log in to SFIAB will NOT be counted)",array($stats['committee_members']));
	echo '<br />';
	echo i18n("Number of judges").": <b>{$stats['judges']}</b>";
	echo '<br />';
	echo '<br />';
	echo '<br />';
 }

 if($server_config['schools_ext']) {
 	echo '<h3>'.i18n('%1 Extended School/Participant data', array($year)).'</h3>';
	echo '<br />';
 	echo i18n('Public schools: <b>%1</b> (<b>%2</b> students).',array(
				$stats['schools_public'], $stats['students_public']));
	echo '<br />';
 	echo i18n('Private/Independent schools: <b>%1</b> (<b>%2</b> students).',array(
				$stats['schools_private'], $stats['students_private']));
	echo '<br />';
 	echo i18n('At-risk/inner city schools: <b>%1</b> (<b>%2</b> students).',array(
				$stats['schools_atrisk'], $stats['students_atrisk']));
	echo '<br />';
 	echo i18n('Number of school boards/distrcits: <b>%1</b>',array(
				$stats['schools_districts']));
	echo '<br />';
	echo '<br />';
	echo '<br />';
 }
 if($server_config['minorities']) {
 	echo '<h3>'.i18n('%1 Data on minority groups', array($year)).'</h3>';
	echo '<br />';
	echo '<table>';
	echo '<tr><td>'.i18n('Number of First Nations students');
	echo ": </td><td><input type=\"text\" name=\"firstnations\" value=\"{$stats['firstnations']}\" size=\"5\" />";
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
	echo ": </td><td><input type=\"text\" name=\"studentsvisiting\" value=\"{$stats['studentsvisiting']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '<tr><td>'.i18n('Number of Public Guests that visited the fair');
	echo ": </td><td><input type=\"text\" name=\"publicvisiting\" value=\"{$stats['publicvisiting']}\" size=\"5\" />";
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
	echo ": </td><td><input type=\"text\" name=\"teacherssupporting\" value=\"{$stats['teacherssupporting']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '<tr><td>'.i18n('Number of Students indicating increased interest in science & technology');
	echo ": </td><td><input type=\"text\" name=\"increasedinterest\" value=\"{$stats['increasedinterest']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '<tr><td>'.i18n('Number of Students considering careers in science & technology');
	echo ": </td><td><input type=\"text\" name=\"consideringcareer\" value=\"{$stats['consideringcareer']}\" size=\"5\" />";
	echo '</td></tr>';
	echo '</table>';
	echo '<br />';
	echo '<br />';
 }
 $keys = array_keys($stats_data);
 foreach($keys as $k) {
 	if($stats_data[$k]['manual'] == true) continue;
	echo "<input type=\"hidden\" name=\"$k\" value=\"{$stats[$k]}\" />";
 }

 $d = ($ok == true) ? '' : 'disabled="disabled"';
 echo "<input type=\"submit\" value=\"".i18n('Send stats to')." {$fair['name']}\" $d />";
 echo '</form>';
 echo "<br />";
 echo "<br />";

debug_("Fair Info: ".print_r($fair, true));
debug_("Server Config: ".print_r($server_config, true));
debug_("Stats: ".print_r($stats, true));
 
 send_footer();
?>
