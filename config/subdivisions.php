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
 user_auth_required('committee', 'config');
 if($_GET['action']=="edit" || $_GET['action']=="new") {
	 send_header(($_GET['action']=="edit") ? "Edit Sub-Division" : "New Sub-Division",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php',
			'Project Sub-Divisions' => 'config/subdivisions.php'),
            "project_sub_divisions");
 } else {
	 send_header("Project Sub-Divisions",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php'),
            "project_sub_divisions");
 }

 if($_POST['action']=="edit")
 {
 	if($_POST['id'] && $_POST['projectdivisions_id'] && $_POST['subdivision'] )
	{
		$q=mysql_query("SELECT id FROM projectsubdivisions WHERE id='".$_POST['id']."' AND year='".$config['FAIRYEAR']."'");
		if(mysql_num_rows($q) && $_POST['saveid']!=$_POST['id'])
		{
			echo error(i18n("Sub-Division ID %1 already exists",array($_POST['id'])));
		}
		else
		{
			mysql_query("UPDATE projectsubdivisions SET ".
				"id='".$_POST['id']."', ".
				"projectdivisions_id='".$_POST['projectdivisions_id']."', ".
				"subdivision='".mysql_escape_string(stripslashes($_POST['subdivision']))."' ".
				"WHERE id='".$_POST['saveid']."'");
			echo happy(i18n("Sub-Division successfully saved"));
		}
	}
	else
	{
		echo error(i18n("All fields are required"));
	}
 }

 if($_POST['action']=="new")
 {
 	if($_POST['projectdivisions_id'] && $_POST['subdivision'])
	{
		if(!$_POST['id'])
		{
			$idq=mysql_query("SELECT MAX(id) AS id FROM projectsubdivisions");
			$idr=mysql_fetch_object($idq);
			$newid=$idr->id+1;

		}
		else
			$newid=$_POST['id'];

		$q=mysql_query("SELECT id FROM projectsubdivisions WHERE id='$newid' AND year='".$config['FAIRYEAR']."'");
		if(mysql_num_rows($q))
		{
			echo error(i18n("Sub-Division ID %1 already exists",array($newid)));
		}
		else
		{
			mysql_query("INSERT INTO projectsubdivisions (id,projectdivisions_id,subdivision,year) VALUES ( ".
				"'$newid', ".
				"'".$_POST['projectdivisions_id']."', ".
				"'".mysql_escape_string(stripslashes($_POST['subdivision']))."', ".
				"'".$config['FAIRYEAR']."') ");
			echo happy(i18n("Sub-Division successfully added"));
		}
	}
	else
	{
		echo error(i18n("All fields except ID are required"));
	}
 }

 if($_GET['action']=="remove" && $_GET['remove'])
 {
	mysql_query("DELETE FROM projectsubdivisions WHERE id='".$_GET['remove']."'");
	echo happy(i18n("Sub-Division successfully removed"));
 }

 echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">";

 if(! ($_GET['action']=="edit" || $_GET['action']=="new") )
	echo "<a href=\"".$_SERVER['PHP_SELF']."?action=new\">".i18n("Add new sub-division")."</a>\n";

 echo "<table class=\"summarytable\">";
 echo "<tr>";
 echo "<th>".i18n("Parent Division")."</th>\n";
 echo "<th>".i18n("ID")."</th>\n";
 echo "<th>".i18n("Sub-Division")."</th>\n";
 echo "<th>".i18n("Actions")."</th>\n";
 echo "</tr>";

 if($_GET['action']=="edit" || $_GET['action']=="new")
 {
 	echo "<input type=\"hidden\" name=\"action\" value=\"".$_GET['action']."\">\n";	
 	if($_GET['action']=="edit")
	{
 		echo "<input type=\"hidden\" name=\"saveid\" value=\"".$_GET['edit']."\">\n";	
		$q=mysql_query("SELECT * FROM projectsubdivisions WHERE id='".$_GET['edit']."' AND year='".$config['FAIRYEAR']."'");
		$divisionr=mysql_fetch_object($q);
		$buttontext="Save";
	}
	else if($_GET['action']=="new")
	{
		$buttontext="Add";
	}
	echo "<tr>";
	echo " <td>";
	echo "<select name=\"projectdivisions_id\">";
	$dq=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY division");
	while($dr=mysql_fetch_object($dq))
	{
		if($dr->id==$divisionr->projectdivisions_id) $sel="selected=\"selected\""; else $sel="";
		echo "<option $sel value=\"$dr->id\">$dr->division</option>\n";
	}
	echo "</select>";
	echo "</td>";
	echo " <td><input type=\"text\" size=\"3\" name=\"id\" value=\"$divisionr->id\"></td>";
	echo " <td><input type=\"text\" size=\"30\" name=\"subdivision\" value=\"$divisionr->subdivision\"></td>";
	echo " <td><input type=\"submit\" value=\"".i18n($buttontext)."\"></td>";
	echo "</tr>";
 }
 else
 {
	 $q=mysql_query("SELECT projectsubdivisions.id, 
	 			projectsubdivisions.projectdivisions_id,
				projectsubdivisions.subdivision,
				projectdivisions.division
				FROM 
					projectsubdivisions,
					projectdivisions
				WHERE 
					projectsubdivisions.year='".$config['FAIRYEAR']."' 
					AND projectdivisions.year='".$config['FAIRYEAR']."'
					AND projectsubdivisions.projectdivisions_id=projectdivisions.id
				ORDER BY 
					division,subdivision");
echo mysql_error();
	 while($r=mysql_fetch_object($q))
	 {
		echo "<tr>";
		echo " <td>$r->division</td>";
		echo " <td>$r->id</td>";
		echo " <td>$r->subdivision</td>";
		echo " <td>";
				echo "<a title=\"Edit\" href=\"".$_SERVER['PHP_SELF']."?action=edit&amp;edit=$r->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\" border=0></a>";
				echo "&nbsp; &nbsp;";
				echo "<a title=\"Remove\" onClick=\"return confirmClick('Are you sure you want to remove this division?');\" href=\"".$_SERVER['PHP_SELF']."?action=remove&amp;remove=$r->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=0></a>";
		echo " </td>";
		echo "</tr>";
	 }
 }
 echo "</table>";
if($_GET['action']=="new")
	echo "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;".i18n("Leave ID field blank to auto-assign next available ID");
 echo "</form>";

 send_footer();
?>
