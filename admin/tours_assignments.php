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
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');

 /* Load Tours */
 $query = "SELECT * FROM tours WHERE 
		year='{$config['FAIRYEAR']}'";
 $r = mysql_query($query);
 $tours = array();
 while($i = mysql_fetch_object($r)) {
	$tours[$i->id]['name'] = $i->name;
	$tours[$i->id]['num'] = $i->num;
 }

 if($_GET['action']=='info') {
	$sid = intval($_GET['id']);

	$query="SELECT * FROM students WHERE id='$sid'
			AND year='{$config['FAIRYEAR']}'";
	$r = mysql_query($query);
	$i = mysql_fetch_object($r);

	send_popup_header(i18n('Student Tour Rank Information - %1 %2',
				array($i->firstname, $i->lastname)));
	$query="SELECT * FROM tours_choice
				WHERE students_id='$sid'
				AND year='{$config['FAIRYEAR']}'
				ORDER BY rank";
	$r = mysql_query($query);
	echo '<table>';
	$count = mysql_num_rows($r);
	while($i = mysql_fetch_object($r)) {
		echo '<tr><td align="right">';
		if($i->rank == 0) {
			echo '<b><nobr>'.i18n('Current Assigned Tour').':</nobr></b>';
			echo '</td><td colspan=\"2\">';
			echo "#{$tours[$i->tour_id]['num']}: {$tours[$i->tour_id]['name']}";
			echo '<br /><br />';
			$count--;
		} else {
			echo '<b>'.i18n('Tour Preference %1',
						array($i->rank)).':</b>';
			echo '</td><td>';
			echo "#{$tours[$i->tour_id]['num']}: {$tours[$i->tour_id]['name']}";
			echo '</td><td>';
			if($i->rank == 1) 
				echo i18n('(Most Preferred)');
			if($i->rank == $count) 
				echo i18n('(Least Preferred)');
		}
		echo '</td></tr>';
	}
	echo '</table>';

	send_popup_footer();
	exit;
 }

 send_header("Tour Assignments",
                array('Committee Main' => 'committee_main.php',
                        'Administration' => 'admin/index.php',
                        'Tours' => 'admin/tours.php')
                        );
?>
<script language="javascript" type="text/javascript">
var infowindow = '';
function addbuttonclicked(id)
{
	document.forms.tours.action.value="add";
	document.forms.tours.tours_id.value=id;
	document.forms.tours.submit();
}

function openinfo(id)
{
	if(id)
		currentid=id;
	else
		currentid=document.forms.tours["studentlist[]"].options[document.forms.tours["studentlist[]"].selectedIndex].value;

	infowindow = window.open("tours_assignments.php?action=info&id="+currentid,"StudentTourRankInformation","location=no,menubar=no,directories=no,toolbar=no,width=770,height=300,scrollbars=yes"); 
	return false;

}

function switchinfo()
{
	if(document.forms.tours["studentlist[]"].selectedIndex != -1)
	{
		currentname=document.forms.tours["studentlist[]"].options[document.forms.tours["studentlist[]"].selectedIndex].text;
		currentid=document.forms.tours["studentlist[]"].options[document.forms.tours["studentlist[]"].selectedIndex].value;

		document.forms.tours.studentinfobutton.disabled=false;
		document.forms.tours.studentinfobutton.value=currentname;

		if(!infowindow.closed && infowindow.location) {
			openinfo();
		}
	}
	else
	{
		document.forms.tours.studentinfobutton.disabled=true;
		document.forms.tours.studentinfobutton.value="<? echo i18n("Student Tour Rank Info")?>";
	}

}

</script>

