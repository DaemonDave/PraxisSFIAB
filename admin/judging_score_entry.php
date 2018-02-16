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
 require("../common.inc.php");
 require_once("../user.inc.php");
 require_once("judges.inc.php");
 user_auth_required('committee', 'admin');

 if($_POST['year']) $year=$_POST['year'];
 else $year=$config['FAIRYEAR'];

 // the global scorecard count
 global $scorecount;
//
/// MODIFIED DRE 2018
//
// action section of page - takes data and shows it for now
 if(isset($_POST['submit']) ) 
 {
	 // check entries for all values
	 if(isset($_POST['score']))
	 {
		$score = $_POST['score'];
	 }
	 else
	 {
		$score = 0;
	 }
	 if(isset($_POST['id']))
	 {
		$projectid = $_POST['id'];
	 }
	 else
	 {
		$projectid = 0;
	 }
	 if(isset($_POST['judge1']))
	 {
		$judge1 = $_POST['judge1'];
	 }
	 else
	 {
		$judge1 = 0;
	 }
	 if(isset($_POST['judge2']))
	 {
		$judge2 = $_POST['judge2'];
	 }
	 else
	 {
		$judge2 = 0;
	 }
	 if(isset($_POST['judge3']))
	 {
		$judge3 = $_POST['judge3'];
	 }
	 else
	 {
		$judge3 = 0;
	 }
	 if(isset($_POST['judge4']))
	 {
		$judge4 = $_POST['judge4'];
	 }
	 else
	 {
		$judge4 = 0;
	 }
		// insert into the scores table                                                                                   
		$q=mysql_query("INSERT INTO scores (year, projectnumber, judge1, judge2, judge3, judge4, score) VALUES ('$year','$projectid','$judge1','$judge2','$judge3','$judge4', '$score')");
		// test if successful so we know
		if($q == TRUE)
		{
			// alert user
			message_push(happy("DATA INSERTED INTO MYSQL SUCCESSFULLY"));
			$scorecount++;
			$sc = (string)$scorecount;
		}
		else// $q == FALSE
		{
			// alert user
			//echo mysql_error();
			message_push(error("DATA INSERT FAILED!!!!"));		
		}
	
 } 
 	// access projects under as arrays of array
	$i=1;
 	send_header("Judging Score Entry",
 			array('Committee Main' => 'committee_main.php',
				'Administration' => 'admin/index.php')
				);

//
/// MODIFICATION DRE 2018
//
		//start a table outside of array elements
		//&score={$score}&id={$r->projectnumber}&judge_id

		echo "<table class=\"tableview\">";
		echo "<tr>";
		echo "<th>".i18n("          Project Name                     ")."</th>";
		echo "<th>".i18n("    Project Num     ")."</th>";
		echo "<th>".i18n("  Judged Times   ")."</th>";
		echo "<th>".i18n("   Current Score       ")."</th>";
		echo "<th>".i18n("   Last Score ")."</th>";
		echo "</tr>";
	// access projects id's only from the projects table
	// SELECT projectnumber FROM `projects` where year='2018' order by id
	$q=mysql_query("SELECT title,projectnumber FROM projects WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
	while($r=mysql_fetch_object($q))
	{

		echo mysql_error();
		// logic for a table row
		echo "<tr>";
		echo "<th>".$r->title."</th>";
		echo "<th>".$r->projectnumber."</th>";
		echo "<th>".$r->projectnumber."</th>";
		//echo "<th><input type=\"text\" size=\"5\" maxlength=\"5\" name=\"score\" value=\"$score\"/></th>";
		echo "<th>Score Input</th>";
		echo "<th>".$scorecount."</th>";	
		$i++;
	}// end while
	echo "</table>\n";
	echo "<br />";	

	echo "<form  action=\"judging_score_entry.php\" method=\"post\">";
	echo "<table class=\"tableview\">";
	echo "<tr><td>Judge #1: </td><td><input type=\"text\" value=\"0\" name=\"judge1\"></td></tr>";
	echo "<tr><td>Judge #2: </td><td><input type=\"text\" value=\"0\" name=\"judge2\"></td></tr>";
	echo "<tr><td>Judge #3: </td><td><input type=\"text\" value=\"0\" name=\"judge3\"></td></tr>";
	echo "<tr><td>Judge #4: </td><td><input type=\"text\" value=\"0\" name=\"judge4\"></td></tr>";
	echo "<tr><td>Project Number: </td><td><input type=\"text\" value=\"1\"  name=\"id\"></td></tr>";
	echo "<tr><td>Score:  </td><td><input type=\"text\" value=\"1\" name=\"score\"></td></tr>";
	echo "<tr><td><input type=\"submit\" name=\"submit\" title=\"Enter Score\"></td>".$sc."<td></td></tr>";
	echo "</table>";
	echo "</form>";

?>

<?
 send_footer();
?>
