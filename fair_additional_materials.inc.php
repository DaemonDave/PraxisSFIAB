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
require_once('common.inc.php');
require_once('projects.inc.php');
require_once('lpdf.php');

/* Creates a nomination form for every winner of a specific award, should only be called
 * by remote.php, which calls it only if the award has additional materials.  */
function fair_additional_materials($fair, $award, $year)
{
	global $config;

	$rep=new lpdf(	"{$config['fairname']} Awards Program",
			"Nomination Form", 
			$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo.gif");
	
	/* Grab a list of winners */
	$q = mysql_query("SELECT * FROM award_prizes
				LEFT JOIN winners ON winners.awards_prizes_id=award_prizes.id
			WHERE winners.year='$year' 
				AND winners.fairs_id='{$fair['id']}'");
	while($r = mysql_fetch_assoc($q)) {
		$pid = $r['projects_id'];
		$rep->newPage("","",1);
		$rep->setFontSize(12);

		/* Left margin width */
		$x = 1;

		$rep->setFontSize(14);
		$rep->addText("{$award['name']}", "center");
		$rep->setFontSize(12);
		$rep->addText("{$r['prize']}", "center");
		$rep->nextLine();
		$rep->hr();
		$rep->nextLine();

		$rep->addTextX("Name of Regional Fair:  ___________________________________________________", $x);
		$rep->addTextX("{$fair['name']}", $x + 1.75);

		$rep->nextLine();

		$rep->addTextX("Authorized By: __________________________________________________________", $x);
		$rep->nextLine();
		$rep->addTextX("Position: _______________________________________________________________", $x);
		$rep->nextLine();


		$rep->addTextX("Date: ________________________________________", $x);
		$rep->addTextX(date('l F dS, Y'), $x + 0.5);
		$rep->nextLine();
		$rep->nextLine();


		$p = project_load($pid);
//		print_r($p);

		$rep->addTextX("Project Title: ____________________________________________________________", $x);
		$rep->prevLine();
		$rep->addText("{$p['title']}", "left", $x+1) ;
		$rep->nextLine();
		$rep->nextLine();
		$rep->nextLine();

		foreach($p['student'] as $s) {
			$rep->addTextX("Name of Student: ________________________________________________________", $x);
			$rep->addTextX("{$s['firstname']} {$s['lastname']}", $x+1.25);
			$rep->nextLine();
			$rep->addTextX("Grade: _____________          Date of birth: _____________", $x);
			$rep->addTextX("{$s['grade']}", $x+0.75);
			list($y,$m,$d) = split('-',$s['dateofbirth']);
			$dob = date('M j, Y', mktime(0,0,0,$m,$d,$y));
			$rep->addTextX("$dob", $x+3);
			$rep->nextLine();
			$rep->addTextX("School: ________________________________________________________________", $x);
			$rep->addTextX("{$s['school']}", $x + 0.75);
			$rep->nextLine();
			$rep->addTextX("Home Address: __________________________________________________________", $x);
			$rep->prevLine();
			$rep->addText("{$s['address']}  {$s['city']}, {$s['province']} {$s['postalcode']}", "left", $x + 1.25);
			$rep->nextLine();
			$rep->nextLine();
			$rep->nextLine();
		}
			
		$rep->hr();

		$rep->setFontBold();
		$rep->addText("To be considered for this award the following materials need to be included with this form:\n");
		$rep->setFontNormal();
		$rep->nextLine();

		$rep->addText("1.");
		$rep->prevLine();
		$rep->addText("Project Summary/Discussion Paper (Please include a copy of the summary sheet and any documentation that accompanied the display, including charts and diagrams, that will improve the understanding and comprehension of the science fair project.\n", "left", 0.9);
		$rep->addText("2. Copy of Judges Report and Comments.");
		$rep->addText("3. Colour photograph(s) of the exhibitor(s) and the exhibit.");
		$rep->addText("4. Completed Declaration of Exhibitor form (next page) ");

		$rep->nextLine();

		$rep->newPage();

		$rep->nextLine();
		$rep->setFontBold();
		$rep->addText("DECLARATION OF EXHIBITOR", "center");
		$rep->nextLine();
		$rep->setFontNormal();
		$rep->nextLine();
		$rep->nextLine();
		$i = ($p['num_students'] == 1)  ? "I" : "We";
		$my = ($p['num_students'] == 1)  ? "my" : "our";
		$rep->addText("1. $i certify this exhibit and report is $my own work.");
		$rep->nextLine();
		$fn = strtoupper($config['fairname']);
		$rep->addText("2.");
		$rep->prevLine();
		$rep->addText("$i hereby give permission to $fn the AWARDING ORGANIZATION to publicize $my award and reprint $my project summary.", "left", 0.9);

		$rep->nextLine();
		$rep->nextLine();
		$rep->nextLine();

		foreach($p['student'] as $s) {
			$rep->addText("______________________________________                           _____________________", "center");
			$rep->nextLine();
			$rep->addTextX("SIGNATURE ({$s['firstname']} {$s['lastname']})", 1.5);
			$rep->addTextX("Date", 6.25);
			$rep->nextLine();
			$rep->nextLine();
			$rep->nextLine();
		}

		$rep->addText("Certified by:");
		$rep->nextLine();
		$rep->nextLine();
		$rep->nextLine();

		$rep->addText("______________________________________                           _____________________", "center");
		$rep->nextLine();
		$rep->addTextX("POSITION", 2.5);
		$rep->addTextX("Date", 6.25);
		$rep->nextLine();

		$rep->nextLine();
		$rep->nextLine();

		$rep->addText("_________________________________________________", "center");
		$rep->nextLine();
		$rep->addText("(Regional Chairperson, Awards Chairperson, or Chief Judge)", "center");
	}

	return $rep->outputArray();

}

?>
