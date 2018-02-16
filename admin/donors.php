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
require_once("fundraising_common.inc.php");


switch($_GET['action']) {
	case 'organizationinfo_load':
		$id=intval($_GET['id']);
		$q=mysql_query("SELECT * FROM sponsors WHERE id='$id'");
		$ret=mysql_fetch_assoc($q);
		echo json_encode($ret);		
		exit;
		break;

	case 'organizationinfo_save':
		$id=intval($_POST['sponsor_id']);
		if($id==-1) {
			$q=mysql_query("INSERT INTO sponsors (year) VALUES ('".$config['FAIRYEAR']."')");
			$id=mysql_insert_id();
			echo json_encode(array("id"=>$id));
			save_activityinfo("Created donor/sponsor", $id, $_SESSION['users_uid'],"System");
			$createnew=true;
		}
		else $createnew=false;

		if($id) {
			$exec="UPDATE sponsors SET ".
			"donortype='".mysql_escape_string(stripslashes($_POST['donortype']))."', ".
			"organization='".mysql_escape_string(stripslashes($_POST['organization']))."', ".
			"address='".mysql_escape_string(stripslashes($_POST['address']))."', ".
			"address2='".mysql_escape_string(stripslashes($_POST['address2']))."', ".
			"city='".mysql_escape_string(stripslashes($_POST['city']))."', ".
			"province_code='".mysql_escape_string(stripslashes($_POST['province_code']))."', ".
			"postalcode='".mysql_escape_string(stripslashes($_POST['postalcode']))."', ".
			"phone='".mysql_escape_string(stripslashes($_POST['phone']))."', ".
			"tollfree='".mysql_escape_string(stripslashes($_POST['tollfree']))."', ".
			"fax='".mysql_escape_string(stripslashes($_POST['fax']))."', ".
			"email='".mysql_escape_string(stripslashes($_POST['email']))."', ".
			"website='".mysql_escape_string(stripslashes($_POST['website']))."', ".
			"notes='".mysql_escape_string(stripslashes($_POST['notes']))."', ".
			"donationpolicyurl='".mysql_escape_string(stripslashes($_POST['donationpolicyurl']))."', ".
			"fundingselectiondate='".mysql_escape_string(stripslashes($_POST['fundingselectiondate']))."', ".
			"proposalsubmissiondate='".mysql_escape_string(stripslashes($_POST['proposalsubmissiondate']))."', ".
			"waiveraccepted='".mysql_escape_string(stripslashes($_POST['waiveraccepted']))."' ".
			"WHERE id='$id'";
			mysql_query($exec);
			echo mysql_error();

			//FIXME accept the logo
			//"logo='".mysql_escape_string(stripslashes($_POST['logo']))."', ".
//($comment , $donorId, $userId, $type, $campaign_id=null){
			if(!$createnew) {
				save_activityinfo("Updated donor/sponsor details", $id, $_SESSION['users_uid'],"System");
				happy_("Donor/Sponsor Details saved");
			}
		}
		exit;
		break;

	case 'sponsorshipinfo_load':
		$id=intval($_GET['id']);
		echo "<h4>".i18n("Summary")."</h4>\n";
		echo "<table cellspacing=3 cellpadding=3>\n";


		//LAST DONATION
		$q=mysql_query("SELECT * FROM fundraising_donations WHERE sponsors_id='$id' ORDER BY datereceived DESC LIMIT 1");
		if($r=mysql_fetch_object($q))
			$lastdonation=i18n("%1 on %2",array(format_money($r->value,false),format_date($r->datereceived)),array("Donation amount","Donation date"));
		else
			$lastdonation=i18n("Never");

		//TOTAL THIS YEAR
		$q=mysql_query("SELECT SUM(value) AS total FROM fundraising_donations 
				WHERE sponsors_id='$id' 
				AND status='received'
				AND fiscalyear={$config['FISCALYEAR']} 
				");

		if($r=mysql_fetch_object($q))
			$totalthisyear=format_money($r->total,false);
		else
			$totalthisyear=format_money(0);

		//TOTAL LAST YEAR
		$lastyear=$config['FISCALYEAR']-1;
		$q=mysql_query("SELECT SUM(value) AS total FROM fundraising_donations 
				WHERE sponsors_id='$id' 
				AND status='received'
				AND fiscalyear=$lastyear
				");

		if($r=mysql_fetch_object($q))
			$totallastyear=format_money($r->total,false);
		else
			$totallastyear=format_money(0);

		//OUTPUT
		echo "<tr><td>".i18n("Last Donation")."</td><td>$lastdonation</td></tr>\n";
		echo "<tr><td>".i18n("Total This Year")."</td><td>$totalthisyear</td></tr>\n";
		echo "<tr><td>".i18n("Total Last Year")."</td><td>$totallastyear</td></tr>\n";
		echo "</table>\n";

		echo "<br />\n";
		echo "<h4>".i18n("Donations/Sponsorships")."</h4>\n";
		echo "<div id=\"thisyeardonationhistory\">";
		$q=mysql_query("SELECT fundraising_donations.*, 
				fundraising_campaigns.name AS campaignname 
				FROM fundraising_donations 
				LEFT JOIN fundraising_campaigns ON fundraising_donations.fundraising_campaigns_id=fundraising_campaigns.id 
				WHERE sponsors_id='$id' 
				AND status='received' 
				AND fundraising_donations.fiscalyear='{$config['FISCALYEAR']}' 
				ORDER BY datereceived DESC");
		echo mysql_Error();

		if(mysql_num_rows($q)) {
			echo "<table class=\"tableview\">";
			echo "<thead>";
			echo "<tr>";
			echo " <th>".i18n("Date")."</th>\n";
			echo " <th>".i18n("Purpose")."</th>\n";
			echo " <th>".i18n("Appeal")."</th>\n";
			echo " <th>".i18n("Value")."</th>\n";
			echo " <th>".i18n("Remove")."</th>\n";
			echo "</tr>";
			echo "</thead>";
			while($r=mysql_fetch_object($q)) {
				echo "<tr>\n";
				echo " <td>".format_date($r->datereceived)."</td>\n";
				$goal=getGoal($r->fundraising_goal);
				echo " <td>$goal->name</td>";
				echo " <td>$r->campaignname</td>";
				echo " <td>".format_money($r->value,false)."</td>";
				echo " <td align=\"center\">";
				echo "<a onclick=\"return removedonation($r->id,$id)\" href=\"#\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
				echo " </td>";
				echo "</tr>\n";
			}
			echo "</table>\n";
		}else{
			echo i18n("No donations this year")."<br />";
		}

		echo "<a href=\"#\" onclick=\"return togglefulldonationhistory()\" id=\"fullhistorylink\">".i18n("View full donation history")."</a>";
		echo "</div>";
		echo "<div id=\"fulldonationhistory\" style=\"display: none;\">";
		echo "<a href=\"#\" onclick=\"return togglefulldonationhistory()\" id=\"fullhistorylink\">".i18n("View this year's donation history")."</a>";
		echo "<table class=\"tableview\">";
		echo "<thead>";
		echo "<tr>";
		echo " <th>".i18n("Date")."</th>\n";
		echo " <th>".i18n("Purpose")."</th>\n";
		echo " <th>".i18n("Appeal")."</th>\n";
		echo " <th>".i18n("Value")."</th>\n";
		echo " <th>".i18n("Remove")."</th>\n";
		echo "</tr>";
		echo "</thead>";

		$q=mysql_query("SELECT fundraising_donations.*, 
				fundraising_campaigns.name AS campaignname 
				FROM fundraising_donations 
				LEFT JOIN fundraising_campaigns ON fundraising_donations.fundraising_campaigns_id=fundraising_campaigns.id 
				WHERE sponsors_id='$id' 
					AND status='received' 
				ORDER BY datereceived DESC");
		while($r=mysql_fetch_object($q)) {
			echo "<tr>\n";
			echo " <td>".format_date($r->datereceived)."</td>\n";
				$goal=getGoal($r->fundraising_goal);
			echo " <td>$goal->name</td>";
			echo " <td>$r->campaignname</td>";
			echo " <td>".format_money($r->value,false)."</td>";
				echo " <td align=\"center\">";
				echo "<a onclick=\"return removedonation($r->id,$id)\" href=\"#\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
				echo " </td>";
				echo "</tr>\n";
		}
		echo "</table>\n";

		echo "</div>\n";
		echo "<br />\n";
		echo "<h4>".i18n("Add New Donation/Sponsorship")."</h4>\n";

		echo "<form id=\"addnewdonationform\" onsubmit=\"return adddonation()\">";
			echo "<input type=\"hidden\" name=\"sponsors_id\" value=\"$id\" />\n";
		echo "<table cellspacing=3 cellpadding=3>";
		echo "<tr><td>";
		echo i18n("Appeal").":";
		echo "</td><td>";

			// loop through each contact in the donor
			$query = mysql_query("SELECT users.id,users.uid,users.deleted,MAX(year) 
				FROM users 
				LEFT JOIN users_sponsor ON users_sponsor.users_id=users.id
				WHERE 
				sponsors_id='$id' 
				AND types LIKE '%sponsor%'
				GROUP BY uid
				HAVING deleted='no' 
				ORDER BY users_sponsor.primary DESC,lastname,firstname
			");
			echo mysql_error();
			$uids=array();
			while($r=mysql_fetch_object($query)) {
				$uids[]=$r->uid;
			}

		$q=mysql_query("SELECT * FROM fundraising_campaigns
				WHERE fiscalyear='{$config['FISCALYEAR']}' 
				ORDER BY name");
		$str="";
		echo "<select id=\"fundraising_campaign_id\" name=\"fundraising_campaigns_id\" onchange=\"campaignchange()\">";
		echo "<option value=\"\">".i18n("Choose an appeal")."</option>\n";
		while($r=mysql_fetch_object($q)) {
				//if there's uids, we can check if this sponsor is in the campaign
				//if there's no uids, (aka no contacts) then there's no way we could have included them in a cmomunication
				//but they could still get here fors ome reason, so we still need to show all the campaigns

				if(count($uids)) {
					$tq=mysql_query("SELECT * FROM fundraising_campaigns_users_link
						WHERE fundraising_campaigns_id='$r->id'
						AND users_uid IN (".implode(",",$uids).")
						");
					if(mysql_num_rows($tq)) {
						$incampaign=i18n("*In Appeal*").": ";
					}
					else $incampaign="";
				}
				else $incampaign="";

			echo "<option value=\"$r->id\">{$incampaign}{$r->name}</option>\n";
			$str.="defaultgoals[$r->id]='$r->fundraising_goal';\n";
		}
		echo "<option value=\"\">".i18n("Other/No Appeal")."</option>\n";
		echo "</select>\n";
		echo "</td></tr>\n";

		echo "<tr><td>";
		echo i18n("Purpose").":";
		echo "</td><td>";
		echo "<script type=\"text/javascript\">";
		echo " var defaultgoals=Array();\n";
		echo $str;
		echo "</script>\n";
		echo "<select id=\"fundraising_goal\" name=\"fundraising_goal\">";
		echo "<option value=\"\">".i18n("Choose a purpose")."</option>\n";
		//FIXME: only show campaigns that they were included as part of
		//we need a campaigns_users_link or campaigns_sponsors_link or something
		$q=mysql_query("SELECT * FROM fundraising_goals WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY name");
		while($r=mysql_fetch_object($q)) {
			echo "<option value=\"$r->goal\">$r->name</option>\n";
		}

		echo "</select>\n";
		echo "</td></tr>\n";

		echo "<tr><td>".i18n("Date Received").":</td><td><input type=\"text\" class=\"date\" name=\"datereceived\" value=\"".date("Y-m-d")."\"></td></tr>\n";
		echo "<tr><td>".i18n("Amount").":</td><td>\$<input type=\"text\" size=\"10\" name=\"value\"></td></tr>\n";
		echo "<tr><td>".i18n("Type of Support").":</td><td>";
		$supporttypes=array("Gift - no receipt");
		if($config['registered_charity'])
		$supporttypes[]="Donation - with receipt";
		$supporttypes[]="Sponsorship";
		echo "<select name=\"supporttype\">\n";
		echo "<option value=\"\">Choose type of support</option>\n";
		foreach($supporttypes AS $st) {
			echo "<option value=\"$st\">".i18n($st)."</option>\n";
		}
		echo "</select>\n";
		echo "</td></tr>\n";
		/*
		echo "<tr><td></td><td>";
		echo "<a href=\"#\" onclick=\"return false;\">".i18n("Generate Thank You")."</a></td>";
		echo "</tr>\n";
		*/
		echo "<tr><td colspan=\"2\" style=\"text-align: center;\"><input type=\"submit\" value=\"".i18n("Add donation/sponsorship")."\" /></td></tr>\n";
		echo "</table>\n";
		echo "</form>\n";
		
		exit;
		break;
	
	case 'contactsinfo_load':
		// make sure a donor id has been selected
		if($_GET['id']){
			draw_contactsinfo_form();
	   	}
		exit;
		break;

	case 'contactsinfo_save':
		save_contact();
		exit;
		break;
	case 'contactsinfo_delete':
		delete_contact();
		exit;
		break;
	case 'contactsinfo_addexisting':
		addexisting_contact();
		exit;
		break;
	case 'activityinfo_load':
		// make sure a donor id has been selected
		if($_GET['id']){
			draw_activityinfo_form();
		}
		exit;
		break;
 	case 'activityinfo_save':
//($comment , $donorId, $userId, $type, $campaign_id=null){
		if(save_activityinfo(
				$_POST['comment'],
				$_GET['id'],
				$_SESSION['users_uid'],
				$_POST['type'],
				$_POST['fundraising_campaigns_id']
		)){
			happy_("Activity Logged");
		}else{
			error_("Unable to save activity log");
		}
		exit;
		break;

	case 'newcontactsearch':

		if($_POST['email'])
		$q=mysql_query("SELECT *,MAX(year) FROM users WHERE email='".trim($_POST['email'])."' GROUP BY uid HAVING deleted='no'");

		if($r=mysql_fetch_object($q)) {
			echo i18n("There is an exact email address match for %1",array($_POST['email']));
			echo "<ul>";
			echo "<li><a href=\"#\" onclick=\"useexistingcontact($r->uid)\">$r->firstname $r->lastname $r->email $r->phonehome</a></li>\n";
			echo "</ul>";

			?>
			<script type="text/javascript">
			 $("#contactnewsave").attr("disabled","disabled");
			</script>
			<?
		}
		else {
			?>
			<script type="text/javascript">
			 $("#contactnewsave").attr("disabled","");
			</script>
			<?

			$searchstr="1 ";
			if($_POST['firstname'])
				$searchstr.=" AND firstname LIKE '%".$_POST['firstname']."%'";
			if($_POST['lastname'])
				$searchstr.=" AND lastname LIKE '%".$_POST['lastname']."%'";
			if($_POST['email'])
				$searchstr.=" AND email LIKE '%".$_POST['email']."%'";

			$q=mysql_query("SELECT *,MAX(year) FROM users WHERE $searchstr GROUP BY uid HAVING deleted='no'");
			$num=mysql_num_rows($q);
			if($num==0) {
				echo i18n("No existing users match, will create a new user");
			}
			else if($num<15) {
				echo i18n("Did you mean one of these existing users? (click to choose one)")."<br />";
				echo "<ul>";
				while($r=mysql_fetch_object($q)) {
					echo "<li><a href=\"#\" onclick=\"useexistingcontact($r->uid)\">$r->firstname $r->lastname $r->email $r->phonehome</a></li>\n";
				}
				echo "</ul>";
			}
			else {
				echo i18n("There are %1 existing users that match, please enter more details",array($num));
			}
		}
		echo "<br />";
	exit;
	break;
	case "donation_add":
		$campaignid=intval($_POST['fundraising_campaigns_id']);
		$sponsorid=intval($_POST['sponsors_id']);
		$goal=$_POST['fundraising_goal'];
		$value=intval($_POST['value']);
		$supporttype=$_POST['supporttype'];
		$datereceived=$_POST['datereceived'];

		if($goal && $value && $supporttype) {
			mysql_query("INSERT INTO fundraising_donations (sponsors_id,fundraising_goal,fundraising_campaigns_id,value,status,probability,fiscalyear,thanked,datereceived,supporttype) VALUES (
			'$sponsorid',
			'".mysql_real_escape_string($goal)."',
			'$campaignid',
			'$value',
			'received',
			'100',
			'{$config['FISCALYEAR']}',
			'no',
			'".mysql_real_escape_string($datereceived)."',
			'".mysql_real_escape_string($supporttype)."'
			)");
			$id=mysql_insert_id();
			$logStr=getDonationString($id);
			save_activityinfo("Added donation/sponsorship: $logStr", $sponsorid, $_SESSION['users_uid'],"System");
			echo mysql_error();

			happy_("Donation/sponsorship added");
		} else {
			error_("All fields are required");
		}

	exit;
	break;
	case "donation_remove":
		//function save_activityinfo($comment, $donorId, $userId, $type, $campaign_id=null){
		$id=intval($_POST['id']);
		$sponsorid=intval($_POST['sponsors_id']);
		if($logStr=getDonationString($id)) {
			save_activityinfo("Removed donation/sponsorship: $logStr", $sponsorid, $_SESSION['users_uid'],"System");
			happy_("Donation/sponsorship removed");
			mysql_query("DELETE FROM fundraising_donations WHERE id='$id' AND sponsors_id='$sponsorid'");
			echo mysql_error();
		}
		else {
			error_("Invalid donation/sponsorship to remove");
		}
	exit;
	break;
}

send_header("Donor/Sponsor Management",
		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Fundraising' => 'admin/fundraising.php')
		);

//delete the contact who has been submitted in _POST
function delete_contact(){
	if(array_key_exists('userid', $_POST)){
		$uid = $_POST['userid'];
		$data = mysql_query("SELECT CONCAT_WS(' ', users.firstname, users.lastname) AS name FROM users WHERE id=" . $uid);
		$namedata = mysql_fetch_array($data);
		$name = trim($namedata['name']);
		user_delete($uid,"sponsor");
		happy_("Deleted contact %1", array($name));

//($comment , $donorId, $userId, $type, $campaign_id=null){
		save_activityinfo("Deleted contact \"$name\"",$_GET['id'],$_SESSION['users_uid'],'System' );
	}
}

function addexisting_contact() {
	$uid=intval($_POST['uid']);
	$sponsors_id=intval($_POST['id']);
	echo "Linking uid=$uid to sponsors_id=$sponsors_id <br />";

	echo "uid $uid has users.id {$u['id']}";

	$u=user_load_by_uid($uid);
	$u['sponsors_id']=$sponsors_id;
	$u['types'][]="sponsor";
	user_save($u);

	save_activityinfo("Existing user (".$u['firstname']." ".$u['lastname'].") linked to donor/sponsor",$sponsors_id,$_SESSION['users_uid'],'System');
	happy_("Added existing user to donor/sponsor");

}

// save the contact info
function save_contact(){
	global $config;
	//happy_("happy!");
	if(validate_contactdata()){
		// load or create the user, according to the situation
		if($_POST['recordtype'] == 'new'){

			if($_POST['email']) {
				$q=mysql_query("SELECT *,MAX(year) FROM users WHERE email='".trim($_POST['email'])."' GROUP BY uid HAVING deleted='no'");
				if(mysql_num_rows($q)) {
					error_("A user with that email address already exists");
					exit;
				}
			}

			// this is a new record being submitted.  Create the user.
			$successMessage = "Contact created successfully";
			$successLog = "Added contact ";
			$u = user_create("sponsor", $_POST['email']);
			$id = $u['id'];
		}else if($_POST['recordtype'] == 'existing'){
			// this is an existing record being updated.  Load the user.
			$successMessage = "Contact updated successfully";
			$successLog = "Updated contact ";

			$u = user_load($_POST['userid']);
			$id = intval($_POST['userid']);
		}

		$sponsor_id = $_POST['sponsor_id'];
		$p = ($_POST['primary']=='yes')?'yes':'no';
		if($p == 'no') {
			/* Make sure this sponsor ($sponsor_id) has a primary */
			$query = "SELECT users_id 
				FROM users_sponsor, users 
				WHERE
				users_sponsor.users_id=users.id
				AND sponsors_id='$sponsor_id'
				AND `primary`='yes'
				AND year='".$config['FAIRYEAR']."'
				AND users_id!='$id'";
			$q = mysql_query($query);
			if(mysql_num_rows($q) == 0) {
				/* This has to be the primary since there isn't one already */
				$p = 'yes';
			}
		} else {
			/* Unset all other primaries */
			mysql_query("UPDATE users_sponsor SET `primary`='no'
					WHERE sponsors_id='$sponsor_id' AND users_id != '$id'");
		}

		// we now know whether or not they're the primary user.  Update them with that,
		// along with all of the user info that's been submitted.
		$u['primary']=$p;
		$u['salutation']=$_POST['salutation'];
		$u['firstname']=$_POST['firstname'];
		$u['lastname']=$_POST['lastname'];
		$u['position']=$_POST['position'];
		$u['phonework']=$_POST['phonework'];
		$u['phonecell']=$_POST['phonecell'];
		$u['phonehome']=$_POST['phonehome'];
		$u['address']=$_POST['address'];
		$u['address2']=$_POST['address2'];
		$u['city']=$_POST['city'];
		$u['postalcode']=$_POST['postalcode'];
		$u['province']=$_POST['province_code'];
		$u['fax']=$_POST['fax'];
		$u['email']=$_POST['email'];
		$u['notes']=$_POST['notes'];
		$u['sponsors_id']=$sponsor_id;
		user_save($u);
		$name = trim($u['firstname'] . ' ' . $u['lastname']);
//($comment , $donorId, $userId, $type, $campaign_id=null){
		save_activityinfo($successLog . '"' . $name . '"',$sponsor_id,$_SESSION['users_uid'],'System');
		happy_($successMessage);
	}else{
		// something's wrong with the user data submitted.  Should flag the fields where
		// appropriate, but for now just pop up an error
		error_("Form not filled out");
	}
}

// FIXME: dummy filler function for now.  Should go through all of the fields
// submitted and validate before hitting the database
function validate_contactdata(){
	$returnval = true;
	if($_POST['recordtype'] != 'new' && $_POST['recordtype'] != 'existing'){
		$returnval = false;
	}

	return $returnval;
}

// draw a group of forms for editing and creating new contacts
function draw_contactsinfo_form($contact = null){
	global $config;

	// make sure we know what sponsor we're dealing with here
	if(!isset($sponsor_id)){
		if($_GET['id'])
			$sponsor_id=$_GET['id'];
		 else if($_POST['id'])
			$sponsor_id=$_POST['id'];
		$buttontext = i18n("Add Contact");
	}

	// start our accordion
	echo "<div id=\"contactaccordion\" style=\"width: 740px;\">\n";


	// loop through each contact and draw a form with their data in it.
	$query = mysql_query("SELECT *,MAX(year) FROM users LEFT JOIN users_sponsor ON users_sponsor.users_id=users.id
	WHERE 
	sponsors_id='" . $sponsor_id . "' 
	AND types LIKE '%sponsor%'
	GROUP BY uid
	HAVING deleted='no' 
	ORDER BY users_sponsor.primary DESC,lastname,firstname
	");
	echo mysql_error();

	while($contact = mysql_fetch_array($query)){
		// draw a header for this user
		echo "<h3><a href=\"#\">";
		echo $contact["firstname"] . " " . $contact["lastname"];
		echo "</a></h3>\n";

		// and draw the actual form
		echo "<div>\n";
		draw_contact_form($sponsor_id, $contact);
		echo "</div>\n";
	}

	// draw an empty form in which to enter a new user
	echo "<h3><a href=\"#\">New Contact";
	echo "</a></h3>\n";
	echo "<div>\n";

	//and do the blank one
	echo "<div id=\"newcontactsearch\"></div>";
	draw_contact_form($sponsor_id);
	echo "</div>\n";

	// and finally end the accordion
	echo "</div>\n";

}

// draw a form in which to enter information about the various contacts
function draw_contact_form($sponsor_id, $contact = null){
	global $salutations;
	global $config;

	//grab the sponsor details, so we can do diff things for individual vs organization
	$q=mysql_query("SELECT * FROM sponsors WHERE id='$sponsor_id'");
	$sponsor=mysql_fetch_object($q);

	if($contact != null){
		$id = $contact["id"];
	}else{
		$id = "new";
		if($sponsor->donortype=="individual") {
			list($firstname,$lastname)=split(" ",$sponsor->organization,2);
			$contact['firstname']=$firstname;
			$contact['lastname']=$lastname;
			$contact['email']=$sponsor->email;
			$contact['phonehome']=$sponsor->phone;
		}
		else {
			$contact['phonework']=$sponsor->phone;
		}

		$contact['fax']=$sponsor->fax;
		$contact['address']=$sponsor->address;
		$contact['address2']=$sponsor->address2;
		$contact['city']=$sponsor->city;
		$contact['province']=$sponsor->province_code;
		$contact['postalcode']=$sponsor->postalcode;

	}

	echo "<form id=\"contact_" . $id . "\" method=\"post\" action=\"donors.php?action=contactsinfo_save\">\n";
	echo "<input type=\"hidden\" name=\"sponsor_id\" value=\"$sponsor_id\">\n";
	if($id == "new"){
		echo "<input type=\"hidden\" name=\"recordtype\" value=\"new\">\n";
		$newcontactsearch="onkeypress=\"return newcontactsearch()\"";
		$newcontactsave="id=\"contactnewsave\"";
	}else{
		echo "<input type=\"hidden\" name=\"recordtype\" value=\"existing\">\n";
		echo "<input type=\"hidden\" name=\"userid\" value=\"" . $id . "\">\n";
	}
?>
	<table class="tableedit">
		<tr>
			<td><?=i18n("Salutation"); ?></td>
			<td>
			<select name="salutation">
			<option value=""><?=i18n("Choose")?></option>
			<?
			foreach($salutations AS $salutation) {
				if($contact['salutation']==$salutation) $sel="selected=\"selected\""; else $sel="";
				echo "<option $sel value=\"$salutation\">$salutation</option>\n";
			}
			?>
			</select>
			</td>
			<td><?=i18n("Position"); ?></td>
			<? 
				if($sponsor->donortype=="individual") {
					$d="disabled=\"disabled\"";
			}
			else $d="";
			?>

			<td><input type="text" name="position" <?=$d?> value = "<?=htmlspecialchars($contact['position'])?>"></td>
		</tr>
		<tr>
			<td><?=i18n("First Name"); ?></td>
			<td><input <?=$newcontactsearch?> type="text" name="firstname" value = "<?=htmlspecialchars($contact['firstname'])?>"></td>
			<td><?=i18n("Last Name"); ?></td>
			<td><input <?=$newcontactsearch?> type="text" name="lastname" value = "<?=htmlspecialchars($contact['lastname'])?>"></td>
		</tr>
		<tr>
			<td><?=i18n("Email"); ?></td>
			<td colspan="3"><input <?=$newcontactsearch?> type="text" name="email" size="60" value = "<?=htmlspecialchars($contact['email'])?>"></td>
		</tr>
		<tr>
			<td><?=i18n("Phone (Work)"); ?></td>
			<td><input type="text" name="phonework" value = "<?=htmlspecialchars($contact['phonework'])?>"></td>
			<td><?=i18n("Phone (Cell)"); ?></td>
			<td><input type="text" name="phonecell" value = "<?=htmlspecialchars($contact['phonecell'])?>"></td>
		</tr>
		<tr>
			<td><?=i18n("Phone (Home)"); ?></td>
			<td><input type="text" name="phonehome" value = "<?=htmlspecialchars($contact['phonehome'])?>"></td>
			<td><?=i18n("Fax"); ?></td>
			<td><input type="text" name="fax" value = "<?=htmlspecialchars($contact['fax'])?>"></td>
		</tr>

			<tr><td><?=i18n("Address 1")?></td><td colspan="3"><input type="text" name="address" size="60" maxlength="64" value="<?=htmlspecialchars($contact['address'])?>" /></td></tr>
			<tr><td><?=i18n("Address 2")?></td><td colspan="3"><input type="text" name="address2" size="60" maxlength="64" value="<?=htmlspecialchars($contact['address2'])?>" /></td></tr>
			<tr><td><?=i18n("City")?></td><td><input id="city" type="text" name="city" size="16" maxlength="32" value="<?=htmlspecialchars($contact['city'])?>" /></td>
			<td><?=i18n($config['provincestate'])?></td><td>
				<? emit_province_selector("province_code",$contact['province']); ?>
			</td></tr>
			<tr><td><?=i18n($config['postalzip'])?></td><td colspan="3"><input type="text" name="postalcode" size="8" maxlength="7" value="<?=htmlspecialchars($contact['postalcode'])?>" /></td></tr>


		<tr>
			<td><?=i18n("Notes"); ?></td>
			<td colspan="3"><textarea name="notes" cols="60" rows="4"><?=htmlspecialchars($contact['notes'])?></textarea></td>
		</tr>
		<tr>
			<td><?=i18n("Primary Contact")?></td>
			<td>
				<label><?=i18n("Yes")?><input type="radio" name="primary" value="yes" <? if($contact['primary'] == 'yes') echo "checked=\"checked\"";?></label>
				<label><?=i18n("No")?><input type="radio" name="primary" value="no" <? if($contact['primary'] != 'yes') echo "checked=\"checked\"";?>></label>
			</td>
<?php
			echo "<td align=\"center\"><input $newcontactsave type=\"submit\" value=\"" . i18n("Save") . "\" onClick=\"return contactsinfo_save('" . $id . "')\" /></td>";
			echo "<td>";
			if($id != "new")
				echo "<input type=\"submit\" value=\"" . i18n("Remove") . "\" onClick=\"return contactsinfo_delete('" . $id . "')\" />";
			echo "</td>";
?>
		</tr>
	</table>
</form>
<?php
}


function draw_activityinfo_form(){
	global $config;
	$sponsorid = $_GET['id'];
// we'll start by drawing the table header
?>
	<form id="activityinfo">
	<table class="tableview" style="width:99%">
	<thead>
		<tr>
			<th><?=i18n("Date")?></th>
			<th><?=i18n("Committee Member")?></th>
			<th><?=i18n("Contact Type")?></th>
			<th><?=i18n("Appeal")?></th>
			<th><?=i18n("Notes")?></th>
		</tr>
		<tr>
			<td align="center" ><input type="submit" value="<?=i18n("Add Log")?>" onClick="return activityinfo_save()" /></td>
			<td align="center"><?=$_SESSION['name']?></td>
			<td align="center">
<?php
				echo "<select name=\"type\">";
				echo "<option value=\"\">".i18n("Choose Type")."</option>\n";
				$logtypes=array("Appeal","Phone Call","Email","Personal Visit","Other");
				foreach($logtypes AS $lt) {
					echo "<option value=\"$lt\">".i18n($lt)."</option>\n";
				}
				echo "</select>\n";
?>
			</td>
			<td align="center">
<?php
				$q=mysql_query("SELECT * FROM fundraising_campaigns WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY name");
				echo "<select name=\"fundraising_campaigns_id\">";
				echo "<option value=\"\">".i18n("Choose Appeal")."</option>\n";
				while($r=mysql_fetch_object($q)) {
					echo "<option value=\"$r->id\">$r->name</option>\n";
				}
				echo "</select>\n";
?>
			</td>
			<td><input type="text" name="comment" style="width: 99%;"/></td>
		</tr>
	</thead>
	<tbody>


<?php
	$query = "SELECT CONCAT_WS('&nbsp;', users.firstname, users.lastname) AS name, fdl.dt, fdl.log, fdl.type,
			fundraising_campaigns.name AS campaignname
		  FROM fundraising_donor_logs AS fdl 
		  LEFT JOIN users ON fdl.users_id=users.id 
		  LEFT JOIN fundraising_campaigns ON fdl.fundraising_campaigns_id=fundraising_campaigns.id
		  WHERE sponsors_id=" . $sponsorid. " ORDER BY dt DESC";
	//echo "<tr><td colspan=\"3\">" . $query . "</td></tr>";
	$q = mysql_query($query);
	echo mysql_error();
	if(mysql_num_rows($q)) {
		while($r = mysql_fetch_array($q)) {
			echo "<tr><td>" . $r["dt"] . "</td>";
			echo "<td>" . $r["name"] . "</td>";
			echo "<td>" . $r["type"] . "</td>";
			echo "<td>" . $r["campaignname"] . "</td>";
			echo "<td>" . $r["log"] . "</td>";
			echo "</tr>\n";
		}
	}else{
		echo "<tr><td colspan=\"5\" style=\"text-align:center\">" . i18n("No records") . "</td></tr>";
	}
?>

	</tbody></table>
	</form>
<?php
}

// Save an activity info log. 
function save_activityinfo($comment, $donorId, $userId, $type, $campaign_id=null){
	if($campaign_id) $cid="'$campaign_id'"; else $cid="NULL";

	$query = "INSERT INTO fundraising_donor_logs (sponsors_id, dt, users_id, log, `type`, fundraising_campaigns_id) 
		VALUES ($donorId,
		NOW(),
		$userId,
		'".mysql_real_escape_string($comment)."',
		'".mysql_real_escape_string($type)."',
		$cid)";
	mysql_query($query);
	echo mysql_error();
}

function getDonationString($id) {
	global $config;
	$q=mysql_query("SELECT fundraising_donations.*, 
		fundraising_campaigns.name AS campaignname 
		FROM fundraising_donations 
		LEFT JOIN fundraising_campaigns ON fundraising_donations.fundraising_campaigns_id=fundraising_campaigns.id 
		WHERE fundraising_donations.id='$id' 
		AND fundraising_donations.fiscalyear='{$config['FISCALYEAR']}' 
		");
	echo mysql_error();
	$str="";
	if($r=mysql_fetch_object($q)) {
		$str.=format_date($r->datereceived)." - ";
		$goal=getGoal($r->fundraising_goal);
		if($goal) {
			$str.=i18n("Goal: %1",array($goal->name));
		}
		else {
			$str.=i18n("Goal: none");
		}
		$str.= " - ";
		if($r->campaignname) {
			$str.= i18n("Campaign: %1",array($r->campaignname));
		}
		else {
			$str.=i18n("Campaign: none");
		}
		$str.= " - ";
		$str.= " Value: ".format_money($r->value,false);
	}
	else {
		return false;
	}
	return $str;
}



?>
<script type="text/javascript">
/* Setup the popup window */
$(document).ready(function() {
/*`
	$("#open_editor").dialog({
		bgiframe: true, autoOpen: false,
		modal: true, resizable: false,
		draggable: false
	});
*/

	$("#editor_tabs").tabs({
		show: function(event, ui) {
			switch(ui.panel.id) {
				case 'editor_tab_organization':
					update_organizationinfo();
					break;
				case 'editor_tab_sponsorship':
					update_sponsorshipinfo();
					break;
				case 'editor_tab_contacts':
					update_contactsinfo();
					break;
				case 'editor_tab_activity':
					update_activityinfo();
				break;
			}
		},
		selected: 0
	});

	$("#organizationinfo_fundingselectiondate").datepicker({ dateFormat: 'yy-mm-dd'});
	$("#organizationinfo_proposalsubmissiondate").datepicker({ dateFormat: 'yy-mm-dd'});
	//, showOn: 'button', buttonText: "<?=i18n("calendar")?>" });
	open_search();
});


var sponsor_id=0;
function open_editor(id) {
	sponsor_id=id;
	$("#donor_editor").show();
	$("#searchbrowse").hide();
	$("#searchresults").hide();

	if(id==-1) {
		$('#editor_tabs').tabs('option', 'selected', 0);
		$('#editor_tabs').tabs('option', 'disabled', [1,2,3]);

		$("#organizationinfo_organization").val("");
		$("#organizationinfo_address").val("");
		$("#organizationinfo_address2").val("");
		$("#organizationinfo_city").val("");
		$("#organizationinfo_province_code").val("");
		$("#organizationinfo_postalcode").val("");
		$("#organizationinfo_phone").val("");
		$("#organizationinfo_tollfree").val("");
		$("#organizationinfo_fax").val("");
		$("#organizationinfo_email").val("");
		$("#organizationinfo_website").val("");
		$("#organizationinfo_donationpolicyurl").val("");
		$("#organizationinfo_fundingselectiondate").val("");
		$("#organizationinfo_proposalsubmissiondate").val("");
		$("#organizationinfo_notes").val("");

		$("#organizationinfo_save_button").attr('disabled','disabled');
		$("[name=donortype]").attr('checked','');

	}
	else {
		$('#editor_tabs').tabs('option', 'selected', 0);
		$('#editor_tabs').tabs('option', 'disabled', []);
	}

	update_organizationinfo();
	return false;
}

function open_search() {
	$("#donor_editor").hide();
	$("#searchbrowse").show();
	donorsearch(false);
}
function update_organizationinfo() 
{
	var id=sponsor_id;		
	if(!sponsor_id)
		return false;
	if(sponsor_id==-1) {
		$("#sponsor_id").val(-1);
		return false;
	}

	$.getJSON("<?=$_SERVER['PHP_SELF']?>?action=organizationinfo_load&id="+id,
	function(json){
		$("#donor_name").html("<h3>"+json.organization+"</h3>");
		$("#sponsor_id").val(json.id);
		$("#organizationinfo_organization").val(json.organization);
		$("#organizationinfo_address").val(json.address);
		$("#organizationinfo_address2").val(json.address2);
		$("#organizationinfo_city").val(json.city);
		$("#organizationinfo_province_code").val(json.province_code);
		$("#organizationinfo_postalcode").val(json.postalcode);
		$("#organizationinfo_phone").val(json.phone);
		$("#organizationinfo_tollfree").val(json.tollfree);
		$("#organizationinfo_fax").val(json.fax);
		$("#organizationinfo_email").val(json.email);
		$("#organizationinfo_website").val(json.website);
		$("#organizationinfo_donationpolicyurl").val(json.donationpolicyurl);
		$("#organizationinfo_fundingselectiondate").val(json.fundingselectiondate);
		$("#organizationinfo_proposalsubmissiondate").val(json.proposalsubmissiondate);
		$("#organizationinfo_notes").val(json.notes);
		// For some reason, with checkboxes, these have to be arrays
		$("[name=waiveraccepted]").val([json.waiveraccepted]);
		$("[name=donortype]").val([json.donortype]);

		donortypechange();
	});
}

function organizationinfo_save() {

	//if we're creating we need to do the post, and get the id it returns, so we can re-open the popup window with that id
	if($("#sponsor_id").val()==-1) {
		$.post("<?$_SERVER['PHP_SELF']?>?action=organizationinfo_save", $("#organizationinfo").serializeArray(),
		function(json) {
			open_editor(json.id);
		},
		"json");
	}
	else
		$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=organizationinfo_save", $("#organizationinfo").serializeArray());

	return false;
}

function update_sponsorshipinfo() 
{
	var id=sponsor_id;		
	$("#editor_tab_sponsorship").load("<?=$_SERVER['PHP_SELF']?>?action=sponsorshipinfo_load&id="+id, null,function() {
		$(".date").datepicker({ dateFormat: 'yy-mm-dd' });
		$('.tableview').tablesorter();
	});
}

function sponsorshipinfo_save() {
	$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=sponsorshipinfo_save", $("#sponsorshipinfo").serializeArray());
	return false;
}

function update_contactsinfo() 
{
	var id=sponsor_id;		
	$("#editor_tab_contacts").load("<?=$_SERVER['PHP_SELF']?>?action=contactsinfo_load&id="+id, null,
	function() {
		$("#contactaccordion").accordion();
	}
	);
}

function contactsinfo_save(uid) {
	var id=sponsor_id;		
	$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=contactsinfo_save&id="+id, $("#contact_" + uid).serializeArray(),
	function() {
		$("#contactaccordion").accordion();
		update_contactsinfo();
	});
	return false;
}

function contactsinfo_delete(uid) {
	var id=sponsor_id;		
	$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=contactsinfo_delete&id="+id, $("#contact_" + uid).serializeArray(),
	function() {
		$("#contactaccordion").accordion();
		update_contactsinfo();
	});
	return false;
}


function update_activityinfo() 
{
	var id=sponsor_id;		
	$("#editor_tab_activity").load(
		"<?=$_SERVER['PHP_SELF']?>?action=activityinfo_load&id="+id,
		null,
		function(){$('.tableview').tablesorter(); }
	);
	return false;
}

function activityinfo_save() {
	var id=sponsor_id;		
	$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=activityinfo_save&id="+id, $("#activityinfo").serializeArray(),
	function(){
		update_activityinfo();
	});
	return false;
}

function donorsearch(realsearch) {
	$("#searchresults").show();
	if(realsearch)
		$("#searchresults").load("donors_search.php", $("#searchform").serializeArray(), function(){$('.tableview').tablesorter(); });
	else
		$("#searchresults").load("donors_search.php", null, function(){$('.tableview').tablesorter(); }); //, $("#searchform").serializeArray());
	
	return false;
}

var searchtimer;

function newcontactsearch() {
	clearTimeout(searchtimer);
	searchtimer=setTimeout('donewcontactsearch()',300);
	return true;
}
function donewcontactsearch() {
		$("#newcontactsearch").load("<?=$_SERVER['PHP_SELF']?>?action=newcontactsearch",$("#contact_new").serializeArray());
}

function useexistingcontact(uid) {
	var id=sponsor_id;		
	$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=contactsinfo_addexisting",{id: id, uid: uid} ,function() { update_contactsinfo(); });
}

function togglefulldonationhistory() {
	$("#fulldonationhistory").toggle('slow');
	$("#thisyeardonationhistory").toggle('slow');
}

function campaignchange() {
	var campaignid=$("#fundraising_campaign_id").val();
	var goal=defaultgoals[campaignid];
	$("#fundraising_goal").val(goal);
}

function donortypechange() {
	if($("input[@name='donortype']:checked").val()=="organization") {
		$("#organizationinfo_logo").attr("disabled","");
		$("#organizationinfo_donationpolicyurl").attr("disabled","");
		$("#organizationinfo_fundingselectiondate").attr("disabled","");
		$("#organizationinfo_proposalsubmissiondate").attr("disabled","");
	}
	else if($("input[@name='donortype']:checked").val()=="individual") {
		$("#organizationinfo_logo").attr("disabled","disabled");
		$("#organizationinfo_donationpolicyurl").attr("disabled","disabled");
		$("#organizationinfo_fundingselectiondate").attr("disabled","disabled");
		$("#organizationinfo_proposalsubmissiondate").attr("disabled","disabled");
	}
	else {
	}
	$("#organizationinfo_save_button").attr('disabled','');
}

function adddonation() {
	var id=sponsor_id;		
	$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=donation_add", $("#addnewdonationform").serializeArray(),function() { update_sponsorshipinfo(); });
	return false;
}

function removedonation(donationid,sponsorid) {
	if(confirmClick('Are you sure you want to remove this donation/sponsorship?')) {
		$("#debug").load("<?$_SERVER['PHP_SELF']?>?action=donation_remove", {id: donationid, sponsors_id: sponsorid},function() { update_sponsorshipinfo(); });
	}
	return false;
}

</script>
<?

 
if($_GET['action']=="delete" && $_GET['delete'])
{
	//dont allow any deleting until we figure out what we need to do, infact, i think we never should hard delete
	//this should only soft-delete so things like awards from previous years are still all linked correctly.
	//mysql_query("DELETE FROM sponsors WHERE id='".$_GET['delete']."'");
	//echo happy("Sponsors cannot be deleted");
}

echo "<table cellspacing=2 width=740 border=0><tr>";
echo "<td>";
echo "<a href=\"#\" onclick=\"open_editor(-1)\">Add New Donor(s)/Sponsor(s)</a>\n";
echo "</td>";
echo "<td>";
echo "<a href=\"#\" onclick=\"open_search()\">View/Modify Donor(s)/Sponsor(s)</a>\n";
echo "</td>";
echo "</tr></table>";
echo "<hr />";
?>
<div id="donor_editor" title="Donor/Sponsor Editor" style="display: none; width: 770px;">
<div id="donor_name"></div>
	<div id="editor_tabs">
		<ul>
			<li><a href="#editor_tab_organization"><span><?=i18n('Donor/Sponsor Details')?></span></a></li>
			<li><a href="#editor_tab_contacts"><span><?=i18n('Contacts')?></span></a></li>
			<li><a href="#editor_tab_sponsorship"><span><?=i18n('Donations/Sponsorships')?></span></a></li>
			<li><a href="#editor_tab_activity"><span><?=i18n('Activity Log')?></span></a></li>
		</ul>

		<div id="editor_tab_organization">
			<form enctype="multipart/form-data" id="organizationinfo">
			<input type="hidden" name="sponsor_id" id="sponsor_id" value="0">
			<table class="tableedit" border=0>
			<tr><td><?=i18n("Donor Type")?></td><td colspan="5">
				<input id="donortype_individual" type="radio" name="donortype" value="individual" onchange="return donortypechange()" /><label for="donortype_individual"><?=i18n("Individual")?></label>
				<input id="donortype_organization" type="radio" name="donortype" value="organization" onchange="return donortypechange()" /><label for="donortype_organization"><?=i18n("Organization")?></label>
			</td></tr>
			<tr><td><?=i18n("Name")?></td><td colspan="5"><input class="translatable" type="text" id="organizationinfo_organization" name="organization" size="60" maxlength="128" /></td></tr>
			<tr><td><?=i18n("Address 1")?></td><td colspan="5"><input id="organizationinfo_address" type="text" name="address" size="60" maxlength="64" /></td></tr>
			<tr><td><?=i18n("Address 2")?></td><td colspan="5"><input id="organizationinfo_address2" type="text" name="address2" size="60" maxlength="64" /></td></tr>
			<tr><td><?=i18n("City")?></td><td><input id="organizationinfo_city" type="text" name="city" size="16" maxlength="32" /></td>
			<td><?=i18n($config['provincestate'])?></td><td>
				<? emit_province_selector("province_code","","id=\"organizationinfo_province_code\""); ?>
			</td>
			<td><?=i18n($config['postalzip'])?></td><td><input id="organizationinfo_postalcode" type="text" name="postalcode" size="8" maxlength="7" /></td></tr>
			<tr><td><?=i18n("Phone")?></td><td><input type="text" id="organizationinfo_phone" name="phone" size="16" maxlength="32" /></td>
			<td><?=i18n("Toll Free")?></td><td><input type="text" id="organizationinfo_tollfree" name="tollfree" size="16" maxlength="32" /></td>
			<td><?=i18n("Fax")?></td><td><input type="text" id="organizationinfo_fax" name="fax" size="16" maxlength="32" /></td></tr>
			<tr><td><?=i18n("Email")?></td><td colspan="5"><input type="text" id="organizationinfo_email" name="email" size="60" maxlength="128" /></td>
			</tr>
			<tr><td><?=i18n("Website")?></td><td colspan="5"><input type="text" id="organizationinfo_website" name="website" size="60" maxlength="128" /></td>
			</tr>
			<tr><td><?=i18n("Donation Policy")?></td><td colspan="5"><input id="organizationinfo_donationpolicyurl" type="file" name="donationpolicyurl" size="30" maxlength="128" /></td></tr>
			<tr><td><?=i18n("Logo")?></td><td colspan="5"><input type="file" id="organizationinfo_logo" name="logo" size="30" /></td></tr>
			<tr><td><?=i18n("Funding Selection Date")?></td><td><input id="organizationinfo_fundingselectiondate" type="text" name="fundingselectiondate" class="date" size="12" maxlength="12" /></td>
				<td><?=i18n("Proposal Submission Date")?></td><td colspan="3"><input id="organizationinfo_proposalsubmissiondate" type="text" name="proposalsubmissiondate" class="date" size="12" maxlength="12" /></td>
			</tr>
<?
/*
			<tr><td><?=i18n("Waiver Accepted")?></td><td>
			<input type="radio" id="organizationinfo_waiveraccepted_no" name="waiveraccepted" value="no"><label for="organizationinfo_waiveraccepted_no"><?=i18n("No")?></label> &nbsp;&nbsp;
			<input type="radio" id="organizationinfo_waiveraccepted_yes" name="waiveraccepted" value="yes"><label for="organizationinfo_waiveraccepted_yes"><?=i18n("Yes")?></label> &nbsp;&nbsp;
			</td>

			<td><?=i18n("Tax Receipt Requested")?></td><td>
			<input type="radio" id="organizationinfo_taxreceiptrequired_no" name="taxreceiptrequired" value="no"><label for="organizationinfo_taxreceiptrequired_no"><?=i18n("No")?></label> &nbsp;&nbsp;
			<input type="radio" id="organizationinfo_taxreceiptrequired_yes" name="taxreceiptrequired" value="yes"><label for="organizationinfo_taxreceiptrequired_yes"><?=i18n("Yes")?></label> &nbsp;&nbsp;
			</td>

			<td><?=i18n("Marketing Receipt Requested")?></td><td colspan="3">
			<input type="radio" id="organizationinfo_marketingreceiptrequired_no" name="marketingreceiptrequired" value="no"><label for="organizationinfo_marketingreceiptrequired_no"><?=i18n("No")?></label> &nbsp;&nbsp;
			<input type="radio" id="organizationinfo_marketingreceiptrequired_yes" name="marketingreceiptrequired" value="yes"><label for="organizationinfo_marketingreceiptrequired_yes"><?=i18n("Yes")?></label> &nbsp;&nbsp;
			</td></tr>
			*/
?>
			<tr><td><?=i18n("Notes")?></td><td colspan="5"><textarea id="organizationinfo_notes" name="notes" rows="4" cols="60"></textarea></td></tr>
			</table>
			<input id="organizationinfo_save_button" type="submit" value="<?=i18n("Save")?>" onClick="return organizationinfo_save()" />
			</form>
		</div>
		<div id="editor_tab_sponsorship">
		sponsorship
		</div>
		<div id="editor_tab_contacts">
		</div>
		<div id="editor_tab_activity">
		activity
		</div>
	</div>
</div>


<div id="searchbrowse" style="display: none;">
<form id="searchform" method="post" action="donors.php" onsubmit="return donorsearch(true)">
<input type="hidden" name="limit" value="-1" />
<?=i18n("Search")?>: <input type="text" name="search" />
<input id="search_donortype_individual" type="checkbox" name="donortype[]" value="individual" checked="checked" /><label for="search_donortype_individual"><?=i18n("Individual")?></label>
<input id="search_donortype_organization" type="checkbox" name="donortype[]" value="organization" checked="checked"/><label for="search_donortype_organization"><?=i18n("Organization")?></label>
<input type="submit" value="<?=i18n("Browse")?>" />
</form>
</div>
<div id="searchresults">
</div>
<?

if($_GET['action']=="add") {
?>
<script type="text/javascript">
$(document).ready(function() {
	open_editor(-1);
});
</script>
<?
}

send_footer();

?>
