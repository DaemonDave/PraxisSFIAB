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


if($_GET['report']) $report=$_GET['report'];
if($_GET['reportname']) $reportname=$_GET['reportname']; else $reportname=$_GET['report'];
if($report)
{
	if($_GET['type']=="pdf")
 	{
		$card_width=4.00;
		$card_height=2.00;
		$xspacer=0.125;
		$yspacer=0.125;
		$fontsize=10;
		$toppadding=0;
	
		if($_GET['width']) $card_width=$_GET['width'];
		if($_GET['height']) $card_height=$_GET['height'];
		if($_GET['xspacer']) $xspacer=$_GET['xspacer'];
		if($_GET['yspacer']) $yspacer=$_GET['yspacer'];
		if($_GET['fontsize']) $fontsize=$_GET['fontsize'];
		if($_GET['toppadding']) $toppadding=$_GET['toppadding'];

		$rep=new lpdf(	i18n($config['fairname']),
				"$reportname Mailing Labels",
				$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
				);

		$rep->setPageStyle("labels");
		$rep->newPage(8.5,11);
		$rep->setLabelDimensions($card_width,$card_height,$xspacer,$yspacer,$fontsize,$toppadding);
 	}
 	else if($_GET['type']=="csv") {
 		$rep=new lcsv(i18n("$reportname Mailing Labels"));
 	}

	switch($report)
	{
		//IF(schools.sciencehead=\"\",\"Science Department Head\",schools.sciencehead) AS co,
		case "schools":
				$q=mysql_query("SELECT  
							schools.school AS name,
							schools.schoollang,
							schools.sciencehead AS co,
							schools.address AS address,
							schools.city AS city,
							schools.province_code AS province,
							schools.postalcode AS postalcode
						FROM
							schools
						WHERE
							year='{$config['FAIRYEAR']}'
						ORDER BY
							school
						");
		break;

		case "sponsors":

				$q=mysql_query("SELECT  
							award_sponsors.organization AS name,
							award_sponsors.address AS address,
							award_sponsors.city AS city,
							award_sponsors.province_code AS province,
							award_sponsors.postalcode AS postalcode,
							IF(award_contacts.salutation=\"\",
							CONCAT(award_contacts.firstname,' ',award_contacts.lastname),
							CONCAT(award_contacts.salutation,' ',award_contacts.firstname,' ',award_contacts.lastname))
							AS co
						FROM
							award_sponsors,
							award_contacts
						WHERE
							award_sponsors.confirmed='yes'
							AND award_contacts.award_sponsors_id=award_sponsors.id
						ORDER BY
							organization
						");
			break;

			case "judges":
				$q=mysql_query("SELECT  
							CONCAT(judges.firstname,' ',judges.lastname) AS name,
							IF(judges.address2=\"\",
								judges.address,
								CONCAT(judges.address,' ',judges.address2)
							) AS address,
							'' AS co,
							judges.city AS city,
							judges.province AS province,
							judges.postalcode AS postalcode
						FROM
							judges,
							judges_years
						WHERE
							judges_years.judges_id=judges.id
							AND judges_years.year='{$config['FAIRYEAR']}'
						ORDER BY
							lastname,firstname
						");
			break;

	}


	if($_GET['type']=="csv") 
	{
		$table=array();
		$table['header'] = array(
					i18n("Name"),
					i18n("C/O"),
					i18n("Address"),
					i18n("City"),
					i18n($config['provincestate']),
					i18n($config['postalzip']));
	}
	
	while($r=mysql_fetch_object($q))
	{
		//handle C/O differently for schools, becuase, well, french schools are picky!
		if($report=="schools") {
			if($r->sciencehead)
				$coname=$r->sciencehead;
			else
				$coname=i18n("Science Department Head",array(),array(),$r->schoollang);

			$co=i18n("C/O %1",array($coname),array("Name of person"),$r->schoollang);
		}
		else $co="C/O $r->co";

		if($_GET['type']=="pdf")
		{
			$rep->newLabel();
			$rep->mailingLabel($r->name,$co,$r->address,$r->city,$r->province,$r->postalcode);
		}
		else if($_GET['type']=="csv")
		{
			$table['data'][]=array($r->name,$co,$r->address,$r->city,$r->province,$r->postalcode);
		}
	}

	if($_GET['type']=="csv") 
		$rep->addTable($table);
	
	$rep->output();

}
?>
