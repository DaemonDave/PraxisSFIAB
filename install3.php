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
<h1>SFIAB Installation - Step 3</h1>
<?
if(!file_exists("data/config.inc.php"))
{
	echo "<div class=\"error\">SFIAB Installation Step 1 is not yet complete.</div>";
	echo "<a href=\"install.php\">Go back to installation step 1</a><br />";
	echo "</body></html>";
	exit;
}

require_once("data/config.inc.php");
require_once("config_editor.inc.php");
require_once("user.inc.php");
require_once("committee.inc.php");
mysql_connect($DBHOST,$DBUSER,$DBPASS);
mysql_select_db($DBNAME);

		echo "Checking for SFIAB database... ";

		$q=@mysql_query("SELECT val FROM config WHERE var='DBVERSION' AND year='0'");
		$r=@mysql_fetch_object($q);
		$dbdbversion=$r->val;

		if(!$dbdbversion)
		{
			echo "<div class=\"error\">SFIAB Installation Step 2 is not yet complete.</div>";
			echo "<a href=\"install2.php\">Go back to installation step 2</a><br />";
			echo "</body></html>";
			exit;
		}

//a fresh install should ONLY have DBVERSION defined in the config table.  If there are others (FAIRYEAR, SFIABDIRECTORY) then this is NOT fresh
$q=mysql_query("SELECT * FROM config WHERE year='0' AND ( var='DBVERSION' OR var='FAIRYEAR' OR var='SFIABDIRECTORY') ");
//we might get an error if the config table does not exist (ie, installer step 2 failed)
if(mysql_error())
{
	//we say all tables, but really only we check for config where year=0;
	echo "<div class=\"error\">ERROR: No SFIAB tables detected,  It seems like step 2 failed.  Please go <a href=\"install2.php\">Back to Installation Step 2</a> and try again.</div>";
	echo "</body></html>";
	exit;

}
//1 is okay (DBVERSION). More than 1 is bad (already isntalled)
if(mysql_num_rows($q)>1)
{
	//we say all tables, but really only we check for config where year=0;
	echo "<div class=\"error\">ERROR: Detected existing table data, SFIAB Installation Step 3 requires a clean SFIAB database installation.</div>";
	echo "</body></html>";
	exit;
}
echo "<b>Found!</b><br />";

