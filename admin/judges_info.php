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
 //this file is meant to be used as a popup from the judging teams page to view the judge info
 //it needs the judge ID passed into it.
 //thus, we do not need the normal header and footer
 require("../questions.inc.php");

 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');

$preferencechoices=array(
	-2=>"Very Low",
	-1=>"Low",
	0=>"Indifferent",
	1=>"Medium",
	2=>"High"
);


$id = intval($_GET['id']);
$judgeinfo = user_load($id);

echo '<div style="text-align:center; padding: 5px;">';
send_popup_header("Judge Information");

 if($id < 1) {
	echo error(i18n("No Judge ID passed to Judges Info"));
	send_popup_footer();
	exit;
 }

?>
	<table class="tableview" style="margin:auto; width:100%; text-align:left">
		<tr>
			<th><?=i18n("First Name");?></th>
			<th><?=i18n("Last Name");?></th>
			<th><?=i18n("Organization");?></th>
		</tr>
		<tr>
			<td><?=$judgeinfo['firstname'];?></td>
			<td><?=$judgeinfo['lastname'];?></td>
			<td><?=$judgeinfo['organization'];?></td>
		</tr>
	</table>

	<table class="tableview" style="margin:auto; width:100%; margin-top: 5px; text-align:left">
		
		<tr>
			<th><?=i18n("Email Address");?>:</th>
			<td><?=$judgeinfo['email'];?></td>
			<th><?=i18n("City");?>:</th>
			<td><?=$judgeinfo['city'];?></td>
		</tr>
		<tr>
			<th><?=i18n("Phone (Home)");?>:</th>
			<td><?=$judgeinfo['phonehome'];?></td>
			<th><?=i18n("Address 1");?>:</th>
			<td><?=$judgeinfo['address'];?></td>
		</tr>
		<tr>
			<th><?=i18n("Phone (Work)");?>:</th>
			<td><?=$judgeinfo['phonework'];?></td>
			<th><?=i18n("Address 2");?>:</th>
			<td><?=$judgeinfo['address2'];?></td>
		</tr>
		<tr>
			<th><?=i18n("Phone (Cell)");?>:</th>
			<td><?=$judgeinfo['phonecell'];?></td>
			<th><?=i18n($config['provincestate']);?>:</th>
			<td><?=$judgeinfo['province'];?></td>
		</tr>
		<tr>
			<th><?=i18n("Languages");?>:</th>
			<td><?=join(', ', $judgeinfo['languages']);?></td>
			<th><?=i18n($config['postalzip']);?>:</th>
			<td><?=$judgeinfo['postalcode'];?></td>
		</tr>
	</table>

<?php

// get their availability
$availabilityText = "";
if($config['judges_availability_enable'] == 'yes'){
	$q = mysql_query("SELECT * FROM judges_availability WHERE users_id=\"{$judgeinfo['id']}\" ORDER BY `start`");
	$sel = array();
	while($r=mysql_fetch_object($q)) {
		$st = substr($r->start, 0, 5);
		$end = substr($r->end, 0, 5);
		$availabilityText .= "<li>$st - $end</li>";
	}
	if(strlen($availabilityText) > 0){
		$availabilityText = '<ul>' . $availabilityText . '</ul>';
	}else{
		$availabilityText = i18n("Unspecified");
	}
}
echo '<div style="text-align:left">';

// is their info complete?
$completeText = $judgeinfo['complete']=="yes" ? "Yes" : "No";

// find out if they've signed up for judging any special awards
$specialAwardsText = "";
if($judgeinfo['special_award_only'] == "yes"){
	$query = "SELECT aa.name AS awardname FROM judges_specialaward_sel jss"
		. " JOIN users ON jss.users_id = users.id"
		. " JOIN award_awards aa ON aa.id = jss.award_awards_id"
		. " WHERE users.id=" . $id;
	$results = mysql_query($query);
	while($record = mysql_fetch_array($results)){
		$awardList[] = $record['awardname'];
	}
	$specialAwardsText .= implode(', ', $awardList);
	
}else{
	$specialAwardsText .= i18n("None");
}

