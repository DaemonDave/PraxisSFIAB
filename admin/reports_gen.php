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
 require_once("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');
 require_once('reports.inc.php');

 
 $id = intval($_GET['id']);
 $type = stripslashes($_GET['type']);
 $stock = stripslashes($_GET['stock']);
 $year = intval($_GET['year']);
 $include_incomplete_registrations = ($_GET['include_incomplete_registrations'] == 'yes') ? 'yes' : '';
 $show_options = array_key_exists('show_options', $_GET);
 if($year < 1000) $year = $config['FAIRYEAR'];

 /* If it's a system report, turn that into the actual report id */
 if(array_key_exists('sid', $_GET)) {
 	$sid = intval($_GET['sid']);
 	$q = mysql_query("SELECT id FROM reports WHERE system_report_id='$sid'");
	$r = mysql_fetch_assoc($q);
	$id = $r['id'];
 }
 
$report = report_load($id);
/* Add a custom filter if specified */
$filter_args = '';
if(is_array($_GET['filter'])) {
	foreach($_GET['filter'] as $f=>$v) {
		$report['filter'][] = array('field'=>$v['field'],'x'=>$v['x'],'value'=>$v['value']);
		$filter_args.="&filter[$f][field]={$v['field']}&filter[$f][x]={$v['x']}&filter[$f][value]={$v['value']}";
	}
}

switch($_GET['action']) {
case 'dialog_gen':
	if($id < 0) {
		$u = "{$config['SFIABDIRECTORY']}/{$report_custom[-$id]['custom_url']}";
		?>
		<script type="text/javascript">
			window.location.href="<?=$u?>";
		</script>
		<?
		exit;
	}
	?>
	<div id="report_dialog_gen" title="Generate Report" style="display: none">
	<div id="report_gen_tabs">
	<ul><li><a href="#report_gen_tab_info"><span><?=i18n('Report Information')?></span></a></li>
	<li><a href="#report_gen_tab_advanced"><span><?=i18n('Advanced Options')?></span></a></li>
	</ul>
	<form id="report_dialog_form" >
	<div id="report_gen_tab_info">
	<input type="hidden" name="id" value="<?=$id?>" />
	<table class="editor" style="width:95%"><tr>
		<td colspan="2"><br /><h3><?=i18n('Report Information')?></h3><br /></td>
	</tr><tr>
		<td class="label"><b><?=i18n("Report Name")?></b>:</td>
		<td class="input"><?=$report['name']?></b></td>
	</tr><tr>
		<td class="label"><b><?=i18n("Description")?></b>:</td>
		<td class="input"><?=$report['desc']?></b></td>
	</tr><tr>
		<td class="label"><b><?=i18n("Created By")?></b>:</td>
		<td class="input"><?=$report['creator']?></td>
	</tr><tr>
	<?
	/* See if the report is in this committee member's list */
	$q = mysql_query("SELECT * FROM reports_committee 
 				WHERE users_id='{$_SESSION['users_uid']}'
				AND reports_id='{$report['id']}'");
	if(mysql_num_rows($q) > 0) {
		$i = mysql_fetch_assoc($q);
		?>
		<td colspan="2"><hr /><h3><?=i18n('My Reports Info')?></h3></td>
		</tr><tr>
			<td class="label"><b><?=i18n("Category")?></b>:</td>
			<td class="input"><?=$i['category']?></b></td>
		</tr><tr>
			<td class="label"><b><?=i18n("Comment")?></b>:</td>
			<td class="input"><?=$i['comment']?></b></td>
		</tr><tr>
	<? } ?>

		<td colspan="2"><br /><hr /><h3><?=i18n('Report Options')?></h3><br /></td>
	</tr>
	<?
	$format = $report['options']['type'];
	$stock = $report['options']['stock'];
	$year = $config['FAIRYEAR'];

	 /* Out of all the report optins, we really only want these ones */
	$option_keys = array('type','stock');
	foreach($report_options as $ok=>$o) {
 		if(!in_array($ok, $option_keys)) continue;

		echo "<tr><td class=\"label\"><b>{$o['desc']}</b>:</td>";
		echo "<td class=\"input\"><select name=\"$ok\" id=\"$ok\">";
		foreach($o['values'] as $k=>$v) {
			$sel = ($report['option'][$ok] == $k) ? 'selected="selected"' : '';
			echo "<option value=\"$k\" $sel>".htmlspecialchars($v)."</option>";
		}
		echo "</select></td></tr>\n";
	}
	/* Find all the years */
	$q = mysql_query("SELECT DISTINCT year FROM config WHERE year>1000 ORDER BY year DESC");
	echo "<tr><td  class=\"label\"><b>".i18n('Year')."</b>:</td>";
	echo "<td class=\"input\"><select name=\"year\" id=\"year\">";
	while($i = mysql_fetch_assoc($q)) {
		$y = $i['year'];
		$sel = ($config['FAIRYEAR'] == $y) ? 'selected="selected"' : '';
		echo "<option value=\"$y\" $sel>$y</option>";
	}
	echo "</select></td></tr>\n";
	?>
	</table>
	</div>
	<div id="report_gen_tab_advanced">
	<table class="editor" style="width:95%"><tr>
		<td colspan="2"><br /><h4><?=i18n('Advanced Options')?></h4><br /></td>
	</tr><tr>
		<td class="label"><input type="checkbox" name="include_incomplete_registrations" value="yes" /></td>
		<td class="input"><?=i18n('Include student and project data from incomplete registrations.  The registration only needs to have a division and category selected.')?></td>
	</table>
	</div>
	</form>
	</div></div>
	<script type="text/javascript">
		$("#report_gen_tabs").tabs();

		$("#report_dialog_gen").dialog({
				bgiframe: true, autoOpen: true,
				modal: true, resizable: false,
				draggable: false,
				width: 800, //(document.documentElement.clientWidth * 0.8);
				height: 600, //(document.documentElement.clientHeight * 0.7),
				close: function() {
						$(this).dialog('destroy');
						$('#report_dialog_gen').remove();
					},
				buttons: { "<?=i18n("Cancel")?>": function() {  
							$('#report_dialog_gen').dialog("close");
							return false;
						},
					   "<?=i18n("Download Report")?>": function() { 
							var dlargs = $('#report_dialog_form').serialize()+"<?=$filter_args?>";
							var dlurl = "<?=$config['SFIABDIRECTORY']?>/admin/reports_gen.php?"+dlargs;
							$('#debug').html(dlurl);
	//						alert(dlurl);
	//						$('#content').attr('src',dlurl);
							window.location.href=dlurl;
							$('#report_dialog_gen').dialog("close");
							return false;
						}
					}
				});

	</script>
	<?
	exit;
 }


 if($show_options == false) {
	 if($id && $year) {
		$report['year'] = $year;
		if($type != '') $report['option']['type'] = $type;
		if($stock != '') $report['option']['stock'] = $stock;
		if($include_incomplete_registrations != '') $report['option']['include_incomplete_registrations'] = 'yes';
		report_gen($report);
	 } else {
	 exit;
 		header("Location: reports.php");
	 }
	 exit;
 }


 send_header('Report Options', array(
 		'Committee Main' => 'committee_main.php',
		'My Reports' => 'admin/reports.php'));

 echo '<form method=\"get\" action="reports_gen.php">';
 echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";

 echo '<table class="tableedit">';
 echo "<tr><td><b>".i18n('Report&nbsp;Name')."</b>:</td>";
 echo "<td>{$report['name']}</td></tr>";
 echo "<tr><td><b>".i18n('Description')."</b>:</td>";
 echo "<td>{$report['desc']}</td></tr>";
 echo "<tr><td><b>".i18n('Created By')."</b>:</td>";
 echo "<td>{$report['creator']}</td></tr>";

 echo '<tr><td colspan="2"><hr /></td></tr>';
 /* See if the report is in this committee member's list */
 $q = mysql_query("SELECT * FROM reports_committee 
 			WHERE users_id='{$_SESSION['users_uid']}'
			AND reports_id='{$report['id']}'");
 echo "<tr><td colspan=\"2\"><h3>".i18n('My Reports Info')."</h3></td></tr>";
 if(mysql_num_rows($q) > 0) {
 	/* Yes, it is */
	$i = mysql_fetch_object($q);
	echo "<tr><td><b>".i18n('Category')."</b>:</td>";
	echo "<td>{$i->category}</td></tr>";
	echo "<tr><td><b>".i18n('Comment')."</b>:</td>";
	echo "<td>{$i->comment}</td></tr>";
 } else {
 	echo "<tr><td></td><td>".i18n('This report is NOT in your \'My Reports\' list.')."</td></tr>";
 }
 echo '<tr><td colspan="2"><hr /></td></tr>';
 echo "<tr><td colspan=\"2\"><h3>".i18n('Report Options')."</h3></td></tr>";

 $format = $report['options']['type'];
 $stock = $report['options']['stock'];
 $year = $config['FAIRYEAR'];

 /* Out of all the report optoins, we really only want these ones */
 $option_keys = array('type','stock');
 foreach($report_options as $ok=>$o) {
 	if(!in_array($ok, $option_keys)) continue;
 	echo "<tr><td><b>{$o['desc']}</b>:</td>";
	echo "<td><select name=\"$ok\" id=\"$ok\">";
	foreach($o['values'] as $k=>$v) {
		$sel = ($report['option'][$ok] == $k) ? 'selected="selected"' : '';
		echo "<option value=\"$k\" $sel>$v</option>";
	}
	echo "</select></td></tr>";
 }
 /* Find all the years */
 $q = mysql_query("SELECT DISTINCT year FROM config WHERE year>1000 ORDER BY year DESC");
 echo "<tr><td><b>".i18n('Year')."</b>:</td>";
 echo "<td><select name=\"year\" id=\"year\">";
 while($i = mysql_fetch_assoc($q)) {
 	$y = $i['year'];
	$sel = ($config['FAIRYEAR'] == $y) ? 'selected="selected"' : '';
	echo "<option value=\"$y\" $sel>$y</option>";
 }
 echo "</select></td></tr>";

 echo "</table>";

 echo '<br />';
 echo "<input type=\"submit\" value=\"".i18n('Generate Report')."\" />";
 echo '</form>';

 send_footer();

 

?>
