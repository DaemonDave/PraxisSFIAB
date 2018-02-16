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
<h1>SFIAB Installation - Step 1</h1>
<?
if(file_exists("data/config.inc.php"))
{
	echo "<div class=\"error\">SFIAB Installation Step 1 is already complete.</div>";
	echo "<a href=\"install2.php\">Proceed to installation step 2</a><br />";
	echo "</body></html>";
	exit;
}
?>


<?
$showform=true;

if($_POST['dbhost'] && $_POST['dbname'] && $_POST['dbuser'] && $_POST['dbpass'])
{
	if(@mysql_connect($_POST['dbhost'],$_POST['dbuser'],$_POST['dbpass']))
	{
		if(mysql_select_db($_POST['dbname']))
		{
			$showform=false;
			echo "<div class=\"happy\">Database connection successful!</div>";
			echo "<br />";
			echo "Storing database connection information... ";
			//create the config.inc.php
			if($fp=fopen("data/config.inc.php","w"))
			{
				
				fputs($fp,"<?\n");
				fputs($fp,"\$DBHOST=\"".$_POST['dbhost']."\";\n");
				fputs($fp,"\$DBUSER=\"".$_POST['dbuser']."\";\n");
				fputs($fp,"\$DBPASS=\"".$_POST['dbpass']."\";\n");
				fputs($fp,"\$DBNAME=\"".$_POST['dbname']."\";\n");
				fputs($fp,"?>\n");
				fclose($fp);
				echo "<b>Done!</b><br />";
				echo "<a href=\"install2.php\">Proceed to installation step 2</a><br />";
			}
			else
			{
				echo "<div class=\"error\">Cannot write to data/config.inc.php.  Make sure the web server has write access to the data/ subdirectory</div>";

			}

		}
		else
		{
			echo "<div class=\"error\">Connected, but cannot select database.  Make sure Database Name is correct, and that the user '".$_POST['dbuser']."' has access to it</div>";
		}
	
	
	}
	else
	{
		echo "<div class=\"error\">Cannot connect to database.  Make sure Host, User and Pass are correct</div>";
	}
	echo "<br />";
}


if($showform)
{
?>
SFIAB requires a MySQL database to store all of its information.  Please enter your MySQL database connection info for your database to continue.  The database must already exist and the user/password you specify must have access to the database. 

<br />
<br />
<form method="post" action="install.php">
<table class="summarytable">
<tr><th>Database Host</th><td><input type="text" name="dbhost" value="localhost"></td></tr>
<tr><th>Database User</th><td><input type="text" name="dbuser" value=""></td></tr>
<tr><th>Database Pass</th><td><input type="text" name="dbpass" value=""></td></tr>
<tr><th>Database Name</th><td><input type="text" name="dbname" value="sfiab"></td></tr>
<tr><td colspan=2 align=center><input type="submit" value="Connect to database"></td></tr>
</table>
</form>

<?
}
?>

</body>
</html>
