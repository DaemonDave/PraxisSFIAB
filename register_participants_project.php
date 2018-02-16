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
 require("common.inc.php");
 include "register_participants.inc.php";
 
 //authenticate based on email address and registration number from the SESSION
 if(!$_SESSION['email'])
 {
 	header("Location: register_participants.php");
	exit;
 }
 if(!$_SESSION['registration_number'])
 {
 	header("Location: register_participants.php");
	exit;
 }

 $q=mysql_query("SELECT registrations.id AS regid, students.id AS studentid, students.firstname FROM registrations,students ".
 	"WHERE students.email='".$_SESSION['email']."' ".
	"AND registrations.num='".$_SESSION['registration_number']."' ". 
	"AND registrations.id='".$_SESSION['registration_id']."' ".
	"AND students.registrations_id=registrations.id ".
	"AND registrations.year=".$config['FAIRYEAR']." ".
	"AND students.year=".$config['FAIRYEAR']);
echo mysql_error();

 if(mysql_num_rows($q)==0)
 {
 	header("Location: register_participants.php");
	exit;
 
 }
 $authinfo=mysql_fetch_object($q);

 //send the header
 send_header("Participant Registration - Project Information");

 echo "<a href=\"register_participants_main.php\">&lt;&lt; ".i18n("Back to Participant Registration Summary")."</a><br />";
 echo "<br />";

 $studentstatus=studentStatus();
 if($studentstatus!="complete")
 {
	echo error(i18n("Please complete the <a href=\"register_participants_students.php\">Student Information Page</a> first"));
	send_footer();
	exit;
 }


 if($_POST['action']=="save")
 {
	if(registrationFormsReceived())
	{
		echo error(i18n("Cannot make changes to forms once they have been received by the fair"));
	}
	else if(registrationDeadlinePassed())
	{
		echo error(i18n("Cannot make changes to forms after registration deadline"));
	}
	else
	{
		//first, lets make sure this project really does belong to them
		$q=mysql_query("SELECT * FROM projects WHERE id='".$_POST['id']."' AND registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
		if(mysql_num_rows($q)==1)
		{
			$summarywords=preg_split("/[\s,]+/",$_POST['summary']);
			$summarywordcount=count($summarywords);
			if($summarywordcount>$config['participant_project_summary_wordmax'] || $summarywordcount<$config['participant_project_summary_wordmin'])
				$summarycountok=0;
			else
				$summarycountok=1;

			if($config['participant_project_title_charmax'] && strlen(stripslashes($_POST['title']))>$config['participant_project_title_charmax'])  //0 for no limit, eg 255 database field limit
			{
				$title=substr(stripslashes($_POST['title']),0,$config['participant_project_title_charmax']);
				echo error(i18n("Project title truncated to %1 characters",array($config['participant_project_title_charmax'])));
			}
			else
				$title=stripslashes($_POST['title']);

			if($config['participant_short_title_enable'] == 'yes'
			   && $config['participant_short_title_charmax'] 
			   && strlen(stripslashes($_POST['shorttitle']))>$config['participant_short_title_charmax'])  //0 for no limit, eg 255 database field limit
			{
				$shorttitle=substr(stripslashes($_POST['shorttitle']),0,$config['participant_short_title_charmax']);
				echo error(i18n("Short project title truncated to %1 characters",array($config['participant_short_title_charmax'])));
			}
			else
				$shorttitle=stripslashes($_POST['shorttitle']);

			mysql_query("UPDATE projects SET ".
					"title='".mysql_escape_string($title)."', ".
					"shorttitle='".mysql_escape_string($shorttitle)."', ".
					"projectdivisions_id='".$_POST['projectdivisions_id']."', ".
					"language='".mysql_escape_string(stripslashes($_POST['language']))."', ".
					"req_table='".mysql_escape_string(stripslashes($_POST['req_table']))."', ".
					"req_electricity='".mysql_escape_string(stripslashes($_POST['req_electricity']))."', ".
					"req_special='".mysql_escape_string(stripslashes($_POST['req_special']))."', ".
					"summary='".mysql_escape_string(stripslashes($_POST['summary']))."', ".
					"summarycountok='$summarycountok'".
					"WHERE id='".$_POST['id']."'");
					echo mysql_error();
			echo notice(i18n("Project information successfully updated"));
		}
		else
		{
			echo error(i18n("Invalid project to update"));
		}
	}
 }


 //now lets find out their MAX grade, so we can pre-set the Age Category
 $q=mysql_query("SELECT MAX(grade) AS maxgrade FROM students WHERE registrations_id='".$_SESSION['registration_id']."'");
 $gradeinfo=mysql_fetch_object($q);

 //now lets grab all the age categories, so we can choose one based on the max grade
 $q=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
 while($r=mysql_fetch_object($q))
 {
 	//save these in an array, just incase we need them later (FIXME: remove this array if we dont need it)
	$agecategories[$r->id]['category']=$r->category;
	$agecategories[$r->id]['mingrade']=$r->mingrade;
	$agecategories[$r->id]['maxgrade']=$r->maxgrade;

	if($gradeinfo->maxgrade >= $r->mingrade && $gradeinfo->maxgrade <= $r->maxgrade)
	{
		$projectcategories_id=$r->id;
	}
 }
 //now select their project info
 $q=mysql_query("SELECT * FROM projects WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
 //check if it exists, if we didnt find any record, lets insert one
 if(mysql_num_rows($q)==0)
 {
 	mysql_query("INSERT INTO projects (registrations_id,projectcategories_id,year) VALUES ('".$_SESSION['registration_id']."','$projectcategories_id','".$config['FAIRYEAR']."')"); 
	//now query the one we just inserted
 	$q=mysql_query("SELECT * FROM projects WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
 }
 $projectinfo=mysql_fetch_object($q);

 //make sure that if they changed their grade on the student page, we update their projectcategories_id accordingly
 if($projectcategories_id && $projectinfo->projectcategories_id!=$projectcategories_id)
 {
 	echo notice(i18n("Age category changed, updating to %1",array($agecategories[$projectcategories_id]['category'])));
	mysql_query("UPDATE projects SET projectcategories_id='$projectcategories_id' WHERE id='$projectinfo->id'");
 }



//output the current status
$newstatus=projectStatus();
if($newstatus!="complete")
{
	echo error(i18n("Project Information Incomplete"));
}
else if($newstatus=="complete")
{
	echo happy(i18n("Project Information Complete"));

}
?>

<script language="javascript" type="text/javascript">
function countwords()
{
	var wordmax=<?=$config['participant_project_summary_wordmax'];?>;
	var wordmin=<?=$config['participant_project_summary_wordmin'];?>;
	var summaryobj=document.getElementById('summary');
	var wordcountobj=document.getElementById('wordcount');
	var wordcountmessageobj=document.getElementById('wordcountmessage');

	var wordarray=summaryobj.value.replace(/\s+/g," ").split(" ");
	var wordcount=wordarray.length;

	if(wordcount>wordmax || wordcount<wordmin)
		wordcountmessageobj.className="incomplete";
	else
		wordcountmessageobj.className="complete";
		
	wordcountobj.innerHTML=wordcount;
}
</script>
<?


 echo "<form name=\"projectform\" method=\"post\" action=\"register_participants_project.php\">\n";
 echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
 echo "<input type=\"hidden\" name=\"id\" value=\"$projectinfo->id\">\n";
 echo "<table>\n";
 echo "<tr><td>".i18n("Project Title").": </td><td><input type=\"text\" name=\"title\" size=\"50\" value=\"".htmlspecialchars($projectinfo->title)."\" />".REQUIREDFIELD;
 if($config['participant_project_title_charmax'])
 	echo i18n("(Max %1 characters)",array($config['participant_project_title_charmax']));
 echo "</td></tr>\n";
 if($config['participant_short_title_enable'] == 'yes') {
 	 echo "<tr><td>".i18n("Short Project Title").": </td><td><input type=\"text\" name=\"shorttitle\" size=\"50\" value=\"".htmlspecialchars($projectinfo->shorttitle)."\" />".REQUIREDFIELD;
	 if($config['participant_short_title_charmax'])
 		echo i18n("(Max %1 characters)",array($config['participant_short_title_charmax']));
	 echo "</td></tr>\n";
 }
 echo "<tr><td>".i18n("Age Category").": </td><td>";
	echo i18n($agecategories[$projectcategories_id]['category']);
	echo " (".i18n("Grades %1-%2",array($agecategories[$projectcategories_id]['mingrade'],$agecategories[$projectcategories_id]['maxgrade'])).")";
 echo "</td></tr>";
 echo "<tr><td>".i18n("Division").": </td><td>";

//###### Feature Specific - filtering divisions by category
 if($config['filterdivisionbycategory']=="yes"){
	$q=mysql_query("SELECT projectdivisions.* FROM projectdivisions,projectcategoriesdivisions_link WHERE projectdivisions.id=projectdivisions_id AND projectcategories_id=".$projectcategories_id." AND projectdivisions.year='".$config['FAIRYEAR']."' AND projectcategoriesdivisions_link.year='".$config['FAIRYEAR']."' ORDER BY division"); 
	echo mysql_error();
//###
}else
 	$q=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY division");
 echo "<select name=\"projectdivisions_id\">";
 echo "<option value=\"\">".i18n("Select a division")."</option>\n";
 while($r=mysql_fetch_object($q))
 {
 	if($r->id == $projectinfo->projectdivisions_id) $sel="selected=\"selected\""; else $sel="";
	echo "<option $sel value=\"$r->id\">".htmlspecialchars(i18n($r->division))."</option>\n";
	
 }
 echo "</select>".REQUIREDFIELD;
 if($config['usedivisionselector']=="yes")
 {
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

	echo "&nbsp;<a href=\"#\" onClick=\"openDivSelWindow(); return false;\">".i18n("Division Selector")."</a>";	
 }
 echo "</td></tr>";

 echo "<tr><td>".i18n("Language").": </td><td>";
 echo "<select name=\"language\">\n";

 if($projectinfo->language)
 	$currentlang=$projectinfo->language;
 else
	$currentlang=$_SESSION['lang'];

 foreach($config['languages'] AS $key=>$val)
 { 
	if($currentlang==$key) $selected="selected=\"selected\""; else $selected="";

	echo "<option $selected value=\"$key\">$val</option>";
 }
 echo "</select>".REQUIREDFIELD;
 echo " ".i18n("This is the language you wish to be judged in!")."</td></tr>";

 echo "<tr><td>".i18n("Requirements").": </td><td>";
	echo "<table>";

	if($config['participant_project_table']=="no")
	{
		//if we arent asking them if they want a table or not, then we set it to 'yes' assuming everyone will get a table
		echo " <input type=\"hidden\" name=\"req_table\" value=\"yes\" />";
	}
	else
	{
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
 if($config['participant_project_summary_wordmin'] > 0)
 {
 	echo i18n(", %1 words minimum", array($config['participant_project_summary_wordmin']));
 }
 echo "</div>";

 echo "</td></tr>";

 echo "</table>";
 echo "<input type=\"submit\" value=\"".i18n("Save Project Information")."\" />\n";
 echo "</form>";


 send_footer();
?>
