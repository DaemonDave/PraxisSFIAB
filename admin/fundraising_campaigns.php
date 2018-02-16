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

switch($_GET['action']){
	case "campaigninfo_save":
		save_campaign_info();
		exit;
		break;

   case "modify":
    echo "<div id=\"campaignaccordion\" style=\"width: 780px;\">\n";
    $q=mysql_query("SELECT * FROM fundraising_campaigns WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY name");
    while($r=mysql_fetch_object($q)) {
        echo "<h3><a href=\"#\">".htmlspecialchars($r->name)."</a></h3>\n";
        echo "<div id=\"campaign_{$r->id}\">\n";
        echo "<form id=\"campaigninfo_{$r->id}\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\" onsubmit=\"return campaigninfo_save($r->id)\">\n";
        echo "<input type=\"hidden\" name=\"campaign_id\" value=\"{$r->id}\" />\n";
        echo "<table>\n";
        display_campaign_form($r);
        ?>
        <tr><td colspan="6" style="text-align: center;">
        <br />
        <input type="submit" value="<?=i18n("Save Appeal")?>"></td>
        </tr>
        </table>
        </form>
        </div>
    <?
    }
    ?>
	<h3><a href="#"><?=i18n("Create New Appeal")?></a></h3>
	<div id="campaign_new">
	<form id="campaigninfo_new" method="post" action="<?=$_SERVER['PHP_SELF']?>" onsubmit="return campaigninfo_save(-1)">
	<input type="hidden" name="campaign_id" value="-1" />
	<table>
<?
    display_campaign_form();
?>
    <tr><td colspan="6" style="text-align: center;">
    <br />
    <input type="submit" value="<?=i18n("Create Appeal")?>"></td>
    </tr>
    </table>
    </form>
	</div>
    </div>
    <?
    exit;
    break;


   case "managelist":
   echo i18n("Select an appeal");
?>
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

    echo "<tr style=\"cursor:pointer;\" onclick=\"return managecampaign($r->id)\">\n";
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
</table>
<script type="text/javascript"> $('.tableview').tablesorter();</script>
<br />
<?
   exit;
   break;


   case "manage":
    if(!$_GET['id']) {
        error_("Missing campaign to manage");
        exit;
    }
    $id=intval($_GET['id']);
    $q=mysql_query("SELECT * FROM fundraising_campaigns WHERE id='$id'");
    $campaign=mysql_fetch_object($q);
    echo "<h3>$campaign->name</h3>\n";
    ?>
    <div id="campaign_tabs">
        <ul>
            <li><a href="#campaign_tab_overview"><span><?=i18n('Overview')?></span></a></li>
            <li><a href="#campaign_tab_donations"><span><?=i18n('Donations/Sponsorships')?></span></a></li>
            <li><a href="#campaign_tab_prospects"><span><?=i18n('Prospects')?></span></a></li>
            <li><a href="#campaign_tab_communications"><span><?=i18n('Communications')?></span></a></li>
        </ul>
        <div id="campaign_tab_overview">
        overview tab
        </div>
        <div id="campaign_tab_donations">
        donations tab
        </div>
        <div id="campaign_tab_prospects">
        prospects tab
        </div>
        <div id="campaign_tab_communications">
        communications tab
        </div>
    </div>
<?

   exit;
   break;

   case "manage_tab_overview":
         $campaign_id=intval($_GET['id']);
         $q=mysql_query("SELECT * FROM fundraising_campaigns WHERE id='$campaign_id' AND fiscalyear='{$config['FISCALYEAR']}'");

         if($r=mysql_fetch_object($q)) {

			$goalr=getGoal($r->fundraising_goal);
            $recq=mysql_query("SELECT SUM(value) AS received FROM fundraising_donations WHERE fundraising_campaigns_id='$r->id' AND fiscalyear='{$config['FISCALYEAR']}' AND status='received'");
            echo mysql_error();
            $recr=mysql_fetch_object($recq);
            $received=$recr->received;
            if($r->target)
                $percent=round($received/$r->target*100,1);
            else
                $percent=0;
            $col=colour_to_percent($percent);
            echo "<table cellspacing=\"3\" cellpadding=\"3\">";
            echo "<tr>\n";
            echo "  <td>".i18n("Type")."</td><td>$r->type</td></tr>\n";
            echo "  <td>".i18n("Start Date")."</td><td>".format_date($r->startdate)."</td>\n";
            echo "</tr>\n";
            echo "  <td>".i18n("Follow-Up Date")."</td><td>".format_date($r->followupdate)."</td>";
            echo "</tr>\n";
            echo "  <td>".i18n("End Date")."</td><td>".format_date($r->enddate)."</td>";
            echo "</tr>\n";
            echo "  <td>".i18n("Default Purpose")."</td><td>$goalr->name</td>";
            echo "</tr>\n";
            echo "  <td>".i18n("Target")."</td><td>".format_money($r->target,false)."</td>\n";
            echo "</tr>\n";
            echo "  <td>".i18n("Received")."</td><td>".format_money($received,false)."</td>\n";
            echo "</tr>\n";
            echo "  <td>".i18n("% to Budget")."</td><td style=\"color: $col;\">{$percent}%</td>\n";
            echo "</tr>\n";
            echo "</table>\n";
         }
   exit;
   break;

   case "manage_tab_donations":
         $campaign_id=intval($_GET['id']);
         $q=mysql_query("SELECT * FROM fundraising_campaigns WHERE id='$campaign_id' AND fiscalyear='{$config['FISCALYEAR']}'");
         if($campaign=mysql_fetch_object($q)) {
			echo "<table class=\"tableview\">";
			echo "<thead>";
			echo "<tr>";
			echo " <th>".i18n("Date")."</th>\n";
			echo " <th>".i18n("Donor/Sponsor")."</th>\n";
			echo " <th>".i18n("Purpose")."</th>\n";
			echo " <th>".i18n("Amount")."</th>\n";
			echo " <th>".i18n("Type of Support")."</th>\n";
			echo "</tr>";
			echo "</thead>\n";

			$q=mysql_query("SELECT * FROM fundraising_donations WHERE fundraising_campaigns_id='$campaign_id'
			AND status='received' ORDER BY datereceived DESC");
			while($r=mysql_fetch_object($q)) {
				$goal=getGoal($r->fundraising_goal);
				$sq=mysql_query("SELECT * FROM sponsors WHERE id='{$r->sponsors_id}'");
				$sponsor=mysql_fetch_object($sq);
				echo "<tr><td>".format_date($r->datereceived)."</td>\n";
				echo "    <td>".$sponsor->organization."</td>\n";
				echo "    <td>".$goal->name."</td>\n";
				echo "    <td>".format_money($r->value)."</td>\n";
				echo "    <td>".i18n($r->supporttype)."</td>\n";
				echo "</tr>\n";
			}
			echo "</table>\n";
         }
   exit;
   break;


   case "manage_tab_prospects":
		$donationhistorylist=array("never"=>"Never donated/sponsored", "past"=>"Donated/sponsored in the past", "lastyear"=>"Donated/sponsored last year", "thisyear"=>"Donated/sponsored this year");
		$emailaddresslist=array("available"=>"Available", "notavaialble"=>"Not Available");

			$rolelist=array(
				"judge"=>"Judge",
				"teacher"=>"Teacher",
				"sciencehead"=>"School Science Head",
				"principal"=>"School Principal",
				"parent"=>"Parent",
				"committee"=>"Committee",
				"volunteer"=>"Volunteer",
				"alumni"=>"Alumni (not implemented)",
				"mentor"=>"Mentor (not implemented)",
				);
         $campaign_id=intval($_GET['id']);
         $q=mysql_query("SELECT * FROM fundraising_campaigns WHERE id='$campaign_id' AND fiscalyear='{$config['FISCALYEAR']}'");
		 $campaign=mysql_fetch_object($q);
		 if($campaign->filterparameters) {
			echo "<h4>".i18n("User List")."</h4>\n";
			$params=unserialize($campaign->filterparameters);
			echo "<table class=\"tableedit\">";
			echo "<tr><td>".i18n("Donor Type")."</td><td>".i18n(ucfirst($params['donortype']))."</td></tr>\n";
			if(is_array($params['donationhistory'])) {
				echo "<tr><td>".i18n("Donation History")."</td><td>";
				foreach($params['donationhistory'] AS $d) {
					echo i18n($donationhistorylist[$d])."<br />\n";
				}
				echo "</td></tr>\n";
			}
//			echo "<tr><td>".i18n("Donation Level")."</td><td>";
//			echo "</td></tr>\n";
			if(is_array($params['emailaddress'])) {
				echo "<tr><td>".i18n("Email Address")."</td><td>";
				foreach($params['emailaddress'] AS $e) {
					echo i18n($emailaddresslist[$e])."<br />\n";
				}
				echo "</td></tr>\n";
			}
			if($params['donortype']=="individual" && is_array($params['individual_type'])) {
				echo "<tr><td>".i18n("Role")."</td><td>";
				foreach($params['individual_type'] AS $e) {
					echo i18n($rolelist[$e])."<br />\n";
				}
				echo "</td></tr>\n";
			} else if( is_array($params['contacttype'])) {
				echo "<tr><td>".i18n("Role")."</td><td>";
				foreach($params['contacttype'] AS $e) {
					echo i18n(ucfirst($e))."<br />";
				}
				echo "</td></tr>\n";
			}

			echo "</table>\n";
			//params: individual/org
			//		donation history
			//		donation level
			// 		email address
			//		role ind
			//		role org

			echo "<br />";
			echo "<form id=\"prospectremoveform\" onsubmit=\"return removeselectedprospects()\">\n";
			echo "<input type=\"hidden\" name=\"fundraising_campaigns_id\" value=\"$campaign_id\" />\n";
			 $q=mysql_query("SELECT * FROM fundraising_campaigns_users_link WHERE fundraising_campaigns_id='$campaign_id'");
			 while($r=mysql_fetch_object($q)) {
				 $u=user_load_by_uid($r->users_uid);
				 //hopefully this never returns false, but who knows..
				 if($u) {
					 echo "<label>";
					 echo "<input type=\"checkbox\" name=\"prospectremovefromlist[]\" value=\"{$u['uid']}\" />";
					 if($u['sponsor']['donortype']=="organization") {
						 echo $u['sponsor']['organization']." - ";
					 }

					 echo $u['firstname']." " .$u['lastname'];
					 if($u['email']) echo " &lt;{$u['email']}&gt;";
					 echo "</label>\n";
					 echo "<br />";
				 }
			}
			echo "<br />";
			echo "<br />";

			echo "<table><tr><td>";	
			echo "<input onclick=\"return prospect_removeselected()\" type=\"button\" value=\"".i18n("Remove selected prospects from list")."\">\n";
			echo "</td><td>";
			echo "<input onclick=\"return prospect_removeall()\" type=\"button\" value=\"".i18n("Remove all prospects from list")."\">\n";
	//		echo "</td><td>";
	//		echo "<input type=\"button\" value=\"".i18n("Finalize prospect list")."\">\n";
			echo "</td></tr></table>\n";
		 }
		 else {
         ?>
         <h4><?=i18n("Choose Prospects")?></h4>
         <form id="prospectform" onsubmit="return prospect_generatelist()">
		 <input type="hidden" name="fundraising_campaigns_id" value="<?=$campaign_id?>" />
        <table>
        <tr><td style="width: 130px;"><?=i18n("Type")?>:</td><td>
         <label><input type="radio" name="donortype" value="organization" onchange="donortypechange()" ><?=i18n("Organization")?></label><br />
         <label><input type="radio" name="donortype" value="individual" onchange="donortypechange()" ><?=i18n("Individual")?></label><br />
         </td></tr>
        </table>
        <div id="prospect_common" style="display: none;">
        <hr />
        <table>
        <tr><td style="width: 130px;"><?=i18n("Donation History")?>:</td><td>
		<?
			foreach($donationhistorylist AS $k=>$v) {
            	echo "<label><input onchange=\"return prospect_search()\" type=\"checkbox\" name=\"donationhistory[]\" value=\"$k\">".i18n($v)."</label><br />\n";
			}
		?>
        </td></tr>
        <tr><td><?=i18n("Donation Level")?>:</td><td>
            <?
            $q=mysql_query("SELECT * FROM fundraising_donor_levels WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY min");
            while($r=mysql_fetch_object($q)) {
                echo "<label><input onchange=\"return prospect_search()\" disabled=\"disabled\" type=\"checkbox\" name=\"donationlevel[]\" value=\"$r->level\" >".i18n($r->level)." (".format_money($r->min,false)." - ".format_money($r->max,false).")</label><br />\n";
            }
			echo "(disabled until the logic requirements can be established)";
            ?>
        </td></tr>
        <tr><td><?=i18n("Email Address")?>:</td><td>
			<?
			foreach($emailaddresslist AS $k=>$v) {
            	echo "<label><input onchange=\"return prospect_search()\" type=\"checkbox\" name=\"emailaddress[]\" value=\"$k\">".i18n($v)."</label><br />\n";
			}
			?>
        </td></tr>
        </table>
        </div>

        <div id="prospect_individual" style="display: none;">
        <table>
        <tr><td style="width: 130px;"><?=i18n("Role")?>:</td><td>
			<?
			foreach($rolelist AS $k=>$v) {
            	echo "<label><input onchange=\"return prospect_search()\" type=\"checkbox\" name=\"individual_type[]\" value=\"$k\">".i18n($v)."</label><br />\n";
			}
			?>

        </td></tr></table>
        </div>

        <div id="prospect_organization" style="display: none;">
            <table>
            <tr><td style="width: 130px;"><?=i18n("Role")?>:</td><td>
                <label><input onchange="return prospect_search()" type="checkbox" name="contacttype[]" value="primary"><?=i18n("Primary contacts")?></label><br />
                <label><input onchange="return prospect_search()" type="checkbox" name="contacttype[]" value="secondary"><?=i18n("Secondary contacts")?></label><br />
            </td></tr></table>
                
        </div>

        <hr />
        <div id="prospectsearchresults"></div>
        </form>
        <?
		 }
   exit;
   break;


   case "manage_tab_communications":
         $campaign_id=intval($_GET['id']);
         $q=mysql_query("SELECT * FROM fundraising_campaigns WHERE id='$campaign_id' AND fiscalyear='{$config['FISCALYEAR']}'");

         if($r=mysql_fetch_object($q)) {

         }
		 $communications=array("initial"=>"Initial Communication",
		 				"followup"=>"Follow-Up Communication");

		 foreach($communications as $key=>$name) {
					echo "<h4>".i18n($name)."</h4>\n";
			//check if they have one in the emails database
			$q=mysql_query("SELECT * FROM emails WHERE fundraising_campaigns_id='$campaign_id' AND val='$key'");
			if($email=mysql_fetch_object($q)) {
				echo "<div style=\"float: right; margin-right: 15px;\">";
				echo "<a title=\"Edit\" href=\"#\" onclick=\"return opencommunicationeditor(null,$email->id,$campaign_id)\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\" border=0></a>";
				echo "&nbsp;&nbsp;";
				echo "<a title=\"Remove\" onClick=\"return removecommunication($email->id);\" href=\"\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=0></a>";
				echo "</div>";


				echo "<table cellspacing=0 cellpadding=3 border=1 style=\"margin-left: 30px; margin-right: 30px; width: 700px;\">";
				echo "<tr><td>".i18n("Subject")."</td><td>".htmlspecialchars($email->subject)."</td></tr>\n";
				echo "<tr><td>".i18n("From")."</td><td>".htmlspecialchars($email->from)."</td></tr>\n";
				echo "<tr><td colspan=\"2\">".$email->bodyhtml."</td></tr>\n";
				echo "<tr><td colspan=\"2\">";
				echo "<table style=\"width: 100%;\"><tr>";
					echo "<td style=\"text-align: center;\">";
					//we let them always send it again for now... might change this back later, but i think just notifying them of when it was last sent is enough and keeps teh form more consistent
					echo "<input type=\"button\" onclick=\"return opensendemaildialog($campaign_id,$email->id)\" value=\"".i18n("Send as email")."\" />";
					echo "<br />\n";
					if($email->lastsent) {
						list($date,$time)=split(" ",$email->lastsent);
						echo i18n("Last Sent");
						echo "<br />".format_date($date);
						echo "<br />".format_time($time);
					} 
					echo "</td>\n";
					echo "<td style=\"text-align: center;\"><input type=\"button\" onclick=\"return opensendmaildialog($campaign_id,'$key')\" value=\"".i18n("Generate PDF for mailing")."\" /></td>\n";
					echo "<td style=\"text-align: center;\"><input type=\"button\" onclick=\"return opensendlabelsdialog(47,$campaign_id)\" value=\"".i18n("Generate mailing labels")."\" /></td>\n";
				echo "</tr></table>\n";
				echo "</td></tr>\n";
				echo "</table>\n";

			}
			else {
			 echo "<ul>\n";
			 echo " <li><a href=\"#\" onclick=\"return opencommunicationchooser('$key');\">".i18n("Start from an existing communication")."</a></li>\n";
			 echo " <li><a href=\"#\" onclick=\"return opencommunicationeditor('$key',null,$campaign_id);\">".i18n("Create a new communication")."</a></li>\n";
			 echo "</ul>\n";
			}
			echo "<br />";
		 }
   exit;
   break;

	case "prospect_removeselected":
		$campaignid=intval($_POST['fundraising_campaigns_id']);
		print_r($_POST);
		if(is_array($_POST['prospectremovefromlist'])) {
			$uidlist=implode(",",$_POST['prospectremovefromlist']);
			$query="DELETE FROM fundraising_campaigns_users_link WHERE fundraising_campaigns_id='$campaignid' AND users_uid IN ($uidlist)";
			mysql_query($query);
			echo mysql_error();
		}
		//if theres nobody left in the list we need to reset the filter params as well
		$q=mysql_query("SELECT COUNT(*) AS num FROM fundraising_campaigns_users_link WHERE fundraising_campaigns_id='$campaignid'");
		$r=mysql_fetch_object($q);
		if($r->num==0) {
			mysql_query("UPDATE fundraising_campaigns SET filterparameters=NULL WHERE id='$campaignid'");
		}

		happy_("Selected users removed from list");
	exit;
	break;

	case "prospect_removeall":
		$campaignid=intval($_POST['fundraising_campaigns_id']);
		mysql_query("DELETE FROM fundraising_campaigns_users_link WHERE fundraising_campaigns_id='$campaignid'");
		mysql_query("UPDATE fundraising_campaigns SET filterparameters=NULL WHERE id='$campaignid'");
		happy_("All users removed from list");
	exit;
	break;

	case "communication_remove":
		$emails_id=$_POST['id'];
		//check if its been sent, if so, it cannot be deleted, sorry!
		$q=mysql_query("SELECT * FROM emails WHERE id='$emails_id'");
		$e=mysql_fetch_object($q);
		if($e->lastsent) {
			error_("Cannot remove an email that has already been sent");
		}
		else {
			mysql_query("DELETE FROM emails WHERE id='$emails_id'");
			happy_("Communicaton removed");
		}


	break;

}

function save_campaign_info(){
    global $config;
    if(!$_POST['name']){
         error_("Appeal Name is required");
         return;
    }
    if(!$_POST['startdate']) $startdate=date("Y-m-d"); else $startdate=$_POST['startdate'];

	if(!$_GET['id']) {
		$query = "INSERT INTO fundraising_campaigns (name,fiscalyear) VALUES (
            '".mysql_real_escape_string(stripslashes($_POST['name']))."','{$config['FISCALYEAR']}')";
        mysql_query($query);
		$id = mysql_insert_id();
        happy_("Appeal Created");
	}else{
		$id = $_GET["id"];
        happy_("Appeal Saved");
    }
    mysql_query("UPDATE fundraising_campaigns SET 
       name='".mysql_real_escape_string(stripslashes($_POST['name']))."',
       `type`='".mysql_real_escape_string($_POST['type'])."',
       startdate='".mysql_real_escape_string($startdate)."',
       followupdate='".mysql_real_escape_string($_POST['followupdate'])."',
       enddate='".mysql_real_escape_string($_POST['enddate'])."',
       target='".mysql_real_escape_string($_POST['target'])."',
       fundraising_goal='".mysql_real_escape_string($_POST['fundraising_goal'])."'
       WHERE id='$id'");
}

send_header("Appeal Management",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Fundraising' => 'admin/fundraising.php'),
            "fundraising"
			);
?>

<script type="text/javascript">
$(document).ready(function() {
    <?
    if($_GET['manage_campaign']) {
        echo "managecampaign(".intval($_GET['manage_campaign']).");\n";
    }
    else {
        echo "managecampaigns();\n";
    }
    ?>
});

function modifycampaigns() {
	$("#campaigndiv").show();
	$("#campaigndiv").load("<?$_SERVER['PHP_SELF']?>?action=modify", null, function() {modifycampaignsfinish();});
}

function managecampaigns() {
	$("#campaigndiv").show();
	$("#campaigndiv").load("<?$_SERVER['PHP_SELF']?>?action=managelist", null, function() {managecampaignsfinish();});
}

var currentcampaignid;

function managecampaign(id) {
	$("#campaigndiv").show();
	$("#campaigndiv").load("<?$_SERVER['PHP_SELF']?>?action=manage&id="+id, null, function() {managecampaignfinish();});
    currentcampaignid=id;
}


function modifycampaignsfinish(){
	$("#campaignaccordion").accordion();
	// create the date pickers for our form
	$(".date").datepicker({
		dateFormat: 'yy-mm-dd'
	});
}

function managecampaignsfinish() {

}

function managecampaignfinish() {
    $("#campaign_tabs").tabs({
    show: function(event, ui) {
        switch(ui.panel.id) {
            case 'campaign_tab_overview':
            update_tab_overview();
            break;
            case 'campaign_tab_donations':
            update_tab_donations();
            break;
            case 'campaign_tab_prospects':
            update_tab_prospects();
            break;
            case 'campaign_tab_communications':
            update_tab_communications();
            break;
        }
    },
    selected: 0
    });

}

function campaigninfo_save(id) {
	//if we're creating we need to do the post, and get the id it returns, so we can re-open the popup window with that id
	if(id==-1) {
		$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=campaigninfo_save", $("#campaigninfo_new").serializeArray(), function() { modifycampaigns(); });
	} else {
		$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=campaigninfo_save&id="+id, $("#campaigninfo_"+id).serializeArray(), function() { modifycampaigns(); });
	}
	return false;
}

function update_tab_overview() {
		$("#campaign_tab_overview").load("<?$_SERVER['PHP_SELF']?>?action=manage_tab_overview&id="+currentcampaignid);
}
function update_tab_donations() {
		$("#campaign_tab_donations").load(
			"<?$_SERVER['PHP_SELF']?>?action=manage_tab_donations&id="+currentcampaignid,
			null,
			function(){$('.tableview').tablesorter();}
		);
}
function update_tab_prospects() {
		$("#campaign_tab_prospects").load("<?$_SERVER['PHP_SELF']?>?action=manage_tab_prospects&id="+currentcampaignid);
}
function update_tab_communications() {
		$("#campaign_tab_communications").load("<?$_SERVER['PHP_SELF']?>?action=manage_tab_communications&id="+currentcampaignid);
}

function donortypechange() {
    if($("input[@name='donortype']:checked").val()=="organization") {
        $("#prospect_common").show('slow');
        $("#prospect_organization").show('slow');
        $("#prospect_individual").hide('slow');
    }
    else if($("input[@name='donortype']:checked").val()=="individual") {
        $("#prospect_common").show('slow');
        $("#prospect_organization").hide('slow');
        $("#prospect_individual").show('slow');
    }
    else {
        $("#prospect_common").hide('slow');
    }
    prospect_search();
}

function prospect_search() {
    $("#prospectsearchresults").load("fundraising_campaigns_prospecting.php",$("#prospectform").serializeArray());
    return false;
}

function prospect_generatelist() {
    $("#prospectsearchresults").load("fundraising_campaigns_prospecting.php?generatelist=true",$("#prospectform").serializeArray(), function() {
		update_tab_prospects();
	});
    return false;
}

function prospect_removeselected() {
    $("#debug").load("fundraising_campaigns.php?action=prospect_removeselected",$("#prospectremoveform").serializeArray(),function() {
		update_tab_prospects();
	});
    return false;
}

function prospect_removeall() {
    $("#debug").load("fundraising_campaigns.php?action=prospect_removeall",$("#prospectremoveform").serializeArray(),function() {
		update_tab_prospects();
	});
    return false;
}

var comm_chooser_key = null;
function opencommunicationchooser(key) {
	comm_chooser_key = key;
	$("#dialog").empty();
	$("#dialog").load("communication.php?action=dialog_choose&type=fundraising",null,function() {
		});
}

function removecommunication(id) {
    $("#debug").load("fundraising_campaigns.php?action=communication_remove",{id:id},function() {
		update_tab_communications();
	});
    return false;
}

function comm_dialog_choose_select(id) {
//	alert('im back with email id: '+id);
	//get rid of hte html
	var key = comm_chooser_key;
	$("#dialog").empty();
	$("#dialog").load("communication.php?action=dialog_edit&cloneid="+id+"&key="+key+"&fundraising_campaigns_id="+currentcampaignid,null,function() {
	});
}

function comm_dialog_choose_cancel() {
//	alert('im cancelled');
}

function comm_dialog_edit_save(id) {
//	alert("saved!");
    update_tab_communications();
}

function comm_dialog_edit_cancel() {
//	alert("cancelled!");
}

function opensendlabelsdialog(reports_id,fcid) {
	$("#dialog").empty();
	var args = "action=dialog_gen&sid="+reports_id+"&filter[0][field]=fundraising_campaigns_id&filter[0][x]=0&filter[0][value]="+fcid;
	$("#dialog").load("reports_gen.php?"+args,null,function() {
		});
}

function opensendmaildialog(fcid,key) {
	var dlargs = "fundraising_campaigns_id="+fcid+"&key="+key;
	var dlurl = "<?=$config['SFIABDIRECTORY']?>/admin/reports_appeal_letters.php?"+dlargs;
	window.location.href = dlurl;
//	$('#content').attr('src',dlurl);
	return false;
}

function opensendemaildialog(fcid,emails_id) {
	$("#dialog").empty();
	$("#dialog").load("communication.php?action=dialog_send&type=fundraising&fundraising_campaigns_id="+fcid+"&emails_id="+emails_id,null,function() {
		});
}

</script>

<?
function display_campaign_form($r=null) {
    global $config;
    global $campaign_types;
?>    
		<tr>
			<td><?=i18n("Name")?></td>
			<td colspan="3"><input size="40" type="text" name="name" value="<?=$r->name?>"></td>
            <td><?=i18n("Type")?></td><td>
            <select name="type">
            <option value=""><?=i18n("Choose")?></option>
            <?
            foreach($campaign_types AS $ct) {
                if($r->type==$ct) $sel="selected=\"selected\""; else $sel="";
                echo "<option $sel value=\"$ct\">".i18n($ct)."</option>\n";
            }
            ?>
            </select>
            </td>
        </tr>
        <?
        if($r->startdate) $sd=$r->startdate;
        else $sd=date("Y-m-d");
        ?>
        <tr>
			<td><?=i18n("Start Date")?></td><td><input type="text" name="startdate" class="date" value="<?=$sd?>" /></td>
			<td><?=i18n("Follow-Up Date")?></td><td><input type="text" name="followupdate" class="date" value="<?=$r->followupdate?>" /></td>
			<td><?=i18n("End Date")?></td><td><input type="text" name="enddate" class="date" value="<?=$r->enddate?>" /></td>
        </tr>
        <tr>
			<td><?=i18n("Target")?></td><td>$<input type="text" id="target" name="target" size="10" value="<?=$r->target?>" /></td>
			<td><?=i18n("Default Purpose")?></td><td colspan="3">
            <?
            $fgq=mysql_query("SELECT * FROM fundraising_goals WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY name");
            echo "<select name=\"fundraising_goal\">";
            echo "<option value=\"\">".i18n("Choose Default Purpose")."</option>\n";
            while($fgr=mysql_fetch_object($fgq)) {
                if($r->fundraising_goal==$fgr->goal) $sel="selected=\"selected\""; else $sel="";
                echo "<option $sel value=\"$fgr->goal\">".i18n($fgr->name)."</option>\n";
            }
            echo "</select>\n";
            ?>
            </td>
        </tr>
<?
}
?>
<table cellspacing=2 width=740 border=0>
<tr><td>
<a href="#" onclick="modifycampaigns()">Create/Modify Appeals</a>
</td><td>
<a href="#" onclick="managecampaigns()">Appeal Management</a>
</td></tr></table>
<hr />
<div id="campaigndiv" style="width: 780px; display: none;"></div>
<div id="dialog" style="width: 780px; display: none;"></div>

<?
 send_footer();
?>
