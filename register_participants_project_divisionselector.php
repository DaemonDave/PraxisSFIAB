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
 require("common.inc.php");
 include "register_participants.inc.php";
 
 //authenticate based on email address and registration number from the SESSION
 if(!$_SESSION['email'])
 {
 	header("Location: register_participants.php");
	exit;
 }
 if(!$_SESSION['registration_number'])
 {
 	header("Location: register_participants.php");
	exit;
 }

 $q=mysql_query("SELECT registrations.id AS regid, students.id AS studentid, students.firstname FROM registrations,students ".
 	"WHERE students.email='".$_SESSION['email']."' ".
	"AND registrations.num='".$_SESSION['registration_number']."' ". 
	"AND registrations.id='".$_SESSION['registration_id']."' ".
	"AND students.registrations_id=registrations.id ".
	"AND registrations.year=".$config['FAIRYEAR']." ".
	"AND students.year=".$config['FAIRYEAR']);
echo mysql_error();

 if(mysql_num_rows($q)==0)
 {
 	header("Location: register_participants.php");
	exit;
 
 }
 $authinfo=mysql_fetch_object($q);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head><title><?=i18n("Division Selector")?></title>
<link rel="stylesheet" href="<?=$config['SFIABDIRECTORY']?>/sfiab.css" type="text/css" />
</head>
<body>
<?
 echo "<div id=\"emptypopup\">";

 if($_GET['division'])
 {
 	//FIXME: this only works when the division form uses ID's in order or their index AND the ID's are sequential starting from 1
 	?>
		<script language="javascript" type="text/javascript">
			opener.document.forms.projectform.projectdivisions_id.selectedIndex=<?=$_GET['division']?>
		</script>
	<?
 	$q=mysql_query("SELECT * FROM projectdivisions WHERE id='".$_GET['division']."'");
	$r=mysql_fetch_object($q);
	echo "<h2>".i18n($r->division)."</h2>\n";
	echo "<a href=\"".$_SERVER['PHP_SELF']."\">".i18n("Restart division selector")."</a>";
	echo "<br />";
	echo "<br />";
	echo "<a href=\"javascript: window.close();\">".i18n("Close window")."</a>\n";

 }
 else
 {
	 if(!$_GET['id'])
		$id=1;
	 else
		$id=$_GET['id'];
	 $q=mysql_query("SELECT * FROM projectdivisionsselector WHERE id='$id'");
	 $r=mysql_fetch_object($q);
	 echo i18n($r->question);
	 echo "<br />";
	 echo "<br />";
	 echo "<table align=\"center\">";
	 echo "<tr><td>";
	 echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\">\n";
	 if($r->no_type=="question")
		echo "<input type=\"hidden\" name=\"id\" value=\"$r->no\">\n";
	 if($r->no_type=="division")
		echo "<input type=\"hidden\" name=\"division\" value=\"$r->no\">\n";
	 echo "<input style=\"width: 100px;\" type=\"submit\" value=\"".i18n("No")."\">";
	 echo "</form>\n";
	 echo "</td><td width=\"50\">";
	 echo "&nbsp;</td><td>";
	 echo "<form method=\"get\" action=\"".$_SERVER['PHP_SELF']."\">\n";
	 if($r->yes_type=="question")
		echo "<input type=\"hidden\" name=\"id\" value=\"$r->yes\">\n";
	 if($r->yes_type=="division")
		echo "<input type=\"hidden\" name=\"division\" value=\"$r->yes\">\n";
	 echo "<input style=\"width: 100px;\" type=\"submit\" value=\"".i18n("Yes")."\">";
	 echo "</form>\n";
	 echo "</td></tr></table>";

 }
 echo "</div>";

?>
</body>
</html>
