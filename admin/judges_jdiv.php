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
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');
 include "judges.inc.php";

 send_header("Judging Division Groupings",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Judges' => 'admin/judges.php')
				);
 echo i18n("Instructions: The goal is to create groupings that have the least number of divisions/categories required to have at least %1 projects in the group.  %1 is the number of projects that a single team can judge that you have specifed in the judge scheduler configuration. Judge division groupings indicate which divisions/categories can be judged together (by the same team of judges), so the divisons/categories should be somewhat similar to ensure there are judges that can handle judging all divisions assigned to a grouping.",array($config['max_projects_per_team']));
?>

<script language="javascript" type="text/javascript">
function addbuttonclicked(jdiv)
{
	document.forms.jdivs.action.value="add";
	document.forms.jdivs.jdiv_id.value=jdiv;
	document.forms.jdivs.submit();
}

function newbuttonclicked(jdivs)
{
	document.forms.jdivs.action.value="new";
	document.forms.jdivs.jdivs.value=jdivs;
	document.forms.jdivs.submit();
}

</script>

<?

	$div = array();
	$divshort = array();
	$q=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
	while($r=mysql_fetch_object($q)) {
		$divshort[$r->id]=$r->division_shortform;
		$div[$r->id]=$r->division;
	}

	$cat = array();
	$q=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
	while($r=mysql_fetch_object($q)) {
		$cat[$r->id]=$r->category;
	}

	$dkeys = array_keys($div);
	$ckeys = array_keys($cat);

	if($config['filterdivisionbycategory']=="yes") {
		$q=mysql_query("SELECT * FROM projectcategoriesdivisions_link WHERE year='".$config['FAIRYEAR']."' ORDER BY projectdivisions_id,projectcategories_id");
		$divcat=array();
		while($r=mysql_fetch_object($q)) {
			$divcat[]=array("c"=>$r->projectcategories_id,"d"=>$r->projectdivisions_id);
		}

	}
	else {
		$divcat=array();
		foreach($dkeys AS $d) {
			foreach($ckeys AS $c) {
				$divcat[]=array("c"=>$c,"d"=>$d);
			}
		}
	}
	
	$langr = array();
	$q=mysql_query("SELECT * FROM languages WHERE active='Y'");
	while($r=mysql_fetch_object($q)) {
		$langr[$r->lang] = $r->langname;
	}


