<?
if($_POST['action']=="sponsorshipdelete") {
	mysql_query("DELETE FROM fundraising_donations WHERE id='".intval($_POST['delete'])."'");
	if(mysql_affected_rows())
		happy_("Successfully removed sponsorship");
   exit;
}

if($_POST['action']=="sponsorshipedit" || $_POST['action']=="sponsorshipadd") {
	$sponsors_id=intval($_POST['sponsors_id']);
	$fundraising_donations_id=intval($_POST['fundraising_donations_id']);
	$fundraising_type=mysql_real_escape_string($_POST['fundraising_type']);
	
	$value=mysql_real_escape_string($_POST['value']);
	$status=mysql_real_escape_string($_POST['status']);
	$probability=mysql_real_escape_string($_POST['probability']);

	if($status=="confirmed" || $status=="received") $probability="100";
	if($probability==100 && $status=="pending") $status="confirmed";
}

if($_POST['action']=="sponsorshipedit") {

	if($fundraising_donations_id && $fundraising_type && $value) {
		$q=mysql_query("SELECT * FROM fundraising_donations WHERE id='$fundraising_donations_id'");
		$current=mysql_fetch_object($q);

		unset($log);
		$log=array();
		if($current->fundraising_type!=$fundraising_type)
			$log[]="Changed sponsorship type from $current->fundraising_type to $fundraising_type";

		if($current->value!=$value)
			$log[]="Changed sponsorship value from $current->value to $value";

		if($current->status!=$status)
			$log[]="Changed sponsorship status from $current->status to $status";

		if($current->probability!=$probability)
			$log[]="Changed sponsorship probability from $current->probability to $probability";

		if(count($log)) {
			mysql_query("UPDATE fundraising_donations SET fundraising_type='$fundraising_type', value='$value', status='$status', probability='$probability' WHERE id='$fundraising_donations_id'");

			foreach($log AS $l) {
					mysql_query("INSERT INTO fundraising_donor_logs (sponsors_id,dt,users_id,log) VALUES (
						'$current->sponsors_id',
						NOW(),
						'".$_SESSION['users_id']."',
						'".mysql_real_escape_string($l)."')");

			}
			if(mysql_error())
				echo error_(mysql_error());
			else
                echo happy_("Saved sponsorship changes");
		}
		else
			echo error_("No changes were made");
	}
	else {
		echo error_("Required fields were missing, please try again".print_r($_POST,true));
	}
   exit;
}
if($_POST['action']=="sponsorshipadd") {
	if($sponsors_id && $fundraising_type && $value) {
		mysql_query("INSERT INTO fundraising_donations (sponsors_id,fundraising_type,value,status,probability,fiscalyear) VALUES ('$sponsors_id','$fundraising_type','$value','$status','$probability','{$config['FISCALYEAR']}')");
		mysql_query("INSERT INTO fundraising_donor_logs (sponsors_id,dt,users_id,log) VALUES (
			'$sponsors_id',
			NOW(),
			'".$_SESSION['users_id']."',
			'".mysql_real_escape_string("Created sponsorship: type=$fundraising_type, value=\$$value, status=$status, probability=$probability%")."')");
		happy_("Added new sponsorship");
	}
	else
		error_("Required fields were missing, please try again");
	if(mysql_error())
		error_(mysql_error());
   exit;
}

?>
