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
 require_once('../common.inc.php');
 require_once('../user.inc.php');
 require_once('../judge.inc.php');
 user_auth_required('committee', 'admin');

 require_once('judges.inc.php');

 $show_types = $_GET['show_types'];
 if(user_valid_type($show_types) == false) $show_types = array('judge');

 $show_complete = ($_GET['show_complete'] == 'yes') ? 'yes' : 'no';
 $show_year = ($_GET['show_year'] == 'current') ? 'current' : 'all';

 $uid = intval($_GET['uid']);

 if($_GET['action']=='remove') 
 {
		if(!$uid)
		{
			echo "Invalid uid for delete";
			exit;
		}
		user_delete($uid);
		message_push(happy(i18n('User deleted.')));
 }
 else if($_GET['action']=='email')// if we want an email from this page
 {
	 // send an email from this page 
		//
		/// ADDED by DRE 2018
		//
		// concatenate website specifics
		$urlproto = $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
		$urlmain = "$urlproto{$_SERVER['HTTP_HOST']}{$config['SFIABDIRECTORY']}";
		$urllogin = "$urlmain/login.php";			

		// Participant Registration worked but it uses the old email_send()
		// 1st val is value to extract from MySQL table
		// 2nd val is email address
		// 3rd val is supposed to be Subject - which agglomerates onto FAIRNAME
		// 4th val is now registations.num from MySQL table `registrations`
		// insert pro-form values as 1-arrays that get evaluated and imploded into single strings
		email_send($_GET['val'],$_GET['to'],array("FAIRNAME"=>i18n($config['fairname'])),array( $email_body, "FAIRNAME"=>i18n($config['fairname']), "WEBSITE"=>$urllogin, "EMAIL"=>$_GET['to']  ));
		//send_header("Email sent!");
		echo notice(i18n("%1 sent to %2", array( $_GET['action'],  $_GET['to']) ));		 
 }
 // normal operation
 send_header("User Editor",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php')
				);
?>
<script language="javascript" type="text/javascript">

function openeditor(id)
{
	if(id) currentid=id;

	window.open("user_editor_window.php?id="+currentid,"UserEditor","location=no,menubar=no,directories=no,toolbar=no,width=1000,height=640,scrollbars=yes"); 
	return false;

}

function toggleoptions()
{
	if(document.getElementById('options').style.display == 'none') {
		document.getElementById('options').style.display = 'block';
		document.getElementById('optionstext').innerHTML = '- <?=i18n('Hide Display Options')?>';

	} else {
		document.getElementById('options').style.display = 'none';
		document.getElementById('optionstext').innerHTML = '+ <?=i18n('Show Display Options')?>';
	}
}

function togglenew()
{
	if(document.getElementById('new').style.display == 'none') {
		document.getElementById('new').style.display = 'block';
		document.getElementById('newtext').innerHTML = '<?=i18n('Cancel New User')?>';

	} else {
		document.getElementById('new').style.display = 'none';
		document.getElementById('newtext').innerHTML = '<?=i18n('Add New User')?>';
	}
}

function neweditor()
{
	var username = document.forms.newuser.new_email.value;
	var usertype = document.forms.newuser.new_type.value;
	window.open("user_editor_window.php?type="+usertype+"&username="+username,"UserEditor","location=no,menubar=no,directories=no,toolbar=no,width=770,height=500,scrollbars=yes"); 
	document.forms.newuser.new_email.value = "";
	return false;
}

</script>

