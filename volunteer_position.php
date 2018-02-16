<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>
   Copyright (C) 2007 David Grant <dave@lightbox.org>

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
 require_once("common.inc.php");
 require_once("user.inc.php");
 require_once("volunteer.inc.php");


 if($_SESSION['embed'] == true) {
 	$u = user_load($_SESSION['embed_edit_id']);
 } else {
	user_auth_required('volunteer');
	$u = user_load($_SESSION['users_id']);
 }


 if($_POST['action']=="save")
 {
	$vals = '';
 	if(is_array($_POST['posn'])) {

		/* Load available IDs */
		$posns = array();
		$q = "SELECT * FROM volunteer_positions WHERE year='{$config['FAIRYEAR']}'";
		$r = mysql_query($q);
		while($p = mysql_fetch_object($r)) {
			$posns[] = $p->id;
		}

		/* Match selections with avaiulable positions */
		foreach($_POST['posn'] as $id=>$val) {
			if(!in_array($id, $posns)) continue;

			if($vals != '') $vals .=',';
			$vals .= "('{$u['id']}','$id','{$config['FAIRYEAR']}')";
		}
	} 
			
	/* Delete existing selections */
	mysql_query("DELETE FROM volunteer_positions_signup 
			WHERE 
				users_id='{$u['id']}' 
				AND year='{$config['FAIRYEAR']}' ");
		echo mysql_error();

	/* Add new selections if there are any */
	if($vals != '') {
		$q = "INSERT INTO volunteer_positions_signup (users_id, volunteer_positions_id,year) 
				VALUES $vals";
		$r=mysql_query($q);
		echo mysql_error();

	}

	message_push(notice(i18n("Volunteer Positions successfully updated")));
 }

/* update overall status */
volunteer_status_update($u);

if($_SESSION['embed'] != true) {
	//output the current status
	$newstatus=volunteer_status_position($u);
	if($newstatus!='complete')
		message_push(error(i18n("Volunteer Position Selection Incomplete")));
	else
		message_push(happy(i18n("Volunteer Position Selection Complete")));
}

if($_SESSION['embed'] == true) {
 	echo "<br />";
	display_messages();
	echo "<h3>".i18n('Volutneer Positions')."</h3>";
 	echo "<br />";
} else {
	//send the header
	send_header("Volunteer Positions", 
 		array("Volunteer Registration" => "volunteer_main.php")
		);
}

 $s = ($_SESSION['embed'] == true) ? $_SESSION['embed_submit_url'] : 'volunteer_position.php';
 echo "<form name=\"personalform\" method=\"post\" action=\"$s\">\n";
 echo "<input type=\"hidden\" name=\"action\" value=\"save\" />\n";
 echo "<table>\n";

 /* Read current selections */
 $q = "SELECT * FROM volunteer_positions_signup WHERE
 		users_id = '{$u['id']}' 
 		AND year='{$config['FAIRYEAR']}'";
 $r = mysql_query($q);
 $checked_positions = array();
 while($p = mysql_fetch_object($r)) {
 	$checked_positions[] = $p->volunteer_positions_id;
 }

 /* Load available volunteer positions */
 $q = "SELECT *,UNIX_TIMESTAMP(start) as ustart, UNIX_TIMESTAMP(end) as uend
 			FROM volunteer_positions WHERE year='{$config['FAIRYEAR']}'";
 $r = mysql_query($q);
 while($p = mysql_fetch_object($r)) {

 	echo '<tr><td>';

	$checked = false;
	
	if($_SESSION['lang'] == 'en') {
		$sday = strftime("%a. %B %e, %Y", $p->ustart);
		$stime = strftime("%H:%M", $p->ustart);
		$eday = strftime("%a. %B %e, %Y", $p->uend);
		$etime = strftime("%H:%M", $p->uend);
		if($sday == $eday) {
			$start = $stime;
			$end = "$etime, $sday";
		} else {
			$start = "$sday, $stime";
			$end = "$eday, $etime";
		}
	} else {
		$start = $p->start;
		$end = $p->end;
	}


	$ch = in_array($p->id, $checked_positions) ? 'checked="checked"' : '';
	echo "<input $ch type=\"checkbox\" name=\"posn[$p->id]\" value=\"checked\" />";
	
	echo '</td><td>';
	echo '<b>'.i18n($p->name).'</b></td>' ;

	echo "<td align=\"right\">($start - $end)</td></tr>";
	echo '<tr><td></td><td colspan="2"><div style="font-size: 0.75em;">';
	echo i18n($p->desc);
	echo '<br /><br /></div></td></tr>';
}

echo "</table>";
echo "<input type=\"submit\" value=\"".i18n("Save Position Selection")."\" />\n";
echo "</form>";

 echo "<br />";

 if($_SESSION['embed'] != true) send_footer();
?>
