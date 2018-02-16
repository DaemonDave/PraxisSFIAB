<?
include "common.inc.php";

if($_SESSION['schoolid'] && $_SESSION['schoolaccesscode'])
{
	send_header("School Participant Invitations");

	echo "<a href=\"schoolaccess.php\">&lt;&lt; ".i18n("Return to school access main page")."</a><br />";
	echo "<br />";
	$q=mysql_query("SELECT * FROM schools WHERE id='".$_SESSION['schoolid']."' AND accesscode='".$_SESSION['schoolaccesscode']."' AND year='".$config['FAIRYEAR']."'");
	echo mysql_error();
	$school=mysql_fetch_object($q);
	if($school)
	{
		if($config['participant_registration_type']=="invite" || $config['participant_registration_type']=="openorinvite" )
		{
			if($_POST['action']=="invite")
			{
				if($_POST['firstname'] && $_POST['lastname'] && $_POST['email'] && $_POST['grade'])
				{
					//make sure they arent already invited!
					$q=mysql_query("SELECT firstname, lastname FROM students WHERE year='".$config['FAIRYEAR']."' AND email='".$_POST['email']."'");
					if(mysql_num_rows($q))
					{
						echo error(i18n("That students email address has already been invited"));
					}
					else
					{

						$regnum=0;
						//now create the new registration record, and assign a random/unique registration number to then.
						do
						{
							//random number between
							//100000 and 999999  (six digit integer)
							$regnum=rand(100000,999999);
							$q=mysql_query("SELECT * FROM registrations WHERE num='$regnum' AND year=".$config['FAIRYEAR']);
						}while(mysql_num_rows($q)>0);

						//actually insert it
						mysql_query("INSERT INTO registrations (num,email,emailcontact,start,status,year) VALUES (".
								"'$regnum',".
								"'".$_POST['email']."',".
								"'".$_POST['emailcontact']."',".
								"NOW(),".
								"'open',".
								$config['FAIRYEAR'].
								")");
						$regid=mysql_insert_id();

						mysql_query("INSERT INTO students (registrations_id,email,firstname,lastname,schools_id,grade,year) VALUES (
								'$regid',
								'".mysql_escape_string($_POST['email'])."',
								'".mysql_escape_string($_POST['firstname'])."',
								'".mysql_escape_string($_POST['lastname'])."',
								'".mysql_escape_string($_SESSION['schoolid'])."',
								'".mysql_escape_string($_POST['grade'])."',
								'".$config['FAIRYEAR']."')");
//
/// DRE 2018 MODIFIED - fixed FAIRNAME  problem with this function call
//	
						email_send("new_participant",$_POST['email'],array("FAIRNAME"=>i18n($config['fairname'])),array("REGNUM"=>$regnum, "EMAIL"=>$_POST['email'], "FAIRNAME"=>i18n($config['fairname']) ) );
						if($_POST['emailcontact'])
							email_send("new_participant",$_POST['emailcontact'],array("FAIRNAME"=>i18n($config['fairname'])),array("REGNUM"=>$regnum, "EMAIL"=>$_POST['email']));
						echo happy(i18n("The participant has been successfully invited"));
					}
				}
				else
					echo error(i18n("All fields are required for invitations"));
			}

			if($_GET['action']=="uninvite")
			{
				//first, make sure that this is really their student, and it sfor this year.
				$q=mysql_query("SELECT * FROM students WHERE id='".$_GET['uninvite']."' AND year='".$config['FAIRYEAR']."' AND schools_id='".$_SESSION['schoolid']."'");
				if(mysql_num_rows($q))
				{
					$r=mysql_fetch_object($q);
					$registrations_id=$r->registrations_id;
					if($registrations_id) //just to be safe!
					{
						mysql_query("DELETE FROM students WHERE registrations_id='$registrations_id'");
						mysql_query("DELETE FROM projects WHERE registrations_id='$registrations_id'");
						mysql_query("DELETE FROM mentors WHERE registrations_id='$registrations_id'");
						mysql_query("DELETE FROM safety WHERE registrations_id='$registrations_id'");
						mysql_query("DELETE FROM emergencycontact WHERE registrations_id='$registrations_id'");
						mysql_query("DELETE FROM registrations WHERE id='$registrations_id'");

						echo happy(i18n("Student successfully uninvited"));
					}
				}
				else
					echo error(i18n("Invalid student to uninvite"));
			}


			$q=mysql_query("SELECT (NOW()>'".$config['dates']['regopen']."' AND NOW()<'".$config['dates']['regclose']."') AS datecheck");
			$datecheck=mysql_fetch_object($q);


			$q=mysql_query("SELECT 	students.*,
						registrations.num,
						registrations.emailcontact
					FROM 
						students,
						registrations 
					WHERE 
						students.schools_id='".$school->id."' 
						AND students.year='".$config['FAIRYEAR']."' 
						AND students.registrations_id=registrations.id 
					ORDER BY 
						lastname,
						firstname");
			$currentinvited=mysql_num_rows($q);

			if($datecheck!=0)
			{
				echo i18n("In order for your school's students to register for the fair, you will need to invite them to register.  Simply enter their email address below to invite them to register.  <b>Important</b>: for group projects, only add one of the participants, that participant will then add the other group member(s) to the project");
				echo "<br />";
				echo "<br />";
				$okaygrades=array();
				if($config['participant_registration_type']=="invite")
				{
					if($school->projectlimitper=="total")
					{
						if($school->projectlimit)
						{
							echo i18n("You have invited %1 of %2 total projects for your school",array($currentinvited,$school->projectlimit));
							if($currenteinvited<$school->projectlimit)
							{
								for($a=$config['mingrade'];$a<=$config['maxgrade'];$a++)
									$okaygrades[]=$a;
							}
						}
						else
						{
							echo i18n("You have invited %1 project(s) for your school",array($currentinvited,$school->projectlimit));
							for($a=$config['mingrade'];$a<=$config['maxgrade'];$a++)
								$okaygrades[]=$a;
							
						}
					}
					else if($school->projectlimitper=="agecategory")
					{
						echo "<br />";
						$catq=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
						while($catr=mysql_fetch_object($catq))
						{

							$q2=mysql_query("SELECT COUNT(students.id) AS num
									FROM 
										students,
										registrations 
									WHERE 
										students.schools_id='".$school->id."' 
										AND students.grade>='$catr->mingrade'
										AND students.grade<='$catr->maxgrade'
										AND students.year='".$config['FAIRYEAR']."' 
										AND students.registrations_id=registrations.id 
									");
									echo mysql_error();
							$r2=mysql_fetch_object($q2);
							$currentinvited=$r2->num;

							if($currentinvited<$school->projectlimit || $school->projectlimit==0)
							{
								for($a=$catr->mingrade;$a<=$catr->maxgrade;$a++)
									$okaygrades[]=$a;
							}

							echo i18n("You have invited %1 of %2 total projects for for the %3 age category",array($currentinvited,$school->projectlimit,i18n($catr->category)));
							echo "<br />";

						}
					}
					else
					{
						//hmm projectlimitper has not been set
						//so we have no limits, anyone can register or they can add as many as they want.
						for($x=$config['mingrade']; $x<=$config['maxgrade']; $x++)
							$okaygrades[]=$x;
					}
				}
				else
				{
				// this could be an else if $config['participant_registration_type']=="openorinvite" )
				//because openorinvite is the only other option

					//so we have no limits, anyone can register or they can add as many as they want.
					//you cannot enforce limits when the system is 'open' because anyone can choose any school
					//and if its openorinvite then whatever happens in the inviter still morepeople can be added
					//by themselves, so there's no point in having limits.
					for($x=$config['mingrade']; $x<=$config['maxgrade']; $x++)
						$okaygrades[]=$x;

				}
				echo "<br />";

				if(count($okaygrades))
				{

					echo "<form method=POST action=\"schoolinvite.php\">";
					echo "<input type=hidden name=action value=\"invite\">";

					echo "<table>";
					echo "<tr><td><nobr>".i18n("Student Email Address")."</nobr></td><td><input type=\"text\" name=\"email\" /></td><td>".i18n("Or unique username for student")."</td></tr>";
					echo "<tr><td><nobr>".i18n("Contact Email Address")."</nobr></td><td><input type=\"text\" name=\"emailcontact\" /></td><td>".i18n("Any emails that would normally go to the student, will also be sent to this address")."</td></tr>";
					echo "<tr><td><nobr>".i18n("Student First Name")."</nobr></td><td colspan=\"2\"><input type=\"text\" name=\"firstname\" /></td></tr>";
					echo "<tr><td><nobr>".i18n("Student Last Name")."</nobr></td><td colspan=\"2\"><input type=\"text\" name=\"lastname\" /></td></tr>";
					echo "<tr><td><nobr>".i18n("Grade")."</nobr></td><td colspan=\"2\">";

					echo "<select name=\"grade\">\n";
					echo "<option value=\"\">".i18n("Select Grade")."</option>\n";
	//				for($gr=$config['mingrade'];$gr<=$config['maxgrade'];$gr++)
					foreach($okaygrades AS $gr)
					{
						echo "<option value=\"$gr\">$gr</option>\n";
					}

					echo "</td></tr>";

					echo "</table>";
					echo "<input type=\"submit\" value=\"Invite Participant\">";
					echo "</form>";
				}
				else
				{
					echo notice(i18n("You have invited the maximum number of participants for your school"));

				}
			}
			echo "<br />";

			echo "<h4>".i18n("Invited participants from your school")."</h4>";
			if(mysql_num_rows($q))
			{
			echo "<table class=\"summarytable\">";
			echo "<tr><th>".i18n("Last Name")."</th><th>".i18n("First Name")."</th>";
			echo "<th>".i18n("Email Address")."</th>";
			echo "<th>".i18n("Grade")."</th>";
			echo "<th>".i18n("Registration Number")."</th>";
			echo "<th colspan=\"2\">".i18n("Actions")."</th></tr>";
			while($r=mysql_fetch_object($q))
			{
				echo "<tr><td>$r->lastname</td><td>$r->firstname</td>";
				echo "<td>$r->email";
				if($r->emailcontact)
					echo " / $r->emailcontact";
				echo "</td>";
				echo "<td align=\"center\">$r->grade</td>";
				echo "<td align=\"center\">$r->num</td>";
				echo "<td align=\"center\">";
				echo "<form target=\"_blank\" method=\"post\" action=\"register_participants.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"continue\">";
				echo "<input type=\"hidden\" name=\"email\" value=\"$r->email\">";
				echo "<input type=\"hidden\" name=\"regnum\" value=\"$r->num\">";
				echo "<input type=\"submit\" value=\"".i18n("Login")."\">";
				echo "</form>";
				echo "</td><td>";
				echo "<a onclick=\"return confirmClick('Are you sure you want to uninvite this student?')\" href=\"schoolinvite.php?action=uninvite&uninvite=$r->id\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
				echo "</td>";
				echo "</tr>";

			}
			echo "</table>";
			}
			else
				echo i18n("You have not yet invited any participants from your school");

		}
	}
	else
	{
		echo error(i18n("Invalid School ID or Access Code"));
		echo "<br />";
		echo "<a href=\"schoolaccess.php\">".i18n("Perhaps you should login first")."</a>";
	}
	send_footer();
}
else
{
	header("Location: schoolaccess.php");
	exit;
}

?>
