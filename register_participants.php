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

	$q=mysql_query("SELECT (NOW()>'".$config['dates']['regopen']."' AND NOW()<'".$config['dates']['regclose']."') AS datecheck,
			NOW()<'".$config['dates']['regopen']."' AS datecheckbefore,
			NOW()>'".$config['dates']['regclose']."' AS datecheckafter");
	$datecheck=mysql_fetch_object($q);

 if($_POST['action']=="new") 
 {
 	$q=mysql_query("SELECT email,num,id,schools_id FROM registrations WHERE email='".$_SESSION['email']."' AND num='".$_POST['regnum']."' AND year=".$config['FAIRYEAR']);
	if(mysql_num_rows($q)) {
		$r=mysql_fetch_object($q);
		$_SESSION['registration_number']=$r->num;
		$_SESSION['registration_id']=$r->id;
		mysql_query("INSERT INTO students (registrations_id,email,schools_id,year) VALUES ('$r->id','".mysql_escape_string($_SESSION['email'])."','".$r->schools_id."','".$config['FAIRYEAR']."')");
		mysql_query("UPDATE registrations SET status='open' WHERE id='$r->id'");

		header("Location: register_participants_main.php");
		exit;

	}
	else {
 		send_header("Participant Registration");
		echo error(i18n("Invalid registration number (%1) for email address %2",array($_POST['regnum'],$_SESSION['email']),array("registration number","email address")));
		$_POST['action']="login";
	}

 }
 else if($_POST['action']=="continue")
 {
 	if($_POST['email'])
	 	$_SESSION['email']=stripslashes(mysql_escape_string($_POST['email']));

	 $q=mysql_query("SELECT registrations.id AS regid, registrations.num AS regnum, students.id AS studentid, students.firstname FROM registrations,students ".
 	"WHERE students.email='".$_SESSION['email']."' ".
	"AND registrations.num='".intval($_POST['regnum'])."' ". 
	"AND students.registrations_id=registrations.id ".
	"AND registrations.year=".$config['FAIRYEAR']." ".
	"AND students.year=".$config['FAIRYEAR']);

	if(mysql_num_rows($q)) 
	{
		$r=mysql_fetch_object($q);
		$_SESSION['registration_number']=$r->regnum;
		$_SESSION['registration_id']=$r->regid;
		$_SESSION['students_id']=$r->studentid;
		header("Location: register_participants_main.php");
		exit;
	}
	
	else {
		send_header("Participant Registration");
		echo error(i18n("Invalid registration number (%1) for email address %2",array($_POST['regnum'],$_SESSION['email']),array("registration number","email address")));
		$_POST['action']="login";
	}

 }
 else if($_POST['action']=="reveal")
 {
 	if($_POST['email'])
	 	$_SESSION['email']=stripslashes(mysql_escape_string($_POST['email']));

	 $q=mysql_query("SELECT registrations.id AS regid, registrations.num AS regnum, students.id AS studentid, students.firstname FROM registrations,students ".
 	"WHERE students.email='".$_SESSION['email']."' ".
	"AND registrations.num='".intval($_POST['regnum'])."' ". 
	"AND students.registrations_id=registrations.id ".
	"AND registrations.year=".$config['FAIRYEAR']." ".
	"AND students.year=".$config['FAIRYEAR']);

	if(mysql_num_rows($q)) 
	{
		$r=mysql_fetch_object($q);
		$_SESSION['registration_number']=$r->regnum;
		$_SESSION['registration_id']=$r->regid;
		$_SESSION['students_id']=$r->studentid;
		header("Reveal Student's Information");
		$registration_num = $r->regnum;
		$registration_id = $r->regid;
		$registration_student = $r->studentid;

		echo  $registration_num , $registration_id , $registration_student  ;
		exit;
	}
	
	else 
	{
		send_header("ERROR: Reveal Student's Information");
		echo error(i18n("Invalid registration number (%1) for email address %2",array($_POST['regnum'],$_SESSION['email']),array("registration number","email address")));
		$_POST['action']="login";
	}

 } 
 else if($_GET['action']=="resend" && $_SESSION['email']) {
 	//first see if the email matches directly from the registrations table
 	$q=mysql_query("SELECT registrations.num FROM 
				registrations
			WHERE 
				registrations.email='".$_SESSION['email']."' 
				AND registrations.year='".$config['FAIRYEAR']."'");
	if(mysql_num_rows($q))
		$r=mysql_fetch_object($q);
	else {

		//no match from registrations, so lets see if it matches from the students table
		$q=mysql_query("SELECT registrations.num FROM 
					registrations, 
					students 
				WHERE 
					students.email='".$_SESSION['email']."' 
					AND students.registrations_id=registrations.id 
					AND registrations.year='".$config['FAIRYEAR']."'");
		$r=mysql_fetch_object($q);

	}
	// if there exists a registration and student for this year
	if($r) 
	{

	//
	/// DRE 2018 MODIFIED
	//			
		// need a second query to find out the proper email body for merge
		// need to access emails table for standard email body portion
		//
		$z=mysql_query("SELECT * FROM emails WHERE val='register_participants_resend_regnum'");
		
		$zz=mysql_fetch_object($z);		
		if($zz)
		{
			$email_body = $zz->body;
//
/// ADDED by DRE 2018
//
			// create a local file to see variable contents for debugging purposes.
			$date =  date('Y-m-d');
			$now =  time();	
			$to = $r->toemail;
			$ID = $r->num;
			$summary = array( $date, $now, $to, $ID, $email_body  );
			$file = 'mysql-logs.out';
			$data = implode(",", $summary);
			$handle = fopen($file, "a+");
			fwrite($handle, $data);
			fclose($handle);			
			// Participant Registration worked but it uses the old email_send()
			// 1st val is value to extract from MySQL table
			// 2nd val is email address
			// 3rd val is supposed to be Subject - which agglomerates onto FAIRNAME
			// 4th val is now registations.num from MySQL table `registrations`
			/// old version - subject but not text body
			//email_send("register_participants_resend_regnum",$_SESSION['email'],array("FAIRNAME"=>i18n($config['fairname'])),array("REGNUM"=>$r->num,"FAIRNAME"=>i18n($config['fairname'])));
			// insert pro-form values as 1-arrays that get evaluated and imploded into single strings
			email_send("register_participants_resend_regnum",$_SESSION['email'],array("FAIRNAME"=>i18n($config['fairname'])),array("REGNUM"=>$r->num, $email_body, "FAIRNAME"=>i18n($config['fairname']) ));
			//email_send_new("register_participants_resend_regnum",$_SESSION['email'],array(),array("REGNUM"=>$r->num));
				
				send_header("Participant Registration");
				echo notice(i18n("Your registration number has been resent to your email address <b>%1</b>",array($_SESSION['email']),array("email address")));			
		}


	}
	else 
	{
		send_header("Participant Registration");
		echo error(i18n("Could not find a registration for your email address"));
	}
 }
 else if($_GET['action']=="logout") {
 	unset($_SESSION['email']);
	unset($_SESSION['registration_number']);
	unset($_SESSION['registration_id']);
	send_header("Participant Registration");
	echo notice(i18n("You have been successfully logged out"));
 }

 
 //if they've alreayd logged in, and somehow wound back up here, take them back to where they should be
 if($_SESSION['registration_number'] && $_SESSION['registration_id'] && $_SESSION['email']) {
	header("Location: register_participants_main.php");
	exit;

 }

 send_header("Participant Registration");
 
 if($_POST['action']=="login" && ( $_POST['email'] || $_SESSION['email']) ) {
 	if($_POST['email'])
	 	$_SESSION['email']=stripslashes(mysql_escape_string($_POST['email']));

 	echo "<form method=\"post\" action=\"register_participants.php\">";

 	$allownew=true;
	$showform=true;


 	//first, check if they have any registrations waiting to be opened
	$q=mysql_query("SELECT * FROM registrations WHERE email='".$_SESSION['email']."' AND status='new' AND year='".$config['FAIRYEAR']."'");
	if(mysql_num_rows($q)>0) {
		echo i18n("Please enter your <b>registration number</b> that you received in your email, in order to begin your new registration");
		echo "<input type=\"hidden\" name=\"action\" value=\"new\">";
		$allownew=false;
	}
	else {
		//check if they have an already open registration
		$q=mysql_query("SELECT 
					students.email,
					registrations.status,
					registrations.id
				FROM 
					students,
					registrations
				WHERE 
					students.email='".$_SESSION['email']."'
					AND students.year=".$config['FAIRYEAR']."
					AND registrations.year=".$config['FAIRYEAR']."
					AND 
					( 	registrations.status='open'
						OR registrations.status='paymentpending'
						OR registrations.status='complete'
					)
					AND students.registrations_id=registrations.id");
		if(mysql_num_rows($q)>0) {
			$r=mysql_fetch_object($q);
//			print_r($r);
			echo i18n("Please enter your <b>registration number</b> in order to login");
			echo "<input type=\"hidden\" name=\"action\" value=\"continue\">";
			$allownew=false;
			echo "<br />";
		}
		else {
			//they dont have a 'new' and they dont have an 'open/paymentpending/complete' so that means that they want to create a new one... BUT...
			if($config['participant_registration_type']=="invite") {
				$allownew=false;
				$showform=false;

				echo i18n("Participant registration is by invite only.  You can not create a new account.  If you have been invited by your school/region, you need to use the same email address that you were invited with.");
				echo "<br />";
				echo "<br />";
				echo "<a href=\"register_participants.php\">Back to Participant Registration</a>";

			}
			else if($config['participant_registration_type']=="singlepassword") {
				$showsinglepasswordform=true;
				if($_POST['singlepassword']) {
					if($_POST['singlepassword']==$config['participant_registration_singlepassword']) {
						$allownew=true;
						$showform=true;
						$showsinglepasswordform=false;
					}
					else {
						echo error(i18n("Invalid registration password, please try again"));
						$allownew=false;
						$showform=false;
					}
				}

				if($showsinglepasswordform) {
					echo i18n("Participant registration is protected by a password.  You must know the <b>registration password</b> in order to create an account.");
					echo "<br />";
					echo "<br />";
					echo "<input type=\"hidden\" name=\"action\" value=\"login\">";
					echo i18n("Email Address:")." ".$_SESSION['email']."<br />";
					echo i18n("Registration Password:");
					echo "<input type=\"text\" size=\"10\" name=\"singlepassword\">";
					echo "<br />";
					echo "<br />";
					echo "<input type=\"submit\" value=\"Submit\">";
					echo "</form>";
					$allownew=false;
					$showform=false;
				}
			}
			else if($config['participant_registration_type']=="schoolpassword") {
				$showschoolpasswordform=true;
				if($_POST['schoolpassword'] && $_POST['schoolid']) {
					$q=mysql_query("SELECT registration_password FROM schools WHERE id='".$_POST['schoolid']."' AND year='".$config['FAIRYEAR']."'");
					$r=mysql_fetch_object($q);

					if($_POST['schoolpassword']==$r->registration_password) {
						$allownew=true;
						$showform=true;
						$showschoolpasswordform=false;
						$schoolidquery="'".$_POST['schoolid']."'";
					}
					else {
						echo error(i18n("Invalid school registration password, please try again"));
						$allownew=false;
						$showform=false;
					}
				}

				if($showschoolpasswordform) {
					echo i18n("Participant registration is protected by a password for each school.  You must know your <b>school registration password</b> in order to create an account.");
					echo "<br />";
					echo "<br />";
					echo "<input type=\"hidden\" name=\"action\" value=\"login\">";
					echo i18n("Email Address:")." ".$_SESSION['email']."<br />";
					echo i18n("School: ");
					$q=mysql_query("SELECT id,school FROM schools WHERE year='".$config['FAIRYEAR']."' ORDER BY school");
					echo "<select name=\"schoolid\">";
					echo "<option value=\"\">".i18n("Choose your school")."</option>\n";
					while($r=mysql_fetch_object($q))
						echo "<option value=\"$r->id\">$r->school</option>\n";
					echo "</select>";
					echo "<br />";
					echo i18n("School Registration Password: ");
					echo "<input type=\"text\" size=\"10\" name=\"schoolpassword\">";
					echo "<br />";
					echo "<br />";
					echo "<input type=\"submit\" value=\"Submit\">";
					echo "</form>";
					$allownew=false;
					$showform=false;
				}
			}
			else if($config['participant_registration_type']=="open") {
				//thats fine, continue on and create them the account.
			}
			else if($config['participant_registration_type']=="openorinvite") {
				//thats fine too, continue on and create them the account.
			}
			else {
				echo error(i18n("There is an error with the SFIAB configuration.  participant_registration_type is not defined.  Contact the fair organizers to get this fixed."));
				$allownew=false;
				$showform=false;
			}

		}
	}


	if($allownew) {
		if($datecheck->datecheck==0) {
			if($datecheck->datecheckbefore)
				echo error(i18n("Registration is not open yet.  You can not create a new account"));
			else if($datecheck->datecheckafter)
				echo error(i18n("Registration is now closed.  You can not create a new account"));
			$showform=false;
			echo "<A href=\"register_participants.php\">Back to Participant Registration Login Page</a>";

		}
		else {
			//they can only create a new registraiton if they have a valid email address, so lets do a quick ereg check on their email
			if(isEmailAddress($_SESSION['email'])) {
			
				$regnum=0;
				//now create the new registration record, and assign a random/unique registration number to then.
				do {
					//random number between
					//100000 and 999999  (six digit integer)
					$regnum=rand(100000,999999);
					$q=mysql_query("SELECT * FROM registrations WHERE num='$regnum' AND year=".$config['FAIRYEAR']);
				}while(mysql_num_rows($q)>0);

				if(!$schoolidquery) $schoolidquery="null";

				//actually insert it
				mysql_query("INSERT INTO registrations (num,email,start,status,schools_id,year) VALUES (".
						"'$regnum',".
						"'".$_SESSION['email']."',".
						"NOW(),".
						"'new',".
						$schoolidquery.",".
						$config['FAIRYEAR'].
						")");
	//
	/// DRE 2018 MODIFIED - fixed FAIRNAME  problem with this function call
	//			
				email_send( "new_participant", $_SESSION['email'], array("FAIRNAME"=>i18n($config['fairname'])) , array("REGNUM"=>$regnum,"EMAIL"=>$_SESSION['email'], "FAIRNAME"=>i18n($config['fairname'])));

				echo i18n("You have been identified as a new registrant.  An email has been sent to <b>%1</b> which contains your new <b>registration number</b>.  Please check your email to obtain your <b>registration number</b> and then enter it below:",array($_SESSION['email']),array("email address")); 
				echo "<input type=\"hidden\" name=\"action\" value=\"new\">";
			}
			else {
				echo error(i18n("The email address you entered (%1) appears to be invalid.  You must use a proper email address in order to create an account",array($_SESSION['email'])));
				echo "<a href=\"register_participants.php\">".i18n("Return to participant registration")."</a>";
				$showform=false;
			}
		}

	}
	if($showform) 
	{
		echo "<br />";
		echo "<br />";
		echo i18n("Registration Number:");
		echo "<input type=\"text\" size=\"10\" name=\"regnum\">";
		echo "<br />";
		echo "<br />";
		echo "<input type=\"submit\" value=\"Submit\">";
		echo "</form>";
		echo "<br />";
		echo i18n("If you have lost or forgotten your <b>registration number</b>, please <a href=\"register_participants.php?action=resend\">click here to resend</a> it to your email address");
	}
 }
 else 
 {
 	//Lets check the date - if we are AFTER 'regopen' and BEFORE 'regclose' then we can login
	//otherwise, registration is closed - no logins!

	//this will return 1 if its between the dates, 0 otherwise.
	if($datecheck->datecheck==0) {
		if($datecheck->datecheckbefore)
			echo notice(i18n("Registration for the %1 %2 is not open yet.  Registration will open on %3.",array($config['FAIRYEAR'],$config['fairname'],format_datetime($config['dates']['regopen'])),array("fair year","fair name","registration open date")));
		else if($datecheck->datecheckafter) {
			echo notice(i18n("Registration for the %1 %2 is now closed.  Existing registrants can login and view (read only) their information, as well as apply for special awards (if applicable).",array($config['FAIRYEAR'],$config['fairname']),array("fair year","fair name")));
			echo i18n("Please enter your email address to login");
		}
		echo "<br />";
		echo "<br />";
		$buttontext=i18n("Login");
	}
	else 
	{
		if($config['participant_registration_type']=="invite") 
		{
			echo i18n("Registration is by invitation only.  As soon as you are invited by your school or the science fair committee, you will receive a welcoming email with your Registration Number");
			echo "<br />";
			echo "<br />";

			echo i18n("Please enter your email address to:");
			echo "<ul>";
		}
		else 
		{
			echo i18n("Please enter your email address to :");
			echo "<ul>";
			echo "<li>".i18n("Begin a new registration")."</li>";
		}
		echo "<li>".i18n("Continue a previously started registration")."</li>";
		echo "<li>".i18n("Modify an existing registration")."</li>";
		echo "</ul>";
		echo i18n("You must enter a valid email address.  We will be emailing you information which you will need to complete the registration process!");
		echo "<br />";		
		echo i18n("If you use Microsoft Outlook or Hotmail and didn't receive an email, please <a href=\"http://seab-sciencefair.com/mediawiki/index.php/WARNING_TO_MICROSOFT_OUTLOOK_AND_HOTMAIL_USERS\">see this webpage for how to get registration email.</a> ");
		echo "<br />";
		$buttontext=i18n("Begin");
	}

	//only show the email login box if registration is open, or we're past the registration deadline (so they can login and view / apply for special awards).  if we're before the registration deadline then they cant create an account or login anwyays so no point in showing the box
	if(!$datecheck->datecheckbefore) 
	{
	?>
	<form method="post" action="register_participants.php">
	<input type="hidden" name="action" value="login" />
	<?=i18n("Email")?>: <input type="text" name="email" size="30" />
	<input type="submit" value="<?=$buttontext?>" />
	</form>
	<?
	}
 }
 send_footer();
?>
