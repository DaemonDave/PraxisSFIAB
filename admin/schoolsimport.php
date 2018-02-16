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
 require("../csvimport.inc.php");

 user_auth_required('committee', 'admin');

 send_header("Schools Import",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'School Management' => 'admin/schools.php')
			);
 
	$showform=true;

	if($_POST['action']=="import")
	{
		if(!$_FILES['schools']['error'] && $_FILES['schools']['size']>0)
		{
			$showform=false;
			$CSVP=new CSVParser();
			$CSVP->parseFile($_FILES['schools']['tmp_name']);
			if(count($CSVP->data)>0)
			{
				//okay it looks like we have something.. lets dump the current stuff
				if($_POST['emptycurrent']==1)
				{
					echo happy(i18n("Old school data erased"));
					mysql_query("DELETE FROM schools WHERE year='".$config['FAIRYEAR']."'");
				}

				$loaded=0;
				foreach($CSVP->data AS $row)
				{
					mysql_query("INSERT INTO schools (school,schoollang,schoollevel,board,district,phone,fax,address,city,province_code,postalcode,principal,schoolemail,sciencehead,scienceheademail,scienceheadphone,accesscode,registration_password,projectlimit,projectlimitper,year) VALUES (
						'".mysql_escape_string(stripslashes($row[0]))."',
						'".mysql_escape_string(stripslashes($row[1]))."',
						'".mysql_escape_string(stripslashes($row[2]))."',
						'".mysql_escape_string(stripslashes($row[3]))."',
						'".mysql_escape_string(stripslashes($row[4]))."',
						'".mysql_escape_string(stripslashes($row[5]))."',
						'".mysql_escape_string(stripslashes($row[6]))."',
						'".mysql_escape_string(stripslashes($row[7]))."',
						'".mysql_escape_string(stripslashes($row[8]))."',
						'".mysql_escape_string(stripslashes($row[9]))."',
						'".mysql_escape_string(stripslashes($row[10]))."',
						'".mysql_escape_string(stripslashes($row[11]))."',
						'".mysql_escape_string(stripslashes($row[12]))."',
						'".mysql_escape_string(stripslashes($row[13]))."',
						'".mysql_escape_string(stripslashes($row[14]))."',
						'".mysql_escape_string(stripslashes($row[15]))."',
						'".mysql_escape_string(stripslashes($row[16]))."',
						'".mysql_escape_string(stripslashes($row[17]))."',
						'".mysql_escape_string(stripslashes($row[18]))."',
						'".mysql_escape_string(stripslashes($row[19]))."',
						'".$config['FAIRYEAR']."')");
					if(!mysql_Error())
						$loaded++;
					else
						echo mysql_error();
				}
				echo happy(i18n("Successfully loaded %1 schools",array($loaded)));
				echo "<a href=\"schools.php\">".i18n("School Management")."</a> <br />";
			}
			else
			{
				echo error(i18n("Found no CSV data in the uploaded file"));
			}
			print_r($data);
		}
		else
		{
			echo error(i18n("Please choose a valid CSV file to upload"));
			$showform=true;
		}
	}

	if($showform)
	{
		echo "<br />";
		echo i18n("Choose the CSV file containing the school information.  The COLUMNS of the file must contain the following information, in this exact order, separated by comma's (,) with fields optionally enclosed by quotes (\"):");
		echo "<br />";
		echo "<br />";
		echo i18n("School Name, School Lang, School Level, Board, District, Phone, Fax, Address, City, %1, %2, Principal, School Email, Science Head, Science Head Email, Science Head Phone, Access Code, Registration Password, Project Limit, Project Limit Per(total or agecategory)",array(i18n($config['provincestate']),i18n($config['postalzip'])));

		echo "<br />";
		echo "<br />";
		echo "<form enctype=\"multipart/form-data\" action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"import\">";
		echo "<input type=\"checkbox\" name=\"emptycurrent\" value=\"1\">".i18n("Empty all current school information before importing?")."<br />";
		echo "<input type=\"file\" name=\"schools\">";
		echo "<input type=\"submit\"  value=\"".i18n("Upload School CSV")."\">\n";
		echo "</form>";

	}

	send_footer();

?>
