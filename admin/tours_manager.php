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
 require("../tours.class.php");
 require("../tableeditor.class.php");



 send_header("Tour Management",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Tours' => 'admin/tours.php')
			);


 if($_GET['action'] == 'renumber') {
 	$q = mysql_query("SELECT id FROM tours WHERE year='{$config['FAIRYEAR']}'");
	$x = 1;
	while($i = mysql_fetch_object($q)) {
		mysql_query("UPDATE tours SET num='$x' WHERE id='{$i->id}'");
		$x++;
	}
	echo happy(i18n('Tours successfully renumbered'));
 }

			
?>
<script language="javascript" type="text/javascript">

function opentoursinfo(id)
{
	if(id)
		currentid=id;
	else
		currentid=document.forms.tours["tourslist[]"].options[document.forms.tours["tourslist[]"].selectedIndex].value;

	window.open("tours_info.php?id="+currentid,"JudgeInfo","location=no,menubar=no,directories=no,toolbar=no,width=770,height=500,scrollbars=yes"); 
	return false;

}
</script>

<?

	$icon_path = $config['SFIABDIRECTORY']."/images/16/";
	$icon_exitension = $config['icon_extension'];


	$editor = new TableEditor('tours');

//	$editor->setDebug(true);
	$editor->filterList("(tours.year={$config['FAIRYEAR']} OR tours.year IS NULL)");

	$editor->execute();

	if($_GET['TableEditorAction'] == '') {

		echo i18n('You can automatically erase all the tour numbers and
		re-number them (starting from 1) by clicking on the link below.
		This will NOT affect any students who have already specified
		their tour preferences.  It will not change which tours
		students have been assigned to (if that has been completed
		too).  It MAY cause the tour numbers to change, so if you have
		already printed reports with the tour numbers on them, they
		will need to be re--printed.');

		echo '<br /><br />'; 
		echo "<a onclick=\"return confirmClick('Are you sure you re-number ALL the tours?')\" href=\"tours_manager.php?action=renumber\">Renumber ALL Tours</a>";
	}

	send_footer();
?>
