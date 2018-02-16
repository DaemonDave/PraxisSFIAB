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


	if($_POST['save']=="edit" || $_POST['save']=="add")
	{
		if($_POST['save']=="add")
		{
			$q=mysql_query("INSERT INTO schools (year) VALUES ('".$config['FAIRYEAR']."')");
			$id=mysql_insert_id();
		}
		else
			$id=intval($_POST['id']);

		$atrisk = $_POST['atrisk'] == 'yes' ? 'yes' : 'no';

/*
			"sciencehead='".mysql_escape_string(stripslashes($_POST['sciencehead']))."', ".
			"scienceheadphone='".mysql_escape_string(stripslashes($_POST['scienceheadphone']))."', ".
			"scienceheademail='".mysql_escape_string(stripslashes($_POST['scienceheademail']))."', ".
			"principal='".mysql_escape_string(stripslashes($_POST['principal']))."', ".
*/

		/* Get the uids for principal/science head */
		$q = mysql_query("SELECT principal_uid,sciencehead_uid FROM schools WHERE id='$id'");
		$i = mysql_fetch_assoc($q);

		$principal_update = '';
		$sciencehead_update = '';
		list($first, $last) = split(' ', $_POST['principal'], 2);
		/* Load existing entry if it exists, else make an entry if
		 * there is data, else, do nothing */
		if($i['principal_uid'] > 0) 
			$pl = user_load_by_uid($i['principal_uid']);
		else if($first != '' && $last != '') {
			$pl = user_create('principal', "*$first$last".user_generate_password());
			$principal_update = "principal_uid='{$pl['uid']}',";
		} else 
			$pl = false;

		$em = $_POST['principalemail'];

		/* If we loaded or created an entry, either
		 * update and save, or purge it */
		if(is_array($pl)) {
			if($first == '' && $last == '') {
				user_purge($pl, 'principal');
				$principal_update = 'principal_uid=NULL,';
			} else {
				$pl['firstname'] = $first;
				$pl['lastname'] = $last;
				$pl['email'] = $em;
				user_save($pl);
			}
		} 


		/* Get info about science head */
		list($first, $last) = split(' ', $_POST['sciencehead'], 2);
		$em = $_POST['scienceheademail'];
		if($em == '' && ($first != '' || $last != '')) $em = "*$first$last".user_generate_password();
		/* Load existing record, or create new if there's something
		 * to insert */
		$sh = false;
		if($i['sciencehead_uid'] > 0) {
			$sh = user_load_by_uid($i['sciencehead_uid']);
			/* It's possile for sh to be false now, happens when the user is
			 * deleted outside the school editor, this condition needs to be
			 * fixed.  If we let it go, the saving the teacher info will
			 * silently fail.  So let's just create a new teacher */

			if(is_array($sh) && ($em != $sh['email'] || $em=='')) {
				/* If the emails don't match we have no way of knowing if we're creating a different
				 * user, or doing a correction, assume it's a different user */
				 user_purge($sh, 'teacher');
				 $sh = false;
			}
		}

		/* If there was no teacher loaded, or if we just purged it, create a new one
		 * if there's an email address */
		if($sh == false && $em != '')  {
			$sh = user_create('teacher', $em);
			$sciencehead_update = "sciencehead_uid='{$sh['uid']}',";
		}

		/* If we have a record update it */
		if(is_array($sh)) {
			$sh['firstname'] = $first;
			$sh['lastname'] = $last;
			$sh['phonework'] = $_POST['scienceheadphone'];
			$sh['email'] = $em; 
			$sh['username'] = $em; 
			user_save($sh);
		}

		$exec="UPDATE schools SET ".
			"school='".mysql_escape_string(stripslashes($_POST['school']))."', ".
			"schoollang='".mysql_escape_string(stripslashes($_POST['schoollang']))."', ".
			"designate='".mysql_escape_string(stripslashes($_POST['schooldesignate']))."', ".
			"schoollevel='".mysql_escape_string(stripslashes($_POST['schoollevel']))."', ".
			"school='".mysql_escape_string(stripslashes($_POST['school']))."', ".
			"board='".mysql_escape_string(stripslashes($_POST['board']))."', ".
			"district='".mysql_escape_string(stripslashes($_POST['district']))."', ".
			"address='".mysql_escape_string(stripslashes($_POST['address']))."', ".
			"city='".mysql_escape_string(stripslashes($_POST['city']))."', ".
			"province_code='".mysql_escape_string(stripslashes($_POST['province_code']))."', ".
			"postalcode='".mysql_escape_string(stripslashes($_POST['postalcode']))."', ".
			"schoolemail='".mysql_escape_string(stripslashes($_POST['schoolemail']))."', ".
			"phone='".mysql_escape_string(stripslashes($_POST['phone']))."', ".
			"fax='".mysql_escape_string(stripslashes($_POST['fax']))."', ".
			"registration_password='".mysql_escape_string(stripslashes($_POST['registration_password']))."', ".
			"projectlimit='".mysql_escape_string(stripslashes($_POST['projectlimit']))."', ".
			"projectlimitper='".mysql_escape_string(stripslashes($_POST['projectlimitper']))."', ".
			"accesscode='".mysql_escape_string(stripslashes($_POST['accesscode']))."', ".
			$sciencehead_update.$principal_update.
			"atrisk='$atrisk' ".
			"WHERE id='$id'";
		mysql_query($exec);
		echo mysql_error();

		if($_POST['save']=="add")
			$notice = 'added';
		else
			$notice = 'saved';
	}

	if($_GET['action']=="delete" && $_GET['delete'])
	{
		mysql_query("DELETE FROM schools WHERE id='".$_GET['delete']."'");
		$notice = 'deleted';
	}

	if($_GET['action']=="clearaccesscodes")
	{
		mysql_query("UPDATE schools SET accesscode=NULL WHERE year='{$config['FAIRYEAR']}'");
		$notice = 'clearaccess';
	}

	if($_GET['action']=="makeaccesscodes")
	{
		$q=mysql_query("SELECT id FROM schools WHERE year='{$config['FAIRYEAR']}' AND (accesscode IS NULL OR accesscode='')");
		while($r=mysql_fetch_object($q))
		{
			$ac=generatePassword(5);
			mysql_query("UPDATE schools SET accesscode='$ac' WHERE id='$r->id' AND year='{$config['FAIRYEAR']}'");

		}
		$notice = 'makeaccess';
	}

	if($_GET['action']=="edit" || $_GET['action']=="add")
	{

 		send_header(($_GET['action']=='edit') ? "Edit School" : "Add New School",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'School Management' => 'admin/schools.php'),
            "schools_management"
				);
		if($_GET['action']=="edit")
		{
			$buttontext="Save School";
			$q=mysql_query("SELECT * FROM schools WHERE id='".$_GET['edit']."'");
			$r=mysql_fetch_object($q);
		}
		else if($_GET['action']=="add")
		{
			$buttontext="Add School";
		}
		$buttontext=i18n($buttontext);

		echo "<form method=\"post\" action=\"schools.php\">\n";
		echo "<input type=\"hidden\" name=\"save\" value=\"".$_GET['action']."\">\n";

		if($_GET['action']=="edit")
			echo "<input type=\"hidden\" name=\"id\" value=\"".$_GET['edit']."\">\n";

		echo "<table>\n";
		echo "<tr><td>".i18n("School Name")."</td><td><input type=\"text\" name=\"school\" value=\"".htmlspecialchars($r->school)."\" size=\"60\" maxlength=\"64\" /></td></tr>\n";
		echo "<tr><td>".i18n("School Language")."</td><td>";
		echo "<select name=\"schoollang\">";
		echo "<option value=\"\">".i18n("Choose")."</option>\n";
		foreach($config['languages'] AS $k=>$l)
		{
			if($r->schoollang==$k) $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"$k\">".i18n($l)."</option>\n";
		}
		echo "</select>";

		echo "</td></tr>\n";
		echo "<tr><td>".i18n("School Designation")."</td><td>";
		$des = array('' => 'Choose', 'public' => 'Public',
				'independent' => 'Independent/Private',
				'home' => 'Home School');
		echo "<select name=\"schooldesignate\">";
		foreach($des as $k=>$v) {
			$sel=($r->designate == $k) ?'selected="selected"' : '';
			echo "<option $sel value=\"$k\">".i18n($v)."</option>\n";
		}
		echo "</select></td></tr>\n";
		echo "<tr><td>".i18n("School Level")."</td><td><input type=\"text\" name=\"schoollevel\" value=\"".htmlspecialchars($r->schoollevel)."\" size=\"32\" maxlength=\"32\" /></td></tr>\n";
		echo "<tr><td>".i18n("School Board")."</td><td><input type=\"text\" name=\"board\" value=\"".htmlspecialchars($r->board)."\" size=\"60\" maxlength=\"64\" /></td></tr>\n";
		echo "<tr><td>".i18n("School District")."</td><td><input type=\"text\" name=\"district\" value=\"".htmlspecialchars($r->district)."\" size=\"60\" maxlength=\"64\" /></td></tr>\n";
		echo "<tr><td>".i18n("Address")."</td><td><input type=\"text\" name=\"address\" value=\"".htmlspecialchars($r->address)."\" size=\"60\" maxlength=\"64\" /></td></tr>\n";
		echo "<tr><td>".i18n("City")."</td><td><input type=\"text\" name=\"city\" value=\"".htmlspecialchars($r->city)."\" size=\"32\" maxlength=\"32\" /></td></tr>\n";
		echo "<tr><td>".i18n($config['provincestate'])."</td><td>";
			emit_province_selector("province_code",$r->province_code);
		echo "</td></tr>\n";
		echo "<tr><td>".i18n($config['postalzip'])."</td><td><input type=\"text\" name=\"postalcode\" value=\"$r->postalcode\" size=\"8\" maxlength=\"7\" /></td></tr>\n";
		echo "<tr><td>".i18n("Phone")."</td><td><input type=\"text\" name=\"phone\" value=\"".htmlspecialchars($r->phone)."\" size=\"16\" maxlength=\"16\" /></td></tr>\n";
		echo "<tr><td>".i18n("Fax")."</td><td><input type=\"text\" name=\"fax\" value=\"".htmlspecialchars($r->fax)."\" size=\"16\" maxlength=\"16\" /></td></tr>\n";

		if($r->principal_uid > 0) 
			$pl = user_load_by_uid($r->principal_uid);
		else
			$pl = array();
		/* Don't show autogenerated emails */
		$e = $pl['email'][0] == '*' ? '' : $pl['email'];
		echo "<tr><td>".i18n("Principal")."</td><td><input type=\"text\" name=\"principal\" value=\"".htmlspecialchars($pl['name'])."\" size=\"60\" maxlength=\"64\" /></td></tr>\n";
		echo "<tr><td>".i18n("Principal Email")."</td><td><input type=\"text\" name=\"principalemail\" value=\"".htmlspecialchars($e)."\" size=\"60\" maxlength=\"128\" /></td></tr>\n";

		echo "<tr><td>".i18n("School Email")."</td><td><input type=\"text\" name=\"schoolemail\" value=\"".htmlspecialchars($r->schoolemail)."\" size=\"60\" maxlength=\"128\" /></td></tr>\n";
		echo "<tr><td>".i18n("Access Code")."</td><td><input type=\"text\" name=\"accesscode\" value=\"".htmlspecialchars($r->accesscode)."\" size=\"32\" maxlength=\"32\" /></td></tr>\n";
		echo "<tr><td colspan=2><br /><b>".i18n("Science head/teacher or science fair contact at school")."</b></td></tr>";
		if($r->sciencehead_uid > 0) 
			$sh = user_load_by_uid($r->sciencehead_uid);
		else 
			$sh = array();
		/* Don't show autogenerated emails */
		$e = $sh['email'][0] == '*' ? '' : $sh['email'];
		echo "<tr><td>".i18n("Email")."</td><td><input type=\"text\" name=\"scienceheademail\" value=\"".htmlspecialchars($e)."\" size=\"60\" maxlength=\"128\" /></td></tr>\n";
		echo "<tr><td>".i18n("Name")."</td><td><input type=\"text\" name=\"sciencehead\" value=\"".htmlspecialchars($sh['name'])."\" size=\"60\" maxlength=\"64\" /></td></tr>\n";
		echo "<tr><td>".i18n("Phone")."</td><td><input type=\"text\" name=\"scienceheadphone\" value=\"".htmlspecialchars($sh['phonework'])."\" size=\"16\" maxlength=\"16\" /></td></tr>\n";

		if($config['participant_registration_type']=="schoolpassword")
		{
			echo "<tr><td colspan=2><br /><b>".i18n("Participant Registration Password")."</b></td></tr>";
			echo "<tr><td>".i18n("Password")."</td><td><input type=\"text\" name=\"registration_password\" value=\"".htmlspecialchars($r->registration_password)."\" size=\"32\" maxlength=\"32\" /></td></tr>\n";
		}
		echo "<tr><td colspan=2><br /><b>".i18n("Participant Registration Limits")."</b></td></tr>";
		if($config['participant_registration_type']=="invite")
		{
			echo "<tr><td colspan=2>".i18n("Set to 0 to have no registration limit")."</td></tr>";
			echo "<tr><td colspan=2>".i18n("Maximum of")."&nbsp;";
			echo "<input type=\"text\" name=\"projectlimit\" value=\"".htmlspecialchars($r->projectlimit)."\" size=\"4\" maxlength=\"4\" />";
			echo "&nbsp;";
			echo i18n("projects");
			echo "&nbsp;";
			echo "<select name=\"projectlimitper\">";
			if($r->projectlimitper=="total") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"total\">".i18n("total")."</option>\n";
			if($r->projectlimitper=="agecategory") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"agecategory\">".i18n("per age category")."</option>\n";
			echo "</select>";
			echo "</td></tr>\n";
		}
		else
		{
			echo "<tr><td colspan=2>".i18n("Participant registration limits are currently disabled.  In order to use participant registration limits for schools, the participant registration type must be set to 'invite' in Configuration / Configuration Variables")."</td></tr>";
			

		}
		echo "<tr><td colspan=2><br /><b>".i18n("Demographic Information")."</b></td></tr>";

		$ch = ($r->atrisk) == 'yes' ? 'checked="checked"' : '';
		echo "<tr><td align=\"right\"><input type=\"checkbox\" name=\"atrisk\" value=\"yes\" $ch /></td><td>".i18n("Inner City or At-Risk school")."</td></tr>\n";
		echo "<tr><td colspan=\"2\">&nbsp;</td></tr>";
		echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"$buttontext\" /></td></tr>\n";

		echo "</table>\n";
		echo "</form>\n";



	}
	else
	{
 		send_header("School Management",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "schools_management"
				);

		switch($notice) {
		case 'added':
			echo happy("School successfully added");
			break;
		case 'saved':
			echo happy("Successfully saved changes to school");
			break;
		case 'deleted':
			echo happy("School successfully deleted");
			break;
		case 'clearaccess':
			echo happy("Access Codes successfully cleared from all schools");
			break;
		case 'makeaccess':
			echo happy("Access Codes successfully set for schools that didn't have one");
			break;
		}

		echo "<br />";
		echo "<a href=\"schools.php?action=add\">".i18n("Add new school")."</a>\n";
		echo "<br />";
		echo "<a href=\"schoolsimport.php?action=add\">".i18n("Import schools from CSV")."</a>\n";
		echo "<br />";
		echo "<a href=\"schools.php?action=makeaccesscodes\">".i18n("Create Access Code for any school without one")."</a>\n";
		echo "<br />";
		echo "<a onclick=\"return confirmClick('".i18n("Are you sure you want to remove all access codes from all schools?")."')\" href=\"schools.php?action=clearaccesscodes\">".i18n("Remove Access Codes from all schools")."</a>\n";
		echo "<br />";
		echo "<table class=\"tableview\">";
		echo "<thead><tr>";
		echo " <th>".i18n("School")."</th>";
		echo " <th>".i18n("Address")."</th>";
		echo " <th>".i18n("Phone")."</th>";
		echo " <th>".i18n("Contact")."</th>";
		if($config['participant_registration_type']=="schoolpassword")
			echo " <th>".i18n("Reg Pass")."</th>";
		echo " <th>".i18n("Access Code")."</th>";
		echo " <th>".i18n("Action")."</th>";
		echo "</tr></thead>\n";

		$q=mysql_query("SELECT * FROM schools WHERE year='".$config['FAIRYEAR']."' ORDER BY school");
		while($r=mysql_fetch_object($q))
		{
			echo "<tr>\n";
			echo " <td>$r->school</td>\n";
			echo " <td>$r->address, $r->city, $r->postalcode</td>\n";
			echo " <td>$r->phone</td>\n";

			$sciencehead = '';
			if($r->sciencehead_uid > 0) {
				$sh = user_load_by_uid($r->sciencehead_uid);
				$sciencehead = $sh['name'];
			}
			echo " <td>$sciencehead</td>\n";
			if($config['participant_registration_type']=="schoolpassword")
				echo " <td>$r->registration_password</td>\n";
			echo " <td>$r->accesscode</td>\n";

			echo " <td align=\"center\">";
			echo "<a href=\"schools.php?action=edit&edit=$r->id\"><img border=\"0\" src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\"></a>";
			echo "&nbsp;";
			echo "<a onclick=\"return confirmClick('Are you sure you want to remove this school?')\" href=\"schools.php?action=delete&delete=$r->id\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";


			echo " </td>\n";
			echo "</tr>\n";
		}

		echo "</table>\n";


	}

	send_footer();

?>
