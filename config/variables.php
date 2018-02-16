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
 require_once("../config_editor.inc.php");
 user_auth_required('committee', 'config');

 $q=mysql_query("SELECT * FROM config WHERE year='-1'");
 while($r=mysql_fetch_object($q)) {
	mysql_query("INSERT INTO config (var,val,category,type,type_values,ord,description,year) VALUES (
		'".mysql_escape_string($r->var)."',
		'".mysql_escape_string($r->val)."',
		'".mysql_escape_string($r->category)."',
		'".mysql_escape_string($r->type)."',
		'".mysql_escape_string($r->type_values)."',
		'".mysql_escape_string($r->ord)."',
		'".mysql_escape_string($r->description)."',
		'".$config['FAIRYEAR']."')");
 }

 //for the Special category
 if($_POST['action']=="save") {
 	if($_POST['specialconfig']) {
		foreach($_POST['specialconfig'] as $key=>$val) {
			mysql_query("UPDATE config SET val='".mysql_escape_string(stripslashes($val))."' WHERE year='0' AND var='$key'");
		}
	}
	message_push(happy(i18n("Configuration successfully saved")));
 }

 //get the category, and if nothing is chosen, default to Global
 if($_GET['category']) $category=$_GET['category'];
 else if($_POST['category']) $category=$_POST['category'];
 else $category="Global";

 $action = config_editor_handle_actions($category, $config['FAIRYEAR'], "var");
 if($action == 'update') {
 	header("Location: variables.php?category=$category");
	exit;
 }

  send_header("Configuration Variables",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"configuration_variables"
			);


 $q=mysql_query("SELECT DISTINCT(category) AS cat FROM config ORDER BY cat");
 echo "\n<table valign=\"top\" cellspacing=0 cellpadding=5 border=0>";

 echo "<tr><td width=\"120\" style=\"border-right: 1px solid black;\">";
 echo "<table cellspacing=0 cellpadding=3 border=0>";
 $trclass = 'odd';
 while($r=mysql_fetch_object($q)) {
 	$trclass = ($trclass == 'odd') ? 'even' : 'odd';
 	echo "<tr class=\"$trclass\">";
 	echo "<td align=\"right\">"; 
 	if($r->cat==$category)
		echo "<b>".i18n($r->cat)."</b>";
	else
		echo "<a href=\"".$_SERVER['PHP_SELF']."?category=".urlencode($r->cat)."\">".i18n($r->cat)."</a>";
	echo "</td>";
 	echo "</tr>\n";
 }
 echo "</table>";

 echo "</td><td>";

 if($category) 
 {
	if($category=="Special") 
	{
		echo "<h3>".i18n("Special Configuration Settings")."</h3>";
		echo "<form method=\"post\" action=\"variables.php\">";
		echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
		echo "<input type=\"hidden\" name=\"category\" value=\"Special\">\n";
		echo "<table cellpadding=\"3\">";
		$q=mysql_query("SELECT * FROM config WHERE year=0 ORDER BY var");
		echo "<tr><td colspan=\"2\">";
		echo i18n("Warning, modifying values on this configuration variables page could cause your SFIAB to stop working.  Only change anything on this page if you really know what you are doing");
		echo "</td></tr>";
		while($r=mysql_fetch_object($q)) 
		{
			if($r->var=="FAIRYEAR" || $r->var=="DBVERSION" || $r->var=="FISCALYEAR") {
				echo "<tr><td><b>$r->var</b> - ".i18n($r->description)."</td><td>$r->val</td></tr>";
			}
			else {
				echo "<tr><td><b>$r->var</b> - ".i18n($r->description)."</td><td><input type=\"text\" name=\"specialconfig[$r->var]\" value=\"$r->val\" /></td></tr>";
			}
		}
		echo "</table>";
		echo "<input type=\"submit\" value=\"".i18n("Save Configuration")."\" />\n";
		echo "</form>";
	}
	else {
//		echo "<h3>".i18n("Configuration settings for fair year %1",array($config['FAIRYEAR']),array("fair year"))."</h3>";
		echo "<h3>".i18n($category)." ({$config['FAIRYEAR']})</h3>";

		config_editor($category, $config['FAIRYEAR'], "var", $_SERVER['PHP_SELF']);
	}
}
else {
	echo i18n("Please choose a configuration category");
}

 echo "</td></tr></table>";

 send_footer();
?>
