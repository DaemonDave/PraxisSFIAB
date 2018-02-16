<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2007 James Grant <james@lightbox.org>

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
 require("common.inc.php");
 send_header("Contact Us",null,"communication");

 function cleanify($in) {
 	$in=ereg_replace("\r","\n",$in);
 	$lines=split("\n",$in);
	return trim($lines[0]);
 }

 if($_POST['action']=="send") {
 	if($_POST['to'] && $_POST['subject'] && $_POST['message'] && $_POST['from'] && $_POST['fromemail']) {
		if(isEmailAddress($_POST['fromemail'])) {
			list($id,$md5email)=split(":",$_POST['to']);
			$q=mysql_query("SELECT * FROM users WHERE uid='$id' ORDER BY year DESC LIMIT 1");
			$r=mysql_fetch_object($q);
			//if a valid selection is made from the list, then this will always match.
			if($md5email == md5($r->email)) {
				$from=cleanify($_POST['from'])." <".cleanify($_POST['fromemail']).">";
				$extra="Return-Path: $from\r\nFrom: $from\r\nReply-To: $from\r\n"; 
				
				//make sure they dont do anything funky with the subject header
				$subject=cleanify($_POST['subject']);

				//and strip the slashes from the message
				$message=stripslashes($_POST['message']);

				mail("$r->firstname $r->lastname <$r->email>",$subject,$message,$extra);
				echo happy(i18n("Contact email successfully sent"));
			}
			else {
				//this should never happen unless a spammer us auto-submitting stuff and it doesnt match.
				echo error(i18n("Invalid email address"));
			}
		}	
		else
			echo error(i18n("Please enter a valid email address"));
	}
	else
		echo error(i18n("All fields are required"));
 }

?>
<script type="text/javascript">
function tochange() {
	if(!document.forms.contactform.to.options[document.forms.contactform.to.selectedIndex].value)
		document.forms.contactform.to.selectedIndex=0;
}
</script>
<?

	echo i18n("Choose who you would like to contact from the list below, type your subject and message, and click the 'Send' button");
	echo "<br />";
	echo "<br />";
	echo "<form name=\"contactform\" method=\"post\" action=\"contact.php\">\n";
	echo "<input type=\"hidden\" name=\"action\" value=\"send\">\n";
	echo "<table class=\"tableedit\">";
	echo "<tr><td>".i18n("To").":</td>";
	echo "<td><select name=\"to\" onchange=\"tochange()\">";
	echo "<option value=\"\">".i18n("Choose a person to contact")."</option>\n";
	$q=mysql_query("SELECT * FROM committees ORDER BY ord,name");
	while($r=mysql_fetch_object($q)) {

		/* Select everyone in this committee, attach the user data using MAX(year) so we only get the most recent
		 * user data */
		$q2=mysql_query("SELECT committees_link.*,users.uid,MAX(users.year),users.firstname,users.lastname,users.email,users.deleted
							FROM committees_link LEFT JOIN users ON users.uid = committees_link.users_uid 
							 WHERE committees_id='{$r->id}' 
							 GROUP BY users.uid ORDER BY ord,users.lastname ");

		//if there's nobody in this committee, then just skip it and go on to the next one.
		if(mysql_num_rows($q2)==0)
			continue;

		echo "<option value=\"\">{$r->name}</option>\n";

		echo mysql_error();
		while($r2=mysql_fetch_object($q2))
		{
			if($r2->deleted != 'no') continue;

			if($r2->email) {
				$name=$r2->firstname.' '.$r2->lastname;
				if($r2->title) $titlestr=" ($r2->title)"; else $titlestr="";
				echo "<option value=\"$r2->uid:".md5($r2->email)."\">&nbsp;&nbsp;-{$name}{$titlestr}</option>\n";
			}
		}
	}
	echo "</select></td></tr>";
	echo "<tr><td>".i18n("Your Name").":</td><td><input type=\"text\" name=\"from\" size=\"50\"></td></tr>";
	echo "<tr><td>".i18n("Your Email Address").":</td><td><input type=\"text\" name=\"fromemail\" size=\"50\"></td></tr>";
	echo "<tr><td>".i18n("Subject").":</td><td><input type=\"text\" name=\"subject\" size=\"50\"></td></tr>";
	echo "<tr><td>".i18n("Message").":</td><td><textarea cols=\"50\" rows=\"6\" name=\"message\"></textarea></td></tr>";
	echo "<tr><td></td><td align=\"center\"><input type=\"submit\" value=\"".i18n("Send")."\"></td></tr>";
	echo "</table>";
	echo "</form>";

	send_footer();
?>
