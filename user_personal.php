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
 require_once("committee.inc.php");

 if(!isset($_SESSION['users_type'])) {
 	/* No type set, invalid session */
	echo "ERROR: session is invalid";
	exit;
 }

 $user_personal_fields = array(
		'salutation' => array('name' => 'Salutation'),
		'firstname' => array('name' => 'First Name'),
		'lastname' => array('name' => 'Last Name'),
		'email' => array('name' => 'Email Address'),
		'username' => array('name' => 'Username'),
		'password' => array('name' => 'Password'),
		'address' => array('name' => 'Address 1'),
		'address2' => array('name' => 'Address 2'),
		'city' => array('name' => 'City'),
		'lang' => array('name' => 'Preferred Language'),
		'province' => array('name' => $config['provincestate']),
		'organization' => array('name' => 'Organization'),
		'sex' => array('name' => 'Gender'),
		'firstaid' => array ('name' => 'First Aid Training',
				'type' => 'yesno'),
		'cpr' => array ('name' => 'CPR Training',
				'type' => 'yesno'),
		'phonehome' => array('name' => 'Phone (Home)',
				'regexp' => '^[1-9][0-9]{2}-[1-9][0-9]{2}-[0-9]{4}( x[0-9]{1,5})?$',
				'format' => '\'NNN-NNN-NNNN\' or \'NNN-NNN-NNNN xEXT\'',),
		'phonecell' => array('name' => 'Phone (Cell)',
				'regexp' => '^[1-9][0-9]{2}-[1-9][0-9]{2}-[0-9]{4}$',
				'format' => '\'NNN-NNN-NNNN\'',),
		'phonework' => array('name' => 'Phone (Work)',
				'regexp' => '^[1-9][0-9]{2}-[1-9][0-9]{2}-[0-9]{4}( x[0-9]{1,5})?$',
				'format' => '\'NNN-NNN-NNNN\' or \'NNN-NNN-NNNN xEXT\'',),
		'fax' => array('name' => 'Fax',
				'regexp' => '^[1-9][0-9]{2}-[1-9][0-9]{2}-[0-9]{4}$',
				'format' => '\'NNN-NNN-NNNN\'',),
		'postalcode' => array('name' => $config['postalzip'],
				'regexp' => '^(([A-Za-z][0-9][A-Za-z]( )?[0-9][A-Za-z][0-9])|([0-9]{5}))$',
				'format' => '\'ANA NAN\' or \'ANANAN\' or \'NNNNN\'',),

);

/* Sort out who we're editting */
if($_POST['users_id']) 
	$eid = intval($_POST['users_id']); /* From a save form */
else if(array_key_exists('embed_edit_id', $_SESSION))
	$eid = $_SESSION['embed_edit_id']; /* From the embedded editor */
else 
	$eid = $_SESSION['users_id'];	/* Regular entry */

if($eid != $_SESSION['users_id']) {
	/* Not editing ourself, we had better be
	 * a committee member */
	user_auth_required('committee','admin');
}
 $type = $_SESSION['users_type'];

 $u = user_load($eid);
 /* Load the fields the user can edit, and theones that are required */
 $fields = array();
 $required = array();
 $errorfields = array();
 foreach($u['types'] as $t) {
	$fields = array_merge($fields, 
			user_personal_fields($t));
	$required = array_merge($required, 
			user_personal_required_fields($t));
 }

 if(committee_auth_has_access('super')) {
	/* If the editer is super, let them see/edit/save the user/pass */
 	$fields[] = 'username';
 	$fields[] = 'password';
 }

