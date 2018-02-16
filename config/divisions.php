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
	send_header(($_GET['action']=="edit") ? "Edit Division" : "New Division",
			array('Committee Main' => 'committee_main.php',
				'SFIAB Configuration' => 'config/index.php',
				'Project Divisions' => 'config/divisions.php'),
                "project_divisions" );
 } else {
 	send_header("Project Divisions", 
			array('Committee Main' => 'committee_main.php',
				'SFIAB Configuration' => 'config/index.php'),
                "project_divisions");
 }

 
 if($_POST['action']=="edit")
 {
 	if($_POST['id'] && $_POST['division'] )
	{
		$q=mysql_query("SELECT id FROM projectdivisions WHERE id='".$_POST['id']."' AND year='".$config['FAIRYEAR']."'");
		if(mysql_num_rows($q) && $_POST['saveid']!=$_POST['id'])
		{
			echo error(i18n("Division ID %1 already exists",array($_POST['id']),array("division ID")));
		}
		else
		{
			mysql_query("UPDATE projectdivisions SET ".
				"id='".$_POST['id']."', ".
				"division='".mysql_escape_string(stripslashes($_POST['division']))."', ".
				"division_shortform='".mysql_escape_string(stripslashes($_POST['division_shortform']))."' ".
				"WHERE id='".$_POST['saveid']."' AND year='{$config['FAIRYEAR']}'");
				
			//###### Feature Specific - filtering divisions by category
 			if($config['filterdivisionbycategory']=="yes"){
				mysql_query("DELETE FROM projectcategoriesdivisions_link WHERE projectdivisions_id='".$_POST['saveid']."' AND year='".$config['FAIRYEAR']."'");
				
				if(is_array($_POST['divcat']))
				{
					foreach($_POST['divcat'] as $tempcat)
					{
						mysql_query("INSERT INTO projectcategoriesdivisions_link (projectdivisions_id,projectcategories_id,year) VALUES ( ".
						"'".$_POST['id']."', ".
						"'".$tempcat."', ".
						"'".$config['FAIRYEAR']."') ");
					}
				}
			}
			//###########
				
			echo happy(i18n("Division successfully saved"));
		}
	}
	else
	{
		echo error(i18n("All fields are required"));
	}
 }

 if($_POST['action']=="new")
 {
 	if($_POST['id'] && $_POST['division'])
	{
		$q=mysql_query("SELECT id FROM projectdivisions WHERE id='".$_POST['id']."' AND year='".$config['FAIRYEAR']."'");
		if(mysql_num_rows($q))
		{
			echo error(i18n("Division ID %1 already exists",array($_POST['id']),array("division ID")));
		}
		else
		{
			mysql_query("INSERT INTO projectdivisions (id,division,division_shortform,year) VALUES ( ".
				"'".$_POST['id']."', ".
				"'".mysql_escape_string(stripslashes($_POST['division']))."', ".
				"'".mysql_escape_string(stripslashes($_POST['division_shortform']))."', ".
				"'".$config['FAIRYEAR']."') ");
				
				
			//###### Feature Specific - filtering divisions by category
 			if($config['filterdivisionbycategory']=="yes"){
				foreach($_POST['divcat'] as $tempcat){
					mysql_query("INSERT INTO projectcategoriesdivisions_link (projectdivisions_id,projectcategories_id,year) VALUES ( ".
						"'".$tempcat."', ".
						"'".$config['FAIRYEAR']."') ");
				}
			}
			//#######
			echo happy(i18n("Division successfully added"));
		}
	}
	else
	{
		echo error(i18n("All fields are required"));
	}
 }

 if($_GET['action']=="remove" && $_GET['remove'])
 {
 	//###### Feature Specific - filtering divisions by category - not conditional, cause even if they have the filtering turned off..if any links
	//for this division exist they should be deleted
 	mysql_query("DELETE FROM projectcategoriesdivisions_link where projectdivisions_id='".$_GET['remove']."' AND year='".$config['FAIRYEAR']."'");
	mysql_query("DELETE FROM projectdivisions WHERE id='".$_GET['remove']."' AND year='".$config['FAIRYEAR']."'");
	echo happy(i18n("Division successfully removed"));
 }

 echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">";

 if(! ($_GET['action']=="edit" || $_GET['action']=="new") )
	echo "<a href=\"".$_SERVER['PHP_SELF']."?action=new\">".i18n("Add new division")."</a>\n";

 echo "<table class=\"summarytable\">";
 echo "<tr>";
 echo "<th>".i18n("Division ID")."</th>\n";
 echo "<th>".i18n("Division Name")."</th>\n";
 echo "<th>".i18n("Short Form")."</th>\n";
