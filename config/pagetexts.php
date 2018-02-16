<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
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
 user_auth_required('committee', 'config');

 //make sure storage folder exists
 if(!file_exists("../data/userfiles"))
 	mkdir("../data/userfiles");


 send_header("Page Texts",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"page_texts"
			);

 $q=mysql_query("SELECT * FROM pagetext WHERE year='-1' ORDER BY textname");
 while($r=mysql_fetch_object($q))
 {
	 foreach($config['languages'] AS $lang=>$langname) {
        mysql_query("INSERT INTO pagetext (textname,textdescription,text,year,lang) VALUES (
            '".mysql_escape_string($r->textname)."',
            '".mysql_escape_string($r->textdescription)."',
            '".mysql_escape_string($r->text)."',
            '".$config['FAIRYEAR']."',
            '".mysql_escape_string($lang)."')");
    }
 }


 if($_POST['action']=="save")
 {
	 foreach($config['languages'] AS $lang=>$langname) {
		$textvar="text_$lang";
		$text=mysql_escape_string(stripslashes($_POST[$textvar]));

		mysql_query("UPDATE pagetext 
				SET 
					lastupdate=NOW(), 
					text='$text' 
				WHERE 
						textname='".mysql_escape_string($_POST['textname'])."' 
					AND year='".$config['FAIRYEAR']."'
					AND lang='$lang'");
	}
	echo happy(i18n("Page texts successfully saved"));

 }

 if($_GET['textname'])
 {
	$q=mysql_query("SELECT * FROM pagetext WHERE textname='".mysql_escape_string($_GET['textname'])."' AND year='".$config['FAIRYEAR']."'");
	//needs to be at least one entry in any languages
	if($r=mysql_fetch_object($q))
	{
		echo "<form method=\"post\" action=\"pagetexts.php\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
		echo "<input type=\"hidden\" name=\"textname\" value=\"$r->textname\">\n";


		foreach($config['languages'] AS $lang=>$langname) {
			$q=mysql_query("SELECT * FROM pagetext WHERE textname='".mysql_escape_string($_GET['textname'])."' AND year='".$config['FAIRYEAR']."' AND lang='$lang'");
			$r=mysql_fetch_object($q);

			if(!$r)
			{
					mysql_query("INSERT INTO pagetext (textname,year,lang) VALUES ('".mysql_escape_string($_GET['textname'])."','".$config['FAIRYEAR']."','$lang')");
					echo mysql_error();
			}

			if($r->lastupdate=="0000-00-00 00:00:00" || !$r->lastupdate) $lastupdate="Never";
			else $lastupdate=$r->lastupdate;
			echo "<b>".htmlspecialchars($_GET['textname'])." - $langname</b> &nbsp;&nbsp; ".i18n("Last updated").": $lastupdate<br />";
			require_once("../fckeditor/fckeditor.php");

			$oFCKeditor = new FCKeditor("text_$lang") ;
			$oFCKeditor->BasePath  = "../fckeditor/";
			$oFCKeditor->Value = $r->text;
			$oFCKeditor->Width="100%";
			$oFCKeditor->Height=300;
			$oFCKeditor->Create() ;

			echo "<hr />";
		}

		echo "<table><tr><td>";
		echo "<input type=\"submit\" value=\"".i18n("Save Page Texts")."\" />\n";
		echo "</form>";
		echo "</td><td>";
		echo "<form method=\"get\" action=\"pagetexts.php\">";
		echo "<input type=\"submit\" value=\"".i18n("Cancel Changes")."\" />\n";
		echo "</form>\n";
		echo "</td></tr></table>\n";

	}
	else
	{
		echo error(i18n("Invalid text name"));
	}
 }
 else
 {
	 echo "<br />";
	 echo i18n("Choose a page text to edit");
	 echo "<table class=\"summarytable\">";

	 $q=mysql_query("SELECT * FROM pagetext WHERE year='".$config['FAIRYEAR']."' AND lang='".$config['default_language']."' ORDER BY textname");
	 echo "<tr><th>".i18n("Page Text Description")."</th><th>".i18n("Last Update")."</th></tr>";
	 while($r=mysql_fetch_object($q))
	 {
		echo "<tr><td><a href=\"pagetexts.php?textname=$r->textname\">$r->textdescription</a></td>";
		if($r->lastupdate=="0000-00-00 00:00:00") $lastupdate="Never";
		else $lastupdate=$r->lastupdate;
		echo "<td>$lastupdate</td>";
		echo "</tr>";


	 }
	 echo "</table>";
 }

 send_footer();
?>
