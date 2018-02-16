<?
if($_POST['action']=="funddelete" && $_POST['delete']) {
	//first lookup all the sponsorships inside the fund
	$id=intval($_POST['delete']);
	$q=mysql_query("SELECT * FROM fundraising_goals WHERE id='$id' AND year='".$config['FISCALYEAR']."'");
	$f=mysql_fetch_object($q);
	//hold yer horses, no deleting system funds!
	if($f) {
		if($f->system=="no") {
			mysql_query("DELETE FROM fundraising_donations WHERE fundraising_goal='".mysql_real_escape_string($f->type)."' AND fiscalyear='".$config['FISCALYEAR']."'");
			mysql_query("DELETE FROM fundraising_goals WHERE id='$id'");
			if(mysql_affected_rows())
				happy_("Successfully removed fund %1",array($f->name));
		}
		else {
			error_("Cannot remove system fund");
		}
	}
    exit;
}
if($_POST['action']=="fundedit" || $_POST['action']=="fundadd") {
	$fundraising_id=intval($_POST['fundraising_id']);
	if($fundraising_id) {
		$q=mysql_query("SELECT * FROM fundraising_goals WHERE id='$fundraising_id'");
		$f=mysql_fetch_object($q);
		$system=$f->system;
	}
	$name=mysql_real_escape_string($_POST['name']);
	$goal=mysql_real_escape_string($_POST['goal']);
	$description=mysql_real_escape_string($_POST['description']);
	$budget=intval($_POST['budget']);
}

if($_POST['action']=="fundedit") {
	if( ($system=="yes" && $budget) || ($system=="no" && $budget && $goal && $name) ) {
		if($system=="yes") {
			mysql_query("UPDATE fundraising SET budget='$budget', description='$description' WHERE id='$fundraising_id'");
		}
		else {
			mysql_query("UPDATE fundraising SET budget='$budget', description='$description', goal='$goal', name='$name' WHERE id='$fundraising_id'");
		}
		if(mysql_error())
			error_("MySQL Error: %1",array(mysql_error()));
		else
			happy_("Saved fund changes");
	}
	else {
		error_("Required fields were missing, please try again");
	}
    exit;
	
}
if($_POST['action']=="fundadd") {
	if( $goal && $type && $name) {
		mysql_query("INSERT INTO fundraising_goals (goal,name,description,system,budget,fiscalyear) VALUES ('$goal','$name','$description','no','$budget','{$config['FISCALYEAR']}')");
		happy_("Added new fund");
	}
	else
		error_("Required fields were missing, please try again");
	if(mysql_error())
		error_("MySQL Error: %1",array(mysql_error()));
    exit;
}

?>
