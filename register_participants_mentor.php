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

 send_header("Participant Registration - Mentor Information");
 echo "<a href=\"register_participants_main.php\">&lt;&lt; ".i18n("Back to Participant Registration Summary")."</a><br />";
 echo "<br />";


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
			if($_POST['id'][$x]==0)
			{
				//only insert if we have a name
				if($_POST['lastname'][$x])
				{
					//INSERT new record
					mysql_query("INSERT INTO mentors (registrations_id,firstname,lastname,email,phone,organization,position,description,year) VALUES (".
							"'".$_SESSION['registration_id']."', ".
							"'".mysql_escape_string(stripslashes($_POST['firstname'][$x]))."', ".
							"'".mysql_escape_string(stripslashes($_POST['lastname'][$x]))."', ".
							"'".mysql_escape_string(stripslashes($_POST['email'][$x]))."', ".
							"'".mysql_escape_string(stripslashes($_POST['phone'][$x]))."', ".
							"'".mysql_escape_string(stripslashes($_POST['organization'][$x]))."', ".
							"'".mysql_escape_string(stripslashes($_POST['position'][$x]))."', ".
							"'".mysql_escape_string(stripslashes($_POST['description'][$x]))."', ".
							"'".$config['FAIRYEAR']."')");
					echo mysql_error();

					echo notice(i18n("%1 %2 successfully added",array($_POST['firstname'][$x],$_POST['lastname'][$x])));
				}

			}
			else
			{
				//UPDATE existing record
				mysql_query("UPDATE mentors SET ".
						"firstname='".mysql_escape_string(stripslashes($_POST['firstname'][$x]))."', ".
						"lastname='".mysql_escape_string(stripslashes($_POST['lastname'][$x]))."', ".
						"email='".mysql_escape_string(stripslashes($_POST['email'][$x]))."', ".
						"phone='".mysql_escape_string(stripslashes($_POST['phone'][$x]))."', ".
						"organization='".mysql_escape_string(stripslashes($_POST['organization'][$x]))."', ".
						"position='".mysql_escape_string(stripslashes($_POST['position'][$x]))."', ".
						"description='".mysql_escape_string(stripslashes($_POST['description'][$x]))."' ".
						"WHERE id='".$_POST['id'][$x]."'");
				echo notice(i18n("%1 %2 successfully updated",array($_POST['firstname'][$x],$_POST['lastname'][$x])));

			}
			$x++;	
		}
	}

}

if($_GET['action']=="removementor")
{
	if(registrationFormsReceived())
	{
		echo error(i18n("Cannot make changes to forms once they have been received by the fair"));
	}
	else
	{
		//first make sure this is one belonging to this registration id
		$q=mysql_query("SELECT id FROM mentors WHERE id='".$_GET['removementor']."' AND registrations_id='".$_SESSION['registration_id']."'");
		if(mysql_num_rows($q)==1)
		{
			mysql_query("DELETE FROM mentors WHERE id='".$_GET['removementor']."' AND registrations_id='".$_SESSION['registration_id']."'");
			echo notice(i18n("Mentor successfully removed"));
		}
		else
		{
			echo error(i18n("Invalid mentor to remove"));
		}
	}
}



