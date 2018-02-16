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
 if(! ($_SESSION['registration_number'] && $_SESSION['registration_id']))
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
 $r=mysql_fetch_object($q);

 send_header("Participant Registration - Student Information");
 echo "<a href=\"register_participants_main.php\">&lt;&lt; ".i18n("Back to Participant Registration Summary")."</a><br />";
 echo "<br />";

 $regfee_items = array();
 $items_q = mysql_query("SELECT * FROM regfee_items
 				WHERE year='{$config['FAIRYEAR']}'");
 while($items_i = mysql_fetch_assoc($items_q)) {
 	$regfee_items[] = $items_i;
 }



//now do any data saves
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
		$x=1;
		while($_POST["num"][$x])
		{
			$students_id = intval($_POST['id'][$x]);
			if($students_id==0)
			{
				//if they use schoolpassword or singlepassword, then we need to set the school based on the school stored in the registration record.  for anything else they can school the school on their own.
				if($config['participant_registration_type']=="schoolpassword" || $config['participant_registration_type']=="invite")
				{
					$q=mysql_query("SELECT schools_id FROM registrations WHERE id='".$_SESSION['registration_id']."' AND YEAR='".$config['FAIRYEAR']."'");
					$r=mysql_fetch_object($q);
					$schools_id=$r->schools_id;

					$schoolvalue="'$schools_id', ";
				}
				else
				{
					$schoolvalue="'".mysql_escape_string(stripslashes($_POST['schools_id'][$x]))."', ";
				}
				//INSERT new record
				$dob=$_POST['year'][$x]."-".$_POST['month'][$x]."-".$_POST['day'][$x];
				mysql_query("INSERT INTO students (registrations_id,firstname,lastname,pronunciation,sex,email,address,city,province,postalcode,phone,dateofbirth,grade,schools_id,tshirt,medicalalert,foodreq,teachername,teacheremail,year) VALUES (".
						"'".$_SESSION['registration_id']."', ".
						"'".mysql_escape_string(stripslashes($_POST['firstname'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['lastname'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['pronunciation'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['sex'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['email'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['address'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['city'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['province'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['postalcode'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['phone'][$x]))."', ".
						"'$dob', ".
						"'".mysql_escape_string(stripslashes($_POST['grade'][$x]))."', ".
						$schoolvalue.
						"'".mysql_escape_string(stripslashes($_POST['tshirt'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['medicalalert'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['foodreq'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['teachername'][$x]))."', ".
						"'".mysql_escape_string(stripslashes($_POST['teacheremail'][$x]))."', ".
						"'".$config['FAIRYEAR']."')");
				$students_id = mysql_insert_id();
				
				echo notice(i18n("%1 %2 successfully added",array($_POST['firstname'][$x],$_POST['lastname'][$x])));

			}
			else
			{

				//if they use schoolpassword or singlepassword, then we dont need to save teh schools_id because its already set when they inserted the record, and we dont allow them to change their school.
				if(( $config['participant_registration_type']=="schoolpassword" || $config['participant_registration_type']=="invite") && !$_POST['schools_id'][$x])
				{
					$schoolquery="";
				}
				else
				{
					$schoolquery="schools_id='".mysql_escape_string(stripslashes($_POST['schools_id'][$x]))."', ";
				}


				//UPDATE existing record
				$dob=$_POST['year'][$x]."-".$_POST['month'][$x]."-".$_POST['day'][$x];
				mysql_query("UPDATE students SET ".
						"firstname='".mysql_escape_string(stripslashes($_POST['firstname'][$x]))."', ".
						"lastname='".mysql_escape_string(stripslashes($_POST['lastname'][$x]))."', ".
						"pronunciation='".mysql_escape_string(stripslashes($_POST['pronunciation'][$x]))."', ".
						"sex='".mysql_escape_string(stripslashes($_POST['sex'][$x]))."', ".
						"email='".mysql_escape_string(stripslashes($_POST['email'][$x]))."', ".
						"address='".mysql_escape_string(stripslashes($_POST['address'][$x]))."', ".
						"city='".mysql_escape_string(stripslashes($_POST['city'][$x]))."', ".
						"province='".mysql_escape_string(stripslashes($_POST['province'][$x]))."', ".
						"postalcode='".mysql_escape_string(stripslashes($_POST['postalcode'][$x]))."', ".
						"phone='".mysql_escape_string(stripslashes($_POST['phone'][$x]))."', ".
						"dateofbirth='$dob', ".
						"grade='".mysql_escape_string(stripslashes($_POST['grade'][$x]))."', ".
						$schoolquery.
						"medicalalert='".mysql_escape_string(stripslashes($_POST['medicalalert'][$x]))."', ".
						"foodreq='".mysql_escape_string(stripslashes($_POST['foodreq'][$x]))."', ".
						"teachername='".mysql_escape_string(stripslashes($_POST['teachername'][$x]))."', ".
						"teacheremail='".mysql_escape_string(stripslashes($_POST['teacheremail'][$x]))."', ".
						"tshirt='".mysql_escape_string(stripslashes($_POST['tshirt'][$x]))."' ".
						"WHERE id='$students_id'");
				echo notice(i18n("%1 %2 successfully updated",array($_POST['firstname'][$x],$_POST['lastname'][$x])));

			}
			/* Update the regfee items link */
			if($config['participant_regfee_items_enable'] == 'yes') {
				mysql_query("DELETE FROM regfee_items_link WHERE students_id='$students_id'");

				if(is_array($_POST['regfee_item'][$x])) {
					foreach($_POST['regfee_item'][$x] as $id=>$enabled) {
						mysql_query("INSERT INTO regfee_items_link(`students_id`,`regfee_items_id`)
								VALUES ('$students_id','$id') ");
					}
				}
			}
			$x++;	
		}
	}
}

if($_GET['action']=="removestudent")
{
	if(registrationFormsReceived())
	{
		echo error(i18n("Cannot make changes to forms once they have been received by the fair"));
	}
	else
	{
		$students_id = intval($_GET['removestudent']);
		//first make sure this is one belonging to this registration id
		$q=mysql_query("SELECT id FROM students WHERE id='$students_id' AND registrations_id='".$_SESSION['registration_id']."'");
		if(mysql_num_rows($q)==1)
		{
			mysql_query("DELETE FROM students WHERE id='$students_id' AND registrations_id='".$_SESSION['registration_id']."'");

			//now see if they have an emergency contact that also needs to be removed

			$q=mysql_query("SELECT id FROM emergencycontact WHERE students_id='$students_id' AND registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
			//no need to error message if this doesnt exist
			if(mysql_num_rows($q)==1)
				mysql_query("DELETE FROM emergencycontact WHERE students_id='$students_id' AND registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");

			mysql_query("DELETE FROM regfee_items_link WHERE students_id='$students_id'");
			
			echo notice(i18n("Student successfully removed"));
		}
		else
		{
			echo error(i18n("Invalid student to remove"));
		}
	}
}



//output the current status
$newstatus=studentStatus();
if($newstatus!="complete")
{
	echo error(i18n("Student Information Incomplete"));
}
else if($newstatus=="complete")
{
	echo happy(i18n("Student Information Complete"));

}

//now query and display

 $q=mysql_query("SELECT * FROM students WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");

 if(mysql_num_rows($q)==0)
 {
 	//uhh oh, we didnt find any, this isnt possible! lets insert one using the logged in persons email address
	//although... this can never really happen, since the above queries only allow the page to view if the student
	//is found in the students table... soo... well, lets leave it here as a fallback anyways, just incase
	mysql_query("INSERT INTO students (registrations_id,email,year) VALUES ('".$_SESSION['registration_id']."','".mysql_escape_string($_SESSION['email'])."','".$config['FAIRYEAR']."')");
	//if we just inserted it, then we will obviously find 1
	$numfound=1;
 }
 else
 {
 	$numfound=mysql_num_rows($q);
 }

 if($_GET['numstudents'])
 	$numtoshow=$_GET['numstudents'];
 else
 	$numtoshow=$numfound;

 echo "<form name=\"numstudentsform\" method=\"get\" action=\"register_participants_students.php\">";
 echo i18n("Number of students that worked on the project: ");
 echo "<select name=\"numstudents\" onchange=\"document.forms.numstudentsform.submit()\">\n";
 for($x=$config['minstudentsperproject'];$x<=$config['maxstudentsperproject'];$x++)
 {
 	if($x<$numfound)
		continue;

 	if($numtoshow==$x) $selected="selected=\"selected\""; else $selected="";

	echo "<option $selected value=\"$x\">$x</option>\n";
 }
 echo "</select>";
 echo "</form>";

 if($numtoshow>$config['maxstudentsperproject']) 
 	$numtoshow=$config['maxstudentsperproject'];

 echo "<form name=\"studentdata\" method=\"post\" action=\"register_participants_students.php\">";
 echo "<input type=\"hidden\" name=\"action\" value=\"save\" />";
 for($x=1;$x<=$numtoshow;$x++)
 {
 	$studentinfo=mysql_fetch_object($q);
	echo "<h3>".i18n("Student %1 Details",array($x))."</h3>";
	//if we have a valid student, set their ID, so we can UPDATE when we submit
	//if there is no record for this student, then set the ID to 0, so we will INSERT when we submit
	if($studentinfo->id) $id=$studentinfo->id; else $id=0;

	//true should work here, it just has to be set to _something_ for it to work.
	echo "<input type=\"hidden\" name=\"num[$x]\" value=\"true\" />";

	//save the ID, or 0 if it doesnt exist
	echo "<input type=\"hidden\" name=\"id[$x]\" value=\"$id\" />";
	echo "<table>";
	echo "<tr>\n";
	echo " <td>".i18n("First Name")."</td><td><input type=\"text\" name=\"firstname[$x]\" value=\"$studentinfo->firstname\" />".REQUIREDFIELD."</td>\n";
	echo " <td>".i18n("Last Name")."</td><td><input type=\"text\" name=\"lastname[$x]\" value=\"$studentinfo->lastname\" />".REQUIREDFIELD."</td>\n";
	echo "</tr>\n";
if($config['participant_student_pronunciation']=='yes') {
	echo "<tr>\n";
	echo " <td>".i18n("Name Pronunciation Key")."</td><td colspan><input type=\"text\" name=\"pronunciation[$x]\" value=\"{$studentinfo->pronunciation}\" /></td>\n";
	echo " <td colspan=2><font size=-1>(for award ceremony, fill out this Pronunciation Key if your name is often mispronounced)</font></td>";
	echo "</tr>\n";
}
	

if($config['participant_student_personal']=="yes")
{
	echo "<tr>\n";
	echo " <td>".i18n("Gender")."</td><td>";
	echo "<select name=\"sex[$x]\">";
	echo "<option value=\"\">".i18n("Select")."</option>\n";
	if($studentinfo->sex=="male") $sel="selected=\"selected\""; else $sel="";
	echo "<option $sel value=\"male\">".i18n("Male")."</option>\n";
	if($studentinfo->sex=="female") $sel="selected=\"selected\""; else $sel="";
	echo "<option $sel value=\"female\">".i18n("Female")."</option>\n";
	echo "</select>".REQUIREDFIELD;
}
	echo "</td>\n";
	echo " <td></td><td></td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo " <td>".i18n("Email Address")."</td><td><input type=\"text\" name=\"email[$x]\" value=\"$studentinfo->email\" />".REQUIREDFIELD."</td>\n";

if($config['participant_student_personal']=="yes")
{
	echo " <td>".i18n("City")."</td><td><input type=\"text\" name=\"city[$x]\" value=\"$studentinfo->city\" />".REQUIREDFIELD."</td>\n";
} 
else
{
	echo "<td></td>";
}

	echo "</tr>\n";

if($config['participant_student_personal']=="yes")
{
	echo "<tr>\n";
	echo " <td>".i18n("Address")."</td><td><input type=\"text\" name=\"address[$x]\" value=\"$studentinfo->address\" />".REQUIREDFIELD."</td>\n";
	echo " <td>".i18n($config['provincestate'])."</td><td>";
	emit_province_selector("province[$x]",$studentinfo->province);
	echo REQUIREDFIELD."</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo " <td>".i18n($config['postalzip'])."</td><td><input type=\"text\" name=\"postalcode[$x]\" value=\"$studentinfo->postalcode\" />".REQUIREDFIELD."</td>\n";
	echo " <td>".i18n("Phone")."</td><td><input type=\"text\" name=\"phone[$x]\" value=\"$studentinfo->phone\" />".REQUIREDFIELD."</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo " <td>".i18n("Date of Birth")."</td><td>\n";
	list($year,$month,$day)=split("-",$studentinfo->dateofbirth);
		echo "<table><tr><td>";
			emit_day_selector("day[$x]",$day);
		echo "</td><td>\n";
			emit_month_selector("month[$x]",$month);
		echo "</td><td>\n";

		//the year selector should be based on the min/max ages possible (as set in $config)
		$minyearselect=$config['FAIRYEAR'] - $config['maxage'];
		$maxyearselect=$config['FAIRYEAR'] - $config['minage'];
			emit_year_selector("year[$x]",$year,$minyearselect,$maxyearselect);
		echo "</td><td>".REQUIREDFIELD."</td></tr></table>\n";
	echo "</td>\n";
}
else
	echo "<tr>";

	echo " <td>".i18n("Grade")."</td><td>\n";

		echo "<select name=\"grade[$x]\">\n";
		echo "<option value=\"\">".i18n("Grade")."</option>\n";
		for($gr=$config['mingrade'];$gr<=$config['maxgrade'];$gr++)
		{
			if($studentinfo->grade==$gr) $sel="selected=\"selected\""; else $sel="";

			echo "<option $sel value=\"$gr\">$gr</option>\n";
		}

		echo "</select>\n";
	echo REQUIREDFIELD."</td>";
	echo "</tr>";

	if($config['participant_student_tshirt']=="yes")
	{
		$tshirt_cost = floatval($config['participant_student_tshirt_cost']);
		echo "<tr>\n";
		echo " <td>".i18n("T-Shirt Size")."</td><td colspan=3>";
		echo "    <select name=\"tshirt[$x]\">\n";
		if($tshirt_cost != 0.0) {
			if($studentinfo->tshirt=="none") $sel="selected=\"selected\""; else $sel="";
			echo "		<option $sel value=\"none\">".i18n("None")."</option>";
		}
			if($studentinfo->tshirt=="xsmall") $sel="selected=\"selected\""; else $sel="";
		echo "		<option $sel value=\"xsmall\">".i18n("X-Small")."</option>";
			if($studentinfo->tshirt=="small") $sel="selected=\"selected\""; else $sel="";
		echo "		<option $sel value=\"small\">".i18n("Small")."</option>";
			if($studentinfo->tshirt=="medium") $sel="selected=\"selected\""; else $sel="";
		echo "		<option $sel value=\"medium\">".i18n("Medium")."</option>";
			if($studentinfo->tshirt=="large") $sel="selected=\"selected\""; else $sel="";
		echo "		<option $sel value=\"large\">".i18n("Large")."</option>";
			if($studentinfo->tshirt=="xlarge") $sel="selected=\"selected\""; else $sel="";
		echo "		<option $sel value=\"xlarge\">".i18n("X-Large")."</option>";
		echo " </select>";
		if($tshirt_cost != 0.0) {
			printf(" The cost of each T-Shirt is $%.2f, sizes are Adult sizes.", $tshirt_cost);
		}
		echo "</td>\n";
		echo "</tr>";
	}

if($config['participant_student_personal']=="yes")
{
	echo "<tr>\n";
	echo "<td>".i18n("Medical Alert Info")."</td><td colspan=\"3\">";
	echo "<input name=\"medicalalert[$x]\" type=\"text\" size=\"50\" value=\"$studentinfo->medicalalert\" />";
	echo "</td>";
	echo "</tr>\n";
}

	if($config['participant_student_foodreq']=="yes")
	{
		echo "<tr>\n";
		echo "<td>".i18n("Special Food Requirements")."</td><td colspan=\"3\">";
		echo "<input name=\"foodreq[$x]\" type=\"text\" size=\"50\" value=\"$studentinfo->foodreq\" />";
		echo "</td>";
		echo "</tr>\n";
	}

	echo "<tr>\n";
	echo " <td>".i18n("School")."</td><td colspan=\"3\">";
	if( $config['participant_registration_type']=="open" || $config['participant_registration_type']=="singlepassword" || $config['participant_registration_type']=="openorinvite" || ($studentinfo && !$studentinfo->schools_id) )
	{
		$schoolq=mysql_query("SELECT id,school,city FROM schools WHERE year='".$config['FAIRYEAR']."' ORDER by city,school");
		echo "<select name=\"schools_id[$x]\">\n";
		echo "<option value=\"\">".i18n("Choose School")."</option>\n";
		while($r=mysql_fetch_object($schoolq))
		{
			if($studentinfo->schools_id==$r->id) $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"$r->id\">".htmlspecialchars($r->city).' - '.htmlspecialchars($r->school)."</option>\n";
				
		}
		echo "</select>".REQUIREDFIELD;
	}
	else
	{
		$schoolq=mysql_query("SELECT id,school FROM schools WHERE year='".$config['FAIRYEAR']."' AND id='$studentinfo->schools_id'");
		$r=mysql_fetch_object($schoolq);
		echo $r->school;
	}

	echo "</td>\n";
	echo "</tr>\n";

	echo "<tr>\n";
	echo " <td>".i18n("Teacher Name")."</td><td><input type=\"text\" name=\"teachername[$x]\" value=\"$studentinfo->teachername\" /></td>\n";
	echo " <td>".i18n("Teacher Email")."</td><td><input type=\"text\" name=\"teacheremail[$x]\" value=\"$studentinfo->teacheremail\" /></td>\n";
	echo "</tr>\n";

	if($config['participant_regfee_items_enable'] == 'yes' ) {
		$sel_q = mysql_query("SELECT * FROM regfee_items_link 
					WHERE students_id=$id");
		$sel = array();
		while($info_q = mysql_fetch_assoc($sel_q)) {
			$sel[$info_q['regfee_items_id']] = $info_q['id'];
		}
		foreach($regfee_items as $rfi) {
			echo "<tr><td align=\"right\">\n";
			$checked = array_key_exists($rfi['id'], $sel) ? 'checked="checked"' : '';
			echo "<input type=\"checkbox\" name=\"regfee_item[$x][{$rfi['id']}]\" $checked />";
			echo '</td><td colspan=\"2\">';
			echo i18n($rfi['description']);
			echo '</td></tr>';
		}
	}
	echo "</table>";



	if($numfound>$config['minstudentsperproject'] && $studentinfo->id)
	{
		echo "<div align=\"right\"><a onclick=\"return confirmClick('".i18n("Are you sure you want to remove this student from the project?")."');\" class=\"caution\" href=\"register_participants_students.php?action=removestudent&amp;removestudent=$studentinfo->id\">".i18n("Remove this student from project")."</a></div>";
	}

	echo "<br />";
	echo "<br />";
 }
 echo "<input type=\"submit\" value=\"".i18n("Save Student Information")."\" />\n";
 echo "</form>";
 echo "<br />";
 echo notice(i18n("Note: if you change the email address that you are logged in with right now, you will be automatically logged out and will need to log back in again with your new email address"));

 send_footer();
?>
