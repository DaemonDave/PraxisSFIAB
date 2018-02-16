<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 James Grant <james@lightbox.org>
   Copyright (C) 2010 David Grant <dave@lightbox.org>

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
 require_once('register_participants.inc.php');
 require_once('tcpdf.inc.php');

 //anyone can access a sample, we dont need to be authenticated or anything for that
 if($_GET['sample']) {
	$registration_number=12345;
	$registration_id=0;
 } else {
	//authenticate based on email address and registration number from the SESSION
	if(!$_SESSION['email']) {
		header("Location: register_participants.php");
		exit;
	}
	if(!$_SESSION['registration_number']) {
	 	header("Location: register_participants.php");
		exit;
	}

	 $q=mysql_query("SELECT registrations.id AS regid, students.id AS studentid, students.firstname 
	 			FROM registrations,students
		 		WHERE students.email='{$_SESSION['email']}' 
				AND registrations.num='{$_SESSION['registration_number']}'
				AND registrations.id='{$_SESSION['registration_id']}'
				AND students.registrations_id=registrations.id
				AND registrations.year={$config['FAIRYEAR']}
				AND students.year={$config['FAIRYEAR']}");
	$registration_number=$_SESSION['registration_number'];
	$registration_id=$_SESSION['registration_id'];

	echo mysql_error();

	if(mysql_num_rows($q)==0) {
	 	header("Location: register_participants.php");
		exit;
 
	}
	$authinfo=mysql_fetch_object($q);

}
 //END OF AUTH, now lets try to generate a PDF using only PHP :) this should be fun!



$pdf=new pdf( "Participant Signature Page ($registration_number)" );

$pdf->setFontSize(11);
$pdf->SetFont('times');
$height_sigspace = 15; //mm
$height_sigfont = $pdf->GetFontSize(); //mm

$pdf->AddPage();

 if($_GET['sample']) {
 	$projectinfo->title="Sample Project Title";
 	$projectinfo->division="Proj Division";
 	$projectinfo->category="Proj Category";
	$studentinfo->firstname="SampleFirst";
	$studentinfo->lastname="SampleLast";
	$studentinfo->grade="10";
	$studentinfoarray[]=$studentinfo;
	$rr->school="SampleSchool";
 } else {
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

 $plural = (count($studentinfoarray)>1) ? 's' : '';

 $pdf->WriteHTML("<h3>".i18n('Registration Summary')."</h3>
	<p>
	".i18n('Registration Number').": $registration_number <br/>
	".i18n('Project Title').": {$projectinfo->title} <br/>
        ".i18n($projectinfo->category)." / ".i18n($projectinfo->division));

 $students = "";
 foreach($studentinfoarray AS $studentinfo) { 
 	if(!$_GET['sample']) {
		$qq = mysql_query("SELECT school FROM schools WHERE id={$studentinfo->schools_id}");
		$rr = mysql_fetch_object($qq);
	}
	if($students != '') $students .= '<br/>';
	$students .= "{$studentinfo->firstname} {$studentinfo->lastname}, Grade {$studentinfo->grade}, {$rr->school}";
 }
$e = i18n("Exhibitor$plural").":";
$w = $pdf->GetStringWidth($e) + 2;
$pdf->WriteHTML("<table><tr><td width=\"{$w}mm\">$e</td><td>$students</td></tr></table>");
$pdf->WriteHTML("<hr>");

function sig($pdf, $text)
{
	global $height_sigspace, $height_font;

	$x = $pdf->GetX();
	/* One cell for the whole thing, to force a page break if needed, leave 
	 * the current pos to the right so the Y is unchanged */
	$pdf->Cell(0, $height_sigspace + $height_font, '', 0, 0);

	/* Restore X, and indent a bit, move Y down the signature space */
	$pdf->SetXY($x + 15, $pdf->GetY() + $height_sigspace);

	/* Box with a top line, then a space, then a box with a top line for the date */
	$pdf->Cell(85, $height_font, $text, 'T', 0, 'C');
	$pdf->SetX($pdf->GetX() + 15);
	$pdf->Cell(60, $height_font, i18n('Date'), 'T', 1, 'C');
}

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='exhibitordeclaration'");
 $r=mysql_fetch_assoc($q);
 if($r['use']) {
 	$t = nl2br($r['text']);
	$pdf->WriteHTML("<h3>".i18n('Exhibitor Declaration')."</h3>$t");

	foreach($studentinfoarray AS $studentinfo) {
		sig($pdf, i18n("%1 %2 (signature)",array($studentinfo->firstname,$studentinfo->lastname)));
	}
	$pdf->WriteHTML("<br><hr>");
 }

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='parentdeclaration'");
 $r=mysql_fetch_assoc($q);
 if($r['use']) {
 	$t = nl2br($r['text']);
	$pdf->WriteHTML("<h3>".i18n('Parent/Guardian Declaration')."</h3>$t");

	foreach($studentinfoarray AS $studentinfo) {
		sig($pdf, i18n("Parent/Guardian of %1 %2 (signature)",array($studentinfo->firstname,$studentinfo->lastname)));
	}
	$pdf->WriteHTML("<br><hr>");
 }

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='teacherdeclaration'");
 $r=mysql_fetch_assoc($q);
 if($r['use']) {
 	$t = nl2br($r['text']);
	$pdf->WriteHTML("<h3>".i18n('Teacher Declaration')."</h3>$t");
	sig($pdf, i18n('Teacher Signature'));
	$pdf->WriteHTML("<br><hr>");	
 }

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='regfee'");
 $r=mysql_fetch_assoc($q);
 if($r['use']) {
	$pdf->WriteHTML("<h3>".i18n('Registration Fee Summary')."</h3><br>");

	list($regfee, $rfeedata) = computeRegistrationFee($registration_id);

	$x = $pdf->GetX() + 20;
	$pdf->SetX($x);
	$pdf->Cell(60, 0, i18n('Item'), 'B', 0, 'C');
	$pdf->Cell(15, 0, i18n('Unit'), 'B', 0, 'C');
	$pdf->Cell(10, 0, i18n('Qty'), 'B', 0, 'C');
	$pdf->Cell(20, 0, i18n('Extended'), 'B', 1, 'C');
	foreach($rfeedata as $rf) {
		$u = "$".sprintf("%.02f", $rf['base']);
		$e = "$".sprintf("%.02f", $rf['ext']);

		$pdf->SetX($x);
		$pdf->Cell(60, 0, $rf['text'], 0, 0, 'L');
		$pdf->Cell(15, 0, $u, 0, 0, 'R');
		$pdf->Cell(10, 0, $rf['num'], 0, 0, 'C');
		$pdf->Cell(20, 0, $e, 0, 1, 'R');
	}
	$t = "$".sprintf("%.02f", $regfee);
	$pdf->SetX($x);
	$pdf->Cell(85, 0, i18n('Total (including all taxes)'), 'T', 0, 'R');
	$pdf->Cell(20, 0, $t, 'T', 1, 'R');
	$pdf->WriteHTML("<br><hr>");	
 }

 $q=mysql_query("SELECT * FROM signaturepage WHERE name='postamble'");
 $r=mysql_fetch_assoc($q);
 if($r['use']) {
 	$t = nl2br($r['text']);
	$pdf->WriteHTML("<h3>".i18n('Additional Information')."</h3>$t");
	$pdf->WriteHTML("<br><hr>");	
 }

 echo $pdf->output();
?>