switch($_GET['action']) {
case 'save':
	$users_id = intval($_POST['users_id']);
	if($users_id != $_SESSION['users_id']) {
		user_auth_required('committee','admin');
	}
	$u = user_load($users_id);

 	$save = true;
 	/* Set values */
	foreach($fields as $f) {
		$u[$f] = iconv("UTF-8","ISO-8859-1",stripslashes($_POST[$f]));
		/* Allow the user to clear a field regardless of regex */
		if($u[$f] == '') continue;

		/* See if this field has a validate */
		if(isset($user_personal_fields[$f]['regexp'])) {
			/* Match the regex */
			if(!ereg($user_personal_fields[$f]['regexp'], $u[$f])) {
				/* Bad */
				error_("Invalid format for $f expecting ({$user_personal_fields[$f]['format']})");
				$save = false;
				$errorfields[] = $f;
			}
		}
	}

	if(!in_array('username', $fields) || !array_key_exists('username', $u) || $u['username'] == '') {
		$u['username'] = $u['email'];
	}

	if(in_array('committee', $u['types'])) {
		/* Trying to save a committee member eh? Well, we established above
		 * that we're allowed to be here, so go ahead and save it */
		$u['displayemail'] = ($_POST['displayemail'] == 'yes') ? 'yes' : 'no';
		$u['emailprivate'] = mysql_real_escape_string(stripslashes($_POST['emailprivate']));

		if(committee_auth_has_access('super')) {
			/* But only superusers can save these ones */
			$u['access_admin'] = ($_POST['access_admin'] == 'yes') ? 'yes' : 'no';
			$u['access_config'] = ($_POST['access_config'] == 'yes') ? 'yes' : 'no';
			$u['access_super'] = ($_POST['access_super'] == 'yes') ? 'yes' : 'no';
		}
	}


	/* Check for an email collision */
	$em = mysql_escape_string(stripslashes($_POST['email']));
	$q=mysql_query("SELECT *,max(year) FROM users WHERE email='$em' HAVING uid!='{$u['uid']}' AND deleted='no' ");
	if(mysql_num_rows($q) > 0) {
		error_("That email address is in use by another user");
		echo "email error";
		$save = false;
	}

	if($save == true) {
		user_save($u);
		happy_("%1 %2 successfully updated",array($u['firstname'],$u['lastname']));
	} 

	//reload the user record because we dont know if we saved or didnt save above, we just want
	//to know what the user looks like _now_
	$u = user_load($users_id);
	$newstatus=user_personal_info_status($u);
	echo "<script type=\"text/javascript\">";
	echo "personal_update_status('$newstatus');\n";
	echo "</script>\n";
	exit;
 }



 //send the header
 if($_SESSION['embed'] == true) {
 	echo "<br/>";
	display_messages();
 	echo "<h3>".i18n("Personal Information")."</h3>";
 	echo "<br/>";
 } else {
	 send_header("Personal Information for {$u['firstname']} {$u['lastname']}", 
 			array($user_what[$type]." Registration" => "{$type}_main.php")
			,"edit_profile"
			);
 } 

$newstatus=user_personal_info_status($u);
?>
<script type="text/javascript">
function personal_save()
{
	$("#debug").load("<?=$config['SFIABDIRECTORY']?>/user_personal.php?action=save", $("#personal_form").serializeArray());
        return false;
}

function personal_update_status(s) {
	if(s!='complete') {
		$("#personal_info_status").html('<?=error(i18n("Personal Information Incomplete"))?>');
	}
	else 
		$("#personal_info_status").html('<?=happy(i18n("Personal Information Complete"))?>');
}

//when we're ready, output the status
$(document).ready( function() { personal_update_status('<?=$newstatus?>');});

</script>
<?
echo "<div id=\"personal_info_status\"></div>";

if(count($u['types']) > 1) {
	$roles='';
	foreach($u['types'] as $t) {
		$roles.= (($roles=='')?'':', ').i18n($user_what[$t]);
	}
	echo notice(i18n('This user has multiple roles, the fields shown below are a combination of every role.  Some may not apply to some roles.  This user has the following roles:').' '.$roles);
}

function item($user, $fname, $subtext='') 
{
	global $fields, $required;
	global $errorfields;
	global $user_personal_fields;

	if(in_array($fname, $fields)) {
		$text = i18n($user_personal_fields[$fname]['name']);
		if(in_array($fname, $errorfields)) $style = 'style="color:red;"';
		echo "<td><span $style>$text</span>: ";
		if($subtext != '') echo '<br /><span style="font-size: 0.5em;">'.i18n($subtext).'</span>';
		echo '</td><td>';

		$req = in_array($fname, $required) ? REQUIREDFIELD : '';

		switch($user_personal_fields[$fname]['type']) {
		case 'yesno':
			echo "<select name=\"$fname\">";
			$sel = ($user[$fname]=='yes') ? 'selected="selected"' : '';
			echo "<option value=\"yes\" $sel>".i18n("Yes")."</option>\n";
			$sel = ($user[$fname]=='no') ? 'selected="selected"' : '';
			echo "<option value=\"no\" $sel>".i18n("No")."</option>\n";
			echo "</select> $req";
			break;
		default:
			echo "<input onchange=\"fieldChanged()\" type=\"text\" name=\"$fname\" value=\"{$user[$fname]}\" />$req";
			break;
		}
		echo '</td>';
	} else {
		echo '<td></td><td></td>';
	}

}

 echo "<form name=\"personalform\" id=\"personal_form\">\n";
 echo "<input type=\"hidden\" name=\"users_id\" value=\"{$u['id']}\" />";
 echo "<table>\n";

