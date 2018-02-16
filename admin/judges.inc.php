<?
function getJudgingTeams()
{
	global $config;

	$q=mysql_query("SELECT 	judges_teams.id,
				judges_teams.num,
				judges_teams.name
			FROM 
				judges_teams
			WHERE 
				judges_teams.year='".$config['FAIRYEAR']."'
			ORDER BY 
				num,name
				");

	$lastteamid=-1;
	$lastteamnum=-1;
	echo mysql_error();
	$teams=array();
	while($r=mysql_fetch_object($q))
	{
		$teams[$r->id]['id']=$r->id;
		$teams[$r->id]['num']=$r->num;
		$teams[$r->id]['name']=$r->name;
		$lastteamid=$r->id;
		$lastteamnum=$r->num;

		/* Load timeslots */
		$rounds = array();
		$tq = mysql_query("SELECT * FROM judges_teams_timeslots_link
					LEFT JOIN judges_timeslots ON judges_timeslots.id=judges_teams_timeslots_link.judges_timeslots_id
					WHERE judges_teams_timeslots_link.judges_teams_id='{$r->id}'");
		$teams[$r->id]['timeslots'] = array();
		$teams[$r->id]['rounds'] = array();
		while($ts = mysql_fetch_assoc($tq)) {
			$teams[$r->id]['timeslots'][] = $ts;
			$rounds[$ts['round_id']] = $ts['round_id'];
		}
		foreach($rounds as $round_id) {
			$tq = mysql_query("SELECT * FROM judges_timeslots WHERE id='{$round_id}'");
			$teams[$r->id]['rounds'][] = mysql_fetch_assoc($tq);
		}

		//get the members for this team
		$mq=mysql_query("SELECT 	
			users.id AS judges_id,
			users.firstname,
			users.lastname,
			judges_teams_link.captain
			
		FROM 
			users,
			judges_teams_link
		WHERE 
			judges_teams_link.users_id=users.id AND
			judges_teams_link.judges_teams_id='$r->id'
		ORDER BY 
			captain DESC,
			lastname,
			firstname");
		echo mysql_error();

		
        $teamlangs=array();
		while($mr=mysql_fetch_object($mq))
		{
			$u = user_load($mr->judges_id, false);
			$judgelangs = join('/', $u['languages']);
			foreach($u['languages'] AS $l) {
				if(!in_array($l,$teamlangs))
					$teamlangs[]=$l;
			}
			
			$teams[$lastteamid]['members'][]=array(
				"id"=>$mr->judges_id,
				"firstname"=>$mr->firstname,
				"lastname"=>$mr->lastname,
				"captain"=>$mr->captain,
				"languages"=>$judgelangs,
				"languages_array"=>$u['languages']
				);
		}
        $teams[$r->id]['languages_members']=$teamlangs;

		//we also need to add all the languages that the team must JUDGE to the teams languages.
		$lq=mysql_query("SELECT projects.language
			FROM judges_teams_timeslots_projects_link 
			LEFT JOIN projects ON judges_teams_timeslots_projects_link.projects_id=projects.id
			WHERE judges_teams_timeslots_projects_link.year='{$config['FAIRYEAR']}' AND
			judges_teams_id='$r->id' ");
		echo mysql_error();
		$projectlangs=array();
		while($lr=mysql_fetch_object($lq)) 
		{
			if(!in_array($lr->language,$projectlangs))
				$projectlangs[]=$lr->language;
			if(!in_array($lr->language,$teamlangs))
				$teamlangs[]=$lr->language;
		}
		//
    $teams[$r->id]['languages_projects']=$projectlangs;
    $teams[$r->id]['languages']=$teamlangs;

		//get the awards for this team
		$aq=mysql_query("SELECT award_awards.id,
					award_awards.name,
					award_awards.criteria,
					award_awards.award_types_id,
					award_types.type AS award_type
				FROM
					award_awards,
					judges_teams_awards_link,
					award_types
				WHERE
					judges_teams_awards_link.award_awards_id=award_awards.id
					AND judges_teams_awards_link.judges_teams_id='$r->id'
					AND award_awards.award_types_id=award_types.id
					AND award_types.year='{$config['FAIRYEAR']}'
				ORDER BY
					name
				");
		while($ar=mysql_fetch_object($aq))
		{
			$teams[$r->id]['awards'][]=array(
					"id"=>$ar->id,
					"name"=>$ar->name,
					"criteria"=>$ar->criteria,
					"award_types_id"=>$ar->award_types_id,
					"award_type"=>$ar->award_type
					);
		}
	}
	return $teams;
}

function getJudgingTeam($teamid)
{
	global $config;

	$q=mysql_query("SELECT 	judges_teams.id,
				judges_teams.num,
				judges_teams.name
				
			FROM 
				judges_teams
			WHERE 
				judges_teams.year='".$config['FAIRYEAR']."' AND
				judges_teams.id='$teamid'
			ORDER BY 
				num,
				name
				");

	$team=array();

	$first=true;
	while($r=mysql_fetch_object($q))
	{
		$team['id']=$r->id;
		$team['num']=$r->num;
		$team['name']=$r->name;

		//get the members for this team
		$mq=mysql_query("SELECT 	
			users.id AS judges_id,
			users.firstname,
			users.lastname,
			judges_teams_link.captain
			
		FROM 
			users,
			judges_teams_link
		WHERE 
			judges_teams_link.users_id=users.id AND
			judges_teams_link.judges_teams_id='$r->id'
		ORDER BY 
			captain DESC,
			lastname,
			firstname");
		echo mysql_error();

		
		while($mr=mysql_fetch_object($mq))
		{
			$team['members'][]=array(
				"id"=>$mr->judges_id,
				"firstname"=>$mr->firstname,
				"lastname"=>$mr->lastname,
				"captain"=>$mr->captain
				);
		}


		//get the awards for this team
		$aq=mysql_query("SELECT award_awards.id,
					award_awards.name,
					award_awards.award_types_id,
					award_types.type AS award_type
				FROM
					award_awards,
					judges_teams_awards_link,
					award_types
				WHERE
					judges_teams_awards_link.award_awards_id=award_awards.id
					AND judges_teams_awards_link.judges_teams_id='$r->id'
					AND award_awards.award_types_id=award_types.id
					AND award_types.year='{$config['FAIRYEAR']}'
				ORDER BY
					name
				");
		while($ar=mysql_fetch_object($aq))
		{
			$team['awards'][]=array(
					"id"=>$ar->id,
					"name"=>$ar->name,
					"award_types_id"=>$ar->award_types_id,
					"award_type"=>$ar->award_type
					);
		}


	}

	return $team;

}

function getJudgingEligibilityCode() {
	global $config;
	switch($config['project_status']) {
		case 'open' :
			return " AND registrations.status != 'open' ";
			break;
		case 'payment_pending' :
			return " AND registrations.status IN ('paymentpending', 'complete')";
			break;
		case 'complete' :
			return " AND registrations.status = 'complete'";
			break;
	}
}

function teamMemberToName($member)
{
       return $member["firstname"] . " " . $member["lastname"];
}

function judges_load_all()
{
	global $config;

	$ret = array();

	$query = "SELECT id FROM users WHERE types LIKE '%judge%' 
				AND year='{$config['FAIRYEAR']}'
				AND deleted='no'
				ORDER BY lastname, firstname";
	$r = mysql_query($query);
	while($i = mysql_fetch_assoc($r)) {
		$u = user_load($i['id']);
		if($u['judge_complete'] == 'no') continue;
		if($u['judge_active'] == 'no') continue;

		$ret[$i['id']] = $u;
	}
	return $ret;
}

?>