<?

	echo "<div class=\"notice\">";
	echo "<a id=\"optionstext\" onclick=\"toggleoptions();return false;\">- ".i18n('Hide Display Options')."</a>";

	echo "<form method=\"GET\" action=\"$PHP_SELF\">";

	echo "<div id=\"options\" style=\"display: block;\" >";
	echo "<table><tr><td>".i18n('Type').":</td>";
	$x = 0;
	foreach($user_what as $k=>$v ) {
		$sel = (in_array($k, $show_types)) ? 'checked="checked"' : '';
		echo "<td><input type=\"checkbox\" name=\"show_types[]\" value=\"$k\" $sel >".i18n($v)."</input></td>";
		if($x) echo "</tr><tr><td></td>";
		$x = ~$x;
	}
	echo "</tr>";

	echo "<tr><td>".i18n('Complete').":</td><td>";
	echo "<select name=\"show_complete\">";
	$s = ($show_complete == 'yes') ? 'selected="selected"' : '';
	echo "<option value=\"yes\" $s>".i18n('Show only complete registrations')."</option>";
	$s = ($show_complete == 'no') ? 'selected="selected"' : '';
	echo "<option value=\"no\" $s>".i18n('Show ALL registrations')."</option>";
	echo "</select>";
	
	echo "</tr>";
	echo "<tr><td>".i18n('Year').":</td><td>";
	echo "<select name=\"show_year\">";
	$s = ($show_year == 'current') ? 'selected="selected"' : '';
	echo "<option value=\"current\" $s>".i18n('Show only registrations from %1', array($config['FAIRYEAR']))."</option>";
	$s = ($show_year == 'all') ? 'selected="selected"' : '';
	echo "<option value=\"all\" $s>".i18n('Show ALL years')."</option>";
	echo "</select>";
	echo "</td></tr></table>";
	echo "<br />";	
	echo "<input type=submit value=\"".i18n('Apply Filter')."\">";
	echo "</div>";
	echo "</form>";
	
	echo "</div>";

	
	echo "<br/><a id=\"newtext\" href=\"javascript:togglenew()\">".i18n('Add New User')."</a>";
	echo '<div id="new" style="display: none;" class="notice">';
	echo "<form name=\"newuser\" method=\"GET\" action=\"$PHP_SELF\">";
	echo "<table><tr><td>".i18n('Type').":</td><td>";
	echo "<select name=\"new_type\">";
	$x = 0;
	foreach($user_what as $k=>$v ) {
		$sel = (in_array($k, $show_types)) ? 'selected="selected"' : '';
		echo "<option value=\"$k\" $sel>".i18n($v)."</option>";
	}
	echo "</select>";
	echo "</tr>";
	echo "<tr><td>".i18n('Email').":</td><td>";
	echo '<input type="text" name="new_email" value="" />';
	echo '</td></tr>';
	echo '</table>';
	echo "<input type=submit onclick=\"neweditor();\" value=\"".i18n('Create New User')."\">";
	
	echo '</form>';
	
	echo '</div>';
	echo "<br />";	
	echo "<br />";	

	/* Grab a list of users */
	$w = array();
	foreach($show_types as $t) {
		$w [] = "u1.types LIKE '%$t%'";
	}
	$where_types = "AND (".join(" OR ", $w).")";

	$where_complete = "";
	if($show_complete == 'yes') {
		foreach($show_types as $t) {
			$where_complete .= "AND ({$t}_complete='yes' OR {$t}_complete IS NULL) ";
		}
	}

	if($show_year == 'current') 
		$having_year = "AND u1.year={$config['FAIRYEAR']}";

	echo "<table class=\"tableview\">";

		$querystr="SELECT 
				*
			FROM 
				users u1
				LEFT JOIN `users_committee` ON `users_committee`.`users_id`=`u1`.`id`
				LEFT JOIN `users_judge` ON `users_judge`.`users_id`=`u1`.`id`
				LEFT JOIN `users_volunteer` ON `users_volunteer`.`users_id`=`u1`.`id`
				LEFT JOIN `users_fair` ON `users_fair`.`users_id`=`u1`.`id`
				LEFT JOIN `users_sponsor` ON `users_sponsor`.`users_id`=`u1`.`id`	
			WHERE u1.year=( SELECT MAX(`year`) FROM users u2 WHERE u1.uid=u2.uid )
			GROUP BY uid
			HAVING 
				u1.deleted='no'
				$having_year
				$where_types
				$where_complete
			ORDER BY 
				lastname ASC,
				firstname ASC,
				year DESC";
	$q=mysql_query($querystr);
	echo mysql_error();
