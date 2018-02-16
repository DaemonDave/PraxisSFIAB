<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2008 James Grant <james@lightbox.org>

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

 //make sure storage folder exists
 if(!file_exists("../data/userfiles"))
 	mkdir("../data/userfiles");


 send_header("Website Content Manager",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "website_content_management"
			);

 if($_POST['action']=="save")
 {
	 $err=false;
	foreach($config['languages'] AS $lang=>$langname) {
		$filename=stripslashes($_POST['filename']);
//		$filename=ereg_replace("[^A-Za-z0-9\.\_\/]","_",$_POST['filename']);

		if(substr($filename,-5)!=".html")
			$filename=$filename.".html";

		$textname="text_$lang";
		$titlename="title_$lang";
		$showlogoname="showlogo_$lang";
		//get the dt here to insert with ALL the languages, we cant rely on the INSERT NOW() always inserting multiple records with the same timestamp!
		$insertdt=date("Y-m-d H:i:s");
		$text=stripslashes($_POST[$textname]);

		mysql_query("INSERT INTO cms (filename,dt,lang,text,title,showlogo) VALUES (
			'".mysql_escape_string($filename)."',
			'$insertdt',
			'$lang',
			'".mysql_escape_string($text)."',
			'".mysql_escape_string($_POST[$titlename])."',
			'".$_POST[$showlogoname]."'
			)");
		if(mysql_error()) {
			echo error(i18n("An error occurred saving %1 in %2",array($filename,$langname)));
			$err=true;
		}
	}
	if(!$err)
		echo happy(i18n("%1 successfully saved",array($_POST['filename'])));
 }

 if($_GET['filename'] || $_GET['action']=="create")
 {
	echo "<a href=\"cms.php\">&lt;&lt; Back to file list</a><br />\n";
	echo "<form method=\"post\" action=\"cms.php\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
	if($_GET['filename'])
		echo "<input type=\"hidden\" name=\"filename\" value=\"".htmlspecialchars($_GET['filename'])."\">\n";
	else
		echo "Choose filename to create: /web/<input type=\"text\" name=\"filename\" size=\"15\">.html<hr />";

	echo "<table width=\"100%\" cellpadding=\"3\">";
	echo "<tr><td valign=\"top\">";
	foreach($config['languages'] AS $lang=>$langname) {
		echo "<table class=\"tableview\" width=\"100%\">";
		echo "<tr><th colspan=\"2\">";
		$q=mysql_query("SELECT * FROM cms WHERE filename='".mysql_escape_string($_GET['filename'])."' AND lang='$lang' ORDER BY dt DESC LIMIT 1");
		if($r=mysql_fetch_object($q)) {
			if($r->dt=="0000-00-00 00:00:00" || !$r->dt) $dt="Never";
			else $dt=$r->dt;
			echo "<b>".htmlspecialchars($_GET['filename'])." - $langname</b> &nbsp;&nbsp; ".i18n("Last updated").": $dt<br />";
			if($_GET['dt']) {
				$q2=mysql_query("SELECT * FROM cms WHERE filename='".mysql_escape_string($_GET['filename'])."' AND lang='$lang' AND dt<='".$_GET['dt']."' ORDER BY dt DESC LIMIT 1");
				$r2=mysql_fetch_object($q2);
				if($r2->dt!=$r->dt)
				{
					echo "Displaying historical file.  Date: $r->dt";
					$r=$r2;
				}

			}
		}
		else
		{
			echo "<b>$langname</b><br />"; // &nbsp;&nbsp; ".i18n("Last updated").": $dt<br />";
		}
		echo "</th></tr>\n";
		echo "<tr><td width=\"100\">".i18n("Page Title").":</td><td><input type=\"text\" name=\"title_$lang\" style=\"width: 99%;\" value=\"".htmlspecialchars($r->title)."\"></td></tr>\n";
		echo "<tr><td width=\"100\">".i18n("Show Logo").":</td><td>";
		if($r->showlogo) $ch="checked=\"checked\""; else $ch="";
		echo "<input $ch type=\"radio\" name=\"showlogo_$lang\" value=\"1\"> ".i18n("Yes");
		echo "&nbsp;&nbsp;&nbsp;";
		if(!$r->showlogo) $ch="checked=\"checked\""; else $ch="";
		echo "<input $ch type=\"radio\" name=\"showlogo_$lang\" value=\"0\"> ".i18n("No");

		echo "</td></tr>\n";
		echo "<tr><td colspan=\"2\">";
		require_once("../fckeditor/fckeditor.php");

		$oFCKeditor = new FCKeditor("text_$lang") ;
		$oFCKeditor->BasePath  = "../fckeditor/";
		$oFCKeditor->Value = $r->text;
		$oFCKeditor->Width="100%";
		$oFCKeditor->Height=400;
		$oFCKeditor->Create() ;

		echo "</td></tr></table>\n";

		echo "<br />";
	}
	echo "</td><td width=\"130\" valign=\"top\">";
	echo "<table class=\"tableview\" width=\"130\">";

	if($_GET['historylimit']) $historylimit=intval($_GET['historylimit']);
	else $historylimit=30;

	echo "<tr><th>".i18n("File History")."</th></tr>\n";
	$q=mysql_query("SELECT DISTINCT(dt) FROM cms WHERE filename='".mysql_escape_string($_GET['filename'])."' ORDER BY dt DESC LIMIT $historylimit");
	$first=true;
	if(mysql_num_rows($q)) {
		while($r=mysql_fetch_object($q)) 
		{
			if($r->dt==$_GET['dt']) $style="font-weight: bold;"; 
			else $style="font-weight: normal;";

			if($first && !$_GET['dt']) $style="font-weight: bold;";

			echo "<tr><td><a href=\"cms.php?filename=".rawurlencode($_GET['filename'])."&amp;dt=".rawurlencode($r->dt)."\" style=\"font-size: 0.75em; $style\">$r->dt</a></td></tr>\n";
			$first=false;
		}

	}
	else
		echo "<tr><td><i>No History</i></td></tr>\n";

	echo "</table>\n";
	echo "</td></tr>\n";

	echo "<tr><td colspan=\"2\">";
	echo "<table><tr><td>";
	echo "<input type=\"submit\" value=\"".i18n("Save Page")."\" />\n";
	echo "</form>";
	echo "</td><td>";
	echo "<form method=\"get\" action=\"cms.php\">";
	echo "<input type=\"submit\" value=\"".i18n("Cancel Changes")."\" />\n";
	echo "</form>\n";
	echo "</td></tr></table>\n";

	echo "</td></tr></table>\n";

 }
 else
 {
	 echo i18n("Choose a web page filename to edit");
	 echo "&nbsp;";
	 echo "<a href=\"cms.php?action=create\">".i18n("or click here to create a new file")."</a><br />\n";

	 echo "<table class=\"summarytable\">";

	 $q=mysql_query("SELECT DISTINCT(filename) AS filename FROM cms ORDER BY filename");
	 echo "<tr><th>".i18n("Filename")."</th><th>".i18n("Last Update")."</th></tr>";
	 while($r=mysql_fetch_object($q))
	 {
		echo "<tr><td><a href=\"cms.php?filename=".rawurlencode($r->filename)."\">/web/$r->filename</a></td>";
		$q2=mysql_query("SELECT dt FROM cms WHERE filename='".mysql_escape_string($r->filename)."' ORDER BY dt DESC LIMIT 1");
		$r2=mysql_fetch_object($q2);
		if($r2->dt=="0000-00-00 00:00:00") $dt="Never";
		else $dt=$r2->dt;
		echo "<td>$dt</td>";
		echo "</tr>";


	 }
	 echo "</table>";
 }

 send_footer();
?>
