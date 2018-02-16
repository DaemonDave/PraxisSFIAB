<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005-2006 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005-2006 James Grant <james@lightbox.org>

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
require_once('../register_participants.inc.php');

$auth_type = user_auth_required(array('fair','committee'), 'admin');


$registrations_id=intval($_GET['id']);
$action = $_GET['action'];

/* Extra restrictions for auth_type = fair */
if($auth_type == 'fair') {
	$fairs_id = $_SESSION['fairs_id'];

	if($registrations_id == -1 && ($action=='registration_load' || $action == 'registration_save')) {
		/* we can't check the project it hasn't been created. */
	} else {
		/* Make sure they have permission to laod this student, check
		the master copy of the fairs_id in the project */
		$q=mysql_query("SELECT * FROM projects WHERE 
				registrations_id='$registrations_id' 
				AND year='{$config['FAIRYEAR']}'
				AND fairs_id=$fairs_id");
		if(mysql_num_rows($q) != 1) {
			echo "permission denied.";
			exit;
		} 
		/* Ok, they have permission */
	}
}


switch($action) {
case 'project_load':
	project_load();
	break;
case 'project_regenerate_number':
	/* Save first */
	project_save();

	/* Now generate */
	$q=mysql_query("SELECT id FROM projects WHERE registrations_id='{$registrations_id}' AND year='{$config['FAIRYEAR']}'");
	$i=mysql_fetch_assoc($q);
	$id = $i['id'];

	mysql_query("UPDATE projects SET projectnumber=NULL,projectsort=NULL,
				projectnumber_seq='0',projectsort_seq='0'
				WHERE id='$id'");
	echo mysql_error();
 	list($pn,$ps,$pns,$pss) = generateProjectNumber($registrations_id);
//	print("Generated Project Number [$pn]");
	mysql_query("UPDATE projects SET projectnumber='$pn',projectsort='$ps',
				projectnumber_seq='$pns',projectsort_seq='$pss'
				WHERE id='$id'");
	happy_("Generated and Saved Project Number: $pn");
	break;

case 'project_save':
	project_save();
	break;
default:
	break;
}

exit;

function project_save()
{
	global $registrations_id, $config;

	//first, lets make sure this project really does belong to them
	$q=mysql_query("SELECT * FROM projects WHERE registrations_id='{$registrations_id}' AND year='{$config['FAIRYEAR']}'");
	$projectinfo=mysql_fetch_object($q);
	if(!projectinfo) {
		echo error(i18n("Invalid project to update"));
	}

	$summarywords=preg_split("/[\s,]+/",$_POST['summary']);
	$summarywordcount=count($summarywords);
	if($summarywordcount>$config['participant_project_summary_wordmax'])
		$summarycountok=0;
	else
		$summarycountok=1;

	if($config['participant_project_title_charmax'] && strlen(stripslashes($_POST['title']))>$config['participant_project_title_charmax']) {  //0 for no limit, eg 255 database field limit 
		$title=substr(stripslashes($_POST['title']),0,$config['participant_project_title_charmax']);
		error_("Project title truncated to %1 characters",array($config['participant_project_title_charmax']));
	} else
		$title=stripslashes($_POST['title']);

	mysql_query("UPDATE projects SET ".
			"title='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",$title))."', ".
			"projectdivisions_id='".intval($_POST['projectdivisions_id'])."', ".
			"language='".mysql_escape_string(stripslashes($_POST['language']))."', ".
			"req_table='".mysql_escape_string(stripslashes($_POST['req_table']))."', ".
			"req_electricity='".mysql_escape_string(stripslashes($_POST['req_electricity']))."', ".
			"req_special='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['req_special'])))."', ".
			"summary='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['summary'])))."', ".
			"summarycountok='$summarycountok',".
			"projectsort='".mysql_escape_string(stripslashes($_POST['projectsort']))."'".
			"WHERE id='".intval($_POST['id'])."'");
			echo mysql_error();
	happy_("Project information successfully updated");

	//check if they changed the project number
	if($_POST['projectnumber']!=$projectinfo->projectnumber) {
		//check if hte new one is available
		$q=mysql_query("SELECT * FROM projects WHERE year='".$config['FAIRYEAR']."' AND projectnumber='".$_POST['projectnumber']."'");
		if(mysql_num_rows($q)) {
			error_("Could not change project number.  %1 is already in use",array($_POST['projectnumber']));
		} else {
			mysql_query("UPDATE projects SET
					projectnumber='".$_POST['projectnumber']."'
					WHERE id='".$_POST['id']."'");
			happy_("Project number successfully changed to %1",array($_POST['projectnumber']));
		}
	}
}


function project_load()
{
	global $registrations_id, $config;

	//now lets find out their MAX grade, so we can pre-set the Age Category
	$q=mysql_query("SELECT MAX(grade) AS maxgrade FROM students WHERE registrations_id='".$registrations_id."'");
	$gradeinfo=mysql_fetch_object($q);

	//now lets grab all the age categories, so we can choose one based on the max grade
	$q=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
	while($r=mysql_fetch_object($q)) {
		//save these in an array, just incase we need them later (FIXME: remove this array if we dont need it)
		$agecategories[$r->id]['category']=$r->category;
		$agecategories[$r->id]['mingrade']=$r->mingrade;
		$agecategories[$r->id]['maxgrade']=$r->maxgrade;

		if($gradeinfo->maxgrade >= $r->mingrade && $gradeinfo->maxgrade <= $r->maxgrade)
			$projectcategories_id=$r->id;
	}

	//now select their project info
	$q=mysql_query("SELECT * FROM projects WHERE registrations_id='".$registrations_id."' AND year='".$config['FAIRYEAR']."'");
	//check if it exists, if we didnt find any record, lets insert one
	$projectinfo=mysql_fetch_object($q);

	//make sure that if they changed their grade on the student page, we update their projectcategories_id accordingly
	if($projectcategories_id && $projectinfo->projectcategories_id!=$projectcategories_id) {
		echo notice(i18n("Age category changed, updating to %1",array($agecategories[$projectcategories_id]['category'])));
		mysql_query("UPDATE projects SET projectcategories_id='$projectcategories_id' WHERE id='$projectinfo->id'");
	}

	//output the current status
?>

<script language="javascript" type="text/javascript">
function countwords()
{
	var wordmax=<?=$config['participant_project_summary_wordmax'];?>;
	var summaryobj=document.getElementById('summary');
	var wordcountobj=document.getElementById('wordcount');
	var wordcountmessageobj=document.getElementById('wordcountmessage');

	var wordarray=summaryobj.value.replace(/\s+/g," ").split(" ");
	var wordcount=wordarray.length;

	if(wordcount>wordmax)
		wordcountmessageobj.className="incomplete";
	else
		wordcountmessageobj.className="complete";
		
	wordcountobj.innerHTML=wordcount;
}
</script>
<?

	if(!$projectinfo) {
		echo error(i18n("Invalid project to edit"));
		exit;
	}

?>
	<form id="project_form">
	<input type="hidden" name="id" value="<?=$projectinfo->id?>">
	<table>
	<tr>	<td><?=i18n("Project Title")?>: </td>
		<td><input type="text" name="title" size="50" value="<?=htmlspecialchars($projectinfo->title)?>" /><?=REQUIREDFIELD?>
<?
	if($config['participant_project_title_charmax'])
		echo i18n("(Max %1 characters)",array($config['participant_project_title_charmax']));
?>
		</td>
	</tr><tr>
		<td><?=i18n("Project Number")?>: </td>
		<td><input type="text" name="projectnumber" size="10" value="<?=$projectinfo->projectnumber?>" />
			<input type="button" id="project_regenerate_number" value="<?=i18n("Re-Generate Project Number")?>" />
		</td>
	</tr><tr>
		<td><?=i18n("Project Sort")?>: </td>
		<td><input type="text" name="projectsort" size="10" value="<?=$projectinfo->projectsort?>" /></td>
	</tr><tr>
		<td><?=i18n("Age Category")?>: </td>
		<td><?=i18n($agecategories[$projectcategories_id]['category'])?> (<?=i18n("Grades %1-%2",array($agecategories[$projectcategories_id]['mingrade'],$agecategories[$projectcategories_id]['maxgrade']))?>)</td>
	</tr><tr>
		<td><?=i18n("Division")?>: </td>
		<td>
<?
	//###### Feature Specific - filtering divisions by category
	if($config['filterdivisionbycategory']=="yes"){
		$q=mysql_query("SELECT projectdivisions.* FROM projectdivisions,projectcategoriesdivisions_link WHERE projectdivisions.id=projectdivisions_id AND projectcategories_id=".$projectcategories_id." AND projectdivisions.year='".$config['FAIRYEAR']."' AND projectcategoriesdivisions_link.year='".$config['FAIRYEAR']."' ORDER BY division"); 
		echo mysql_error();
	//###
	} else
		$q=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY division");

	echo "<select name=\"projectdivisions_id\">";
	echo "<option value=\"\">".i18n("Select a division")."</option>\n";
	while($r=mysql_fetch_object($q)) {
		if($r->id == $projectinfo->projectdivisions_id) $sel="selected=\"selected\""; else $sel="";
		echo "<option $sel value=\"$r->id\">".htmlspecialchars(i18n($r->division))."</option>\n";
	}
	echo "</select>".REQUIREDFIELD;

	if($config['usedivisionselector']=="yes") {
	 ?>
		<script language="javascript" type="text/javascript">

		function openDivSelWindow()
		{
			divselwin=window.open('register_participants_project_divisionselector.php','divsel','width=500,height=220,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no')
			if(divselwin.opener==null) divselwin.opener=self;
			return false;
		}
		</script>
	<?
	}

	echo "<br />";
	echo i18n("WARNING!  If you change the division you must manually change the project number too!  It will NOT be assigned a new number automatically");
	echo "</td></tr>";

	echo "<tr><td>".i18n("Language").": </td><td>";
	echo "<select name=\"language\">\n";

	if($projectinfo->language)
		$currentlang=$projectinfo->language;
	else
		$currentlang=$_SESSION['lang'];

	foreach($config['languages'] AS $key=>$val) { 
		if($currentlang==$key) $selected="selected=\"selected\""; else $selected="";

		echo "<option $selected value=\"$key\">$val</option>";
	}
	echo "</select>".REQUIREDFIELD;
	echo "</td></tr>";

	echo "<tr><td>".i18n("Requirements").": </td><td>";
	echo "<table>";

	if($config['participant_project_table']=="no") {
		//if we arent asking them if they want a table or not, then we set it to 'yes' assuming everyone will get a table
		echo " <input type=\"hidden\" name=\"req_table\" value=\"yes\" />";
	} else {
		echo "<tr>";
		echo " <td>".i18n("Table").REQUIREDFIELD."</td>";
		if($projectinfo->req_table=="yes") $check="checked=\"checked\""; else $check="";
		echo " <td><input $check type=\"radio\" name=\"req_table\" value=\"yes\" />Yes</td>";
		echo " <td width=\"20\">&nbsp;</td>";
		if($projectinfo->req_table=="no") $check="checked=\"checked\""; else $check="";
		echo " <td><input $check type=\"radio\" name=\"req_table\" value=\"no\" />No</td>";
		echo "</tr>";
	}

	if($config['participant_project_electricity']=="no")
	{
		//if we arent asking them if they want electricity or not, then we set it to 'yes' assuming everyone will get electricity
		echo " <input type=\"hidden\" name=\"req_electricity\" value=\"yes\" />";
	}
	else
	{
		echo "<tr>";
		echo " <td>".i18n("Electricity").REQUIREDFIELD."</td>";
		if($projectinfo->req_electricity=="yes") $check="checked=\"checked\""; else $check="";
		echo " <td><input $check type=\"radio\" name=\"req_electricity\" value=\"yes\" />Yes</td>";
		echo " <td width=\"20\">&nbsp;</td>";
		if($projectinfo->req_electricity=="no") $check="checked=\"checked\""; else $check="";
		echo " <td><input $check type=\"radio\" name=\"req_electricity\" value=\"no\" />No</td>";
		echo "</tr>";
	}

	echo "<tr>";
	echo " <td>".i18n("Special")."</td>";
	echo " <td colspan=\"3\"><input type=\"text\" name=\"req_special\" value=\"$projectinfo->req_special\" /></td>";
	echo "</tr>";

	echo "</table>";

	echo "</td></tr>";

	echo "<tr><td>".i18n("Summary").": </td><td><textarea onchange='countwords()' onkeypress='countwords()' cols=\"60\" rows=\"12\" id=\"summary\" name=\"summary\">".htmlspecialchars($projectinfo->summary)."</textarea>".REQUIREDFIELD."<br />";

	$summarywords=preg_split("/[\s,]+/",$projectinfo->summary);
	$summarywordcount=count($summarywords);
	if($summarywordcount>$config['participant_project_summary_wordmax'])
		echo "<div id=\"wordcountmessage\" class=\"incomplete\">";
	else
		echo "<div id=\"wordcountmessage\" class=\"complete\">";

	echo "<span id=\"wordcount\">$summarywordcount</span>/";
	echo i18n("%1 words maximum",array($config['participant_project_summary_wordmax']));
	echo "</div>";

?>
	</td></tr>
	</table>
	<input type="button" id="project_save" value="<?=i18n("Save Project Information")?>" />
	</form>
<?
}
?>
