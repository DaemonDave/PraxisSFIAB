<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005-2006 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005-2006 James Grant <james@lightbox.org>

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

$auth_type = user_auth_required(array('fair','committee'), 'admin');

//require_once('../register_participants.inc.php');

 if($_GET['year']) $year=$_GET['year'];
 else $year=$config['FAIRYEAR'];

$q=mysql_query("SELECT * FROM projectcategories WHERE year='$year' ORDER BY id");
while($r=mysql_fetch_object($q))
	$cats[$r->id]=$r->category;

$q=mysql_query("SELECT * FROM projectdivisions WHERE year='$year' ORDER BY id");
while($r=mysql_fetch_object($q))
	$divs[$r->id]=$r->division;

$action=$_GET['action'];
switch($action) {
case 'load_row':
	$id = intval($_GET['id']);
	$q = list_query($year, '', $id);
	$r = mysql_fetch_object($q);
	print_row($r);
	exit;

case 'delete':
	$regid = intval($_GET['id']);
	$q = mysql_query("SELECT * FROM projects WHERE registrations_id='$regid'");
	if(mysql_num_rows($q)) {
		$p = mysql_fetch_assoc($q);
		mysql_query("DELETE FROM winners WHERE projects_id='{$p['id']}'");
	}
 	mysql_query("DELETE FROM registrations WHERE id='$regid' AND year='".$config['FAIRYEAR']."'");
	mysql_query("DELETE FROM students WHERE registrations_id='$regid' AND year='".$config['FAIRYEAR']."'");
	mysql_query("DELETE FROM projects WHERE registrations_id='$regid' AND year='".$config['FAIRYEAR']."'");
	mysql_query("DELETE FROM safety WHERE registrations_id='$regid' AND year='".$config['FAIRYEAR']."'");
	mysql_query("DELETE FROM questions_answers WHERE registrations_id='$regid' AND year='".$config['FAIRYEAR']."'");
	mysql_query("DELETE FROM mentors WHERE registrations_id='$regid' AND year='".$config['FAIRYEAR']."'");
	mysql_query("DELETE FROM emergencycontact WHERE registrations_id='$regid' AND year='".$config['FAIRYEAR']."'");
	happy_("Registration and all related data successfully deleted");
	exit;
}

if($auth_type == 'committee') {
	 send_header("Registration Management",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Participant Registration' => 'admin/registration.php')
				);
} else {
	 send_header("Student/Project Management",
 		array('Fair Main' => 'fair_main.php') );
}

 ?>

<div id="student_editor" title="Student/Project Editor" style="display: none">
	<div id="editor_tabs" >
		<ul>
			<li><a href="#editor_tab_reg"><span><?=i18n('Registration')?></span></a></li>
			<li><a href="#editor_tab_students"><span><?=i18n('Students')?></span></a></li>
			<li><a href="#editor_tab_project"><span><?=i18n('Project')?></span></a></li>
		</ul>
		<div id="editor_tab_reg">Loading...</div>
		<div id="editor_tab_students">Loading...</div>
		<div id="editor_tab_project">Loading...</div>
	</div>
</div>


<script language="javascript" type="text/javascript">

var registrations_id = 0;
var registrations_new = 0;

function popup_editor(id, open_tab)
{
	var w = (document.documentElement.clientWidth * 0.9);
	var h = (document.documentElement.clientHeight * 0.9);

	registrations_id = id;
	registrations_new = 0;

	if(id == -1) {
		open_tab = 'reg';
		registrations_new = 1;
	}

	/* Force no tabs to be selected, need to set collapsible 
	 * to true first */
	$('#editor_tabs').tabs('option', 'collapsible', true);
	$('#editor_tabs').tabs('option', 'selected', -1);

	/* Then we'll select a tab to force a reload */
	switch(open_tab) {
	case 'reg':
		/* If we open on the reg tab, disable the others until a save */
		$('#editor_tabs').tabs('option', 'disabled', [1,2]);
		$('#editor_tabs').tabs('select', 0);
		break;

	case 'project':
		$('#editor_tabs').tabs('option', 'disabled', []);
		$('#editor_tabs').tabs('select', 2);
		break;
	default:
		$('#editor_tabs').tabs('option', 'disabled', []);
		$('#editor_tabs').tabs('select', 1);
		break;
	}
	/* Don't let anything collapse */
	$('#editor_tabs').tabs('option', 'collapsible', false);

	/* Show the dialog */
	$('#student_editor').dialog('option', 'width', w);
	$('#student_editor').dialog('option', 'height', h);
	$("#student_editor").dialog('open');

	return true;
}

