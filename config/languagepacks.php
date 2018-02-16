<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2006 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2006 James Grant <james@lightbox.org>

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
 send_header("Language Packs",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"language_pack_installer"
			);

 echo i18n("Checking for language packs will access a remote server, if you wish to continue click the 'Check for available language packs' link below");
 echo "<br />";
 echo "<br />";
 echo "<a href=\"languagepacks.php?action=check\">".i18n("Check for available language packs")."</a><br />";

 function loadLanguagePacks()
 {
 	$ret=array();
 	if($packs=file("http://www.sfiab.ca/languages/langpacklist.txt"))
	{
		$num=count($packs);
		//format of each line is:
		//lang:filename:lastupdate
		if(count($packs))
		{
			foreach($packs AS $p)
			{
				list($langpack,$filename,$lastupdate)=split("\t",trim($p));
				$ret[$langpack]=array("lang"=>$langpack,"filename"=>$filename,"lastupdate"=>$lastupdate);
			}
		}
	}
	else
	{
		echo error(i18n("There was an error connecting to the language pack server"));
	}
	return $ret;

 }

 if($_GET['action']=="check")
 {
 	$packs=loadLanguagePacks();

	$num=count($packs);
	echo '<hr />';
	echo i18n("Found %1 available language pack(s)",array($num));
	//format of each line is:
	//lang:filename:lastupdate
	if(count($packs))
	{
		echo "<table class=\"summarytable\">";
		echo "<tr>";
		echo "<th>".i18n("Language")."</th>";
		echo "<th>".i18n("Filename")."</th>";
		echo "<th>".i18n("Last Update")."</th>";
		echo "<th>".i18n("Install")."</th>";
		echo "</tr>";
		foreach($packs AS $p)
		{
			echo "<tr><td align=\"center\">{$p['lang']}</td><td>{$p['filename']}</td><td>{$p['lastupdate']}</td>";
			echo "<td align=\"center\">";
			echo "<a href=\"languagepacks.php?action=install&install={$p['lang']}\">".i18n("Install")."</a>";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
 }

 if($_GET['action']=="install" && $_GET['install'])
 {
 	$packs=loadLanguagePacks();
	$loaded=0;
	if($packs[$_GET['install']])
	{
		$lines=file("http://www.sfiab.ca/languages/{$packs[$_GET['install']]['filename']}");
		$totallines=count($lines);
		$numtranslations=round($totallines/2);
		echo i18n("There are %1 translations in this language pack... processing...",array($numtranslations));
		if(count($lines))
		{
			foreach($lines AS $line)
			{
				$line=trim($line);

				if(substr($line,0,6)=="UPDATE" || substr($line,0,6)=="INSERT")
				{
					mysql_query($line);
					$a=mysql_affected_rows();
					$loaded+=$a;
				}
				else
					echo notice("Ignoring invalid language pack line: %1",array($l));
			}
			if($loaded==0)
				echo notice(i18n("You already have all of the translations in this language pack"));
			else
				echo happy(i18n("Successfully loaded %1 new translations",array($loaded)));
		}
		else
		{
			echo error(i18n("Error downloading language pack"));
		}
	}
	else
	{
		echo error(i18n("Invalid language pack to install"));
	}
 }

 send_footer();
?>
