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
 send_header("Participant Registration",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "participant_registration"
			);
 echo "<br />";
 echo "<a href=\"registration_receivedforms.php\">".i18n("Input Received Signature Forms")."</a> <br />";
 echo "<a href=\"registration_list.php\">".i18n("Registration List and Student/Project Editor")."</a> <br />";
 echo "<a href=\"registration_stats.php\">".i18n("Registration Statistics")."</a> <br />";
 echo "<a href=\"registration_webconsent.php\">".i18n("Website Consent")."</a> <br />";



 send_footer();
?>
