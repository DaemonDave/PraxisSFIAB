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
require_once('common.inc.php');
require_once('user.inc.php');
require_once('fair.inc.php');

 $fair_type = array('feeder' => 'Feeder Fair', 'sfiab' => 'SFIAB Upstream', 'ysc' => 'YSC/CWSF Upstream');

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
	$fairs_id = intval($u['fairs_id']);
	if($fairs_id == 0) {
		$q = mysql_query("INSERT INTO fairs(`id`,`name`) VALUES('','new entry')");
		$id = mysql_insert_id();
	} else {
		$id = intval($fairs_id);
	}

	$name = mysql_real_escape_string(stripslashes($_POST['name']));
	$abbrv = mysql_real_escape_string(stripslashes($_POST['abbrv']));
	$url = mysql_real_escape_string($_POST['url']);
	$website = mysql_real_escape_string($_POST['website']);
	$type = array_key_exists($_POST['type'], $fair_type) ? $_POST['type'] : '';
	$username = mysql_real_escape_string(stripslashes($_POST['username']));
	$password = mysql_real_escape_string(stripslashes($_POST['password']));
	$enable_stats = ($_POST['enable_stats'] == 'yes') ? 'yes' : 'no';
	$enable_awards = ($_POST['enable_awards'] == 'yes') ? 'yes' : 'no';
	$enable_winners = ($_POST['enable_winners'] == 'yes') ? 'yes' : 'no';

	$q = mysql_query("UPDATE fairs SET `name`='$name',
				`abbrv`='$abbrv', `url`='$url',
				`website`='$website',
				`type`='$type' , `username`='$username',
				`password`='$password',
				`enable_stats`='$enable_stats',
				`enable_awards`='$enable_awards',
				`enable_winners`='$enable_winners'
				WHERE id=$id");
	echo mysql_error();
	$u['fairs_id'] = $id;
	user_save($u);
	happy_("Fair Informaiton successfully updated");
	exit;
 }

 function yesno($name, $val) 
 {
	echo "<select name=\"$name\">";
	$sel = ($val == 'yes') ? 'selected="selected"' : '';
	echo "<option $sel value=\"yes\">".i18n("Yes")."</option>";
	$sel = ($val == 'no') ? 'selected="selected"' : '';
	echo "<option $sel value=\"no\">".i18n("No")."</option>";
	echo "</select>";
 }


/* update overall status */
fair_status_update($u);

if($_SESSION['embed'] != true) {
	//output the current status
	$newstatus=fair_status_info($u);
	if($newstatus!='complete')
		message_push(error(i18n("Fair Information Incomplete")));
	else
		message_push(happy(i18n("Fair Information Complete")));
}

if($_SESSION['embed'] == true) {
 	echo "<br />";
	display_messages();
	echo "<h3>".i18n('Fair Information')."</h3>";
 	echo "<br />";
} else {
	//send the header
	send_header("Fair Information", 
 		array("Science Fair Main" => "fair_main.php")
		);
}

?>
<script type="text/javascript">
function fairinfo_save()
{
	$("#debug").load("<?=$config['SFIABDIRECTORY']?>/fair_info.php?action=save", $("#fairinfo_form").serializeArray());
        return false;
}
</script>

