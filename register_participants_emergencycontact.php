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
 include "user.inc.php";
 
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
 send_header("Participant Registration - Emergency Contact Information");

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
	if(registrationFormsReceived()) {
		echo error(i18n("Cannot make changes to forms once they have been received by the fair"));
	}
	else if(registrationDeadlinePassed()) {
		echo error(i18n("Cannot make changes to forms after registration deadline"));
	}
	else {
		//first, lets make sure this emergency contact really does belong to them
		foreach($_POST['ids'] AS $id)
		{ 
			$q=mysql_query("SELECT * FROM emergencycontact WHERE id='$id' AND registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
			if(mysql_num_rows($q)==1) {
                $e=stripslashes($_POST['email'][$id]);
                if($_POST['relation'][$id]=="Parent" && $e && user_valid_email($e)) {
                   if($u=user_load_by_email($e)) {
                       $u['firstname']=stripslashes($_POST['firstname'][$id]);
                       $u['lastname']=stripslashes($_POST['lastname'][$id]);
                       $u['phonehome']=stripslashes($_POST['phone1'][$id]);
                       $u['phonework']=stripslashes($_POST['phone2'][$id]);
                       $u['email']=$e;
                       $u['types'][]="parent";
                       user_save($u);
                   }
                   else {
                       $u=user_create("parent",$e);
                       $u['firstname']=stripslashes($_POST['firstname'][$id]);
                       $u['lastname']=stripslashes($_POST['lastname'][$id]);
                       $u['phonehome']=stripslashes($_POST['phone1'][$id]);
                       $u['phonework']=stripslashes($_POST['phone2'][$id]);
                       $u['email']=$e;
                       user_save($u);
                   }
                }

				mysql_query("UPDATE emergencycontact SET ".
						"firstname='".mysql_escape_string(stripslashes($_POST['firstname'][$id]))."', ".
						"lastname='".mysql_escape_string(stripslashes($_POST['lastname'][$id]))."', ".
						"relation='".mysql_escape_string(stripslashes($_POST['relation'][$id]))."', ".
						"phone1='".mysql_escape_string(stripslashes($_POST['phone1'][$id]))."', ".
						"phone2='".mysql_escape_string(stripslashes($_POST['phone2'][$id]))."', ".
						"phone3='".mysql_escape_string(stripslashes($_POST['phone3'][$id]))."', ".
						"phone4='".mysql_escape_string(stripslashes($_POST['phone4'][$id]))."', ".
						"email='".mysql_escape_string(stripslashes($_POST['email'][$id]))."' ".
						"WHERE id='$id'");
						echo mysql_error();
				echo notice(i18n("Emergency contact information successfully updated"));
			}
			else
			{
				echo error(i18n("Invalid emergency contact to update (%1)"),array($id));
			}
		}
	}
 }



//output the current status
$newstatus=emergencycontactStatus();
if($newstatus!="complete")
{
	echo error(i18n("Emergency Contact Information Incomplete"));
}
else if($newstatus=="complete")
{
	echo happy(i18n("Emergency Contact Information Complete"));

}

$sq=mysql_query("SELECT id,firstname,lastname FROM students WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
$numstudents=mysql_num_rows($sq);

echo "<form name=\"emergencycontactform\" method=\"post\" action=\"register_participants_emergencycontact.php\">\n";
echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";

while($sr=mysql_fetch_object($sq))
{
	$q=mysql_query("SELECT * FROM emergencycontact WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."' AND students_id='$sr->id'");

	if(mysql_num_rows($q)==0) {
		mysql_query("INSERT INTO emergencycontact (registrations_id,students_id,year) VALUES ('".$_SESSION['registration_id']."','".$sr->id."','".$config['FAIRYEAR']."')");
		$id=mysql_insert_id();
		unset($r);
	}
	else {
		$r=mysql_fetch_object($q);
		$id=$r->id;
	}

	echo "<h3>".i18n("Emergency Contact for %1 %2",array($sr->firstname,$sr->lastname))."</h3>";
	echo "<input type=\"hidden\" name=\"ids[]\" value=\"$id\">";
	echo "<table>\n";
	echo "<tr>";
	echo " <td>".i18n("First Name").": </td><td><input type=\"text\" name=\"firstname[$id]\" size=\"20\" value=\"$r->firstname\" />".REQUIREDFIELD."</td>";
	echo " <td>".i18n("Last Name").": </td><td><input type=\"text\" name=\"lastname[$id]\" size=\"20\" value=\"$r->lastname\" />".REQUIREDFIELD."</td>";
	echo "</tr>\n";
	echo "<tr>";
	echo " <td>".i18n("Relation").": </td><td>";
    echo "  <select name=\"relation[$id]\">\n";
    echo "   <option value=\"\">".i18n("Choose a relation")."</option>\n";
    $relations=array("Parent","Legal Guardian","Grandparent","Family Friend", "Other");
    foreach($relations AS $rel) {
        if($r->relation==$rel) $sel="selected=\"selected\""; else $sel="";
        echo "<option $sel value=\"$rel\">".i18n($rel)."</option>\n";
    }
    echo "  </select>\n";
    echo REQUIREDFIELD."</td>";
	echo " <td>".i18n("Email Address").": </td><td><input type=\"text\" name=\"email[$id]\" size=\"20\" value=\"$r->email\" /></td>";
	echo "</tr>\n";
	echo "<tr>";
	echo " <td>".i18n("Phone 1").": </td><td><input type=\"text\" name=\"phone1[$id]\" size=\"20\" value=\"$r->phone1\" />".REQUIREDFIELD."</td>";
	echo " <td>".i18n("Phone 2").": </td><td><input type=\"text\" name=\"phone2[$id]\" size=\"20\" value=\"$r->phone2\" /></td>";
	echo "</tr>\n";
	echo "<tr>";
	echo " <td>".i18n("Phone 3").": </td><td><input type=\"text\" name=\"phone3[$id]\" size=\"20\" value=\"$r->phone3\" /></td>";
	echo " <td>".i18n("Phone 4").": </td><td><input type=\"text\" name=\"phone4[$id]\" size=\"20\" value=\"$r->phone4\" /></td>";
	echo "</tr>\n";
	echo "</table>";
	echo "<br />";
	echo "<br />";

 }
 echo "<input type=\"submit\" value=\"".i18n("Save Emergency Contact Information")."\" />\n";
 echo "</form>";


 send_footer();
?>
