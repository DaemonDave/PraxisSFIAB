<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>
   Copyright (C) 2007 David Grant <dave@lightbox.org>

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
 require_once("common.inc.php");
 require_once("user.inc.php");
 require_once("user_page.inc.php");

 user_auth_required('committee');

 send_header("Committee Main", array());

 //only display the named greeting if we have their name
 echo i18n("Hello <b>%1</b>",array($_SESSION['name']));
 echo "<br />";
 echo "<br />";

 echo "<table class=\"adminconfigtable\">";
 echo " <tr>";
 if(committee_auth_has_access('config')) {
	 echo " <td><a href=\"config/index.php\">".theme_icon("configuration")."<br />".i18n("Configure SFIAB")."</a></td>";
 }
 if(committee_auth_has_access('admin')) {
	 echo " <td><a href=\"admin/index.php\">".theme_icon("administration")."<br />".i18n("Administer the Fair")."</a></td>";
	 echo " <td><a href=\"admin/reports.php\">".theme_icon("print/export_reports")."<br />".i18n("My Reports (View/Print/Edit)")."</a></td>";
 }
 echo " </tr>\n";
 echo " <tr>\n";
 echo "  <td><a href=\"user_personal.php\">".theme_icon("edit_profile")."<br />".i18n("Edit My Profile")."</a></td>";
 echo "  <td><a href=\"user_password.php\">".theme_icon("change_password")."<br />".i18n("Change My Password")."</a></td>";
 echo "  <td><a href=\"user_activate.php\">".theme_icon("manage_roles")."<br />".i18n("Manage My Roles")."</a></td>";
 echo " </tr>\n";
 echo "</table>\n";

 send_footer();
?>
