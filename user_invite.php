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
 require_once('common.inc.php');
 require_once('user.inc.php');

 user_auth_required('committee', 'admin');
 //include "judges.inc.php";

 /* AJAX query */
 if(intval($_GET['ajax']) == 1) 
 {
 	/* Do ajax processing for this file */
	$email = mysql_escape_string(stripslashes($_GET['email']));
	$type = $_GET['type'];

	/* Sanity check type */
	if(!in_array($type, $user_types)) 
	{
		echo "err\n";
		exit;
	}

	$q = mysql_query("SELECT id FROM users WHERE email='$email' ORDER BY year DESC");
	if(mysql_num_rows($q) == 0) {
		/* User doesn't exist */
		echo "notexist\n";
		exit;
	}
	$u = mysql_fetch_assoc($q);
	$u = user_load($u['id']);

	if($u['deleted'] == 'yes') 
	{
		echo "notexist\n";
		exit;
	}

	if(!in_array($type, $u['types'])) 
	{
		echo "norole\n";
		exit;
	}

	if($u['year'] != $config['FAIRYEAR']) 
	{
		echo "noyear\n";
		exit;
	}

	echo "exist\n"; 
 	exit;
 }// end ajax

 send_header("Invite Users",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'
			) );

 require_once('ajax.inc.php');

?>
	<script type="text/javascript">
	function check_email()
	{
		var url, email, type;

		type = document.invite.type.value;
		email = document.invite.email.value;

		if(email.length < 3 || type == "") {
			update_status("<?=i18n('Select a role and enter an email address')?>");
			document.invite.button.disabled = true;
			document.invite.button.value = "<?=i18n('Invite')?>";
			return true;
		}
		
		url="user_invite.php?ajax=1&email="+email+"&type="+type;
//		alert(url);
		http.open("GET",url,true);
		http.onreadystatechange=ajax_response;
		http.send(null);
		return true;
	}

	function update_status(text)
	{
		div = document.getElementById('status');
		div.innerHTML = text;
	}


	function ajax_response()
	{
		try {
			if(http.readyState == 4) {
				var lines=http.responseText.split('\n');
				var response=lines[0];
				switch(response) {
				case "err":
					update_status("<?=i18n('Select a role and enter an email address')?>");
					document.invite.button.disabled = true;
					document.invite.button.value = "<?=i18n('Invite')?>";
					document.invite.action.value = "err";
					break;
				case "notexist":
					update_status("<?=i18n('User not found.  Choose the \"Invite New User\" button below to create an account for this user and send them an email invite.')?>");
					document.invite.button.disabled = false;
					document.invite.button.value = "<?=i18n('Invite New User')?>";
					document.invite.action.value = "notexist";
					break;
				case "norole":
					update_status("<?=i18n('User found without the selected role.  Choose the \"Invite User to Role\" button below add the selected role on this user\'s account and send them email notice of the change.')?>");
					document.invite.button.disabled = false;
					document.invite.button.value = "<?=i18n('Invite User to Role')?>";
					document.invite.action.value = "norole";
					break;
				case "noyear":
					update_status("<?=i18n('This user and role already exist, but the user has not yet activated their account for this year.  Choose the \"Send Activation Reminder\" button below to send this user an email reminder to login (which activates their account for this year).')?>");
					document.invite.button.disabled = false;
					document.invite.button.value = "<?=i18n('Send Activation Reminder')?>";
					document.invite.action.value = "noyear";
					break;
				case "exist":
					update_status("<?=i18n('This user and role already exist.  They cannot be invited.')?>");
					document.invite.button.disabled = true;
					document.invite.button.value = "<?=i18n('Invite')?>";
					document.invite.action.value = "err";
					break;
				}
			} else {
//				update_status("<?=i18n('Searching...')?>");
			}
		} catch(e) {
			alert('caught error: '+e);
		}
	}
	</script>
<?

 echo "<br />";
