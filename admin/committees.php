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
 require_once("../common.inc.php");
 require_once("../user.inc.php");
 require_once("../committee.inc.php");

 user_auth_required('committee', 'admin');

if($_POST['users_uid'])
	$uid = intval($_POST['users_uid']);


 /* Now, start the output for this page */
 send_header("Committee Management",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php' ),
            "committee_management");


 $_SESSION['last_page'] = 'committee_management';
?>


<script type="text/javascript">
<!--

function openeditor(id)
{
	window.open("user_editor_window.php?id="+id,"UserEditor","location=no,menubar=no,directories=no,toolbar=no,width=770,height=500,scrollbars=yes"); 
	return false;
}

function neweditor()
{
	var username = document.forms.addmember.add_member.value;
	window.open("user_editor_window.php?type=committee&username="+username,"UserEditor","location=no,menubar=no,directories=no,toolbar=no,width=770,height=500,scrollbars=yes"); 
	document.forms.addmember.add_member.value = "";
	return false;
}


function getElement(e,f)
{
	if(document.layers)
	{
		f=(f)?f:self;
		if(f.document.layers[e]) {
			return f.document.layers[e];
		}
		for(W=0;i<f.document.layers.length;W++) {
			return(getElement(e,fdocument.layers[W]));
		}
	}
	if(document.all) {
		return document.all[e];
	}
	return document.getElementById(e);
}


function actionChanged() 
{
	if(document.forms.memberaction.action.selectedIndex==1) //assign
	{
		getElement('assigndiv').style.display = 'block';

	}
	else // edit or delete
	{
		getElement('assigndiv').style.display = 'none';
	}

}

function actionSubmit()
{
	if(document.forms.memberaction.action.selectedIndex==0)
	{
		alert('You must choose an action');
		return false;
	}
	if(document.forms.memberaction.users_uid.selectedIndex==0)
	{
		alert('You must choose a member');
		return false;
	}

	if(document.forms.memberaction.action.selectedIndex == 2) {
		// Edit
		var id = document.forms.memberaction.users_uid.options[document.forms.memberaction.users_uid.selectedIndex];
		openeditor(id.value);
//		alert("id="+id.value);
		return false;
	}
	if(document.forms.memberaction.action.selectedIndex==3) //remove
	{
		return confirmClick('Are you sure you want to completely remove this member?');
	}

	return true;
}
//-->
</script>
<?

if($_POST['addcommittee'])
{
	//add a new committee
	mysql_query("INSERT INTO committees (name) VALUES ('".mysql_escape_string($_POST['addcommittee'])."')");
	echo happy(i18n("Committee successfully added"));
}

if($_POST['committees_id'] && $_POST['committees_ord']) {
	//re-order the committees
	$x=0;
	$ids=$_POST['committees_id'];
	$ords=$_POST['committees_ord'];

	$titles=$_POST['title'];
	$pords = $_POST['order'];
	while($ids[$x]) {
		$cid = intval($ids[$x]);
		mysql_query("UPDATE committees SET ord='".intval($ords[$x])."' WHERE id='$cid'");
		$x++;

		$ctitle = $titles[$cid];
		$cord = $pords[$cid];

		/* If the committee has no members, don't bother trying to do
		 * anything */
		if(!is_array($ctitle)) continue;
//		print_r($ctitle);

		foreach($ctitle as $uid=>$title) {
			$o = intval($cord[$uid]);
			$t = mysql_escape_string(stripslashes($title));
			$u = intval($uid);
			$q = "UPDATE committees_link SET title='$t', ord='$o'
				WHERE committees_id='$cid' AND users_uid='$u'";
//				echo $q;
			mysql_query($q);
		}

	}
	echo happy(i18n("Committees successfully saved"));

}

if($_POST['action']=="assign")
{
	if($_POST['committees_id'] && $_POST['users_uid']) {
		$cid = intval($_POST['committees_id']);
		$q=mysql_query("SELECT * FROM committees_link WHERE committees_id='$cid' AND users_uid='$uid'");

		if(!mysql_num_rows($q)) {
			mysql_query("INSERT INTO committees_link (committees_id,users_uid) VALUES ('$cid','$uid')");
			echo happy(i18n("Successfully added member to committee"));
		}
		else
			echo error(i18n("That member already exists in that committee"));
	}
	else
		echo error(("You must choose both a member and a committee"));
}

