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
require_once('../projects.inc.php');
require_once('../fair_additional_materials.inc.php');

$auth_type = user_auth_required(array('fair','committee'), 'admin');

$award_awards_id = intval($_GET['award_awards_id']);
$action = $_GET['action'];

/* Load fairs */
$fairs = array();
$q = mysql_query("SELECT * FROM fairs WHERE type='feeder' ORDER BY name");
while(($f = mysql_fetch_assoc($q))) {
	$fairs[$f['id']] = $f;
}


switch($action) {
case 'addwinner':
	$prize_id = intval($_GET['prize_id']);
	$projects_id = intval($_GET['projects_id']);

	if(!$prize_id || !$projects_id) {
		error_("Illegal Assignment");
		exit;
	}
	
	//first check how many we are allowed to have
	$q=mysql_query("SELECT number FROM award_prizes WHERE id='$prize_id'");
	echo mysql_error();
	$r=mysql_fetch_assoc($q);
	$number=$r['number'];

	/* Get the award info */
	$q = mysql_query("SELECT * FROM award_awards WHERE id='$award_awards_id'");
	echo mysql_error();
	$a=mysql_fetch_assoc($q);

	/* Get the project */
	$q = mysql_query("SELECT fairs_id FROM projects WHERE id='$projects_id'");
	echo mysql_error();
	$p=mysql_fetch_assoc($q);
	$fairs_id = $p['fairs_id'];

	/* Quick sanity check don't let a fair user do an assignment for someone not
	 * in their fair */
	if($auth_type == 'fair' && $fairs_id != $_SESSION['fairs_id']) {
		error_("Illegal Assignemnt");
		exit;
	}

	if($a['per_fair'] == 'yes') {
		/* Count is the number of this fair already assigned */
		$q=mysql_query("SELECT COUNT(*) AS count FROM winners 
						LEFT JOIN projects ON winners.projects_id=projects.id
					WHERE 
						projects.fairs_id='$fairs_id'
						awards_prizes_id='$prize_id'");
		echo mysql_error();
		$r=mysql_fetch_assoc($q);
		$count=$r['count'];
	} else {
		/* Count is the total number assigned */
		$q=mysql_query("SELECT COUNT(*) AS count FROM winners WHERE awards_prizes_id='$prize_id'");
		echo mysql_error();
		$r=mysql_fetch_assoc($q);
		$count=$r['count'];
	}

	if($count<$number) {
		mysql_query("INSERT INTO winners (awards_prizes_id,projects_id,year) VALUES ('$prize_id','$projects_id','{$config['FAIRYEAR']}')");
		happy_("Winning project added");
	} else {
		error_("This prize cannot accept any more winners.  Maximum: %1",$number);
	}

	exit;

case 'deletewinner':
	$prize_id = intval($_GET['prize_id']);
	$projects_id = intval($_GET['projects_id']);

	if($prize_id && $projects_id) {
		mysql_query("DELETE FROM winners WHERE awards_prizes_id='$prize_id' AND projects_id='$projects_id'");
		happy_("Winning project removed");
	}
	exit;
case 'award_load':
	$fairs_id = intval($_GET['fairs_id']);
	/* Load the award */
	$q=mysql_query("SELECT 
				award_awards.id,
				award_awards.name,
				award_awards.criteria,
				award_awards.order AS awards_order,
				award_awards.per_fair,
				award_awards.external_additional_materials,
				award_awards.award_source_fairs_id,
				award_types.type
			FROM 
				award_awards ,
				award_types
			WHERE 
					award_awards.year='{$config['FAIRYEAR']}'
				AND	award_awards.award_types_id=award_types.id
				AND 	award_types.year=award_awards.year
				AND	award_awards.id='$award_awards_id'
			");

	echo mysql_error();

	if(mysql_num_rows($q) != 1) {
		echo i18n("Invalid award to load $award_awards_id");
		exit;
	}
	$r=mysql_fetch_assoc($q);
	print_award($r, $fairs_id);
	exit;

case 'edit_load':
	$fairs_id = intval($_GET['fairs_id']);
	
	/* Force the fair user to only edit their fair */
//	if($auth_type == 'fair') $fairs_id = $_SESSION['fairs_id'];

	/* Load the award */
	$q=mysql_query("SELECT 
				award_awards.id,
				award_awards.name,
				award_awards.criteria,
				award_awards.order AS awards_order,
				award_awards.per_fair,
				award_awards.external_additional_materials,
				award_awards.award_source_fairs_id,
				award_types.type
			FROM 
				award_awards ,
				award_types
			WHERE 
					award_awards.year='{$config['FAIRYEAR']}'
				AND	award_awards.award_types_id=award_types.id
				AND 	award_types.year=award_awards.year
				AND	award_awards.id='$award_awards_id'
			");

	echo mysql_error();

	if(mysql_num_rows($q) != 1) {
		echo i18n("Invalid award to edit $award_awards_id");
		exit;
	}

	$r=mysql_fetch_assoc($q);

	$editor_data = array();

	/* Load projects */
	if($r['type'] == 'Special') {
		$editor_data['projects_nominated'] = getProjectsNominatedForSpecialAward($r['id']);
		$editor_data['disable_nominated'] = false;
	} else {
		$editor_data['projects_nominated'] = array();
		$editor_data['disable_nominated'] = true;
	}
	$editor_data['projects_eligible'] = getProjectsEligibleForAward($r['id']);

	/* Print the award header */
	echo "<br />";
	if($fairs_id) echo "<h4>".i18n('Winners from').": {$fairs[$fairs_id]['name']}</h4>";
	echo "<b>{$r['type']} - {$r['name']}</b><br />";
	echo "{$r['criteria']}<br />";

	/* Print the award with editor */
	print_award($r, $fairs_id, true, $editor_data);
	exit;


case 'additional_materials':
	$fairs_id = intval($_GET['fairs_id']);
	$q = mysql_query("SELECT * FROM award_awards WHERE id='$award_awards_id'");
	if($fairs_id == 0) {
		echo "Unsupported Action: Can't get additional materials for fairs_id=0.  Edit the project and set it's fair to anything except 'Local/Unspecified'.";
		exit;
	}
	$a = mysql_fetch_assoc($q);
	$q = mysql_query("SELECT * FROM fairs WHERE id='$fairs_id'");
	$fair = mysql_fetch_assoc($q);
	$pdf = fair_additional_materials($fair, $a, $config['FAIRYEAR']);
	foreach($pdf['header'] as $h) header($h);
	echo $pdf['data'];
	exit;

}


if($auth_type == 'fair') {
	send_header("Enter Winning Projects",
		array('Science Fair Main' => 'fair_main.php'),
			    "enter_winning_projects"
		);
} else {
	send_header("Enter Winning Projects",
		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
			    "enter_winning_projects"
		);
}

?>
<div id="winner_editor" title="Edit Winners" style="display: none">
	<div id="winner_content"></div>
</div>

<script language="javascript" type="text/javascript">

var award_awards_id = 0;
var fairs_id = 0;

function update_winner_content()
{
	$("#winner_content").load("<?=$_SERVER['PHP_SELF']?>?action=edit_load&award_awards_id="+award_awards_id+"&fairs_id="+fairs_id,{},
		function(responseText, textStatus, XMLHttpRequest) {
			/* Attach to save button */
			$("#project_save").click(function() {
				var id = award_awards_id;
				$("#debug").load("project_editor.php?action=save&award_awards_id="+id, $("#project_form").serializeArray());
			});
		}
	);
}


function popup_editor(id,fid)
{
	var w = (document.documentElement.clientWidth * 0.9);
	var h = (document.documentElement.clientHeight * 0.9);

	award_awards_id = id;
	fairs_id = fid;

	/* Show the dialog */
	$('#winner_editor').dialog('option', 'width', w);
	$('#winner_editor').dialog('option', 'height', h);
	$("#winner_editor").dialog('open');

	update_winner_content();

	return true;
}

function delete_winner(prize_id, projects_id)
{
	var conf = confirm("<?=i18n('Are you sure you want to remove this project from this prize?')?>");
	if(conf == false) return false;

	$("#debug").load("<?=$_SERVER['PHP_SELF']?>?action=deletewinner&award_awards_id="+award_awards_id+"&prize_id="+prize_id+"&projects_id="+projects_id,{},
		function(responseText, textStatus, XMLHttpRequest) {
			update_winner_content();
		}
	);
}

function add_winner(prize_id)
{
	var projects_id;
	if($("#nom_"+prize_id).hasClass('text-link-selected')) {
		projects_id = $("#projects_select_"+prize_id+"_nom").val();
	} else {
		projects_id = $("#projects_select_"+prize_id+"_eli").val();
	}
	$("#debug").load("<?=$_SERVER['PHP_SELF']?>?action=addwinner&award_awards_id="+award_awards_id+"&prize_id="+prize_id+"&projects_id="+projects_id,{},
		function(responseText, textStatus, XMLHttpRequest) {
			update_winner_content();
		}
	);
}

function unlist_winner(projects_id)
{
	$(".projects_select option[value='"+projects_id+"']").remove();
}

function set_nom(prize_id)
{
	/* Don't try anything if this is already selected */
	if($("#nom_"+prize_id).hasClass('text-link-selected')) return false;
	/* If nominated link is disabled, set all-eligible instead */
	if($("#nom_"+prize_id).hasClass('text-link-disabled')) return set_eli(prize_id);

	/* Populate List */
	$("#projects_select_"+prize_id+"_eli").css('display', 'none');
	$("#projects_select_"+prize_id+"_nom").css('display', 'inline');

/* This was brilliant, but we need to filter select boxes by fairs_id in some cases, 
 * so keeping a master copy and duplicating it just didn't work :( 
 * Actually, we can do this now, but the code works the other way, so i'm not going
  * to change it back. :p */
/*	$("#projects_select_"+prize_id).val('');
	$("#projects_select_"+prize_id).html('');
	$("#projects_nominated > option").clone().appendTo("#projects_select_"+prize_id); */

	/* Swap selected styles */
	$("#nom_"+prize_id).addClass('text-link-selected');
	$("#eli_"+prize_id).removeClass('text-link-selected');
}

function set_eli(prize_id)
{
	/* Don't try anything if this is already selected */
	if($("#eli_"+prize_id).hasClass('text-link-selected')) {
		return false;
	}

	/* Populate List */
	$("#projects_select_"+prize_id+"_eli").css('display', 'inline');
	$("#projects_select_"+prize_id+"_nom").css('display', 'none');

	/* Swap selected styles */
	$("#nom_"+prize_id).removeClass('text-link-selected');
	$("#eli_"+prize_id).addClass('text-link-selected');
}


$(document).ready(function() {

	$("#winner_editor").dialog({
		bgiframe: true, autoOpen: false,
		modal: true, resizable: false,
		draggable: false,
		buttons: { 
/*			"<?=i18n('Cancel')?>": function() { 
				$(this).dialog("close"); 
			},
			"<?=i18n('Save')?>": function() { 
				save_report();	
				$(this).dialog("close"); */
			"<?=i18n('Close')?>": function() { 
//				save_report();	
				$(this).dialog("close"); 
			} 
		},
		close: function() {
			/* Reload the row after the dialog close in case the info has changed */
			$("#winner_list_"+award_awards_id+"_"+fairs_id).load("<?=$_SERVER['PHP_SELF']?>?action=award_load&award_awards_id="+award_awards_id+"&fairs_id="+fairs_id);
		}
	});
});



</script>
<?


$fair_join = '';
$fair_where = '';
if($auth_type == 'fair') {
	/* Join to fairs_award_link, and only list awards that are set
	 * as "upload winners" for this fair */
	$fair_join = 'LEFT JOIN fairs_awards_link ON fairs_awards_link.award_awards_id=award_awards.id';
	$fair_where = "AND fairs_awards_link.upload_winners='yes' 
			AND fairs_awards_link.fairs_id='{$_SESSION['fairs_id']}'";
}
$q=mysql_query("SELECT 
			award_awards.id,
			award_awards.name,
			award_awards.order AS awards_order,
			award_awards.per_fair,
			award_awards.external_additional_materials,
			award_awards.award_source_fairs_id,
			award_types.type,
			sponsors.organization
		FROM 
			award_awards $fair_join,
			award_types,
			sponsors
		WHERE 
				award_awards.year='{$config['FAIRYEAR']}'
			AND	award_awards.award_types_id=award_types.id
			AND	award_types.year='{$config['FAIRYEAR']}'
			AND	award_awards.sponsors_id=sponsors.id
			$fair_where
		ORDER BY awards_order");

echo mysql_error();

if(mysql_num_rows($q) == 0) {
	echo i18n('No awards to display.');
	send_footer();
	exit;
}

echo "<br />";
echo i18n("Choose an award to assign winners");
echo "<br />";
echo "<br />";

$fairs_id = ($auth_type == 'fair') ? $_SESSION['fairs_id'] : 0;

while($r=mysql_fetch_assoc($q)) {
	if($r['per_fair'] == 'yes' && $auth_type != 'fair') {
?>		<?=$r['type']?> - <?=$r['name']?>
		<span style="font-size: 0.8em; font-style: italic;">(<?=$r['organization']?>)</span><br />
<?
		foreach($fairs as $fid=>$f) {
?>			<a title="<?=i18n('Edit winners for this award')?>" href="#" onClick="popup_editor(<?=$r['id']?>,<?=$f['id']?>);return false;">
			<?=$f['name']?></a><br />
			<div id="winner_list_<?=$r['id']?>_<?=$fid?>">

<?			print_award($r, $f['id']);
			echo "</div>";
		}
	} else {
?>		<a title="<?=i18n('Edit winners for this award')?>" href="#" onClick="popup_editor(<?=$r['id']?>,<?=$fairs_id?>);return false;">
			<?=$r['type']?> - <?=$r['name']?></a>
		<span style="font-size: 0.8em; font-style: italic;">(<?=$r['organization']?>)</span><br />
		<div id="winner_list_<?=$r['id']?>_<?=$fairs_id?>">
<?		print_award($r, $fairs_id);
		echo "</div>";
	}
	echo '<br />';
}



function print_award(&$r, $fairs_id, $editor=false, $editor_data=array())
{
	global $config, $auth_type;

//	echo "fair=$fairs_id";

	/* Setup the winner filter, we don't want to restrict this 
	 * to a specific fair for the 'fair' user */
	$fairs_where = '';
	if($r['per_fair'] == 'yes') {
		if($fairs_id == 0) {
			echo "blank fairs_id for per-fair award. bug.\n";
			exit;
		}
		/* For per-fair awards, filter the results */
		$fairs_where = "AND projects.fairs_id='$fairs_id'";
	}

	/* Force the 'fair' user to only edit their own fair */
	if($auth_type == 'fair') $fairs_id = $_SESSION['fairs_id'];
	

	/* Load prizes for this award */
	$q=mysql_query("SELECT 
				award_prizes.prize,
				award_prizes.number,
				award_prizes.id,
				award_prizes.cash,
				award_prizes.scholarship
			FROM 
				award_prizes 
			WHERE 
				award_awards_id='{$r['id']}' 
				AND award_prizes.year='{$config['FAIRYEAR']}'
			ORDER BY 
				`order`");
	echo mysql_error();
	
	echo "<table width=\"100%\"><tr><td>";
	$has_winners = false;
	while($pr=mysql_fetch_object($q)) {

		if($editor == true) {
			echo '<br /><hr />';
		}

		echo "&nbsp;&nbsp;<b>{$pr->prize}";
		if($pr->cash || $pr->scholarship) {
			echo " (";	
			if($pr->cash && $pr->scholarship)
				echo "\${$pr->cash} cash / \${$pr->scholarship} scholarship";
			else if($pr->cash)
				echo "\${$pr->cash} cash";
			else if($pr->scholarship)
				echo "\${$pr->scholarship} scholarship";
			echo ")";
		}

		/* Load winners for this prize */
		$cq=mysql_query("SELECT winners.projects_id,
					projects.projectnumber,
					projects.title,
					projects.fairs_id
				FROM 
					winners
					LEFT JOIN projects ON projects.id=winners.projects_id
				WHERE 
					winners.awards_prizes_id='{$pr->id}'
					$fairs_where ");
		echo mysql_error();
		$count = mysql_num_rows($cq);
//		echo "winners=$count";

		/* Print count */ 
		$colour = ($count < $pr->number) ? 'red' : 'green';
		echo " <font color=\"$colour\">[$count/{$pr->number}]</font>";
		echo "</b>";
		echo "<br />";


		/* List current winners for this prize */
		$winners = array();
		while($w = mysql_fetch_assoc($cq)) {
			if($w['projectnumber']) {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;";
				if($editor == true) {
					/* Print the delete X before the project */
					if($auth_type == 'fair' && $w['fairs_id'] != $fairs_id) {
						/* show a blank so everything lines up */
						echo '&nbsp;&nbsp;&nbsp;&nbsp;';
					} else {
						echo "<a onclick=\"delete_winner({$pr->id},{$w['projects_id']});return false;\" href=\"#\" >";
						echo "<img style=\"vertical-align:middle\" border=0 src=\"{$config['SFIABDIRECTORY']}/images/16/button_cancel.{$config['icon_extension']}\"></a>";
						echo '&nbsp;';
					}
					$winners[] = $w['projects_id'];
				}
				$has_winners = true;
				echo "({$w['projectnumber']}) {$w['title']}";
				echo "<br />";
			} else {
			/*	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<font color=\"red\">No winner(s) specified</font>"; */
			}
		}

		/* Unlist all the winners we just printed from ALL lists */
		if($editor == true) {
			echo "<script language=\"javascript\" type=\"text/javascript\">";
			foreach($winners as $w) echo "unlist_winner($w);";
			echo "</script>";
		}

		/* Print the select box if we need it */
		if($editor == true && $count < $pr->number) {
			$n_nom = 0;
			$n_eli = 0;
?>
			<br />
			<form id="winner_<?=$pr->id?>">
			&nbsp;&nbsp;&nbsp;
			<select id="projects_select_<?=$pr->id?>_nom" class="projects_select" style="display:none">
			<option value=""><?=i18n('Choose a project')?></option>
<?			foreach($editor_data['projects_nominated'] as $p) {
				if($fairs_id != 0 && $p['fairs_id']!= $fairs_id) continue;
				echo "<option value=\"{$p['id']}\">({$p['projectnumber']}) {$p['title']}</option>";
				$n_nom++;
			}
?>			</select>
			<select id="projects_select_<?=$pr->id?>_eli" class="projects_select" style="display:none">
			<option value=""><?=i18n('Choose a project')?></option>
<?			foreach($editor_data['projects_eligible'] as $p) {
				if($fairs_id != 0 && $p['fairs_id']!= $fairs_id) continue;
				echo "<option value=\"{$p['id']}\">({$p['projectnumber']}) {$p['title']}</option>";
				$n_eli++;
				print_r($p);
			}
?>			</select>
			<button id="" onClick="add_winner(<?=$pr->id?>); return false;"><?=i18n('Add')?></button>
			<br />&nbsp;&nbsp;&nbsp;
			<?=i18n('List')?>: 
<?
			if($editor_data['disable_nominated'] == true) $n_nom = 'N/A';
			$nom = i18n('All Nominated')." ($n_nom)";
			$el = i18n('All Eligible')." ($n_eli)";

			$cl = ($editor_data['disable_nominated'] == true) ? 'text-link-disabled' : '';
?>
			<span id="nom_<?=$pr->id?>" class="text-link <?=$cl?>" onClick="set_nom(<?=$pr->id?>);return false;"><?=$nom?>
			</span> - <span id="eli_<?=$pr->id?>" class="text-link" onClick="set_eli(<?=$pr->id?>);return false;"><?=$el?></span>
			</form>

			<script language="javascript" type="text/javascript">
<?				/* Start with the nominated list, unless there are none, do
				 * the eligible list by default in that case */
				if($n_nom > 0) 
					echo "set_nom({$pr->id});";
				else
					echo "set_eli({$pr->id});";
?>			</script>
<?
		}
	}
	echo "</td><td align=\"right\">";
	if($r['external_additional_materials'] == 1 && $editor==false && $r['award_source_fairs_id'] == NULL) {
		$d = 'disabled="disabled"';
		$a = '';
		if($has_winners == true) {
			echo "<a href=\"{$_SERVER['PHP_SELF']}?action=additional_materials&award_awards_id={$r['id']}&fairs_id={$fairs_id}\" >";
			$d = '';
			$a = '</a>';
		}
		echo "<button $d title=\"".i18n("Download additional material (currently only a nomination form)")."\">".i18n('Download Additional Materials')."</button>$a<br/>";
	}
	echo "</td></tr></table>";
		
}

send_footer();

?>
