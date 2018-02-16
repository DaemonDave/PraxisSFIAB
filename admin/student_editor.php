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
$auth_type = user_auth_required(array('fair','committee'), 'admin');

$registrations_id = intval($_GET['id']);
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
case 'registration_load':
	registration_load();
	exit;

case 'registration_save':
	registration_save();
	exit;

case 'students_load':
	students_load();
	exit;

case 'students_save':
	students_save();
	exit;

case 'student_remove':
	$remove_id = intval($_GET['students_id']);
	$q=mysql_query("SELECT id FROM students WHERE id='$remove_id' AND registrations_id='$registrations_id'");
	if(mysql_num_rows($q)!=1) {
		error_("Invalid student to remove");
		exit;
	}

	mysql_query("DELETE FROM students WHERE id='$remove_id' AND registrations_id='$registrations_id'");

	//now see if they have an emergency contact that also needs to be removed
	$q=mysql_query("SELECT id FROM emergencycontact WHERE students_id='$remove_id' AND registrations_id='$registrations_id' AND year='{$config['FAIRYEAR']}'");
	//no need to error message if this doesnt exist
	if(mysql_num_rows($q)==1)
		mysql_query("DELETE FROM emergencycontact WHERE students_id='$remove_id' AND registrations_id='$registrations_id' AND year='{$config['FAIRYEAR']}'");

	happy_("Student successfully removed");
	exit;

default:
	exit;
}


exit;


//now do any data saves
function students_save()
{
	global $registrations_id, $config;

	$x=1;
	while($_POST["num"][$x]) {
		if($_POST['id'][$x]==0) {
			//if they use schoolpassword or singlepassword, then we need to set the school based on the school stored in the registration record.  for anything else they can school the school on their own.
			if($config['participant_registration_type']=="schoolpassword" || $config['participant_registration_type']=="invite") {
				$q=mysql_query("SELECT schools_id FROM registrations WHERE id='$registrations_id' AND YEAR='{$config['FAIRYEAR']}'");
				$r=mysql_fetch_object($q);
				$schools_id=$r->schools_id;
				$schoolvalue="'$schools_id', ";
			} else {
				$schoolvalue="'".mysql_escape_string(stripslashes($_POST['schools_id'][$x]))."', ";
			}
			//INSERT new record
			$dob=$_POST['year'][$x]."-".$_POST['month'][$x]."-".$_POST['day'][$x];
			mysql_query("INSERT INTO students (registrations_id,firstname,lastname,sex,email,address,city,province,postalcode,phone,dateofbirth,grade,schools_id,tshirt,medicalalert,foodreq,teachername,teacheremail,year) VALUES (".
					"'".$registrations_id."', ".
					"'".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['firstname'][$x])))."', ".
					"'".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['lastname'][$x])))."', ".
					"'".mysql_escape_string(stripslashes($_POST['sex'][$x]))."', ".
					"'".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['email'][$x])))."', ".
					"'".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['address'][$x])))."', ".
					"'".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['city'][$x])))."', ".
					"'".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['province'][$x])))."', ".
					"'".mysql_escape_string(stripslashes($_POST['postalcode'][$x]))."', ".
					"'".mysql_escape_string(stripslashes($_POST['phone'][$x]))."', ".
					"'$dob', ".
					"'".mysql_escape_string(stripslashes($_POST['grade'][$x]))."', ".
					$schoolvalue.
					"'".mysql_escape_string(stripslashes($_POST['tshirt'][$x]))."', ".
					"'".mysql_escape_string(stripslashes($_POST['medicalalert'][$x]))."', ".
					"'".mysql_escape_string(stripslashes($_POST['foodreq'][$x]))."', ".
					"'".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['teachername'][$x])))."', ".
					"'".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['teacheremail'][$x])))."', ".
					"'".$config['FAIRYEAR']."')");

			happy_("%1 %2 successfully added",array($_POST['firstname'][$x],$_POST['lastname'][$x]));

		} else {

			//if they use schoolpassword or singlepassword, then we dont need to save teh schools_id because its already set when they inserted the record, and we dont allow them to change their school.
			if(( $config['participant_registration_type']=="schoolpassword" || $config['participant_registration_type']=="invite") && !$_POST['schools_id'][$x]) {
				$schoolquery="";
			} else if($_POST['schools_id'][$x]) {
				$schoolquery="schools_id='".mysql_escape_string(stripslashes($_POST['schools_id'][$x]))."', ";
			} else
				$schoolquery="";


			//UPDATE existing record
			$dob=$_POST['year'][$x]."-".$_POST['month'][$x]."-".$_POST['day'][$x];
			mysql_query("UPDATE students SET ".
					"firstname='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['firstname'][$x])))."', ".
					"lastname='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['lastname'][$x])))."', ".
					"sex='".mysql_escape_string(stripslashes($_POST['sex'][$x]))."', ".
					"email='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['email'][$x])))."', ".
					"address='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['address'][$x])))."', ".
					"city='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['city'][$x])))."', ".
					"province='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['province'][$x])))."', ".
					"postalcode='".mysql_escape_string(stripslashes($_POST['postalcode'][$x]))."', ".
					"phone='".mysql_escape_string(stripslashes($_POST['phone'][$x]))."', ".
					"dateofbirth='$dob', ".
					"grade='".mysql_escape_string(stripslashes($_POST['grade'][$x]))."', ".
					$schoolquery.
					"medicalalert='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['medicalalert'][$x])))."', ".
					"foodreq='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['foodreq'][$x])))."', ".
					"teachername='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['teachername'][$x])))."', ".
					"teacheremail='".mysql_escape_string(iconv("UTF-8","ISO-8859-1//TRANSLIT",stripslashes($_POST['teacheremail'][$x])))."', ".
					"tshirt='".mysql_escape_string(stripslashes($_POST['tshirt'][$x]))."' ".
					"WHERE id='".$_POST['id'][$x]."'");
			happy_("%1 %2 successfully updated",array(iconv("UTF-8","ISO-8859-1//TRANSLIT",$_POST['firstname'][$x]),iconv("UTF-8","ISO-8859-1//TRANSLIT",$_POST['lastname'][$x])));
		}
		$x++;	
	}
}