// get their preference for age category
$q=mysql_query("SELECT * FROM projectcategories WHERE year='{$config['FAIRYEAR']}'");

$catPreferenceText =  mysql_error() . "<ul>";
while($r=mysql_fetch_object($q)) {
	$p = intval($judgeinfo['cat_prefs'][$r->id]);
	$catPreferenceText .= "<li><em>" . i18n($r->category)."</em>: {$preferencechoices[$p]}</li>";
}
$catPreferenceText .= "</ul>";

?>

	<table class="tableview" style="margin:auto; width:100%; text-align:left; margin-top:5px;">
	<tr><td>
		<ul>

			<li><strong><?="Complete for {$config['FAIRYEAR']}";?>: </strong>
			<?=$completeText;?></li>

			<li><strong><?=i18n("Special awards");?>: </strong>
			<?=$specialAwardsText;?></li>

			<li><strong><?=i18n("Highest post-secondary degree");?>: </strong>
			<?=$judgeinfo['highest_psd'];?></li>

			<li><strong><?=i18n("Age category preference");?>: </strong>
			<?=$catPreferenceText;?></li>

			<?php
			if($availabilityText != ""){
				echo "<li><strong>" . i18n("Time Availability") . ": </strong>";
				echo $availabilityText . "</li>";
			}
			?>
		</ul>
	</td>
	<td>
		<h3><?=i18n("Areas of Expertise");?></h3>
		<table class="tableview" style="margin:auto;width:100%">
<?php

//grab the list of divisions, because the last fields of the table will be the sub-divisions 
$q=mysql_query("SELECT * FROM projectdivisions WHERE year='{$config['FAIRYEAR']}' ORDER BY id");
$divs=array();
while($r=mysql_fetch_object($q))
{
	$divs[]=$r->id;
	$divnames[$r->id]=$r->division;
}

foreach($divs as $div)
{
	$p = $judgeinfo['div_prefs'][$div];
	echo "<tr><th align=\"right\" >".i18n($divnames[$div]).":</th>";
	echo " <td>$p/5</td>";

	echo "<td>";
	$subq=mysql_query("SELECT * FROM projectsubdivisions WHERE 
				projectdivisions_id='$div' AND year='{$config['FAIRYEAR']}' ORDER BY subdivision");
	$sd = array();
	while($subr=mysql_fetch_object($subq)) {
		if($u['div_prefs_sub'][$subr->id] == 1) {
			$sd[] = $subdivr->subdivision;
		}
	}

	// Only show subdiv if main div >=3 
	if($p >= 3) echo implode(", ",$sd);
	else echo "&nbsp;";

	echo "</td>";
	echo "</tr>";
}
echo "<tr>\n";
echo " <th align=\"right\" valign=\"top\">".i18n("Other").":</th>";
echo " <td colspan=\"2\">{$judgeinfo['expertise_other']}<br />";
echo " </td>\n";
echo "</tr>\n";
?>
		</table>
	</td></tr>
	</table>
<?php

// get the judge's special award info
//print_r($judgeinfo);
echo '</div></div>';

/*
 send_popup_header("Judge Information");
*/


/*
echo "<tr>\n";
echo " <th valign=\"top\" align=\"right\" colspan=\"2\">".i18n("Time Availability").":</th><td colspan=\"2\">";
$q = mysql_query("SELECT * FROM judges_availability WHERE users_id=\"{$judgeinfo['id']}\" ORDER BY `start`");
$sel = array();
while($r=mysql_fetch_object($q)) {
	$st = substr($r->start, 0, 5);
	$end = substr($r->end, 0, 5);
	echo "$st - $end<br />";
}
echo "</td></tr>";
echo "<tr>";
*/



//send_popup_footer();
?>