//###### Feature Specific - filtering divisions by category
 if($config['filterdivisionbycategory']=="yes")
 	echo "<th>".i18n("Categories")."</th>\n";
//#####
 echo "<th>".i18n("Actions")."</th>\n";
 echo "</tr>";

 if($_GET['action']=="edit" || $_GET['action']=="new")
 {
 	echo "<input type=\"hidden\" name=\"action\" value=\"".$_GET['action']."\">\n";	
 	if($_GET['action']=="edit")
	{
 		echo "<input type=\"hidden\" name=\"saveid\" value=\"".$_GET['edit']."\">\n";	
		$q=mysql_query("SELECT * FROM projectdivisions WHERE id='".$_GET['edit']."' AND year='".$config['FAIRYEAR']."'");
		$divisionr=mysql_fetch_object($q);
		$buttontext="Save";
	}
	else if($_GET['action']=="new")
	{
		$buttontext="Add";
	}
	echo "<tr>";
	echo " <td><input type=\"text\" size=\"3\" name=\"id\" value=\"$divisionr->id\" /></td>";
	echo " <td><input type=\"text\" size=\"40\" name=\"division\" value=\"$divisionr->division\" /></td>";
	echo " <td align=\"center\"><input type=\"text\" size=\"5\" name=\"division_shortform\" value=\"$divisionr->division_shortform\" /></td>";
	
	//###### Feature Specific - filtering divisions by category
	if($config['filterdivisionbycategory']=="yes"){
		echo " <td>";
		$q=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY mingrade");
		while($categoryr=mysql_fetch_object($q)){
			$query="SELECT * FROM projectcategoriesdivisions_link WHERE projectdivisions_id=".$divisionr->id." AND projectcategories_id=".$categoryr->id." AND year='".$config['FAIRYEAR']."'";
			$t=mysql_query($query);
			if($t && mysql_num_rows($t)>0)
				echo "<nobr><input type=\"checkbox\" name=\"divcat[]\" value=\"$categoryr->id\" checked=\"checked\" /> $categoryr->category</nobr><br/>";
			else
				echo "<nobr><input type=\"checkbox\" name=\"divcat[]\" value=\"$categoryr->id\" /> $categoryr->category</nobr><br/>";
		
		}
		echo "</td>";
	}
	
	echo " <td><input type=\"submit\" value=\"".i18n($buttontext)."\" /></td>";
	echo "</tr>";
 }
 else
 {
	 $q=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
	 while($r=mysql_fetch_object($q))
	 {
		echo "<tr>";
		echo " <td>$r->id</td>";
		echo " <td>".i18n($r->division)."</td>";
		echo " <td align=\"center\">$r->division_shortform</td>";
		//###### Feature Specific - filtering divisions by category
 		if($config['filterdivisionbycategory']=="yes"){

			$c=mysql_query("SELECT category FROM projectcategoriesdivisions_link, projectcategories 
					WHERE projectcategoriesdivisions_link.projectcategories_id = projectcategories.id
				AND projectdivisions_id='$r->id'
				AND projectcategoriesdivisions_link.year='".$config['FAIRYEAR']."'
				AND projectcategories.year='".$config['FAIRYEAR']."'
				ORDER BY projectcategories.mingrade");
				echo mysql_error();
			if(!$c){
				$tempcat="&nbsp;";
			}else{
				$tempcat="";
				while($categoryr=mysql_fetch_object($c)){
					$tempcat.=",".$categoryr->category;
				}
				$tempcat=substr($tempcat,1);
			}
			echo "<td> {$tempcat} </td>";
		}
		//############
		echo " <td>";
				echo "<a title=\"Edit\" href=\"".$_SERVER['PHP_SELF']."?action=edit&amp;edit=$r->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\" border=0></a>";
				echo "&nbsp; &nbsp;";
				echo "<a title=\"Remove\" onClick=\"return confirmClick('Are you sure you want to remove this division?');\" href=\"".$_SERVER['PHP_SELF']."?action=remove&amp;remove=$r->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=0></a>";
		echo " </td>";
		echo "</tr>";
	 }
 }
 echo "</table>";
 echo "</form>";
 echo i18n("You should assign the 'Division ID's in numerical order, starting with 1.  This Division ID is used to generate the project number");

 send_footer();
?>