function students_load()
{
	global $registrations_id, $config;

	//now query and display 
	$q=mysql_query("SELECT * FROM students WHERE
				registrations_id='$registrations_id' 
				AND year='{$config['FAIRYEAR']}'");
	echo mysql_error();

	$numfound=mysql_num_rows($q);

	$numtoshow = intval($_GET['numstudents']);
	if($numtoshow == 0) $numtoshow=$numfound;


	echo "<form>";
	echo i18n("Number of students that worked on the project: ");
	echo "<select id=\"students_num\">\n";
	for($x=$config['minstudentsperproject'];$x<=$config['maxstudentsperproject'];$x++) {
		/* Don't let them go back to fewer student by selection, 
		 * force them to delete one */
		if($x<$numfound) continue;

		$sel = ($numtoshow==$x) ? 'selected="selected"' : '';
		echo "<option $sel value=\"$x\">$x</option>\n";
	}
	echo "</select>";
	echo "</form>";

	echo "<form id=\"students_form\" >";
	for($x=1;$x<=$numtoshow;$x++) {
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

		if($config['participant_student_personal']=="yes") {
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

			//the year selector should be based on the min/max grades possible
			//assume min age of 3 for grade=0 (kindergarden)
			//assume max age of 18 for grade=12
			$minyearselect=$config['FAIRYEAR'] - 6 - $config['maxgrade'];
			$maxyearselect=$config['FAIRYEAR'] - 3 - $config['mingrade'];
			emit_year_selector("year[$x]",$year,$minyearselect,$maxyearselect);
			echo "</td><td>".REQUIREDFIELD."</td></tr></table>\n";
			echo "</td>\n";
		}
		else
			echo "<tr>";

		echo " <td>".i18n("Grade")."</td><td colspan=\"3\">\n";

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
			echo "<tr>\n";
			echo " <td>".i18n("T-Shirt Size")."</td><td>";
			echo "    <select name=\"tshirt[$x]\">\n";
				if($studentinfo->tshirt=="none") $sel="selected=\"selected\""; else $sel="";
			echo "		<option $sel value=\"none\">".i18n("None")."</option>";
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
		if( $config['participant_registration_type']=="open" || $config['participant_registration_type']=="singlepassword" || ($studentinfo && !$studentinfo->schools_id) )
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




		echo "</table>";

		if($numfound>$config['minstudentsperproject'] && $studentinfo->id)
		{
			/* Create a hidden with same id as the button and some extra, so we can find it inside
			 * the button even with: this.id"+_studebts_id" */
			echo "<input type=\"hidden\" id=\"students_remove_{$studentinfo->id}_students_id\" name=\"students_remove[]\" value=\"{$studentinfo->id}\" />";

			/* Define the button */
			echo "<div align=\"right\"><button id=\"students_remove_{$studentinfo->id}\" class=\"students_remove_button\" >";
			echo "<img style=\"vertical-align:middle\" src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=0>";
			echo "&nbsp;&nbsp;".i18n("Remove this student from project");
			echo "</button></div>";

			echo "<br/><hr/>";
		}

		echo "<br />";
		echo "<br />";
	}
	echo "<br />";
	echo i18n("WARNING! If you make a change to the grade that would affect the project number, you must update the project number manually, it will NOT be automatically updated");
	echo "<br />";
	echo "<input type=\"button\" id=\"students_save\" value=\"".i18n("Save Student Information")."\" />\n";
	echo "</form>";
	echo "<br />";
}


function registration_load()
{
	global $registrations_id, $config, $auth_type;

	/* Load reg data */
	if($registrations_id == -1) {
		/* New project */
		/* Find a reg num */
		do {
			$regnum=rand(100000,999999);
			$q=mysql_query("SELECT * FROM registrations WHERE num='$regnum' AND year={$config['FAIRYEAR']}");
		} while(mysql_num_rows($q)>0);

		$r['num'] = $regnum;
		echo notice(i18n('New registration number generated.'));
		echo notice(i18n('This new registration will added when the "Save Registration Information" button is pressed below.  At that time the other tabs will become available.'));
	} else {
		$q = mysql_query("SELECT * FROM registrations WHERE id='$registrations_id'");
		if(mysql_num_rows($q) != 1) 
			$r = array();
		else {
			$r = mysql_fetch_assoc($q);
			/* Get the fair from the project */
			$q = mysql_query("SELECT fairs_id FROM projects WHERE registrations_id='$registrations_id'");
			if(mysql_num_rows($q) == 1) {
				$p = mysql_fetch_assoc($q);
				$r['fairs_id'] = $p['fairs_id'];
			}
		}

	}

	/* Load fairs */
	$fairs = array();
	$q = mysql_query("SELECT * FROM fairs WHERE type='feeder'");
	while(($f = mysql_fetch_assoc($q))) {
		$fairs[$f['id']] = $f;
	}

	/* Print form */
	$status = array('new'=>'New', 'open'=>'Open','paymentpending'=>'Payment Pending', 'complete'=>'Complete');

?>
	<form id="registration_form">
	<table>
	<tr>
		<td><?=i18n("Registration Number")?>:</td>
		<td><input type="text" name="registration_num" value="<?=$r['num']?>"></td>
	</tr><tr>
		<td><?=i18n("Registration Email")?>:</td>
		<td><input type="text" name="registration_email" value="<?=$r['email']?>"></td>
	</tr><tr>
		<td><?=i18n("Status")?>:</td>
		<td><select name="registration_status">
<?			foreach($status as $k=>$v) {
				$sel = ($k == $r['status']) ? 'selected="selected"' : '';
				echo "<option $sel value=\"$k\">$v</option>";
			} 
?>			</select></td>
	</tr>
<?
if(count($fairs)>0) {	
?>	
	 <tr>
		<td><?=i18n("Fair")?>:</td>
		<td>
<?		if($auth_type == 'fair') {
			echo $fairs[$_SESSION['fairs_id']]['name'];
		} else {
?>			<select name="registration_fair">
				<option value="0"><?=i18n('Independent/None')?></option>
<?				foreach($fairs as $fid=>$f) {
					$sel = ($fid == $r['fairs_id']) ? 'selected="selected"' : '';
					echo "<option $sel value=\"$fid\">{$f['name']}</option>";
				} 
?>				</select>
<?		}
?>		</td>
	</tr>
<?
}
else {
	echo "<input type=\"hidden\" name=\"registration_fair\" value=\"0\" />\n";
}
?>
	</table>
	<br /><br />
	<button id="registration_save"><?=i18n('Save Registration Information')?></button>
	</form>
<?
}

function registration_save()
{
	global $registrations_id, $config, $auth_type;
	$registration_num = intval($_POST['registration_num']);
	$registration_status = mysql_real_escape_string(stripslashes($_POST['registration_status']));
	$registration_email = mysql_real_escape_string(stripslashes($_POST['registration_email']));
	$fairs_id = intval($_POST['registration_fair']);

	if($registrations_id == -1) {
		mysql_query("INSERT INTO registrations (start,schools_id,year) VALUES (
					NOW(), NULL, '{$config['FAIRYEAR']}')");
		$registrations_id = mysql_insert_id();

		/* Create one student and a project */
		mysql_query("INSERT INTO students (registrations_id,email,year) VALUES (
					$registrations_id, '$registration_email', '{$config['FAIRYEAR']}')");
		mysql_query("INSERT INTO projects (registrations_id,year) VALUES (
					$registrations_id, '{$config['FAIRYEAR']}')");
		happy_('Created student and project record');
	}

	/* Update registration */
	mysql_query("UPDATE registrations SET 
					num='$registration_num',
					status='$registration_status',
					email='$registration_email'
				WHERE 
					id='$registrations_id'");
	echo mysql_error();

	/* And the fairs_id, override anythign specified 
	 * if the user is a fair, force their own fairs_id */
	if($auth_type == 'fair') $fairs_id = $_SESSION['fairs_id'];
	mysql_query("UPDATE projects SET 
					fairs_id='$fairs_id'
				WHERE 
					registrations_id='$registrations_id'");
	echo mysql_error();
	happy_('Information Saved');
	echo "<script language=\"javascript\" type=\"text/javascript\">";
	echo "registrations_id=$registrations_id;";
	echo "</script>";
}

?>
