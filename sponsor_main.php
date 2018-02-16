<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2008 Sci-Tech Ontario Inc <info@scitechontario.org>
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
 require_once("common.inc.php");
 require_once("user.inc.php");
 require_once("user_page.inc.php");

 user_auth_required('sponsor');

 send_header("Sponsor Main", array());
 $u=user_load($_SESSION['users_id']);
 //print_r($u);
 $q=mysql_query("SELECT * FROM sponsors WHERE id='".$u['sponsors_id']."'");
 $sponsor=mysql_fetch_object($q);

 //only display the named greeting if we have their name
 echo i18n("Hello <b>%1</b>",array($_SESSION['name']));
 if($sponsor->organization) echo " ".i18n("from %1",array($sponsor->organization));
 echo "<br />";
 echo "<br />";

 echo "<table class=\"adminconfigtable\">";
 echo " <tr>\n";
 echo "  <td><a href=\"user_personal.php\">".theme_icon("edit_profile")."<br />".i18n("Edit My Profile")."</a></td>";
 echo "  <td><a href=\"user_password.php\">".theme_icon("change_password")."<br />".i18n("Change My Password")."</a></td>";
// echo "  <td><a href=\"user_activate.php\">".theme_icon("")."<br />".i18n("Manage My Roles")."</a></td>";
 echo " </tr>\n";
 echo "</table>\n";
 echo "<h2>Your Sponsorships</h2>\n";

	$sq=mysql_query("SELECT fundraising_donations.id, 
						sponsors.organization, 
						fundraising_donations.value, 
						fundraising_donations.status, 
						fundraising_donations.probability,
						fundraising.name
		 FROM fundraising_donations
		 JOIN sponsors ON fundraising_donations.sponsors_id=sponsors.id
		 JOIN fundraising_goals ON fundraising_donations.fundraising_goal=fundraising_goal.goal
		  AND fundraising_donations.fiscalyear='{$config['FISCALYEAR']}'
		  AND fundraising_goals.fiscalyear='{$config['FISCALYEAR']}'
		  AND sponsors.id='".$u['sponsors_id']."'
		  ORDER BY status DESC, probability DESC, organization");
	echo mysql_error();

	echo "<table class=\"tableview\">";
	echo "<tr>";
	echo " <th>".i18n("Sponsorship Category")."</th>\n";
	echo " <th>".i18n("Status")."</th>\n";
	echo " <th>".i18n("Amount")."</th>\n";
	echo " <th>".i18n("Action")."</th>";
	echo "</tr>\n";
	$total=0;
	while($sr=mysql_fetch_object($sq)) {
		echo "<tr id=\"donations_$sr->id\" class=\"fundraising{$sr->status}\">";
		echo "<td>$sr->name</td>\n";
		echo "<td>$sr->status</td>";
		echo "<td style=\"text-align: right\">".format_money($sr->value)."</td>";
		echo "<td align=\"center\">";
		if($sr->status=="confirmed") {
			echo "<a href=\"paynow.php\">Pay Online</a>\n";
		}
		else if($sr->status=="pending") {
			echo "<a onclick=\"return confirmClick('".i18n("By confirming funding, you are guaranteeing that you/your organization will provide said funds to the fair.  Please only confirm the funds here once final approval has been granted by you and/or your organization")."')\" href=\"sponsor_main.php?confirm=$sr->id\">Confirm Funding</a>\n";
		}
		echo "&nbsp;";
		echo "</td>\n";
		echo "</tr>\n";
		$total+=$sr->value;
	}
	echo "</table>\n";
	echo "<br />\n";
	echo "<br />\n";

	echo "<h2>Donor Levels</h2>\n";
	$q=mysql_query("SELECT * FROM fundraising_donor_levels WHERE year='".$config['FISCALYEAR']."' ORDER BY max DESC");
	echo "<table class=\"tableview\">";
	echo "<th></th><th>".i18n("Level")."</th>";
	echo "<th>".i18n("Description / Benefits")."</th>\n";
	echo "<th>".i18n("Range")."</th>\n";
	echo "</tr>\n";
	$first=true;
	while($r=mysql_fetch_object($q)) {
		echo "<tr>";
		echo "<td>";
		if($total>=$r->min && $total<=$r->max) {
			echo "<b>".i18n("You are here");
			echo "&nbsp;&gt;&gt;";
			echo "</b>";
		}
		echo "</td>";
		echo "<td>$r->level</td>";
		echo "<td>$r->description</td>";
		echo "<td>";
		if($first) {
			echo format_money($r->min)."+";
			$first=false;
		}
		else
			echo format_money($r->min)."-".format_money($r->max);
	
		echo "</td>";

	}
	echo "</table>\n";

 send_footer();
?>
