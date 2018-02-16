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

 send_header("Web Consent", 
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Participant Registration' => 'admin/registration.php')
				);

 echo "<br />";

 if(is_array($_POST['changed']))
 {
 	$numchanged=0;
 	foreach($_POST['changed'] AS $id=>$val)
	{
		if($val==1)
		{
			$numchanged++;
			$webfirst=$_POST['webfirst'][$id]=="yes"?"yes":"no";
			$weblast=$_POST['weblast'][$id]=="yes"?"yes":"no";
			$webphoto=$_POST['webphoto'][$id]=="yes"?"yes":"no";
			mysql_query("UPDATE students SET 
					webfirst='$webfirst',
					weblast='$weblast',
					webphoto='$webphoto'
					WHERE
					id='$id'");
		}
	}
 	if($numchanged==1)
		echo happy(i18n("1 student record updated"));
	else if($numchanged>1)
		echo happy(i18n("%1 student records updated",array($numchanged)));
	else
		echo error(i18n("No student records where changed"));
}
 ?>
 <script type="text/javascript">
 function changed(id)
 {
 	var o=document.getElementById('changed_'+id);
	o.value=1;
 }
 </script>

 <?

		$sq=mysql_query("SELECT students.firstname,
					students.lastname,
					students.id,
					projects.projectnumber,
					students.webfirst,
					students.weblast,
					students.webphoto
				FROM
					students,
					registrations,
					projects
				WHERE
				 	students.registrations_id=registrations.id
				AND	( registrations.status = 'complete' OR registrations.status='paymentpending' )
				AND	projects.registrations_id=registrations.id
				AND 	registrations.year='".$config['FAIRYEAR']."' 
				AND 	projects.year='".$config['FAIRYEAR']."' 
				AND 	students.year='".$config['FAIRYEAR']."' 
				ORDER BY projectnumber
				");
				echo mysql_error();

	echo "<form method=\"post\" action=\"registration_webconsent.php\">";
	echo "<table class=\"tableview\">";
	echo "<thead><tr>";
	echo " <th>".i18n("Proj #")."</th>";
	echo " <th>".i18n("Student Name")."</th>";
	echo " <th>".i18n("First")."</th>";
	echo " <th>".i18n("Last")."</th>";
	echo " <th>".i18n("Photo")."</th>";
	echo "</tr></thead>";
	while($r=mysql_fetch_object($sq))
	{
		echo "<tr>";
		echo "<td>$r->projectnumber<input id=\"changed_$r->id\" type=\"hidden\" name=\"changed[$r->id]\" value=\"0\"></td>";
		echo "<td>$r->firstname $r->lastname</td>";
		$ch=$r->webfirst=="yes"?"checked=\"checked\"":"";
		echo "<td><input $ch type=\"checkbox\" name=\"webfirst[$r->id]\" value=\"yes\" onchange=\"changed($r->id)\"></td>";
		$ch=$r->weblast=="yes"?"checked=\"checked\"":"";
		echo "<td><input $ch type=\"checkbox\" name=\"weblast[$r->id]\" value=\"yes\" onchange=\"changed($r->id)\"></td>";
		$ch=$r->webphoto=="yes"?"checked=\"checked\"":"";
		echo "<td><input $ch type=\"checkbox\" name=\"webphoto[$r->id]\" value=\"yes\" onchange=\"changed($r->id)\"></td>";
		echo "</tr>";
	}
	echo "</table>";
	echo "<input type=\"submit\" value=\"".i18n("Save Changes")."\">";
	echo "</form>";

 send_footer();
?>