if($_POST['action']=="save")
{
	$err=false;
	if(!$_POST['fairyear'])
	{
		echo "Fair Year is required";
		$err=true;
	}
	
	if(!$_POST['email'])
	{
		echo "Superuser email address is required";
		$err=true;
	}
	
	if(!( $_POST['pass1'] && $_POST['pass2']))
	{
		echo "Superuser password and password confirmation are required";
		$err=true;
	}
	if($_POST['pass1'] != $_POST['pass2'])
	{
		echo "Password and Password confirmation do not match";
		$err=true;
	}
	
	if(!$err)
	{
		echo "Creating configuration settings...";
		mysql_query("INSERT INTO config (var,val,category,ord,year) VALUES ('FAIRYEAR','".$_POST['fairyear']."','Special','0','0')");
		mysql_query("INSERT INTO config (var,val,category,ord,year) VALUES ('FISCALYEAR','".$_POST['fiscalyear']."','Special','0','0')");
		mysql_query("INSERT INTO config (var,val,category,ord,year) VALUES ('SFIABDIRECTORY','".$_POST['sfiabdirectory']."','Special','','0')");

		$year = intval($_POST['fairyear']);
		
		//copy over the config defautls
		config_update_variables($year);

		// Update some variables 
		mysql_query("UPDATE config SET 
				val='".mysql_escape_string(stripslashes($_POST['fairname']))."'
				WHERE var='fairname' AND year='$year'");

		mysql_query("UPDATE config SET 
				val='".mysql_escape_string(stripslashes($_POST['email']))."'
				WHERE var='fairmanageremail' AND year='$year'");

		$q=mysql_query("SELECT * FROM dates WHERE year='-1'");
		while($r=mysql_fetch_object($q))
		{
			mysql_query("INSERT INTO dates (date,name,description,year) VALUES ('$r->date','$r->name','$r->description','".$_POST['fairyear']."')");
		}

		//copy over the award_types defautls
		$q=mysql_query("SELECT * FROM award_types WHERE year='-1'");
		while($r=mysql_fetch_object($q))
		{
			mysql_query("INSERT INTO award_types (id,type,`order`,year) VALUES ('$r->id','$r->type','$r->order','".$_POST['fairyear']."')");
		}

		echo "<b>Done!</b><br />";
		echo "Creating superuser account...";

		$u = user_create('committee',$_POST['email']);
		if($_POST['firstname'] && $_POST['lastname']) {
			$u['firstname']=mysql_escape_string(stripslashes($_POST['firstname']));
			$u['lastname']=mysql_escape_string(stripslashes($_POST['lastname']));
		}
		else {
			$u['firstname'] = 'Superuser';
			$u['lastname'] = 'Account';
		}
		$u['emailprivate'] = mysql_escape_string(stripslashes($_POST['email']));
		$u['email'] = mysql_escape_string(stripslashes($_POST['email']));
		$u['username'] = mysql_escape_string(stripslashes($_POST['email']));
		$u['password'] = mysql_escape_string(stripslashes($_POST['pass1']));
		$u['access_admin'] = 'yes';
		$u['access_config'] = 'yes';
		$u['access_super'] = 'yes';
		user_save($u);

		echo "<b>Done!</b><br />";
		echo "Installation is now complete!  You can now proceed to the following location: <br />";
		echo "&nbsp; &nbsp; <a href=\"".$_POST['sfiabdirectory']."\">Your SFIAB main page</a><br />";
		echo "</body></html>";
		exit;
	}

}

echo "<br />";
echo "Please enter the following options <br />";
echo "<br />";

$month=date("m");
if($month>4) $fairyearsuggest=date("Y")+1;
else $fairyearsuggest=date("Y");

if($month>6) $fiscalyearsuggest=date("Y")+1;
else $fiscalyearsuggest=date("Y");

$directorysuggest=substr($_SERVER['REQUEST_URI'],0,-13);
echo "<h3>Options</h3>";
echo "<form method=\"post\" action=\"install3.php\">";
echo "<input type=\"hidden\" name=\"action\" value=\"save\" />";

echo "<table>";
echo "<tr><td>Fair Name</td><td><input size=\"25\" type=\"text\" name=\"fairname\" value=\"\"></td><td>The name of the fair you are installing SFIAB to run</td></tr>";
echo "<tr><td>Fair Year</td><td><input size=\"8\" type=\"text\" name=\"fairyear\" value=\"$fairyearsuggest\"></td><td>The year of the fair you are installing SFIAB to run</td></tr>";
echo "<tr><td>Fiscal Year</td><td><input size=\"8\" type=\"text\" name=\"fiscalyear\" value=\"$fiscalyearsuggest\"></td><td>The current fiscal year (for fundraising/accounting purposes)</td></tr>";
echo "<tr><td>Directory</td><td><input size=\"25\" type=\"text\" name=\"sfiabdirectory\" value=\"$directorysuggest\"></td><td>The directory of this SFIAB installation as seen by the web browser</td></tr>";

echo "</table>";
echo "<br />";
echo "<h3>Superuser Account</h3>";
echo "Please choose your superuser account which is required to login to SFIAB and configure the system, as well as to add other users. <br />";
echo "<table>";
echo "<tr><td>Superuser Email Address</td><td><input size=\"40\" type=\"text\" name=\"email\"></td></tr>";
echo "<tr><td>Superuser Password</td><td><input size=\"15\" type=\"password\" name=\"pass1\"></td></tr>";
echo "<tr><td>Superuser Password (Confirm)</td><td><input size=\"15\" type=\"password\" name=\"pass2\"></td></tr>";
echo "</table>";
echo "<br />";
echo "<input type=\"submit\" value=\"Complete Installation\">";
echo "</form>";

?>

</body></html>
