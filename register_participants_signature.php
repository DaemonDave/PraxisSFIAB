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
 include "register_participants.inc.php";
 require("lpdf.php");

 //anyone can access a sample, we dont need to be authenticated or anything for that
 if($_GET['sample']) {
	$registration_number=12345;
	$registration_id=0;
 }
 else {
 //authenticate based on email address and registration number from the SESSION
 if(!$_SESSION['email'])
 {
 	header("Location: register_participants.php");
	exit;
 }
 if(!$_SESSION['registration_number'])
 {
 	header("Location: register_participants.php");
	exit;
 }

 $q=mysql_query("SELECT registrations.id AS regid, students.id AS studentid, students.firstname FROM registrations,students ".
 	"WHERE students.email='".$_SESSION['email']."' ".
	"AND registrations.num='".$_SESSION['registration_number']."' ". 
	"AND registrations.id='".$_SESSION['registration_id']."' ".
	"AND students.registrations_id=registrations.id ".
	"AND registrations.year=".$config['FAIRYEAR']." ".
	"AND students.year=".$config['FAIRYEAR']);

	$registration_number=$_SESSION['registration_number'];
	$registration_id=$_SESSION['registration_id'];

echo mysql_error();

 if(mysql_num_rows($q)==0)
 {
 	header("Location: register_participants.php");
	exit;
 
 }
 $authinfo=mysql_fetch_object($q);

}
 //END OF AUTH, now lets try to generate a PDF using only PHP :) this should be fun!

$pdf=new lpdf(	i18n($config['fairname']),
	i18n("Participant Signature Page (".$registration_number.")"),
	$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
	);

$pdf->newPage();
 $height['sigspace']=0.40;
 $pdf->setFontSize(11);

