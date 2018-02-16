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
 send_header("Participant Registration - Tour Information");

 echo "<a href=\"register_participants_main.php\">&lt;&lt; ".i18n("Back to Participant Registration Summary")."</a><br />";
 echo "<br />";

 if($_POST['action']=="save")
 {
//	if(registrationFormsReceived())
//	{
//		echo error(i18n("Cannot make changes to forms once they have been received by the fair"));
//	}
//	else 
	if(registrationDeadlinePassed())
	{
		echo error(i18n("Cannot make changes to forms after registration deadline"));
	}
	else
	{
		//first we will delete all their old answer, its easier to delete and re-insert in this case then it would be to find the corresponding answers and update them
		mysql_query("DELETE FROM tours_choice 
				WHERE registrations_id='{$_SESSION['registration_id']}' 
				AND year='{$config['FAIRYEAR']}' 
				AND rank!='0'");
		if(is_array($_POST['toursel']))
		{
			foreach($_POST['toursel'] AS $students_id=>$ts)
			{
				$selarray = array();
				
				foreach($ts AS $rank=>$tid) {
					if($tid == -1) continue;

					$rank = intval($rank);

					$x = intval($tid);

					/* If this choice has already been selected, don't record it */
					if(in_array($x, $selarray)) continue;
					
					/* Remember this choice in a format that is easily searchable */
					$selarray[] = $x;

					mysql_query("INSERT INTO tours_choice (registrations_id,students_id,tour_id,year,rank) VALUES (".
						"'".$_SESSION['registration_id']."', ".
						"'".intval($students_id)."', ".
						"'".intval($tid)."', ".
						"'".$config['FAIRYEAR']."', ".
						"'$rank')");
						echo mysql_error();
				}

			}
		}
	}	
 }

/*
 if($_POST['action']=="volunteer") {
 	$vname = mysql_escape_string(stripslashes($_POST['vname']));
	$vemail =  mysql_escape_string(stripslashes($_POST['vemail']));
 	mysql_query("INSERT INTO tours_volunteers (registrations_id,name,email,year) VALUES (".
		"'".$_SESSION['registration_id']."', ".
		"'".$vname."', ".
		"'".$vemail."', ".
		"'".$config['FAIRYEAR']."'); ");
	echo happy(i18n("Tour volunteer added.  They will be contacted soon."));
		
  }
*/

//output the current status
$newstatus=tourStatus();
if($newstatus!="complete")
{
	echo error(i18n("Tour Selection Incomplete.  You must select your tour preferences!"));
}
else if($newstatus=="complete")
{
	echo happy(i18n("Tour Selection Complete"));

}


 $assigned_tour = array();
 $q=mysql_query("SELECT * FROM tours_choice WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
 while($r=mysql_fetch_object($q))
 {
 	if($r->rank == 0) $assigned_tour[$r->students_id] = $r->tour_id;
	$tour_choice[$r->students_id][$r->rank] = $r->tour_id;
 }

 $tours = array();
 $q=mysql_query("SELECT * FROM tours WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
 if(mysql_num_rows($q) == 0)
 {
 	echo notice(i18n("There is not tour information"));
	send_footer(); 
	exit;
 }

 while($r=mysql_fetch_object($q))
 {
	$tours[$r->id]['name'] = $r->name;
	$tours[$r->id]['num'] = $r->num;
	$tours[$r->id]['description'] = $r->description;
	$tours[$r->id]['capacity'] = $r->capacity;
	$tours[$r->id]['grade_min'] = $r->grade_min;
	$tours[$r->id]['grade_max'] = $r->grade_max;
 }
 

 $min = $config['tours_choices_min'];
 $max = $config['tours_choices_max'];

 echo "<form method=\"post\" action=\"register_participants_tours.php\">\n";
 echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";

 $q=mysql_query("SELECT * FROM students WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
 $num_found = mysql_num_rows($q);

 $print_submit = false;
 while($r=mysql_fetch_object($q)) {

	echo i18n("Tour Selection for")." <b>{$r->firstname} {$r->lastname}</b>:<br /><br />";
	if($r->grade <= 0) {
		echo error(i18n("You must select your grade on the Student Information page before selecting tours"));
		echo i18n("Go to the"). " <a href=\"register_participants_students.php\">";
		echo i18n("Student Information");
	        echo "</a>". i18n(" page now."). "<br /><br />";
		continue;
	}

	if(array_key_exists($r->id, $assigned_tour)) {
		echo happy(i18n('You have been assigned to a tour.  Tour selection is disabled.'));
		$t = $tours[$assigned_tour[$r->id]];
		echo i18n("Your Tour").": <b>#{$t['num']}: ".i18n($t['name'])."</b><br />";
	 	echo '<span style="font-size: 0.8em;">'.i18n($t['description'])."</span><br /><br />";
		continue;
	}
	$print_submit = true;


	echo "<table>\n";
	for($x=0;$x<$max;$x++) {
		echo "<tr><td align=right>";

		$rank = $x+1;
		if($x==0) echo i18n("(most preferred)")." ";
		echo i18n("Choice")." $rank:";
		echo "</td><td>";
		echo "<select name='toursel[{$r->id}][$rank]'>";
		echo "<option $sel value=\"-1\">".i18n("Choose")."</option>";
		foreach($tours as $id=>$t) {
			$sel = "";
			
			/* Don't show this tour as an option if the student is outside the grade range */
			if($r->grade < $t['grade_min'] || $r->grade > $t['grade_max']) continue;
			
			if($tour_choice[$r->id][$rank] == $id) 
				$sel = "selected=selected";
			echo "<option $sel value=\"$id\">{$t['name']}</option>";
		}
		echo "</select>";
		
		echo "</td></tr>";
	}
	echo "<tr><td>&nbsp;</td><td></td></tr>";
 	echo ("</table>");
 }

		/*
		$rank = $tour_choice[$r->id];
		if($rank < 1 || $rank > $max) $rank = "--";
	 	echo "<tr><td><input type=\"text\" size=2 name=\"tours[$r->id]\" value=\"$rank\" />";
		echo "<td>(tour&nbsp;$num). </td><td>";
		echo i18n($r->description)."</td>";
		echo "</tr>";
		$num++;
		*/
 if($print_submit == true) {
	echo "<input type=\"submit\" value=\"".i18n("Save Tour Choices")."\" />\n";
 }
 echo "</form>";


 echo "<br /><br />";
 echo "<h4>".i18n("Tour Descriptions")."</h4><br />";

 	/* Dump the tours */
	 foreach($tours as $id=>$t) {
	 	echo i18n("Tour")." <b>#{$t['num']}</b>: <b>".i18n($t['name'])."</b><br />";
	 	echo i18n("Grade").": <b>".$t['grade_min']." - ".$t['grade_max']."</b>";
	//	echo i18n(",  Capacity").": <b>".$t['capacity']."</b> ".i18n("students");
		echo "<br />";
	 	echo '<span style="font-size: 0.8em;">'.i18n($t['description'])."</span><br /><br />";

	 }
	 	

/*
	 echo "<h3>Add A Parent/Teacher Volunteer for Tour Day </h3><br />";
	 echo "If you have a parent / teacher who would like to accompany a group of students on a UBC tour (ask your parents right now and see if they want to volunteer on a tour!), please enter their name and email address below, and click on the Submit Volunteer button.  The GVRSF will contact them by email to confirm additional details.";
	 echo "<form method=\"post\" action=\"register_participants_tours.php\">\n";
	 echo "<input type=\"hidden\" name=\"action\" value=\"volunteer\">\n";
	 echo "Name: <input type=\"text\" name=\"vname\" /><br />";
	 echo "Email: <input type=\"text\" name=\"vemail\" /><br />";
	 echo "<input type=\"submit\" value=\"".i18n("Submit Volunteer")."\" />\n";
	 echo "</form><br />";
*/
 send_footer();
?>