//now query and display

 $q=mysql_query("SELECT nummentors FROM registrations WHERE id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
 $r=mysql_fetch_object($q);
 $registrations_nummentors=$r->nummentors;

 $q=mysql_query("SELECT * FROM mentors WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");

 $numfound=mysql_num_rows($q);

 if(isset($_GET['nummentors']))
 {
	mysql_query("UPDATE registrations SET nummentors='".$_GET['nummentors']."' WHERE id='".$_SESSION['registration_id']."'");
	$registrations_nummentors=$_GET['nummentors'];
 	$numtoshow=$_GET['nummentors'];
 }
 else
 	$numtoshow=$numfound;


//output the current status
$newstatus=mentorStatus();
if($newstatus!="complete")
{
	echo error(i18n("Mentor Information Incomplete"));
}
else if($newstatus=="complete")
{
	echo happy(i18n("Mentor Information Complete"));

}


 echo "<form name=\"nummentorsform\" method=\"get\" action=\"register_participants_mentor.php\">";
 echo i18n("Number of mentors that helped with the project: ");
 echo "<select name=\"nummentors\" onchange=\"document.forms.nummentorsform.submit()\">\n";
 if($registrations_nummentors==null) $sel="selected=\"selected\""; else $sel="";
 echo "<option $sel value=\"\">".i18n("Choose")."</option>\n";
 for($x=$config['minmentorsperproject'];$x<=$config['maxmentorsperproject'];$x++)
 {
 	//dont let them go less than the number we found.  to go less, they must delete each record individually
 	if($x<$numfound)
		continue;

 	if($numtoshow==$x && $registrations_nummentors!=null) $selected="selected=\"selected\""; else $selected="";

	echo "<option $selected value=\"$x\">$x</option>\n";
 }
 echo "</select>";
 echo "</form>";

 echo "<form name=\"mentordata\" method=\"post\" action=\"register_participants_mentor.php\">";
 echo "<input type=\"hidden\" name=\"action\" value=\"save\" />";
 for($x=1;$x<=$numtoshow;$x++)
 {
 	$mentorinfo=mysql_fetch_object($q);
	echo "<h3>".i18n("Mentor %1 Details",array($x))."</h3>";
	//if we have a valid mentor, set their ID, so we can UPDATE when we submit
	//if there is no record for this mentor, then set the ID to 0, so we will INSERT when we submit
	if($mentorinfo->id) $id=$mentorinfo->id; else $id=0;

	//true should work here, it just has to be set to _something_ for it to work.
	echo "<input type=\"hidden\" name=\"num[$x]\" value=\"true\" />";

	//save the ID, or 0 if it doesnt exist
	echo "<input type=\"hidden\" name=\"id[$x]\" value=\"$id\" />";
	echo "<table>";
	echo "<tr>\n";
	echo " <td>".i18n("First Name")."</td><td><input type=\"text\" name=\"firstname[$x]\" value=\"$mentorinfo->firstname\" />".REQUIREDFIELD."</td>\n";
	echo " <td>".i18n("Last Name")."</td><td><input type=\"text\" name=\"lastname[$x]\" value=\"$mentorinfo->lastname\" />".REQUIREDFIELD."</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo " <td>".i18n("Email Address")."</td><td><input type=\"text\" name=\"email[$x]\" value=\"$mentorinfo->email\" />".REQUIREDFIELD."</td>\n";
	echo " <td>".i18n("Phone")."</td><td><input type=\"text\" name=\"phone[$x]\" value=\"$mentorinfo->phone\" />".REQUIREDFIELD."</td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo " <td>".i18n("Organization")."</td><td><input type=\"text\" name=\"organization[$x]\" value=\"$mentorinfo->organization\" />".REQUIREDFIELD."</td>\n";
	echo " <td>".i18n("Position")."</td><td><input type=\"text\" name=\"position[$x]\" value=\"$mentorinfo->position\" /></td>\n";
	echo "</tr>\n";
	echo "<tr>\n";
	echo " <td>".i18n("Description of help")."</td>";
	echo "<td colspan=3><textarea rows=\"3\" cols=\"60\" name=\"description[$x]\">".htmlspecialchars($mentorinfo->description)."</textarea>".REQUIREDFIELD."</td>\n";
	echo "</tr>\n";

	echo "</table>";

	if($mentorinfo->id)
	{
		echo "<div align=\"right\"><a onclick=\"return confirmClick('".i18n("Are you sure you want to remove this mentor?")."');\" class=\"caution\" href=\"register_participants_mentor.php?action=removementor&amp;removementor=$mentorinfo->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=0> ".i18n("Remove this Mentor from project")."</a></div>";
	}

	echo "<br />";
	echo "<br />";
 }
 if($numtoshow)
 {
 	echo "<input type=\"submit\" value=\"".i18n("Save Mentor Information")."\" />\n";
 }
 echo "</form>";

 send_footer();
?>