<?
 /* Load the fair info */
 $q = mysql_query("SELECT * FROM fairs WHERE id={$u['fairs_id']}");
 if(mysql_num_rows($q)) {
	 $f = mysql_fetch_assoc($q);
 } else {
	 $f = array();
 }

 echo "<form name=\"fairinfo\" id=\"fairinfo_form\" >\n";
 echo "<input type=\"hidden\" name=\"users_id\" value=\"{$u['id']}\" />\n";
 echo "<table class=\"editor\">\n";
 echo '<tr><td class="label">'.i18n('Fair Type').':</td><td class="right">';
 echo "<select name=\"type\" id=\"type\" >";
 foreach($fair_type as $k=>$o) {
	 $s = ($f['type'] == $k) ? 'selected="selected"' : '';
	 echo "<option value=\"$k\" $s >".i18n($o)."</option>";
 }
 echo "</select></td></tr>";
 echo '<tr><td class="label">'.i18n('Fair Name').':</td><td class="right">';
 echo "<input type=\"text\" name=\"name\" value=\"{$f['name']}\" size=\"40\" />";
 echo '<tr><td class="label">'.i18n('Fair Abbreviation').':</td><td class="right">';
 echo "<input type=\"text\" name=\"abbrv\" value=\"{$f['abbrv']}\" size=\"7\" />";
 echo '<tr><td class="label">'.i18n('Fair Website').':</td><td class="right">';
 if($f['website'] == '') $f['website'] = 'http://';
 echo "<input type=\"text\" name=\"website\" value=\"{$f['website']}\" size=\"40\" />";
 echo '</td></tr>';
 echo '</table>';

 /* All upstream stuff */
 echo '<div id="upstream">';
 echo "<table class=\"editor\">\n";
 echo '<tr><td class="label">'.i18n('Upstream URL').':</td><td class="right">';
 if($f['url'] == '') $f['url'] = 'http://';
 echo "<input type=\"text\" name=\"url\" value=\"{$f['url']}\" size=\"40\" />";
 echo '</td></tr>';
 echo '<tr><td class="label">';
 echo i18n(($f['type'] == 'ysc') ? 'YSC Region ID' : 'Upstream Username');
 echo ':</td><td class="right">';
 echo "<input type=\"text\" name=\"username\" value=\"{$f['username']}\" size=\"20\" />";
 echo '</td></tr>';
 echo '<tr><td class="label">';
 echo i18n(($f['type'] == 'ysc') ? 'YSC Region Password' : 'Upstream Password');
 echo ':</td><td class="right">';
 echo "<input type=\"text\" name=\"password\" value=\"{$f['password']}\" size=\"15\" />";
 echo '</td></tr>';
 echo '<tr><td class="label">'.i18n('Enable stats upload').':</td><td class="right">';
 yesno('enable_stats', $f['enable_stats']);
 echo '</td></tr>';
 echo '<tr><td class="label">'.i18n('Enable awards download').':</td><td class="right">';
 yesno('enable_awards', $f['enable_awards']);
 echo '</td></tr>';
 echo '<tr><td class="label">'.i18n('Enable winners upload').':</td><td class="right">';
 yesno('enable_winners', $f['enable_winners']);
 echo '</td></tr>';
 /* End upstream stuff */
 echo "</table>";

 echo i18n('* Use the \'Personal\' tab to specify contact information for someone at this fair.');
 echo '</div>';
 echo '<div id="feeder">';
 echo i18n('* The feeder fair must login to this SFIAB to download award lists
 and upload statistics and winners.  Use the \'Personal\' tab to specify an
 email and password for the feeder fair, use the email address of a contact at
 the feeder fair.  Then give the email/password to that person so they can configure
 their own SFIAB to upload data to this SFIAB.'); echo '</div>';

 echo "<br />";
echo "<input type=\"submit\" onclick=\"fairinfo_save();return false;\" value=\"".i18n("Save Fair Information")."\" />\n";
echo "</form>";

 echo "<br />";



 if($_SESSION['embed'] != true) send_footer();

?>
<script language="javascript" type="text/javascript">
<!--

var fairtype=document.getElementById("type");
fairtype.onchange=function() { /* Hook onto the onchange */
	var type = this.options[this.selectedIndex].value;
	var upstream_div = document.getElementById("upstream");
	var feeder_div = document.getElementById("feeder");
	if(type == "feeder") {
		upstream_div.style.display="none";
		feeder_div.style.display="block";
	} else if(type == "sfiab") {
		upstream_div.style.display="block";
		feeder_div.style.display="none";
	} else {
		upstream_div.style.display="block";
		feeder_div.style.display="none";
	}
	return true;
}


fairtype.onchange();
-->
</script>
<?

?>
