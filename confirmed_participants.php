<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2007 James Grant <james@lightbox.org>

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

 send_header("Confirmed Participants");

	//first, lets make sure someone isnt tryint to see something that they arent allowed to!
	$q=mysql_query("SELECT (NOW()>'".$config['dates']['postparticipants']."') AS test");
	$r=mysql_fetch_object($q);
	if($r->test!=1)
	{
		list($d,$t)=split(" ",$config['dates']['postparticipants']);
		echo i18n("Confirmed participants (that signature forms have been received for) will be posted here on %1 at %2.  Please do not contact the fair to inquire about receipt of your signature form until after this date (and only if you are not listed here after this date).",array($d,$t));
	}
	else
	{
		$q=mysql_query("SELECT  registrations.id AS reg_id,
					registrations.status,
					registrations.email,
					projects.title,
					projects.projectnumber,
					projects.projectcategories_id,
					projects.projectdivisions_id,
					projectcategories.category,
					projectdivisions.division
					
				FROM
					registrations
					LEFT JOIN projects on projects.registrations_id=registrations.id
					LEFT JOIN projectcategories ON projectcategories.id=projects.projectcategories_id
					LEFT JOIN projectdivisions ON projectdivisions.id=projects.projectdivisions_id
				WHERE
					1
					AND registrations.year='".$config['FAIRYEAR']."' 
					AND projectcategories.year='".$config['FAIRYEAR']."' 
					AND projectdivisions.year='".$config['FAIRYEAR']."' 
					AND (status='complete' OR status='paymentpending')
				ORDER BY
					projectcategories.id,
					projectdivisions.id,
					projects.projectnumber
				");
			echo mysql_error();
		
		$lastcat="something_that_does_not_exist";
		$lastdiv="something_that_does_not_exist";
		echo i18n("The following is a list of all confirmed participants that the signature form has been received for.  If you think you registered but you are not on this list, you should contact the %1 immediately.",array($config['fairname']))."<br />";
		if($config['regfee']>0)
		{
			echo "<br />";
			echo "<font color=\"red\">*</font>".i18n(" indicates payment was not received with the signature form.");
			echo "<br />";
			echo "<br />";
		}
		echo "<table style=\"font-size: 0.9em;\">";
		while($r=mysql_fetch_object($q))
		{
			if($r->category != $lastcat)
			{
				echo "<tr><td colspan=\"3\">";
				if($lastcat!="something_that_does_not_exist")
					echo "<br /><br />";
				echo "<h3>$r->category</h3>";
				echo "</td></tr>";
				$lastcat=$r->category;

				//anytime the age category changes, we want to re-force it to display the division again
				$lastdiv="something_that_does_not_exist";
			}
			if($r->division != $lastdiv)
			{
				echo "<tr><td colspan=\"3\">";
				if($lastdiv!="something_that_does_not_exist")
					echo "<br />";
				echo "<h4>$r->division</h3>";
				echo "</td></tr>";
				$lastdiv=$r->division;

			}

			//no need to output the status if we dont have a reg fee, becuase status is either 'complete' or 'payment pending' but if we dont have a regfee it can never be payment pending, so thus, it must be complete!
			$statusstar="";
			if($config['regfee']>0) {
				if($r->status=="paymentpending")
					$statusstar="<font color=\"red\">*</font>";

//				$status_text=i18n("Complete");
			}
			else
				$status_text="";

			echo "<tr>";
			echo "<td>$status_text".$statusstar."</td>";
			echo "<td>$r->projectnumber</td>";
			echo "<td>$r->title</td>";

			$sq=mysql_query("SELECT students.firstname,
						students.lastname,
						students.id,
						students.webfirst,
						students.weblast,
						schools.school
					FROM
						students,schools
					WHERE
						students.registrations_id='$r->reg_id'
						AND
						students.schools_id=schools.id
					");
					echo mysql_error();

			$studnum=1;
			$schools="";
			$students="";
			$sameschools=true;
			$lastschool="";
			while($studentinfo=mysql_fetch_object($sq))
			{
				if($studentinfo->webfirst=="yes")
					$students.="$studentinfo->firstname ";
				if($studentinfo->weblast=="yes")
					$students.="$studentinfo->lastname ";
				if($r->studentinfo->webfirst=="yes" || $studentinfo->weblast=="yes") $students.="<br />";

				$schools.="$studentinfo->school <br />";
				if($lastschool)
				{
					if($lastschool!=$studentinfo->school)
						$sameschools=false;
				}
				$lastschool=$studentinfo->school;
				$stats_totalstudents++;
			}
			if($sameschools) $schools=$lastschool;
			echo "<td>$schools</td>";
			echo "<td>$students</td>";
			echo "</tr>";
		}
		echo "</table>\n";
		echo "<br />";
	
	}

send_footer();

?>
