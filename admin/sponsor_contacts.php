<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005-2008 James Grant <james@lightbox.org>

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

 send_header("Donor Contacts", 
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Donor' => 'admin/donors.php'));

 if($_GET['sponsors_id'])
 	$sponsors_id=$_GET['sponsors_id'];
 else if($_POST['sponsors_id'])
 	$sponsors_id=$_POST['sponsors_id'];
?>

<?
	$q=mysql_query("SELECT id,organization FROM sponsors ORDER BY organization");
	echo "<form method=\"get\" action=\"sponsor_contacts.php\" name=\"sponsorchange\">";
	echo "<select name=\"sponsors_id\" onchange=\"document.forms.sponsorchange.submit()\">";
	echo "<option value=\"\">".i18n("Choose a sponsor to view contacts")."</option>";
	while($r=mysql_fetch_object($q))
	{
		if($r->id == $sponsors_id)
		{
			$sel="selected=\"selected\"";
			$sponsors_organization=$r->organization;
		}
		else
			$sel="";
		echo "<option $sel value=\"$r->id\">".i18n($r->organization)."</option>";
	}
	echo "</select>";
	echo "</form>";

	if($sponsors_id)
	{
		if($_POST['save']=="edit" || $_POST['save']=="add")
		{
			$p = ($_POST['primary']=='yes')?'yes':'no';

			if($_POST['save']=="add") {
				$u=user_create("sponsor", $_POST['email']);
				$id=$u['id'];
			}
			else {
				$u=user_load($_POST['id']);
				$id=intval($_POST['id']);
			}

			if($p == 'no') {
				/* Make sure this sponsor ($sponsors_id) has a primary */
				$q = mysql_query("SELECT users_id 
							FROM users_sponsor, users 
							WHERE
							users_sponsor.users_id=users.id
							AND sponsors_id='$sponsors_id'
							AND `primary`='yes'
							AND year='".$config['FAIRYEAR']."'
							AND users_id!='$id'");
				if(mysql_num_rows($q) == 0) {
					/* This must be the primary */
					$p = 'yes';
				} 
			} else {
				/* Unset all other primaries */
				mysql_query("UPDATE users_sponsor SET `primary`='no'
						WHERE  sponsors_id='$sponsors_id'");
			}

			$u['primary']=$p;
			$u['salutation']=$_POST['salutation'];
			$u['firstname']=$_POST['firstname'];
			$u['lastname']=$_POST['lastname'];
			$u['position']=$_POST['position'];
			$u['phonework']=$_POST['phonework'];
			$u['phonecell']=$_POST['phonecell'];
			$u['phonehome']=$_POST['phonehome'];
			$u['fax']=$_POST['fax'];
			$u['email']=$_POST['email'];
			$u['notes']=$_POST['notes'];
			$u['sponsors_id']=$sponsors_id;
			user_save($u);

			if($_POST['save']=="add")
				echo happy(i18n("Contact successfully added"));
			else
				echo happy(i18n("Successfully saved changes to contact"));
		}

		if($_GET['action']=="delete" && $_GET['delete']) {
			user_delete(intval($_GET['delete']));
			echo happy("Contact successfully deleted");
		}

		if($_GET['action']=="edit" || $_GET['action']=="add")
		{

			echo "<a href=\"sponsor_contacts.php?sponsors_id=$sponsors_id\">&lt;&lt; ".i18n("Back to %1 Contacts",array($sponsors_organization))."</a>\n";
			if($_GET['action']=="edit")
			{
				echo "<h3>".i18n("Edit %1 Contact",array($sponsors_organization))."</h3>\n";
				$buttontext="Save Contact";
//				$q=mysql_query("SELECT * FROM sponsor_contacts WHERE id='".$_GET['edit']."'");
//				$r=mysql_fetch_object($q);
				$u=user_load(intval($_GET['edit']));
			}
			else if($_GET['action']=="add")
			{
				echo "<h3>".i18n("Add %1 Contact",array($sponsors_organization))."</h3>\n";
				$buttontext="Add Contact";
			}
			$buttontext=i18n($buttontext);

			echo "<form method=\"post\" action=\"sponsor_contacts.php\">\n";
			echo "<input type=\"hidden\" name=\"sponsors_id\" value=\"$sponsors_id\">\n";
			echo "<input type=\"hidden\" name=\"save\" value=\"".$_GET['action']."\">\n";

			if($_GET['action']=="edit")
				echo "<input type=\"hidden\" name=\"id\" value=\"".$_GET['edit']."\">\n";

			echo "<table>\n";
			echo "<tr><td>".i18n("Salutation")."</td><td><input type=\"text\" name=\"salutation\" value=\"".htmlspecialchars($u['salutation'])."\" size=\"4\" maxlength=\"8\" /></td></tr>\n";
			echo "<tr><td>".i18n("First Name")."</td><td><input type=\"text\" name=\"firstname\" value=\"".htmlspecialchars($u['firstname'])."\" size=\"32\" maxlength=\"32\" /></td></tr>\n";
			echo "<tr><td>".i18n("Last Name")."</td><td><input type=\"text\" name=\"lastname\" value=\"".htmlspecialchars($u['lastname'])."\" size=\"32\" maxlength=\"32\" /></td></tr>\n";
			echo "<tr><td>".i18n("Position")."</td><td><input type=\"text\" name=\"position\" value=\"".htmlspecialchars($u['position'])."\" size=\"60\" maxlength=\"64\" /></td></tr>\n";
			echo "<tr><td>".i18n("Phone (Work)")."</td><td><input type=\"text\" name=\"phonework\" value=\"".htmlspecialchars($u['phonework'])."\" size=\"16\" maxlength=\"32\" /></td></tr>\n";
			echo "<tr><td>".i18n("Phone (Cell)")."</td><td><input type=\"text\" name=\"phonecell\" value=\"".htmlspecialchars($u['phonecell'])."\" size=\"16\" maxlength=\"32\" /></td></tr>\n";
			echo "<tr><td>".i18n("Phone (Home)")."</td><td><input type=\"text\" name=\"phonehome\" value=\"".htmlspecialchars($u['phonehome'])."\" size=\"16\" maxlength=\"32\" /></td></tr>\n";
			echo "<tr><td>".i18n("Fax")."</td><td><input type=\"text\" name=\"fax\" value=\"".htmlspecialchars($u['fax'])."\" size=\"16\" maxlength=\"32\" /></td></tr>\n";
			echo "<tr><td>".i18n("Email")."</td><td><input type=\"text\" name=\"email\" value=\"".htmlspecialchars($u['email'])."\" size=\"60\" maxlength=\"128\" /></td></tr>\n";
			echo "<tr><td>".i18n("Notes")."</td><td><textarea name=\"notes\" rows=\"8\" cols=\"60\">".htmlspecialchars($u['notes'])."</textarea></td></tr>\n";
			echo "<tr><td>".i18n("Primary Contact")."</td><td><select name=\"primary\">";
			$sel = ($u['primary'] == 'yes') ? 'selected="selected"': '';
			echo "<option value=\"yes\" $sel>".i18n('Yes')."</option>";
			$sel = ($u['primary'] == 'no') ? 'selected="selected"': '';
			echo "<option value=\"no\" $sel>".i18n('No')."</option>";
			echo "</select></td></tr>\n";
			echo "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"$buttontext\" /></td></tr>\n";

			echo "</table>\n";
			echo "</form>\n";
		}
		else
		{

			echo "<br />";
			echo "<a href=\"sponsor_contacts.php?sponsors_id=$sponsors_id&action=add\">".i18n("Add New Contact to %1",array($sponsors_organization))."</a>\n";
			echo "<br />";

			$q=mysql_query("SELECT * FROM users LEFT JOIN users_sponsor ON users_sponsor.users_id=users.id
					 WHERE year='".$config['FAIRYEAR']."' 
					 AND sponsors_id='$sponsors_id' 
					 AND deleted='no' 
					 ORDER BY lastname,firstname");
			echo mysql_Error();

			if(mysql_num_rows($q))
			{
				echo "<table class=\"tableview\">";
				echo "<thead><tr>";
				echo " <th>".i18n("Name")."</th>";
				echo " <th>".i18n("Email")."</th>";
				echo " <th>".i18n("Phone (Work)")."</th>";
				echo " <th>".i18n("Phone (Cell)")."</th>";
				echo " <th>".i18n("Primary")."</th>";
				echo " <th>Actions</th>";
				echo "</tr></thead>\n";


				while($r=mysql_fetch_object($q))
				{
					echo "<tr>\n";
					echo " <td>";
					if($r->salutation) echo $r->salutation." ";
					echo "$r->firstname $r->lastname</td>\n";
					echo " <td>";
					if($r->email) {
						list($eb,$ea)=split("@",$r->email);
						echo "<script language=\"javascript\" type=\"text/javascript\">em('$eb','$ea')</script>";
					}
					else
						echo "&nbsp;";

					echo " </td>";
					echo " <td>$r->phonework</td>\n";
					echo " <td>$r->phonecell</td>\n";
					$p = i18n(($r->primary=='yes')?'Yes':'No');
					echo " <td>$p</td>\n";
					echo " <td align=\"center\">";
					//FIXME: should we just go to /user_personal.php here instead?
					echo "<a href=\"sponsor_contacts.php?sponsors_id=$sponsors_id&action=edit&edit=$r->id\"><img border=\"0\" src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\"></a>";
					echo "&nbsp;";
					echo "<a onclick=\"return confirmClick('Are you sure you want to remove this contact?')\" href=\"sponsor_contacts.php?sponsors_id=$sponsors_id&action=delete&delete=$r->id\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
					echo " </td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";
			}
		}

	}

	send_footer();

?>
