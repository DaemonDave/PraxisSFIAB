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
require_once('common.inc.php');
require_once('user.inc.php');

user_auth_required(array('judge', 'committee'));

$pn = mysql_escape_string(stripslashes($_GET['pn']));

 

$q=mysql_query("SELECT * FROM projects WHERE 
			projectnumber='$pn'
			AND year='{$config['FAIRYEAR']}'");
if(mysql_num_rows($q)==0) {
	echo "not found";
	exit;
}
$pi = mysql_fetch_object($q);



$sq = mysql_query("SELECT firstname,lastname,school FROM students 
		LEFT JOIN schools ON schools.id = students.schools_id
		WHERE
		registrations_id='{$pi->registrations_id}'
		AND students.year='{$config['FAIRYEAR']}'");

$student = array();
while($si = mysql_fetch_object($sq)) {
	$student[] = $si->firstname.' '.$si->lastname;
	$school = $si->school;
}

$students = implode(' and ', $student);

if(file_exists($prependdir."data/logo-100.gif"))
	$logo = "<img align=\"left\" height=\"50\" src=\"".$config['SFIABDIRECTORY']."/data/logo-100.gif\">";
else 
	$logo = "";


?>
<html><head>
<title>Project Summary for <?=$pi->projectnumber?></title>
</head>
<body bgcolor="#FFFFFF">
<P> 
<center>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" col="3">
<TR>
	<td><?=$logo?></td>
	<td><center><p><strong><font size="3" face="Verdana, Arial, Helvetica, sans-serif" color="#6699CC">
		<?=$pi->title?><br />
		<?=$students?><br />
		<?=$school?><br />
		Floor Location : <?=$pi->projectnumber?></font></strong></center></td>
	<td></td>
</tr>
</table>
</center>
<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
<?=nl2br(htmlspecialchars($pi->summary))?>
</font></body></html>

<?

?>