function get_all_divs()
{
	global $config;
	global $divshort, $div,$cat, $langr;
	global $divcat;

	$cdlcheck = array();
	$cdl = array();
	$q=mysql_query("SELECT * FROM judges_jdiv");
	while($r=mysql_fetch_object($q)) {
		$cdl[$r->id]['id'] = $r->id;
		$cdl[$r->id]['jdiv'] = $r->jdiv_id;
		$cdl[$r->id]['div'] = $r->projectdivisions_id;
		$cdl[$r->id]['cat'] = $r->projectcategories_id;
		$cdl[$r->id]['lang'] = $r->lang;

		$cdlcheck[$r->projectcategories_id][$r->projectdivisions_id][$r->lang] = 1;
	}

	/* Check for missing cdls */
	$divkeys = array_keys($divshort);
	$catkeys = array_keys($cat);
	$lankeys = array_keys($langr);

	foreach($divcat AS $dc) {
		$y=$dc['d'];
		$x=$dc['c'];
		foreach($lankeys as $z) {
			if($cdlcheck[$x][$y][$z] == 1)
				continue;

			/* Also, make an entry in the DB, so that this isn't 
			 * unassigned anymore */
			mysql_query("INSERT INTO judges_jdiv (id, jdiv_id, projectdivisions_id, projectcategories_id, lang) ".
					" VALUES('', 0, '$y', '$x', '$z')"); 
			$q = mysql_query("SELECT id FROM judges_jdiv WHERE ".
					" projectdivisions_id='$y' ".
					" AND projectcategories_id='$x' ".
					" AND lang='$z' ");
			$r = mysql_fetch_object($q);

			$cdl[$r->id]['id'] = $r->id;
			$cdl[$r->id]['jdiv'] = 0; /* Unassigned */
			$cdl[$r->id]['cat'] = $x;
			$cdl[$r->id]['div'] = $y;
			$cdl[$r->id]['lang'] = $z;

		}
		reset($lankeys);
	}
	reset($divcat);

	/* Make names for all the DCLs, and count the number of projects */
	$dkeys = array_keys($cdl);
	foreach($dkeys as $id) {
		$x = $cat[$cdl[$id]['cat']];
		$y = $divshort[$cdl[$id]['div']];
		$z = $div[$cdl[$id]['div']];
		$q = mysql_query("SELECT count(projects.id) AS cnt FROM projects,registrations WHERE ".
			" projectdivisions_id='{$cdl[$id]['div']}' ".
			" AND projectcategories_id='{$cdl[$id]['cat']}' ".
			" AND language='{$cdl[$id]['lang']}' ".
			" AND registrations.year='{$config['FAIRYEAR']}'".
			" AND projects.registrations_id=registrations.id".
			" AND (registrations.status='complete' OR registrations.status='paymentpending')");

		$r = mysql_fetch_object($q);
		echo mysql_error();
		$c = $r->cnt;

		$cdl[$id]['name'] = "$x $y ({$cdl[$id]['lang']}) ($c project".($c==1?'':'s').")";
		$cdl[$id]['lname'] = "$x $z ({$cdl[$id]['lang']}) ($c project".($c==1?'':'s').")";
		$cdl[$id]['projects'] = $c;
	}
	return $cdl;
}

	if($_POST['action']=="add" && $_POST['jdiv_id'] && count($_POST['cdllist'])>0)
	{
		foreach($_POST['cdllist'] AS $selectedcdl) {
			$q=mysql_query("UPDATE judges_jdiv SET jdiv_id='{$_POST['jdiv_id']}' WHERE ".
						" id='$selectedcdl' ");
		}
		echo happy(i18n("Judging Division(s) successfully added"));
	}

	if($_GET['action']=="del" && $_GET['cdl_id']) {
		mysql_query("UPDATE judges_jdiv SET jdiv_id=0 WHERE id='{$_GET['cdl_id']}'");
	}

	if($_GET['action']=="empty" && $_GET['jdiv_id']) {
		mysql_query("UPDATE judges_jdiv SET jdiv_id=0 WHERE jdiv_id='{$_GET['jdiv_id']}' ");
		echo happy(i18n("Emptied all divisions from Judging Division Group %1",array($_GET['jdiv_id'])));
	}

	if($_GET['action']=="recreate") {
		//just delete them all, they'll be recreated automagically
		mysql_query("TRUNCATE TABLE judges_jdiv");
		echo happy(i18n("Recreated all division/category/language options"));
	}


	/* Sort out all the judging divisions */
	$cdl = get_all_divs();

	$dkeys = array_keys($cdl);

	/* Count the divisions, or, use the posted variable so we can create new 
	 * and empty judging divisions */
	if($_POST['jdivs'] > 0) { 
		$jdivs = $_POST['jdivs'];
	} else {
		$jdivs = 0;
		foreach($dkeys as $d) {
			if($cdl[$d]['jdiv'] > $jdivs) $jdivs = $cdl[$d]['jdiv'];
		}
	}
	
	reset($dkeys);
	$showdivlist=false;
	foreach($dkeys as $id) {
		if($cdl[$id]['jdiv'] == 0){ $showdivlist=true; break; }
	}


	echo "<form name=\"jdivs\" method=\"post\" action=\"judges_jdiv.php\">";
	echo "<input type=\"hidden\" name=\"action\">";
	echo "<input type=\"hidden\" name=\"jdivs\" value=\"$jdivs\">";
	echo "<input type=\"hidden\" name=\"jdiv_id\">";
	echo "<input type=\"hidden\" name=\"judges_id\">";
	echo "<table width=\"100%\">";
	echo "<tr>";

	if($showdivlist) {
		echo "<th width=\"25%\">".i18n("Division List");
		echo "<br />";
		echo "</th>";
	}
	echo "<th>".i18n("Judging Division Groups")."</th>";
	echo "</tr>";
	echo "<tr>";

	if($showdivlist) {
		echo "<td valign=\"top\">";
		echo "<select name=\"cdllist[]\" multiple=\"multiple\" style=\"width: 300px; height: 600px;\">";
		/* Print the list of all unassigned divs */
		reset($dkeys);
		foreach($dkeys as $id) {
			if($cdl[$id]['jdiv'] != 0) continue;
			echo "<option value=\"$id\">{$cdl[$id]['name']}</option>\n";
		}
		echo "</select>";
		echo "</td>";
	}
	echo "<td valign=\"top\">";

	/* Print he groupings of the assigned ones */
	for($jdiv = 1; $jdiv <= $jdivs; $jdiv++) {
		echo "<hr>";

		echo "<table width=\"100%\">";
		echo "<tr><td valign=top width=\"80\">";
		if($showdivlist) {
			echo "<input onclick=\"addbuttonclicked('$jdiv')\" type=\"button\" value=\"Add &gt;&gt;\"><br />";
		}
		echo "<br />";
		echo "<a onclick=\"return confirmClick('Are you sure you want to empty all the divisions from this grouping?')\" href=\"judges_jdiv.php?action=empty&jdiv_id=$jdiv \">";
		echo "<img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\">";		
		echo " ".i18n("Empty")." ";
		echo "</a>";


		echo "</td><td>";

		$p = 0;
		reset($dkeys);
		foreach($dkeys as $id) {
			if($cdl[$id]['jdiv'] != $jdiv) continue;
			$p += $cdl[$id]['projects'];
		}

		echo "<table class=\"tableedit\" width=\"95%\">\n";
		echo "<tr><th colspan=\"2\" align=\"left\">Judging Division $jdiv ($p project".($p==1?'':'s').")";
		echo "</th></tr>\n";

		$x = 0;
		reset($dkeys);
		foreach($dkeys as $id) {
			if($cdl[$id]['jdiv'] != $jdiv) continue;

			echo "<tr><td>";
			echo "<a href=\"judges_jdiv.php?action=del&cdl_id=$id\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
			echo "</td><td width=\"100%\">";

			echo $cdl[$id]['lname'];
			echo "</td></tr>";
			$x++;

		}

		if($x) {
			echo "<tr><td colspan=\"2\">";
//			echo "<a onclick=\"return confirmClick('Are you sure you want to empty all the divisions from this grouping?')\" href=\"judges_jdiv.php?action=empty&jdiv_id=$jdiv \">";
//			echo " ".i18n("Empty All Divisions")." ";
//			echo "<img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\">";
//			echo "</a>";
			echo "</td></tr>";
		} else {
			echo "<tr><td colspan=\"2\">";
			echo error(i18n("No divisions present"),"inline");
			echo "</td></tr>";
		}

		echo "</table>";

		echo "</td></tr></table>";
	}
	echo "<hr><input onclick=\"newbuttonclicked('".($jdivs+1)."')\" type=\"button\" value=\"New Judging Divsion Group\">";

	echo "<br />";

	echo "</td></tr>";
	echo "</table>";
	echo "</form>";

	echo "<br />";
	echo "<a onclick=\"return confirmClick('".i18n("Are you sure you want to empty all groupings and re-create the options")."')\" href=\"judges_jdiv.php?action=recreate\">".i18n("Re-create all division/category/language options")."</a>.  ".i18n("This will completely empty ALL of your groupings and recreate all the possibly division/category/language options.  Do this if for example you end up with a division/category that should not exist (due to the config option to filter divisions by category, or due to changing your divisions/categories alltogether)");
	echo "<br />";
	echo "<br />";

	send_footer();



?>
