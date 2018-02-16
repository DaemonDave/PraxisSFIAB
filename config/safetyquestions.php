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
 user_auth_required('committee', 'config');
 send_header("Safety Questions",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"project_safety_questions"
			);
 if($_POST['action']=="save" && $_POST['save'])
 {
 	if($_POST['question'])
	{
		if(!ereg("^[0-9]*$",$_POST['ord']))
			echo notice(i18n("Defaulting non-numeric order value %1 to 0",array($_POST['ord'])));

		mysql_query("UPDATE safetyquestions SET 
				question='".mysql_escape_string(stripslashes($_POST['question']))."',
				`type`='".mysql_escape_string(stripslashes($_POST['type']))."',
				`required`='".mysql_escape_string(stripslashes($_POST['required']))."',
				ord='".mysql_escape_string(stripslashes($_POST['ord']))."'
				WHERE id='".$_POST['save']."' AND year='".$config['FAIRYEAR']."'");
				echo mysql_error();

		echo happy(i18n("Safety question successfully saved"));
	}
	else
		echo error(i18n("Question is required"));
 }

 if($_POST['action']=="new")
 {
 	if($_POST['question'])
	{
		mysql_query("INSERT INTO safetyquestions (question,type,required,ord,year) VALUES ( 
					'".mysql_escape_string(stripslashes($_POST['question']))."',
					'".mysql_escape_string(stripslashes($_POST['type']))."',
					'".mysql_escape_string(stripslashes($_POST['required']))."',
					'".mysql_escape_string(stripslashes($_POST['ord']))."',
					'".$config['FAIRYEAR']."'
					)");
					echo mysql_error();

		echo happy(i18n("Safety question successfully added"));
	}
	else
		echo error(i18n("Question is required"));
 }

 if($_GET['action']=="remove" && $_GET['remove'])
 {
 	mysql_query("DELETE FROM safetyquestions WHERE id='".$_GET['remove']."' AND year='".$config['FAIRYEAR']."'");
	echo happy(i18n("Safety question successfully removed"));

 }

 if(($_GET['action']=="edit" && $_GET['edit']) || $_GET['action']=="new")
 {
 	$showform=true;
	echo "<form method=\"post\" action=\"safetyquestions.php\">";
 	if($_GET['action']=="new")
	{
		$buttontext="Add safety question";
		echo "<input type=\"hidden\" name=\"action\" value=\"new\">\n";
		$r=null;
	}
	else if($_GET['action']=="edit")
	{
		$buttontext="Save safety question";
		echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
		$q=mysql_query("SELECT * FROM safetyquestions WHERE id='".$_GET['edit']."' AND year='".$config['FAIRYEAR']."'");
		echo "<input type=\"hidden\" name=\"save\" value=\"".$_GET['edit']."\">\n";
		if(!$r=mysql_fetch_object($q))
		{
			$showform=false;
			echo error(i18n("Invalid safety question"));
		}


	}
	if($showform)
	{
		echo "<table class=\"summarytable\">";
		echo "<tr><td>".i18n("Question")."</td><td>";
		echo "<input size=\"60\" type=\"text\" name=\"question\" value=\"".htmlspecialchars($r->question)."\">\n";
		echo "</td></tr>";
		echo "<tr><td>".i18n("Type")."</td><td>";
		echo "<select name=\"type\">";
		if($r->type=="check") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"check\">".i18n("Check box")."</option>\n";
		if($r->type=="yesno") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"yesno\">".i18n("Yes/No")."</option>\n";
		echo "</select>";
		echo "</td>";
		echo "<tr><td>".i18n("Required?")."</td><td>";
		echo "<select name=\"required\">";
		if($r->required=="yes") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"yes\">".i18n("Yes")."</option>\n";
		if($r->required=="no") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"no\">".i18n("No")."</option>\n";
		echo "</select>";
		echo "</td>";
		echo "<tr><td>".i18n("Display Order")."</td><td>";
		echo "<input size=\"5\" type=\"text\" name=\"ord\" value=\"".htmlspecialchars($r->ord)."\">\n";
		echo "</td></tr>";
		echo "<tr><td colspan=\"2\" align=\"center\">";
		echo "<input type=\"submit\" value=\"".i18n($buttontext)."\" />\n";
		echo "</td></tr>";
		echo "</table>";
		echo "</form>";
		echo "<br />";
		echo "<hr />";
	}
	else
	{
	}
 }
 echo "<br />";
 echo "<a href=\"safetyquestions.php?action=new\">".i18n("Add new safety question")."</a>";

 echo "<table class=\"summarytable\">";
 $q=mysql_query("SELECT * FROM safetyquestions WHERE year='".$config['FAIRYEAR']."' ORDER BY ord");
 echo "<tr><th>".i18n("Ord")."</th><th>".i18n("Question")."</th><th>".i18n("Type")."</th><th>".i18n("Required")."</th><th>".i18n("Actions")."</th></tr>";
 while($r=mysql_fetch_object($q))
 {
 	echo "<tr>";
	echo "<td>$r->ord</td>";
	echo "<td>$r->question</td>";
	echo "<td align=\"center\">$r->type</td>";
	echo "<td align=\"center\">$r->required</td>";
	echo "<td align=\"center\">";
	echo "<a title=\"Edit\" href=\"".$_SERVER['PHP_SELF']."?action=edit&amp;edit=$r->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\" border=0></a>";
	echo "&nbsp; &nbsp;";
	echo "<a title=\"Remove\" onClick=\"return confirmClick('".i18n("Are you sure you want to remove this safety question?")."');\" href=\"".$_SERVER['PHP_SELF']."?action=remove&amp;remove=$r->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=0></a>";

	echo "</td>";
	echo "</tr>";


 }
 echo "</table>";

 send_footer();
?>
