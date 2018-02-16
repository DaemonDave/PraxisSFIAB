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
user_auth_required('committee', 'config');

//make sure backup/restore folder exists, and htaccess it to deny access
if(!file_exists("../data/backuprestore"))
	mkdir("../data/backuprestore");
 if(!file_exists("../data/backuprestore/.htaccess"))
 	file_put_contents("../data/backuprestore/.htaccess","Order Deny,Allow\r\nDeny From All\r\n");


if($_GET['action']=="backup") {
$ts=time();
$dump="#SFIAB SQL BACKUP: ".date("r",$ts)."\n";
$dump.="#SFIAB VERSION: ".$config['version']."\n";
$dump.="#SFIAB DB VERSION: ".$config['DBVERSION']."\n";
$dump.="#SFIAB FAIR NAME: ".$config['fairname']."\n";
$dump.="#-------------------------------------------------\n";

$tableq=mysql_query("SHOW TABLES FROM `$DBNAME`");
while($tr=mysql_fetch_row($tableq)) {
	$table=$tr[0];
	$dump.="#TABLE: $table\n";
	$columnq=mysql_query("SHOW COLUMNS FROM `$table`");
	$str="INSERT INTO `$table` (";
	unset($fields);
	$fields=array();
	while($cr=mysql_fetch_object($columnq)) {
		$str.="`".$cr->Field."`,";
		$fields[]=$cr->Field;
	}
	$str=substr($str,0,-1);
	$str.=") VALUES (";

	$dataq=mysql_query("SELECT * FROM `$table` ORDER BY `{$fields[0]}`");
	while($data=mysql_fetch_object($dataq)) {
		$insertstr=$str;
		foreach($fields AS $field) {
			if(is_null($data->$field))
				$insertstr.="NULL,";
			else
			{
				$escaped=str_replace("\\","\\\\",$data->$field);
				$escaped=str_replace("'","''",$escaped);
				$escaped=str_replace("\n","\\n",$escaped);
				$escaped=str_replace("\r","\\r",$escaped);
				$insertstr.="'".$escaped."',";
			}
		}
		$insertstr=substr($insertstr,0,-1);
		$insertstr.=");";

		$dump.=$insertstr."\n";
	}
}
header("Content-Type: text/sql");
header("Content-Disposition: attachment; filename=sfiab_backup_".date("Y-m-d-H-i-s",$ts).".sql");
header("Content-Length: ".strlen($dump));
//Make IE with SSL work
header("Pragma: public");
echo $dump;
}
else if($_POST['action']=="restore") {
	echo send_header("Database Backup/Restore",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"backup_restore"
	);
	echo i18n("Processing file: %1",array($_FILES['restore']['name']))."<br />\n";
	echo "<br />\n";
	do {
		//hmm just some random filename
		$tmpfilename=md5(rand().time().$_FILES['restore']['name']);
	} while(file_exists("../data/backuprestore/$tmpfilename"));

	move_uploaded_file($_FILES['restore']['tmp_name'],"../data/backuprestore/$tmpfilename");

	$fp=fopen("../data/backuprestore/$tmpfilename","r");

	for($x=0;$x<4;$x++) {
		$line=fgets($fp,1024);
		$hdr[$x]=split(":",trim($line),2);
	}
	fclose($fp);

	if(trim($hdr[0][0])=="#SFIAB SQL BACKUP") {

		echo "<table class=\"tableview\">\n";
		$now=date("r");
		echo "<tr><th>".i18n("Information")."</th><th>".i18n("Restore File")."</th><th>".i18n("Live System")."</th></tr>\n";
		if(trim($hdr[0][1])<trim($now)) $cl="happy"; else { $cl="error"; $err=true; }
		echo "<tr class=\"$cl\"><td>".i18n("Date/Time")."</td><td>".$hdr[0][1]."</td><td>$now</td></tr>\n";
		if(version_compare(trim($hdr[1][1]),$config['version'])==0) $cl="happy"; else { $cl="error"; $err=true; }
		echo "<tr class=\"$cl\"><td>".i18n("SFIAB Version")."</td><td>".$hdr[1][1]."</td><td>".$config['version']."</td></tr>\n";
		if(version_compare(trim($hdr[2][1]),$config['DBVERSION'])==0) $cl="happy"; else { $cl="error"; $err=true; } 
		echo "<tr class=\"$cl\"><td>".i18n("Database Version")."</td><td>".$hdr[2][1]."</td><td>".$config['DBVERSION']."</td></tr>\n";
		if(trim($hdr[3][1])==$config['fairname']) $cl="happy"; else { $cl="error"; $err=true; } 
		echo "<tr class=\"$cl\"><td>".i18n("Fair Name")."</td><td>".$hdr[3][1]."</td><td>".$config['fairname']."</td></tr>\n";
		echo "</table>\n";
		echo "<br />\n";
		if($err) {
			echo error(i18n("Warning, there are discrepencies between the restore file and your current live system. Proceed at your own risk!"));
		}

		echo "<form method=\"post\" action=\"backuprestore.php\">\n";
		echo "<input type=\"hidden\" name=\"action\" value=\"restoreproceed\">\n";
		echo "<input type=\"hidden\" name=\"filename\" value=\"".$_FILES['restore']['name']."\">\n";
		echo "<input type=\"hidden\" name=\"realfilename\" value=\"$tmpfilename\">\n";
		echo "<input type=\"submit\" value=\"".i18n("Proceed with Database Restore")."\">";
		echo "</form>\n";
		echo "<br />\n";
		echo "<a href=\"backuprestore.php\">";
		echo i18n("If you are not going to proceed, please click here to clean up the temporary files which may contain confidential information!");
		echo "</a>\n";
	}
	else
	{
			echo error(i18n("This file is NOT a SFIAB SQL BACKUP file"));
			echo i18n("Only backups created with the SFIAB Backup Creator can be used to restore from.");
			echo "<br />\n";
	}

	send_footer();
}
else if($_POST['action']=="restoreproceed") {
	echo send_header("Database Backup/Restore",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"backup_restore"
	);

	//make sure the filename's good before we used it
	if(ereg("^[a-z0-9]{32}$",$_POST['realfilename']) && file_exists("../data/backuprestore/".$_POST['realfilename'])) {
		$filename=$_POST['realfilename'];
		echo i18n("Proceeding with database restore from %1",array($_POST['filename']))."...";
		$lines=file("../data/backuprestore/$filename");
		$err=false;
		echo "<pre>";
		foreach($lines AS $line) {
			$line=trim($line);
			if(ereg("^#TABLE: (.*)",$line,$args)) {
				//empty out the table
				$sql="TRUNCATE TABLE `".$args[1]."`";
		//			echo $sql."\n";
				mysql_query($sql);
			}
			else if(ereg("^#",$line)) {
				//just skip it
			}
			else
			{
				//insert the new data
				mysql_query($line);
				if(mysql_error()) {
					echo $line."\n";
					echo mysql_error()."\n";
					$err=true;
				}
			}
		}
		echo "</pre>";
		if($err) {
			echo error(i18n("An error occured while importing the restore database"));
		}
		else
			echo happy(i18n("Database successfully restored"));

		unlink("../data/backuprestore/$filename");
	}
	else
		echo error(i18n("Invalid filename"));

	send_footer();

}
else
{
	echo send_header("Database Backup/Restore",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"backup_restore"
	);


	//we try to remove temp files every time we load this page, who knows, maybe they navigated away 
	//last time instead of clicking the link to come back here
	$dh=opendir("../data/backuprestore");
	$removed=false;
	while($fn=readdir($dh)) {
		if(ereg("[a-z0-9]{32}",$fn)) {
			unlink("../data/backuprestore/$fn");
			$removed=true;
		}
	}
	closedir($dh);

	if($removed) {
		echo happy(i18n("Temporary files successfully removed"));
	}


	echo "<h3>".i18n("Backup Database")."</h3>\n";
	echo "<a href=\"backuprestore.php?action=backup\">".i18n("Create Database Backup File")."</a><br />\n";
	echo "<br /><br />\n";
	echo "<hr />\n";

	echo "<h3>".i18n("Restore Database")."</h3>\n";
	echo error(i18n("WARNING: Importing a backup will completely DESTROY all data currently in the database and replace it with what is in the backup file"));
	echo "<form method=\"post\" action=\"backuprestore.php\" enctype=\"multipart/form-data\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"restore\">\n";
	echo "<input type=\"file\" name=\"restore\">\n";
	echo "<input type=\"submit\" value=\"".i18n("Upload Restore File")."\">\n";
	echo "</form>\n";


	send_footer();
}

?>
