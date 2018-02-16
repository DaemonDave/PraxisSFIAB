<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005-2006 Sci-Tech Ontario Inc <info@scitechontario.org>
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
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'config');

 send_header("SFIAB Configuration",
 		array('Committee Main' => 'committee_main.php')
		,"configuration"
		);


 echo "<table class=\"adminconfigtable\">";
 echo " <tr>";
 echo "  <td><a href=\"variables.php\">".theme_icon("configuration_variables")."<br />".i18n("Configuration Variables")."</a></td>";
 echo "  <td><a href=\"dates.php\">".theme_icon("important_dates")."<br />".i18n("Important Dates")."</a></td>";
 echo "  <td><a href=\"categories.php\">".theme_icon("project_age_categories")."<br />".i18n("Project Age Categories")."</a></td>";
 echo "  <td><a href=\"divisions.php\">".theme_icon("project_divisions")."<br />".i18n("Project Divisions")."</a></td>";
 echo " </tr>";
 echo " <tr>";
 echo "  <td><a href=\"divisions_cwsf.php\">".theme_icon("cwsf_project_divisions")."<br />".i18n("CWSF Project Divisions")."</a></td>";
 echo "  <td><a href=\"subdivisions.php\">".theme_icon("project_sub_divisions")."<br />".i18n("Project Sub-Divisions")."</a></td>";
 echo "  <td><a href=\"pagetexts.php\">".theme_icon("page_texts")."<br />".i18n("Page Texts")."</a></td>";
 echo "  <td><a href=\"signaturepage.php\">".theme_icon("exhibitor_signature_page")."<br />".i18n("Exhibitor Signature Page")."</a></td>";
 echo " </tr>\n";
 echo " <tr>";
 echo "  <td><a href=\"judges_questions.php\">".theme_icon("judge_registration_questions")."<br />".i18n("Judge Registration Questions")."</a></td>";
 echo "  <td><a href=\"safetyquestions.php\">".theme_icon("project_safety_questions")."<br />".i18n("Project Safety Questions")."</a></td>";
 echo "  <td><a href=\"images.php\">".theme_icon("images")."<br />".i18n("Images (Fair Logo)")."</a></td>";
 echo "  <td></td>";
 echo " </tr>\n";
 echo "</table>\n";
 echo "<hr />";
 echo "<table class=\"adminconfigtable\">";
 echo " <tr>";
 echo "  <td><a href=\"languagepacks.php\">".theme_icon("language_pack_installer")."<br />".i18n("Language Pack Installer")."</a></td>";
 echo "  <td><a href=\"versionchecker.php\">".theme_icon("new_version_checker")."<br />".i18n("New Version Checker")."</a></td>";
 echo "  <td></td>\n";
 echo "  <td></td>\n";
 echo " </tr>";
 echo "</table>\n";
 echo "<hr />";
 echo "<table class=\"adminconfigtable\">";
 echo " <tr>";
 echo "  <td><a href=\"rollover.php\">".theme_icon("rollover_fair_year")."<br />".i18n("Rollover Fair Year")."</a></td>";
 echo "  <td><a href=\"rolloverfiscal.php\">".theme_icon("rollover_fiscal_year")."<br />".i18n("Rollover Fiscal Year")."</a></td>";
 echo "  <td><a href=\"backuprestore.php\">".theme_icon("backup_restore")."<br />".i18n("Database Backup/Restore")."</a></td>";
 echo "  <td></td>\n";
 echo "  <td></td>\n";
 echo " </tr>";
 echo "</table>\n";

 send_footer();
?>
