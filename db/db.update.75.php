<?


function db_update_75_pre()
{
}

function db_update_75_post()
{
	global $config;

	$q = mysql_query("SELECT id FROM users WHERE types LIKE '%committee%'");

	$x = 0;
	while($i = mysql_fetch_object($q)) {
		$uid = $i->id;

		$sid = array(9, 36, -1, -2, 17, 19, 16, 30, 26, 27,
				28, -3, 21, 22, -4, -6, 29, -8, -9);
		foreach($sid as $s) {
			if($s > 0) {
				$qq = mysql_query("SELECT id FROM reports WHERE 
						system_report_id='$s'");
				$ii = mysql_fetch_object($qq);
				$rid[$x] = $ii->id;
			} else {
				$rid[$x] = $s;
			}
			$x++;
		}


		/* Find all committee members */
		$qq = "INSERT INTO `reports_committee` (`id`, `users_id`, `reports_id`, `category`, `comment`, `format`, `stock`) VALUES
		(NULL, $uid, {$rid[0]}, '1. Fair Day', 'Checkin Lists for the Front Desk', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[1]}, '2. Old Custom Reports', 'School Access Codes and Passwords', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[2]}, '2. Old Custom Reports', 'Mailing Label Generator', '', ''),
		(NULL, $uid, {$rid[3]}, '2. Old Custom Reports', 'Project Summary Details', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[4]}, '2. Old Custom Reports', 'Student emergency contact names and numbers', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[5]}, '2. Old Custom Reports', 'Students/Projects From Each School', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[6]}, '2. Old Custom Reports', 'Project Logistical Requirements (tables, electricity)', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[7]}, '2. Old Custom Reports', 'Project Table Labels', 'label', 'fullpage_landscape'),
		(NULL, $uid, {$rid[8]}, '2. Old Custom Reports', 'Student Nametags', 'label', 'nametag'),
		(NULL, $uid, {$rid[9]}, '2. Old Custom Reports', 'Judge Nametags', 'label', 'nametag'),
		(NULL, $uid, {$rid[10]}, '2. Old Custom Reports', 'Committee Member Nametags', 'label', 'nametag'),
		(NULL, $uid, {$rid[11]}, '2. Old Custom Reports', 'Judges List', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[12]}, '2. Old Custom Reports', 'Judging Teams', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[13]}, '2. Old Custom Reports', 'Awards each Judging Team will judge for', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[14]}, '2. Old Custom Reports', 'Judging Teams Project Assignments', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[15]}, '2. Old Custom Reports', 'Projects Judging Team Assignments', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[16]}, '2. Old Custom Reports', 'Project Identification Labels (for judging sheets)', 'label', '5961'),
		(NULL, $uid, {$rid[17]}, '2. Old Custom Reports', 'Award List for Award Ceremony Program', 'pdf', 'fullpage'),
		(NULL, $uid, {$rid[18]}, '2. Old Custom Reports', 'Winners for each award', 'pdf', 'fullpage');";

		echo $qq;
		echo "\n\n";

		mysql_query($qq);
	}
}


?>
