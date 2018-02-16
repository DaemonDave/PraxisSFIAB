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
				i18n("Project Judging Team Assignments"),
				$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
				);

		$rep->newPage();
		$rep->setFontSize(11);
	}
	else if($type=="csv")
	{
		$rep=new lcsv(i18n("Project Judging Team Assignments"));
	}

	$teams=getJudgingTeams();

	$q=mysql_query("SELECT DISTINCT(date) AS d FROM judges_timeslots WHERE year='".$config['FAIRYEAR']."'");
	if(mysql_num_rows($q)>1)
		$show_date=true;
	else
		$show_date=false;


	$projq=mysql_query("SELECT  
				registrations.id AS reg_id,
				registrations.num AS reg_num,
				projects.id,
				projects.title,
				projects.projectnumber,
				projects.projectdivisions_id,
				projects.projectcategories_id,
				projectdivisions.division,
				projectcategories.category
			FROM
				registrations
				LEFT JOIN projects ON projects.registrations_id=registrations.id
				LEFT JOIN projectdivisions ON projectdivisions.id=projects.projectdivisions_id
				LEFT JOIN projectcategories ON projectcategories.id=projects.projectcategories_id

			WHERE
				projects.year='".$config['FAIRYEAR']."' 
				AND projectdivisions.year='".$config['FAIRYEAR']."'
				AND projectcategories.year='".$config['FAIRYEAR']."'
				AND ( registrations.status='complete'
				 OR registrations.status='paymentpending' )
			ORDER BY
				projects.projectnumber
			");
			echo mysql_error();
	
	while($proj=mysql_fetch_object($projq))
	{
		$rep->heading("(".$proj->projectnumber.") ".$proj->title);

		$sq=mysql_query("SELECT students.firstname,
					students.lastname
				FROM
					students
				WHERE
					students.registrations_id='$proj->reg_id'
				");


		$students="";
		$studnum=0;
		while($studentinfo=mysql_fetch_object($sq))
		{
			if($studnum>0) $students.=", ";
			$students.="$studentinfo->firstname $studentinfo->lastname";
			$studnum++;
		}
		$rep->addText($students);
		$rep->nextLine();

		$table=array();
		$table['header']=array(i18n("Timeslot"),i18n("Judging Team"));
		if($show_date)
			$table['widths']=array(		2.25,		4.75);
		else
			$table['widths']=array(		1.5,		5.50);

		$table['dataalign']=array("center","left");

		//get the timeslots that this project has assigned to been judged.
		$q=mysql_query("SELECT 
					judges_timeslots.date,
					judges_timeslots.starttime,
					judges_timeslots.endtime,
					judges_teams.name
				FROM
					judges_teams_timeslots_projects_link
					LEFT JOIN judges_timeslots ON judges_teams_timeslots_projects_link.judges_timeslots_id=judges_timeslots.id
					LEFT JOIN judges_teams ON judges_teams_timeslots_projects_link.judges_teams_id=judges_teams.id
				WHERE
					judges_teams_timeslots_projects_link.projects_id='$proj->id'
					AND judges_teams_timeslots_projects_link.year='".$config['FAIRYEAR']."'
				ORDER BY
					date,starttime
				");
		$numslots=mysql_num_rows($q);

		while($r=mysql_fetch_object($q))
		{
			if($show_date)
				$timeslot=format_date($r->date)." ";
			else
				$timeslot="";
			$timeslot.=format_time($r->starttime)." - ".format_time($r->endtime);

			$table['data'][]=array($timeslot, $r->name);
		}
		$rep->addTable($table);
		$rep->newPage();
		unset($table);
	}

	$rep->output();
?>