function update_students(numstudents)
{
	var id = registrations_id;

	var req = "action=students_load&id="+id;
	if(numstudents != 0 && numstudents != undefined) req = req+"&numstudents="+numstudents;
		
	$("#editor_tab_students").load("student_editor.php?"+req, '', 
		function(responseText, textStatus, XMLHttpRequest) {
			/* Attach to events we care about */
			$("#students_num").change(function() {
				var num = $("#students_num").val();
				update_students(num);
			});

			$("#students_save").click(function() {
				var id = registrations_id;
				$("#debug").load("student_editor.php?action=students_save&id="+id, $("#students_form").serializeArray());
			});

			$(".students_remove_button").click(function() {		
				var id = registrations_id;
				var sid = $("#"+this.id +"_students_id").val();
				var conf = confirmClick('<?=i18n("Are you sure you want to remove this student from the project?")?>');

				if(conf == false) return false;

				$("#debug").load("student_editor.php?action=student_remove&id="+id+"&students_id="+sid, '',
					function(responseText, textStatus, XMLHttpRequest) {
						update_students();
					});

				return false;
			});
		}
	);
	return false;
}

function update_project() 
{
	var id = registrations_id;
	$("#editor_tab_project").load("project_editor.php?action=project_load&id="+id, '',
		function(responseText, textStatus, XMLHttpRequest) {
			/* Attach to regenerate button */
			$("#project_regenerate_number").click(function() {
				var id = registrations_id;
				/* Call for regen, and when that's done reload the project screen (and rebind everything), 
				 * pass all the form data in, because regen does a save first */
				$("#debug").load("project_editor.php?action=project_regenerate_number&id="+id,$("#project_form").serializeArray(),
					function(responseText, textStatus, XMLHttpRequest) {
						update_project();
					});
			});

			/* Attach to save button */
			$("#project_save").click(function() {
				var id = registrations_id;
				$("#debug").load("project_editor.php?action=project_save&id="+id, $("#project_form").serializeArray());
			});

		}
	);
	return false;
}

function delete_registration(id)
{
	registrations_id=id;
	var conf = confirmClick('<?=i18n("Are you sure you want to completely delete this registration?")?>');
	if(conf == false) return false;

	$("#debug").load("<?=$_SERVER['PHP_SELF']?>?action=delete&id="+id,{},
			function(responseText, textStatus, XMLHttpRequest) {
				var id = registrations_id;
				$("#row_"+id).remove();
			});

}

function update_reg() 
{
	var id = registrations_id;
	$("#editor_tab_reg").load("student_editor.php?action=registration_load&id="+id, '',
		function(responseText, textStatus, XMLHttpRequest) {
			/* Attach to save button */
			$("#registration_save").click(function() {
				var id = registrations_id;
				$('#debug').load("student_editor.php?action=registration_save&id="+id, $("#registration_form").serializeArray());
				/* Enable the other tabs now after a save, FIXME: should be 
				 * after a successful save, but we should use on-the-fly form
				 * validation to disable the save button, so the extra callback/error
				 * check isn't needed */
				$('#editor_tabs').tabs('option', 'disabled', []);

				return false;
			});

		}
	);
	return false;
}



$(document).ready(function() {

	$("#student_editor").dialog({
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
			var id = registrations_id;
			if(registrations_new == true) {
				/* Create a row before loading it */
				$("#registration_list").append("<tr id=\"row_"+id+"\"></tr>");
			}
			$("#row_"+id).load("<?$_SERVER['PHP_SELF']?>?action=load_row&id="+id);
			$("#row_"+id).effect('highlight',{},500);			
		}
	});


	$("#editor_tabs").tabs({
		show: function(event, ui) {
			switch(ui.panel.id) {
			case 'editor_tab_students':
				update_students();
				break;
			case 'editor_tab_project':
				update_project();
				break;
			case 'editor_tab_reg':
				update_reg();
				break;
			default: 
				break;
			}
		},
		selected: -1
	});

	$("#newproject").click(function() {
			popup_editor(-1);
		}
	);
});
</script>


<br />
<table width="100%">
<tr><td>
	<?=i18n("Choose Status")?>:
	<form name="statuschangerform" method="get" action="registration_list.php">
	<select name="showstatus" onchange="document.forms.statuschangerform.submit()">

<?
 //if there is no reg fee, then we dont need to show this status, because nobody will ever be in this status
 $status_str = array('' => 'Any Status', 'complete' =>  'Complete',
		'paymentpending' => ($config['regfee']>0) ? 'Payment Pending' : '',
		'completeorpaymentpending' => ($config['regfee']>0) ? 'Complete or Payment Pending' : '',
		'open' => 'Open', 'new' => 'New');

 $showstatus = $_GET['showstatus'];

 foreach($status_str as $s=>$str) {
	if($str == '') continue;
	$sel = ($showstatus == $s) ? "selected=\"selected\"" : '';
	echo "<option $sel value=\"$s\">".i18n($str)."</option>\n";
 }
?>
	</select></form></td>
	<td align="right"><button id="newproject"><?=i18n("Create New Project")?></button></td>
	</tr></table>
<?

