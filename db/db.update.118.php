<?
function db_update_118_post()
{
	global $config;
	$available="ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789";
	$availlen=strlen($available) - 1;

	$userfields=array("salutation","firstname","lastname","email","phonehome","phonework","phonecell","fax");

	//grab all the contacts from awards_contacts
	$q=mysql_query("SELECT * FROM award_contacts");
	while($r=mysql_fetch_object($q)) {

		//if its older than the current year, then set them to complete/active because if they were in the
		//system then, then they must have beenc omplete and active
		//set anyone for the current fair year to complete=no, active = yes, because its not too late to get them
		//to login and make sure that all the info is complete
		if($r->year<$config['FAIRYEAR']) {
			$complete="yes";
			$active="yes";
		}
		else {
			$complete="no";
			$active="yes";
		}
		//see if a user exists with this email
		$uq=mysql_query("SELECT * FROM users WHERE (username='".mysql_real_escape_string($r->email)."' OR email='".mysql_real_escape_string($r->email)."') ORDER BY year DESC LIMIT 1"); // AND year='$r->year'");
		if($r->email && $ur=mysql_fetch_object($uq)) {
			$user_id=$ur->id;
			echo "Using existing users.id=$user_id for award_contacts.id=$r->id because email address ($r->email) matches\n";

			//update any info we have thats missing
			$sqlset="";
			foreach($userfields AS $f) {
				//if its NOT in their USER record, but it IS in their AWARD_CONTACTS record, then bring it over, else, assume the users record has priority
				if(!$ur->$f && $r->$f) {
					$sqlset.="`$f`='".mysql_real_escape_string($r->$f)."', ";
				}
			}
			$sql="UPDATE users SET $sqlset `types`='{$ur->types},sponsor' WHERE id='$user_id'";
			mysql_query($sql);
			echo mysql_error();
			echo "  Updated user record\n";

		}
		else {
			//we need a username, if htere's no email, then we need to gerneate one to use.
			if($r->email) {
				$username=$r->email;
			}
			else {
				$username="";
				for($x=0;$x<16;$x++)
					$username.=$available{rand(0,$availlen)};
			}

			//and create them a password
			$password="";
			for($x=0;$x<8;$x++)
				$password.=$available{rand(0,$availlen)};

			//set passwordset to 0000-00-00 to force it to expire on next login
			$sql="INSERT INTO users (`types`,`username`,`created`,`password`,`passwordset`,`".implode("`,`",$userfields)."`,`year`) VALUES (";
			$sql.="'sponsor','".mysql_real_escape_string($username)."',NOW(),'$password','0000-00-00'";
			foreach($userfields AS $f) {
				$sql.=",'".mysql_real_escape_string($r->$f)."'";
			}
			$sql.=",'".mysql_real_escape_string($r->year)."')";
			mysql_query($sql);
			echo mysql_error();

			$user_id=mysql_insert_id();
			//and link it to themselves as a starting record
			mysql_query("UPDATE users SET uid='$user_id' WHERE id='$user_id'");
			echo "Creating new users.id=$user_id for award_contacts.id=$r->id\n";

		}

		echo "  Linking $user_id to users_sponsor record\n";
		mysql_query("INSERT INTO users_sponsor (`users_id`,`sponsors_id`,`sponsor_complete`,`sponsor_active`,`primary`,`position`,`notes`) VALUES (
				'".$user_id."',
				'".$r->award_sponsors_id."',
				'$complete',
				'$active',
				'".mysql_real_escape_string($r->primary)."',
				'".mysql_real_escape_string($r->position)."',
				'".mysql_real_escape_string($r->notes)."')");
		echo mysql_error();
	}
}

?>