//	echo $querystr;
	$num=mysql_num_rows($q);
	echo i18n("Listing %1 people total.  See the bottom for breakdown of by complete status",array($num));

	echo mysql_error();
	echo "<thead>";
	echo "<tr>";
	echo " <th>".i18n("Name")."</th>";
	echo " <th>".i18n("Email Address")."</th>";
	echo " <th>".i18n("Year")."</th>";
	echo " <th>".i18n("Type(s)")."</th>";
	echo " <th>".i18n("Active")."</th>";
	echo " <th>".i18n("Complete")."</th>";
	echo " <th>".i18n("Actions")."</th>";
	echo "</tr>";
	echo "</thead>";

	$tally = array();
	$tally['active'] = array();
	$tally['inactive'] = array();
	$tally['active']['complete'] = 0;
	$tally['active']['incomplete'] = 0;
	$tally['inactive']['complete'] = 0;
	$tally['inactive']['incomplete'] = 0;
	while($r=mysql_fetch_assoc($q))
	{
		//JAMES - TEMP - due to the previous error with improperly setting judge status to NOT complete when special awards was turned off
		//we now need to make sure we re-calculate all the judge statuses somehow, so might as well do it here.
		//FIXME: remove this after all the fairs are done this year SUMMER 2010
		if(in_array('judge',$show_types))
		{
			$u=user_load_by_uid($r['uid']);

			//we also set teh $r array so it displays properly on first load
			if(judge_status_update($u)=="complete")
				$r['judge_complete']='yes';
		}
		$types = split(',', $r['types']);
		$span = count($types) > 1 ? "rowspan=\"".count($types)."\"" : '';
		echo "<tr><td $span>";

		$name = "{$r['firstname']} {$r['lastname']}";
		if(in_array('fair', $types)) 
		{
			$qq = mysql_query("SELECT * FROM users_fair 
							LEFT JOIN fairs ON fairs.id=users_fair.fairs_id
						WHERE users_id='{$r['id']}'");
			$rr = mysql_fetch_assoc($qq);
			$name = "{$rr['name']}".((trim($name)=='') ? '' : "<br />($name)");
		} 
		echo "<a href=\"#\" onclick=\"return openeditor({$r['id']})\">$name</a>";
		echo "</td>";

		echo "<td $span>{$r['email']}</td>";

		echo "<td $span>{$r['year']}</td>";

		$first = true;
		$complete = false;
		$incomplete = false;
		foreach($types as $t) 
		{
			if(!$first) echo '</tr><tr>';
			echo "<td>{$user_what[$t]}</td>";

			echo "<td>";
			if($r["{$t}_active"] == 'yes') 
			{
				echo "<div class=\"happy\" align=\"center\">".i18n("yes")."</div>";
				$userstate = 'active';
			} 
			else
			{
				echo "<div class=\"error\" align=\"center\">".i18n("no")."</div>";
				$userstate = 'inactive';
			}
			echo "</td>";

			echo "<td>";
			if(in_array($t, array('parent','committee','alumni','mentor','fair'))) 
			{
				/* Do nothing, there's nothing to complete */
			} else if($r["{$t}_complete"] == 'yes') {
				echo "<div class=\"happy\" align=\"center\">".i18n("yes")."</div>";
				$complete = true;
			} else {
				echo "<div class=\"error\" align=\"center\">".i18n("no")."</div>";
				$incomplete = true;
			}
			echo "</td>";

			if($first) 
			{
				/* Finish off the the first line */
				echo "<td $span align=\"center\">";
				echo "<a title=\"Edit\" href=\"#\" onclick=\"return openeditor({$r['id']})\"><img border=0 src=\"{$config['SFIABDIRECTORY']}/images/16/edit.{$config['icon_extension']}\"></a>&nbsp;";
				echo "<a title=\"Delete\" onclick=\"return confirmClick('Are you sure you wish to completely delete this user?')\" href=\"user_list.php?action=remove&uid={$r['id']}\"><img border=0 src=\"{$config['SFIABDIRECTORY']}/images/16/button_cancel.{$config['icon_extension']}\"></a>";
//
/// MODIFIED DRE 2018
//				
//! \note Invite email added for existing judges
				echo "<a onclick=\"return confirmClick('Are you sure you wish to email this user?')\" title=\"Email Judge\" href=\"user_list.php?action=email&to={$r['email']}&val=judge_activate_reminder\"><img border=0 src=\"{$config['SFIABDIRECTORY']}/images/16/email-better.png\"></a>";
				
				echo "</td>";
			}
			$first = false;
		}
		echo '</tr>';

		if($complete){
			$tally[$userstate]['complete']++;
		}else if($incomplete){
			$tally[$userstate]['incomplete']++;
		}
	}

	echo "</table>";
	echo i18n("Note: Deleting users from this list is a permanent operation and cannot be undone.  Consider editting the user and deactivating or deleting roles in their account instead.");

	// let's make a table with the complete/incomplete counts and the active/inacteve states
?>
	<table rules="all" style="border:solid 1px; margin:2em">
		<thead>
			<tr><td colspan="4" align="center">List Totals</td></tr>
			<tr>
				<th></th>
				<th>Complete</th>
				<th>Incomplete</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><strong>Active</strong></td>
				<td><?=$tally['active']['complete']?></td>
				<td><?=$tally['active']['incomplete']?></td>
				<td><?=$tally['active']['complete'] + $tally['active']['incomplete']?></td>
			</tr><tr>
				<td><strong>Inactive</strong></td>
				<td><?=$tally['inactive']['complete']?></td>
				<td><?=$tally['inactive']['incomplete']?></td>
				<td><?=$tally['inactive']['complete'] + $tally['inactive']['incomplete']?></td>
			</tr><tr>
				<td><strong>Total</strong></td>
				<td><?=$tally['active']['complete'] + $tally['inactive']['complete']?></td>
				<td><?=$tally['active']['incomplete'] + $tally['inactive']['incomplete']?></td>
				<td><?=$num?></td>
			</tr>
		</tbody>
	</table>
<?php
	send_footer();
?>
