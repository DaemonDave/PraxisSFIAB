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
 include "judges.inc.php";

 send_header("Invite Judges",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judges' => 'admin/judges.php')
			);
 echo "<br />";
 if($_POST['action']=="invite" && $_POST['email'])
 {
 	$q=mysql_query("SELECT id FROM judges WHERE email='".$_POST['email']."'");
	if(mysql_num_rows($q))
	{
		echo error(i18n("A judge already exists with that email address"));
	}
	else
	{
		$pass=generatePassword();
		mysql_query("INSERT INTO judges (email,password) VALUES ('".mysql_escape_string(stripslashes($_POST['email']))."','$pass')");
//
/// MODIFIED DRE 2018 -- NEEDS FIXING
//		
		email_send("new_judge_invite",stripslashes($_POST['email']),array("FAIRNAME"=>$config['fairname']),array("FAIRNAME"=>$config['fairname'],"EMAIL"=>stripslashes($_POST['email']),"PASSWORD"=>$pass) );

		echo happy(i18n("%1 has been invited to be a judge",array($_POST['email'])));
	}
 }


 echo i18n("Enter the judge's email address to invite them to be a judge");
 echo "<br />\n";
 echo "<br />\n";
 echo "<form method=\"post\" action=\"judges_invite.php\">\n";
 echo "<input type=\"hidden\" name=\"action\" value=\"invite\" />\n";
 echo i18n("Email").": ";
 echo "<input type=\"text\" name=\"email\" size=\"40\" />\n";
 echo "<input type=\"submit\" value=\"".i18n("Invite Judge")."\" />\n";
 echo "</form>\n";

 send_footer();
?>
