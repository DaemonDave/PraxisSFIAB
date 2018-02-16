<?
if($_GET['action']=="fundraisingmain") {

//this table is eventually going to be massive, and probably not in a tableview format, it'll show goals as well as all ongoing fund pledges, probabilities, etc as well as over/under, etc, all prettily colour coded.. basically a good overview of the total fundraising status of the fair.
 $q=mysql_query("SELECT * FROM fundraising_goals WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY system DESC,goal");
 echo "<table class=\"fundraisingtable\">";

 while($r=mysql_fetch_object($q)) {
	 echo "<tr>";
	 echo "<th><a title=\"".i18n("Edit fund details")."\" onclick=\"return popup_fund_editor('fundraising_types.php?id=$r->id')\" href=\"#\"><img border=\"0\" src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\"></a>";
	 if($r->system=="no") {
		//echo "<a title=\"".i18n("Remove Fund")."\" onclick=\"return confirmClick('Are you sure you want to remove this fund and all sponsorships inside it?')\" href=\"fundraising.php?action=funddelete&delete=$r->id\">";
        echo "<img style=\"cursor:pointer\" onclick=\"return delete_fund($r->id)\" border=\"0\" src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\">";
       // echo "</a>";
	 }

	 echo "</th>\n";
	 echo "<th colspan=\"5\">".i18n($r->name)."</th>\n";
	 echo "<th style=\"text-align: right\"><nobr>".format_money($r->budget)."</nobr></th>\n";
 	 echo "</tr>\n";

	if($r->type=="general") 
		$orsql.="OR fundraising_type IS NULL";

	$typetotal=0;
	$typeprobtotal=0;
	$sq=mysql_query("
            SELECT fundraising_donations.id, sponsors.organization AS name, fundraising_donations.value, fundraising_donations.status, fundraising_donations.probability
		 FROM fundraising_donations
		 JOIN sponsors ON fundraising_donations.sponsors_id=sponsors.id
		  WHERE (fundraising_donations.fundraising_goal='$r->goal' $orsql) 
		  AND fundraising_donations.fiscalyear='{$config['FISCALYEAR']}'

          UNION

        SELECT fundraising_donations.id, CONCAT(users.firstname,' ',users.lastname) AS name, fundraising_donations.value, fundraising_donations.status, fundraising_donations.probability
		 FROM fundraising_donations
		 JOIN users ON fundraising_donations.users_uid=users.uid
		  WHERE (fundraising_donations.fundraising_goal='$r->goal' $orsql) 
		  AND fundraising_donations.fiscalyear='{$config['FISCALYEAR']}'

		  ORDER BY status DESC, probability DESC, name
            ");
    echo mysql_error();
	while($sr=mysql_fetch_object($sq)) {
		echo "<tr id=\"sponsorships_$sr->id\" class=\"fundraising{$sr->status}\">";
		echo "<td>";
        echo "<img style=\"cursor:pointer;\" onclick=\"delete_sponsorship($sr->id)\" border=\"0\" src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\">";
		echo "</td>";
		echo "<td style=\"cursor: pointer;\" onclick=\"popup_sponsorship_editor('fundraising_sponsorship.php?id=$sr->id&fundraising_type=$r->type')\">";
        echo "$sr->name</td>\n";
		echo "<td>$sr->status</td>";
		echo "<td>";
		if($sr->status=="pending") {
			echo "$sr->probability%";
			echo "</td>";
			echo "<td><nobr>".format_money($sr->value)."</nobr></td>";
		}
		else
			echo "</td><td></td>\n";

		$probval=$sr->probability/100*$sr->value; 
		echo "<td style=\"text-align: right\"><nobr>".format_money($probval)."</nobr></td>";
		echo "<td></td>\n";
		echo "</tr>\n";
		$typeprobtotal+=$probval;
		$typetotal+=$sr->value;
	}
	 echo "<tr>";
	 echo "<td><a onclick=\"return popup_sponsorship_editor('fundraising_sponsorship.php?fundraising_type=$r->type')\" href=\"#\">add</a></td>";
	 echo "<td colspan=\"3\" style=\"text-align: right; font-weight: bold;\">".i18n("%1 Total",array($r->name),array("Fundraising type total, eg) Award Sponsorship Total"))."</td>\n";
	 echo "<td style=\"font-weight: bold; text-align: right;\"><nobr>".format_money($typetotal)."</nobr></td>\n";
	 echo "<td style=\"font-weight: bold; text-align: right;\"><nobr>".format_money($typeprobtotal)."</nobr></td>\n";
	$typediff=$typeprobtotal-$r->goal;
	 echo "<td style=\"font-weight: bold; text-align: right;\"><nobr>".format_money($typediff)."</nobr></td>\n";
 	 echo "</tr>\n";

	$totalgoal+=$r->goal;
	$totaldiff+=$typediff;
	echo "<tr><td colspan=\"7\">&nbsp;</td></tr>\n";
 }
 echo "<tr>";
 echo "<td colspan=\"2\"><a onclick=\"return popup_fund_editor('fundraising_types.php')\" href=\"#\">add fund type</a></td>";
 echo "<td colspan=\"4\" style=\"font-weight: bold; text-align: right;\">".i18n("Total Net Position")."</td><td style=\"text-align: right; font-weight: bold;\">".format_money($totaldiff)."</td></tr>\n";
 echo "</table>\n";
 exit;
}

