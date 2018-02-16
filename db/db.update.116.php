<?
function db_update_116_post()
{
	global $config;

	/* Fix the users that have a 0 year */
	$q = mysql_query("UPDATE `users` SET year={$config['FAIRYEAR']} WHERE year=0");
	echo mysql_error();

	/* Fix users without a username */
	mysql_query("UPDATE `users` SET `username`=`email` WHERE `username`=''");

	/*randomize usernames for any user that doesnt have a username at this point */
	$q=mysql_query("SELECT id FROM `users` WHERE username=''");

	//this is ripped from user.inc.php's generate passsword function.
	//yes there's a chance of collisions, but i think highly unlikely enough that we
	//dont need to worry about it.
  	$available="ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789";
   	$len=strlen($available) - 1;
	while($r=mysql_fetch_object($q)) {
		$username="";
	  	for($x=0;$x<16;$x++)
	   		$username.=$available{rand(0,$len)};
		mysql_query("UPDATE users SET username='$username' WHERE id='$r->id'");
	}

	
	//okay now finally, there's a chance of duplicates from 
	//committee/volunteer that were in here before, so we need to merge 
	//them
	$q = mysql_query("SELECT * FROM `users` WHERE types LIKE '%committee%'");
	while($r = mysql_fetch_assoc($q)) {
		$orig_r = $r;
		$qq = mysql_query("SELECT * FROM `users` WHERE
					(`username`='{$r['username']}' OR `email`='{$r['email']}')
					AND `id`!={$r['id']}");
		if(mysql_num_rows($qq) == 0) continue;

		echo "User id {$r['id']} ({$r['username']} {$r['email']}) has multiple users, merging...\n";

		/* Now, there should only be two types, because the system is 
		 * only supposed to let committee members and volunteers be
		 * created, and it has only been in use for one year without 
		 * year stamps., but we'll handle any number */

		/* However, we will make the committee the record that sticks 
		 * */
		$delete_ids = array();
		$delete_userids = array();
		while($rr = mysql_fetch_assoc($qq)) {
			$delete_ids[] = "`id`={$rr['id']}";
			$delete_userids[] = "`users_id`={$rr['id']}";
			$keys = array_keys($rr);
			foreach($keys as $k) {
				switch($k) {
				case 'id':
					/* Skip */
					break;
				case 'types':
					/* Merge types */
					if(strstr($r['types'], $rr['types']) == false) {
						$r['types']= $r['types'].','.$rr['types'];
						echo "   New type: {$r['types']}\n";
					}
					break;
				default:
					/* Save data */
					if(trim($r[$k]) == '' && trim($rr[$k]) != '') {
						$r[$k] = $rr[$k];
					}
					break;
				}
			}
		}



		/* Construct SQL for a SET clause */
		$set = array();
		$keys = array_keys($r);
		foreach($keys as $k) {
			if($r[$k] != $orig_r[$k]) {
				$set[] = "`$k`='{$r[$k]}'";
			}
		}
		if(count($set)) {
			$query = join(',',$set);
			mysql_query("UPDATE `users` SET $query WHERE id={$r['id']}");
			echo "Update query: UPDATE `users` SET $query WHERE id={$r['id']}\n";
		}

		/* Join together the WHERE commands */
		$where_id = "WHERE ".join(" OR ", $delete_ids);
		$where_users_id = "WHERE ".join(" OR ", $delete_userids);

		echo "Merged... Deleting duplicate and adjusting volunteer tables...\n";
		/* Delete the dupe */
		mysql_query("DELETE FROM `users` $where_id");
		/* Update volunteer linkage */
		mysql_query("UPDATE `users_volunteer` SET `users_id`={$r['id']} $where_users_id");
		mysql_query("UPDATE `volunteer_positions_signup` SET `users_id`={$r['id']} $where_users_id");

		echo "done with this user.\n";

	}
	
	/* Create volunteer database entries for any that don't exist */
	$q = mysql_query("SELECT * FROM users WHERE types LIKE '%volunteer%'");
	while($i = mysql_fetch_object($q)) {
		mysql_query("INSERT INTO users_volunteer(`users_id`,`volunteer_active`,`volunteer_complete`)
				VALUES ('{$i->id}','yes','{$i->complete}')");
	}

	/* Update any remaining volunteer entries  */
	$q = mysql_query("SELECT * FROM users WHERE types LIKE '%volunteer%'");
	while($i = mysql_fetch_object($q)) {
		mysql_query("UPDATE users_volunteer 
				SET volunteer_complete='{$i->complete}' 
				WHERE users_id='{$i->id}'");
		echo mysql_error();
	}

	/* Every committee member role should be activated */
	$q = mysql_query("SELECT * FROM users WHERE types LIKE '%committee%'");
	while($i = mysql_fetch_object($q)) {
		mysql_query("UPDATE users_committee
				SET committee_active='yes' 
				WHERE users_id='{$i->id}'");
		echo mysql_error();
	}

	/* Convert Judges */
	$map = array();
	$jtl = array();
	$jsal = array();

	/* Select all judges, duplicate rows for each year */
	$jq = mysql_query("SELECT * FROM judges
				LEFT JOIN judges_years ON judges_years.judges_id=judges.id
				ORDER BY year");

	while($j = mysql_fetch_object($jq)) {

		if(!is_array($map[$j->id])) {
			$map[$j->id] = array('uid' => '');
		}

		$u = array( 'id' => '',
			'uid' => $map[$j->id]['uid'],
			'types' => 'judge',
			'firstname' => mysql_escape_string($j->firstname),
			'lastname' => mysql_escape_string($j->lastname),
			'username' => mysql_escape_string($j->email),
			'email' => mysql_escape_string($j->email),
			'sex' => '',
			'password' => mysql_escape_string($j->password),
			'passwordset' => $j->lastlogin, 
			'oldpassword' => '',
			'year' => $j->year,
			'phonehome' => mysql_escape_string($j->phonehome),
			'phonework' => mysql_escape_string($j->phonework.(($j->phoneworkext=='') ? '' : " x{$j->phoneworkext}")),
			'phonecell' => mysql_escape_string($j->phonecell),
			'fax' => '',
			'organization' => mysql_escape_string($j->organization),
			'lang' => '',  /* FIXME, or unused for judges?, this is preferred communication language, not judging languages */
			'created' => $j->created,
			'lastlogin' => $j->lastlogin,
			'address' => mysql_escape_string($j->address),
			'address2' => mysql_escape_string($j->address2),
			'city' => mysql_escape_string($j->city),
			'province' => mysql_escape_string($j->province),
			'postalcode' => mysql_escape_string($j->postalcode),
			'firstaid' => 'no',
			'cpr' => 'no',
			'deleted' => $j->deleted,
			'deleteddatetime' => $j->deleteddatetime );

		$updateexclude=array("id","uid","types","username","password","passwordset","oldpassword","year","created","lastlogin","firstaid","cpr","deleted","deleteddatetime");

		//check if a user already exists with this username
		$uq=mysql_query("SELECT * FROM users WHERE (username='".mysql_real_escape_string($j->email)."' OR email='".mysql_real_escape_string($j->email)."') AND year='$j->year'");
		if($j->email && $ur=mysql_fetch_object($uq)) {
			$id=$ur->id;
			 echo "Using existing users.id=$id for judges.id=$j->id because email address/year ($j->email/$j->year) matches\n";

			$sqlset="";
			foreach($u AS $f=>$v) {
				if(!$ur->$f && $j->$f && !in_array($f,$updateexclude)) {
					$sqlset.="`$f`='".mysql_real_escape_string($j->$f)."', ";
				}
			}
			$sql="UPDATE users SET $sqlset `types`='{$ur->types},judge',`username`='".mysql_real_escape_string($j->email)."' WHERE id='$id'";
			mysql_query($sql);
			echo mysql_error();
			echo "   Updated user record with judge info, but only merged:\n";
			echo "   ($sqlset)\n";

		}
		else
		{
			/* Insert the judge */
			$fields = '`'.join('`,`', array_keys($u)).'`';
			$vals = "'".join("','", array_values($u))."'";
			$q = mysql_query("INSERT INTO users ($fields) VALUES ($vals)");
			$id = mysql_insert_id();

			if($map[$j->id]['uid'] == '') {
				$map[$j->id]['uid'] = $id;
				$q = mysql_query("UPDATE users SET `uid`='$id' WHERE id='$id'");
			}
		}

		$uj = array( 'users_id' => "$id",
			'judge_active' => 'yes',
			'highest_psd' => mysql_escape_string($j->highest_psd),
			'special_award_only' => ($j->typepref == 'speconly') ? 'yes' : 'no',
			'expertise_other' => mysql_escape_string((($j->professional_quals != '')?($j->professional_quals."\n"):'').
						$j->expertise_other),
			/* These need to get pulled from the questions */
			'years_school' => $j->years_school,
			'years_regional' => $j->years_regional,
			'years_national' => $j->years_national,
			'willing_chair' => $j->willing_chair,
			'judge_complete' => $j->complete,
			);
//			$j->attending_lunch,

		/* catprefs */
		$q = mysql_query("SELECT * FROM judges_catpref WHERE judges_id='{$j->id}' AND year='{$j->year}'");
		$catpref = array();
		while($i = mysql_fetch_object($q)) {
			$catpref[$i->projectcategories_id] = $i->rank;
		}
		$uj['cat_prefs'] = mysql_escape_string(serialize($catpref));

		/* divprefs and subdivision prefs */
		$q = mysql_query("SELECT * FROM judges_expertise WHERE judges_id='{$j->id}' AND year='{$j->year}'");
		$divpref = array();
		$divsubpref = array();
		while($i = mysql_fetch_object($q)) {
			if($i->projectdivisions_id) 
				$divpref[$i->projectdivisions_id] = $i->val;
			else if ($i->projectsubdivisions_id) 
				$divsubpref[$i->projectsubdivisions_id] = $i->val;
		}
		$uj['div_prefs'] = mysql_escape_string(serialize($divpref));
		$uj['divsub_prefs'] = mysql_escape_string(serialize($divsubpref));

		/* languages */
		$q = mysql_query("SELECT * FROM judges_languages WHERE judges_id='{$j->id}'");
		$langs = array();
		while($i = mysql_fetch_object($q)) {
			$langs[] = $i->languages_lang;
		}
		$uj['languages'] = mysql_escape_string(serialize($langs));

		/* Map judges questions back to the profile.  We're going to keep questions we need for
		 * judge scheduling as hard-coded questions so users can't erase them. 
		 * "Years School" "Years Regional" "Years National" "Willing Chair" */
		$qmap = array('years_school' => 'Years School',
				'years_regional' => 'Years Regional',
				'years_national' => 'Years National',
				'willing_chair' => 'Willing Chair');
		foreach($qmap as $field=>$head) {
			/* Find the question ID */
			$q = mysql_query("SELECT id FROM questions WHERE year='{$j->year}' AND db_heading='{$head}'");
			if(mysql_num_rows($q) == 0) {
				echo "Warning: Question '$head' for judge {$j->id} doesn't exist in year '{$j->year}', cannot copy answer.\n";
				continue;
			}

			$i = mysql_fetch_object($q);

			/* Now find the answer */
			$q = mysql_query("SELECT * FROM question_answers WHERE 
					year='{$j->year}' AND 
					registrations_id='{$j->id}' AND
					questions_id='{$i->id}'");
			echo mysql_error();
			if(mysql_num_rows($q) == 0) {
				echo "Warning: Judge {$j->id} did not answer question '$head' in year '{$j->year}', cannot copy answer.\n";
				continue;
			}
			$i = mysql_fetch_assoc($q);
			$uj[$field] = $i['answer'];
		}

//		print_r($uj);

		$fields = '`'.join('`,`', array_keys($uj)).'`';
		$vals = "'".join("','", array_values($uj))."'";
		$q = mysql_query("INSERT INTO users_judge ($fields) VALUES ($vals)");
		echo mysql_error();

		/* FIXUP all the judging tables (but don't write back yet, we don't want to 
		 * accidentally create a duplicate judges_id and overwrite it later) */

		/* judges_teams_link */
		$q = mysql_query("SELECT * FROM judges_teams_link WHERE judges_id='{$j->id}' AND year='{$j->year}'");
		while($i = mysql_fetch_object($q))
			$jtl[$i->id] = $id;

		/* judges_specialawards_sel */
		$q = mysql_query("SELECT * FROM judges_specialaward_sel WHERE judges_id='{$j->id}' AND year='{$j->year}'");
		echo mysql_error();
		while($i = mysql_fetch_object($q)) 
			$jsal[$i->id] = $id;

		/* question_answers */
		$q = mysql_query("SELECT * FROM question_answers WHERE registrations_id='{$j->id}' AND year='{$j->year}'");
		echo mysql_error();
		while($i = mysql_fetch_object($q)) 
			$qa[$i->id] = $id;
	}

	/* Now write back the judge ids */
	if(count($jtl)) {
	foreach($jtl as $id=>$new_id) 
		$q = mysql_query("UPDATE judges_teams_link SET judges_id='$new_id' WHERE id='$id' ");
	}
	if(count($jsal)) {
	foreach($jsal as $id=>$new_id) 
		$q = mysql_query("UPDATE judges_specialaward_sel SET judges_id='$new_id' WHERE id='$id' ");
	}
	if(count($qa)) {
	foreach($qa as $id=>$new_id)
		$q = mysql_query("UPDATE question_answers SET registrations_id='$new_id' WHERE id='$id' ");
	}
}
?>
