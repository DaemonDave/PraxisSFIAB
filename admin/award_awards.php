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
 require_once('../common.inc.php');
 require_once('../user.inc.php');
 user_auth_required('committee', 'admin');
 require_once('awards.inc.php');

 switch($_GET['action']) {
 case 'awardinfo_load':
 	$id = intval($_GET['id']);
	$q=mysql_query("SELECT * FROM award_awards WHERE id='$id'");
	$ret = mysql_fetch_assoc($q);
	//json_encode NEEDS UTF8 DATA, but we store it in the database as ISO :(
	foreach($ret AS $k=>$v) {
		$ret[$k]=iconv("ISO-8859-1","UTF-8",$v);
	}
	//echo iconv("ISO-8859-1","UTF-8",json_encode($ret));
	echo json_encode($ret);
	exit;

 case 'award_delete':
	$id=intval($_GET['id']);
	award_delete($id);
	exit;

 case 'awardinfo_save':
 	/* Scrub the data while we save it */
	$id=intval($_POST['id']);

	if($id == -1) {
		$q=mysql_query("INSERT INTO award_awards (year,self_nominate,schedule_judges) 
				VALUES ('{$config['FAIRYEAR']}','yes','yes')");
		$id = mysql_insert_id();
		happy_("Award Created");
		/* Set the award_id in the client */
		echo "<script type=\"text/javascript\">award_id=$id;</script>";
	}

	$q = "UPDATE award_awards SET 
		award_types_id='".intval($_POST['award_types_id'])."',
		presenter='".mysql_escape_string(iconv("UTF-8","ISO-8859-1",stripslashes($_POST['presenter'])))."', 
		excludefromac='".(($_POST['excludefromac'] == 1) ? 1 : 0)."',
		cwsfaward='".(($_POST['cwsfaward'] == 1) ? 1 : 0)."', 
		self_nominate='".(($_POST['self_nominate'] == 'yes') ? 'yes' : 'no')."', 
		schedule_judges='".(($_POST['schedule_judges'] == 'yes') ? 'yes' : 'no')."', 
		description='".mysql_escape_string(iconv("UTF-8","ISO-8859-1",stripslashes($_POST['description'])))."' ";

	if(array_key_exists('name', $_POST)) {
		/* These values may be disabled, if they name key exists, assume
		 * they aren't disabled and save them too */
		$q .= ",name='".mysql_escape_string(iconv("UTF-8","ISO-8859-1",stripslashes($_POST['name'])))."',
			criteria='".mysql_escape_string(iconv("UTF-8","ISO-8859-1",stripslashes($_POST['criteria'])))."', 
			sponsors_id='".intval($_POST['sponsors_id'])."' ";
	}
	$q .= "WHERE id='$id'";
	mysql_query($q);
	print_r($_POST);
	echo $q;
	echo mysql_error();
	happy_("Award information saved");
	exit;

 case 'eligibility_load':
 	$id = intval($_GET['id']);
	//select the current categories that this award is linked to
	$ret = array('categories'=>array(), 'divisions'=>array() );
	$q=mysql_query("SELECT * FROM award_awards_projectcategories WHERE award_awards_id='$id'");
	while($r=mysql_fetch_assoc($q)) {
		$ret['categories'][] = $r['projectcategories_id'];
	}

	//select the current categories that this award is linked to
	$q=mysql_query("SELECT * FROM award_awards_projectdivisions WHERE award_awards_id='$id'");
	while($r=mysql_fetch_assoc($q)) {
		$ret['divisions'][] = $r['projectdivisions_id'];
	}
	echo json_encode($ret);
	exit;

 case 'eligibility_save':
 	$id = intval($_POST['award_awards_id']);

	//now add the new ones
	if(!is_array($_POST['categories']) || !is_array($_POST['divisions'])) {
		error_("Invalid data");
		exit;
	}

	//wipe out any old award-category links
	mysql_query("DELETE FROM award_awards_projectcategories WHERE award_awards_id='$id'");

	foreach($_POST['categories'] AS $key=>$cat) {
		$c = intval($cat);
		mysql_query("INSERT INTO award_awards_projectcategories (award_awards_id,projectcategories_id,year) 
				VALUES ('$id','$c','{$config['FAIRYEAR']}')");
		echo mysql_error();
	}

	//wipe out any old award-divisions links
	mysql_query("DELETE FROM award_awards_projectdivisions WHERE award_awards_id='$id'");

	//now add the new ones
	foreach($_POST['divisions'] AS $key=>$div) {
		$d = intval($div);
		mysql_query("INSERT INTO award_awards_projectdivisions (award_awards_id,projectdivisions_id,year) 
				VALUES ('$id','$d','{$config['FAIRYEAR']}')");
		echo mysql_error();
	}
	happy_("Eligibility information saved");
	exit;

 case 'prize_order':
	$order = 0;
	foreach ($_GET['prizelist'] as $position=>$id) {
		if($id == '') continue;
		$order++;
		mysql_query("UPDATE `award_prizes` SET `order`='$order' WHERE `id`='$id'");
	}
//	print_r($_GET);
	happy_("Order Updated.");
	exit;

 case 'award_order':
	$order = 0;
	foreach ($_GET['awardlist'] as $position=>$id) {
		if($id == '') continue;
		$order++;
		mysql_query("UPDATE `award_awards` SET `order`='$order' WHERE `id`='$id'");
	}
	happy_("Order updated");
	exit;

 case 'prizeinfo_load':
	$id = intval($_GET['id']);
	if($id == -1) {
		$q=mysql_query("SELECT * FROM award_prizes WHERE year='-1' AND award_awards_id='0' ORDER BY `order`");
	} else {
		$q = mysql_query("SELECT * FROM award_prizes WHERE award_awards_id='$id' ORDER BY `order`");
	}
	while($r=mysql_fetch_assoc($q)) {
		foreach($r AS $k=>$v) {
			$r[$k]=iconv("ISO-8859-1","UTF-8",$v);
		}
		$ret[] = $r;
	}
	echo json_encode($ret);
	exit;
 case 'prize_load':
	$id = intval($_GET['id']);
	$q = mysql_query("SELECT * FROM award_prizes WHERE id='$id'");
	$ret=mysql_fetch_assoc($q);
	foreach($ret AS $k=>$v) {
		$ret[$k]=iconv("ISO-8859-1","UTF-8",$v);
	}
	echo json_encode($ret);
	exit;

 case 'prize_create':
	$aaid = intval($_GET['award_awards_id']);
	$year = $config['FAIRYEAR'];
	if($aaid == -1) {
		$aaid = 0;
		$year = -1;
	}
	mysql_query("INSERT INTO award_prizes(award_awards_id,year) VALUES ('$aaid','$year');");
	$ret = array('id' => mysql_insert_id() );
	echo json_encode($ret);
	exit;

 case 'prize_save':
	$id = intval($_POST['id']);
	$q="UPDATE award_prizes SET 
				prize='".mysql_escape_string(stripslashes(iconv("UTF-8","ISO-8859-1",$_POST['prize'])))."', 
				cash='".intval($_POST['cash'])."',
				scholarship='".intval($_POST['scholarship'])."',
				value='".intval($_POST['value'])."',
				number='".intval($_POST['number'])."',
				excludefromac='".(($_POST['excludefromac']==1)? 1 : 0)."',
				trophystudentkeeper='".(($_POST['trophystudentkeeper']==1) ? 1 : 0)."',
				trophystudentreturn='".(($_POST['trophystudentreturn']==1) ? 1 : 0)."',
				trophyschoolkeeper='".(($_POST['trophyschoolkeeper']==1) ? 1 : 0)."',
				trophyschoolreturn='".(($_POST['trophyschoolreturn']==1) ? 1 : 0)."'
				WHERE id='$id'";
	mysql_query($q);
//	echo $q;
//	echo mysql_error();
	happy_("Prize saved");
	exit;

 case 'prize_delete':
	$id = intval($_GET['id']);
	award_prize_delete($id);
	happy_("Prize deleted");
	exit;

 case 'feeder_load':
 	$id = intval($_GET['id']);
	/* Prepare two lists of fair IDs, for which fairs can upload and download this award */
	$q=mysql_query("SELECT * FROM fairs_awards_link WHERE award_awards_id='$id'");
	$ul = array(); 
	$dl = array();
	while($r=mysql_fetch_assoc($q)) {
		if($r['upload_winners'] == 'yes') $ul[$r['fairs_id']] = true;
		if($r['download_award'] == 'yes') $dl[$r['fairs_id']] = true;
	}
	$q = mysql_query("SELECT * FROM award_awards WHERE id='$id'");
	$a = mysql_fetch_assoc($q);
?>
	<h4><?=i18n("Feeder Fairs")?></h4>
	<form id="feeder_form">
	<input type="hidden" id="feeder_id" name="award_awards_id" value="<?=$a['id']?>"/>

<?	$ch = $a['per_fair'] == 'yes' ? 'checked="checked"' : ''; ?>
	<p><input type="checkbox" name="per_fair" value="yes" <?=$ch?> />
		<?=i18n("Treat this award as a separate award for each feeder fair (instead of as a single award across the whole system).  This will allow winners to be assigned to prizes for each feeder fair.  If disabled, only a single group if winners will be permitted across all feeder fairs.")?></p>

<?	$ch = (count($ul) || count($dl)) ? 'checked="checked"' : ''; ?>
	<p><input type="checkbox" id="feeder_enable" name="enable" value="yes" <?=$ch?> />
		<?=i18n("Allow feeder fairs to download this award.")?></p>
	<div id="feeder_en">
	<table class="editor">
		<tr><td><?=i18n('Unique Name')?>:</td>
			<td><input type="text" name="identifier" value="<?=$a['external_identifier']?>" size="40" maxlength="128" /></td></tr>
<?		$ch = $a['external_additional_materials'] ? 'checked="checked"' : ''; ?>
		<tr><td><input type="checkbox" name="register_winners" value="1" <?=$ch?> /></td>
			<td><?=i18n("Winners uploaded by a feeder fair should be registered as participants at this fair (both download award and upload winners should be turned on below)")?></td></tr>
<?		$ch = $a['external_register_winners'] ? 'checked="checked"' : ''; ?>
		<tr><td><input type="checkbox" name="additional_materials" value="1" <?=$ch?> /></td>
			<td><?=i18n("There is additional material for this award (e.g. forms, instructions).  If a feeder fair assigns a winner to this award, they will be told they need to contact this fair to get the additional material.")?></td></tr>
	</table>	
	<p><?=i18n("Select which feeder fairs can download this award and upload winners.")?></p>
	<table class="tableview">
		<tr><th><?=i18n("Fair")?></th>
		<th style="width: 5em"><?=i18n("Download Award")?></th>
		<th style="width: 5em"><?=i18n("Upload Winners")?></th>
		</tr>
<?
	$q = mysql_query("SELECT * FROM fairs WHERE type='feeder'");
	while($r = mysql_fetch_assoc($q)) {
		echo "<tr><td style=\"padding-left:1em;padding-right:1em\">{$r['name']}</td>";
		$ch = $dl[$r['id']] == true ? 'checked="checked"' : '';
		echo "<td style=\"text-align:center\"><input type=\"checkbox\" name=\"feeder_dl[]\" value=\"{$r['id']} $ch \"></td>";
		$ch = $ul[$r['id']] == true ? 'checked="checked"' : '';
		echo "<td style=\"text-align:center\"><input type=\"checkbox\" name=\"feeder_ul[]\" value=\"{$r['id']} $ch \"></td>";
		echo '</tr>';
	}
?>
	</table>
	</div>
	<br />
	<button id="feeder_save"><?=i18n("Save")?></button>
	</form>
<?
	exit;

 case 'feeder_save':
// 	print_r($_POST);

 	$id = intval($_POST['award_awards_id']);
	$dl = is_array($_POST['feeder_dl']) ? $_POST['feeder_dl'] : array();
	$ul = is_array($_POST['feeder_ul']) ? $_POST['feeder_ul'] : array();

	/* Prepare a fair-wise list */
	$data = array();
	foreach($dl AS $fairs_id) $data[$fairs_id]['dl']  = true;
	foreach($ul AS $fairs_id) $data[$fairs_id]['ul']  = true;

	/* Now save each one */
	mysql_query("DELETE FROM fairs_awards_link WHERE award_awards_id='$id'");
				echo mysql_error();
	foreach($data as $fairs_id=>$f) {
		$dl = ($f['dl'] == true) ? 'yes' : 'no';
		$ul = ($f['ul'] == true) ? 'yes' : 'no';
		mysql_query("INSERT INTO fairs_awards_link (award_awards_id,fairs_id,download_award,upload_winners)
				VALUES ('$id','$fairs_id','$dl','$ul')");
				echo mysql_error();
	}
	$ident=mysql_escape_string(stripslashes($_POST['identifier']));
	$per_fair = $_POST['per_fair'] == 'yes' ? 'yes' : 'no';
	$mat = intval($_POST['additional_materials']);
	$w = intval($_POST['register_winners']);
	mysql_query("UPDATE award_awards SET external_identifier='$ident',
						external_additional_materials='$mat',
						external_register_winners='$w',
						per_fair='$per_fair'
					WHERE id='$id'");

	happy_("Feeder Fair information saved");
	exit;
}

 send_header("Awards Management",
		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Awards Main' => 'admin/awards.php') );

 ?>
<script type="text/javascript" src="../js/jquery.tablednd_0_5.js"></script>
<script type="text/javascript">

var award_id = 0;
var award_tab_update = new Array();

function update_awardinfo()
{
	if(award_tab_update['awardinfo'] == award_id) return;
	award_tab_update['awardinfo'] = award_id;
//	alert(award_id);
	if(award_id == -1) {
//		$("#awardinfo input:text").val('');
		/* New award, set defaults and clear everythign else */
		$("#awardinfo_id").val(-1);
		$("#awardinfo_name").val("");
		$("#awardinfo_sponsors_id").val(0);
		$("#awardinfo_presenter").val("");
		$("#awardinfo_description").val("");
		$("#awardinfo_criteria").val("");
		$("#awardinfo_award_types_id").val(5);
		// For some reason, with checkboxes, these have to be arrays 
		$("#awardinfo_excludefromac").val([]);
		$("#awardinfo_cwsfaward").val([]);
		$("#awardinfo_selfnominate").val(["yes"]);
		$("#awardinfo_schedulejudges").val(["yes"]); 
		return;
	}

	/* Enable all fields */
	$("#awardinfo *").removeAttr('disabled');

	$.getJSON("<?=$_SERVER['PHP_SELF']?>?action=awardinfo_load&id="+award_id,
		function(json){
			$("#awardinfo_id").val(json.id);
			$("#awardinfo_name").val(json.name);
			$("#awardinfo_award_source_fairs_id").val(json.award_source_fairs_id);
			$("#awardinfo_sponsors_id").val(json.sponsors_id);
			$("#awardinfo_presenter").val(json.presenter);
			$("#awardinfo_description").val(json.description);
			$("#awardinfo_criteria").val(json.criteria);
			$("#awardinfo_award_types_id").val(json.award_types_id);
			// For some reason, with checkboxes, these have to be arrays 
			$("#awardinfo_excludefromac").val([json.excludefromac]);
			$("#awardinfo_cwsfaward").val([json.cwsfaward]);
			$("#awardinfo_selfnominate").val([json.self_nominate]);
			$("#awardinfo_schedulejudges").val([json.schedule_judges]); 

			/* Disable fields we don't want the user to edit 
			 * for downloaded awards */
			if(json.award_source_fairs_id>0) {
				$("#awardinfo_name").attr('disabled', 'disabled');
				$("#awardinfo_sponsors_id").attr('disabled', 'disabled');
				$("#awardinfo_criteria").attr('disabled', 'disabled');
			}

			/* Update the dialog title */
			$('#popup_editor').dialog('option', 'title', "<?=i18n('Award')?>: " + $('#awardinfo_name').val());
			/* Update the status */
			if($('#awardinfo_award_source_fairs_id').val() != 0) {
				$('#popup_status').html("<?=addslashes(notice(i18n('This award was downloaded, some fields are not edittable')))?>");
			} else {
				$('#popup_status').html("");
			}

		});
}


function awardinfo_save()
{
	var reload = (award_id == -1) ? true : false;
	/* This is sneaky, we're going to make the awardinfo_save possibly emit
	 * javascript to set a new award_id, so it will ALWAYS be correct
	 * after the .load finishes */
	$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=awardinfo_save", $("#awardinfo").serializeArray(),
			function(responseText, textStatus, XMLHttpRequest)
			{
				/* At this point, award_id has been updated by the load */
				/* We want to do this AFTER the load completes.
				 * Somehow, the value of reload properly makes
				 * it into this function */
				if(reload) {
					$("#popup_editor").dialog('close');
					popup_editor(award_id, '');
				}

			});
	return false;
}

function update_eligibility()
{
	if(award_tab_update['eligibility'] == award_id) return;
	award_tab_update['eligibility'] = award_id;
	$.getJSON("<?=$_SERVER['PHP_SELF']?>?action=eligibility_load&id="+award_id,  
		function(json){
			$("[name=categories\\[\\]]").val(json.categories);
			$("[name=divisions\\[\\]]").val(json.divisions);
		});
}

function prizelist_refresh()
{
	$("#prizelist").tableDnD({
		        onDrop: function(table, row) {
				var order = $.tableDnD.serialize();
				$("#prizeinfo_info").load("<?=$_SERVER['PHP_SELF']?>?action=prize_order&"+order); 
				/* Change the order */
				var rows = table.tBodies[0].rows;
				for (var i=0; i<rows.length; i++) {
					$("#prizelist_order_"+rows[i].id).html(i);
				}
	        	},
			dragHandle: "drag_handle"
		});
}

function update_prizeinfo()
{
	/* We can't do this filtering here, sometimes we need to fiorce
	 * a prizeinfo reload */
//	if(award_tab_update['prizeinfo'] == award_id) return;
//	award_tab_update['prizeinfo'] = award_id;
	/* This also works for the prize template, id=-1 */
	$.getJSON("<?=$_SERVER['PHP_SELF']?>?action=prizeinfo_load&id="+award_id,
		function(json) {
			$(".prizelist_tr").remove();
			for( var i in json ) {
				var p = json[i];
				var oc = " onclick=\"edit_prize("+p.id+");\" ";
				$("#prizelist").append("<tr class=\"prizelist_tr\" id=\""+p.id+"\"><td class=\"drag_handle\" style=\"cursor:move; text-align:center;\" id=\"prizelist_order_"+p.id+"\">"+p.order+"</td>"+
							"<td style=\"cursor:pointer; text-align:center;\" "+oc+" >"+p.number+"</td>"+
							"<td style=\"cursor:pointer;\" "+oc+" >"+p.prize+"</td>"+
							"<td style=\"cursor:pointer; width:4em; text-align:right; padding-left:2em; padding-right:2em;\" "+oc+">"+p.cash+"</td>"+
							"<td style=\"cursor:pointer; width:4em; text-align:right; padding-left:2em; padding-right:2em;\" "+oc+">"+p.scholarship+"</td>"+
							"<td style=\"cursor:pointer; width:4em; text-align:right; padding-left:2em; padding-right:2em;\" "+oc+">"+p.value+"</td>"+
							"<td style=\"text-align:center;\">"+
							" <a onclick=\"edit_prize("+p.id+");\" href=\"#\"><img border=\"0\" src=\"<?=$config['SFIABDIRECTORY']?>/images/16/edit.<?=$config['icon_extension']?>\"></a>&nbsp;"+
							"<a onclick=\"prize_delete("+p.id+");\" href=\"#\" ><img border=0 src=\"<?=$config['SFIABDIRECTORY']?>/images/16/button_cancel.<?=$config['icon_extension']?>\"></a>"+
							"</td></tr>");
			}
			prizelist_refresh();
		});
}

function edit_prize(id)
{
	$.getJSON("<?=$_SERVER['PHP_SELF']?>?action=prize_load&id="+id,  
        function(json){
			$("#prizeinfo_edit_header").html("<?=i18n("Edit Prize")?>");
			$("#prizeinfo_id").val(json.id);
			$("#prizeinfo_prize").val(json.prize);
			$("#prizeinfo_cash").val(json.cash);
			$("#prizeinfo_scholarship").val(json.scholarship);
			$("#prizeinfo_value").val(json.value);
			$("#prizeinfo_number").val(json.number);
			$("#prizeinfo_trophystudentkeeper").val([json.trophystudentkeeper]);
			$("#prizeinfo_trophystudentreturn").val([json.trophystudentreturn]);
			$("#prizeinfo_trophyschoolreturn").val([json.trophyschoolreturn]);
			$("#prizeinfo_trophyschoolkeeper").val([json.trophyschoolkeeper]);
			$("#prizeinfo_excludefromac").val([json.excludefromac]);
			$(".prizeinfo").removeAttr("disabled");
			$("#prizeinfo_save").removeAttr("disabled");
        });
}

function eligibility_save()
{
	$("#eligibility_award_awards_id").val(award_id);
	$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=eligibility_save", $("#eligibility").serializeArray());
	return false;
}

function prize_save()
{
	$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=prize_save", $("#prizeinfo").serializeArray(),
			function(responseText, textStatus, XMLHttpRequest)
			{
				update_prizeinfo();
			});
	return false;
}

function prize_delete(id)
{
	var confirm = confirmClick('Are you sure you want to delete this prize?');
	if(confirm == true) {
		$("#prizeinfo_info").load("<?$_SERVER['PHP_SELF']?>?action=prize_delete&id="+id,null,
			function(responseText, textStatus, XMLHttpRequest)
			{
				$(".prizelist_tr#"+id).fadeTo('slow', 0);
				$(".prizelist_tr#"+id).remove();
				prizelist_refresh();
			});
	}
	return 0;
} 

function prize_create()
{
	$.getJSON("<?=$_SERVER['PHP_SELF']?>?action=prize_create&award_awards_id="+award_id,
	        function(json){
			$(".prizeinfo").val("");
			$("#prizeinfo_id").val(json.id);
			$("#prizeinfo_edit_header").html("<?=i18n("New Prize")?>");
			$(".prizeinfo").removeAttr("disabled");
			$("#prizeinfo_save").removeAttr("disabled");
			update_prizeinfo();
		});
}

function update_feeder()
{
	if(award_tab_update['feeder'] == award_id) return;
	award_tab_update['feeder'] = award_id;

	$("#editor_tab_feeder").load("<?=$_SERVER['PHP_SELF']?>?action=feeder_load&id="+award_id, '',
		function(responseText, textStatus, XMLHttpRequest) {
			/* Register buttons and handlers */
			$("#feeder_enable").change(function() {
					update_feeder_enable();
				});
			$("#feeder_save").click(function() {
				$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=feeder_save", $("#feeder_form").serializeArray());
				return false;
			});

			update_feeder_enable();
		});
}

function update_feeder_enable()
{
	var checked = $('#feeder_enable:checkbox').is(':checked');
	if(checked==true) {
		$('#feeder_en *').removeAttr('disabled');
	} else {
		$("#feeder_en *").attr('disabled', 'disabled');
		$('#feeder_enable').removeAttr('disabled');
	}
}

/* Setup the popup window */
$(document).ready(function() { 
	$("#popup_editor").dialog({
			bgiframe: true, autoOpen: false,
			modal: true, resizable: false,
			draggable: false,
			close: function() {
				var $tabs = $('#editor_tabs').tabs();
				var selected = $tabs.tabs('option', 'selected'); 
				if(award_id == -1 && selected== 0) {
					notice_("<?=i18n('New Award Cancelled')?>");
				}
			}
		});

	$("#editor_tabs").tabs({
			show: function(event, ui) { 
				switch(ui.panel.id) {
				case 'editor_tab_awardinfo':
					update_awardinfo();
					break;			
				case 'editor_tab_eligibility':
					update_eligibility();
					break;			
				case 'editor_tab_prizes':
					update_prizeinfo();
					break;
				case 'editor_tab_feeder':
					update_feeder();
					break;
				}
				return true;
			},
			collapsible: true,
			selected: -1 /* None selected */
		});

}); 

 </script>

<?
 /* Begin popup */
?>
 
<div id="popup_editor" title="Award Editor" style="display: none">
<div id="popup_status"></div>
<div id="editor_tabs">
	<ul><li><a href="#editor_tab_awardinfo"><span><?=i18n('Award Info')?></span></a></li>
	<li><a href="#editor_tab_eligibility"><span><?=i18n('Eligibility')?></span></a></li>
	<li><a href="#editor_tab_prizes"><span><?=i18n('Prizes')?></span></a></li>
	<li><a href="#editor_tab_feeder"><span><?=i18n('Feeder Fairs')?></span></a></li>
</ul>

<div id="editor_tab_awardinfo">
	<h4><?=i18n("Award Info")?></h4>
	<form>
	<?/* We dont' want to ever change this, but we want it's value, so put it in 
	   * a form by itself */?>
	<input type="hidden" name="award_source_fairs_id" id="awardinfo_award_source_fairs_id" value="0" />
	</form>
	<form id="awardinfo">
	<input type="hidden" name="id" id="awardinfo_id" value="0" />
	<table class="editor">
		<tr><td><?=i18n("Name")?>:</td>
			<td><input class="translatable" type="text" id="awardinfo_name" name="name" value="Loading..." size="50" maxlength="128">
			</td></tr>
		<tr><td><?=i18n("Sponsor")?>:</td><td>
<?
	$sq=mysql_query("SELECT id,organization FROM sponsors ORDER BY organization");
	echo "<select id=\"awardinfo_sponsors_id\" name=\"sponsors_id\">";
	echo "<option value=\"\">".i18n("Choose a sponsor")."</option>\n";
	while($sr=mysql_fetch_object($sq)) {
		echo "<option value=\"$sr->id\">".i18n($sr->organization)."</option>";
	}
?>
		</select></td></tr>
		<tr><td><?=i18n("Presenter")?>:</td>
			<td><input type="text" id="awardinfo_presenter" name="presenter" value="Loading..." size="50" maxlength="128" />
			</td></tr>
		<tr><td><?=i18n("Type")?>:</td><td>
<?
	$tq=mysql_query("SELECT id,type FROM award_types WHERE year='{$config['FAIRYEAR']}' ORDER BY type");
	echo "<select id=\"awardinfo_award_types_id\" name=\"award_types_id\">";
	//only show the "choose a type" option if we are adding,if we are editing, then they must have already chosen one.
	echo $firsttype;
	while($tr=mysql_fetch_object($tq)) {
		echo "<option value=\"$tr->id\">".i18n($tr->type)."</option>";
	}
?>
		</select></td></tr>
		<tr><td><label><?=i18n("Criteria")?>:</label></td>
			<td><textarea class="translatable" id="awardinfo_criteria" name="criteria" rows="3" cols="50">Loading...</textarea></td></tr>
		<tr><td><?=i18n("Description")?>:</td>
			<td><textarea class="translatable" id="awardinfo_description" name="description" rows="3" cols="50">Loading...</textarea></td></tr>
	</table>

	<h4>Options</h4>
	<table class="editor">
	<tr>	<td><input type="checkbox" id="awardinfo_excludefromac" name="excludefromac" value="1"></td>
		<td><?=i18n("Exclude this award from the award ceremony script")?></td>
	</tr><tr>
		<td><input type="checkbox" id="awardinfo_cwsfaward" name="cwsfaward" value="1"></td>
		<td><?=i18n("This award identifies the students that will be attending the Canada-Wide Science Fair")?></td>
	</tr><tr>
		<td><input type="checkbox" id="awardinfo_selfnominate" name="self_nominate" value="yes"></td>
		<td><?=i18n("Students can self-nominate for this award (this is usually checked for special awards)")?></td>
	</tr><tr>
		<td><input type="checkbox" id="awardinfo_schedulejudges" name="schedule_judges" value="yes"></td>
		<td><?=i18n("Allow the Automatic Judge Scheduler to assign judges to this award (usually checked)")?></td>
	</tr></table>
	<input type="submit" onClick="awardinfo_save();return false;" value="Save" />
	</form>
</div>

<? /* Next Tab */ ?>
<div id="editor_tab_eligibility">
	<h4><?=i18n("Eligibility")?></h4>
	<br />
	<form id="eligibility">
	<input type="hidden" id="eligibility_award_awards_id" name="award_awards_id" value="" />
	<table class="editor">
	<tr><td><?=i18n("Age Categories")?>:</td><td>
<?
//	if(count($currentcategories)==0) $class="class=\"error\""; else $class="";

	//now select all the categories so we can list them all
	$cq=mysql_query("SELECT * FROM projectcategories WHERE year='{$config['FAIRYEAR']}' ORDER BY mingrade");
	echo mysql_error();
	while($cr=mysql_fetch_object($cq)) {
		echo "<input type=\"checkbox\" id=\"eligibility_categories_{$cr->id}\" name=\"categories[]\" value=\"$cr->id\" />".i18n($cr->category)."<br />";
	}
?>
	</td></tr>

	<tr><td><?=i18n("Divisions")?>:</td><td>
<?
	$dq=mysql_query("SELECT * FROM projectdivisions WHERE year='{$config['FAIRYEAR']}' ORDER BY division");
	echo mysql_error();
	while($dr=mysql_fetch_object($dq)) {
		echo "<input type=\"checkbox\" id=\"eligibility_divisions_{$dr->id}\" name=\"divisions[]\" value=\"$dr->id\" />".i18n($dr->division)."<br />";
	}
//	if(count($currentcategories)==0 || count($currentdivisions)==0)
//		echo "<tr><td colspan=\"2\" class=\"error\">".i18n("At least one age category and one division must be selected")."</td></tr>";

?>
	</td></tr></table>
	<input type="submit" onClick="eligibility_save();return false;" value="Save" />
	</form>
</div>

<?  /* Next Tab */ ?>

<div id="editor_tab_prizes">
	<h4><?=i18n("Prizes")?></h4>
	<br />
	<table id="prizelist" class="tableview"> 
	<tr class="nodrop nodrag">
		<th style="width:4em"><?=i18n("Script Order")?></th>
		<th><?=i18n("# Available")?></th>
		<th style="width: 15em"><?=i18n("Prize Description")?></th>
		<th><?=i18n("Cash")?></th>
		<th><?=i18n("Scholarship")?></th>
		<th><?=i18n("Value")?></th>
		<th><?=i18n("Actions")?></th>
	</tr></table>
	<br >
	* <?=i18n("Click on the Script Order and drag to re-order the prizes")?>
	<br >
	<hr>

	<br /><h4 id="prizeinfo_edit_header">Click on a prize to edit</h4><br />
	<form id="prizeinfo">
	<input type="hidden" id="prizeinfo_id" name="id" value=""/>
	<input type="hidden" id="prizeinfo_award_awards_id" name="award_awards_id" value=""/>
	<table class="editor">
	<tr>
		<td><?=i18n("Number available")?>:</td>
		<td><input type="text" id="prizeinfo_number" class="prizeinfo" name="number" value="" size="3" maxlength="5" disabled="disabled" /></td>
	</tr><tr>
		<td><?=i18n("Prize Description")?>:</td>
		<td><input type="text" id="prizeinfo_prize" class="prizeinfo translatable" name="prize" value="" size="40" maxlength="128" disabled="disabled"/></td>
	</tr><tr>
		<td><?=i18n("Cash Amount")?> ($):</td>
		<td><input type="text" id="prizeinfo_cash" class="prizeinfo" name="cash" value="" size="10" maxlength="10"  disabled="disabled" /></td>
	</tr><tr>
		<td><?=i18n("Scholarship Amount")?> ($):</td>
		<td><input type="text" id="prizeinfo_scholarship" class="prizeinfo" name="scholarship" value="" size="10" maxlength="10"  disabled="disabled" /></td>
	</tr><tr>
		<td><?=i18n("Prize Value")?> ($):</td>
		<td><input type="text" id="prizeinfo_value" class="prizeinfo" name="value" value="" size="10" maxlength="10"  disabled="disabled" /></td>
	</tr><tr>
		<td><?=i18n("Plaque/Trophy")?>:</td>
		<td>
		<table>
		<tr>	<td style="width:5%;"><input type="checkbox" id="prizeinfo_trophystudentkeeper" class="prizeinfo" name="trophystudentkeeper" value="1"  disabled="disabled"></td>
			<td style="width:45%"><?=i18n("Student(s) keeper trophy")?></td>
			<td style="width:5%;"><input type="checkbox" id="prizeinfo_trophystudentreturn" class="prizeinfo" name="trophystudentreturn" value="1"  disabled="disabled"></td>
			<td style="width:45%"><?=i18n("Student(s) annual return/reuse trophy")?></td>
		</tr><tr>
			<td style="width:5%;"><input type="checkbox" id="prizeinfo_trophyschoolkeeper" class="prizeinfo" name="trophyschoolkeeper" value="1"  disabled="disabled"></td>
			<td style="width:45%;"><?=i18n("School keeper trophy")?></td>
			<td style="width:5%;"><input type="checkbox" id="prizeinfo_trophyschoolreturn" class="prizeinfo" name="trophyschoolreturn" value="1"  disabled="disabled"></td>
			<td style="width:45%;"><?=i18n("School annual return/reuse trophy")?></td>
		</tr></table></td>
	</tr><tr>
		<td><?=i18n("Awards Ceremony")?>:</td>
		<td><input type="checkbox" id="prizeinfo_excludefromac" class="prizeinfo" name="excludefromac" value="1"  disabled="disabled"><?=i18n("Exclude this prize from the award ceremony script")?></td>
	</tr>
	</table>
	</form>
	<br />
	<form>
	<input type="submit" onClick="prize_create();return false;" value="<?=i18n("Create New Prize")?>" />
	<input type="submit" id="prizeinfo_save" onClick="prize_save();return false;" value="<?=i18n("Save Prize")?>" disabled="disabled" />
	</form>
</div>

<? /* Next Tab */ ?>
<div id="editor_tab_feeder"></div>

<? /* End tabs, end popup */ ?>
</div></div>

<?
/* Here's all the code for the award list, except for the AJAX queries which are
 * at the top of this file */
?>

<script type="text/javascript">

function popup_editor(id, mode)
{
	var w = (document.documentElement.clientWidth * 0.8);
	var h = (document.documentElement.clientHeight * 0.8);

	award_id = id;
	/* We don't really need this, but we'll force all the tabs to reload on
	 * activation anyway */
	award_tab_update = new Array();

	/* Force no tabs to be selected, need to set collapsible 
	 * to true first */
	$('#editor_tabs').tabs('option', 'collapsible', true);
	$('#editor_tabs').tabs('option', 'selected', -1);

	/* Then we'll select a tab to force a reload */
	switch(mode) {
	case 'new':
		$('#editor_tabs').tabs('option', 'disabled', [1, 2, 3]);
		$('#editor_tabs').tabs('select', 0);
		break;
	case 'template':
		$('#editor_tabs').tabs('option', 'disabled', [0, 1, 3]);
		$('#editor_tabs').tabs('select', 2);
		break;
	default:
		$('#editor_tabs').tabs('option', 'disabled', []);
		$('#editor_tabs').tabs('select', 0);
		break;
	}
	/* Don't let anything collapse */
	$('#editor_tabs').tabs('option', 'collapsible', false);

	/* Force an awardinfo update, there's some info in there that we want now */
	update_awardinfo();

	/* Show the dialog */
	$('#popup_editor').dialog('option', 'width', w);
	$('#popup_editor').dialog('option', 'height', h);
	$("#popup_editor").dialog('open');



	return true;
}

function awardlist_refresh()
{
	$("#awardlist").tableDnD({
		        onDrop: function(table, row) {
					var order = $.tableDnD.serialize();
//					$(row).fadeTo('fast',1);
					$("#award_info").load("<?=$_SERVER['PHP_SELF']?>?action=award_order&"+order); 

					/* Change the order */
					var rows = table.tBodies[0].rows;
					for (var i=0; i<rows.length; i++) {
						$("#awardlist_order_"+rows[i].id).html(i);
					}
			       	},
			onDragStart: function(table, row) {
//					$(row).fadeTo('fast',0.2);
				},
			dragHandle: "drag_handle"
		});
}

function award_delete(id)
{
	var conf = confirmClick('<?=i18n("Are you sure you want to remove this award?")?>');
	if(conf == true) {
		$("#info_info").load("<?$_SERVER['PHP_SELF']?>?action=award_delete&id="+id);
		/* The TRs need to have just a numeric ID, which could conflict with other lists, so
		 * tag each TR with a class too, and select both the class and the ID */
		$(".awardlist_tr#"+id).fadeTo('slow', 0);
		$(".awardlist_tr#"+id).remove();
		/* Rows changed, need to refresh the drag list */
		awardlist_refresh();
	}

}

$(document).ready(function() { 
	awardlist_refresh();
});

</script>


 <div id="info_info"></div>

<?


 
 /* List filtering */
 if($_GET['sponsors_id'] && $_GET['sponsors_id']!="all")
 	$_SESSION['sponsors_id']=$_GET['sponsors_id'];
 else if($_GET['sponsors_id']=="all")
	unset($_SESSION['sponsors_id']);

 if($_GET['award_types_id'] && $_GET['award_types_id']!="all") 
 	$_SESSION['award_types_id']=$_GET['award_types_id'];
 else if($_GET['award_types_id']=="all")
	 unset($_SESSION['award_types_id']);

/*
 if($_GET['award_sponsors_confirmed'] && $_GET['award_sponsors_confirmed']!="all") 
 	$_SESSION['award_sponsors_confirmed']=$_GET['award_sponsors_confirmed'];

 if($_GET['sponsors_id']=="all")
 	unset($_SESSION['sponsors_id']);
 if($_GET['award_types_id']=="all")
 	unset($_SESSION['award_types_id']);
 if($_GET['award_sponsors_confirmed']=="all")
 	unset($_SESSION['award_sponsors_confirmed']);
*/

 $award_types_id=$_SESSION['award_types_id'];
 $sponsors_id=$_SESSION['sponsors_id'];
 //$award_sponsors_confirmed=$_SESSION['award_sponsors_confirmed'];

echo "<br />";
echo i18n("Filter By:");
echo "<form method=\"get\" action=\"award_awards.php\" name=\"filterchange\">";

echo "<table><tr><td colspan=\"2\">";

$q=mysql_query("SELECT id,organization FROM sponsors ORDER BY organization");
echo "<select name=\"sponsors_id\" onchange=\"document.forms.filterchange.submit()\">";
echo "<option value=\"all\">".i18n("All Sponsors")."</option>";
while($r=mysql_fetch_object($q)) {
	if($r->id == $sponsors_id) {
		$sel="selected=\"selected\"";
		$sponsors_organization=$r->organization;
	} else
		$sel="";
	echo "<option $sel value=\"$r->id\">".i18n($r->organization)."</option>";
}
echo "</select>";
echo "</td></tr>";
echo "<tr><td>";

$q=mysql_query("SELECT id,type FROM award_types WHERE year='{$config['FAIRYEAR']}' ORDER BY type");
echo "<select name=\"award_types_id\" onchange=\"document.forms.filterchange.submit()\">";
echo "<option value=\"all\">".i18n("All Award Types")."</option>";
while($r=mysql_fetch_object($q)) {
	if($r->id == $award_types_id) {
		$sel="selected=\"selected\"";
		$award_types_type=$r->type;
	} else
		$sel="";
	echo "<option $sel value=\"$r->id\">".i18n($r->type)."</option>";
}
echo "</select>";
echo "</td><td>";

/*
//FIXME: 'confirmed' no longer exists, we need to lookup their sponsorship record and check the status there, either pending, confirmed or received, dunno if it makes sense to put that here or not..

echo "<select name=\"award_sponsors_confirmed\" onchange=\"document.forms.filterchange.submit()\">";
if($award_sponsors_confirmed=="all") $sel="selected=\"selected\""; else $sel="";
echo "<option value=\"all\">".i18n("Any Status")."</option>";
if($award_sponsors_confirmed=="yes") $sel="selected=\"selected\""; else $sel="";
echo "<option $sel value=\"yes\">".i18n("Confirmed Only")."</option>";
if($award_sponsors_confirmed=="no") $sel="selected=\"selected\""; else $sel="";
echo "<option $sel value=\"no\">".i18n("Unconfirmed Only")."</option>";
echo "</select>";
*/
echo "</form>";
echo "</td></tr>";
echo "</table>";

?>
<br />
<form>
<input type="button" onClick="popup_editor(-1, 'new');" value="<?=i18n("Create New Award")?>" />
<input type="button" onClick="popup_editor(-1, 'template');" value="<?=i18n("Edit Generic Prize Template")?>" />
</form>
<br /><br />

<?
/* For some reason, this submit button opens the dialog then it closes right away, but it doesn't
 * if the entry is done through the a href */
//<input type="submit" onClick="award_create();" value="<?=i18n("Create New Award")>" />

if($sponsors_id) $where_asi="AND sponsors_id='$sponsors_id'";
if($award_types_id) $where_ati="AND award_types_id='$award_types_id'";
//			if($award_sponsors_confirmed) $where_asc="AND award_sponsors.confirmed='$award_sponsors_confirmed'";

if(!$orderby) $orderby="order";

$q=mysql_query("SELECT 
			award_awards.id,
			award_awards.name,
			award_awards.order,
			award_awards.award_source_fairs_id,
			award_types.type,
			sponsors.organization
		FROM 
			award_awards
		LEFT JOIN sponsors ON sponsors.id = award_awards.sponsors_id
		LEFT JOIN award_types ON award_types.id = award_awards.award_types_id
		WHERE 
				award_awards.year='{$config['FAIRYEAR']}'
				$where_asi
				$where_ati
			AND 	award_types.year='{$config['FAIRYEAR']}'
		ORDER BY `$orderby`");

echo mysql_error();

if(mysql_num_rows($q))
		{
	echo "* ".i18n("Click on the Script Order and drag to re-order the awards");
	echo "<table id=\"awardlist\" class=\"tableview\" >";
	echo "<tr class=\"nodrop nodrag\">";
	echo " <th>".i18n("Order")."</th>";
	echo " <th>".i18n("Sponsor")."</th>";
		echo " <th>".i18n("Type")."</th>";
		echo " <th>".i18n("Name")."</th>";
		echo " <th>".i18n("Prizes")."</th>";
		echo " <th>".i18n("Actions")."</th>";
		echo "</tr>\n";


		$hasexternal=false;
		while($r=mysql_fetch_object($q)) {
			if($r->award_source_fairs_id) {
				$cl="externalaward";
				$hasexternal=true;
			}
			else $cl="";
			$eh = "style=\"cursor:pointer;\" onclick=\"popup_editor({$r->id},'');\"";
			echo "<tr class=\"$cl awardlist_tr\" id=\"{$r->id}\" >\n";
			echo " <td id=\"awardlist_order_{$r->id}\" class=\"drag_handle\" style=\"cursor:move; text-align:right;\">{$r->order}</td>\n";
			echo " <td $eh>{$r->organization}</td>\n";
			echo " <td $eh>{$r->type}</td>\n";
			echo " <td $eh>{$r->name}</td>\n";

			$numq=mysql_query("SELECT SUM(number) AS num FROM award_prizes WHERE award_awards_id='{$r->id}'");
			$numr=mysql_fetch_assoc($numq);
			if(!$numr['num'])
				$numr['num']=0;

			echo " <td $eh align=\"center\">{$numr['num']}</td>";

			echo " <td align=\"center\">";
//			echo "<img border=\"0\" src=\"{$config['SFIABDIRECTORY']}/images/16/edit.{$config['icon_extension']}\">";
//			echo "&nbsp;";
			echo "<a onclick=\"award_delete({$r->id});\" href=\"#\" ><img border=0 src=\"{$config['SFIABDIRECTORY']}/images/16/button_cancel.{$config['icon_extension']}\"></a>";

			echo " </td>\n";
			echo "</tr>\n";
		}
		if($hasexternal)
			echo "<tr class=\"externalaward\"><td colspan=\"6\">".i18n("Indicates award imported from an external source")."</td></tr>";
		echo "</table>\n";
		echo "</form>";
	}
	echo "<br />";
//	echo "<a href=\"award_prizes.php?award_awards_id=-1\">Edit prizes for the generic prize template</a>";


	if($_GET['action'] == 'edit_prize_template') {
 	
		?><script type="text/javascript">
        $(document).ready(function() { 
            popup_editor(-1,'template');
        });           
		</script>
		<?
	 }



send_footer();

?>
