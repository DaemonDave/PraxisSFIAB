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
	if(!is_array($_POST['division']))
		$_POST['division']=array();
	if(!is_array($_POST['subdivision']))
		$_POST['subdivision']=array();

	$u['div_prefs'] = array();
 	foreach($_POST['division'] AS $key=>$val)
		$u['div_prefs'][$key] = $val;

	$u['div_prefs_sub'] = array();
 	foreach($_POST['subdivision'] AS $key=>$val)
		$u['div_prefs_sub'][$key] = $val;

	if($_POST['expertise_other'])
		$u['expertise_other'] = stripslashes($_POST['expertise_other']);
	else 
		$u['expertise_other'] = NULL;

	$u['cat_prefs'] = array();
	if(is_array($_POST['catpref']))	{
		foreach($_POST['catpref'] AS $k=>$v) {
			if($v == '') continue;

			$u['cat_prefs'][$k] = $v;
		}
	}
	user_save($u);
	happy_("Preferences successfully saved");

        //reload the user record because we dont know if we saved or didnt save above, we just want
        //to know what the user looks like _now_
        $u = user_load($eid);
        $newstatus=judge_status_expertise($u);
        echo "<script type=\"text/javascript\">";
        echo "expertise_update_status('$newstatus');\n";
        echo "</script>\n";
	exit;
}


 if($_SESSION['embed'] == true) {
	 echo "<br /><h3>".i18n("Judging Expertise")."</h3>";
	display_messages();
 } else {
	//send the header
	send_header('Category and Division Preferences', 
 		array('Judge Registration' => 'judge_main.php')
		);
 }

$newstatus=judge_status_expertise($u);
?>
<script type="text/javascript">
function judgeexpertise_save()
{
	$("#debug").load("<?=$config['SFIABDIRECTORY']?>/judge_expertise.php?action=save", $("#judgeexpertise_form").serializeArray());
        return false;
}

function expertise_update_status(s) {
        if(s!='complete') {
                $("#expertise_info_status").html('<?=error(i18n("Divisional Judging Information Incomplete"))?>');
        }
        else
                $("#expertise_info_status").html('<?=happy(i18n("Divisional Judging Information Complete"))?>');
}

//when we're ready, output the status
$(document).ready( function() { expertise_update_status('<?=$newstatus?>');});

</script>
<?

judge_status_update($u);

echo "<div id=\"expertise_info_status\"></div>\n";

 if($u['special_award_only'] == 'yes') {
 	echo i18n("You have specified that you are a judge for a specific special award.  Divisional Judging preferences have been disabled because they do not apply to you.");
	echo "<br />";
	send_footer();
	exit;
 }

echo "<form name=\"expertiseform\" id=\"judgeexpertise_form\">\n";
echo "<input type=\"hidden\" name=\"users_id\" value=\"{$u['id']}\">\n";

 $q=mysql_query("SELECT * FROM projectcategories WHERE year='{$config['FAIRYEAR']}' ORDER BY mingrade");
 echo "<br /><h4>".i18n("Age Category Preferences")."</h4><br>";
 echo "<table class=\"editor\" style=\"width: 300px;\" >";
 while($r=mysql_fetch_object($q))
 {
	echo "<tr><td class=\"label\" >";
	echo i18n("%1 (Grades %2-%3)",array(i18n($r->category),$r->mingrade,$r->maxgrade));
	echo ":</td>";
	echo "<td>";
	echo "<select name=\"catpref[$r->id]\">";
	echo "<option value=\"\">".i18n("Choose")."</option>\n";
	foreach($preferencechoices AS $val=>$str)
	{
		if($u['cat_prefs'][$r->id]==$val && $u['cat_prefs'][$r->id]!="") 
			$sel="selected=\"selected\""; 
		else 
			$sel="";
		echo "<option $sel value=\"$val\">".i18n($str)."</option>\n";
	}
	echo "</select>".REQUIREDFIELD;

	echo "</td>";
	echo "</tr>";
 }
 echo "</table>";
 echo "<br />";
 echo "<br />";
 echo "<h4>".i18n("Division Expertise")."</h4><br>";


 echo i18n("Please rank the following divisions according to the amount of knowledge you have of each subject.  A '1' indicates very little knowledge, and a '5' indicates you are very knowledgeable of the subject");
 echo "<br />";
 echo "<br />";
 echo i18n("Once you save, any division that you specified as 3 or more might offer sub-divisions for you to choose from.");
 echo "<br />";
 echo "<br />";

 echo "<table>\n";

 //query all of the categories
 $q=mysql_query("SELECT * FROM projectdivisions WHERE year='{$config['FAIRYEAR']}' ORDER BY division");
 $first = true;
 while($r=mysql_fetch_object($q)) {

	$trclass = ($trclass == 'odd') ? 'even' : 'odd';
 	if($first == true) {
	 	echo "<tr><td></td><td colspan=\"2\">".i18n("Novice")."</td><td colspan=\"3\" align=\"right\">".i18n("Expert")."</td></tr>";
		echo "<tr><th></th>";
		for($x=1;$x<=5;$x++)
			echo "<th>$x</th>";
		echo "</tr>";
		$first = false;
	}

	echo "<tr class=\"$trclass\"><td><b>".i18n($r->division)."</b></td>";

	for($x=1;$x<=5;$x++) {
		if(!$u['div_prefs'][$r->id]) $u['div_prefs'][$r->id]=1;
		$sel = ($u['div_prefs'][$r->id]==$x) ? "checked=\"checked\"" : '';
		echo "<td width=\"30\"><input onclick=\"fieldChanged()\" $sel type=\"radio\" name=\"division[$r->id]\" value=\"$x\" /></td>";
	}
//	echo "<td width=\"100\"></td>";
	echo "</tr>";

	//only show the sub-divisions if the 'main' division is scored >=3
	if($u['div_prefs'][$r->id]>=3) {

		$subq=mysql_query("SELECT * FROM projectsubdivisions WHERE projectdivisions_id='$r->id' AND year='".$config['FAIRYEAR']."' ORDER BY subdivision");
		while($subr=mysql_fetch_object($subq)) {
			echo "<tr>";
			echo "<td>&nbsp;</td>";
			$ch = ($u['div_prefs_sub'][$subr->id]) ? "checked=\"checked\"" : '';

			echo "<td><input onclick=\"fieldChanged()\" $ch type=\"checkbox\" name=\"subdivision[$subr->id]\" value=\"1\" /></td>";
			echo "<td colspan=\"5\">";
			echo "$subr->subdivision";
			echo "</td>";
			echo "</tr>";
		}
	}
 }
?>
</table>
<br />
<h4><?=i18n("Other Areas of Expertise not listed above")?></h4>
<textarea name="expertise_other" rows="4" cols="60"><?=htmlspecialchars($u['expertise_other'])?></textarea>
<br />
<br />

<input type="submit" onclick="judgeexpertise_save();return false;" value="<?=i18n("Save Judging Preferences")?>" />
</form>

<?
 if($_SESSION['embed'] != true) send_footer();

?>
