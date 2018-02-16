<?
if(!function_exists("system")) {
	echo "DB Update requires php's system() function to be available\n";
	exit;
}

//include the config.inc.php
//so we have the db connection info
require("../data/config.inc.php");
echo "<pre>\n";
if(file_exists("db.code.version.txt"))
{
	$dbcodeversion_file=file("db.code.version.txt");
	$dbcodeversion=trim($dbcodeversion_file[0]);
}
else
{
	echo "Couldnt load current db.code.version.txt\n";
	exit;
}

//same fix here for mysql 5.1 not truncating the 16 char usernames
$DBUSER=substr($DBUSER,0,16);

mysql_connect($DBHOST,$DBUSER,$DBPASS);
mysql_select_db($DBNAME);
@mysql_query("SET NAMES latin1");
$q=mysql_query("SELECT val FROM config WHERE var='DBVERSION' AND year='0'");
$r=mysql_fetch_object($q);
$dbdbversion=$r->val;
if(!$dbdbversion)
{
	echo "Couldnt get current db version.  Is SFIAB properly installed?\n";
	exit;
}

/* Get the fair year */
$q=mysql_query("SELECT val FROM config WHERE var='FAIRYEAR' AND year='0'");
$r=mysql_fetch_object($q);
$config = array('FAIRYEAR' => $r->val);

/* Load config just in case there's a PHP script that wants it */
$q=mysql_query("SELECT * FROM config WHERE year='{$config['FAIRYEAR']}'");
while($r=mysql_fetch_object($q)) $config[$r->var]=$r->val;


require_once("../config_editor.inc.php"); // For config_update_variables()

if($dbcodeversion && $dbdbversion)
{
	//lets see if they match
	if($dbcodeversion == $dbdbversion)
	{
		echo "DB and CODE are all up-to-date.  Version: $dbdbversion\n";
		exit;
	}
	else if($dbcodeversion<$dbdbversion)
	{
		echo "ERROR: dbcodeversion<dbdbversion ($dbcodeversion<$dbdbversion).  This should not happen!";
		exit;

	}
	else if($dbcodeversion>$dbdbversion)
	{
		echo "DB update requirements detected\n";
		echo "Current DB Version: $dbdbversion\n";
		echo "Current CODE Version: $dbcodeversion\n";

		echo "Updating database from $dbdbversion to $dbcodeversion\n";

		for($ver=$dbdbversion+1;$ver<=$dbcodeversion;$ver++)
		{
			if(file_exists("db.update.$ver.php"))
			{
				include("db.update.$ver.php");
			}
			if(is_callable("db_update_{$ver}_pre")) {
				echo "db.update.$ver.php::db_update_{$ver}_pre() exists - running...\n";
				call_user_func("db_update_{$ver}_pre");
				echo "db.update.$ver.php::db_update_{$ver}_pre() done.\n";
			}
			if(file_exists("db.update.$ver.sql"))
			{
				echo "db.update.$ver.sql detected - running...\n";
				readfile("db.update.$ver.sql");
				echo "\n";
				system("mysql --default-character-set=latin1 -h$DBHOST -u$DBUSER -p$DBPASS $DBNAME <db.update.$ver.sql");
			}
			else
			{
				echo "Version $ver SQL update file not found - skipping over\n";
			}
			if(is_callable("db_update_{$ver}_post")) {
				echo "db.update.$ver.php::db_update_{$ver}_post() exists - running...\n";
				call_user_func("db_update_{$ver}_post");
				echo "db.update.$ver.php::db_update_{$ver}_post() done.\n";
			}
		}
		if($db_update_skip_variables != true) {
			echo "\nUpdating Configuration Variables...\n";
			config_update_variables($config['FAIRYEAR']);
		}

		echo "\nAll done - updating new DB version to $dbcodeversion\n";
		mysql_query("UPDATE config SET val='$dbcodeversion' WHERE var='DBVERSION' AND year='0'");

	}

}
else
{
	echo "ERROR: dbcodeversion and dbdbversion are not defined\n";
}

echo "</pre>\n";

?>
