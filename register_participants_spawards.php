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
 include "projects.inc.php";
 
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

 $q=mysql_query("SELECT * FROM projects WHERE registrations_id='".$_SESSION['registration_id']."'");
 $project=mysql_fetch_object($q);

 //send the header
 send_header("Participant Registration - Self-Nomination for Special Awards");
 ?>
<script language="javascript" type="text/javascript">
function checkboxclicked(b)
{
	max=<?=$config['maxspecialawardsperproject'];?>;
	num=0;
	df=document.forms["specialawards"];
	for (i=0; i<df.elements.length; i++) {
		if (df[i].type=="checkbox" && df[i].name=="spaward[]") {
			if (df[i].checked==true) {
				num++;
			}
		}
	}
	if(num>max)
	{
		b.checked=false;
		alert('<?=i18n("You can only self-nominate for up to %1 special awards",array($config['maxspecialawardsperproject']))?>');
	}

}
</script>

 <?

 echo "<a href=\"register_participants_main.php\">&lt;&lt; ".i18n("Back to Participant Registration Summary")."</a><br />";
 echo "<br />";

 if($config['specialawardnomination']=="date") {
	echo notice(i18n("Special award self-nomination is only available from %1 to %2.  Please make sure you complete your nominations between these dates.", array($config['dates']['specawardregopen'],$config['dates']['specawardregclose'])));
	$q=mysql_query("SELECT (NOW()>'".$config['dates']['specawardregopen']."' AND NOW()<'".$config['dates']['specawardregclose']."') AS datecheck");
	$r=mysql_fetch_object($q);
	//this will return 1 if its between the dates, 0 otherwise.
	if($r->datecheck==1)
		$readonly=false;
	else
		$readonly=true;
 } else {
 	/* Never make the awards readonly, when registration closes, so do the awards */
 	$readonly = false;
}
 echo notice(i18n("You may apply to a maximum of %1 special awards.",array($config['maxspecialawardsperproject'])));


 if($_POST['action']=="save")
 {
	//this will return 1 if its between the dates, 0 otherwise.
	if(!$readonly)
	{
		$splist = array();
		$noawards = false;
		if(is_array($_POST['spaward'])) $splist = $_POST['spaward'];

		/* If all they've selected is they don't want to self nominate, then erase all other selections */
		if(in_array(-1, $splist)) {
			$splist = array(-1);
			$noawards = true;
		}
		
		$num=count($splist);
		if($num>$config['maxspecialawardsperproject'])
		{
			echo error(i18n("You can only apply to %1 special awards.  You have selected %2",array($config['maxspecialawardsperproject'],$num)));
		}
		else
		{
			mysql_query("DELETE FROM project_specialawards_link WHERE projects_id='$project->id' AND year='".$config['FAIRYEAR']."'");

			foreach($splist AS $spaward)
			{
				$s = ($spaward == -1) ? "NULL" : "'$spaward'";
				mysql_query("INSERT INTO project_specialawards_link (award_awards_id,projects_id,year) VALUES (".
						"$s, ".
						"'$project->id', ".
						"'".$config['FAIRYEAR']."')");
						echo mysql_error();
			}
			if($num) {
				if($noawards == true)
					echo happy(i18n("Successfully registered for no special awards"));
				else 
					echo happy(i18n("Successfully registered for %1 special awards",array($num)));
			}
		}
	}
	else
	{
 		echo error(i18n("Special award self-nomination is only available from %1 to %2",array($config['dates']['specawardregopen'],$config['dates']['specawardregclose'])));

	}
 }

//output the current status
$newstatus=spawardStatus();
if($newstatus!="complete")
{
	echo error(i18n("Special Awards Self-Nomination Incomplete"));
}
else if($newstatus=="complete")
{
	echo happy(i18n("Special Awards Self-Nomination Complete"));
}

 echo "<form name=\"specialawards\" method=\"post\" action=\"register_participants_spawards.php\">\n";
 echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
 echo "<table>\n";

 $eligibleawards=getSpecialAwardsEligibleForProject($project->id);
 $nominatedawards=getSpecialAwardsNominatedForProject($project->id);
 
 $eligibleawards = array_merge(array(array(	'id'=> -1,
 					'name' => "I do not wish to self-nominate for any special awards",
					'criteria' => "Select this option if you do not wish to self-nominate you project for any special awards.<hr>",
					'self_nominate' => 'yes')),
				$eligibleawards);
 /* See if they have the -1 award selected */
 $noawards = getNominatedForNoSpecialAwardsForProject($project->id);
 if($noawards == true) {
	 $nominatedawards = array_merge(array(array('id' => '-1')), $nominatedawards);
 }

/*
 echo "eligible awards <br>";
 echo nl2br(print_r($eligibleawards,true));
 echo "nominated awards <br>";
 echo nl2br(print_r($nominatedawards,true));
 */

 $nominatedawards_list=array();
 foreach($nominatedawards AS $naward)
 {
	$nominatedawards_list[]=$naward['id'];
 }

 echo "<table>";
 foreach($eligibleawards AS $eaward)
 {
 	if($eaward['self_nominate'] == 'no') continue;
 	echo "<tr><td rowspan=\"2\">";
	if(in_array($eaward['id'],$nominatedawards_list)) $ch="checked=\"checked\""; else $ch="";
	echo "<input onclick=\"checkboxclicked(this)\" $ch type=\"checkbox\" name=\"spaward[]\" value=\"".$eaward['id']."\" />";
	echo "</td><td>";
	echo "<b>".$eaward['name']."</b>";
	echo "</td></tr>";
	echo "<tr><td>";
	echo $eaward['criteria'];
	if($eaward['id'] != -1) echo '<br /><br />';
	echo "</td></tr>";
 }
 echo "</table>";
 if(!$readonly)
 echo "<input type=\"submit\" value=\"".i18n("Save Special Award Nominations")."\" />\n";
 echo "</form>";


 send_footer();
?>
