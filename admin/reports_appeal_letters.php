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
require_once('../common.inc.php');
require_once('../user.inc.php');
user_auth_required('committee', 'admin');

require_once('../tcpdf/tcpdf_sfiab_config.php');
require_once('../tcpdf/tcpdf.php');

$fcid = intval($_GET['fundraising_campaigns_id']);
$key = mysql_real_escape_string($_GET['key']);

/* Start an output PDF */
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator('SFIAB');
$pdf->SetAuthor('SFIAB');
$pdf->SetTitle($config['fairname']);
$pdf->SetSubject('Fundraising Appeal Letters');
$pdf->SetKeywords('');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setPrintFooter(false);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

//set some language-dependent strings
//$pdf->setLanguageArray($l); 

/* Load the users */
$users = array();
$q = mysql_query("SELECT * FROM fundraising_campaigns_users_link WHERE fundraising_campaigns_id='$fcid'");
while($l = mysql_fetch_assoc($q)) {
	$uid = $l['users_uid'];
	$users[$uid] = user_load_by_uid($uid);
}

/* Grab all the emails */
$q = mysql_query("SELECT * FROM emails WHERE fundraising_campaigns_id='$fcid' AND val='$key'");

while($e = mysql_fetch_assoc($q)) {

	foreach($users as $uid=>&$u) {
	 	$subject = communication_replace_vars($e['subject'], $u);
		$body = communication_replace_vars($e['bodyhtml'], $u);
		/* these dont' need substitutions */
		$to = $u['name'];
		$date = date("F j, Y");

 		$html = "<table><tr><td align=\"right\" width=\"25\%\"><b>Attn:  </b></td><td>$to</td></tr>
				<tr><td align=\"right\" width=\"25\%\"><b>Subject:  </b></td><td>$subject</td></tr>
				<tr><td align=\"right\" width=\"25\%\"><b>Date:  </b></td><td>$date</td></tr>
			</table>
			<hr />";

	 	$pdf->AddPage();
		$pdf->writeHTML($html);
		$pdf->writeHTML($body);
		$pdf->lastPage();
	}
 }
 $pdf->Output('report.pdf','I');

?>
