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
 	send_header(($_GET['action']=="edit") ? 'Edit Category' : 'New Category',
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php',
			'Age Categories' => 'config/categories.php'),"project_age_categories");
 } else {
 	send_header("Age Categories",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php'),"project_age_categories");
 }

 if($_POST['action']=="edit")
 {
 	//ues isset($_POST['mingrade']) instead of just $_POST['mingrade'] to allow entering 0 for kindergarden
 	if($_POST['id'] && $_POST['category'] && isset($_POST['mingrade']) && $_POST['maxgrade'])
	{
		$q=mysql_query("SELECT id FROM projectcategories WHERE id='".$_POST['id']."' AND year='".$config['FAIRYEAR']."'");
		echo mysql_error();
		if(mysql_num_rows($q) && $_POST['saveid']!=$_POST['id'])
		{
			echo error(i18n("Category ID %1 already exists",array($_POST['id']),array("category ID")));
		}
		else
		{
			mysql_query("UPDATE projectcategories SET ".
				"id='".$_POST['id']."', ".
				"category='".mysql_escape_string(stripslashes($_POST['category']))."', ".
				"category_shortform='".mysql_escape_string(stripslashes($_POST['category_shortform']))."', ".
				"mingrade='".$_POST['mingrade']."', ".
				"maxgrade='".$_POST['maxgrade']."' ".
				"WHERE id='".$_POST['saveid']."'");
			echo happy(i18n("Category successfully saved"));
		}
	}
	else
	{
		echo error(i18n("All fields are required"));
	}
 }

 if($_POST['action']=="new")
 {
 	//ues isset($_POST['mingrade']) instead of just $_POST['mingrade'] to allow entering 0 for kindergarden
 	if($_POST['id'] && $_POST['category'] && isset($_POST['mingrade']) && $_POST['maxgrade'])
	{
		$q=mysql_query("SELECT id FROM projectcategories WHERE id='".$_POST['id']."' AND year='".$config['FAIRYEAR']."'");
		if(mysql_num_rows($q))
		{
			echo error(i18n("Category ID %1 already exists",array($_POST['id']),array("category ID")));
		}
		else
		{
			mysql_query("INSERT INTO projectcategories (id,category,category_shortform,mingrade,maxgrade,year) VALUES ( ".
				"'".$_POST['id']."', ".
				"'".mysql_escape_string(stripslashes($_POST['category']))."', ".
				"'".mysql_escape_string(stripslashes($_POST['category_shortform']))."', ".
				"'".$_POST['mingrade']."', ".
				"'".$_POST['maxgrade']."', ".
				"'".$config['FAIRYEAR']."')");
			echo happy(i18n("Category successfully added"));
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
 	mysql_query("DELETE FROM projectcategoriesdivisions_link where projectcategories_id='".$_GET['remove']."' AND year='".$config['FAIRYEAR']."'");
	//####
	mysql_query("DELETE FROM projectcategories WHERE id='".$_GET['remove']."' AND year='".$config['FAIRYEAR']."'");
	echo happy(i18n("Category successfully removed"));
 }

 echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">";

 if(! ($_GET['action']=="edit" || $_GET['action']=="new") )
	echo "<a href=\"".$_SERVER['PHP_SELF']."?action=new\">".i18n("Add new age category")."</a>\n";

 echo "<table class=\"summarytable\">";
 echo "<tr>";
 echo "<th>".i18n("Category ID")."</th>\n";
 echo "<th>".i18n("Category Name")."</th>\n";
 echo "<th>".i18n("Shortform")."</th>\n";
 echo "<th>".i18n("Minimum Grade")."</th>\n";
 echo "<th>".i18n("Maximum Grade")."</th>\n";
 echo "<th>".i18n("Actions")."</th>\n";
 echo "</tr>";

 if($_GET['action']=="edit" || $_GET['action']=="new")
 {
 	echo "<input type=\"hidden\" name=\"action\" value=\"".$_GET['action']."\">\n";	
 	if($_GET['action']=="edit")
	{
 		echo "<input type=\"hidden\" name=\"saveid\" value=\"".$_GET['edit']."\">\n";	
		$q=mysql_query("SELECT * FROM projectcategories WHERE id='".$_GET['edit']."' AND year='".$config['FAIRYEAR']."'");
		$categoryr=mysql_fetch_object($q);
		$buttontext="Save";
	}
	else if($_GET['action']=="new")
	{
		$buttontext="Add";
	}
	echo "<tr>";
	echo " <td><input type=\"text\" size=\"3\" name=\"id\" value=\"$categoryr->id\"></td>";
	echo " <td><input type=\"text\" size=\"20\" name=\"category\" value=\"$categoryr->category\"></td>";
	echo " <td><input type=\"text\" size=\"5\" name=\"category_shortform\" value=\"$categoryr->category_shortform\"></td>";
	echo " <td><input type=\"text\" size=\"3\" name=\"mingrade\" value=\"$categoryr->mingrade\"></td>";
	echo " <td><input type=\"text\" size=\"3\" name=\"maxgrade\" value=\"$categoryr->maxgrade\"></td>";
	echo " <td><input type=\"submit\" value=\"".i18n($buttontext)."\"></td>";
	echo "</tr>";
 }
 else
 {
	 $q=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY mingrade");
	 while($r=mysql_fetch_object($q))
	 {
		echo "<tr>";
		echo " <td align=\"center\">$r->id</td>";
		echo " <td>".i18n($r->category)."</td>";
		echo " <td>".i18n($r->category_shortform)."</td>";
		echo " <td align=\"center\">$r->mingrade</td>";
		echo " <td align=\"center\">$r->maxgrade</td>";
		echo " <td align=\"center\">";
				echo "<a title=\"Edit\" href=\"".$_SERVER['PHP_SELF']."?action=edit&amp;edit=$r->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\" border=0></a>";
				echo "&nbsp; &nbsp;";
				echo "<a title=\"Remove\" onClick=\"return confirmClick('Are you sure you want to remove this age category?');\" href=\"".$_SERVER['PHP_SELF']."?action=remove&amp;remove=$r->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=0></a>";
		echo " </td>";
		echo "</tr>";
	 }
 }
 echo "</table>";
 echo "</form>";
 echo i18n("You should assign the 'Category ID's in numerical order, starting with 1.  This Category ID is used to generate the project number");

 send_footer();
?>
