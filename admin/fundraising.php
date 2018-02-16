<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2009 James Grant <james@lightbox.org>

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

if($_GET['action']=="refresh") {
?>

<h3><?=i18n("Fundraising Purposes and Progress Year to Date")?></h3>
<?
$q=mysql_query("SELECT * FROM fundraising_goals WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY deadline");

?>
<table class="tableview">
 <thead>
 <tr>
	<th><?=i18n("Purpose")?></th>
	<th><?=i18n("Goal")?></th>
	<th><?=i18n("Amount Received")?></th>
	<th><?=i18n("% to Budget")?></th>
	<th><?=i18n("Deadline")?></th>
 </tr>
 </thead>
 <?
 while($r=mysql_fetch_object($q)) {
     //lookup all donations made towards this goal
     $recq=mysql_query("SELECT SUM(value) AS received FROM fundraising_donations WHERE fundraising_goal='$r->goal' AND fiscalyear='{$config['FISCALYEAR']}' AND status='received'");
     echo mysql_error();
     $recr=mysql_fetch_object($recq);
     $received=$recr->received;
     if($r->budget)
         $percent=round($received/$r->budget*100,1);
     else
         $percent=0;

     echo "<tr><td>$r->name</td>";
     echo "<td style=\"text-align: right;\">".format_money($r->budget,false)."</td>";
     echo "<td style=\"text-align: right;\">".format_money($received,false)."</td>";
     $col=colour_to_percent($percent);
     echo "<td style=\"text-align: center; background-color: $col;\">{$percent}%</td>";
     echo "<td>".format_date($r->deadline)."</td></tr>\n";
 }
 ?>
</table>
<br />

<h3><?=i18n("Current Appeals")?></h3>
<table class="tableview">
 <thead>
 <tr>
	<th><?=i18n("Name")?></th>
	<th><?=i18n("Type")?></th>
	<th><?=i18n("Start Date")?></th>
	<th><?=i18n("End Date")?></th>
	<th><?=i18n("Target($)")?></th>
	<th><?=i18n("Received")?></th>
	<th><?=i18n("% to Budget")?></th>
	<th><?=i18n("Purpose")?></th>
 </tr>
 </thead>
<?
 $q=mysql_query("SELECT * FROM fundraising_campaigns WHERE fiscalyear='{$config['FISCALYEAR']}'");

 while($r=mysql_fetch_object($q)) {

    $goalq=mysql_query("SELECT * FROM fundraising_goals WHERE goal='{$r->fundraising_goal}' AND fiscalyear='{$config['FISCALYEAR']}'");
    $goalr=mysql_fetch_object($goalq);
    $recq=mysql_query("SELECT SUM(value) AS received FROM fundraising_donations WHERE fundraising_campaigns_id='$r->id' AND fiscalyear='{$config['FISCALYEAR']}' AND status='received'");
    echo mysql_error();
    $recr=mysql_fetch_object($recq);
    $received=$recr->received;
    if($r->target)
        $percent=round($received/$r->target*100,1);
    else
        $percent=0;
    $col=colour_to_percent($percent);

    echo "<tr style=\"cursor:pointer;\" onclick=\"window.location.href='fundraising_campaigns.php?manage_campaign=$r->id'\">\n";
    echo "  <td>$r->name</td>\n";
    echo "  <td>$r->type</td>\n";
    echo "  <td>".format_date($r->startdate)."</td>\n";
    echo "  <td>".format_date($r->enddate)."</td>";
    echo "  <td style=\"text-align: right;\">".format_money($r->target,false)."</td>\n";
    echo "  <td style=\"text-align: right;\">".format_money($received,false)."</td>\n";
    echo "  <td style=\"text-align: center; background-color: $col;\">{$percent}%</td>\n";
    echo "  <td>$goalr->name</td>";
    echo "</tr>\n";
 }
 ?>
 </tr>
</table>
<script type="text/javascript"> $('.tableview').tablesorter();</script>
<br />

<form id="thankyouform" method="post" action="fundraising.php">
<h3><?=i18n("To Do List")?></h3>
<h4><?=i18n("Thank You's")?></h4>
<?
$q=mysql_query("SELECT id,value, thanked, status, sponsors_id, datereceived,
	DATE_ADD(datereceived, INTERVAL 1 MONTH) < NOW() AS onemonth,
	DATE_ADD(datereceived, INTERVAL 2 MONTH) < NOW() AS twomonth
    FROM fundraising_donations
    WHERE thanked='no' AND status='received'
    AND fiscalyear='{$config['FISCALYEAR']}' 
    ORDER BY datereceived
    ");
echo mysql_error();

if(mysql_num_rows($q)) {
    echo "<table class=\"tableview\">";
    echo "<thead><tr><th>".i18n("Name")."</th>\n";
    echo "<th>".i18n("Date Received")."</th>\n";
    echo "<th>".i18n("Amount")."</th>\n";
    echo "<th>".i18n("Generate Thank You")."</th>\n";
    echo "<th>".i18n("Thanked")."</th>\n";
    echo "</tr></thead>\n";

    while($r=mysql_fetch_object($q)) {
        $dq=mysql_query("SELECT organization AS name FROM sponsors WHERE id='$r->sponsors_id'");
        $dr=mysql_fetch_object($dq);
		if($r->twomonth) $s="style=\"background-color: ".colour_to_percent(0).";\"";
		else if($r->onemonth) $s="style=\"background-color: ".colour_to_percent(50).";\"";
		else $s="";

		$u=getUserForSponsor($r->sponsors_id);

        echo "<tr $s>";
        echo " <td>$dr->name</td>";
        echo " <td>".format_date($r->datereceived)."</td>";
        echo " <td style=\"text-align: right;\">".format_money($r->value)."</td>";
        echo " <td style=\"text-align: center;\">";
		if($u) {
			echo "<a href=\"#\" onclick=\"return opencommunicationsender('{$u['uid']}','fundraising_thankyou_template');\">".i18n("Generate Thank You")."</a></td>";
		} else {
			echo i18n("No contact");
		}
		echo "<td align=\"center\"><input style=\"padding: 0px; margin: 0px;\" type=\"checkbox\" name=\"thanked[]\" value=\"$r->id\" onclick=\"return thanked($r->id)\"></td>\n";
        echo "</tr>\n";
    }
    echo "</table>\n";
}else {
     echo i18n("No Thank You's pending");
    echo "<br />\n";
}
?>
</form>

<br />
<h4><?=i18n("Receipts to Issue")?></h4>
<?
$q=mysql_query("SELECT value, receiptrequired, receiptsent, status, sponsors_id, datereceived,
	DATE_ADD(datereceived, INTERVAL 1 MONTH) < NOW() AS onemonth,
	DATE_ADD(datereceived, INTERVAL 2 MONTH) < NOW() AS twomonth
    FROM fundraising_donations
    WHERE (receiptrequired='yes' AND receiptsent='no') AND status='received'
    AND fiscalyear='{$config['FISCALYEAR']}' 
    ORDER BY datereceived
    ");
echo mysql_error();
if(mysql_num_rows($q)) {
    echo "<table class=\"tableview\">";
    echo "<tr><th>".i18n("Name")."</th>\n";
    echo "<th>".i18n("Date Received")."</th>\n";
    echo "<th>".i18n("Amount")."</th>\n";
    echo "<th>".i18n("Generate Receipt")."</th>\n";
    echo "</tr>\n";

    while($r=mysql_fetch_object($q)) {
        $dq=mysql_query("SELECT organization AS name FROM sponsors WHERE id='$r->sponsors_id'");
        $dr=mysql_fetch_object($dq);
		if($r->twomonth) $s="style=\"background-color: ".colour_to_percent(0).";\"";
		else if($r->onemonth) $s="style=\"background-color: ".colour_to_percent(50).";\"";
		else $s="";

        echo "<tr $s>";
        echo " <td>$dr->name</td>";
        echo " <td>".format_date($r->datereceived)."</td>";
        echo " <td style=\"text-align: right;\">".format_money($r->value)."</td>";
        echo " <td style=\"text-align: center;\">";
        echo "<a href=\"#\" onclick=\"return false;\">".i18n("Generate Receipt")."</a></td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
}else {
     echo i18n("No Receipts pending");
    echo "<br />\n";
}
?>

<br />
<h4><?=i18n("Appeal Follow-Ups")?></h4>
<?
$q=mysql_query("SELECT * FROM fundraising_campaigns WHERE followupdate>=NOW() ORDER BY followupdate LIMIT 5");
echo mysql_error();
if(mysql_num_rows($q)) {
    echo "<table class=\"tableview\">";
    echo "<thead><tr>";
    echo " <th>".i18n("Appeal")."</th>\n";
    echo " <th>".i18n("Start Date")."</th>\n";
    echo " <th>".i18n("Follow-Up Date")."</th>\n";
    echo "</tr></thead>\n";
    while($r=mysql_fetch_object($q)) {
        echo "<tr><td>$r->name</td><td>".format_date($r->startdate)."</td><td>".format_date($r->followupdate)."</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo i18n("No appeal follow-ups");
    echo "<br />\n";
}

?>
<br />
<h4>Upcoming Proposals</h4>
<?
$q=mysql_query("SELECT * FROM sponsors WHERE fundingselectiondate>=NOW() OR proposalsubmissiondate>=NOW() ORDER BY fundingselectiondate LIMIT 5");
echo mysql_error();
if(mysql_num_rows($q)) {
    echo "<table class=\"tableview\">";
    echo "<tr>";
    echo " <th>".i18n("Name")."</th>\n";
    echo " <th>".i18n("Proposal Submission Date")."</th>\n";
    echo " <th>".i18n("Funding Selection Date")."</th>\n";
    echo "</tr>\n";
    while($r=mysql_fetch_object($q)) {
        echo "<tr><td>$r->organization</td>";
        echo "<td>".format_date($r->proposalsubmissiondate)."</td>";
        echo "<td>".format_date($r->fundingselectiondate)."</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo i18n("No proposals upcoming");
}

	exit;
}
else if (count($_POST['thanked'])) {
	foreach($_POST['thanked'] AS $t) {
		mysql_query("UPDATE fundraising_donations SET thanked='yes' WHERE id='$t'");
	}
}
 
send_header("Fundraising",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Fundraising' => 'admin/fundraising.php'),
            "fundraising"
			);
?>
<script type="text/javascript">
$(document).ready(function() {
	refreshDashboard();
});

function refreshDashboard() {
	$("#dashboard").load("fundraising.php?action=refresh");
}

function thanked() {
	$.post("fundraising.php",$("#thankyouform").serializeArray(),function() {
		refreshDashboard();
	});
}

//key is initial or followup
//start is either 'new' to start with a blank, or 'existing' to load an existing email to start from
function opencommunicationsender(uid,template) {
	$("#debug").load("communication.php?action=dialog_sender&uid="+uid+"&template=fundraising_thankyou_template",null,function() {
	});
	return false;
}

</script>

<div id="dashboard"></div>

<?
 send_footer();
?>
