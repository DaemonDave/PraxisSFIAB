<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2008 James Grant <james@lightbox.org>

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

 if($_GET['id']) {
	 $id=intval($_GET['id']);
	 $q=mysql_query("SELECT fundraising_donations.*, sponsors.organization FROM fundraising_donations,sponsors WHERE fundraising_donations.id='$id' AND fundraising_donations.sponsors_id=sponsors.id");
	 $sponsorship=mysql_fetch_object($q);
	 $formaction="sponsorshipedit";
 }
 else
 {
	 $formaction="sponsorshipadd";
	 $fundraising_type=$_GET['fundraising_type'];
 }
?>
<script type="text/javascript">
function typechange() { 
    var t=($("[name=sponsortype]:checked").val());
    if(t=="organization") {
        $("#sponsor_type_organization").show();
        $("#sponsor_type_individual").hide();
    } else {
        $("#sponsor_type_organization").hide();
        $("#sponsor_type_individual").show();
    }


}
</script>
<?
    echo "<form id=\"fundraisingsponsorship\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"$formaction\">";
	echo "<input type=\"hidden\" name=\"fundraising_donations_id\" value=\"$id\">";
	 echo "<table cellspacing=0 cellpadding=0 class=\"tableedit\">";

	if($formaction=="sponsorshipadd") {
        echo "<tr><th>".i18n("Donor Type")."</th>";
        echo "<td>";
         echo "<input onchange=\"typechange()\" type=\"radio\" name=\"sponsortype\" value=\"organization\"> ".i18n("Organization");
         echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;";
         echo "<input onchange=\"typechange()\" type=\"radio\" name=\"sponsortype\" value=\"individual\"> ".i18n("Individual");
         echo "</td></tr>\n";

         echo "<tr><th>".i18n("Donor")."</th>";
         echo "<td>";

	 $q=mysql_query("SELECT * FROM sponsors ORDER BY organization");
	 echo mysql_error();
     echo "<span id=\"sponsor_type_organization\" style=\"display: none;\">";
	 echo "<select name=\"sponsors_id\">";
	 echo "<option value=\"\">".i18n("Choose")."</option>\n";
	 while($r=mysql_fetch_object($q)) {
		 if($r->id==$sponsorship->sponsors_id) $sel="selected=\"selected\""; else $sel="";
		 echo "<option $sel value=\"$r->id\">$r->organization</option>\n";
	 }
	 echo "</select>&nbsp;<a href=\"donors.php?action=add\">".i18n("Add")."</a>\n";
     echo "</span>";


	 $q=mysql_query("SELECT users.*, MAX(year) AS year FROM users WHERE (firstname!='' AND lastname!='') GROUP BY uid HAVING deleted='no' ORDER BY lastname,firstname");
	 echo mysql_error();
     echo "<span id=\"sponsor_type_individual\" style=\"display: none;\">";
	 echo "<select name=\"users_uid\">";
	 echo "<option value=\"\">".i18n("Choose")."</option>\n";
	 while($r=mysql_fetch_object($q)) {
		 if($r->uid==$sponsorship->users_uid) $sel="selected=\"selected\""; else $sel="";
		 echo "<option $sel value=\"$r->uid\">[$r->year][$r->uid] $r->lastname, $r->firstname ($r->email)</option>\n";
	 }
     echo "</span>";



	}
	else {
        echo "<tr><th>".i18n("Donor Type")."</th>";
        echo "<td>";
        if($sponsorship->sponsors_id) echo i18n("Organization");
        else echo i18n("Individual");
        echo "</td></tr>\n";

        echo "<tr><th>".i18n("Donor")."</th>";
        echo "<td>";
        echo $sponsorship->organization;
	}
	 echo "</td></tr>\n";

	 echo "<tr><th>".i18n("Donation Allocation")."</th>";
	 echo "<td>";
	 $q=mysql_query("SELECT * FROM fundraising WHERE year='{$config['FAIRYEAR']}' ORDER BY name");
	 echo mysql_error();
	 echo "<select name=\"fundraising_type\">";
	 echo "<option value=\"\">".i18n("Choose")."</option>\n";
	 while($r=mysql_fetch_object($q)) {
		 if($r->type==$sponsorship->fundraising_type || $r->type==$fundraising_type) $sel="selected=\"selected\""; else $sel="";
		 echo "<option $sel value=\"$r->type\">$r->name</option>\n";
	 }
	 echo "</select>\n";
	 echo "</td></tr>\n";
	 echo "<tr><th>".i18n("Amount")."</th><td><input type=\"text\" name=\"value\" value=\"$sponsorship->value\"></td></tr>\n";

	 echo "<tr><th>".i18n("Status")."</th>";
	 echo "<td>";
	 echo "<select name=\"status\">";
	 echo "<option value=\"\">".i18n("Choose")."</option>\n";
	 $statuses=array("pending","confirmed","received");
	 foreach($statuses AS $status) {
		 if($sponsorship->status==$status) $sel="selected=\"selected\""; else $sel="";
		 echo "<option $sel value=\"$status\">".i18n(ucfirst($status))."</option>\n";
	 }
	 echo "</select>\n";
	 echo "</td></tr>\n";

	 echo "<tr><th>".i18n("Probability")."</th>";
	 echo "<td>";
	 echo "<select name=\"probability\">";
	 echo "<option value=\"\">".i18n("Choose")."</option>\n";
	 $probs=array("25","50","75","90","95","99","100");
	 foreach($probs AS $prob) {
		 if($sponsorship->probability==$prob) $sel="selected=\"selected\""; else $sel="";
		 echo "<option $sel value=\"$prob\">$prob%</option>\n";
	 }
	 echo "</select>\n";
	 echo "</td></tr>\n";

	 echo "</table>\n";
     echo "</form>\n";

?>
