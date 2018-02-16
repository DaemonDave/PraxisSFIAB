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
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head><title>SFIAB Installation</title>
<link rel="stylesheet" href="sfiab.css" type="text/css" />
</head>
<body>
<h1>SFIAB Installation - Step 2</h1>
<?

if(!function_exists("system")) {
	echo "<div class=\"error\">Installation requires php's system() function to be available</div>\n";
	echo "</body></html>";
	exit;
}

if(!file_exists("data/config.inc.php"))
{
	echo "<div class=\"error\">SFIAB Installation Step 1 is not yet complete.</div>";
	echo "<a href=\"install.php\">Go back to installation step 1</a><br />";
	echo "</body></html>";
	exit;
}

require_once("data/config.inc.php");
mysql_connect($DBHOST,$DBUSER,$DBPASS);
mysql_select_db($DBNAME);

		echo "Getting database version requirements for code... ";

		if(file_exists("db/db.code.version.txt"))
		{
			$dbcodeversion_file=file("db/db.code.version.txt");
			$dbcodeversion=trim($dbcodeversion_file[0]);
		}
		else
		{
			echo "<b>ERROR: Couldnt load current db/db.code.version.txt</b><br />";
			exit;
		}
		echo "<b>version $dbcodeversion</b><br />";

		echo "Checking for existing SFIAB database... ";

		$q=@mysql_query("SELECT val FROM config WHERE var='DBVERSION' AND year='0'");
		$r=@mysql_fetch_object($q);
		$dbdbversion=$r->val;

		if($dbdbversion)
		{
			echo "<b>ERROR: found version $dbdbversion</b><br />";

			//lets see if they match
			if($dbcodeversion == $dbdbversion)
				echo "Your SFIAB database is already setup with the required version\n";
			else if($dbcodeversion<$dbdbversion)
				echo "ERROR: dbcodeversion<dbdbversion ($dbcodeversion<$dbdbversion).  This should not happen!";
			else if($dbcodeversion>$dbdbversion)
				echo "Your SFIAB database needs to be updated.  You should run the update script instead of this installer!\n";
			exit;
		}
		else
		{
			echo "<b>Not found (good!)</b><br />";
		}

		echo "Checking for database installer for version $dbcodeversion... ";
		if(file_exists("db/db.full.$dbcodeversion.sql"))
		{
			echo "<b>db/db.full.$dbcodeversion.sql found</b><br />";

			echo "Setting up database tables... ";

			system("mysql --default-character-set=latin1 -h$DBHOST -u$DBUSER -p$DBPASS $DBNAME <db/db.full.$dbcodeversion.sql");

			echo "<b>Done! installed database version $dbcodeversion</b><br />\n";

			//now update the db version in the database
			mysql_query("UPDATE config SET val='$dbcodeversion' WHERE var='DBVERSION' AND year='0'");

			echo "<br />";
			echo "<b>Done!</b><br />";
			echo "<a href=\"install3.php\">Proceed to installation step 3</a><br />";
		}
		else
		{
			echo "<b>WARNING: Couldnt find db/db.full.$dbcodeversion.sql</b><br />";
			echo "Trying to find an older version... <br />";

			for($x=$dbcodeversion;$x>0;$x--)
			{
				if(file_exists("db/db.full.$x.sql"))
				{
					echo "<b>db/db.full.$x.sql found</b><br />";
					echo "Setting up database tables... ";

					system("mysql --default-character-set=latin1 -h$DBHOST -u$DBUSER -p$DBPASS $DBNAME <db/db.full.$x.sql");

					echo "<b>Done! installed database version $x</b><br />\n";

					//now update the db version in the database
					mysql_query("UPDATE config SET val='$x' WHERE var='DBVERSION' AND year='0'");

					echo "<b>Attempting to update database using standard update script to update from $x to $dbcodeversion<br />";
					echo "<br />Please scroll to the bottom of this page for the link to the next step of the installation process.<br /></b>";
					chdir ("db");
					/* Update the database, but don't update the config variables yet, because
					 * We haven't set the FAIRYEAR */
					$db_update_skip_variables = true;
					include "db_update.php";
					chdir ("../");

					echo "<br />";
					echo "<b>Done!</b><br />";
					echo "<a href=\"install3.php\">Proceed to installation step 3</a><br />";
					break;
				}
			}
		}

		//only if this file was created will we go ahead with the rest
		//creating all the tables and such..

?>

</body></html>