echo "<tr>\n";
item($u, 'firstname');
item($u, 'lastname');
echo "</tr>\n";
echo "<tr>\n";
item($u, 'email');
item($u, 'salutation');
echo "</tr>\n";
echo "<tr>\n";
item($u, 'username', '(if different from Email)');
item($u, 'password');
echo "</tr>\n";
echo "<tr>\n";
item($u, 'address');
item($u, 'city');
echo "</tr>\n";
echo "<tr>\n";
item($u, 'address2');
 if(in_array('province', $fields)) {
	echo '<td>'.i18n($config['provincestate']).': </td>';
	echo '<td>';
	emit_province_selector("province",$u['province'],"onchange=\"fieldChanged()\"");
	if(in_array('province', $required)) echo REQUIREDFIELD;
	echo '</td>';
 } else {
	echo '<td></td><td></td>';
 }
echo "</tr>\n";
echo "<tr>\n";
item($u, 'postalcode');
echo "<td></td><td></td>";
echo "</tr>\n";
echo "<tr>";
item($u, 'phonehome');
item($u, 'phonecell');
echo "</tr>\n";

echo "<tr>\n";
item($u, 'organization');
item($u, 'phonework');
echo "</tr>";
echo "<tr>\n";
item($u, 'fax');
 if(in_array('sex', $fields)) {
	echo '<td>'.i18n('Gender').': </td>';
	echo '<td>';
	echo "<select name=\"sex\">";
	echo "<option value=\"\">".i18n("Choose")."</option>\n";
	if($u['sex']=="male") $sel="selected=\"selected\""; else $sel="";
	echo "<option value=\"male\" $sel>".i18n("Male")."</option>\n";
	if($u['sex']=="female") $sel="selected=\"selected\""; else $sel="";
	echo "<option value=\"female\" $sel>".i18n("Female")."</option>\n";
	echo "</select>";
	if(in_array('sex', $required)) echo REQUIREDFIELD;
	echo '</td>';
 } else {
	echo '<td></td><td></td>';
 }
echo "</tr>";

echo "<tr>\n";
item($u, 'firstaid');
item($u, 'cpr');
echo "</tr>";
echo "<tr>\n";
 if(in_array('lang', $fields)) {
	echo '<td>'.i18n('Preferred Lang').': </td>';
	echo '<td>';
	echo "<select name=\"lang\">";
	echo "<option value=\"\">".i18n("Choose")."</option>\n";
	foreach($config['languages'] AS $l=>$ln) {
		if($u['lang']==$l) $sel="selected=\"selected\""; else $sel="";
		echo "<option value=\"$l\" $sel>".i18n($ln)."</option>\n";
	}
	echo "</select>";
	if(in_array('lang', $required)) echo REQUIREDFIELD;
	echo '</td>';
 } else {
	echo '<td></td><td></td>';
 }
echo "<td></td><td></td>";
echo "</tr>";


echo "<tr><td colspan=\"4\"><hr /></td></tr>";

echo "</table>";

/* Committee specific fields */
if(in_array('committee', $u['types'])) {
	echo "<table>";

	echo "<tr><td>".i18n("Email (Private)").":</td><td><input size=\"25\" type=\"text\" name=\"emailprivate\" value=\"{$u['emailprivate']}\" /></td></tr>\n";
	echo "<tr><td>".i18n("Display Emails").":</td><td>";
	if($u['displayemail']=="no") $checked="checked=\"checked\""; else $checked="";
	echo "<input type=\"radio\" name=\"displayemail\" value=\"no\" $checked />".i18n("No");
	echo "&nbsp; &nbsp; &nbsp;";
	if($u['displayemail']=="yes") $checked="checked=\"checked\""; else $checked="";
	echo "<input type=\"radio\" name=\"displayemail\" value=\"yes\" $checked />".i18n("Yes");

	if(committee_auth_has_access("super"))
	{
		/* If the user is a committee member, only print these fields
		 * if the editer has super access */
		echo "<tr><td align=\"center\" colspan=\"2\"><hr /></td></tr>";
		echo "<tr><td>".i18n("Access Controls").":</td><td>";
		$ch = ($u['access_admin']=="yes") ? 'checked="checked"' : '';
		echo "<input type=\"checkbox\" name=\"access_admin\" value=\"yes\" $ch /> ".i18n("Administration")."<br />";
		$ch = ($u['access_config']=="yes") ? 'checked="checked"' : '';
		echo "<input type=\"checkbox\" name=\"access_config\" value=\"yes\" $ch /> ".i18n("Configuration")."<br />";
		$ch = ($u['access_super']=="yes") ? 'checked="checked"' : '';
		echo "<input type=\"checkbox\" name=\"access_super\" value=\"yes\" $ch /> ".i18n("Superuser")."<br />";
		echo "</td></tr>";
	}
	echo '</table>';
}



echo "<input type=\"submit\" onclick=\"personal_save();return false;\" value=\"".i18n("Save Personal Information")."\" />\n";
echo "</form>";

 echo "<br />";

if($_SESSION['embed'] != true) {
	send_footer();
}

?>
