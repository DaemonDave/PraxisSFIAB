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


$tabs = array(	'fairinfo' => array(
			'label' => 'Fair Information',
			'types' => array('fair'),
			'file' => '../fair_info.php',
			'enabled' => true,),
		'fairstatsgathering' => array(
			'label' => 'Fair Stats Gathering',
			'types' => array('fair'),
			'file' => 'fair_stats_select.php',
			'enabled' => true,),
		'personal' => array(
			'label' => 'Personal',
			'types' => array('student','judge','committee','volunteer','sponsor','fair'),
			'file' => '../user_personal.php',
			'enabled' => true),
		'roles' => array(
			'label' => 'Roles/Account',
			'types' => array('student','judge','committee','volunteer','sponsor','fair'),
			'file' => '../user_activate.php',
			'enabled' => true),
		'judgeother' => array(
			'label' => 'Judge Other',
			'types' => array('judge'),
			'file' => '../judge_other.php',
			'enabled' => true),
		'judgeexpertise' => array(
			'label' => 'Expertise',
			'types' => array('judge'),
			'file' => '../judge_expertise.php',
			'enabled' => true),
		'judgeavailability' => array(
			'label' => 'Time Avail.',
			'types' => array('judge'),
			'file' => '../judge_availability.php',
			'enabled' => $config['judges_availability_enable'] == 'yes' ? true : false),
		'judgesa' => array(
			'label' => 'Special Awards',
			'types' => array('judge'),
			'file' => '../judge_special_awards.php',
			'enabled' => true,),
		'volunteerpos' => array(
			'label' => 'Volunteer Positions',
			'types' => array('volunteer'),
			'file' => '../volunteer_position.php',
			'enabled' => true,),
		'fairstats' => array(
			'label' => 'Fair Statistics and Information',
			'types' => array('fair'),
			'file' => '../fair_stats.php',
			'enabled' => true,),


		);


if(array_key_exists('username',$_GET)) {
	$username = $_GET['username'];
	$type = $_GET['type'];
	$un = mysql_escape_string($username);
	$q = mysql_query("SELECT id,MAX(year),deleted FROM users WHERE username='$un' GROUP BY uid");
echo mysql_error();

	if(mysql_num_rows($q)) {
		$r = mysql_fetch_object($q);
		if($r->deleted == 'no') {
			/* Load the user */
			$u = user_load_by_email($username);
			if(in_array($type, $u['types'])) {
				echo "Username already exists with role '$type'";
				exit;
			} else {
				/* Add the role, user_create does a role_allowed check
				 * so we'll never add a judge/committee role to a student */
				user_create($type, $username, $u);
			}
		}
	} else {
		$u = user_create($type, $username);
		$u['email'] = $username;
	}
	user_save($u);
	$id = $u['id'];
} else {
	$id = $_GET['id'];
}

$u = user_load($id);

$selected = $_GET['tab'];
if(!array_key_exists($selected, $tabs)) {
	if(in_array('fair', $u['types']) )
		$selected = 'fairinfo';
	else 
		$selected = 'personal';
}


if($_GET['sub'] == 1) {
	$_SESSION['embed'] = true;
	$_SESSION['embed_submit_url'] = "{$_SERVER['PHP_SELF']}?id=$id&tab=$selected";
	$_SESSION['embed_edit_id'] = $id;
	$t = $tabs[$selected];
	include("{$t['file']}");

	unset($_SESSION['embed']);
	unset($_SESSION['embed_edit_id']);
	unset($_SESSION['embed_submit_url']);
	exit;
}

send_popup_header(i18n("User Editor").": {$u['name']}");


/* Setup tabs */
echo '<div id="tabs">';
echo '<ul>';
$index = 0;
$selected_index = 0;
foreach($tabs as $k=>$t) {
	/* Make sure the tab is enabled */
	if($t['enabled'] == false) continue;
	/* Make sure the user has the right type to see the tab */
	$i = array_intersect($t['types'], $u['types']);
	if(count($i) == 0) continue;

	if($k == $selected) $selected_index = $index;
	$index++;

	/* Show the tab */
	$href = "{$_SERVER['PHP_SELF']}?id=$id&tab=$k&sub=1";
	echo "<li><a href=\"$href\"><span>".i18n($t['label'])."</span></a></li>";
}
echo '</ul>';

?>
<script type="text/javascript">

$(document).ready(function() {
	$("#tabs").tabs({
			selected: <?=$selected_index?>	
		});
	window.focus();
});

</script>

<?

$icon_path = $config['SFIABDIRECTORY']."/images/16/";
$icon_exitension = $config['icon_extension'];


	send_popup_footer();
?>