if($showstatus) {
	switch($showstatus) {
		case "complete": $wherestatus="AND status='complete' "; break;
		case "paymentpending": $wherestatus="AND status='paymentpending' "; break;
		case "completeorpaymentpending": $wherestatus="AND (status='complete' OR status='paymentpending') "; break;
		case "open": $wherestatus="AND status='open' "; break;
		case "new": $wherestatus="AND status='new' "; break;
		default: $wherestatus="";
	}
}
else $wherestatus="";

$q = list_query($year, $wherestatus, false);
	
echo "<table id=\"registration_list\" class=\"tableview\">";
echo "<thead><tr>";
if($showstatus) $stat="&showstatus=".$showstatus;
echo  "<th>".i18n("Status")."</th>";
echo  "<th>".i18n("Email Address")."</th>";
echo  "<th>".i18n("Reg Num")."</th>";
echo  "<th>".i18n("Proj Num")."</th>";
echo  "<th>".i18n("Project Title")."</th>";
echo  "<th>".i18n("Age Category")."</th>";
echo  "<th>".i18n("Division")."</th>";
echo  "<th>".i18n("School(s)")."</th>";
echo  "<th>".i18n("Student(s)")."</th>";
echo  "<th>".i18n("Action")."</th>";
echo "</tr></thead>";

while($r=mysql_fetch_object($q)) {
	echo "<tr id=\"row_{$r->reg_id}\">";
	print_row($r);
	echo "</tr>";
}
echo "</table>";


echo "<br/><br/>The statistics have moved here: <a href=\"registration_stats.php\">Registration Statistics</a><br/><br/>";

send_footer();

/* Now some helper fucntions we call more than once */
function list_query($year, $wherestatus, $reg_id)
{
	global $auth_type;

	$reg = '';
	if($reg_id != false)
		$reg = "AND registrations.id='$reg_id'";

	$fair = '';
	if($auth_type == 'fair') {
		$fair = "AND projects.fairs_id='{$_SESSION['fairs_id']}'";
	}

	$q=mysql_query("SELECT  registrations.id AS reg_id,
				registrations.num AS reg_num,
				registrations.status,
				registrations.email,
				projects.title,
				projects.projectnumber,
				projects.projectcategories_id,
				projects.projectdivisions_id
			FROM
				registrations
				left outer join projects on projects.registrations_id=registrations.id
			WHERE
				1
				AND registrations.year='$year' 
				$wherestatus
				$reg $fair
			ORDER BY
				registrations.status DESC, projects.title
			");
	echo mysql_error();
	return $q;
}



function print_row($r)
{
	global $cats, $divs, $config, $year;
	switch($r->status) {
	case "new": $status_text="New"; break;
	case "open": $status_text="Open"; break;
	case "paymentpending": $status_text="Payment Pending"; break;
	case "complete": $status_text="Complete"; break;
	}

	$status_text=i18n($status_text);

	$scl = "style=\"cursor:pointer;\" onclick=\"popup_editor('{$r->reg_id}','');\"";
	$pcl = "style=\"cursor:pointer;\" onclick=\"popup_editor('{$r->reg_id}','project');\"";

	echo "<td $scl>{$status_text}</td>";
	echo "<td $scl>{$r->email}</td>";
	echo "<td $scl>{$r->reg_num}</td>";
	$pn = str_replace(' ', '&nbsp;', $r->projectnumber);
	echo "<td $scl>$pn</td>";
	echo "<td $pcl>{$r->title}</td>";

	echo "<td $scl>".i18n($cats[$r->projectcategories_id])."</td>";
	echo "<td $scl>".i18n($divs[$r->projectdivisions_id])."</td>";

	$sq=mysql_query("SELECT students.firstname,
				students.lastname,
				students.id,
				schools.school,
				schools.board,
				schools.id AS schools_id
			FROM
				students,schools
			WHERE
				students.registrations_id='$r->reg_id'
				AND
				students.schools_id=schools.id
			");
			echo mysql_error();

	$studnum=1;
	$schools="";
	$students="";
	while($studentinfo=mysql_fetch_object($sq))
	{
		$students.="$studentinfo->firstname $studentinfo->lastname<br />";
		$schools.="$studentinfo->school <br />";
	}

	echo "<td $scl>$schools</td>";
	echo "<td $scl>$students</td>";
	echo "<td align=\"center\"  >";
	if($year==$config['FAIRYEAR']) {
		echo "<a title=\"".i18n("Delete this registration")."\" href=\"#\" onClick=\"delete_registration({$r->reg_id});return false\" >";
		echo "<img src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=0>";
		echo "</a>";

			echo "<form target=\"_blank\" method=\"post\" action=\"../register_participants.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"continue\">";
			echo "<input type=\"hidden\" name=\"email\" value=\"$r->email\">";
			echo "<input type=\"hidden\" name=\"regnum\" value=\"$r->reg_num\">";
			echo "<input type=\"submit\" value=\"".i18n("Login")."\">";
			echo "</form>";


	}
	echo "</td>";

}
?>
