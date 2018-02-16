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
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');
 require("../register_participants.inc.php");

 if($_GET['year']) $year=$_GET['year'];
 else $year=$config['FAIRYEAR'];

 send_header("Registration Statistics",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Participant Registration' => 'admin/registration.php')
				);

 echo "<br />";
 echo i18n("Choose Status").":";
 echo "<form name=\"statuschangerform\" method=\"get\" action=\"registration_stats.php\">";
 echo "<select name=\"showstatus\" onchange=\"document.forms.statuschangerform.submit()\">";

 $status_str = array();
 $status_str[''] = i18n("Any Status");
 $status_str['complete'] = i18n("Complete");
 //if there is no reg fee, then we dont need to show this status, because nobody will ever be in this status
 if($config['regfee']>0)  {
	 $status_str['paymentpending'] = i18n("Payment Pending");
	 $status_str['completeorpaymentpending'] = i18n("Complete or Payment Pending");
 }
 $status_str['open'] = i18n("Open");
 $status_str['new'] = i18n("New");

 $showstatus = $_GET['showstatus'];

 foreach($status_str as $s=>$str) {
 	$sel = ($showstatus == $s) ? "selected=\"selected\"" : '';
	echo "<option $sel value=\"$s\">$str</option>\n";
 }
 echo "</select>";
 echo "</form>";

$q=mysql_query("SELECT * FROM projectcategories WHERE year='$year' ORDER BY id");
while($r=mysql_fetch_object($q))
	$cats[$r->id]=$r->category;