if($_GET['deletecommittee']) {
	$del = intval($_GET['deletecommittee']);
	mysql_query("DELETE FROM committees WHERE id='$del'");
	echo happy(i18n("Committee removed"));
}

if($_POST['action']=="remove") {
	/* user_delete takes care of unlinking the user in other tables */
	user_delete($uid, 'committee');
	echo happy(i18n("Committee member deleted"));
}

if($_GET['unlinkmember'] && $_GET['unlinkcommittee']) {
	$mem = intval($_GET['unlinkmember']);
	$com = intval($_GET['unlinkcommittee']);
	//unlink the member from the committee
	mysql_query("DELETE FROM committees_link WHERE users_uid='$mem' AND committees_id='$com'");
	echo happy(i18n("Committee member unlinked from committee"));
}


	echo "<table>";
	echo "<tr><td>";

	echo "<h4>".i18n("Add Committee")."</h4>\n";
	echo "<form method=\"post\" action=\"committees.php\">\n";
	echo "<table>\n";
	echo "<tr><td>".i18n("Committee Name").": </td><td><input type=\"text\" size=\"15\" name=\"addcommittee\" /></td>";
	echo "    <td><input type=\"submit\" value=\"".i18n("Add")."\" /></td></tr>\n";
	echo "</table>\n";
	echo "</form>\n";

	echo "</td><td width=\"40\">&nbsp;</td><td>";

	echo "<h4>".i18n("Add Committee Member")."</h4>\n";
	echo "<form method=\"post\" name=\"addmember\" action=\"committees.php\" onsubmit=\"return neweditor();\">\n";
	echo "<table>\n";
	echo "<tr><td>".i18n("Member Email").": </td><td>";
	echo "<input type=\"text\" size=\"15\" name=\"add_member\" />\n";
	echo "</td>\n";
	echo " <td><input type=\"submit\" onclick=\"return neweditor();\" value=\"".i18n("Add")."\" /></td></tr>\n";
	echo "</table>\n";
	echo "<a href=\"committees.php\">".i18n("Reload committee list (needed after adding a new member)")."</a>\n";
	echo "</form>\n";

	echo "</td></tr>";
	echo "</table>";


	echo "<hr />";
	echo "<h4>".i18n("Committee Member Management")."</h4>\n";
	echo "<form name=\"memberaction\" method=\"post\" action=\"committees.php\" onsubmit=\"return actionSubmit()\">\n";
	echo "<table>";
	echo "<tr><td>";
	echo "<select name=\"action\" onchange=\"javascript:actionChanged()\">";
	echo "<option value=\"\">".i18n("Choose")."</option>\n";
	echo "<option value=\"assign\">".i18n("Assign")."</option>\n";
	echo "<option value=\"edit\">".i18n("Edit")."</option>\n";
	echo "<option value=\"remove\">".i18n("Remove")."</option>\n";
	echo "</select>";

	echo "</td><td>";
	$q=mysql_query("SELECT uid,MAX(year),firstname,lastname,email,deleted FROM users WHERE types LIKE '%committee%' GROUP BY uid ORDER BY firstname");
	echo "<select name=\"users_uid\">";
	echo "<option value=\"\">".i18n("Select a Member")."</option>\n";
	while($r=mysql_fetch_object($q))
	{
		if($r->deleted != 'no') continue;
		$displayname = $r->firstname.' '.$r->lastname;
		echo "<option value=\"$r->uid\">$displayname ($r->email)</option>\n";
	}
	echo "</select>";


	echo "</td><td>";


	//The Assign Div
	echo "<div id=\"assigndiv\">";
	echo i18n("To Committee").": ";
	$q=mysql_query("SELECT * FROM committees ORDER BY ord,name");
	echo "<select name=\"committees_id\">";
	echo "<option value=\"\">".i18n("Select a Committee")."</option>\n";
	while($r=mysql_fetch_object($q))
	{
		echo "<option value=\"$r->id\">$r->name</option>\n";
	}
	echo "</select>";
	echo "</div>";


	//The Edit or Remove Div

	echo "</td><td><input type=\"submit\" value=\"".i18n("Go")."\" /></td></tr>";

	echo "</table>";
	echo "</form>";

	echo "<script language=\"javascript\" type=\"text/javascript\">actionChanged()</script>";
	echo "<hr />";


	$q=mysql_query("SELECT * FROM committees ORDER BY ord,name");
	if(mysql_num_rows($q))
	{
		echo "<h4>".i18n("Committees")."</h4>";
		echo "<form method=\"post\" action=\"committees.php\">\n";
		echo "<table>";
		echo "<tr><td colspan=\"2\"></td><td><b>".i18n('Title')."</b></td>";
		echo "<td><b>".i18n('Order')."</b></td>";
		echo "<td><b>".i18n("Public Email / Private Email")."</b></td></tr>";
		while($r=mysql_fetch_object($q))
		{
			echo "<tr>";
			echo "<td colspan=\"3\">";
			echo "<input type=\"hidden\" name=\"committees_id[]\" value=\"$r->id\" />";
			echo "<input size=\"1\" type=\"text\" name=\"committees_ord[]\" value=\"$r->ord\" />";
			echo "&nbsp; <b>$r->name</b>";


			$q2=mysql_query("SELECT 
								committees_link.title,
								committees_link.ord,
								users.uid,
								MAX(users.year) AS my,
								users.lastname
								FROM committees_link 
								JOIN users ON users.uid = committees_link.users_uid 
								WHERE committees_id='{$r->id}' 
								GROUP BY users.uid 
								ORDER BY ord,
								users.lastname ");

			if(mysql_num_rows($q2)==0) {
				echo "&nbsp; &nbsp;";
				echo "<a title=\"Remove Committee\" onclick=\"return confirmClick('Are you sure you want to remove this committee?');\" href=\"committees.php?deletecommittee=$r->id\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=\"0\" alt=\"Remove Committee\" /></a>";
			}

			echo "</td></tr>\n";
			echo mysql_error();
			while($r2=mysql_fetch_object($q2)) {
				$u = user_load_by_uid($r2->uid);
				echo "<tr><td align=\"right\">&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<a title=\"Edit Member\" href=\"#\" onclick=\"openeditor({$u['id']})\"><img src=\"{$config['SFIABDIRECTORY']}/images/16/edit.{$config['icon_extension']}\" border=\"0\" alt=\"Edit\" /></a>";
				echo "&nbsp;";
				echo "<a title=\"Unlink Member from Committee\" onclick=\"return confirmClick('Are you sure you want to unlink this member from this committee?');\" href=\"committees.php?unlinkmember={$u['uid']}&amp;unlinkcommittee={$r->id}\"><img src=\"{$config['SFIABDIRECTORY']}/images/16/undo.{$config['icon_extension']}\" border=\"0\" alt=\"Unlink\" /></a>";
				echo "</td>";
				echo "<td valign=\"top\">";
				echo "<b>{$u['name']}</b>";
				echo "</td><td>";
				echo "<input type=\"text\" value=\"{$r2->title}\" name=\"title[{$r->id}][{$u['uid']}]\" size=\"15\">";
				echo "</td><td>";
				echo "<input type=\"text\" value=\"{$r2->ord}\" name=\"order[{$r->id}][{$u['uid']}]\" size=\"2\">";

				echo "</td><td>";

				if($u['email']) {
					list($b,$a)=split("@",$u['email']);
					echo "<script language=\"javascript\" type=\"text/javascript\">em('$b','$a')</script>";
				}

				if($u['emailprivate']) {
					if($u['email']) echo " <b>/</b> ";
					list($b,$a)=split("@",$u['emailprivate']);
					echo "<script language=\"javascript\" type=\"text/javascript\">em('$b','$a')</script>";
				}

				echo "</td></tr>\n";
			}
			echo "<tr><td colspan=\"2\">&nbsp;</td></tr>\n";
		}
		echo "<tr><td colspan=\"2\"><input type=\"submit\" value=\"".i18n("Save Committee Orders and Titles")."\" /></td></tr>\n";
		echo "</table>";
		echo "</form>\n";
	}

send_footer();
?>

