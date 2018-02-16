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
 send_header("CWSF Project Divisions",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"cwsf_project_divisions"
			);

 if(count($_POST['cwsfdivision']))
 {
 	foreach($_POST['cwsfdivision'] AS $k=>$v)
	{
		mysql_query("UPDATE projectdivisions SET cwsfdivisionid='$v' WHERE id='$k' AND year='".$config['FAIRYEAR']."'");
	}
	echo happy(i18n("Corresponding CWSF divisions saved"));
 }

 echo "<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">";


 echo i18n("For each of your divisions, please select a corresponding CWSF division if applicable.  If no corresponding CWSF division is chosen then you will need to manually select the CWSF division for each project that you register for the CWSF.  You can select the 'most likely' division to use as a default which can then be changed on a per-project basis when you perform the automatic CWSF registration");

echo "<br />";
echo "<br />";
 echo "<table class=\"summarytable\">";
 echo "<tr>";
 echo "<th>".i18n("Your Division")."</th>\n";
 echo "<th>".i18n("Corresponding CWSF Division")."</th>\n";
 echo "</tr>";

	 $q=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
	 while($r=mysql_fetch_object($q))
	 {
		echo "<tr>";
		echo " <td>".i18n($r->division)."</td>";
		echo " <td>";
		echo "<select name=\"cwsfdivision[$r->id]\">";
		echo "<option value=\"\">".i18n("No corresponding CWSF division")."</option>\n";
		foreach($CWSFDivisions AS $k=>$v)
		{
			if($k==$r->cwsfdivisionid) $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"$k\">".i18n($v)."</option>\n";
		}
		echo "</select>\n";
		echo " </td>";
		echo "</tr>";
	 }
	 echo "<tr><td colspan=\"2\" align=\"center\">";
	 echo "<input type=\"submit\" value=\"".i18n("Save Corresponding CWSF Divisions")."\">";
	 echo "</td></tr>";
 echo "</table>";
 echo "</form>";

 send_footer();
?>
