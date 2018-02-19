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
 send_header("Judging Score Entry - Update",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judging Scorecard Review & Update' => 'admin/judging_score_edit.php')
				);

	// get the current year
	$year=$config['FAIRYEAR'];
	// set default values
	$score = 0;
	$selectedscorecard =0;
	
	// score entry phase - cascading ifs to eliminate error database conditions
	if(isset($_POST['submit']) ) // if we have a form
	{
		 // check entries for all values
		 //if(isset($_POST['score'])) // if there is a score
                 if(isset($_POST['selector']))
		 {
			 $markforupdate=$_POST['selector'];
                         $projnumber=$_POST['project'.$markforupdate.'number'];   //REMARK: Would this work?
			 $score = $_POST['score'.$markforupdate.'number'];        //REMARK: Would this work?
			 //$score = $_POST['score']; 
			 // if there is data and an entry selected, then insert data
			 // isset($_POST['selector']) && $score > 0 && 
			 //if(isset($_POST['count']))// if this isn't an accident
			 //{
				 // get the row count
				 //$count = $_POST['count'];
				 // inserting into column count only
				 //$q=mysql_query("UPDATE scores SET score='$score' WHERE count='$count' ");
			         $q=mysql_query("UPDATE scores SET score='$score' WHERE projectnumber='$projnumber' and year='$year' ");
				 // check for database error
				 if ($q == FALSE)
				 {
					  echo mysql_error();
				 }
				 else		 
					 //message_push(happy("Scorecard ".$count." selected, data inserted!!!!"));	
					 message_push(happy("Scorecard ".$projnumber." selected, data inserted!!!!"));	
			 //}
			 //else // improper selection
			 //{
					//$count = 0;
					//message_push(error("Scorecard not selected, no data inserted!!!!"));		
			 //}
		 }
		 else// no score to input
		 {
					//$count = 0;
					message_push(error("Scorecard has no score, no data inserted!!!!"));	
		 }
		 // reset the count to avoid accidently re-inserting later on
		 //$count = 0;
	
		
	}// end of data insertion
	// query for scorecards
	$q=mysql_query("SELECT * FROM scores WHERE year='$year' ORDER BY projectnumber");

	echo mysql_error();


	echo "<form action=\"judging_score_edit.php\" method=\"post\" accept-charset=\"UTF-8 ISO-8859-1\">";
	echo "<input type=\"hidden\" name=\"scorecards\" value=\"" . mysql_num_rows($q) . "\"/>";
	echo "<input type=\"hidden\" name=\"projectid\" value=\"$project_id\"/>";
	echo "<table class=\"tableview\">";
	echo "<tr>";
	echo "<th>".i18n("Project Number")."</th>";
	echo "<th>".i18n("Judge 1")."</th>";
	echo "<th>".i18n("Judge 2")."</th>";
	echo "<th>".i18n("Judge 3")."</th>";
	echo "<th>".i18n("Judge 4")."</th>";
	echo "<th>".i18n("Score")."</th>";	
	echo "<th>".i18n("Editable Score")."</th>";
	echo "<th>".i18n("Scorecard Count")."</th>";
	echo "<th>".i18n("Edit ")."</th>";
	echo "</tr>";

	$i = 1;
	echo "<div>";
	// loop through all scorecards
	while($r=mysql_fetch_object($q)) 
	{
		// create table header
		echo "<tr>\n";
		echo "<input type=\"hidden\" name=\"project" . $i. "number\" value=\"$r->projectnumber\"/>\n";
		echo "<td style=\"vertical-align: middle\">\n";
		echo $r->projectnumber;
		echo "</td>\n";
		echo "<td style=\"vertical-align: middle\" name=\"judge1" . $i. "id\">";
		echo $r->judge1;
		echo "</td>\n";
		echo "<td style=\"vertical-align: middle\" name=\"judge2" . $i. "id\">";
		echo $r->judge2;
		echo "</td>\n";
		echo "<td style=\"vertical-align: middle\" name=\"judge3" . $i. "id\">";
		echo $r->judge3;
		echo "</td>\n";
		echo "<td style=\"vertical-align: middle\" name=\"judge4" . $i. "id\">";
		echo $r->judge4;
		echo "</td>\n";
		echo "<td style=\"vertical-align: middle; text-align: center\">\n";
		if($r->score) 
		{
			echo $r->score;
		}
		else
		{
			echo "None";
		}
		echo "\n</td>\n";
		echo "<td style=\"vertical-align: middle; text-align: center\">\n";
		echo "<input type=\"text\" size=\"6\" maxlength=\"6\" name=\"score".$i."number\" value=\"$r->score\"/>\n";
		echo "</td>\n";
		echo "<td style=\"vertical-align: middle; text-align: center\">\n";
		echo "<input type=\"hidden\" name=\"count".$i."\" value=\"$r->count\"/>\n";
		echo $r->count;		
		echo "</td>\n";
		echo "<td style=\"vertical-align: middle; text-align: center\">\n";
		echo "<input id=\"radio".$i."\" type=\"radio\" name=\"selector\" value=\"$i\" />\n";
		echo "</td>\n";
		echo "</tr>\n";
		$i++;
	}
	echo "</div>";	
	echo "</table>\n";
	echo "<br />";	
	echo "<div>";
	echo "<input type=\"submit\" />\n";
	echo "</div>";
	echo "</form>\n";
	echo "<br />";
	echo "<p>Total Rows:".$i." Last Score:".$score." </p>\n"
	// Project Average Score logic goes here:


?>

<?
 send_footer();
?>
