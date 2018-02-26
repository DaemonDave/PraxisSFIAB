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
 send_header("Judging Availability - Update",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judging Management' => 'admin/judges.php',
			'Judging Listing' => 'admin/judging_avail_list.php')
				);

	// get the current year
	$year=$config['FAIRYEAR'];
	// set default values
	$score = 0;
	$selectedscorecard =0;
	
	// score entry phase - cascading ifs to eliminate error database conditions
	if(isset($_POST['submit']) ) // if we have a form
	{
     if(isset($_POST['selector']))
		 {
			 
			 $markforupdate=$_POST['selector'];
       $cnt=$_POST['count'.$markforupdate];   //REMARK: Would this work?
       //
       /// NO INPUTS UNTIL WORKING
       //
			 // if there is data and an entry selected, then insert data
       //$q=mysql_query("UPDATE scores SET score='$score' WHERE projectnumber='$projnumber' and year='$year' AND count='$cnt'  ");
			 // check for database error
				if ($q == FALSE)
				{
					  echo mysql_error();
				}
				else		 
					 message_push(happy("Judge ".$$cnt." selected, data inserted!!!!"));	

		 }
		 else// no score to input
		 {
					message_push(error("Scorecard has no judge, no data inserted!!!!"));	
		 }
		
	}// end of data insertion
	// query for scorecards
	$q=mysql_query("SELECT * FROM users_judge ORDER BY users_id");

	echo mysql_error();


	echo "<form action=\"judging_avail_list.php\" method=\"post\" accept-charset=\"UTF-8 ISO-8859-1\">";
	echo "<input type=\"hidden\" name=\"Judges Number\" value=\"" . mysql_num_rows($q) . "\"/>";
	echo "<table class=\"tableview\">";
	echo "<tr>";
	echo "<th>".i18n("Judge email")."</th>";
	echo "<th>".i18n("Judge UID")."</th>";
	echo "<th>".i18n("Availability")."</th>";
	echo "</tr>";

	$i = 1;
	echo "<div>";
	// loop through all scorecards
	while($r=mysql_fetch_object($q)) 
	{
		// create table header
		echo "<tr>\n";
		// find the judges email from the user DB using cross reference
		$j = mysql_query("SELECT email FROM users WHERE year='$year' and uid='Sr->users_id");
		$k = mysql_fetch_object($j);
		echo "<td style=\"vertical-align: middle\">\n";
		echo $k->email;
		echo "</td>\n";
		echo "<td style=\"vertical-align: middle\">\n";
		echo $r->users_id;
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
	echo "<input type=\"submit\" name=\"submit\" />\n";
	echo "</div>";
	echo "</form>\n";
	echo "<br />";
	echo "<p>Total Rows:".$i." </p>\n"


?>

<?
 send_footer();
?>