$q=mysql_query("SELECT * FROM projectdivisions WHERE year='$year' ORDER BY id");
while($r=mysql_fetch_object($q))
	$divs[$r->id]=$r->division;

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
	switch($_GET['sort']) {
		case 'status': 	$ORDERBY="registrations.status DESC, projects.title"; break;
		case 'num': 	$ORDERBY="registrations.num"; break;
		case 'projnum':	$ORDERBY="projects.projectsort, projects.projectnumber"; break;
		case 'title': 	$ORDERBY="projects.title, registrations.status DESC"; break;
		case 'cat': 	$ORDERBY="projects.projectcategories_id, projects.title"; break;
		case 'div': 	$ORDERBY="projects.projectdivisions_id, projects.title"; break;
		default:	$ORDERBY="registrations.status DESC, projects.title"; break;
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
			ORDER BY
				$ORDERBY
			");
		echo mysql_error();
	
	$stats_totalprojects=0;
	$stats_totalstudents=0;
	$stats_divisions=array();
	$stats_categories=array();
	$stats_students_catdiv=array();
	$stats_projects_catdiv=array();
	$stats_students_schools=array();
	$stats_projects_schools=array();
	$schools_names=array();

	while($r=mysql_fetch_object($q))
	{
		$stats_totalprojects++;
		$stats_divisions[$r->projectdivisions_id]++;
		$stats_categories[$r->projectcategories_id]++;
		$stats_projects_catdiv[$r->projectcategories_id][$r->projectdivisions_id]++;

		switch($r->status)
		{
			case "new": $status_text="New"; break;
			case "open": $status_text="Open"; break;
			case "paymentpending": $status_text="Payment Pending"; break;
			case "complete": $status_text="Complete"; break;
		}
		$status_text=i18n($status_text);


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
			$stats_totalstudents++;
			$stats_students_catdiv[$r->projectcategories_id][$r->projectdivisions_id]++;
			$stats_students_schools[$r->projectcategories_id][$studentinfo->schools_id]++;
			$schools_names[$studentinfo->schools_id]=$studentinfo->school." ($studentinfo->board)";
			$lastschoolid=$studentinfo->schools_id;
		}
		//this really isnt right, its only taking the school from the last student in the project to count towards the school's project totals
		//but there's really no other way
		$stats_projects_schools[$r->projectcategories_id][$lastschoolid]++;
	}

	echo "<table style=\"margin-left: 50px;\">";	
	echo "<tr><td colspan=\"2\"><h3>{$status_str[$showstatus]} - ".i18n("Students / projects per age category / division")."</h3></td></tr>";
	echo "<tr><td colspan=\"2\">";
	echo "<table class=\"tableview\" width=\"100%\">";	
	echo "<thead><tr><td width=\"50%\"></td>";
	foreach($cats AS $c=>$cn) {
		echo "<th>$cn<br /><nobr>".i18n("Stud | Proj")."</nobr></th>";
	}
	echo "<th>".i18n("Total")."<br /><nobr>".i18n("Stud | Proj")."</th>";
	echo "</tr></thead>";
	foreach($divs AS $d=>$dn) {
		echo "<tr><td>$dn</td>";
		$tstud=0;
		$tproj=0;
		foreach($cats AS $c=>$cn)
		{
			echo "<td align=\"center\">";
			echo ($stats_students_catdiv[$c][$d]?$stats_students_catdiv[$c][$d]:0);
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo ($stats_projects_catdiv[$c][$d]?$stats_projects_catdiv[$c][$d]:0);
			echo "</td>";
			$tstud+=$stats_students_catdiv[$c][$d];
			$tproj+=$stats_projects_catdiv[$c][$d];

			$tstudcat[$c]+=$stats_students_catdiv[$c][$d];
			$tprojcat[$c]+=$stats_projects_catdiv[$c][$d];
		}
		echo "<td align=\"center\"><b>";
		echo ($tstud?$tstud:0);
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo ($tproj?$tproj:0);
		echo "</b></td>";
		echo "</tr>";
	}
	echo "<tr><td><b>".i18n("Total")."</b></td>";
	$tstud=0;
	$tproj=0;
	foreach($cats AS $c=>$cn) {
		echo "<td align=\"center\"><b>";
		echo ($tstudcat[$c]?$tstudcat[$c]:0);
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo ($tprojcat[$c]?$tprojcat[$c]:0);
		echo "</b></td>";
		$tstud+=$tstudcat[$c];
		$tproj+=$tprojcat[$c];
	}
	echo "<td align=\"center\"><b>";
	echo ($tstud);
	echo "&nbsp;&nbsp;&nbsp;&nbsp;";
	echo ($tproj);
	echo "</b></td>";
	echo "</tr>";

	echo "</table>";
	echo "</td></tr>";

	echo "<tr><td colspan=\"2\"><br /></td></tr>";
	echo "<tr><td colspan=\"2\"><h3>{$status_str[$showstatus]} - ".i18n("Students / projects per age category / school")."</h3></td></tr>";
	echo "<tr><td colspan=\"2\">";
	echo "<table class=\"tableview\" width=\"100%\">";	
	echo "<thead><tr><td width=\"50%\"></td>";
	foreach($cats AS $c=>$cn) {
		echo "<th>$cn<br /><nobr>".i18n("Stud | Proj")."</nobr></th>";
	}
	echo "<th>".i18n("Total")."<br /><nobr>".i18n("Stud | Proj")."</nobr></th>";
	echo "</tr></thead>";

	asort($schools_names);
	foreach($schools_names AS $id=>$sn) 
	{
		echo "<tr><td>$sn</td>";
		$tstud=0;
		$tproj=0;
		foreach($cats AS $c=>$cn) {
			echo "<td align=\"center\">".($stats_students_schools[$c][$id]?$stats_students_schools[$c][$id]:0);
			echo "&nbsp;&nbsp;&nbsp;&nbsp;";
			echo ($stats_projects_schools[$c][$id]?$stats_projects_schools[$c][$id]:0)."</td>";
			$tstud+=$stats_students_schools[$c][$id];
			$tproj+=$stats_projects_schools[$c][$id];
		}
		echo "<td align=\"center\"><b>".($tstud?$tstud:0);
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo ($tproj?$tproj:0)."</b></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo i18n("%1 schools total",array(count($schools_names)));

	echo "</td></tr>";
	echo "</table>";

	echo "<br />";
	echo i18n("Note: statistics reflect the numbers of the current 'Status' selected at the top of the page");
	echo "<br />";
	echo "<br />";


 send_footer();
?>