//! DRE 2018 added sponsor
 $allowed_types = array('judge', 'volunteer', 'sponsor', 'mentor');
 $type = $_POST['type'];
 if($type == '') $type = $_GET['type'];
 if($type != '') 
 {
	 if(!in_array($type, $allowed_types))
	 {
			echo "Type $type not allowed for invite<br /><br/>";
			exit;
	 }
 }

 if($_POST['action']!="" && $_POST['email'] && $type != '') 
 {
 	$allowed_actions = array('notexist','norole','noyear');
 	$email = stripslashes($_POST['email']);

	$action = $_POST['action'];
	if(!in_array($action, $allowed_actions)) 
		exit;

	$q = mysql_query("SELECT id FROM users WHERE email='$email' ORDER BY year DESC");
	if(mysql_num_rows($q) > 0) 
	{
		$u = mysql_fetch_assoc($q);
		$u = user_load($u['id']);
	}
	else
	{
		$u = NULL;
	}
//
/// MODIFIED DRE 2018
//
	// concatenate website specifics
	$urlproto = $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
	$urlmain = "$urlproto{$_SERVER['HTTP_HOST']}{$config['SFIABDIRECTORY']}";
	$urllogin = "$urlmain/login.php";		
	switch($action) 
	{
	case 'notexist': /* Create the user */
		$u = user_create($type, $email);
		$u['email'] = $email;
		user_save($u);
		// included website in emails
		email_send("{$type}_new_invite",$u['email'],
					array("FAIRNAME"=>$config['fairname']),
					array("FAIRNAME"=>$config['fairname'],
						"EMAIL"=>$u['email'],
						"PASSWORD"=>$u['password'],
						"WEBSITE"=>$urllogin ));
		echo happy(i18n('%1 has been invited to be a %2', array($u['email'], $user_what[$type])));
		echo happy(i18n('An email has been sent to %1', array($u['email'])));
		break;

	case 'norole': /* Add role to the existing user */
		user_create($type, $u['username'], $u);
		// included website in emails		
		email_send("{$type}_add_invite",$u['email'],
					array("FAIRNAME"=>$config['fairname']),
					array("FAIRNAME"=>$config['fairname'],
						"WEBSITE"=>$urllogin ));					
		echo happy(i18n('%1 is now also a %2', array($u['email'], $user_what[$type])));
		echo happy(i18n('An email has been sent to %1', array($u['email'])));
		break;
	case 'noyear': /* Send a reminder email */
		// included website in emails	
		email_send("{$type}_activate_reminder",$u['email'],
					array("FAIRNAME"=>$config['fairname']),
					array("FAIRNAME"=>$config['fairname'],
						"EMAIL"=>$u['email'],
						"WEBSITE"=>$urllogin ));
		echo happy(i18n('An email has been sent to %1', array($u['email'])));
		break;
	}
 }


 echo "<br />\n";
 echo "<form method=\"post\" name=\"invite\" action=\"user_invite.php\">\n";
 echo "<input type=\"hidden\" name=\"action\" value=\"invite\" />\n";
 echo "<table><tr><td>";
 echo i18n("Select a Role: ");
 echo "</td><td><select name=\"type\" onChange=\"check_email();\">\n";
 echo "<option value=\"\" >".i18n('Choose')."</option>\n";
 $sel = ($type == 'judge') ? 'selected="selected"' : '';
 echo "<option value=\"judge\" $sel >".i18n('Judge')."</option>\n";
 $sel = ($type == 'volunteer') ? 'selected="selected"' : '';
 echo "<option value=\"volunteer\" $sel >".i18n('Volunteer')."</option>\n";
 $sel = ($type == 'sponsor') ? 'selected="selected"' : '';
 echo "<option value=\"sponsor\" $sel >".i18n('Sponsor')."</option>\n";
 $sel = ($type == 'mentor') ? 'selected="selected"' : '';
 echo "<option value=\"mentor\" $sel >".i18n('Mentor')."</option>\n";
 echo "</select></td></tr><tr><td>";
 echo i18n("Enter an Email: ");
 echo "</td><td><input type=\"text\" name=\"email\" size=\"40\" onKeyUp=\"check_email();\" />";
 echo "</td></tr></table>";
 echo "<br />\n";
 echo "<div class=\"notice\" id=\"status\">".i18n('Select a role and enter an email address')."</div>";

 echo "<br />\n";
 echo "<input name=\"button\" type=\"submit\" disabled=\"disabled\" value=\"".i18n("Invite")."\" />\n";

 echo "</form>\n";


 send_footer();
?>
