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

$catq=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' AND id='".$_GET['cat']."'");
if($catr=mysql_fetch_object($catq))
{

 	$pdf=new lpdf(	i18n($config['fairname']),
			i18n("Checkin List")." - ".i18n($catr->category),
			$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
			);

	$pdf->newPage();
	$pdf->setFontSize(11);
	$q=mysql_query("SELECT  registrations.id AS reg_id,
				registrations.num AS reg_num,
				registrations.status,
				projects.title,
				projects.projectnumber,
				projects.projectdivisions_id
			FROM
				registrations
				left outer join projects on projects.registrations_id=registrations.id
			WHERE
				registrations.year='".$config['FAIRYEAR']."' 
				AND ( registrations.status='complete' OR registrations.status='paymentpending' )
				AND projects.projectcategories_id='$catr->id'
			ORDER BY
				projects.title
			");
		echo mysql_error();

	$table=array();

	//only show the 'paid' column if the regfee > 0.  if registration is fee, then we dont care about the 'paid' column!
	if($config['regfee']>0)
	{
		$table['header']=array(i18n("Paid?"),i18n("Proj #"),i18n("Project Title"),i18n("Student(s)"),i18n("Div"));
		$table['widths']=array(0.5,	   	  0.6,		 	3.5,		    2.4,	             0.5);
		$table['dataalign']=array("center","left","left","left","center");
	}
	else
	{
		$table['header']=array(i18n("Proj #"),i18n("Project Title"),i18n("Student(s)"),i18n("Div"));
		$table['widths']=array(	  0.6,		 	3.7,		    2.7,	             0.5);
		$table['dataalign']=array("left","left","left","center");

	}
	while($r=mysql_fetch_object($q))
	{
		$divq=mysql_query("SELECT division,division_shortform FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' AND id='".$r->projectdivisions_id."'");
		$divr=mysql_fetch_object($divq);

		$sq=mysql_query("SELECT students.firstname,
					students.lastname
				FROM
					students
				WHERE
					students.registrations_id='$r->reg_id'
				");

		$students="";
		$studnum=0;
		while($studentinfo=mysql_fetch_object($sq))
		{
			if($studnum>0) $students.=", ";
			$students.="$studentinfo->firstname $studentinfo->lastname";
			$studnum++;
		}

		//only show the paid column if regfee >0
		if($config['regfee']>0)
		{
			switch($r->status)
			{
				case "paymentpending": $status_text="No"; break;
				case "complete": $status_text=""; break;
			}
			$status_text=i18n($status_text);

			$table['data'][]=array($status_text,$r->proj_num,$r->title,$students,i18n($divr->division_shortform));
		}
		else
			$table['data'][]=array($r->projectnumber,$r->title,$students,i18n($divr->division_shortform));

	}

	$pdf->addTable($table);

	$pdf->output();
}
?>
