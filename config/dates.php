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
 send_header("Dates",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"important_dates"
			);

$q=mysql_query("SELECT * FROM dates WHERE year='-1'");
while($r=mysql_fetch_object($q)) {
	$defaultdates[$r->name]=$r;
}

?>
<script type="text/javascript">
$(document).ready(function() {
    $(".date").datepicker({ dateFormat: 'yy-mm-dd', showOn: 'button', buttonText: "<?=i18n("calendar")?>" });
});

</script>
<?

$error_ids = array();

 if($_POST['action']=="save") {
 	if($_POST['savedates']) {
		foreach($_POST['savedates'] as $key=>$val) {
            //put the date and time back together
			$d = mysql_escape_string(stripslashes($val));
            $t  =mysql_escape_string(stripslashes($_POST['savetimes'][$key]));
            $v="$d $t";
			mysql_query("UPDATE dates SET date='$v' WHERE year='".$config['FAIRYEAR']."' AND id='$key'");
		}
	}
	echo happy(i18n("Dates successfully saved"));
 }

 echo "<form method=\"post\" action=\"dates.php\">";
 echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
 echo "<table>";
 echo "<tr><td colspan=\"3\"><h3>".i18n("Dates for fair year %1",array($config['FAIRYEAR']),array("fair year"))."</h3></td></tr>";

/* List the dates in the order we would like them to appear */
$dates  = array('fairdate' => array() , 
		'regopen' => array(), 
		'regclose' => array(), 
		'postparticipants' => array(),
		'postwinners' => array(), 
		'judgeregopen' => array(), 
		'judgeregclose' => array(), 
		'judgescheduleavailable' => array(), 
		'specawardregopen' => array(),
		'specawardregclose' => array());

/* Now copy the SQL data into the above array */
 $q=mysql_query("SELECT * FROM dates WHERE year='".$config['FAIRYEAR']."' ORDER BY date");
 while($r=mysql_fetch_object($q)) {
 	$dates[$r->name]['description'] = $r->description;
	$dates[$r->name]['id'] = $r->id;
	$dates[$r->name]['date'] = $r->date;

	$v = $r->date;
	/* See if $v is something resembling a valid date */
	if(!ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})", $v, $d)) {
		$error_ids[$r->id] = i18n("Invalid date format");
	} else if($d[3]==0 || $d[2]==0 || $d[1]==0) {
		$error_ids[$r->id] = i18n("Invalid date");
	}
 }

function chkafter($d1, $d2) 
{	
	global $dates;
	global $error_ids;

	$id2 = $dates[$d2]['id'];

	/* Parse both dates 1, 2, 3, 4, 5, 6 */
	ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",$dates[$d1]['date'], $p1);
	ereg("([0-9]{4})-([0-9]{1,2})-([0-9]{1,2}) ([0-9]{1,2}):([0-9]{1,2}):([0-9]{1,2})",$dates[$d2]['date'], $p2);

	// int mktime ( [int hour [, int minute [, int second [, int month [, int day [, int year [, int is_dst]]]]]]] )
	$u1 = mktime($p1[4], $p1[5], $p1[6], $p1[2], $p1[3], $p1[1]);
	$u2 = mktime($p2[4], $p2[5], $p2[6], $p2[2], $p2[3], $p2[1]);

	if($u1 > $u2) {
		/* Insert an error for $u2 */
		$error_ids[$id2] = i18n("Must come after \"%1\"", array($dates[$d1]['description']));
	}
}

chkafter('regopen','regclose');
chkafter('judgeregopen','judgeregclose');
chkafter('specawardregopen','specawardregclose');
chkafter('fairdate','postwinners');

 /* And print the table with all the info in the correct order */
foreach($dates as $dn=>$d) {
	if(!$d['id']) {
		$def=$defaultdates[$dn];
		//hmm if we dont have a record for this date this year, INSERT the sql from the default
		mysql_query("INSERT INTO dates (date,name,description,year) VALUES (
			'".mysql_real_escape_string($def->date)."',
			'".mysql_real_escape_string($dn)."',
			'".mysql_real_escape_string($def->description)."',
			'".$config['FAIRYEAR']."'
			)");
		$d['id']=mysql_insert_id();
		$d['description']=$def->description;
		$d['date']=$def->date;
	}
	$e = '';
	if($error_ids[$d['id']]) {
		$e = "<span style=\"color: red;\">*</span> ".$error_ids[$d['id']]."</font>";
	}
    list($_d,$_t)=split(" ",$d['date']);

	echo "<tr><td>".i18n($d['description'])."</td>";
    echo "<td><input size=\"10\" class=\"date\" type=\"text\" name=\"savedates[{$d['id']}]\" value=\"{$_d}\" />";
    echo "<input size=\"10\" type=\"text\" name=\"savetimes[{$d['id']}]\" value=\"{$_t}\" />";
    echo "{$e}</td></tr>";
}
 echo "</table>";
 echo "<input type=\"submit\" value=\"".i18n("Save Dates")."\" />\n";
 echo "</form>";

 send_footer();
?>
