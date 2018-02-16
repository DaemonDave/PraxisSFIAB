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
 user_auth_required('committee', 'config');
 send_header("Signature Page",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"exhibitor_signature_page"
			);

 if($_POST['action']=="save")
 {
 	if($_POST['useexhibitordeclaration']) $useex="1"; else $useex="0";
 	if($_POST['useparentdeclaration']) $usepg="1"; else $usepg="0";
 	if($_POST['useteacherdeclaration']) $usete="1"; else $usete="0";
 	if($_POST['usepostamble']) $usepa="1"; else $usepa="0";
 	if($_POST['useregfee']) $userf="1"; else $userf="0";

 	mysql_query("UPDATE signaturepage SET `use`='$useex', `text`='".mysql_escape_string(stripslashes($_POST['exhibitordeclaration']))."' WHERE name='exhibitordeclaration'");
 	mysql_query("UPDATE signaturepage SET `use`='$usepg', `text`='".mysql_escape_string(stripslashes($_POST['parentdeclaration']))."' WHERE name='parentdeclaration'");
 	mysql_query("UPDATE signaturepage SET `use`='$usete', `text`='".mysql_escape_string(stripslashes($_POST['teacherdeclaration']))."' WHERE name='teacherdeclaration'");
 	mysql_query("UPDATE signaturepage SET `use`='$usepa', `text`='".mysql_escape_string(stripslashes($_POST['postamble']))."' WHERE name='postamble'");
 	mysql_query("UPDATE signaturepage SET `use`='$userf', `text`='' WHERE name='regfee'");
	echo happy(i18n("Signature page text successfully saved"));
 }

echo "<a href=\"../register_participants_signature.php?sample=true\">Preview your signature form as a PDF (as a student would see it)</a><br />";

$q=mysql_query("SELECT * FROM signaturepage WHERE name='exhibitordeclaration'");
$r=mysql_fetch_object($q);
echo "<form method=\"post\" action=\"signaturepage.php\">";
echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
if($r->use) $ch="checked=\"checked\""; else $ch="";
echo "<input $ch type=\"checkbox\" name=\"useexhibitordeclaration\" value=\"1\">".i18n("Use the exhibitor declaration and obtain exhibitor signatures");
echo "<br />";
echo "<textarea name=\"exhibitordeclaration\" rows=\"8\" cols=\"80\">".$r->text."</textarea>";
echo "<br />";
echo "<br />";

$q=mysql_query("SELECT * FROM signaturepage WHERE name='parentdeclaration'");
$r=mysql_fetch_object($q);
if($r->use) $ch="checked=\"checked\""; else $ch="";
echo "<input $ch type=\"checkbox\" name=\"useparentdeclaration\" value=\"1\">".i18n("Use the parent/guardian declaration and obtain parent/guardian signatures");
echo "<br />";
echo "<textarea name=\"parentdeclaration\" rows=\"8\" cols=\"80\">".$r->text."</textarea>";
echo "<br />";
echo "<br />";

$q=mysql_query("SELECT * FROM signaturepage WHERE name='teacherdeclaration'");
$r=mysql_fetch_object($q);
if($r->use) $ch="checked=\"checked\""; else $ch="";
echo "<input $ch type=\"checkbox\" name=\"useteacherdeclaration\" value=\"1\">".i18n("Use the teacher declaration and obtain teacher's signature");
echo "<br />";
echo "<textarea name=\"teacherdeclaration\" rows=\"8\" cols=\"80\">".$r->text."</textarea>";
echo "<br />";
echo "<br />";

$q=mysql_query("SELECT * FROM signaturepage WHERE name='regfee'");
$r=mysql_fetch_object($q);
if($r->use) $ch="checked=\"checked\""; else $ch="";
echo "<input $ch type=\"checkbox\" name=\"useregfee\" value=\"1\">".i18n("Include registration fee information on the signature page");
echo "<br />";
echo "<br />";

$q=mysql_query("SELECT * FROM signaturepage WHERE name='postamble'");
$r=mysql_fetch_object($q);
if($r->use) $ch="checked=\"checked\""; else $ch="";
echo "<input $ch type=\"checkbox\" name=\"usepostamble\" value=\"1\">".i18n("Place Additional Information after all the required signatures");
echo "<br />";
echo "<textarea name=\"postamble\" rows=\"8\" cols=\"80\">".$r->text."</textarea>";
echo "<br />";
echo "<br />";

echo "<input type=\"submit\" value=\"".i18n("Save Signature Page")."\">";
echo "</form>";

 send_footer();
?>
