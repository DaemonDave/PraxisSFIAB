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
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');
 require("../lpdf.php");
 require("../lcsv.php");
 require("judges.inc.php");

 $type=$_GET['type'];

	if($type=="pdf")
	{

		$rep=new lpdf(	i18n($config['fairname']),
				i18n("Judging Team Project Assignments"),
				$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
				);

		$rep->newPage();
		$rep->setFontSize(11);
	}
	else if($type=="csv")
	{
		$rep=new lcsv(i18n("Judging Team Project Assignments"));
	}

	$teams=getJudgingTeams();

	$q=mysql_query("SELECT DISTINCT(date) AS d FROM judges_timeslots WHERE year='".$config['FAIRYEAR']."'");
	if(mysql_num_rows($q)>1)
		$show_date=true;
	else
		$show_date=false;

	foreach($teams AS $team)
	{
		$table=array();
		$table['header']=array(i18n("Timeslot"),i18n("Proj #"),i18n("Project Title"));
		if($show_date)
			$table['widths']=array(		2.25,		0.75, 		4.00);
		else
			$table['widths']=array(		1.5,		0.75, 		4.75);

		$table['dataalign']=array("center","center","left");

		$rep->heading($team['name']." (".$team['num'].")");

		$memberlist="";
		if(count($team['members']))
		{
			foreach($team['members'] AS $member)
			{
				$memberlist.=$member['firstname']." ".$member['lastname'];
				if($member['captain']=="yes")
					$memberlist.="*";
				$memberlist.=", ";
			}
			$memberlist=substr($memberlist,0,-2);
		}
		$rep->addText($memberlist);

		if(count($team['awards']))
		{
			$rep->heading(i18n("Awards that this team judges").":");
			foreach($team['awards'] AS $award)
			{
				$rep->addText($award['name']);
				$rep->addText(i18n("Criteria").": ".$award['criteria']);

				//get category eligibility
				$q=mysql_query("SELECT projectcategories.category FROM projectcategories, award_awards_projectcategories WHERE award_awards_projectcategories.projectcategories_id=projectcategories.id AND award_awards_projectcategories.award_awards_id='{$award['id']}' AND award_awards_projectcategories.year='{$config['FAIRYEAR']}' AND projectcategories.year='{$config['FAIRYEAR']}' ORDER BY category");
				echo mysql_error();
				$cats="";
				while($r=mysql_fetch_object($q))
				{
					if($cats) $cats.=", ".i18n($r->category);
					else $cats=i18n($r->category);
				}
				$rep->addText(i18n("Categories").": $cats");


				//get division eligibility
				$q=mysql_query("SELECT projectdivisions.division_shortform FROM projectdivisions, award_awards_projectdivisions WHERE award_awards_projectdivisions.projectdivisions_id=projectdivisions.id AND award_awards_projectdivisions.award_awards_id='{$award['id']}' AND award_awards_projectdivisions.year='{$config['FAIRYEAR']}' AND projectdivisions.year='{$config['FAIRYEAR']}' ORDER BY division_shortform");
				echo mysql_error();
				$divs="";
				while($r=mysql_fetch_object($q))
				{
					if($divs) $divs.=", ".i18n($r->division_shortform);
					else $divs=i18n($r->division_shortform);
				}
				$rep->addText(i18n("Divisions").": $divs");
			}

		}

		$rep->nextLine();

		//get the timeslots that this team has.
		$q=mysql_query("SELECT 
					judges_timeslots.id,
					judges_timeslots.date,
					judges_timeslots.starttime,
					judges_timeslots.endtime
				FROM
					judges_timeslots,
					judges_teams,
					judges_teams_timeslots_link
				WHERE
					judges_teams.id='".$team['id']."' AND
					judges_teams.id=judges_teams_timeslots_link.judges_teams_id AND
					judges_timeslots.id=judges_teams_timeslots_link.judges_timeslots_id
				ORDER BY
					date,starttime
				");
		$numslots=mysql_num_rows($q);

		while($r=mysql_fetch_object($q))
		{
			if($show_date)
				$timeslot=$r->date." ";
			else
				$timeslot="";
			$timeslot.=format_time($r->starttime)." - ".format_time($r->endtime);

			$projq=mysql_query("SELECT
					projects.projectnumber,
					projects.id,
					projects.title
				FROM
					projects,
					judges_teams_timeslots_projects_link
				WHERE
					judges_teams_timeslots_projects_link.judges_timeslots_id='$r->id' AND
					judges_teams_timeslots_projects_link.judges_teams_id='".$team['id']."' AND
					judges_teams_timeslots_projects_link.projects_id=projects.id AND
					judges_teams_timeslots_projects_link.year='".$config['FAIRYEAR']."'
				ORDER BY
					projectnumber
				");

			while($proj=mysql_fetch_object($projq))
			{
				$table['data'][]=array($timeslot, $proj->projectnumber,$proj->title);
				//make the timeslot empty so we dont list it each time if there's more than one project in the timeslot
				$timeslot="";
			}
		}
		$rep->addTable($table);
		$rep->newPage();
		unset($table);
	}

	$rep->output();
?>
