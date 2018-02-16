<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2007 James Grant <james@lightbox.org>
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
 require_once('../common.inc.php');
 require_once('../user.inc.php');
 require_once('reports.inc.php');

 user_auth_required('committee');

$option_keys = array('type','stock');


switch($_GET['action']) {
case 'remove_report':
 	$id = intval($_GET['id']);
	mysql_query("DELETE FROM reports_committee WHERE
			users_id='{$_SESSION['users_uid']}' AND id='$id'");
	happy_('Report successfully removed');
	exit;
case 'reload':
 	$edit_mode = true;
 	$reports_id = intval($_POST['reports_id']);
	exit;

case 'load_report':
 	$id = intval($_GET['id']);

	/* Load report */
	if($id == -1) {
		$reports_id = intval($_GET['reports_id']);
		$report = report_load($reports_id);

		$ret['id'] = -1;
		$ret['reports_id'] = $reports_id;
		$ret['type'] = $report['option']['type'];
		$ret['stock'] = $report['option']['stock'];
		$ret['comment'] = $report['desc'];
		$ret['name'] = $report['name'];
		$ret['category'] = '';
	} else {
		$q = mysql_query("SELECT * FROM reports_committee WHERE id='$id'");
		$ret = mysql_fetch_assoc($q);
		$ret['type'] = $ret['format'];
	}

	/* Load available categories */
	$q = mysql_query("SELECT DISTINCT category FROM reports_committee
 			WHERE users_id='{$_SESSION['users_uid']}'
			ORDER BY category");
	while($i = mysql_fetch_object($q))
		$ret['cat'][] = $i->category;
	echo json_encode($ret);
	exit;

case 'save':
	echo "POST: ";
	print_r($_POST);
	$id = intval($_POST['id']);
 	$reports_id = intval($_POST['reports_id']);
	if($id == -1) {
		/* New entry */
		mysql_query("INSERT INTO `reports_committee` (`users_id`,`reports_id`) 
			VALUES('{$_SESSION['users_uid']}','$reports_id');");
		echo mysql_error();
		$id = mysql_insert_id();
	}

	/* Update entry */
	$category = $_POST['category'];
	$category_exist = $_POST['category_exist'];
	$comment = mysql_real_escape_string(stripslashes($_POST['comment']));

	if($category_exist != '') $category = $category_exist;
	$category = mysql_real_escape_string(stripslashes(trim($category)));

	if($category == '') $category = 'default';

	if($reports_id > 0) {
		/* SFIAB report */
		$type = $_POST['type'];
		$stock = $_POST['stock'];
		if(!array_key_exists($type, $report_options['type']['values'])) {
			error_("Invalid format: type=$type");
			exit;
		}
		if(!array_key_exists($stock, $report_stock)) {
			error_("Invalid stock: stock=$stock");
			exit;
		}
	} else {
		/* Old custom */
		$type = '';
		$stock = '';
	}

	mysql_query("UPDATE `reports_committee` SET
			`category`='$category',
			`comment`='$comment',
			`format`='$type',
			`stock`='$stock'
			WHERE id='$id'");
	happy_("Saved");
	exit;
 }


 //send the header
send_header("My Reports", 
		array("Committee Main" => "committee_main.php"),
		            "print/export_reports"
		);

/* Send a greeting */
echo i18n('Welcome to the new report interface.  You can select and save specific reports under specific categories so you can always find the report you need without having to go through the list each time.  To begin customizing this list, click on the "Edit This List" button at the bottom of this page.');
?>
<br /><br />

<script type="text/javascript">

function remove_report(id)
{
	$('#debug').load("<?$_SERVER['PHP_SELF']?>?action=remove_report&id="+id);
	$("#report_tr_"+id).remove();
}

function edit_report(id,reports_id)
{
	var r = (id == -1) ? '&reports_id='+reports_id : '';
	$.getJSON("<?=$_SERVER['PHP_SELF']?>?action=load_report&id="+id+r,
		function(json){
			$("#report_category_exist").html("<option value=\"\">-- <?=i18n('Use New Category')?> --</option>");
			for(var i in json.cat ) {
				var c = json.cat[i];
				$("#report_category_exist").append("<option value=\""+c+"\">"+c+"</option>");
			}
			$("#report_id").val( (id == -1) ? -1 : json.id);
			$("#report_reports_id").val(json.reports_id);
			$("#report_category").val(json.category);
			$("#report_stock").val(json.stock);
			$("#report_format").val(json.format);
			$("#report_comment").val(json.comment);
			/* Update the dialog title */
			$('#popup_editor').dialog('option', 'title', "<?=i18n('Report')?>: " + json.name);

			popup_editor(id);
		});
}

function save_report()
{
	$('#debug').load("<?$_SERVER['PHP_SELF']?>?action=save", $('#report_form').serializeArray(), function() {
		window.location.reload();
	});
}

function add_report()
{
	edit_report(-1, $('#report').val());
}

function gen_report() {
	report_gen($('#report').val());
	return false;
}

var edit=false;
function edit_toggle()
{
	if(edit == false) {
		$('#edit_toggle').val("<?=i18n("Done Editing")?>");
		$('#edit_info').show();
		$('.edit_buttons').show();
		edit = true;
	} else {
		$('#edit_toggle').val("<?=i18n("Edit This List")?>");
		$('#edit_info').hide();
		$('.edit_buttons').hide();
		edit = false;
	}
}

function popup_editor(id)
{
	var w = (document.documentElement.clientWidth * 0.6);
	var h = (document.documentElement.clientHeight * 0.4);

	report_id = id;

	/* Show the dialog */
	$('#popup_editor').dialog('option', 'width', w);
	$('#popup_editor').dialog('option', 'height', h);
	$("#popup_editor").dialog('open');
	return true;
}

/* Setup the popup window */
$(document).ready(function() { 
	$("#popup_editor").dialog({
			bgiframe: true, autoOpen: false,
			modal: true, resizable: false,
			draggable: false,
			buttons: { 
				"<?=i18n('Cancel')?>": function() { 
					$(this).dialog("close"); 
				},
				"<?=i18n('Save')?>": function() { 
					save_report();	
					$(this).dialog("close"); 
				} 
			} 
		});
});
	
</script>
<?

 /* Load all the users reports */
 $q = mysql_query("SELECT reports_committee.*,reports.name
 			FROM reports_committee 
				LEFT JOIN reports ON reports.id=reports_committee.reports_id
 			WHERE users_id='{$_SESSION['users_uid']}'
			ORDER BY category,id");
 echo mysql_error();
 if(mysql_num_rows($q) == 0) {
 	echo i18n('You have no reports saved');
 } else {

	$last_category = '';
	$x=0;
	echo "<table class=\"tableview\" style=\"border:0px;\">";
	while($i = mysql_fetch_object($q)) {
		$x++;
		if($last_category != $i->category) {
			/* New category */
			echo '<tr><td style="border:0px;" colspan="3" style="even"><h3>';
			if($edit_mode == true) echo i18n('Category').': ';
			echo "{$i->category}</h3></td></tr>";
			$last_category = $i->category;
		}

		if($i->reports_id > 0) { 
//			$url = "admin/reports_gen.php?id={$i->reports_id}&show_options=1";
			$name = "<a href=\"#\" onclick=\"return report_gen({$i->reports_id})\">{$i->name}</a>";
		} else {
			$name = "<a href=\"{$config['SFIABDIRECTORY']}/{$report_custom[-$i->reports_id]['custom_url']}\">
					{$report_custom[-$i->reports_id]['name']}</a>";
		}
?>
		<tr id="report_tr_<?=$i->id?>">
		<td style="border:0px;"><?=$name?></td>
		<td style="border:0px;"><?=$i->comment?></td>
		<td style="border:0px;">
			<div class="edit_buttons" style="display:none">
			<a title="Edit Report" onclick="edit_report(<?=$i->id?>,0);return false;" href="#">
				<img border="0" src="<?=$config['SFIABDIRECTORY']?>/images/16/edit.<?=$config['icon_extension']?>" />
			</a>
			<a title="Remove Report" onclick="remove_report(<?=$i->id?>);return false;" href="#">
				<img src="<?=$config['SFIABDIRECTORY']?>/images/16/button_cancel.<?=$config['icon_extension']?>" border="0" alt="Remove Report" />
			</a>&nbsp;
			</div>
			</td>
		</tr>			
<?
/*

		if($i->reports_id > 0) {
			echo '<tr><td width=\"20px\"></td><td>';
			echo '<span style=\"font-size: 0.75em;\">';
			echo i18n('Format').": {$i->format}, ";
			echo i18n('Paper').": {$report_stock[$i->stock]['name']}, ";
			echo i18n('Year').": {$config['FAIRYEAR']}";
			echo '</span>';
			echo '</td></tr>';
		}
*/

	}
	echo "</table>";
		
 }
 
?>

<div id="edit_info" style="display:none;"> 
<p>* <?=i18n('Deleting all the reports from a category will also delete the category.')?></p>
<p>* <?=i18n('Deleting a report only unlinks it from your list, it doesn\'t delete it from the system.')?></p>
</div>
<br />
<input id="edit_toggle" type="submit" onclick="edit_toggle();return false;" value="<?=i18n("Edit This List")?>">
<br />
<br />

<?
 /* Load available reports */
 $reports = report_load_all();
 foreach($report_custom as $id=>$r) {
 	$r['id'] = -$id;
 	$reports[-$id] = $r;
 }
?>

<hr />
<h4><?=i18n("All Reports")?></h3>

<form name="reportgen" >
<select name="id" id="report">
<option value="0"><?=i18n("Select a Report")?></option>
<?
foreach($reports as $r) {
	echo "<option value=\"{$r['id']}\">{$r['name']}</option>\n";
}
?>
</select><br />
<input type="submit" onclick="gen_report();return false;" value="<?=i18n("Generate Report")?>">
<input type="submit" onclick="add_report();return false;" value="<?=i18n("Add this Report to my list")?>">
</form>
<br />

<?
 /* Create an add report box */
?>
<div id="popup_editor" title="Report" style="display: none">

<form id="report_form">
<input type="hidden" id="report_id" name="id" value="" />
<input type="hidden" id="report_reports_id" name="reports_id" value="" />
<br />
<table class="tableedit">
<tr>
	<td><?=i18n("Category")?>:</td>
	<td><?=i18n("Existing Category")?>: <select name="category_exist" id="report_category_exist" onchange="$('#report_category').val('')" >
			</select><br />
		<?=i18n("OR New Category")?>: <input type="text" id="report_category" name="category" onkeypress="$('#report_category_exist').val('')" >
	</td>
</tr>
<?
 foreach($report_options as $ok=>$o) {
 	if(!in_array($ok, $option_keys)) continue;
 	echo "<tr><td>{$o['desc']}:</td>";
	echo "<td><select name=\"$ok\" id=\"report_$ok\">";
	foreach($o['values'] as $k=>$v) {
		echo "<option value=\"$k\">$v</option>\n";
	}
	echo "</select><span id=\"report{$ok}custom\" style=\"display: none;\">".i18n("Custom")."</span></td></tr>";
 }
?> 
<tr>
	<td><?=i18n("Comments")?>:</td>
	<td><textarea rows="3" cols="40" name="comment" id="report_comment"></textarea></td>
</tr>
</table>
</form>
</div>

<?

send_footer();
?>
