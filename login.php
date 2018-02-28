<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2008 James Grant <james@lightbox.org>

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
 send_header("Login/Register");

 echo i18n("Please choose one of the following:");
 echo "<br />\n";
 echo "<br />\n";
 echo "<h4>".i18n("Participant")."</h4>\n";
 echo "<ul style=\"margin-top: 0px;\">";
 echo "<li><a href=\"register_participants.php\">".i18n("I am a participant")."</a></li>\n";
 echo "</ul>\n";

 echo "<h4>".i18n("Judge")."</h4>\n";
 echo "<ul style=\"margin-top: 0px;\">";
 echo "<li><a href=\"user_login.php?type=judge\">".i18n("I am a judge and I already have an account")."</a></li>\n";
 echo "<li><a href=\"user_new.php?type=judge\">".i18n("I am a new judge and would like to register to judge")."</a></li>\n";
 echo "</ul>\n";

 echo "<h4>".i18n("Sponsor")."</h4>\n";
 echo "<ul style=\"margin-top: 0px;\">";
 echo "<li><a href=\"user_login.php?type=sponsor\">".i18n("I am an existing sponsor")."</a></li>\n";
 echo "<li><a href=\"user_new.php?type=sponsor\">".i18n("I would like to become a sponsor")."</a></li>\n";
 echo "</ul>\n";

 echo "<h4>".i18n("Teacher/School")."</h4>\n";
 echo "<ul style=\"margin-top: 0px;\">";
 echo "<li><a href=\"schoolaccess.php\">".i18n("I am a teacher or science fair coordinator at a school")."</a></li>\n";
 echo "</ul>\n";

 if($config['volunteer_enable'] == 'yes') {
	 echo "<h4>".i18n("Volunteer")."</h4>\n";
	 echo "<ul style=\"margin-top: 0px;\">";
	 echo "<li><a href=\"user_login.php?type=volunteer\">".i18n("I am a volunteer and I already have an account")."</a></li>\n";
	 echo "<li><a href=\"user_new.php?type=volunteer\">".i18n("I am a new volunteer and would like to help out")."</a></li>\n";
	 echo "</ul>\n";
 }

 echo "<h4>".i18n("Committee")."</h4>\n";
 echo "<ul style=\"margin-top: 0px;\">";
 echo "<li><a href=\"user_login.php?type=committee\">".i18n("I am a committee member")."</a></li>\n";
 echo "</ul>\n";

if($config['fairs_enable'] == 'yes' && $config['fairs_allow_login'] == 'yes') 
{
	echo "<h4>".i18n("Science Fairs")."</h4>\n";
	echo "<ul style=\"margin-top: 0px;\">";
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_login.php?type=fair\">".i18n("{$config['fairs_name']} Fair Login").'</a></li>';
	echo "</ul>\n";
}
	echo "<br>";
	echo "<b>If you have lost or forgotten your password, or you didn't receive</b><br>";
	echo "<b>a registration email, and you have logged in before then</b><br>";
	echo "<b>your email is in the database so click on the correct category </b><br>";
	echo "<b>(Participant, Judge, ...) link above, enter your email </b><br> ";
	echo "<b>and click on the request for password.</b>  ";
	echo "<br>";
	echo "<a href=\"http://seab-sciencefair.com/mediawiki/index.php/WARNING_TO_MICROSOFT_OUTLOOK_AND_HOTMAIL_USERS\">".i18n(" MICROSOFT OUTLOOK AND HOTMAIL USERS: PLEASE READ").'</a>';

send_footer();
?>

