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
 send_header("Reports",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php')
			);
 echo "<br />";
 echo error('This page will no longer be available after Summer 2008.  Please use the new \'My Reports\' interface. ');

 $id = intval($_POST['id']);
 echo "<h4>".i18n("All Reports")."</h4>";
 echo "<form method=\"get\" name=\"reportgen\" action=\"reports_gen.php\">";
 echo "<select name=\"id\" id=\"report\">";
 echo "<option value=\"0\">".i18n("Select a Report")."</option>\n";
 $reports = report_load_all();
 $x=0;
 foreach($reports as $r) {
	$sel = ($id == $r['id']) ? 'selected=\"selected\"' : '';
	echo "<option value=\"{$r['id']}\" $sel>{$r['name']}</option>\n";
 }
 echo "</select>";
 echo "<select name=\"type\"><option value=\"\">Default Format</option>";
 echo "<option value=\"pdf\">PDF</option>";
 echo "<option value=\"csv\">CSV</option>";
 echo "<option value=\"label\">Label</option>";
 echo "</select>";
 echo "<input type=\"text\" value=\"{$config['FAIRYEAR']}\" size=\"5\" name=\"year\" />";
 echo "<input type=\"submit\" value=\"Generate Report\"></form>";
 echo "<br />";
 echo "<hr />";
			   
			   
 echo "<h4>".i18n("Custom Reports")."</h4>";

/*
 echo i18n("Day of Fair Registration/Checkin Forms (All Categories)").": ";
 echo "<a href=\"reports_gen.php?id=9&type=pdf\">PDF</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?id=9&type=csv\">CSV</a> &nbsp; ";
 */

//lets split this up by age category, 
/*
$catq=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
while($catr=mysql_fetch_object($catq))
{
	echo "<td>";
	echo "<a href=\"reports_checkin.php?type=pdf&cat=$catr->id\">$catr->category (PDF)</a> &nbsp; ";
	echo "<br>";
	echo "<a href=\"reports_checkin.php?type=csv&cat=$catr->id\">$catr->category (CSV)</a> &nbsp; ";
	echo "</td>";
}
*/
 echo "<br />";
 echo i18n("Mailing Labels").": ";
 echo "<a href=\"reports_mailinglabels.php\">".i18n("Mailing Label Generator")."</a>";

 echo "<br />";
 echo "<br />";
 echo i18n("School Access Codes").": ";
 echo "<a href=\"reports_gen.php?sid=36&type=pdf\">PDF</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=36&type=csv\">CSV</a> &nbsp; ";
// echo "<a href=\"reports_schoolaccesscodes.php?type=pdf\">PDF</a> &nbsp; ";
// echo "<a href=\"reports_schoolaccesscodes.php?type=csv\">CSV</a> &nbsp; ";

 echo "<br />";
 echo i18n("Student Emergency Contact Names/Numbers").": ";
 echo "<a href=\"reports_gen.php?sid=17&type=pdf\">PDF</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=17&type=csv\">CSV</a> &nbsp; ";
 echo "<br />";
 echo i18n("Students/Projects From Each School").": ";
 echo "<a href=\"reports_gen.php?sid=19&type=pdf\">PDF</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=19&type=csv\">CSV</a> &nbsp; ";

 echo "<br />";
 echo i18n("Project Logistical Requirements (tables, electricity)").": ";
 echo "<a href=\"reports_gen.php?sid=16&type=pdf\">PDF</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=16&type=csv\">CSV</a> &nbsp; ";
 echo "<br />";

 echo i18n("Project Table Labels").": ";
 echo "<a href=\"reports_gen.php?sid=30\">PDF</a> &nbsp; ";
 echo "<br />";

  echo i18n("Project Summary Details").": ";
 echo "<a href=\"reports_projects_details.php?type=pdf\">PDF</a> &nbsp; ";
 echo "<br />";
 echo i18n("Nametags").": ";
 echo "<a href=\"reports_gen.php?sid=26\">Students PDF</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=26&type=csv\">Students CSV</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=27\">Judges PDF</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=27&type=csv\">Judges CSV</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=28\">Committee PDF</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=28&type=csv\">Committee CSV</a> &nbsp; ";

 echo "<br />";
 echo "<br />";
 echo i18n("Judges List").": ";
 echo "<a href=\"reports_judges.php?type=csv\">Judge List (CSV)</a> &nbsp; ";

 echo "<br />";
 echo i18n("Judging Teams").": ";
 echo "<a href=\"reports_gen.php?sid=21&type=csv\">List (CSV)</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=21&type=pdf\">List (PDF)</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=22&type=csv\">Team Awards (CSV)</a> &nbsp; ";
 echo "<a href=\"reports_gen.php?sid=22&type=pdf\">Team Awards (PDF)</a> &nbsp; ";

 echo "<br />";
 echo i18n("Judging Teams Project Assignments").": ";
 echo "<a href=\"reports_judges_teams_projects.php?type=csv\">CSV</a> &nbsp; ";
 echo "<a href=\"reports_judges_teams_projects.php?type=pdf\">PDF</a> &nbsp; ";

 echo "<br />";
 echo i18n("Projects Judging Team Assignments").": ";
 echo "<a href=\"reports_projects_judges_teams.php?type=csv\">CSV</a> &nbsp; ";
 echo "<a href=\"reports_projects_judges_teams.php?type=pdf\">PDF</a> &nbsp; ";

 echo "<br />";
 echo i18n("Project Identification Labels (for judging sheets)").": ";
 echo "<a href=\"reports_gen.php?sid=29\">PDF</a> &nbsp; ";
 echo "<br />";
 echo "<br />";


 echo i18n("Awards list for Program").": ";
 echo "<a href=\"reports_program_awards.php?type=csv\">CSV</a> &nbsp; ";

  
 echo "<br />";
 echo i18n("Award Ceremony Script").": ";
 echo "<a href=\"reports_acscript.php?type=pdf\">FULL PDF</a> &nbsp;";
 echo "<a href=\"reports_acscript.php?type=pdf&awardtype=Divisional\">(Divisional)</a> &nbsp; ";
 echo "<a href=\"reports_acscript.php?type=pdf&awardtype=Special\">(Special)</a> &nbsp; ";
 echo "<a href=\"reports_acscript.php?type=pdf&awardtype=Interdisciplinary\">(Interdisciplinary)</a> &nbsp; ";
 echo "<a href=\"reports_acscript.php?type=pdf&awardtype=Other\">(Other)</a> &nbsp; ";
 echo "<a href=\"reports_acscript.php?type=pdf&awardtype=Grand\">(Grand)</a> &nbsp; ";
 echo "<br />";
 echo i18n("Award Ceremony Script").": ";
 echo "<a href=\"reports_acscript.php?type=csv\">CSV</a> &nbsp; ";
 echo "<br />";
 echo "<a href=\"reports_gen.php?sid=42\">Award Winners CSV</a> &nbsp; ";
 echo "<br />";

 send_footer();
?>
