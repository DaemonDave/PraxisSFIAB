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
 user_auth_required('committee', 'admin');

 send_header("Awards", 
 	array('Committee Main' => 'committee_main.php',
		'Administration' => 'admin/index.php'),
        "awards_management" );

 require_once("rerollprizes.php");

 echo "<br />";
 echo "<a href=\"award_awards.php\">".i18n('Awards Management')."</a><br />";
 echo "<a href=\"donors.php\">".i18n('Sponsors')."</a><br />";
 echo "<br />";
 echo "<a href=\"award_awards.php?action=edit_prize_template\">".i18n('Edit prize template for the divisional awards')."</a>";
 echo "<br />";
 echo "<a href=\"award_awardcreatedivisional.php\">".i18n('Create divisional awards for all divisions & categories')."</a><br />";
 echo "<br />";
 echo "<a href=\"award_download.php\">".i18n('Download awards from external sources')."</a><br />";
 echo "<a href=\"award_upload.php\">".i18n('Upload award winners to external sources')."</a><br />";

 send_footer();

?>
