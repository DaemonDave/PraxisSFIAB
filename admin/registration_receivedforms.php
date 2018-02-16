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
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');
 require("../register_participants.inc.php");

 send_header("Input Received Signature Forms",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Participant Registration' => 'admin/registration.php')
				);
 echo "<br />";

$showformatbottom=true;
 if($_POST['action']=="received" && $_POST['registration_number'])
 {
 	$q=mysql_query("SELECT * FROM registrations WHERE num='".$_POST['registration_number']."' AND year='".$config['FAIRYEAR']."'");
	if(mysql_num_rows($q)==1)
	{
		$r=mysql_fetch_object($q);
		$reg_id=$r->id;
		$reg_num=$r->num;
		$reg_status=$r->status;

		if($r->status=='new')
		{
			echo error(i18n("Invalid Registration Status (%1 is New).  Cannot receive an empty form.",array($_POST['registration_number'])));
		}
		else
		{
			//make sure all of the statuses are correct
			$statusstudent=studentStatus($reg_id);
			$statusemergencycontact=emergencycontactStatus($reg_id);
			$statusproject=projectStatus($reg_id);
			if($config['participant_mentor']=="yes")
				$statusmentor=mentorStatus($reg_id);
			else
				$statusmentor="complete";
			$statussafety=safetyStatus($reg_id);
			$statusnamecheck=namecheckStatus($reg_id);

			if(
				$statusstudent == "complete" &&
				$statusemergencycontact == "complete" &&
				$statusproject == "complete" &&
				$statusmentor == "complete" &&
				$statussafety == "complete" &&
				$statusnamecheck == "complete"
			) {
				
				$q=mysql_query("SELECT projects.title,
							projectcategories.category,
							projectdivisions.division
						FROM
							projects,projectcategories,projectdivisions
						WHERE
							projects.registrations_id='$reg_id'
							AND
							projects.projectcategories_id=projectcategories.id
							AND
							projects.projectdivisions_id=projectdivisions.id
						");

echo mysql_Error();
				$projectinfo=mysql_fetch_object($q);
				echo "<table class=\"summarytable\">";
 				echo "<tr><th colspan=\"2\">".i18n("Registration Summary for %1",array($reg_num))."</th></tr>";
				switch($reg_status)
				{
					case "paymentpending": $status_text="Payment Pending"; break;
					case "complete": $status_text="Complete"; break;
					case "open": $status_text="Open"; break;
				}
				echo "<tr><td><b>".i18n("Registration Status")."</b></td><td>$status_text</td></tr>";

				echo "<tr><td><b>".i18n("Registration Number")."</b></td><td>$reg_num</td></tr>";
				echo "<tr><td><b>".i18n("Project Title")."</b></td><td>$projectinfo->title</td></tr>";
				echo "<tr><td><b>".i18n("Category / Division")."</b></td><td>$projectinfo->category / $projectinfo->division</td></tr>";

				$q=mysql_query("SELECT students.firstname,
							students.lastname,
							schools.school
						FROM
							students,schools
						WHERE
							students.registrations_id='$reg_id'
							AND
							students.schools_id=schools.id
						");

				$studnum=1;
				while($studentinfo=mysql_fetch_object($q))
				{
					echo "<tr><td><b>".i18n("School %1",array($studnum))."</b></td><td>$studentinfo->school </td></tr>";

					echo "<tr><td><b>".i18n("Student %1",array($studnum))."</b></td><td>$studentinfo->firstname $studentinfo->lastname </td></tr>";
					$studnum++;
				}

				list($regfee,$regfeedata) = computeRegistrationFee($reg_id);
				echo "<tr><td><b>".i18n("Registration Fee")."</b></td><td>".sprintf("$%.02f", $regfee)."</td></tr>";
				echo "</table>\n";
					echo "<br />";

				if($r->status!='complete')
				{
					echo "<table style=\"margin-left: 30px;\">";
					echo "<tr><td colspan=\"3\">";
					echo i18n("Is this the correct form to register?");
					echo "</td></tr>";
					echo "<tr>";
					echo "<td>";

					echo "<form method=\"post\" action=\"registration_receivedforms.php\">";
					echo "<input type=\"hidden\" name=\"registration_number\" value=\"$reg_num\" />";
					echo "<input type=\"hidden\" name=\"action\" value=\"receivedno\" />";
					echo "<input type=submit value=\"".i18n("No, this is the wrong form")."\" style=\"width: 400px;\"/>";
					echo "</form>";

					if($config['regfee']>0)
					{

						echo "<form method=\"post\" action=\"registration_receivedforms.php\">";
						echo "<input type=\"hidden\" name=\"registration_number\" value=\"$reg_num\" />";
						echo "<input type=\"hidden\" name=\"action\" value=\"receivedyes\" />";
						echo "<input type=submit value=\"".i18n("Yes, right form with registration fee")."\" style=\"width: 400px;\"/>";
						echo "</form>";

						echo "<form method=\"post\" action=\"registration_receivedforms.php\">";
						echo "<input type=\"hidden\" name=\"registration_number\" value=\"$reg_num\" />";
						echo "<input type=\"hidden\" name=\"action\" value=\"receivedyesnocash\" />";
						echo "<input type=submit value=\"".i18n("Yes, right form without registration fee")."\" style=\"width: 400px;\"/>";
						echo "</form>";
					}
					else
					{
						echo "<form method=\"post\" action=\"registration_receivedforms.php\">";
						echo "<input type=\"hidden\" name=\"registration_number\" value=\"$reg_num\" />";
						echo "<input type=\"hidden\" name=\"action\" value=\"receivedyes\" />";
						echo "<input type=submit value=\"".i18n("Yes, this is the right form")."\" style=\"width: 400px;\"/>";
						echo "</form>";


					}
					echo "<br />";

					echo "</td>\n";
					echo "</tr>";
					echo "</table>";
					$showformatbottom=false;
				}
				else
				{
					echo i18n("This form has already been received.  Registration is complete");
					echo "<br />";
					echo "<a href=\"registration_receivedforms.php?action=unregister&registration_number=$reg_num\">".i18n("Click here to unregister this project")."</a>";
					echo "<br />";
					echo "<hr />";
				}


			}
			else
			{
				echo error(i18n("All registration sections are not complete.  Cannot register incomplete form"));
			}
		}
	}
	else
	{
		echo error(i18n("Invalid Registration Number (%1)",array($_POST['registration_number'])));

	}
 	

 }
 else if(($_POST['action']=="receivedyes" || $_POST['action']=="receivedyesnocash") && $_POST['registration_number']) {
 	
	$regnum = intval($_POST['registration_number']);
 	$checkNumQuery=mysql_query("SELECT projectnumber
					FROM projects, registrations 
					WHERE projects.registrations_id = registrations.id 
						AND num='$regnum'
						AND registrations.year='{$config['FAIRYEAR']}'");
	$checkNumResults=mysql_fetch_object($checkNumQuery);
 	$projectnum=$checkNumResults->projectnumber;

	$q=mysql_query("SELECT id FROM registrations WHERE num='$regnum' AND year='{$config['FAIRYEAR']}'");
	$r=mysql_fetch_object($q);
	$reg_id = $r->id;

 	if($projectnum == null)
 	{
	 	list($projectnumber,$ps,$pns,$pss) = generateProjectNumber($reg_id);
		mysql_query("UPDATE projects SET projectnumber='$projectnumber',
				projectsort='$ps',projectnumber_seq='$pns',projectsort_seq='$pss'
				WHERE registrations_id='$reg_id' AND year='{$config['FAIRYEAR']}'");
		echo happy(i18n("Assigned Project Number: %1",array($projectnumber)));
 	}
	else
		$projectnumber=$projectnum;

	//get all students with this registration number
	$recipients=getEmailRecipientsForRegistration($reg_id);

 	if($_POST['action']=="receivedyes")
 	{
 		//actually set it to 'complete'
		mysql_query("UPDATE registrations SET status='complete' WHERE num='$regnum' AND year='{$config['FAIRYEAR']}'");

		foreach($recipients AS $recip) {
			$to=$recip['to'];
			$subsub=array();
			$subbod=array(
				"TO"=>$recip['to'],
				"EMAIL"=>$recip['email'],
				"FIRSTNAME"=>$recip['firstname'],
				"LASTNAME"=>$recip['lastname'],
				"NAME"=>$recip['firstname']." ".$recip['lastname'],
				"REGNUM"=>$regnum,
				"PROJECTNUMBER"=>$projectnumber,
				);
			email_send("register_participants_received",$to,$subsub,$subbod);
		}

	 	echo happy(i18n("Registration of form %1 successfully completed",array($regnum)));
 	}
 	else if($_POST['action']=="receivedyesnocash")
 	{
 		//actually set it to 'paymentpending'
		mysql_query("UPDATE registrations SET status='paymentpending' WHERE num='$regnum' AND year='{$config['FAIRYEAR']}'");

		foreach($recipients AS $recip) {
			$to=$recip['to'];
			$subsub=array();
			$subbod=array(
				"TO"=>$recip['to'],
				"EMAIL"=>$recip['email'],
				"FIRSTNAME"=>$recip['firstname'],
				"LASTNAME"=>$recip['lastname'],
				"NAME"=>$recip['firstname']." ".$recip['lastname'],
				"REGNUM"=>$regnum,
				"PROJECTNUMBER"=>$projectnumber,
				);

			email_send("register_participants_paymentpending",$to,$subsub,$subbod);
		}
 		echo happy(i18n("Registration of form %1 marked as payment pending",array($regnum)));
 	}
 }
 else if($_POST['action']=="receivedno" && $_POST['registration_number'])
 {
 	echo notice(i18n("Registration of form %1 cancelled",array($_POST['registration_number'])));

 }
 else if($_GET['action']=="unregister" && $_GET['registration_number']) {
 	$reg_num=intval(trim($_GET['registration_number']));
 	$q=mysql_query("SELECT registrations.id AS reg_id, projects.id AS proj_id FROM projects,registrations WHERE projects.registrations_id=registrations.id AND registrations.year='{$config['FAIRYEAR']}' AND registrations.num='$reg_num'");
	$r=mysql_fetch_object($q);
 	mysql_query("UPDATE projects SET projectnumber=null, projectsort=null, projectnumber_seq=0, projectsort_seq=0 WHERE id='$r->proj_id' AND year='{$config['FAIRYEAR']}'");
 	mysql_query("UPDATE registrations SET status='open' WHERE id='$r->reg_id' AND year='{$config['FAIRYEAR']}'");
	echo happy(i18n("Successfully unregistered project"));
 }


 if($showformatbottom)
 {
	 echo "<form id=\"inputform\" method=\"post\" action=\"registration_receivedforms.php\">";
	 echo "<input type=\"hidden\" name=\"action\" value=\"received\" />";
	 echo i18n("Enter the registration number from the signature form: ")."<br />";
	 echo "<input id=\"registration_number\" type=\"text\" size=\"15\" name=\"registration_number\" />";
	 echo "<input type=\"submit\" value=\"".i18n("Lookup Registration Number")."\" />";
	 echo "</form>";
	 ?>
	 <script type="text/javascript">
	 document.forms.inputform.registration_number.focus();
	 </script>
	 <?
 }

 send_footer();
?>
