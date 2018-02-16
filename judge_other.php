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
 require_once("questions.inc.php");

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
	if(!is_array($_POST['languages'])) $_POST['languages']=array();

	$u['languages'] = array();
 	foreach($_POST['languages'] AS $val)
		$u['languages'][] = $val;

	$u['special_award_only'] = ($_POST['special_award_only'] == 'yes') ? 'yes' : 'no';
	$u['willing_chair'] = ($_POST['willing_chair'] == 'yes') ? 'yes' : 'no';
	$u['years_school'] = intval($_POST['years_school']);
	$u['years_regional'] = intval($_POST['years_regional']);
	$u['years_national'] = intval($_POST['years_national']);
	$u['highest_psd'] = stripslashes($_POST['highest_psd']);

	user_save($u);
    questions_save_answers("judgereg",$u['id'],$_POST['questions']);
	happy_("Preferences successfully saved");

	$u=user_load($eid);
	$newstatus=judge_status_other($u);
        echo "<script type=\"text/javascript\">";
        echo "other_update_status('$newstatus');\n";
        echo "</script>\n";
	exit;
}

 if($_SESSION['embed'] == true) {
 	echo "<br />";
	display_messages();
	echo "<h3>".i18n('Other Information')."</h3>";
 	echo "<br />";
 } else {
	//send the header
	send_header('Other Information', 
 		array('Judge Registration' => 'judge_main.php')
		);
 }

$newstatus=judge_status_other($u);
?>
<script type="text/javascript">
function judgeother_save()
{
	$("#debug").load("<?=$config['SFIABDIRECTORY']?>/judge_other.php?action=save", $("#judgeother_form").serializeArray());
        return false;
}

function other_update_status(s) {
        if(s!='complete') {
                $("#other_info_status").html('<?=error(i18n("Other Information Incomplete"))?>');
        }
        else
                $("#other_info_status").html('<?=happy(i18n("Other Information Complete"))?>');
}

//when we're ready, output the status
$(document).ready( function() { other_update_status('<?=$newstatus?>');});

</script>
<?
judge_status_update($u);
echo "<div id=\"other_info_status\"></div>\n";
?>
<form name="otherform" id="judgeother_form">
<input type="hidden" name="users_id" value="<?=$u['id']?>">
<table class="editor">
<tr>
	<td style="width:35%"><?=i18n("I can judge in the following languages")." ".REQUIREDFIELD?>: </td>
	<td>
<?
$q=mysql_query("SELECT * FROM languages WHERE active='Y' ORDER BY langname");
echo mysql_error();
while($r=mysql_fetch_object($q))
{
	$ch = (in_array($r->lang,$u['languages'])) ? 'checked="checked"' : '';
	echo "<input onclick=\"fieldChanged()\" $ch type=\"checkbox\" name=\"languages[]\" value=\"$r->lang\" /> $r->langname <br />";
}
?>

</td></tr>

<?
if($config['judges_specialaward_only_enable'] == 'yes') {
?>
	<tr><td colspan="2"><hr /></td></tr>
	<tr><td><?=i18n("I am a judge for a specific special award")?>:</td>
	<td><table><tr><td>
	<?
	$ch = ($u['special_award_only'] == 'yes') ? 'checked="checked"' : '';
	echo "<input $ch type=\"checkbox\" name=\"special_award_only\" value=\"yes\" />";
	echo "</td><td>";
	echo i18n("Check this box if you are supposed to judge a specific special award, and please select that award on the Special Award Preferences page.");
	?>
	</td></tr></table>
	</td></tr>
<?
}
?>

<tr><td colspan="2"><hr /></td></tr>

<tr>	<td><?=i18n("Years of judging experience at a School level:")?></td>
 	<td><input onchange="fieldChanged()" type="text" name="years_school" size="5" value="<?=$u['years_school']?>" /></td>
</tr><tr>
	<td><?=i18n("Years of judging experience at a Regional level:")?></td>
	<td><input onchange="fieldChanged()" type="text" name="years_regional" size="5" value="<?=$u['years_regional']?>" /></td>
</tr><tr>
	<td><?=i18n("Years of judging experience at a National level:")?></td>
	<td><input onchange="fieldChanged()" type="text" name="years_national" size="5" value="<?=$u['years_national']?>" /></td>
</tr><tr>
	<td><?=i18n("I am willing to be the lead for my judging team")?></td>
	<td>
	<? $ch = ($u['willing_chair'] == 'yes') ? 'checked="checked"' : ''; ?>
 	<input <?=$ch?> type="checkbox" name="willing_chair" value="yes" />
</tr><tr>
	<td><?=i18n("Highest post-secondary degree")?></td>
	<td><input onchange="fieldChanged()" type="text" name="highest_psd" size="35" value="<?=$u['highest_psd']?>" /></td>
</tr><tr>
	<td colspan="2"><hr /></td></tr>
</table>
<table class="editor">
<td style="width:35%" colspan="2"></td><td colspan="2"></td>
<?
questions_print_answer_editor('judgereg', $u, 'questions');
?>
</table>

<br /><br />

<input type="submit" onclick="judgeother_save(); return false;" value="<?=i18n("Save Information")?>" />
</form>

<?
 if($_SESSION['embed'] != true) send_footer();
?>
