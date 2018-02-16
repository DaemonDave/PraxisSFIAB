<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2007 James Grant <james@lightbox.org>

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
 user_auth_required('committee', 'admin');
 require_once('curl.inc.php');
 require_once('awards.inc.php');



switch($_GET['action']) {
case 'check':
	$fairs_id = intval($_GET['fairs_id']);
	$q=mysql_query("SELECT * FROM fairs WHERE id='$fairs_id'");
	$fair=mysql_fetch_assoc($q);
	if(!($fair['username'] && $fair['password'])) {
		echo error(i18n("Username and Password are not set for source '%1'.  Please set them in the SFIAB Configuration/External Award Sources editor first",array($r->name)));
		return;
	}

	echo i18n("Checking %1 for awards...",array($fair['name']));
	echo "<br />";

	if($fair['type'] == 'ysc') {
		$req=array("awardrequest"=>array(
				"username"=>$fair['username'],
				"password"=>$fair['password'],
				"year"=>$config['FAIRYEAR'],
				)
			);
	} else {
		$req['getawards'] = array('year'=>$config['FAIRYEAR']);
	}

	$data = curl_query($fair, $req);

	if($data['error'] != 0) {
		echo error("Server said: {$data['message']}<br />");
		send_footer();
		exit;
	}
	echo notice(i18n('Server said: Success'));
//            echo "sending [".nl2br(htmlspecialchars($xmldata))."]";

	$keys=array_keys($data);
	if(!array_key_exists('awards', $data)) {
		echo error(i18n("Invalid XML response.  Expecting '%1' in '%2'",array("awards",join(',',array_keys($data)))));
//				echo "response=".print_r($data);
		return;
	}			

	//get a list of all the existing awards for this external source
	$aq=mysql_query("SELECT * FROM award_awards WHERE award_source_fairs_id='$fairs_id' AND year='{$config['FAIRYEAR']}'");
	$existingawards=array();
	while($ar=mysql_fetch_object($aq)) {
		$existingawards[$ar->id] = true;
	}
	
	echo "<i>";
	$awards = $data['awards'];
	$postback = $data['postback'];
	echo i18n("Postback URL: %1",array($postback))." <br />";

	$numawards=is_array($awards) ? count($awards) : 0;
	echo i18n("Number of Awards: %1",array($numawards))." <br />";

	if($numawards == 0) {
		echo i18n('No awards to process').'</i> <br />';
		return;
	}

	$divs = projectdivisions_load();
	$cats = projectcategories_load();

	foreach($awards as $award) {
		$identifier=$award['identifier'];
		$year=$award['year'];
		echo i18n("Award Identifier: %1",array($identifier))." &nbsp; ";
		echo i18n("Award Year: %1",array($year))."<br />";
		echo i18n("Award Name: %1",array($award['name_en']))."<br />";

		if($year != $config['FAIRYEAR']) {
			echo error(i18n("Award is not for the current fair year... skipping"));
			echo '<br />';
			continue;
		}

		$tq=mysql_query("SELECT * FROM award_awards WHERE 
					external_identifier='$identifier' AND 
					award_source_fairs_id='$fairs_id' AND 
					year='$year'");
		if(mysql_num_rows($tq)  == 0) {
			/* Award doesn't exist, create it, then update it with the common code below */
			mysql_query("INSERT INTO award_awards (award_types_id,
						year, external_identifier,
						award_source_fairs_id) 
					VALUES (2,'{$year}',
					'".mysql_escape_string($identifier)."',
					'$fairs_id')");
			$award_id=mysql_insert_id();
			/* By default make all divs/cats eligible */
			foreach($divs as $id=>$d) 
				mysql_query("INSERT INTO award_awards_projectdivisions(award_awards_id,projectdivisions_id,year) VALUES ('$award_id','$id','{$config['FAIRYEAR']}')");
			foreach($cats as $id=>$c) 
				mysql_query("INSERT INTO award_awards_projectcategories(award_awards_id,projectcategories_id,year) VALUES ('$award_id','$id','{$config['FAIRYEAR']}')");
		} else {
			echo i18n("Award already exists, updating info")."<br />";
			$awardrecord=mysql_fetch_object($tq);
			$award_id = $awardrecord->id;
		}

		//remove it from the existingawards list
		unset($existingawards[$award_id]);

		//check if the sponsor exists, if not, add them
		$sponsor_str = mysql_escape_string($award['sponsor']);
		$sponsorq=mysql_query("SELECT * FROM sponsors WHERE organization='$sponsor_str'");
		if($sponsorr=mysql_fetch_object($sponsorq)) {
			$sponsor_id=$sponsorr->id;
		} else {
			mysql_query("INSERT INTO sponsors (organization,year,notes) 
				VALUES ('$sponsor_str','$year','".mysql_escape_string("Imported from external source: $r->name")."')");
			echo mysql_error();
			$sponsor_id=mysql_insert_id();
		}


		$self_nominate = ($award['self_nominate'] == 'yes') ? 'yes' : 'no';
		$schedule_judges = ($award['schedule_judges'] == 'yes') ? 'yes' : 'no';
		mysql_query("UPDATE award_awards SET
				sponsors_id='$sponsor_id',
				name='".mysql_escape_string($award['name_en'])."',
				criteria='".mysql_escape_string($award['criteria_en'])."',
				external_postback='".mysql_escape_string($postback)."',
				external_register_winners='".(($award['external_register_winners']==1)?1:0)."',
				external_additional_materials='".(($award['external_additional_materials']==1)?1:0)."',
				self_nominate='$self_nominate',
				schedule_judges='$schedule_judges'
			WHERE 
				id='$award_id' 
				AND external_identifier='".mysql_escape_string($identifier)."' 
				AND year='$year'
		");
		echo mysql_error();

		//update the prizes
		$prizes = $award['prizes'];
		if(!is_array($prizes)) {
			continue;
		}

		echo i18n("Number of prizes: %1",array(count($prizes)))."<br />";
		/* Get existing prizes */
		$pq=mysql_query("SELECT * FROM award_prizes WHERE award_awards_id='$award_id'");
		$existingprizes=array();
		while($pr=mysql_fetch_assoc($pq)) 
			$existingprizes[$pr['prize']]=$pr;


		/* Iterate over the downloaded pizes */
		foreach($prizes AS $prize) {
			//if it doesn't exist, add it
			if(!array_key_exists($prize['prize_en'],$existingprizes)) {
				/* Add a base entry, then update it below, yes it's two sql queries,
				 * but it's much shorter code, and means changing things in only
				 * one spot */
				echo "&nbsp;".i18n("Adding prize %1",array($prize['prize_en']))."<br />";
				$p = mysql_escape_string(stripslashes($prize['prize_en']));
				mysql_query("INSERT INTO award_prizes (award_awards_id,prize,year,external_identifier)
					VALUES ('$award_id','$p','$year','$p')");
				$prize_id = mysql_insert_id();
			} else {
				$ep=$existingprizes[$prize['prize_en']];
				echo "&nbsp;".i18n("Updating prize %1",array($ep['prize']))."<br />";
				$prize_id = $ep['id'];
				//remove it from the list
				unset($existingprizes[$ep['prize']]);
			}

			if(!array_key_exists('identifier', $prize)) $prize['identifier'] = $prize['prize_en'];

			mysql_query("UPDATE award_prizes SET 
					cash='".intval($prize['cash'])."',
					scholarship='".intval($prize['scholarship'])."',
					value='".intval($prize['value'])."',
					prize='".mysql_escape_string($prize['prize_en'])."',
					number='".intval($prize['number'])."',
					`order`='".intval($prize['ord'])."',
					external_identifier='".mysql_real_escape_string(stripslashes($prize['identifier']))."',
					trophystudentkeeper='".intval($prize['trophystudentkeeper'])."',
					trophystudentreturn='".intval($prize['trophystudentreturn'])."',
					trophyschoolkeeper='".intval($prize['trophyschoolkeeper '])."',
					trophyschoolreturn='".intval($prize['trophyschoolreturn'])."'
				WHERE
					id='$prize_id'");

			echo mysql_error();
			//FIXME: update the translations
		}

		/* Delete local entries that weren't downloaded */
		foreach($existingprizes AS $ep) {
			echo "&nbsp;".i18n("Removing prize %1",array($ep['prize']))."<br />";
			award_prize_delete($ep['id']);
		}
	}
	echo "<br />";


	//remove any awards that are left in the $existingawards array, they must have been removed from the source
	foreach($existingawards AS $aid=>$val) {
		echo i18n("Removing award id %1 that was removed from external source",array($aid))."<br />";
		award_delete($aid);
	}
	
	echo "</i>";
	exit;
}

send_header("Download Awards",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Awards Main' => 'admin/awards.php' ));

?>
<script type="text/javascript">

function award_download(id)
{
	if(id == -1) return false;
	$("#award_download_status").load("<?=$_SERVER['PHP_SELF']?>?action=check&fairs_id="+id);
}
</script>

<?


if(!function_exists('curl_init')) {
	echo error(i18n("CURL Support Missing"));
	echo notice(i18n("Your PHP installation does not support CURL.  You will need to have CURL support added by your system administrator before being able to access external award sources"));
	$links=false;
} else {
	$links=true;
}

?>
<table class="tableview"><thead>
<tr><th><?=i18n("Source Name")?></th>
<th><?=i18n("Source Location URL")?></th>
<th><?=i18n("Check")?></th>
</tr></thead>
<?

$q=mysql_query("SELECT * FROM fairs WHERE enable_awards='yes' ORDER BY name");
while($r=mysql_fetch_object($q)) {
	echo "<tr>";
	echo "<td>{$r->name}</td>\n";
	echo "<td>{$r->url}</td>";
	echo "<td align=\"center\">";
	if($links)
		echo "<a href=\"#\" onclick=\"award_download({$r->id})\">".i18n("check")."</a>";
	else
		echo "n/a";
	echo "</td>";
	echo "</tr>";
//	$checkurl.="&check[]={$r->id}";
 }
/*
 if($links)
	 echo "<a href=\"award_download.php?action=check$checkurl\">".i18n("Check all sources")."</a>";
*/
?>
 </table>
 <br />
 <div id="award_download_status"></div>

<?
 send_footer();
?>
