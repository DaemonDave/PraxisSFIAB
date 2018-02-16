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

 if(!isset($_SESSION['users_type'])) {
 	/* No type set, invalid session */
	echo "ERROR: session is invalid";
	exit;
 }

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
 $u = user_load($eid);

 /* Validate the type */
 if($_GET['action'] != '') {
	 $action_type = $_GET['type'];
	 if(!in_array($action_type, $user_types)) {
 		echo "ERROR: not an allowed type.";
		exit;
	 }
	 $action_what = $user_what[$action_type]; 
 }

switch($_GET['action']) {
case 'delete':
 	//okay here we go, lets get rid of them completely, since this is what theyve asked for
	message_push(happy(i18n("Account successfully deleted. Goodbye")));
	user_delete($u);
	if($_SESSION['embed'] == true)
		display_messages();
	else 
		header('location: user_login.php?action=logout');
	exit;

case 'remove':
 	/* Like delete, only we're only deleting a role, not the whole account */
	happy_("$action_what role successfully removed.");
	echo error(i18n("Permanently Removed"));
	user_delete($u, $action_type);
	exit;

 case 'activate':
	$u["{$action_type}_active"] = 'yes';
	user_save($u);
	happy_("$action_what role for %1 successfully activated",array($config['FAIRYEAR']));
	echo happy(i18n("Active"));
	exit;

 case 'deactivate':
	$u["{$action_type}_active"] = 'no';
	user_save($u);
	happy_("$action_what role for %1 successfully deactivated",array($config['FAIRYEAR']));
	echo error(i18n("Deactivated"));
	exit;
 }

 $u = user_load($u['id']);

 if($_SESSION['embed'] == true) {
 	echo "<br/>";
 	display_messages();
 	echo "<h3>".i18n("Role and Account Management")."</h3>";
	echo "<br/>";
 } else {
	 $type = $_SESSION['users_type'];
	 $m = $user_what[$type];
	 send_header("Role and Account Management", 
 				array("$m Main" => "{$type}_main.php")
			);
 }

?>
<script type="text/javascript">
function activate(type)
{
	$("#status_"+type).load("<?=$config['SFIABDIRECTORY']?>/user_activate.php?action=activate&type="+type,$('#activate_form').serializeArray());
	$("#activate_"+type).attr('disabled', 'disabled');
	$("#deactivate_"+type).removeAttr('disabled');
	$("#remove_"+type).removeAttr('disabled');
}
function deactivate(type)
{
	$("#status_"+type).load("<?=$config['SFIABDIRECTORY']?>/user_activate.php?action=deactivate&type="+type,$('#activate_form').serializeArray());
	$("#activate_"+type).removeAttr('disabled');
	$("#deactivate_"+type).attr('disabled', 'disabled');
	$("#remove_"+type).attr('disabled', 'disabled');
}
function remove(type)
{
	var con = confirmClick("<?=i18n("Are you sure you want to remove this role from your account?\\nThis action cannot be undone.")?>");
	if(con == true) {
		$("#status_"+type).load("<?=$config['SFIABDIRECTORY']?>/user_activate.php?action=remove&type="+type,$('#activate_form').serializeArray());
		$("#activate_"+type).attr('disabled', 'disabled');
		$("#deactivate_"+type).attr('disabled', 'disabled');
		$("#remove_"+type).attr('disabled', 'disabled');
	}
}
</script>

<form id="activate_form">
<input type="hidden" name="users_id" value="<?=$u['id']?>" />

<?
 foreach($u['types'] as $t) {
 	echo '<h3>'.i18n("Role: {$user_what[$t]}").'</h3>';
	echo "<div id=\"status_$t\">";
	if($u["{$t}_active"] == 'yes') {
		echo happy(i18n('Active'));
		$a = 'disabled="disabled"';
		$d = '';
	} else {
		echo notice(i18n('Deactivated'));
		$a = '';
		$d = 'disabled="disabled"';
	}
?>
	</div>

	<table><tr><td>
	<input style="width: 200px;" id="activate_<?=$t?>" <?=$a?> onclick="activate('<?=$t?>');return false;" type="submit" value="<?=i18n("Activate Role")?>">
	</td><td>
	<input style="width: 200px;" id="deactivate_<?=$t?>"<?=$d?> onclick="deactivate('<?=$t?>');return false;" type="submit" value="<?=i18n("Deactivate Role")?>">
	</td><td>
	<input style="width: 200px;" id="remove_<?=$t?>"<?=$d?> onclick="remove('<?=$t?>');return false;" type="submit" value="<?=i18n("Remove Role")?>">

	</td></tr></table>
	<br />
	<hr />
<?
 }
 echo "</form>";

 echo '<ul>';
 echo '<li>'.i18n("An <b>Active Role</b> indicates you would like to participate in the %1 %2 as that role (Judge, Volunteer, etc.)",array($config['FAIRYEAR'],$config['fairname']));
 echo '</li><li>'.i18n("A <b>Deactivated Role</b> indicates you cannot participate in the deactivated roles this year, but would like remain on the mailing lists for future years.  You can activate your deactivated role at any time.");
 echo '</li><li>'.i18n("The <b>Remove Role</b> button completely deletes the role from your account.  You will not receive future emails for the removed role.  This action cannot be undone.");
 echo '</li><li>'.i18n("The <b>Delete Entire Account</b> button below completely deletes your entire account.  You will not receive any future email for any roles.  It completely removes you from the system.  This action cannot be undone.");
 echo '</ul>';

 echo "<form method=\"post\" action=\"{$config['SFIABDIRECTORY']}/user_activate.php?action=delete\">";
 echo "<input type=\"hidden\" name=\"users_id\" value=\"{$u['id']}\" />";
 echo "<input style=\"width: 300px;\" onclick=\"return confirmClick('".i18n("Are you sure you want to completely delete your account?\\nDoing so will remove you from our mailing list for future years and you will never hear from us again.\\nThis action cannot be undone.")."')\" type=\"submit\" value=\"".i18n("Delete Entire Account")."\">";
 echo "</form>";

 if($_SESSION['embed'] != true) send_footer();
?>
