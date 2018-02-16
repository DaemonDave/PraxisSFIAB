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
 if(! ($_SESSION['registration_number'] && $_SESSION['registration_id']))
 {
 	header("Location: register_participants.php");
	exit;
 }

 $q=mysql_query("SELECT registrations.status AS status, registrations.id AS regid, students.id AS studentid, students.firstname FROM registrations,students ".
 	"WHERE students.email='".$_SESSION['email']."' ".
	"AND registrations.num='".$_SESSION['registration_number']."' ". 
	"AND registrations.id='".$_SESSION['registration_id']."' ".
	"AND students.registrations_id=registrations.id ".
	"AND registrations.year=".$config['FAIRYEAR']." ".
	"AND students.year=".$config['FAIRYEAR']);
echo mysql_error();

 if(mysql_num_rows($q)==0)
 {
 	header("Location: register_participants.php?action=logout");
	exit;
 
 }
 $r=mysql_fetch_object($q);
 send_header("Participant Registration - Summary");

 //only display the named greeting if we have their name
 if($r->firstname)
 {
	echo i18n("Hello <b>%1</b>",array($r->firstname));
 	echo "<br />";
 }
 echo "<br />";

 if(registrationFormsReceived())
 {

	 //now select their project number
	 $q=mysql_query("SELECT projectnumber FROM projects WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
	 $projectinfo=mysql_fetch_object($q);

 	if($r->status=="complete")
	{
	 	echo i18n("Congratulations, you are successfully registered for the %1.  No further changes may be made to any of your forms.",array($config['fairname']));
	
	}
	else if($r->status=="paymentpending")
	{
	 	echo i18n("We have received your forms but are missing your registration fee.  You are NOT registered for the fair until your registration fee has been received");
	}

	echo "<br />";
	echo i18n("Your project number is:");
	echo "&nbsp; <span style=\"font-size: 2.0em; font-weight: bold\">$projectinfo->projectnumber</span>";

 }
 else
 {
 	echo i18n("Please use the checklist below to complete your registration.  Click on an item in the table to edit that information.  When you have entered all information, the <b>Status</b> field will change to <b>Complete</b>");
 }
 echo "<br />";
 echo "<br />";

echo "<table><tr><td>";

	 echo "<table class=\"summarytable\">";
	 echo "<tr><th>".i18n("Registration Item")."</th><th>".i18n("Status")."</th></tr>";

	//participant information
	echo "<tr><td>";
	echo "<a href=\"register_participants_students.php\">";
	echo i18n("Student Information");
	echo "</a>";
	echo "</td><td>";
	//check to see if its complete
	$statusstudent=studentStatus();
	echo outputStatus($statusstudent);
	echo "</td></tr>";

	//participant emergency contact information
	echo "<tr><td>";
	if($statusstudent=="complete")
		echo "<a href=\"register_participants_emergencycontact.php\">";
	echo i18n("Emergency Contact Information");
	if($statusstudent=="complete")
		echo "</a>";
	echo "</td><td>";
	//check to see if its complete
	$statusemergencycontact=emergencycontactStatus();
	echo outputStatus($statusemergencycontact);
	echo "</td></tr>";

	//project information - project requires students, so only show the link if the students is complete
	echo "<tr><td>";
	if($statusstudent=="complete")
		echo "<a href=\"register_participants_project.php\">";
	echo i18n("Project Information");
	if($statusstudent=="complete")
		echo "</a>";
	echo "</td><td>";
	//check to see if its complete
	$statusproject=projectStatus();
	echo outputStatus($statusproject);
	echo "</td></tr>";

	if($config['participant_mentor']=="yes")
	{
		//mentor information
		echo "<tr><td>";
		echo "<a href=\"register_participants_mentor.php\">";
		echo i18n("Mentor Information");
		echo "</a>";
		echo "</td><td>";
		//check to see if its complete
		$statusmentor=mentorStatus();
		echo outputStatus($statusmentor);
		echo "</td></tr>";
	}
	else
	{
		//if mentorship isnt required, then assume its complete so the checks below will still work properly
		$statusmentor="complete";
	}

	//safety information
	echo "<tr><td>";
	echo "<a href=\"register_participants_safety.php\">";
	echo i18n("Safety Information");
	echo "</a>";
	echo "</td><td>";
	//check to see if its complete
	$statussafety=safetyStatus();
	echo outputStatus($statussafety);
	echo "</td></tr>";

	if($config['tours_enable']=="yes") {
		echo "<tr><td>";
		echo "<a href=\"register_participants_tours.php\">";
		echo i18n("Tour Selection");
		echo "</a>";
		echo "</td><td>";
		//check to see if its complete
		$statustour=tourStatus();
		echo outputStatus($statustour);
		echo "</td></tr>";
	} else {
		$statustour = "complete";
	}

	//name check
	echo "<tr><td>";
	echo "<a href=\"register_participants_namecheck.php\">";
	echo i18n("Double Check your Name");
	echo "</a>";
	echo "</td><td>";
	//check to see if its complete
	$statusnamecheck=namecheckStatus($_SESSION['registration_id']);
	echo outputStatus($statusnamecheck);
	echo "</td></tr>";

	//FIXME: this should be a global detection so we can use the results elsewhere, especially for all the reports! 
	if(function_exists("pdf_new"))
		$sigfile="register_participants_signature.php";
	else if(file_exists("tcpdf/tcpdf.php"))
		$sigfile="register_participants_signature_tcpdf.php";
	else
		$sigfile="";

	//signature page
	if($statusstudent=="complete" && $statusproject=="complete" && $statusmentor=="complete" && $statussafety=="complete" && $statusemergencycontact=="complete" && $statustour=="complete" && $statusnamecheck=="complete")
		$all_complete = true;
	else 
		$all_complete = false;
	echo "<tr><td>";
	if($all_complete == true)
	{
		if($sigfile)
			echo "<a href=\"$sigfile\">";
		else
			echo error(i18n("No PDF generation library detected"),true);
	}
	echo i18n("Signature Page");
	if($all_complete == true)
		echo "</a>";
	else 
		echo "<br /><font color=\"red\">(".i18n("Available when ALL above sections are \"Complete\"").")</font>";

	echo "</td><td>";
	echo i18n("Print");
	//check to see if its complete
	echo "</td></tr>";

	//received information
	echo "<tr><td>".i18n("Signature Page Received")."</td><td>";
	if(registrationFormsReceived())
		echo outputStatus("complete");
	else
		echo outputStatus("incomplete");

	//check to see if its complete
	echo "</td></tr>";


	 echo "</table>" ;

 echo "</td>";
 echo "<td align=\"left\" width=\"50%\">";
 if($config['specialawardnomination']!="none")
 {
 	echo "<table class=\"summarytable\">";
	echo "<tr><th>".i18n("Special Award Nominations")."</th></tr>";

	$sp_proj_ok = true;
	if($statusstudent =="incomplete" || $statusproject == "incomplete") {
		$sp_proj_ok = false;
	}

	$special_awards_open = false;
	if($config['specialawardnomination_aftersignatures']=="no") {
		$special_awards_open = true;
	} else {
		$special_awards_open = (registrationFormsReceived()) ? true : false;
	}

	if($special_awards_open == true)
	{
		if($config['specialawardnomination']=="date")
		{
			$q=mysql_query("SELECT (NOW()>'".$config['dates']['specawardregopen']."' AND NOW()<'".$config['dates']['specawardregclose']."') AS datecheck");
			$r=mysql_fetch_object($q);
			//this will return 1 if its between the dates, 0 otherwise.
			if($r->datecheck==1)
			{
				echo "<tr><td><a href=\"register_participants_spawards.php\">".i18n("Self-nominate for special awards")."</a></td></tr>";
			}
			else
			{
				echo "<tr><td>".error(i18n("Special award self-nomination is only available from %1 to %2",array($config['dates']['specawardregopen'],$config['dates']['specawardregclose'])),"inline")."</td></tr>";

			}
		}
		else if($config['specialawardnomination']=="registration")
		{
			if($sp_proj_ok == false) {
				echo "<tr><td><font color=\"red\">(".i18n("Available when your Student Information and Project Information is \"Complete\"").")</font></td></tr>";
			} else {
				echo "<tr><td><a href=\"register_participants_spawards.php\">".i18n("Self-nominate for special awards")."</a></td></tr>";
			}
		}

		$q=mysql_query("SELECT * FROM projects WHERE registrations_id='".$_SESSION['registration_id']."' AND year='{$config['FAIRYEAR']}'");
		$project=mysql_fetch_object($q);
		$nominatedawards=getSpecialAwardsNominatedForProject($project->id);
		$num=count($nominatedawards);
		$noawards=getNominatedForNoSpecialAwardsForProject($project->id);
		
		echo "<tr><td>";
		if($num)
		{
			echo happy(i18n("You are nominated for %1 awards",array($num)),"inline");
			echo "</td></tr>";

			echo "<tr><td>";
			$c=1;
			foreach($nominatedawards AS $na)
			{
				echo $c.". ".$na['name']."<br />";
				$c++;
			}

		}
		else if($noawards == true) 
		{
			echo happy(i18n("You are nominated for 0 awards"),"inline");
		}
		else
		{
			echo error(i18n("You are nominated for 0 awards"),"inline");
		}
		echo "</td></tr>";
	}
	else
	{
		echo "<tr><td>".error(i18n("We must receive your signature form before you can nominate yourself for special awards"))."</td></tr>";
	}

	echo "</table>";
 }

 echo "</td></tr>";
 echo "</table>";
	 
 echo "<br /><br />";
 function regfee_line($item, $unit, $qty, $tot, $extra) 
 {
	echo "<tr><td>".i18n($item)."</td>";
	echo "<td>($".sprintf("%.02f", $unit)."</td>";
	echo "<td>* $qty)</td>";
	echo "<td>$".sprintf("%.02f", $tot)."</td>";
	echo "<td><font size=-1>".i18n($extra)."</font></td>";
	echo "</tr>";
 }

 if($config['regfee_show_info'] == 'yes') 
 {
	echo "<h3>".i18n("Registration Fee Information")."</h3>";

	list($regfee, $rfeedata) = computeRegistrationFee($_SESSION['registration_id']);

	$extra_after = "";
	echo "<table>";
	foreach($rfeedata as $rf) {
		$ex = '';
		if($rf['id'] == "tshirt") {
			$ex = "*";
			$extra_after = "* If you do not wish to order a T-Shirt, please select your T-Shirt size as 'None' on the Student Information Page";
		}
		regfee_line($rf['text'],$rf['base'],$rf['num'],$rf['ext'],$ex);
	}
	echo "<tr><td align=right colspan=3>".i18n("Total (including all taxes)")."</td><td><b>$".sprintf("%.02f", $regfee)."</b></td><td></td></tr>";
	echo "</table><br />";
	echo i18n($extra_after);

	echo "<br />";
	echo "<br />";
	echo "<br />";
}

//
/// MODIFIED DRE 2018
//

	
 echo "<h3>".i18n("Registration Instructions")."</h3>";
 // inserting a deadline line 

 $q=mysql_query("SELECT *,UNIX_TIMESTAMP(date) AS udate FROM dates WHERE year='{$config['FAIRYEAR']}' AND name='regclose'");
 while($r=mysql_fetch_object($q))
 {
  if($r->name = 'regclose')
  {
		$d =  format_datetime($r->udate);
		echo "<h3>".i18n("Registration DEADLINE: ")." $d </h3>";		
	}
 }
//
///
//

 //now get the text of special instructions for the bottom of this page:
 output_page_text("register_participants_main_instructions");

  echo "<br /><br />";


 echo "<h3><a href=\"register_participants.php?action=logout\">".i18n("Logout")."</a></h3>";


 send_footer();
?>
