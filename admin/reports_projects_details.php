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
				i18n("Project Details"),
				$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
				);

		$rep->newPage();
		$rep->setFontSize(11);
	}
	else if($type=="csv")
	{
		$rep=new lcsv(i18n("Project Details"));
	}

	$projq=mysql_query("SELECT  
				registrations.id AS reg_id,
				registrations.num AS reg_num,
				projects.id,
				projects.title,
				projects.projectnumber,
				projects.projectdivisions_id,
				projects.projectcategories_id,
				projects.summary,
				projects.req_electricity,
				projects.req_table,
				projects.req_special,
				projects.language,
				projectdivisions.division,
				projectcategories.category
			FROM
				registrations
				LEFT JOIN projects on projects.registrations_id=registrations.id
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

	$totalprojects=mysql_num_rows($projq);
	$projectcount=0;
	
	while($proj=mysql_fetch_object($projq))
	{
		$projectcount++;
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
		$rep->heading(i18n("Project Information"));
		$rep->nextline();

		$table=array();
//		$table['header']=array(i18n("Timeslot"),i18n("Judging Team"));
		$table['widths']=array(		1.25,		4.75);
		$table['dataalign']=array("left","left");
		$table['data'][]=array(i18n("Project Number"),$proj->projectnumber);
		$table['data'][]=array(i18n("Project Title"),$proj->title);
		$table['data'][]=array(i18n("Age Category"),$proj->category);
		$table['data'][]=array(i18n("Division"),$proj->division);
		$table['data'][]=array(i18n("Students"),$students);
		$table['data'][]=array(i18n("Table?"),$proj->req_table);
		$table['data'][]=array(i18n("Electricity?"),$proj->req_electricity);
		$table['data'][]=array(i18n("Special Requests"),$proj->req_special);
		$table['data'][]=array(i18n("Language"),$proj->language);
		$rep->addTable($table);
		unset($table);

		$q=mysql_query("SELECT * FROM mentors WHERE registrations_id='".$proj->reg_id."'");
		$rep->nextline();
		$rep->heading(i18n("Mentor Information"));
		$rep->nextline();

		if(mysql_num_rows($q))
		{
		while($r=mysql_fetch_object($q))
		{
			$rep->addText(i18n("%1 %2 from %3",array($r->firstname,$r->lastname,$r->organization)));
			$rep->addText(i18n("Phone: %1 Email: %2",array($r->phone,$r->email)));
		}
		}
		else
		{
			$rep->addText(i18n("No mentors"));
		}

		$rep->nextline();
		$rep->heading(i18n("Project Summary"));
		$rep->nextline();
		$rep->addText($proj->summary);

		if($projectcount!=$totalprojects)
			$rep->newPage();
	}

	$rep->output();
?>
