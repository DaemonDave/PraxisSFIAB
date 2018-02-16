<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2009 James Grant <james@lightbox.org>

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
 if(!$_SESSION['email']) {
 	header("Location: register_participants.php");
	exit;
 }
 if(!$_SESSION['registration_number']) {
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

 if(mysql_num_rows($q)==0) {
 	header("Location: register_participants.php");
	exit;
 }
 $authinfo=mysql_fetch_object($q);

 //send the header
 send_header("Participant Registration - Safety Information");

 echo "<a href=\"register_participants_main.php\">&lt;&lt; ".i18n("Back to Participant Registration Summary")."</a><br />";
 echo "<br />";

 if($_POST['action']=="save") {
	if(registrationFormsReceived()) {
		echo error(i18n("Cannot make changes to forms once they have been received by the fair"));
	}
	else if(registrationDeadlinePassed()) {
		echo error(i18n("Cannot make changes to forms after registration deadline"));
	}
	else {
		//first we will delete all their old answer, its easier to delete and re-insert in this case then it would be to find the corresponding answers and update them
		mysql_query("DELETE FROM safety WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
		if(is_array($_POST['safety'])) {
			$safetyids=array_keys($_POST['safety']);
			foreach($safetyids AS $key=>$val) {
				mysql_query("INSERT INTO safety (registrations_id,safetyquestions_id,year,answer) VALUES (".
						"'".$_SESSION['registration_id']."', ".
						"'$val', ".
						"'".$config['FAIRYEAR']."', ".
						"'".mysql_escape_string(stripslashes($_POST['safety'][$val]))."')");
						echo mysql_error();
			}
		}
	}	
 }

//output the current status
$newstatus=safetyStatus();
if($newstatus!="complete") {
	echo error(i18n("Safety Information Incomplete.  You must agree to / answer all required safety questions!"));
}
else if($newstatus=="complete") {
	echo happy(i18n("Safety Information Complete"));
}

 $q=mysql_query("SELECT * FROM safety WHERE registrations_id='".$_SESSION['registration_id']."'");
 while($r=mysql_fetch_object($q)) {
	$safetyanswers[$r->safetyquestions_id]=$r->answer;
 }

 $q=mysql_query("SELECT * FROM safetyquestions WHERE year='".$config['FAIRYEAR']."' ORDER BY ord");
 if(mysql_num_rows($q)) {
	 echo i18n("Please agree to / answer the following safety questions by checking the box next to the question, or choosing the appropriate answer");
	 echo "<br />";
	 echo "<br />";
	 echo "<form method=\"post\" action=\"register_participants_safety.php\">\n";
	 echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
	 echo "<table class=\"tableedit\">\n";
	 $num=1;
	 while($r=mysql_fetch_object($q)) {
		$trclass=($num%2==0?"odd":"even");
		echo "<tr class=\"$trclass\"><td><b>$num</b>. </td><td>";
		if($r->required=="yes") echo REQUIREDFIELD;
		echo i18n($r->question)."</td>";
		echo "<td>";
		if($r->type=="check") {
			if($safetyanswers[$r->id]=="checked") $ch="checked=\"checked\""; else $ch="";
			echo "<input $ch type=\"checkbox\" name=\"safety[$r->id]\" value=\"checked\" />";
		}
		else if($r->type=="yesno") {
			echo "<nobr>";
			if($safetyanswers[$r->id]=="yes") $ch="checked=\"checked\""; else $ch="";
			echo "<input $ch type=\"radio\" name=\"safety[$r->id]\" value=\"yes\" />";
			echo i18n("Yes");
			echo "</nobr><br /><nobr>";
			if($safetyanswers[$r->id]=="no") $ch="checked=\"checked\""; else $ch="";
			echo "<input $ch type=\"radio\" name=\"safety[$r->id]\" value=\"no\" />";
			echo i18n("No");
			echo "</nobr>";
		}
		echo "</td>";
		echo "</tr>";
		$num++;
	 }
	 echo "</table>";
	 echo "<input type=\"submit\" value=\"".i18n("Save Safety Information")."\" />\n";
	 echo "</form>";
 }
 else
 	echo notice(i18n("There are no safety questions to be answered"));

 send_footer();
?>
