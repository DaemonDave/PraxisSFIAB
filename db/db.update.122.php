<?

function db_update_122_post()
{
	global $config;
	$year = $config['FAIRYEAR'];
	$q = mysql_query("SELECT * FROM judges_timeslots WHERE year='$year'");
	$round = array();
	while($r = mysql_fetch_assoc($q)) {
		$type = $r['type'];

		if(!array_key_exists($type, $round)) {
			$round[$type]['starttime'] = $r['starttime'];
			$round[$type]['endtime'] = $r['endtime'];
			$round[$type]['date'] = $r['date'];
		}

		if($r['starttime'] < $round[$type]['starttime'] ) {
			$round[$type]['starttime'] = $r['starttime'];
		}

		if($r['endtime'] > $round[$type]['endtime']) {
			$round[$type]['endtime'] = $r['endtime'];
		}
	}

	foreach($round as $type=>$d) {
		mysql_query("INSERT INTO judges_timeslots (round_id,type,date,starttime,endtime,year)
			VALUES ('0','$type','{$d['date']}','{$d['starttime']}','{$d['endtime']}','$year')");
		$round_id = mysql_insert_id();

		mysql_query("UPDATE judges_timeslots SET 
				round_id='$round_id', type='timeslot'
				WHERE type='$type' AND year='$year'");

		/* Undo the set we just did to the round we just inserted */
		mysql_query("UPDATE judges_timeslots SET 
				round_id='0',type='$type'
				WHERE id='$round_id'");
	}
}

?>
