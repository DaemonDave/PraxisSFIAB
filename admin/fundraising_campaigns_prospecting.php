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
 require("fundraising_common.inc.php");

    $userslist=array();
    $otherlist=array();

if($_POST['donortype']=="organization") {
    $q=mysql_query("SELECT id, organization AS name, address, address2, city, province_code, postalcode FROM sponsors ORDER BY name");
    echo mysql_error();

    if(!$_POST['contacttype'])
        $contacttype=array("primary","secondary");
    else 
        $contacttype=$_POST['contacttype'];

    $primary="";
    while($r=mysql_fetch_object($q)) {
        foreach($contacttype AS $ct) {
            switch($ct) {
                case "primary":
                    $primary="yes";
                    break;
                case "secondary":
                    $primary="no";
                    break;
            }
			$cq = mysql_query("SELECT *,MAX(year) FROM users LEFT JOIN users_sponsor ON users_sponsor.users_id=users.id
			WHERE 
			sponsors_id='" . $r->id . "' 
			AND `primary`='$primary' 
			AND types LIKE '%sponsor%'
			GROUP BY uid
			HAVING deleted='no' 
			ORDER BY users_sponsor.primary DESC,lastname,firstname
			");

            echo mysql_error();
            while($cr=mysql_fetch_object($cq)) {
                if(!$userslist[$cr->uid])
                    $userslist[$cr->uid]=user_load($cr->users_id);
            }
        }
    }
}
else if($_POST['donortype']=="individual") {

    if(!$_POST['individual_type'])
        $individual_type=array("judge","teacher","sciencehead","principal","parent","mentor","committee","volunteer","students");
    else 
        $individual_type=$_POST['individual_type'];

    foreach($individual_type AS $t) {
		$query="SELECT *,MAX(year) FROM users WHERE types LIKE '%$t%' GROUP BY uid HAVING deleted='no' ORDER BY lastname,firstname";
		$q=mysql_query($query);
		echo mysql_error();
		while($r=mysql_fetch_object($q)) {
			if(!$userslist[$r->uid])
				$userslist[$r->uid]=user_load_by_uid($r->uid);
        }
    }
}

//okie dokie, now we need to filter ou the list on the other criteria
if($_POST['emailaddress']) {
	$emailaddress=$_POST['emailaddress'];
}
else {
	$emailaddress=array("available","not available");
}

if(count($emailaddress)==1) {
	$emailavailablelist=array();
	$emailnotavailablelist=array();

	foreach($userslist AS $uid=>$u) {
		if($u['email'])
			$emailavailablelist[$uid]=$u;
		else
			$emailnotavailablelist[$uid]=$u;
	}

	if($emailaddress[0]=="available") {
		$userslist=$emailavailablelist;
	} else {
		$userslist=$emailnotavailablelist;
	}
}


if($_POST['donationhistory']) {
	$donationhistory=$_POST['donationhistory'];
}
else {
	$donationhistory=array("never","past","lastyear","thisyear");
}

/*
FIXME: put this back in as it would eliminate a lot of processing

if(in_array('never',$donationhistory) && in_array('past',$donationhistory)) {
	//these cancel eachother out basically, so include everyone
	unset($donationhistory[array_search("never",$donationhistory)]);
	unset($donationhistory[array_search("past",$donationhistory)]);
}
*/

$neverlist=$userslist;
$pastlist=$userslist;
$lastyearlist=$userslist;
$thisyearlist=$userslist;

	//if they dont have a sponsors id, then they,ve never donated for sure, so keep them
		//if they DO have a sponsors id, we need to check if tere is a donation record for them
		//and if so, remove them if there is because tey have donated in the past

	foreach($neverlist AS $uid=>$u) {
		if($u['sponsors_id']) {
			$q=mysql_query("SELECT * FROM fundraising_donations WHERE status='received' AND sponsors_id='{$u['sponsors_id']}'");
			if(mysql_num_rows($q)) {
		//		echo "removing $uid because they have donated in the past <br />";
				unset($neverlist[$uid]);
			}
		}
	}

//if they dont have a sponsors id, then they,ve never donated for sure, get rid of them
	//if they DO have a sponsors id, we need to check if tere is a donation record for them
	//and if not remove them if there is because tey have not donated in the past
	
	foreach($pastlist AS $uid=>$u) {
		if($u['sponsors_id']) {
			$q=mysql_query("SELECT * FROM fundraising_donations WHERE status='received' AND sponsors_id='{$u['sponsors_id']}'");
			if(!mysql_num_rows($q)) {
		//		echo "removing $uid because they have NOT donated in the past <br />";
				unset($pastlist[$uid]);
			}
		}
		else {
		//		echo "removing $uid because they have NOT donated in the past <br />";
				unset($pastlist[$uid]);

		}
	}

	$lastyear=$config['FISCALYEAR']-1;

	foreach($lastyearlist AS $uid=>$u) {
		if($u['sponsors_id']) {
			$q=mysql_query("SELECT * FROM fundraising_donations WHERE status='received' AND sponsors_id='{$u['sponsors_id']}' AND fiscalyear='$lastyear'");
			if(!mysql_num_rows($q)) {
		//		echo "removing $uid because they have NOT donated last year <br />";
				unset($lastyearlist[$uid]);
			}
		}
		else {
		//		echo "removing $uid because they have NOT donated last year <br />";
				unset($lastyearlist[$uid]);

		}
	}

	foreach($thisyearlist AS $uid=>$u) {
		if($u['sponsors_id']) {
			$q=mysql_query("SELECT * FROM fundraising_donations WHERE status='received' AND sponsors_id='{$u['sponsors_id']}' AND fiscalyear='{$config['FISCALYEAR']}'");
			if(!mysql_num_rows($q)) {
		//		echo "removing $uid because they have NOT donated this year <br />";
				unset($thisyearlist[$uid]);
			}
		}
		else {
		//		echo "removing $uid because they have NOT donated this year <br />";
				unset($thisyearlist[$uid]);

		}
	}

/*
echo "neverlist:".count($neverlist)."<br />";
echo "pastlist:".count($pastlist)."<br />";
echo "lastyearlist:".count($lastyearlist)."<br />";
echo "thisyearlist:".count($thisyearlist)."<br />";
*/

$userslist=array();
foreach($donationhistory AS $dh) {
	$arr=$dh."list";
	foreach($$arr AS $uid=>$u) {
		$userslist[$uid]=$u;
	}
}

if($_GET['generatelist']) {
	$campaignid=$_POST['fundraising_campaigns_id'];
	$params=serialize($_POST);
	echo "params=$params";
	mysql_query("UPDATE fundraising_campaigns SET filterparameters='{$params}' WHERE id='$campaignid'");
	$uids=array_keys($userslist);
	foreach($uids AS $u) {
		mysql_query("INSERT INTO fundraising_campaigns_users_link (fundraising_campaigns_id, users_uid) VALUES ('$campaignid','$u')");
	}
	echo "List created";
}
else {
	//just show the results
	$usersnum=count($userslist);
	echo i18n("%1 users match the given criteria",array($usersnum))." <br />";
	echo "<input type=\"submit\" value=\"".i18n("Generate List")."\">\n";
	//print_r($userslist);
	//print_r($otherlist);
}


	echo "<br /><br />";
echo nl2br(print_r($_POST,true));


?>
