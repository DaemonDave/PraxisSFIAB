<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
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
 echo "<br />\n";

		//$q=mysql_query("SELECT * FROM award_sponsors WHERE year='".$config['FAIRYEAR']."' ORDER BY organization");
		//we want to show all years, infact that year field probably shouldnt even be there.
		$sql="";
		if($_POST['search']) $sql.=" AND organization LIKE '%".mysql_real_escape_string($_POST['search'])."%' ";
		if(count($_POST['donortype'])) {
			$sql.=" AND (0 ";
			foreach($_POST['donortype'] AS $d) {
				$sql.=" OR donortype='$d'";
			}
			$sql.=") ";
		}
		$query="SELECT * FROM sponsors WHERE 1 $sql ORDER BY organization";
//		echo "query=$query";
		$q=mysql_query($query);

		$thisyear=$config['FISCALYEAR'];
		$lastyear=$config['FISCALYEAR']-1;
		$rows=array();

		while($r=mysql_fetch_object($q))
		{
			$cq=mysql_query("SELECT SUM(value) AS total FROM fundraising_donations WHERE sponsors_id='$r->id' AND status='received' AND fiscalyear='$thisyear'");
			$cr=mysql_fetch_object($cq);
			$thisyeartotal=$cr->total;
			$cq=mysql_query("SELECT SUM(value) AS total FROM fundraising_donations WHERE sponsors_id='$r->id' AND status='received' AND fiscalyear='$lastyear'");
			$cr=mysql_fetch_object($cq);
			$lastyeartotal=$cr->total;
			if($lastyeartotal)
				$change=round(($thisyeartotal-$lastyeartotal)/$lastyeartotal*100);
			else
				$change="N/A";
			$rows[]=array("id"=>$r->id, "name"=>$r->organization, "thisyeartotal"=>$thisyeartotal, "lastyeartotal"=>$lastyeartotal, "change"=>$change);
		}
		$thisyearsort=array();
		if(!$_POST['order']) {
			//if order is not given, lets order by donation amount this year
			foreach($rows AS $key=>$val) {
				$thisyearsort[$key]=$val['thisyeartotal'];
			}
			array_multisort($thisyearsort,SORT_DESC,$rows);
		}

		if($_POST['limit']) {
			$limit=$_POST['limit'];
		}
		else {
			$limit=10;
			echo  "<h4>".i18n("Top 10 donors this year")."</h4>";
		}

		echo "<table class=\"tableview\">";
		echo "<thead>";
		echo "<tr>";
		echo " <th>".i18n("Donor/Sponsor")."</th>";
		echo " <th>".i18n("Total $ this year")."</th>";
		echo " <th>".i18n("Total $ last year")."</th>";
		echo " <th>".i18n("% change")."</th>";
		echo "</tr>";
		echo "</thead>\n";


		$x=0;
		foreach($rows AS $r) {
			echo "<tr>\n";
            $eh="style=\"cursor:pointer;\" onclick=\"open_editor({$r['id']});\"";
			echo " <td $eh>{$r['name']}</td>\n";
			echo " <td style=\"text-align: right;\">";
			echo format_money($r['thisyeartotal']);
			echo "</td>\n";
			echo " <td style=\"text-align: right;\">";
			echo format_money($r['lastyeartotal']);
			echo "</td>\n";
			if(is_numeric($r['change'])) {
				$n=$r['change']/2+50;
				if($n<0) $n=0;
				if($n>100) $n=100;
				$col="color: ".colour_to_percent($n);
				$sign="%";
			}
			else{
				 $col=""; $sign=""; }
			echo " <td style=\"text-align: center; $col\">";
			echo $r['change'].$sign;
			echo "</td>\n";
			echo "</tr>\n";

			$x++;
			if($x==$limit)
				break;
		}

		echo "</table>\n";

		
?>