/*
//The title of the fair
 $yloc=10.25;
 $height['title']=0.25;
 $height['subtitle']=0.22;
 $height['topbox']=0.8;
 $height['exhibitortitle']=0.2;
 $height['exhibitorbox']=1.3;
 $height['exhibitorsigtext']=0.13;
 $height['parenttitle']=0.2;
 $height['parentbox']=2.80;
 $height['parentsigtext']=0.13;

*/

 if($_GET['sample']) {
 	$projectinfo->title="Sample Project Title";
 	$projectinfo->division="Proj Division";
 	$projectinfo->category="Proj Category";
	$studentinfo->firstname="SampleFirst";
	$studentinfo->lastname="SampleLast";
	$studentinfo->grade="10";
	$studentinfoarray[]=$studentinfo;
	$rr->school="SampleSchool";
 }
 else
 {
 //grab the project info
 $q=mysql_query("SELECT projects.*, 
                        projectcategories.category, 
                        projectdivisions.division
                 FROM projects
                 JOIN projectdivisions ON projects.projectdivisions_id=projectdivisions.id
                 JOIN projectcategories ON projects.projectcategories_id=projectcategories.id
                 WHERE registrations_id='".$_SESSION['registration_id']."' 
                        AND projects.year='".$config['FAIRYEAR']."'
                        AND projectdivisions.year='".$config['FAIRYEAR']."'
                        AND projectcategories.year='".$config['FAIRYEAR']."'
                        ");
 $projectinfo=mysql_fetch_object($q);

 $q=mysql_query("SELECT * FROM students WHERE registrations_id='".$_SESSION['registration_id']."' AND year='".$config['FAIRYEAR']."'");
 while($si=mysql_fetch_object($q))
	$studentinfoarray[]=$si;
 }

 $topboxtext=i18n("Registration Number").": ".$registration_number."\n".
                         i18n("Project Title").": $projectinfo->title\n".
                         i18n($projectinfo->category)." / ".i18n($projectinfo->division)."\n";
 if(count($studentinfoarray)>1)
 	$plural="s";
 else
 	$plural="";

 $pdf->heading(i18n("Registration Summary"));
 $pdf->addText($topboxtext);
 $pdf->nextline();

 $pdf->addTextX("Exhibitor$plural: ", 0.75);
 
 foreach($studentinfoarray AS $studentinfo)
 {
 	if(!$_GET['sample']) {
	$qq = mysql_query("SELECT school FROM schools WHERE id={$studentinfo->schools_id}");
	$rr = mysql_fetch_object($qq);
	}
	
	$pdf->addTextX("$studentinfo->firstname $studentinfo->lastname, Grade {$studentinfo->grade}, {$rr->school}", 1.5); 
	$pdf->nextline();
 }
 //strip off the last comma
 //add the newline
// $topboxtext.="\n";
			 
 $pdf->hr();

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='exhibitordeclaration'");
 $r=mysql_fetch_object($q);
 if($r->use)
 {
	 $pdf->heading(i18n("Exhibitor Declaration"));

	$studentbox=$r->text;
/*
	 $studentbox="The following section is to be read and signed by the exhibitor$plural.\n\n".
		($plural?"We":"I")." certify that:\n". 
		" - The preparation of this project is mainly ".($plural?"our":"my")." own work\n". 
		" - ".($plural?"We":"I")." have read the rules and regulations and agree to abide by them\n".
		" - ".($plural?"We":"I")." agree that the decision of the judges will be final\n";

*/
	 $pdf->addText($studentbox);

	 foreach($studentinfoarray AS $studentinfo)
	 {
	 	//we want to make sure the vspace, line, and text under the line dont
		//get wrapped onto multiple pages, so make sure we have enough space for the whole thing before we
		//start, and if we dont, make a new page.  normal stop for footer is at 0.9, so 1.65 gives 0.75 inches 
		//which should be enough... i think :)
                if($pdf->yloc< 1.65 )
			$pdf->newPage();

		$pdf->vspace($height['sigspace']);

		//signature line
		$pdf->hline(1,4.5);
		//date line
		$pdf->hline(5,7);
		//go to next line
		$pdf->nextLine();

		//show their name
		$pdf->addTextX(i18n("%1 %2 (signature)",array($studentinfo->firstname,$studentinfo->lastname)),1.25);
		//show the Date text
		$pdf->addTextX(i18n("Date"),5.25);

		//go to next line
		$pdf->nextLine();
	 }

	 $pdf->hr();
 }

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='parentdeclaration'");
 $r=mysql_fetch_object($q);
 if($r->use)
 {
	 //now for the parent/guardian signatures
	 $pdf->heading(i18n("Parent/Guardian Declaration"));

	$parentbox=$r->text;
	 $pdf->addText($parentbox);
	  
	 foreach($studentinfoarray AS $studentinfo)
	 {
	 	//we want to make sure the vspace, line, and text under the line dont
		//get wrapped onto multiple pages, so make sure we have enough space for the whole thing before we
		//start, and if we dont, make a new page.  normal stop for footer is at 0.9, so 1.65 gives 0.75 inches 
		//which should be enough... i think :)
                if($pdf->yloc< 1.65 )
			$pdf->newPage();

		$pdf->vspace($height['sigspace']);

		//signature line
		$pdf->hline(1,4.5);

		//date line
		$pdf->hline(5,7);
		$pdf->nextLine();

		//show their name
		$pdf->addTextX(i18n("Parent/Guardian of %1 %2 (signature)",array($studentinfo->firstname,$studentinfo->lastname)),1.25);

		//show the Date text
		$pdf->addTextX(i18n("Date"),5.25);
		$pdf->nextLine();

	 }
	 $pdf->hr();
 }

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='teacherdeclaration'");
 $r=mysql_fetch_object($q);
 if($r->use)
 {
	 //now for the teacher signature
	 $pdf->heading(i18n("Teacher Declaration"));

	$teacherbox=$r->text;
	 $pdf->addText($teacherbox);
	  
	//we want to make sure the vspace, line, and text under the line dont
	//get wrapped onto multiple pages, so make sure we have enough space for the whole thing before we
	//start, and if we dont, make a new page.  normal stop for footer is at 0.9, so 1.65 gives 0.75 inches 
	//which should be enough... i think :)
	if($pdf->yloc< 1.65 )
		$pdf->newPage();


	//we only need 1 teacher signature line, we can assume (maybe incorrectly) that both students
	//have the same teacher.. if they are not the same, then they can get the best teacher to sign
	//it doesnt matter.
	$pdf->vspace($height['sigspace']);

	//signature line
	$pdf->hline(1,4.5);

	//date line
	$pdf->hline(5,7);
	$pdf->nextLine();

	//show their name
	$pdf->addTextX(i18n("Teacher Signature"),1.25);

	//show the Date text
	$pdf->addTextX(i18n("Date"),5.25);
	$pdf->nextLine();

	$pdf->hr();
  }

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='regfee'");
 $r=mysql_fetch_object($q);
 if($r->use)
 {
	//now for the teacher signature
	$pdf->heading(i18n("Registration Fee Summary"));
	$pdf->nextLine();

	list($regfee, $rfeedata) = computeRegistrationFee($registration_id);

	$pdf->addTextX(i18n('Item'), 3.5);
	$pdf->addTextX(i18n('Unit'), 5.1);
	$pdf->addTextX(i18n('Qty'), 5.5);
	$pdf->addTextX(i18n('Extended'), 5.9);
	$pdf->vspace(0.05);
	$pdf->hline(1.75,6.75);
	foreach($rfeedata as $rf) {
		$pdf->nextLine();
		$u = "$".sprintf("%.02f", $rf['base']);
		$e = "$".sprintf("%.02f", $rf['ext']);
		
		$pdf->addTextX($rf['text'], 2);
		$pdf->addTextX("$u", 5);
		$pdf->addTextX($rf['num'], 5.6);
		$pdf->addTextX("$e", 6);
	}
	$pdf->vspace(0.05);
	$pdf->hline(1.75,6.75);
	$pdf->nextLine();
	$t = "$".sprintf("%.02f", $regfee);

	$pdf->addTextX(i18n('Total (including all taxes)'), 4.2);
	$pdf->addTextX("$t", 6);
	$pdf->nextLine();

	$pdf->hr();
 }

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='postamble'");
 $r=mysql_fetch_object($q);
 if($r->use)
 {
	//now for the teacher signature
	$pdf->heading(i18n("Additional Information"));

	$box=$r->text;
	$pdf->addText($box);
	  
 }


/*
header("Content-type: application/pdf");
 header("Content-disposition: inline; filename=sfiab_sig_".$_SESSION['registration_id'].".pdf");
 header("Content-length: ".strlen($pdfdata));
 */
 echo $pdf->output();
?>
