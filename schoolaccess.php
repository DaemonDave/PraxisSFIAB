<?
require_once('common.inc.php');
require_once('user.inc.php');

if($_POST['schoolid'] && $_POST['accesscode'])
{
	$q=mysql_query("SELECT * FROM schools WHERE id='".$_POST['schoolid']."' AND accesscode='".$_POST['accesscode']."' AND year='".$config['FAIRYEAR']."'");
	if(mysql_num_rows($q)==1)
	{
		$_SESSION['schoolid']=$_POST['schoolid'];
		$_SESSION['schoolaccesscode']=$_POST['accesscode'];
		mysql_query("UPDATE schools SET lastlogin=NOW() WHERE id='".$_POST['schoolid']."'");

	}
	else
		$errormsg="Invalid School ID or Access Code";
}

if($_GET['action']=="logout")
{
	unset($_SESSION['schoolid']);
	unset($_SESSION['schoolaccesscode']);
	$happymsg=i18n("You have been logged out from the school access page");
}
send_header("School Access");



if($_SESSION['schoolid'] && $_SESSION['schoolaccesscode'])
{
	$q=mysql_query("SELECT * FROM schools WHERE id='".$_SESSION['schoolid']."' AND accesscode='".$_SESSION['schoolaccesscode']."' AND year='".$config['FAIRYEAR']."'");
	echo mysql_error();
	$school=mysql_fetch_object($q);
	if($school) {
		if($_POST['action']=="save") {

			/* Get info about science head */
			$sciencehead_update = '';
			list($first, $last) = split(' ', $_POST['sciencehead'], 2);
			$em = $_POST['scienceheademail'];
			if($em == '' && ($first != '' || $last != '')) $em = "*$first$last".user_generate_password();
			/* Load existing record, or create new if there's something
			* to insert */
			if($school->sciencehead_uid > 0)
				$sh = user_load_by_uid($school->sciencehead_uid);
			else if($em != '')  {
				$sh = user_create('teacher', $em);
				$sciencehead_update = "sciencehead_uid='{$sh['uid']}',";
			} else
				$sh = false;

			/* If we have a record, either delete it or update it */
			if(is_array($sh)) {
				if($em == '') {
					user_purge($sh, 'teacher');
					$sciencehead_update = 'sciencehead_uid=NULL,';
				} else {
					$sh['firstname'] = $first;
					$sh['lastname'] = $last;
					$sh['phonework'] = $_POST['scienceheadphone'];
					$sh['email'] = $em;
					$sh['username'] = $em;
					user_save($sh);
				}
			}


			mysql_query("UPDATE schools SET
				school='".mysql_escape_string(stripslashes($_POST['school']))."',
				address='".mysql_escape_string(stripslashes($_POST['address']))."',
				city='".mysql_escape_string(stripslashes($_POST['city']))."',
				province_code='".mysql_escape_string(stripslashes($_POST['province_code']))."',
				postalcode='".mysql_escape_string(stripslashes($_POST['postalcode']))."',
				phone='".mysql_escape_string(stripslashes($_POST['phone']))."',
				$sciencehead_update
				fax='".mysql_escape_string(stripslashes($_POST['fax']))."'
				WHERE id='$school->id'");

			echo mysql_error();
				if(mysql_error())
					echo error(i18n("An Error occured trying to save the school information"));
				else
					echo happy(i18n("School information successfully updated"));

				//and reselect it
				$q=mysql_query("SELECT * FROM schools WHERE id='".$_SESSION['schoolid']."' AND accesscode='".$_SESSION['schoolaccesscode']."' AND year='".$config['FAIRYEAR']."'");
				echo mysql_error();
				$school=mysql_fetch_object($q);
		}

/*
		if($_POST['action']=="numbers")
		{
			mysql_query("UPDATE schools SET
						junior='".$_POST['junior']."',
						intermediate='".$_POST['intermediate']."',
						senior='".$_POST['senior']."'
						WHERE id='$school->id'");

				echo mysql_error();

				$q=mysql_query("SELECT * FROM schools WHERE id='".$_SESSION['schoolid']."' AND accesscode='".$_SESSION['schoolaccesscode']."'");
				echo "<font color=blue><b>Participation Information Successfully Updated</b></font><br>\n";
				$school=mysql_fetch_object($q);

		}
		*/
		if($school->sciencehead_uid > 0) 
			$sh = user_load_by_uid($school->sciencehead_uid);
		else
			$sh = array();
		$sh_email = ($sh['email'] != '' && $sh['email'][0] != '*') ? $sh['email'] : '';

		if($_POST['action']=="feedback")
		{
			$body="";
			$body.=date("r")."\n";
			$body.=$_SERVER['REMOTE_ADDR']." (".$_SERVER['REMOTE_HOST'].")\n";
			$body.="School ID: $school->id\n";
			$body.="School Name: $school->school\n";
			if($sh['name']) $body.="Science Teacher: {$sh['name']}\n";
			if($sh['phonework']) $body.="Science Teacher Phone: {$sh['phonework']}\n";
			if($sh_email) $body.="Science Teacher Email: $sh_email\n";
			$body.="\nFeedback:\n".stripslashes($_POST['feedbacktext'])."\n";
			$returnEmailAddress = $sh_email;
			mail($config['fairmanageremail'],"School Feedback",$body,"From: ". $returnEmailAddress."\nReply-To: ".$returnEmailAddress."\nReturn-Path: ".$returnEmailAddress);
			echo happy(i18n("Your feedback has been sent"));
		}

		echo "<h3>$school->school</h3>";
		echo "<h4>".i18n("School Information")."</h4>";
		echo i18n("Please make sure your school contact information is correct, make any necessary changes:");
		echo "<form method=POST action=\"schoolaccess.php\">";
		echo "<input type=hidden name=action value=\"save\">";
		echo "<table border=0 cellspacing=0 cellpadding=3>";
		echo "<tr><td>".i18n("School Name")."</td><td><input value=\"$school->school\" type=text name=school size=40></td></tr>";
//			echo "<tr><td>Registration Password</td><td><input value=\"$school->registration_password\" type=text name=\"registration_password\" size=\"20\"></td></tr>";
		echo "<tr><td>".i18n("Address")."</td><td><input value=\"$school->address\" type=text name=address size=40></td></tr>";
		echo "<tr><td>".i18n("City")."</td><td><input value=\"$school->city\" type=text name=city size=30></td></tr>";
		echo "<tr><td>".i18n($config['provincestate'])."</td><td>";
		emit_province_selector("province_code",$school->province_code);
		echo "</td></tr>\n";
		echo "<tr><td>".i18n($config['postalzip'])."</td><td><input value=\"$school->postalcode\"  type=text name=postalcode size=10></td></tr>";
		echo "<tr><td>".i18n("Phone Number")."</td><td><input value=\"$school->phone\" type=text name=phone size=30></td></tr>";
		echo "<tr><td>".i18n("Fax Number")."</td><td><input value=\"$school->fax\" type=text name=fax size=30></td></tr>";
		
		echo "<tr><td>".i18n("Science Teacher")."</td><td><input value=\"{$sh['name']}\" type=text name=sciencehead size=40></td></tr>";
		echo "<tr><td>".i18n("Science Teacher Email")."</td><td><input value=\"$sh_email\" type=text name=scienceheademail size=40></td></tr>";
		echo "<tr><td>".i18n("Science Teacher Phone")."<br><font size=1>(".i18n("If different than above").")</font></td><td><input value=\"{$sh['phonework']}\" type=text name=scienceheadphone size=30></td></tr>";
		echo "</table>";
		echo "<input type=submit value=\"".i18n("Save Changes")."\">";
		echo "</form>";
		echo "<br>";

		if($config['participant_registration_type']=="schoolpassword")
		{
			echo "<h4>".i18n("Participant Registration Password")."</h4>";

			echo i18n("In order for your school's students to register for the fair, they will need to know your specific school registration password");
			echo "<br />";
			echo "<br />";
			echo i18n("Registration Password: <b>%1</b>",array($school->registration_password));
			echo "<br />";
			echo "<br />";
		}
		else if($config['participant_registration_type']=="invite" || $config['participant_registration_type']=="openorinvite" )
		{

			echo "<h4>".i18n("Participant Registration Invitations")."</h4>";
			if($config['participant_registration_type']=="invite")
				echo i18n("In order for your school's students to register for the fair, you must first invite them via email.  Use the 'Participant Registration Invitations' link below to invite your students to the fair");
			else if($config['participant_registration_type']=="openorinvite" )
				echo i18n("In order for your school's students to register for the fair, you can invite them via email using the 'Participant Registration Invitations' link below, or they can register on their own by accessing the 'Participant Registration' link in the menu.");
			echo "<br />";
			echo "<br />";
			echo "&nbsp;&nbsp;&nbsp;<a href=\"schoolinvite.php\">".i18n("Participant Registration Invitations")."</a>";
			echo "<br />";
		}

		echo "<br>";
		echo "<h4>".i18n("School Feedback / Questions")."</h4>";

		echo i18n("We are always welcome to any feedback (both positive and constructive criticism!), or any questions you may have.  Please use the following form to communicate with the science fair committee!");
		if($sh_email != '') {
		echo "<form method=POST action=\"schoolaccess.php\">";
		echo "<input type=hidden name=action value=\"feedback\">";
		echo "<br><textarea name=feedbacktext rows=8 cols=60></textarea><br>";
		echo "<input type=submit value=\"Send Feedback\">";
		echo "</form>";
		}
		else
			echo error(i18n("Feedback is disabled until a science teacher email address is entered above"));
	}
	else {
		echo error(i18n("Invalid School ID or Access Code"));
	}
}
else {
	if($errormsg) echo "<font color=red><b>$errormsg</b></font>";
	if($happymsg) echo happy($happymsg);

	echo "	<form method=POST action=\"schoolaccess.php\">\n";

	echo output_page_text("schoolaccess");

	if($config['participant_registration_type']=="open" || $config['participant_registration_type']=="openorinvite")
	{
		echo "<br><br>\n";
		echo i18n("Note: Schools do not need to login in order to have students register from their school.  Students can register by going to the Participant Registration Page.  The only benefit of logging in is to update your school contact information or submit feedback.:");
		echo "<br />";
		echo "&nbsp;&nbsp;&nbsp;<a href=\"register_participants.php\">",i18n("Participant Registration")."</a><br />";

	}
	echo "<br />";
	echo i18n("Please login below by selecting your school and entering your school <b>Access Code</b> that you received in your package");
?>

	<br><br>
	<table border=0 cellspacing=0 cellpadding=5>
	<tr><td><?=i18n("School")?>:</td><td>
	<select name="schoolid">
	<option value=""><?=i18n("Choose your school")?></option>
	<?
	$q=mysql_query("SELECT id,school,city FROM schools WHERE year='".$config['FAIRYEAR']."' ORDER BY school");
	$prev="somethingthatdoesnotexist";
	while($r=mysql_fetch_object($q))
	{
		if($r->school==$prev)
			echo "<option value=\"$r->id\">$r->school ($r->city)</option>\n";
		else
			echo "<option value=\"$r->id\">$r->school</option>\n";
		$prev=$r->school;
	}
	?>
	</select>
	</td></tr>
	<tr><td><?=i18n("Access Code")?>:</td><td><input type=text name=accesscode></td></tr>
	<tr><td align=center><input type=submit value="<?=i18n("Login")?>"></td></tr>
	</table>

	</form>

	<br><br>

<?
}


send_footer();

?>