<?



	$tours_id = intval($_POST['tours_id']);
	$student_list = is_array($_POST['studentlist']) ? $_POST['studentlist'] : array();

	if($_POST['action']=='add' && $tours_id != 0 && count($student_list)>0) {
		// make sure the tour is valid
		if(!array_key_exists($tours_id, $tours)) {
			/* Someone is hacking the POST */
			echo "HALT: Tour list changed between the form and the POST.";
			exit;
		}

		$added=0;
		foreach($student_list AS $sid) {
			/* Make sure the student exists */
			$sid = intval($sid);

			$q = mysql_query("SELECT registrations_id FROM students 
					WHERE id='$sid'");
			$i = mysql_fetch_object($q);
			$rid = $i->registrations_id;

			/* Delete any old linking */
			mysql_query("DELETE FROM tours_choice WHERE
					students_id='$sid' AND
					year='{$config['FAIRYEAR']}' AND
					rank='0'");
			/* Connect this student to this tour */
			mysql_query("INSERT INTO tours_choice 
					(`students_id`,`registrations_id`,
						`tour_id`,`year`,`rank`)
					VALUES (
					'$sid', '$rid', '$tours_id',
					'{$config['FAIRYEAR']}','0')");
			$added++;
		}
		if($added==1) $j=i18n("student");
		else $j=i18n("students");

		echo happy(i18n("%1 %2 added to tour #%3 (%4)",array(
				$added,$j,$tours[$tours_id]['num'],$tours[$tours_id]['name'])));
	}

	$tours_id = intval($_GET['tours_id']);
	$students_id = intval($_GET['students_id']);

	if($_GET['action']=='del' && $tours_id>0 && $students_id>0) {
		mysql_query("DELETE FROM tours_choice 
				WHERE students_id='$students_id' 
				AND year='{$config['FAIRYEAR']}'
				AND rank='0'");

		echo happy(i18n("Removed student from tour #%1 (%2)",array($tours[$tours_id]['num'],$tours[$tours_id]['name'])));

	}

	if($_GET['action']=="empty" && $tours_id>0)
	{
		mysql_query("DELETE FROM tours_choice WHERE 
				tour_id='$tours_id'
				AND year='{$config['FAIRYEAR']}'
				AND rank='0'");
		echo happy(i18n("Emptied all students from tour #%1 (%2)",array($tours[$tours_id]['num'],$tours[$tours_id]['name'])));
	}


	if(!$_SESSION['viewstate']['students_teams_list_show'])
		$_SESSION['viewstate']['students_teams_list_show']='unassigned';
	//now update the students_teams_list_show viewstate
	if($_GET['students_teams_list_show'])
		$_SESSION['viewstate']['students_teams_list_show']=$_GET['students_teams_list_show'];

	echo "<form name=\"tours\" method=\"post\" action=\"tours_assignments.php\">";
	echo "<input type=\"hidden\" name=\"action\">";
	echo "<input type=\"hidden\" name=\"tours_id\">";
	echo "<input type=\"hidden\" name=\"students_id\">";
	echo "<table>";
	echo "<tr>";
	echo "<th>".i18n("Student List");
	echo "<br />";
	echo "<input disabled=\"true\" name=\"studentinfobutton\" id=\"studentinfobutton\" onclick=\"openinfo()\" type=\"button\" value=\"".i18n("Student Rank Info")."\">";
	echo "</th>";
	echo "<th>".i18n("Tours")."</th>";
	echo "</tr>";
	echo "<tr><td valign=\"top\">";

	/* Load students with the current tour selections 
	 * (rank=0), or if there is no selection, make
	  * rank NULL, and tours_id NULL */
	$querystr="SELECT 	students.firstname, students.lastname,
				students.id,
				tours_choice.tour_id, tours_choice.rank
			FROM 
				students
				LEFT JOIN tours_choice ON (tours_choice.students_id=students.id AND tours_choice.rank=0)
				LEFT JOIN registrations ON registrations.id=students.registrations_id
			WHERE 
				students.year='{$config['FAIRYEAR']}' AND
				(tours_choice.year='{$config['FAIRYEAR']}' OR
				 tours_choice.year IS NULL) AND
				registrations.status='complete'
			ORDER BY 
				students.lastname,
				students.firstname,
				tours_choice.rank";

	$q=mysql_query($querystr);

	echo mysql_error();

	$student = array();
	$last_student_id = -1;
	while($r=mysql_fetch_object($q))
	{
		$id = $r->id;
		$tours_id = $r->tour_id;
		$rank = $r->rank;

		if($id != $last_student_id) {
			$last_student_id = $id;
			
			$student[$id]['name'] = $r->firstname.' '.$r->lastname;
		}
		if($tours_id != NULL) {
			$tours[$tours_id]['students'][] = $id;
			$student[$id]['tours_id'] = $tours_id;
		}
	}

//rint_r($student);
	echo "<select name=\"studentlist[]\" onchange=\"switchinfo()\" multiple=\"multiple\" style=\"width: 250px; height: 600px;\">";
	foreach($student as $id=>$s) {
		if($s['tours_id'] != 0) continue;
		echo "<option value=\"$id\">{$s['name']}</option>\n";
	}
	echo "</select>";
	echo "</td>";
	echo "<td valign=\"top\">";

	echo "<table width=\"100%\">";
	foreach($tours as $tid=>$t) {
		echo '<tr><td colspan=\"3\"><hr />';

		echo "<input onclick=\"addbuttonclicked('$tid')\" type=\"button\" value=\"Add &gt;&gt;\">";
		echo "&nbsp;#{$t['num']}: {$t['name']} ";
		echo '</td></tr>';
		$x = 0;
		if(is_array($t['students']) ) {
			foreach($t['students'] AS $sid) {
				$s = $student[$sid];
				if($x == 0) echo '<tr>';
				echo '<td>';
				echo "<a href=\"tours_assignments.php?action=del&tours_id=$tid&students_id=$sid\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
				echo '&nbsp;';
				echo "<a href=\"\" onclick=\"return openinfo($sid);\">";
				echo "{$s['name']}</a>";
				echo "</td>";
				if($x==2) {
					echo '</tr>';
					$x = 0;
				} else 
					$x++;
			}
			if($x != 0) echo '</tr>';
			echo "<tr><td colspan=\"3\">";
			echo "<a onclick=\"return confirmClick('Are you sure you want to empty all students from this tour?')\" href=\"tours_assignments.php?action=empty&tours_id=$tid\">";
			echo " ".i18n("Empty All Members")." ";
			echo "<img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\">";
			echo "</a>";
			echo "</td></tr>";
		}
		else
		{
			echo "<tr><td colspan=\"2\">";
			echo error(i18n("Tour has no members"),"inline");
			echo "</td></tr>";
		}

	}

		echo "</table>";

	echo "<br />";

	echo "</td></tr>";
	echo "</table>";
	echo "</form>";

	send_footer();

?>
