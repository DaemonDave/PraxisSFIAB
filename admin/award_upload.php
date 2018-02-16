<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2007 James Grant <james@lightbox.org>

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

require_once('../common.inc.php');
require_once('../user.inc.php');
require_once('../projects.inc.php');
require_once('curl.inc.php');
user_auth_required('committee', 'admin');

//function get_cwsf_award_winners()
function get_winners($awardid, $fairs_id)
{
 	global $config;

	/* Mappings of the name we want => to the column name returned in MYSQL */
	$school_fields = array( 'schoolname'=>'school',
				'schoollang'=>'schoollang',
				'schoollevel'=>'schoollevel',
				'board'=>'board',
				'district'=>'district',
				'phone'=>'phone',
				'fax'=>'fax',
				'address'=>'address',
				'city'=>'city',
				'province_code'=>'province_code',
				'postalcode'=>'postalcode',
				'schoolemail'=>'schoolemail');
/*				'principal'=>'principal',
				'sciencehead'=>'sciencehead',
				'scienceheademail'=>'scienceheademail',
				'scienceheadphone'=>'scienceheadphone');*/

	$student_fields = array('firstname'=>'firstname',
				'lastname'=>'lastname',
				'email'=>'email',
				'gender'=>'sex',
				'grade'=>'grade',
				'language'=>'lang',
				'birthdate'=>'dateofbirth',
				'address'=>'address',
				'city'=>'city',
				'province'=>'province',
				'postalcode'=>'postalcode',
				'phone'=>'phone',
				'teachername'=>'teachername',
				'teacheremail'=>'teacheremail');


	$awards = array();
	if($awardid == -1) {
		/* Get all for this fair */
	 	$q=mysql_query("SELECT * FROM award_awards WHERE award_source_fairs_id='$fairs_id' AND year='{$config['FAIRYEAR']}'");
		if(mysql_num_rows($q) == 0) {
			error_("Can't find award id $awardid");
			return false;
		}
		while($a = mysql_fetch_assoc($q)) {
			$awards[] = $a;
		}
	} else {
		/* Get the award */
	 	$q=mysql_query("SELECT * FROM award_awards WHERE id='$awardid' AND year='{$config['FAIRYEAR']}'");
		if(mysql_num_rows($q)!=1) {
			error_("Can't find award id $awardid");
			return false;
		}
		$award=mysql_fetch_assoc($q);
		$awards[] = $award;
	}

	/* Get the fair for the div/cat mappings */
	$q = mysql_query("SELECT * FROM fairs WHERE id='{$award['award_source_fairs_id']}'");
	$fair = mysql_fetch_assoc($q);
	$catmap = unserialize($fair['catmap']);
	$divmap = unserialize($fair['divmap']);


	foreach($awards as $award) {
		$winners=array(	'id' => $award['id'],
				'award_name' => $award['name'],
				'external_identifier' => $award['external_identifier'],
				'year' => $config['FAIRYEAR'],
				'prizes' => array());

		if($fair['type'] != 'sfiab') {
			/* YSC Compatability */
			$winners['external_postback'] = $award['external_postback'];
		}

		/* Get the prizes */
		$q=mysql_query("SELECT * FROM award_prizes WHERE award_awards_id='{$award['id']}'");
		while($prize=mysql_fetch_assoc($q)) {
			$pid = $prize['id'];
			$wq=mysql_query("SELECT projects.* FROM award_prizes
						LEFT JOIN winners ON winners.awards_prizes_id=award_prizes.id
						LEFT JOIN projects ON projects.id=winners.projects_id
					WHERE 
						awards_prizes_id='$pid' AND 
						winners.year='{$config['FAIRYEAR']}'");
			echo mysql_error();
			/* Get all projects assigned to this prize */
			$prizewinners = array();
			while($project=mysql_fetch_assoc($wq)) {

				/* Get the students */
				$sq=mysql_query("SELECT * FROM students WHERE registrations_id='{$project['registrations_id']}'
							AND year='{$config['FAIRYEAR']}'");
				$students=array();
				while($s=mysql_fetch_assoc($sq)) {

					/* Get the student's school */
					$schoolq=mysql_query("SELECT * FROM schools WHERE id='{$s['schools_id']}'");
					$schoolr=mysql_fetch_assoc($schoolq);
					$school = array("xml_type"=>"school");/* for ysc compatability */
					foreach($school_fields as $k=>$v) 
						$school[$k] = $schoolr[$v];

					/* Pack up the student data too */
					$student = array('xml_type'=>'student',/* for ysc compatability */
							'school' => $school);
					foreach($student_fields as $k=>$v) 
						$student[$k] = $s[$v];
					
					$students[] = $student;

				}
				/* Turn our load ID into a server-side cat/div id */
				$cat_id = $catmap[$project['projectcategories_id']];
				$div_id = $divmap[$project['projectdivisions_id']];

				/* Save the project info => students */
				$prizewinners[]=array(	'xml_type' => 'project',/* for ysc compatability */
							'projectid'=>$project['id'],
							'projectnumber'=>$project['projectnumber'],
							'title'=>$project['title'],
							'abstract'=>$project['summary'],
							'language'=>$project['language'],
							'projectcategories_id'=>$cat_id,
							'projectdivisions_id'=>$div_id,
							'client_projectdivisions_id' => $project['projectdivisions_id'],
							'students'=>$students );
			}
			/* Save the prize info => projects */
			$winners['prizes'][$prize['prize']] = array(
					'id' => $prize['id'],
					'name' => $prize['prize'],
					'xml_type'=>'prize', /* For ysc compatability */
					'identifier'=>$prize['external_identifier'], /* for ysc compatability */
					'projects'=>$prizewinners);
		}
		$all_winners[] = $winners;
	}
	return $all_winners;
}

function count_winners($awardid, $fairs_id)
{
	global $config;
	$count = 0;
	$awards = array();
	if($awardid == -1) {
		/* Get all for this fair */
	 	$q=mysql_query("SELECT * FROM award_awards WHERE award_source_fairs_id='$fairs_id' AND year='{$config['FAIRYEAR']}'");
		if(mysql_num_rows($q) == 0) {
			error_("Can't find award id $awardid");
			return 0;
		}
		while($a = mysql_fetch_assoc($q)) {
			$awards[] = $a;
		}
	} else {
		/* Get the award */
	 	$q=mysql_query("SELECT * FROM award_awards WHERE id='$awardid' AND year='{$config['FAIRYEAR']}'");
		if(mysql_num_rows($q)!=1) {
			error_("Can't find award id $awardid");
			return 0;
		}
		$award=mysql_fetch_assoc($q);
		$awards[] = $award;
	}

	foreach($awards as $award) {
		/* Get the prizes */
		$q=mysql_query("SELECT * FROM award_prizes WHERE award_awards_id='{$award['id']}'");
		while($prize=mysql_fetch_assoc($q)) {
			$pid = $prize['id'];
			$wq=mysql_query("SELECT COUNT(projects.id) as C FROM award_prizes
						LEFT JOIN winners ON winners.awards_prizes_id=award_prizes.id
						LEFT JOIN projects ON projects.id=winners.projects_id
					WHERE 
						awards_prizes_id='$pid' AND 
						winners.year='{$config['FAIRYEAR']}'");
			$wc = mysql_fetch_assoc($wq);
			$count += $wc['C'];
		}
	}
	return $count;
			
}


function load_server_cats_divs($fairs_id)
{
	global $config;
	$q = mysql_query("SELECT * FROM fairs WHERE id='$fairs_id'");
	$fair = mysql_fetch_assoc($q);

	$req = array('get_categories' => array('year' => $config['FAIRYEAR']),
			'get_divisions' => array('year' => $config['FAIRYEAR'])
			);
	$data = curl_query($fair, $req);

	/* If selected mappings don't exist, try to discover some */
	if(trim($fair['catmap']) != '') {
		$catmap = unserialize($fair['catmap']);
	} else {
		$catmap = array();
		/* Load ours */
		$q=mysql_query("SELECT * FROM projectcategories WHERE year='{$config['FAIRYEAR']}' ORDER BY mingrade");
		while($r=mysql_fetch_object($q)) {
			foreach($data['categories'] as $id=>$c) {
				if($c['mingrade'] == $r->mingrade) {
					$catmap[$r->id] = $id;
					break;
				}
			}
		}
	}
	if(trim($fair['divmap']) != '') {
		$divmap = unserialize($fair['divmap']);
	} else {
		$ret['divmap'] = array();
		$q=mysql_query("SELECT * FROM projectdivisions WHERE year='{$config['FAIRYEAR']}' ORDER BY id");
		while($r=mysql_fetch_object($q)) {
			$lowest = 999;
			$lowest_id = 0;
			foreach($data['divisions'] as $id=>$d) {
				/* Who knew levenshtein was builtin to php as of PHP 4 */
				$l = levenshtein($d['division'], $r->division);
				if($l < $lowest) {
					$lowest = $l;
					$lowest_id = $id;
				}
			}
			$divmap[$r->id] = $lowest_id;
		}
	}
	return array($data['categories'], $data['divisions'], $catmap, $divmap);
}

switch($_GET['action']) {
case 'award_upload':
	$award_awards_id = intval($_POST['award_awards_id']);
	$fairs_id = intval($_POST['fairs_id']);
	$divs = $_POST['div'];
	$cats = $_POST['cat'];


	$all_winners = get_winners($award_awards_id, $fairs_id);

	/* Get the fair */
	$q = mysql_query("SELECT * FROM fairs WHERE id='$fairs_id}'");
	$fair = mysql_fetch_assoc($q);

	echo '<br />';
	/* Check that we're going to upload something, and override the 
	 * divisions/cats with the divisions taht were set, and the categories
	 * that were computed */
	$upload_something = false;
	foreach($all_winners as &$w) {
		foreach($w['prizes'] as &$p) {

			if(count($p['projects'])) 
				$upload_something = true;

			/* Only update divs/cats for SFIAB fairs, the 
			 * YSC/STO awards system doesn't care about divisions, but YSC 
			 * registration does, but that's a different bit of code */
			if($fair['type'] != 'sfiab') continue;

			foreach($p['projects']  as &$pr) {
				$div_id = intval($divs[$w['id']][$p['id']][$pr['projectid']]);
				$pr['projectdivisions_id'] = $div_id;
				$cat_id = intval($cats[$w['id']][$p['id']][$pr['projectid']]);
				$pr['projectcategories_id'] = $cat_id;
			}
		}
	}

	if($upload_something == false) {
		echo notice(i18n('No winners to be uploaded'));
		exit;
	}


	if($fair['type'] == 'ysc') {
		if($award_awards_id == -1) {
			echo "Multiple uploads not supported for YSC targets.\n";
			exit;
		}
		/* Pull the single-award out, get_winners() will never
		 * return more than one award for YSC targets */
		$winners = array_shift($all_winners);
		$w = array();
		foreach($winners['prizes'] as $prize_name=>$prize) {
			$w[] = $prize;
		}
		$req=array("awardwinners"=>array(
				"username"=>$fair['username'],
				"password"=>$fair['password'],
				"identifier"=>$winners['external_identifier'],
				"prizes"=>$w,
				)
			);
		$url = $winners['external_postback'];
	} else {
		$req = array();
		$req['awards_upload'] = $all_winners;
		$url = ''; /* url is ignored for type = sfiab */

	}
	echo i18n("Sending winners to %1...", array('<b>'.$fair['name'].'</b>'));
	echo '<br />';
//		echo "<pre>"; print_r($req); echo "</pre>";
			
	$data = curl_query($fair, $req, $url);

	if($data['error'] != 0) {
		echo error("Server said: ".htmlspecialchars(print_r($data,true)));
	} else {
		if(is_array($data['notice']))
			echo notice("{$fair['name']} server said: <pre>".join("\n", $data['notice'])."</pre>");
		else if(is_array($data['message']))
			echo notice("{$fair['name']} server said: <pre>".join("\n", $data['message'])."</pre>");
		else if($data['message'])
			echo notice("{$fair['name']} server said: <pre>".$data['message']."</pre>");
		else 
			echo notice("{$fair['name']} server said: <pre>".htmlspecialchars(print_r($data,true))."</pre>");
		echo happy(i18n("Upload completed successfully"));
	}
	exit;

case 'catdiv_load':
	$fairs_id = intval($_GET['fairs_id']);

	list($c, $d, $cm, $dm) = load_server_cats_divs($fairs_id);
	$divs = projectdivisions_load();

	$q = mysql_query("SELECT * FROM fairs WHERE id='$fairs_id}'");
	$fair = mysql_fetch_assoc($q);

?>	<h4><?=i18n("Division Mapping")?></h4>
	<br />

	<form id="catdiv_form">
	<input type="hidden" name="fairs_id" value="<?=$fairs_id?>" />

	<table class="editor">
	<tr><th><?=i18n("Our Division")?></th><th><?=i18n("%1 Division", array($fair['abbrv']))?></th></tr>
<?
	
	foreach($divs as $div) {
		echo "<tr><td class=\"label\">{$div['division']}&nbsp;=> </td>";
		echo "<td><select name=\"div[{$div['id']}]\" class=\"upload_div\">";
		$mapto = $dm[$div['id']];
		foreach($d as $sdiv) {
			$sel = ($sdiv['id'] == $mapto) ? 'selected="selected"' : '';
			echo "<option $sel value=\"{$sdiv['id']}\">{$sdiv['division']}</option>";
		}
		echo '</select></td></tr>';
	}
?>
	</table>
	</form>
	<br />
<?
	exit;

case 'catdiv_save':
 	$fairs_id = intval($_POST['fairs_id']);

	$cat = array();
//	foreach($_POST['cat'] AS $key=>$c) {
//		$cat[intval($key)] = intval($c);
//	}
	$div = array();
	foreach($_POST['div'] AS $key=>$d) {
		$div[intval($key)] = intval($d);
	}
	
	$catmap = mysql_real_escape_string(serialize($cat));
	$divmap = mysql_real_escape_string(serialize($div));
	mysql_query("UPDATE fairs SET catmap='$catmap',divmap='$divmap' WHERE id='$fairs_id'");
	echo "UPDATE fairs SET catmap='$catmap',divmap='$divmap' WHERE id='$fairs_id'";
	echo mysql_error();

	happy_("Category/Division mapping information saved");
	exit;

case 'additional_materials':
	$award_awards_id = intval($_GET['award_awards_id']);
	$q = mysql_query("SELECT award_source_fairs_id,external_identifier FROM award_awards WHERE id='$award_awards_id'");
	$a = mysql_fetch_assoc($q);
	$q = mysql_query("SELECT * FROM fairs WHERE id='{$a['award_source_fairs_id']}'");
	$fair = mysql_fetch_assoc($q);
	$req = array('award_additional_materials' => array(
				'year'=>$config['FAIRYEAR'],
				'identifier'=>$a['external_identifier'])
			);
	$data = curl_query($fair, $req, $url);
	foreach($data['award_additional_materials']['pdf']['header'] as $h) 
		header($h);
	echo base64_decode($data['award_additional_materials']['pdf']['data64']);
	exit;

case 'load':
	$award_awards_id = intval($_GET['id']);
	$fairs_id = intval($_GET['fairs_id']);

	$winners = get_winners($award_awards_id, $fairs_id);
	$divs = projectdivisions_load();

	$q = mysql_query("SELECT * FROM fairs WHERE id='$fairs_id}'");
	$fair = mysql_fetch_assoc($q);

	echo i18n("The following list of winning projects/students will be sent to: <b>%1</b>.  Use the 'Edit Default Division Assignments' button to change the default mappings for divisions.  You can over-ride any division assignment by changing it in the list below.  Category assignments are done automatically based on grade.  When you are happy with the list below, click the 'Upload Winners' button.", array($fair['name']));

	if($fair['type'] != 'sfiab') {
		echo '<br /><br />';
		echo i18n('This server does not collection Division information, all division selection is disabled.');
		$server_cats = array();
		$server_divs = array();
		$catmap =array();
		$divmap = array();
		$division_disabled = true;
	} else {
		list($server_cats, $server_divs, $catmap, $divmap) = load_server_cats_divs($fairs_id);
		$division_disabled = false;
	}

?>
	<br /><br />
	<button onClick="popup_divmap(<?=$fairs_id?>);return false;" <?=$division_disabled ? 'disabled="disabled' : ''?>
			title="<?=i18n("Edit Default Division Assignments")?>"><?=i18n("Edit Default Division Assignments")?></button>

	<form id="winner_divs_form">
	<input type="hidden" name="fairs_id" value="<?=$fairs_id?>" />
	<input type="hidden" name="award_awards_id" value="<?=$award_awards_id?>" />
	<table class="tableview">
<?
	
	foreach($winners as &$w) {
		echo "<tr><td style=\"border: 0px;\" colspan=\"3\">";
		echo "<br /><h3>{$w['award_name']}</h3>";
		foreach($w['prizes'] as &$p) {
			echo "<tr><td style=\"border: 0px;\" colspan=\"3\">";
			echo "<h4>{$p['name']}</h4>";
			echo '</td></tr>';
			if(count($p['projects']) == 0) {
				echo i18n('No winners to upload');
				continue;
			}
			foreach($p['projects'] as &$pr) {
?>				<tr><td style="border: 0px;">&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td><b><?=$pr['projectnumber']?> - <?=$pr['title']?></b><br/>
<?				$highest_grade = 0;
				foreach($pr['students'] as &$s) {
					echo i18n("Name").": ";
					echo $s['firstname']." ".$s['lastname'];
					echo "<br />";
					echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;";
					echo i18n("Grade").": ".$s['grade'];
					echo "<br />";
					echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;";
					echo i18n("School").": ".$s['school']['schoolname'];
					echo '<br />';
					if($s['grade'] > $highest_grade) $highest_grade = $s['grade'];
				}
				$server_cat = '';
				foreach($server_cats as $c) {
					if($highest_grade >= $c['mingrade'] && $highest_grade <= $c['maxgrade']) {
						$server_cat = $c['id'];
					}
				}
?>
				</td>
				<td>
					<table class="default">
					<tr>	<td align="right" style="border: 0px;"><?=i18n('Our division')?>:</td>
						<td><b><?=$divs[$pr['client_projectdivisions_id']]['division']?></td>
					</tr>
<?
					if($division_disabled == false) {
?>						<tr>	<td align="right"><?=i18n('%1 Division', array($fair['abbrv']))?>:</td>
							<td><select name="div[<?=$w['id']?>][<?=$p['id']?>][<?=$pr['projectid']?>]">
<?
							$mapto = $divmap[$pr['client_projectdivisions_id']];
							foreach($server_divs as $d) {
								$sel = ($mapto == $d['id']) ? 'selected="selected"' : '';
								echo "<option $sel value=\"{$d['id']}\">{$d['division']}</option>";
							}
?>							</select>
							<input type="hidden" name="cat[<?=$w['id']?>][<?=$p['id']?>][<?=$pr['projectid']?>]" value="<?=$server_cat?>" />
							</td>
						</tr>
						<tr>	<td align="right"><?=i18n('%1 Category', array($fair['abbrv']))?>:</td>
							<td><b><?=$server_cats[$server_cat]['category']?> (<?=i18n('Grade')?> <?=$server_cats[$server_cat]['mingrade']?> - <?=$server_cats[$server_cat]['maxgrade']?>)</td>
						</tr>
<?					}
?>					</table>
				</td></tr>
<?
			}
			
		}
	}
	echo '</table></form><br />';
	exit;
	
}


 send_header("Award Upload",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Awards Main' => 'admin/awards.php')
			);
 echo "<br />";

?>
<script type="text/javascript">

var fairs_id = -1;
var award_awards_id = -1;

function catdiv_save()
{
	$("#debug").load("<?=$_SERVER['PHP_SELF']?>?action=catdiv_save",
			$('#catdiv_form').serializeArray());
	return false;
}

function popup_upload_load()
{
	var ids = "&id="+award_awards_id+"&fairs_id="+fairs_id;
	$("#popup_upload").load("<?=$_SERVER['PHP_SELF']?>?action=load"+ids);
}

function popup_upload(fid,aaid)
{
	var w = (document.documentElement.clientWidth * 0.8);
	var h = (document.documentElement.clientHeight * 0.8);

	fairs_id = fid;
	award_awards_id = aaid;

	/* Load dialog content (it's in a function because we use it when
	 * the div editor closes too, to reload the content */
	popup_upload_load();

	/* Show the dialog */
	$('#popup_upload').dialog('option', 'width', w);
	$('#popup_upload').dialog('option', 'height', h);
	$("#popup_upload").dialog('open');

	return true;
}

function popup_divmap(fid)
{
	var w = (document.documentElement.clientWidth * 0.4);
	var h = (document.documentElement.clientHeight * 0.6);

	/* Load dialog content */
	$("#popup_divmap").load("<?=$_SERVER['PHP_SELF']?>?action=catdiv_load&fairs_id="+fairs_id);

	/* Show the dialog */
	$('#popup_divmap').dialog('option', 'width', w);
	$('#popup_divmap').dialog('option', 'height', h);
	$("#popup_divmap").dialog('open');

	return true;
}


/* Setup the popup window */
$(document).ready(function() { 
	$("#popup_upload").dialog({
			bgiframe: true, autoOpen: false,
			modal: true, resizable: false,
			draggable: false,
			buttons: { 
				"<?=i18n('Cancel')?>": function() { 
						$(this).dialog("close"); 
					},
				"<?=i18n('Upload Winners')?>": function() { 
						$("#award_upload_status").load("<?=$_SERVER['PHP_SELF']?>?action=award_upload",
								$('#winner_divs_form').serializeArray());
						/* Don't need to wait for the .load to complete before closing */
						$(this).dialog("close"); 
					}
				}
		});

	$("#popup_divmap").dialog({
			bgiframe: true, autoOpen: false,
			modal: true, resizable: false,
			draggable: false,
			buttons: { 
				"<?=i18n('Cancel')?>": function() { 
						$(this).dialog("close"); 
					},
				"<?=i18n('Save Mappings')?>": function() { 
						$("#debug").load("<?=$_SERVER['PHP_SELF']?>?action=catdiv_save",
									$('#catdiv_form').serializeArray(), function() {
										popup_upload_load();
									}
						);
						/* Don't need to wait for the .load to complete before closing */
						$(this).dialog("close"); 
					}
				}
		});
});
</script>

<div id="popup_upload" title="Upload Award" style="display: none"></div>
<div id="popup_divmap" title="Edit Mappings" style="display: none"></div>


<?


if(!function_exists('curl_init')) {
	echo error(i18n("CURL Support Missing"));
	echo notice(i18n("Your PHP installation does not support CURL.  You will need to have CURL support added by your system administrator before being able to access external award sources"));
	send_footer();
	exit;
}


/* Fairs first */
$q = mysql_query("SELECT fairs.id, fairs.name, fairs.type, COUNT(award_awards.id) as AWARD_COUNT FROM fairs 
			LEFT JOIN award_awards ON award_awards.award_source_fairs_id=fairs.id
			WHERE award_awards.award_source_fairs_id IS NOT NULL
				AND award_awards.year='{$config['FAIRYEAR']}'
			GROUP BY fairs.id
			ORDER BY fairs.name ");
echo mysql_error();

?>
<h4><?=i18n('Upload all winners to a source')?>:</h4>

<table class="tableview"><thead>
<tr><th><?=i18n("Source Name")?></th>
<th><?=i18n("Number of Awards")?></th>
<th><?=i18n("Winners<br />Assigned")?></th>
<th><?=i18n("Send All")?></th>
</tr></thead>
<?

while($r=mysql_fetch_object($q)) {
	$count = count_winners(-1, $r->id);
?>
	<tr><td><?=$r->name?></td>
	<td align="center"><?=$r->AWARD_COUNT?></td>
	<td align="center"><?=$count?></td>
	<td align="center">
<?
	if($r->type == 'sfiab')
		echo "<a href=\"#\" onClick=\"popup_upload({$r->id},-1)\" >".i18n("Send All")."</a>";
	else
		echo "Not available yet, we're working on it!";
	echo "</td></tr>";
}
?>
</table>
<br />
<br />


<?

$q = mysql_query("SELECT award_awards.id, award_awards.name AS awardname,
			  fairs.name as fairname, award_source_fairs_id, 
			  fairs.type as fairtype, award_awards.external_additional_materials
			FROM award_awards 
			LEFT JOIN fairs ON fairs.id=award_awards.award_source_fairs_id 
			WHERE award_awards.award_source_fairs_id IS NOT NULL
				AND award_awards.year='{$config['FAIRYEAR']}'
			ORDER BY fairs.name, award_awards.name");
echo mysql_error();

?>
<h4><?=i18n('Upload individual winners to a source')?>:</h4>

<table class="tableview"><thead>
<tr><th><?=i18n("Award Name")?></th>
<th><?=i18n("Source Name")?></th>
<th><?=i18n("Winners<br />Assigned")?></th>
<th><?=i18n("Send")?></th>
<th><?=i18n("Additional<br />Info")?></th>
</tr></thead>
<?
while($r=mysql_fetch_object($q)) {
	$count = count_winners($r->id, $r->award_source_fairs_id);
?>	
	<tr><td><?=$r->awardname?></td>
	<td><?=$r->fairname?></td>
	<td align="center"><?=$count?></td>
	<td align="center">
<?
	if($count > 0) 
		$onclick = "popup_upload({$r->award_source_fairs_id},{$r->id});return false;";
	else 
		$onclick = "alert('".i18n('Assign a winner first')."');return false;";
?>	
	<a href="#" onClick="<?=$onclick?>"><?=i18n("send")?></a>
	</td><td>

<?	if($r->external_additional_materials) {
		echo "<a href=\"{$_SERVER['PHP_SELF']}?action=additional_materials&award_awards_id={$r->id}\" target=\"_blank\">".i18n("download")."</a>";
	}
	echo '</td></tr>';
}
?>
</table>
<br />

<div id="award_upload_status"></div>


<?
/*<a href="award_upload.php?action=send<?=$sendurl?>"><?=i18n("Send all awards")?></a> */
send_footer();
?>
