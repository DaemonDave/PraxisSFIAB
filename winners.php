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
 require("projects.inc.php");

 send_header("Winners");

if($_GET['edit']) $edit=$_GET['edit'];
if($_POST['edit']) $edit=$_POST['edit'];

if($_GET['action']) $action=$_GET['action'];
if($_POST['action']) $action=$_POST['action'];

if($_GET['year'] && $_GET['type'])
{
	$show_unawarded_awards="no";
	$show_unawarded_prizes="no";

	echo "<h2>".i18n("%1 %2 Award Winners",array($_GET['year'],$_GET['type']))."</h2>";
	$year=$_GET['year'];

	$ok=true;
	//first, lets make sure someone isnt tryint to see something that they arent allowed to!
	//but only if the year they want is the FAIRYEAR.  If they want a past year, thats cool
	if($_GET['year']>=$config['FAIRYEAR']) {
		$q=mysql_query("SELECT (NOW()>'".$config['dates']['postwinners']."') AS test");
		$r=mysql_fetch_object($q);
		if($r->test!=1)
		{
			echo error(i18n("Crystal ball says future is very hard to see!"));
			$ok=false;
		}
	}

	if($ok)
	{
	
		$q=mysql_query("SELECT 
					award_awards.id,
					award_awards.name,
					award_awards.order AS awards_order,
					award_types.type
				FROM 
					award_awards,
					award_types
				WHERE 
						award_awards.year='$year'
					AND	award_awards.award_types_id=award_types.id
					AND	award_types.type='".$_GET['type']."'
					AND	award_types.year='$year'
				ORDER BY 
					awards_order");

		echo mysql_error();

		if(mysql_num_rows($q))
		{
			echo "<a href=\"winners.php\">".i18n("Back to Winners main page")."</a>";
			echo "<br />";
			while($r=mysql_fetch_object($q))
			{
				$pq=mysql_query("SELECT 
							award_prizes.prize,
							award_prizes.number,
							award_prizes.id,
							award_prizes.cash,
							award_prizes.scholarship,
							winners.projects_id,
							projects.projectnumber,
							projects.title,
							projects.registrations_id AS reg_id
						FROM 
							award_prizes 
							LEFT JOIN winners ON winners.awards_prizes_id=award_prizes.id
							LEFT JOIN projects ON projects.id=winners.projects_id
						WHERE 
							award_awards_id='$r->id' 
							AND award_prizes.year='$year'
						ORDER BY 
							`order`");
						echo mysql_error();
				$awarded_count = 0;
				if($show_unawarded_awards=="no")
				{
					while($pr=mysql_fetch_object($pq))
					{
						if($pr->projectnumber)
						{
							$awarded_count++;
						}
					}
					mysql_data_seek($pq, 0);
				}
				if($show_unawarded_awards=="yes" || $awarded_count > 0)
				{
					echo "<h3>".i18n($r->name)."</h3> \n";
				}

				$prevprizeid=-1;
				while($pr=mysql_fetch_object($pq))
				{
					if(!($pr->projectnumber) && $show_unawarded_prizes=="no") 
					{ 
						continue;
					}
					if($prevprizeid!=$pr->id)
					{
						echo "&nbsp;";
						echo "&nbsp;";
						echo "<b>";
						echo i18n($pr->prize);
						if(($pr->cash || $pr->scholarship) && $config['winners_show_prize_amounts'] == 'yes')
						{
							echo " (";	
							if($pr->cash && $pr->scholarship)
								echo i18n("\$%1 cash / \$%2 scholarship",array($pr->cash,$pr->scholarship),array("Cash dollar value","Scholarship dollar value"));
							else if($pr->cash)
								echo i18n("\$%1 cash",array($pr->cash),array("Cash dollar value"));
							else if($pr->scholarship)
								echo i18n("\$%1 scholarship",array($pr->scholarship),array("Scholarship dollar value"));
							echo ")";
						
						}
						echo "</b>";
						echo "<br />";
						$prevprizeid=$pr->id;
					}

					if($pr->projectnumber)
					{
						echo "&nbsp&nbsp;&nbsp;&nbsp;";
						echo "($pr->projectnumber) $pr->title";

						$sq=mysql_query("SELECT students.firstname,
									students.lastname,
									students.schools_id,
									students.webfirst,
									students.weblast,
									students.webphoto,
									schools.school
								FROM
									students,
									schools
								WHERE
									students.registrations_id='$pr->reg_id'
									AND students.schools_id=schools.id
								");

						$studnum=0;
						$students="";
                        $schools=array();
						while($studentinfo=mysql_fetch_object($sq))
						{
							if($studnum>0 && $prev) $students.=", ";

							if($studentinfo->webfirst=="yes")
								$students.="$studentinfo->firstname ";
							if($studentinfo->weblast=="yes")
								$students.="$studentinfo->lastname ";
							if($r->studentinfo->webfirst=="yes" || $studentinfo->weblast=="yes") 
								$prev=true;
							else
								$prev=false;

//							$students.="$studentinfo->firstname $studentinfo->lastname";
							$studnum++;
							
							//we will assume that they are coming from the same school, so lets just grab the last students school
							//and use it.
                            if(!in_array($studentinfo->school,$schools)) 
                                $schools[]=$studentinfo->school;

//							$school=$studentinfo->school;
						}
						echo "<br />";
						echo "&nbsp&nbsp;&nbsp;&nbsp;";
						echo "&nbsp&nbsp;&nbsp;&nbsp;";
						if($studnum > 1)
							echo i18n("Students").": $students";
						else 
							echo i18n("Student").": $students";

						echo "<br />";
						echo "&nbsp&nbsp;&nbsp;&nbsp;";
						echo "&nbsp&nbsp;&nbsp;&nbsp;";
						echo i18n("School").": ";
                        $schoollist="";
                        foreach($schools AS $school) $schoollist.=$school.", ";
                        $schoollist=substr($schoollist,0,-2);
                        echo $schoollist;
						echo "<br />";
					}
					else 
					{
						echo "&nbsp&nbsp;&nbsp;&nbsp;";
						echo i18n("Prize not awarded");
						echo "<br />";
					}
					echo "<br />";
				}
			}

		}
	}
}
else
{
	$q=mysql_query("SELECT 
				DISTINCT(winners.year) AS year, 
				dates.date 
			FROM 
				winners,
				dates 
			WHERE 
				winners.year=dates.year
				AND dates.name='postwinners'
				AND dates.date<=NOW()
			ORDER BY 
				year DESC");
	$first=true;
	if(mysql_num_rows($q))
	{
		while($r=mysql_fetch_object($q))
		{
			if($first && $r->year != $config['FAIRYEAR'])
			{
				list($d,$t)=split(" ",$config['dates']['postwinners']);
				echo "<h2>".i18n("%1 Winners",array($config['FAIRYEAR']))."</h2>";
				echo i18n("Winners of the %1 %2 will be posted here on %3 at %4",array($config['FAIRYEAR'],$config['fairname'],format_date($d),format_time($t)));
                echo "<br />\n";
                echo "<br />\n";
				$first=false;
			}
			//get the "winnersposted" date for the year, and make
			echo "<h2>".i18n("%1 Winners",array($r->year))."</h2>";

			//do this each time, because each year the names of the award types could change, along with what is actually given out.
			//
			$tq=mysql_query("SELECT 
						DISTINCT(award_types.type) AS type
					FROM
						winners,
						award_types,
						award_awards,
						award_prizes
					WHERE
						award_awards.award_types_id=award_types.id
						AND winners.awards_prizes_id=award_prizes.id
						AND award_prizes.award_awards_id=award_awards.id
						AND winners.year='$r->year'
					ORDER BY
						award_types.order
					");
					echo mysql_error();
			while($tr=mysql_fetch_object($tq))
			{
				echo "&nbsp;&nbsp;<a href=\"winners.php?year=$r->year&type=$tr->type\">".i18n("%1 %2 award winners",array($r->year,$tr->type))."</a><br />";
			}
			echo "<br />";
			$first=false;
		}
	}
	else
	{
		list($d,$t)=split(" ",$config['dates']['postwinners']);
		echo "<h2>".i18n("%1 Winners",array($config['FAIRYEAR']))."</h2>";
		echo i18n("Winners of the %1 %2 will be posted here on %3 at %4",array($config['FAIRYEAR'],$config['fairname'],format_date($d),format_time($t)));
	}

}

	send_footer();

?>
