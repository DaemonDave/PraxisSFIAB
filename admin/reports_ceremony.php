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
 require_once('reports.inc.php');
 user_auth_required('committee', 'admin');
 send_header("Award Ceremony Scripts",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "print_awards_ceremony_scripts"
			);
 echo "<br />";
 echo "<form action=\"reports_acscript.php\" method=\"get\">";
 echo "<table class=\"tableedit\">";
 
 echo "<tr><td><b>".i18n("Year").":</b></td><td>";

 //get the year information, use fairname since it should be there for all years[right?]
 $results = mysql_query("SELECT year FROM config WHERE var='fairname' AND year > 0 ORDER BY year DESC");
 
 echo "<select name=\"year\" size=1>";
 while($r=mysql_fetch_object($results)) {
 	echo "<option>$r->year</option>";
 }
 echo "</select></td></tr>";

 //list output formats
 echo "<tr><td>
 		<b>".i18n("Type").":</b>
	   </td>
	   <td>
	   	<select name=\"type\" size=1>
			<option value=\"pdf\">PDF</option>
			<option value=\"csv\">CSV</option>
	</select></td>";

 echo "</td></tr>\n";
 echo "<tr>";
 //list award subsets to output
 echo "<td><b>".i18n("Award Type").":</b></td> <td> <select name=\"awardtype\" size=1>";
 $results = mysql_query("SELECT type FROM award_types WHERE year=".$config['FAIRYEAR']." ORDER BY type");
 echo "<option value=\"All\">".i18n("All")."</option>";
 while($r=mysql_fetch_object($results)) {
 	echo "<option value=\"$r->type\">".i18n("$r->type")."</option>";
 }
 echo "</select></td>";
 echo "</td></tr>\n";
 echo "<tr>";

 //list award formats to output
 echo "<td>
	<b>".i18n("Script Format").":</b>
	</td> 
	<td> 
		<select name=\"scriptformat\" size=1>
			<option value=\"default\">Default</option>
			<option value=\"formatted\">Formatted</option>
 	</select></td></tr>";

 echo "<tr><td ><b>".i18n("Show awards without winners").":</b></td>";
 echo "<td><input name=\"show_unawarded_awards\" type=\"checkbox\" ".($config['reports_show_unawarded_awards'] == 'yes' ? "checked" : "")."/></td></tr>";
 echo "<tr><td ><b>".i18n("Show prizes without winners").":</b></td>";
 echo "<td><input name=\"show_unawarded_prizes\" type=\"checkbox\" ".($config['reports_show_unawarded_prizes'] == 'yes' ? "checked" : "")."/></td></tr>";
 echo "<tr><td ><b>".i18n("Show criteria for each award").":</b></td>";
 echo "<td><input name=\"show_criteria\" type=\"checkbox\" ".($config['reports_show_criteria'] == 'yes' ? "checked" : "")." value=\"on\"/></td></tr>";
 echo "<tr><td ><b>".i18n("Show student name pronunciation").":</b></td>";
 echo "<td><input name=\"show_pronunciation\" type=\"checkbox\" /></td></tr>";
 echo "<tr><td width=\"30%\"><b>".i18n("Group divisional results by Prize (instead of Award Name).  This groups all the honourable mentions in all divisions together, all the bronzes together, etc."). ":</b></td>";
 echo "<td><input name=\"group_by_prize\" type=\"checkbox\" /></td></tr>";

 echo "<tr><td><b>".i18n("Include the following age categories").":</b></td>";
 echo "<td>";
 $q=mysql_query("SELECT * FROM projectcategories WHERE year='{$config['FAIRYEAR']}' ORDER BY id");
 while($r=mysql_fetch_object($q)) {
	echo "<input name=\"show_category[{$r->id}]\" type=\"checkbox\" checked=\"checked\" />";
	echo "".i18n($r->category)."<br />";
 }

 echo "</table>";
 echo "<input type=\"submit\" value=\"".i18n("Generate Script")."\" />";
 echo "</form>";

 send_footer();
?>
