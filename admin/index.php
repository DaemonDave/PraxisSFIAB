<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005-2008 James Grant <james@lightbox.org>

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
 require_once("../common.inc.php");
 require_once("../user.inc.php");
 require_once("../committee.inc.php");

 user_auth_required('committee','admin');

 send_header("Administration",
 	array('Committee Main' => 'committee_main.php'),
	"administration");

 echo "<table class=\"adminconfigtable\">";
 echo " <tr>";
 echo "  <td><a href=\"registration.php\">".theme_icon("participant_registration")."<br />".i18n("Participant Registration")."</a></td>";
 echo "  <td><a href=\"committees.php\">".theme_icon("committee_management")."<br />".i18n("Committee Management")."</a></td>";
 echo "  <td><a href=\"judges.php\">".theme_icon("judging_management")."<br />".i18n("Judging Management")."</a></td>";
 echo "  <td>";
 if($config['volunteer_enable'] == 'yes')
	echo "<a href=\"volunteers.php\">".theme_icon("volunteer_management")."<br />".i18n("Volunteer Management")."</a>";
 else
	echo theme_icon("volunteer_management")."<br />".i18n("Volunteer Management")."<br /><i>(".i18n("disabled").")</i>";
 echo "</td></tr>";
 echo "</table>\n";
 echo "<hr />";
 echo "<table class=\"adminconfigtable\">";
 echo " <tr>";
 echo "  <td><a href=\"awards.php\">".theme_icon("awards_management")."<br />".i18n("Awards Management")."</a></td>";
 echo "  <td><a href=\"schools.php\">".theme_icon("schools_management")."<br />".i18n("Schools Management")."</a></td>";
 echo "  <td>";
 if($config['tours_enable'] == 'yes')
 	echo "<a href=\"tours.php\">".theme_icon("tour_management")."<br />".i18n("Tour Management")."</a>";
 else
 	echo theme_icon("tour_management")."<br />".i18n("Tour Management")."<br /><i>(".i18n("disabled").")</i>";
 echo "</td>";
 echo "  <td>";
 if($config['participant_regfee_items_enable'] == 'yes')
 	echo "<a href=\"regfee_items_manager.php\">".theme_icon("registration_fee_items_management")."<br />".i18n("Registration Fee Items Management")."</a>";
 else
 	echo theme_icon("registration_fee_items_management")."<br />".i18n("Registration Fee Items Management")."<br /><i>(".i18n("disabled").")</i>";
 echo "</td>";
 echo " </tr>\n";
 echo " <tr>";
 echo "  <td><a href=\"reports.php\">".theme_icon("print/export_reports")."<br />".i18n("Print / Export Reports")."</a></td>";
 echo "  <td><a href=\"reports_ceremony.php\">".theme_icon("print_awards_ceremony_scripts")."<br />".i18n("Print Award Ceremony Scripts")."</a></td>";
 echo "  <td><a href=\"reports_editor.php\">".theme_icon("report_management")."<br />".i18n("Report Management")."</a></td>";
 echo "  <td><a href=\"translations.php\">".theme_icon("translations_management")."<br />".i18n("Translations Management")."</a></td>";
 echo "  <td>";
 echo "</td>";
 echo " </tr>\n";
 echo " <tr>";
 echo "<td></td><td></td>\n";
 echo " </tr>\n";
 echo "</table>\n";
 echo "<hr />";
 echo "<table class=\"adminconfigtable\">";
 echo " <tr>";
//
/// MODIFIED DRE 2018
//
 if($config['score_entry_enable'] == 'yes') 
 {
	echo "<td><a href=\"judging_score_entry.php\">".theme_icon("judging_score_entry")."<br />".i18n("Judging Score Entry")."</a></td>";
	echo "<td><a href=\"judging_score_edit.php\"><img border=0 src=\"{$config['SFIABDIRECTORY']}/images/32/spreadsheet.png\"><br />".i18n("Scorecard Spreadsheet")."</a></td>";
 }
 echo "  <td><a href=\"winners.php\">".theme_icon("enter_winning_projects")."<br />".i18n("Enter Winning Projects")."</a></td>";
 echo "  <td><a href=\"cwsfregister.php\">".theme_icon("one-click_cwsf_registration")."<br />".i18n("One-Click CWSF Registration")."</a></td>";
 echo "  <td><a href=\"fair_stats.php\">".theme_icon("fair_stats")."<br />".i18n("Upload Fair Statistics")."</a></td>";
 echo "  <td><a href=\"user_list.php?show_types[]=fair\">".theme_icon("sciencefair_management")."<br />".i18n("Feeder/Upstream Fair Management")."</a></td>";
 echo " </tr>\n";
 echo "</table>\n";
 echo "<hr />";
 echo "<table class=\"adminconfigtable\">";
 echo " <tr>";
 echo "  <td><a href=\"communication.php\">".theme_icon("communication")."<br />".i18n("Communication (Send Emails)")."</a></td>";
 echo "  <td><a href=\"documents.php\">".theme_icon("internal_document_management")."<br />".i18n("Internal Document Management")."</a></td>";
 echo "  <td><a href=\"cms.php\">".theme_icon("website_content_management")."<br />".i18n("Website Content Management")."</a></td>";
 echo "  <td><a href=\"fundraising.php\">".theme_icon("fundraising")."<br />".i18n("Fundraising")."</a></td>";
 echo "  <td></td>";
 echo " </tr>\n";
 echo "</table>\n";

 send_footer();
?>
