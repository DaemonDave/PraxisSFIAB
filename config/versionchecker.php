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
 send_header("Version Checker",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"new_version_checker"
			);

 echo i18n("Checking for new versions will access a remote server, if you wish to continue click the 'Check for new versions' link below");
 echo "<br />";
 echo "<br />";
 echo i18n("Your currently installed version: <b>%1</b>",array($config['version']));
 echo "<br />";


 function loadVersions()
 {
 	$ret=array();
 	if($v=file("http://www.sfiab.ca/version.txt"))
	{
		list($version,$date)=split("\t",trim($v[0]));
		$ret['version']=$version;
		$ret['date']=$date;
	}
	else
		echo error(i18n("There was an error connecting to the version checker server"));
	return $ret;
 }

 if($_GET['action']=="check")
 {
 	$v=loadVersions();
	echo i18n("Newest version available: <b>%1</b> (%2)",array($v['version'],$v['date']));
	echo "<br />";
	echo "<br />";
	$val=version_compare($config['version'],$v['version']);
	if($val==0)
	{
		echo happy(i18n("Your current version (%1) is up-to-date",array($config['version'])));
	}
	else if($val<0)
	{
		echo error(i18n("There is a new version available!<br />Newest version: %1 Released on %2",array($v['version'],$v['date'])));
		echo i18n("The newest version can be downloaded from <a target=\"_blank\" href=\"http://www.sfiab.ca/download.php\">http://www.sfiab.ca/download.php</a>");
	}
	else if($val>0)
	{
		echo happy(i18n("You are running a newer (probably a development) version (%1) that is newer than the most recent release (%2)",array($config['version'],$v['version'])));
	}

 }
 else
	 echo "<a href=\"versionchecker.php?action=check\">".i18n("Check for new versions")."</a><br />";

 send_footer();
?>
