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
 require_once('judge.inc.php');

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

switch($_GET['action']) {
case 'save':
 	//first delete all their old associations for this year..
	mysql_query("DELETE FROM judges_specialaward_sel WHERE users_id='{$u['id']}'");
	
	if(array_key_exists('spaward', $_POST)) {
	 	foreach($_POST['spaward'] AS $aid) {
			mysql_query("INSERT INTO judges_specialaward_sel (users_id, award_awards_id) 
					VALUES ('{$u['id']}','$aid')");
		}
	}
	happy_("Special Award preferences successfully saved");
	exit;
 }

 if($_SESSION['embed'] == true) {
	display_messages();
	echo "<br /><h3>".i18n('Special Awards')."</h3>";
 	echo "<br />";
 } else {
	//send the header
	send_header('Special Awards', 
 		array('Judge Registration' => 'judge_main.php')
		);
 }
?>
<script type="text/javascript">
function judgespecialawards_save()
{
	$("#debug").load("<?=$config['SFIABDIRECTORY']?>/judge_special_awards.php?action=save", $("#judgespecialawards_form").serializeArray());
        return false;
}
</script>
<?

 judge_status_update($u);

if($_SESSION['embed'] != true) {
	//output the current status
	$newstatus=judge_status_special_awards($u);
	if($newstatus!='complete')
		echo error(i18n("Special Award Preferences Incomplete"));
	else
		echo happy(i18n("Special Award Preferences Complete"));
}

?>

<form id="judgespecialawards_form">
<input type="hidden" name="users_id" value="<?=$u['id']?>" />

<?
 if($u['special_award_only'] == 'yes') {
 	 echo i18n("Please select the special award you are supposed to judge.");
 } else {
 	 echo i18n("Please select any special awards you would prefer to judge.");
	 echo "&nbsp;&nbsp;";
 	 echo i18n("We assign judges to divisional awards first.  So please note that by selecting awards here it does not guarantee that you will be judging special awards.  This selects your special award judging preferences IF you are not assigned to a divisional judging team.");
 }
 echo "<br />";
 echo "<br />";

 $q=mysql_query("SELECT * FROM judges_specialaward_sel WHERE users_id='{$u['id']}'");
 $spawards = array();
 while($r=mysql_fetch_object($q)) $spawards[] = $r->award_awards_id;

 echo "<table>\n";


 //query all of the awards
 $q=mysql_query("SELECT award_awards.id,
			award_awards.name,
                        award_awards.criteria,
			sponsors.organization
		FROM 
			award_awards, 
			award_types,
			sponsors
		WHERE 
			award_types.id=award_awards.award_types_id		
			AND sponsors.id=award_awards.sponsors_id		
			AND (award_types.type='Special' OR award_types.type='Other')
			AND award_awards.year='{$config['FAIRYEAR']}' 
			AND award_types.year='{$config['FAIRYEAR']}' 
		ORDER BY 
			name");
 echo mysql_error();
 while($r=mysql_fetch_object($q))
 {
 	?>
 	<tr><td rowspan=\"2\">
		<? $ch = (in_array($r->id,$spawards)) ? "checked=\"checked\"" : ""; ?>
		<input onclick="checkboxclicked(this)" <?=$ch?> type="checkbox" name="spaward[]" value="<?=$r->id?>" />
		</td><td>
		<b><?=$r->name?></b> (<?=$r->organization?>)</td>
	</tr><tr>
		<td><?=$r->criteria?>
		<br /><br /></td>
	</tr>
	<?
}

?>
</table>
<input type="submit" onclick="judgespecialawards_save();return false;" value="<?=i18n("Save Special Award Preferences")?>" />
</form>

<?
 if($_SESSION['embed'] != true) send_footer();
?>
