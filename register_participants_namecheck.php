<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>
   Copyright (C) 2008 David Grant <dave@lightbox.org>

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
 if(!$_SESSION['registration_number'])
 {
 	header("Location: register_participants.php");
	exit;
 }

 $q=mysql_query("SELECT * FROM students WHERE registrations_id='{$_SESSION['registration_id']}'");
 echo mysql_error();

 if(mysql_num_rows($q)==0) {
 	header("Location: register_participants.php");
	exit;
 }

 while($s=mysql_fetch_object($q)) {
	 $student_display_name[]="{$s->firstname} {$s->lastname}";
 }

 //send the header
 send_header("Participant Registration - Check Your Name");

 echo "<a href=\"register_participants_main.php\">&lt;&lt; ".i18n("Back to Participant Registration Summary")."</a><br />";
 echo "<br />";

 if($_POST['action']=="save")
 {
	if(registrationDeadlinePassed()) {
		echo error(i18n("Cannot make changes after registration deadline."));
	} else {
		$sp = ($_POST['spelling'] == 'yes') ? true : false;
		$ca = ($_POST['caps'] == 'yes') ? true : false;
		$pu = ($_POST['punc'] == 'yes') ? true : false;

		if($sp && $ca && $pu) {
			$q=mysql_query("UPDATE students SET namecheck_complete='yes' WHERE registrations_id='{$_SESSION['registration_id']}'");
		} else if($s->namecheck_complete!='no') {
			$q=mysql_query("UPDATE students SET namecheck_complete='no' WHERE registrations_id='{$_SESSION['registration_id']}'");
		}
	}
 }

//output the current status
$newstatus=namecheckStatus($_SESSION['registration_id']);
if($newstatus!="complete") {
	echo error(i18n("Name Check Incomplete.  Please check your name and check all the boxes below"));
}
else if($newstatus=="complete") {
	echo happy(i18n("Name Check Complete"));
}

 echo i18n('Every year there is one participant who realizes that his/her name
 is spelled wrong after certificates are printed and plaques are engraved.  This
 page has been created in an effort to ensure you are not that student.  It is
 difficult to re-print certificates and even harder to re-engrave a plaque.');
 echo '<br /><br />';
 echo i18n('Your name is in the box below.  (If you have a partner, your
 partners name is also shown below).  This is EXACTLY how your name will appear 
 on any certificates, awards, or engraving.');
 echo '<br /><br />';
 echo i18n('Just to clarify, EXACTLY means EXACTLY.  We will not add upper-case
 letters if you typed your name in all lower-case.  We will not change letters
 to lower-case if you typed your name in all capitals. And we will not fix
 any spelling if there is a typo.  If your name appears incorrect, please visit 
 the %1Student Information%2 page and correct it. ', array(
 	'<a href="register_participants_students.php">', '</a>'));
 echo '<br /><br />';
	echo "<table class=\"summarytable\">";
 foreach($student_display_name AS $sn) 
	 echo "<tr><td><span style=\"font-size: 4.0em; font-weight: bold\">&nbsp;$sn&nbsp;</span></td></tr>";
 echo "</table>";
 echo '<br /><br />';
 echo i18n('Please confirm that:');
 echo '<br />';
 echo '<br />';
 echo "<form method=\"post\" action=\"register_participants_namecheck.php\">\n";
 echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";

 $ch = ($newstatus == 'complete') ? 'checked="checked"' : '';

 echo "<input type=\"checkbox\" name=\"spelling\" value=\"yes\" $ch /> ".i18n('My name is correctly spelled');
 echo '<br />';
 echo "<input type=\"checkbox\" name=\"caps\" value=\"yes\" $ch /> ".i18n('The correct letters are capitalized and in lower-case.');
 echo '<br />';
 echo "<input type=\"checkbox\" name=\"punc\" value=\"yes\" $ch /> ".i18n('Any required punctuation and accents are present and correct.');
 echo '<br />';
 echo '<br />';

 echo "<input type=\"submit\" value=\"".i18n("My Name is Correct")."\" />\n";
 echo "</form>";

 send_footer();
?>
