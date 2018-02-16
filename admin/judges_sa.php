<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>

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
 require_once('../common.inc.php');
 require_once('../user.inc.php');
 require_once('../questions.inc.php');
 require_once('../projects.inc.php');
 require_once('judges.inc.php');
 require_once('anneal.inc.php');

if($_SERVER['SERVER_ADDR']) {
	echo "This script must be run from the command line";
	exit;
}

//function TRACE() { }
//function TRACE_R() { }
function TRACE($str) { print($str); }
function TRACE_R($array) { print_r($array); }


TRACE("<pre>");

$round_divisional1 = NULL;
$round_divisional2 = NULL;



function set_status($txt)
{
	TRACE("Status: $txt\n");
	mysql_query("UPDATE config SET val='$txt' WHERE 
			var='judge_scheduler_activity' AND year=0");
}

$set_percent_last_percent = -1;
function set_percent($n)
{
	global $set_percent_last_percent;
	$p = floor($n);
	if($p == $set_percent_last_percent) return;
	TRACE("Progress: $p\%\n");
	mysql_query("UPDATE config SET val='$p' WHERE 
			var='judge_scheduler_percent' AND year=0");
	$set_percent_last_percent = $p;
}

set_status("Initializing...");
set_percent(0);

/* The cost function is:
	+ 50 * each judge below the min for each team
	+ 10 * each judge above the max for each team
	+  2 * each level of preference away from the 
		max level for each judge
	+ 40 if the team doesn't have a chair.
	+ 25 for each memember on the team that can't speak the language
	     of the judging team

	( ex: if a judge has selected LS->2, PS->0, CS->-1 
	 	then matching that judge with a:
		LS = -4,
		PS = 0,
		CS = -2,
		else = 0
	)
*/

/* Compute the cost of adding a judge to a team */

function judges_cost_function($annealer, $bucket_id, $ids)
{
	global $config;
	global $jteam;
	global $judges, $round_divisional2;

	/* Bucket ID is the team number */
	/* ids are the judge ids currently in the bucket */

//	TRACE("Bucket id=$bucket_id, ids=");
//	TRACE_R($ids);

	$cost = 0;
	$have_chair = false;
	$have_div2 = false;
	$years_experience = 0;

	if($bucket_id == 0) {
		/* This is the placeholder for all judges, there's a slight 
		 * cost for not using a judge  */
		$cost = count($ids) * 5;
//		TRACE("Extra judge team cost=$cost\n");
		return $cost;
	}
	
	
	$t =& $jteam[$bucket_id];
	
	/* Compute the over max / under min costs */
	$c = count($ids);
	$min = ($c < $t['min_judges']) ? $t['min_judges'] - $c : 0;
	$max = ($c > $t['max_judges']) ? $c - $t['max_judges'] : 0;
	$cost += $min * 50;
	$cost += $max * 10;

	//add an additional large cost above the minimum requirement cost if the team is completely empty
	if($c==0)
		$cost+=50;

//	TRACE("Under min=$min, over max=$max\n");

	/* For each judge on the team, score their preferences */
	for($x=0; $x<count($ids); $x++) {
		$j =& $judges[$ids[$x]];
		/* Get the division, and see where it fits with this
		 * judges preferences */
		$cpref = 0;
		for($y=0; $y < count($t['cats']); $y++) {
			$l = $t['cats'][$y];
			/* Lookup the judge cat pref for this category */
			$pref = -$j['catprefs'][$l] + 2;
			/* $pref = 0 (best match) --- 4 (worst match) */
			$cpref += $pref;
		}
		$dpref = 0;
		for($y=0; $y < count($t['divs']); $y++) {
			$l = $t['divs'][$y];
			/* Lookup the judge cat pref for this category */
			$pref = -$j['divprefs'][$l] + 2;
			/* $pref = 0 (best match) --- 4 (worst match) */
			$dpref += $pref;
		}
		 
//		TRACE("Judge {$ids[$x]}({$j['name']}) cp=$cpref, dp=$dpref\n");

		$cost += 2 * $cpref;
		//division matching is more important than category matching
		$cost += 3 * $dpref;

		/* See if the judge is willing to chair a team */
		if($j['willing_chair'] == 'yes') $have_chair = true;

		/* For each lang the team needs that the judge doesn't have, 
		 * increase the cost */
		for($y=0; $y < count($t['langs']); $y++) {
			$l = $t['langs'][$y];
			if(!in_array($l, $j['languages'])) $cost += 45;
		}

		/* For each additional language that the judge knows that they dont need
		 * increase the cost, this should hopefully stop the condition where
		 * it uses up all the bilingual judges for english only teams
		 * leaving no french/bilingual judges for the french teams */
		 $tlangs_count=count($t['langs']);
		 $jlangs_count=count($j['languages']);
		 if($jlangs_count>$tlangs_count)
			 $cost+=($jlangs_count-$tlangs_count)*15;

		/* If divisional round2 is enabled, make sure there is a judge
		 * on the team for round2 */
		if($j['available_for_divisional2'] == true) $have_div2 = true;

		/* Add up the years experience */
		$years_experience += $j['years_school'] + $j['years_regional'] + $j['years_national'];
		$years_experience_weighted += $j['years_school'] + $j['years_regional']*2 + $j['years_national']*4;
	
	}
	/* Huge penalty for a team without a willing chair, but only if the min judges per team >1 */
	if(!$have_chair && $config['min_judges_per_team']>1) $cost += 40;

	/* Huge penalty for not having a round2 person on the team */
	if($round_divisional2 != NULL) {
		if($have_div2 == false) $cost += 40;
	}

	/* Small penalty for a jteam with very little experience, 
	 * but only if there's more than 1 person on the team */
	if($years_experience_weighted<5 && count($ids)>1) {
		$cost += (5-$years_experience_weighted)*2;
	}
 
//	TRACE("Team $bucket_id, cost is $cost\n");

	return $cost;
}


$current_jdiv = array();


function jdiv_compute_cost($annealer, $bucket_id, $ids)
{
	/* IDS is a list of project ids for a judging team */
	global $current_jdiv;
	
	$cost = 0;
	$t_div = array();
	$t_cat = array();
	$t_lang = array();

	/* Foreach project this jteam is judging, record the 
	 * div/cat/lang */
	for($x=0; $x<count($ids); $x++) {
		$proj =& $current_jdiv['projects'][$ids[$x]];
		if(!in_array($proj['div'],$t_div)) $t_div[] = $proj['div'];
		if(!in_array($proj['cat'],$t_cat)) $t_cat[] = $proj['cat'];
		if(!in_array($proj['lang'],$t_lang)) $t_lang[] = $proj['lang'];
	}
	/* Square the project count for highter penalties for more projects */
	$cost += floor (abs(count($ids) - $annealer->items_per_bucket)) * 100;
	/* Score 100 pts for multiple languages */
	$cost += (count($t_lang) - 1) * 75;
	/* Score 25pts for multiple divs/cats */
	$cost += (count($t_div) - 1) * 25;
	$cost += (count($t_cat) - 1) * 25;

	/* Score +200 pts for each duplicate project this team is judging, we
	 * really don't want a jteam judging the same project twice */
	for($x=0; $x<count($ids) - 1; $x++) {
		for($y=$x+1; $y<count($ids); $y++) {
			if($ids[$x] == $ids[$y]) $cost += 200;
		}
	}
	return $cost;
}

/* Returns true if a judge time preference indicates they are available for the 
 * specified round.  Always returns true if judge time availablility selection 
 * is off */
function judge_available_for_round($j, $r)
{
	global $config;
	if($config['judges_availability_enable'] == 'no') return true;

	foreach($j['availability'] as $a) {
		if($a['start'] <= $r['starttime'] 
		  && $a['end'] >= $r['endtime'] 
		  && $a['date'] == $r['date'] ) {
			return true;
		}
	}
	return false;
}

function judge_mark_for_round($j, $r)
{
	/* The judge has been assigned to round $r, modify their available to 
	 * exclude any time that falls within this time 
	 * TODO: modify the DB to store date/times in timestamps, so we don't
	 * have to deal with dates separately. */
	global $config;
	global $judges;
	if($config['judges_availability_enable'] == 'no') return true;

	/* Grab a pointer to the real judge, because we want to 
	 * modify it, not a copy of it */
	$ju =& $judges[$j['id']];

	foreach($ju['availability'] as $key=>&$a) {
		if($r['starttime'] >= $a['start'] && $r['starttime'] <= $a['end']) {
			/* Round starts in the middle of this availablity slot
			 * modify this availabilty so it doesn't overlap */
			/* This may cause $a['start'] == $a['end'], that's ok */
			$a['end'] = $r['starttime'];
//			TRACE("adjust starttime\n");
		}

		if($r['endtime'] >= $a['start'] && $r['endtime'] <= $a['end']) {
			/* Round ends in the middle of this availablity slot
			 * modify this availabilty so it doesn't overlap */
			/* This may cause $a['start'] == $a['end'], that's ok */
			$a['start'] = $r['endtime'];
//			TRACE("adjust endtime\n");
		}

		if($a['start'] >= $a['end']) {
			/* Delete the whole round */
			unset($ju['availability'][$key]);
		}
	}

//	print_r($ju['availability']);

}

/* UNUSED: should be moved to the timeslot manager to ensure rounds
 * don't overlap. */
function rounds_overlap($r1, $r2) {
	$s1 = strtotime("{$r1['date']} {$r1['starttime']}");
	$e1 = strtotime("{$r1['date']} {$r1['endtime']}");
	$s2 = strtotime("{$r1['date']} {$r2['starttime']}");
	$e2 = strtotime("{$r1['date']} {$r2['endtime']}");

	if($s1 <= $s2 && $e1 > $s1) return true;
	if($s1 > $s2 && $s1 < $e2) return true;
	return false;
}

/* Print a judge */
function pr_judge(&$jt, $jid)
{
	global $judges;
	$j =& $judges[$jid];
	print("   - {$j['name']} (".join(' ', $j['languages']).')');
	print("(");
	foreach($jt['cats'] as $c)
		print("c{$c}={$j['cat_prefs'][$c]} ");
	foreach($jt['divs'] as $d)
		print("d{$d}={$j['div_prefs'][$d]} ");

	print(")");
	if($j['willing_chair'] == 'yes') print(" (chair) ");

	print("\n");
}	


set_status("Loading Data From Database...");
TRACE("\n\n");
$div = array();
TRACE("Loading Project Divisions...\n");
$q=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
while($r=mysql_fetch_object($q))
{
	$divshort[$r->id]=$r->division_shortform;
	$div[$r->id]=$r->division;
	TRACE("   {$r->id} - {$div[$r->id]}\n");
}

TRACE("Loading Project Age Categories...\n");
$cat = array();
$q=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
while($r=mysql_fetch_object($q)) {
	$cat[$r->id]=$r->category;
	TRACE("   {$r->id} - {$r->category}\n");
}

TRACE("Loading Languages...\n");
$langr = array();
$q=mysql_query("SELECT * FROM languages WHERE active='Y'");
while($r=mysql_fetch_object($q)) {
	$langr[$r->lang] = $r->langname;
	TRACE("   {$r->lang} - {$r->langname}\n");
}

TRACE("Loading Judging Round time data...\n");
$round_special_awards = array();
$round = array();
$q = mysql_query("SELECT * FROM judges_timeslots WHERE round_id='0' AND `year`='{$config['FAIRYEAR']}'");
/* Loads judges_timeslots.id, .starttime, .endtime, .date, .name */
while($r = mysql_fetch_assoc($q)) {
	TRACE("   id:{$r['id']} type:{$r['type']} name:{$r['name']}\n");

	$qq = mysql_query("SELECT * FROM judges_timeslots WHERE round_id='{$r['id']}'");
	if(mysql_num_rows($qq) == 0) {
		echo "ERROR: Round type:{$r['type']} name:{$r['name']} has no judging timeslots!  Abort.\n";
		exit;
	}
	while($rr = mysql_fetch_assoc($qq)) {
		TRACE("      Timeslot: {$rr['starttime']}-{$rr['endtime']}\n");
		$r['timeslots'][] = $rr;
	}
	$round[] = $r;

	if($r['type'] == 'divisional1') $round_divisional1 = $r;
	if($r['type'] == 'divisional2') $round_divisional2 = $r;
	if($r['type'] == 'special') $round_special_awards[] = $r;
}

if($round_divisional1 == NULL) {
	echo "No divisional1 round defined! Aborting!\n";
	exit;
}

$jdiv = array();
TRACE("Loading Judging Division Configuration and Projects...\n");
$q=mysql_query("SELECT * FROM judges_jdiv");
while($r=mysql_fetch_object($q)) {
	/* Ignore jdiv 0 (all unassigned div/cats) */
	if($r->jdiv_id == 0) continue;

	$jdiv[$r->jdiv_id]['config'][] = array('div' => $r->projectdivisions_id,
					'cat' => $r->projectcategories_id,
					'lang' => $r->lang);
}

$keys = array_keys($jdiv);
foreach($keys as $jdiv_id) {
	TRACE("    $jdiv_id\t- ");
	$jdiv[$jdiv_id]['projects'] = array();
	for($x=0;$x<count($jdiv[$jdiv_id]['config']); $x++) {
		$d = $jdiv[$jdiv_id]['config'][$x];
		if($x > 0) TRACE("\t- ");
		TRACE($cat[$d['cat']]." ".$div[$d['div']]." - ".$langr[$d['lang']]);
		$qp = mysql_query("SELECT projects.* FROM projects, registrations WHERE ".
					" projects.year='".$config['FAIRYEAR']."' AND ".
					" projectdivisions_id='{$d['div']}' AND ".
					" projectcategories_id='{$d['cat']}' AND ".
					" language='{$d['lang']}' AND " .
					" registrations.id = projects.registrations_id " .
					getJudgingEligibilityCode()
				);
		$count = 0;
		while($rp = mysql_fetch_object($qp)) {
			$jdiv[$jdiv_id]['projects'][$rp->id] = array( 
					'div' => $d['div'],
					'cat' => $d['cat'],
					'lang' => $d['lang']);
			$jdiv[$jdiv_id]['award_ids'] = array();
			$count++;
		}
		TRACE(" ($count projects)\n");
	}
	if(count($jdiv[$jdiv_id]['projects']) == 0) {
		TRACE("\t- This div has no projects, removing.\n");
		unset($jdiv[$jdiv_id]);
	}
}


/* Clean out the judging teams that were autocreated in a previous run */
TRACE("Deleting autocreated divisional and special award judging teams:");
$q = mysql_query("SELECT * FROM judges_teams WHERE autocreate_type_id=1 AND year={$config['FAIRYEAR']}");
while($r = mysql_fetch_object($q)) {
	$id = $r->id;
	print(" $id");
	/* Clean out the judges_teams_link */
	mysql_query("DELETE FROM judges_teams_link WHERE judges_teams_id='$id' AND year={$config['FAIRYEAR']}");
	print mysql_error();
	/* Awards */
	mysql_query("DELETE FROM judges_teams_awards_link WHERE judges_teams_id='$id' AND year={$config['FAIRYEAR']}");
	print mysql_error();
	/* Timeslots */
	mysql_query("DELETE FROM judges_teams_timeslots_link WHERE judges_teams_id='$id' AND year={$config['FAIRYEAR']}");
	print mysql_error();
	/* Timeslots projects */
	mysql_query("DELETE FROM judges_teams_timeslots_projects_link WHERE judges_teams_id='$id' AND year={$config['FAIRYEAR']}");
	print mysql_error();
}
echo "\n";

/* Finally, delete all the autocreated judges teams */
mysql_query("DELETE FROM judges_teams WHERE autocreate_type_id=1 AND year={$config['FAIRYEAR']}");
print mysql_error();

/* Also delete any judges_teams_link that link to teams that dont exist, just 
 * in case */
$q=mysql_query("SELECT judges_teams_link.id, judges_teams.id AS judges_teams_id 
			FROM judges_teams_link 
			LEFT JOIN judges_teams ON judges_teams_link.judges_teams_id=judges_teams.id 
			WHERE judges_teams_link.year={$config['FAIRYEAR']}");
$n=0;
while($r=mysql_fetch_object($q)) {
	if(!$r->judges_teams_id) {
		mysql_query("DELETE FROM judges_teams_link WHERE id='$r->id'");
		$n++;
	}
}
print("Deleted $n orphaned team linkings\n");
TRACE(" Done.\n");


set_status("Loading Judges");

$judges = judges_load_all();


foreach($judges as &$j) {
	if($j['judge_active'] == 'no') {
		TRACE("   {$j['name']} has their judge profile deactivated, skipping.\n");
		unset($judges[$j['id']]);
		continue;
	}
	if($j['judge_complete'] == 'no') {
		TRACE("   {$j['name']} hasn't completed their judge profile, skipping.\n");
		unset($judges[$j['id']]);
		continue;
	}

	$q = mysql_query("SELECT users_id FROM judges_teams_link WHERE 
				users_id='{$j['id']}'
				AND year='{$config['FAIRYEAR']}'");
	if(mysql_num_rows($q) != 0) {
		TRACE("   {$j['name']} is already on a judging team, skipping.\n");
		unset($judges[$j['id']]);
		continue;
	}
	if($config['judges_availability_enable']=="yes") {
		/* Load the judge time availability */
		$q = mysql_query("SELECT * FROM judges_availability WHERE users_id='{$j['id']}' ORDER BY `start`");
		if(mysql_num_rows($q) == 0) {
			TRACE("   {$j['name']} hasn't selected any time availability, POTENTIAL BUG (they shouldn't be marked as complete).\n");
			TRACE("      Ignoring this judge.\n");
			unset($judges[$j['id']]);
			continue;
		}
		while($r = mysql_fetch_assoc($q)) {
			$j['availability'][] = $r;
		}
	}

	/* Load special award preferences */
	$q = mysql_query("SELECT award_awards.id,award_awards.name FROM 
				judges_specialaward_sel,award_awards 
				WHERE
				award_awards.id=judges_specialaward_sel.award_awards_id
				AND judges_specialaward_sel.users_id='{$j['id']}'
				AND award_awards.year='{$config['FAIRYEAR']}'");
	echo mysql_error();

	if($j['special_award_only'] == 'yes') {
		TRACE("   {$j['name']} is a special awards only.\n");
		/* Find their special award id */
		if(mysql_num_rows($q) == 0) {
			TRACE("      NO special award selected! (removing special award only request)\n");
			$j['special_award_only'] = 'no';
//		} else if(mysql_num_rows($q) > 1) {
//			TRACE("      More than ONE special award selected (removing special award only request):\n");
//			$j['special_award_only'] = 'no';
		} 
	}

	$j['special_awards'] = array();
	while($r = mysql_fetch_object($q)) {
		if($j['special_award_only'] == 'yes') {
			TRACE("      {$r->name}\n");
		}
		/* Add them to the SA judge list (modify the actual list, not
		 * $j, which is a copy */
		$j['special_awards'][] = $r->id;
	}

	/* optimization, so the div1 cost function can try to find one
	 * round2 judge per team */
	$j['available_for_divisional2'] = judge_available_for_round($j, $round_divisional2);
}
unset($j);

TRACE("Loaded ".count($judges)." judges\n");
$jteam[0]['max_judges'] = count($judges);

if(count($judges)==0) {
	echo "No judges available. Aborting!\n";
	set_status("Error - no judges available...");
	set_percent(0);
	exit;
}

/* Load the numbers for any user-defined judge teams that already exist,
 * these numbers will be off-limits for auto-assigning numbers */
$q = mysql_query("SELECT * FROM judges_teams WHERE year={$config['FAIRYEAR']}");
$used_judges_teams_numbers = array();
while($i = mysql_fetch_assoc($q)) {
	$used_judges_teams_numbers[] = $i['num'];
}
echo "The following judge team numbers are already used: \n";
print_r($used_judges_teams_numbers);

$next_judges_teams_number_try = 1;
/* A function to get the next available number */
function next_judges_teams_number()
{
	global $used_judges_teams_numbers;
	global $next_judges_teams_number_try;

	while(1) {
		if(!in_array($next_judges_teams_number_try, $used_judges_teams_numbers)) break;

		$next_judges_teams_number_try++;
	}
	$r = $next_judges_teams_number_try;
	$next_judges_teams_number_try++;
	return $r;
}

function judge_team_create($num, $name)
{
	global $config;
	$name = mysql_escape_string($name);
	mysql_query("INSERT INTO judges_teams (num,name,autocreate_type_id,year)
		VALUES ('$num','$name','1','{$config['FAIRYEAR']}')");
	$id = mysql_insert_id();
	return $id;
}

function judge_team_add_judge($team_id, $users_id)
{
	global $config, $judges;
	mysql_query("INSERT INTO judges_teams_link
			 (users_id,judges_teams_id,captain,year) 
		 VALUES ('$users_id','$team_id','{$judges[$users_id]['willing_chair']}',
				'{$config['FAIRYEAR']}')");
	echo mysql_error();
}

/****************************************************************************
 * Round 1 Divisional Scheduling
 * 	- Compute required divisional judge teams
 * 	- Delete existing ones
 * 	- Anneal Projects to Teams
 * 	- Anneal Judtes to Projects
 *
 ***************************************************************************/

set_status("Computing required judging teams");
TRACE("   Each judging team may judge {$config['max_projects_per_team']} projects\n");
TRACE("   Each project must be judged {$config['times_judged']} times\n");

$keys = array_keys($jdiv);
foreach($keys as $jdiv_id) {
	$c = count($jdiv[$jdiv_id]['projects']);
	$t=ceil($c/$config['max_projects_per_team']*$config['times_judged']);
	if($t < $config['times_judged']) $t = $config['times_judged'];
	TRACE("   $jdiv_id has $c projects, requires $t judging teams\n");
	$jdiv[$jdiv_id]['num_jteams'] = $t;
}

$jteam = array();
$jteam_id = 0;
/* Create one more jteam, for anyone the annealer doesn't want to place */
$jteam[$jteam_id]['id'] = $jteam_id;
$jteam[$jteam_id]['projects'] = array();
$jteam[$jteam_id]['divs'] = array();
$jteam[$jteam_id]['cats'] = array();
$jteam[$jteam_id]['langs'] = array();
$jteam[$jteam_id]['min_judges'] = 0;
$jteam[$jteam_id]['max_judges'] = 0;
$jteam_id++;

set_status("Assigning projects to judging teams");
$keys = array_keys($jdiv);
for($k=0; $k<count($keys); $k++) {
	$jdiv_id = $keys[$k];
	TRACE("Judging Division $jdiv_id ({$jdiv[$jdiv_id]['num_jteams']} teams): \n");
	$project_ids = array();
	for($x=0; $x<$config['times_judged']; $x++) {
		$project_ids = array_merge($project_ids, array_keys($jdiv[$jdiv_id]['projects']) );
	}
	$current_jdiv = $jdiv[$jdiv_id];

	$e = 100 + 10 * ($config['effort'] / 1000);
	$a = new annealer($jdiv[$jdiv_id]['num_jteams'], 125, $e, 0.9, 
			jdiv_compute_cost, $project_ids);
	$a->anneal();

	$jdiv[$jdiv_id]['jteams'] = array();
	for($x=0;$x<$a->num_buckets; $x++) {
		$bkt = $a->bucket[$x];
		TRACE("   SubTeam $x: (jteam $jteam_id)\n");
		$jdiv[$jdiv_id]['jteams'][] = $jteam_id;
		
		$jteam[$jteam_id]['id'] = $jteam_id;
		$jteam[$jteam_id]['num'] = next_judges_teams_number();
		$jteam[$jteam_id]['projects'] = $a->bucket[$x];
		$jteam[$jteam_id]['sub'] = $x;
		$jteam[$jteam_id]['jdiv_id'] = $jdiv_id;
		$jteam[$jteam_id]['divs'] = array();
		$jteam[$jteam_id]['cats'] = array();
		$jteam[$jteam_id]['langs'] = array();
		$jteam[$jteam_id]['min_judges'] = $config['min_judges_per_team'];
		$jteam[$jteam_id]['max_judges'] = $config['max_judges_per_team'];
		
		foreach($bkt as $projid) {
			$p = $jdiv[$jdiv_id]['projects'][$projid];
			TRACE("      $projid - ".$cat[$p['cat']]." ".$div[$p['div']]." - ".$langr[$p['lang']]."\n");
			if(!in_array($p['cat'], $jteam[$jteam_id]['cats'])) {
				$jteam[$jteam_id]['cats'][] = $p['cat'];
			}
			if(!in_array($p['div'], $jteam[$jteam_id]['divs'])) {
				$jteam[$jteam_id]['divs'][] = $p['div'];
			}
			if(!in_array($p['lang'], $jteam[$jteam_id]['langs'])) {
				$jteam[$jteam_id]['langs'][] = $p['lang'];
			}
		}
		$jteam_id++;
	}
}

TRACE("There are ".(count($jteam) - 1)." judging teams\n");


TRACE("Finding judges available for round1 divisional\n");
$div1_judge_ids = array();
foreach($judges as $j) {
	if(judge_available_for_round($j, $round_divisional1) == false) continue;
	if($j['special_award_only'] == 'yes') continue;

	/* If we get here, the judge is ok for div1 */
	$div1_judge_ids[] = $j['id'];
}

TRACE(count($div1_judge_ids)." judges available for round1 divisional\n");

function judges_to_teams_update($progress, $total)
{
	set_percent(($progress * 50) / $total);
}
set_status("Assigning Judges to Teams");

$e = $config['effort'];
$a = new annealer(count($jteam), 25, $e, 0.98, judges_cost_function, $div1_judge_ids);
$a->set_update_callback(judges_to_teams_update);
$a->anneal();



for($x=1;$x<count($jteam); $x++) {
	$t =& $jteam[$x];
	print("Judging Team {$t['num']}: cost={$a->bucket_cost[$x]} ");
	$lang_array = $t['langs'];
	asort($lang_array);
	$langstr = implode(' ', $lang_array);

	//sort the cats and divs too, so we dont end up with "int/sen" <--> "sen/int"
	asort($t['cats']);
	asort($t['divs']);

	print("langs=($langstr)");
	print("cats=(");
	$catstr="";

	if(count($t['cats'])) {
        	$first=true;
	        foreach($t['cats'] AS $cid) {
        	    print("c".$cid." ");
	            if(!$first) $catstr .= "+";
        	    $catstr .= $cat[$cid];
	            $first=false;
        	}
	}
	print(")divs=(");
	$divstr="";
	if(count($t['divs'])) {
		$first=true;
		foreach($t['divs'] AS $did) {
			print("d".$did." ");
			if(!$first) $divstr.="/";
			$divstr .= $div[$did];
			$first=false;
		}
	}
	print(")\n");

	/* Add this judging team to the database */
	$tn = "$catstr $divstr ($langstr) ".($t['sub']+1);

	$team_id = judge_team_create($t['num'], $tn);

	$t['team_id'] = $team_id;

	$ids = $a->bucket[$x];
	for($y=0; $y<count($ids); $y++) {
		pr_judge($t, $ids[$y]);
		$j =& $judges[$ids[$y]];

		/* Mark this judge as used in round1 divisional */
		judge_mark_for_round($j, $round_divisional1);

		/* Add the judge to our internal team list */
		$t['judge_ids'][] = $j['id'];

		/* Write the SQL to do it */
		judge_team_add_judge($team_id, $j['id']);
	}

	/* Get the original jdiv that this team was created from.  The exact 
	 * breakdown of each and every div/cat/lang that this team is judging
	 * is in the jdiv['config'] array */
	$jd = $jdiv[$t['jdiv_id']];
	for($y=0; $y<count($jd['config']); $y++) {
		$cfg = $jd['config'][$y];
		$q=mysql_query("SELECT award_awards.id FROM 
						award_awards,
						award_awards_projectcategories,
						award_awards_projectdivisions
					WHERE 
						award_awards.year='{$config['FAIRYEAR']}'
						AND award_awards.id=award_awards_projectcategories.award_awards_id
						AND award_awards.id=award_awards_projectdivisions.award_awards_id
						AND award_awards_projectcategories.projectcategories_id='{$cfg['cat']}'
						AND award_awards_projectdivisions.projectdivisions_id='{$cfg['div']}'
						AND award_awards.award_types_id='1'
					");
		if(mysql_num_rows($q)!=1) {
			echo error(i18n("Cannot find award for %1 - %2",array($cat[$cfg['cat']],$div[$cfg['div']])));
		} else {
			$r=mysql_fetch_object($q);
			mysql_query("INSERT INTO judges_teams_awards_link (award_awards_id,judges_teams_id,year) VALUES ('$r->id','$team_id','{$config['FAIRYEAR']}')");
			/* Add the award ID to the jdiv, if it's not already there */
			if(!in_array($r->id, $jdiv[$t['jdiv_id']]['award_ids'])) {
				$jdiv[$t['jdiv_id']]['award_ids'][] = $r->id;
			}
		}
	}

}

print("Unused Judges:\n");
$ids = $a->bucket[0];
for($y=0; $y<count($ids); $y++) {
	pr_judge($jteam[0], $ids[$y]);
}

/****************************************************************************
 * Round 2 Divisional Scheduling
 * 	- Find a judge on each team that is available for both rounds
 * 	- Mark them as used
 * 	- No annealing required
 *
 ***************************************************************************/

if($round_divisional2 == NULL) {
	echo "No Round 2 Divisional defined, skipping.\n";
} else {

	echo "Finding round2 carry-over judges:\n";

	foreach($jdiv as $jdiv_id=>$jd) {

		$num = next_judges_teams_number();
		$team_id = judge_team_create($num, 'Round 2 Divisional '.$jdiv_id);

		TRACE("Created Round2 team id $team_id\n");

		/* Find all the jteams in this jdiv */
		for($x=1;$x<count($jteam); $x++) {
			$t =& $jteam[$x];

			if($t['jdiv_id'] != $jdiv_id) continue;

			TRACE("   Round1 team #{$t['num']} ({$t['id']})\n");

			$rep_id = NULL;
			$chair_rep = false;

			/* We would like the willing_chair to be the person that sticks around
			 * for round2, but if that's not possible, prefer anyone on the jteam be 
			 * around for round2 */
			foreach($t['judge_ids'] as $judge_id) {
				$j =& $judges[$judge_id];
				if(judge_available_for_round($j, $round_divisional2)) {
					if($j['willing_chair'] == true) {
						$rep_id = $judge_id;
						$chair_rep = true;
						break;
					} else if($chair_rep == false) {
						$rep_id = $judge_id;
					}
				}
			}
			if($rep_id != NULL) {
				pr_judge($t, $rep_id);
				/* Mark this judge as used in this round */
				judge_mark_for_round($judges[$rep_id], $round_divisional2);
				/* Write it to the DB */
				judge_team_add_judge($team_id, $rep_id);
			} else {
				echo "WARNING: Team $x has no carryover judge.\n";
			}
		}

		/* Assign all the awards in this jdiv */
		foreach($jd['award_ids'] as $aid) {
			mysql_query("INSERT INTO judges_teams_awards_link (award_awards_id,judges_teams_id,year) VALUES ('$aid','$team_id','{$config['FAIRYEAR']}')");
		}
	}

}
				
/****************************************************************************
 * Special Awards 
 * 	- Find ALL special award rounds
 * 		- special case: for awards with judges who are special awards 
 * 		  only, that award should match their timeslot
 * 	- Assign special awards to rounds based on needed judges
 * 	- Assign judges to teams
 *
 ***************************************************************************/

/* ====================================================================*/
/* Two functions for the Special Award Annealer, if special award
 * scheduling is disabled, these will never get called */
$current_jteam_ids = array();
function judges_sa_cost_function($annealer, $bucket_id, $ids)
{
	global $sa_jteam;
	global $judges, $current_jteam_ids;

	/* Bucket ID is the team number */
	/* ids are the judge ids currently in the bucket */

	$cost = 0;
	if($bucket_id == 0) {
		/* This is the placeholder */
		$cost = count($ids) * 50;
		return $cost;
	}
	
	$t =& $sa_jteam[$current_jteam_ids[$bucket_id]];
	
	/* Compute the over max / under min costs */
	$c = count($ids);
	$min = ($c < $t['min_judges']) ? $t['min_judges'] - $c : 0;
	$max = ($c > $t['max_judges']) ? $c - $t['max_judges'] : 0;
	$cost += $min * 50;
	$cost += $max * 10;

	/* For each judge on the team, score their preferences */
	for($x=0; $x<count($ids); $x++) {
		$j =& $judges[$ids[$x]];
		$apref = 0;

		/* Make sure this judge isn't on the team more than
		 * once.  For S-A only judges, we duplicate the judge IDs
		 * so the judge can be scheduled on multiple teams */
		if($j['special_award_only'] == 'yes') {
			for($i=0; $i<count($ids); $i++) {
				if($i == $x) continue;
				if($ids[$i] == $ids[$x]) $cost += 1000;
			}
		}

		/* See if the sa_jteam award id (what the team is judging)
		 * is in the judges selection list */
		/* Run through all awards this team is judging */
//		TRACE("   - {$j['name']}\n");
		foreach($t['award_ids'] as $aid) {
			if(in_array($aid, $j['special_awards'])) {
///				TRACE("       - award match\n");
				/* This judge wants to judge this award */
				/* No cost */
			} else {
				if($j['special_award_only'] == 'yes') {
//					TRACE("       - sa only mismatch\n");
					/* This judge is for an award, but
					 * NOT assigned to the proper one,
					 * HUGE cost */
					$cost += 500;
				}
				$apref++;
			}
		}
		$cost += 5 * $apref;
	}



//	TRACE("Team $bucket_id, cost is $cost\n");

	return $cost;
}


if($config['scheduler_enable_sa_scheduling'] == 'yes') {

	TRACE("Finding judges for special award round(s)\n");
	foreach($round_special_awards as &$r) {
		$r['available_judge_ids'] = array();
	}

	$total_judges = 0;
	foreach($judges as &$j) {
		TRACE("   {$j['firstname']} {$j['lastname']}\n"); 
		foreach($round_special_awards as &$r) {
			if(judge_available_for_round($j, $r) == true) {
				TRACE("      {$r['name']} yes\n"); 
				$r['available_judge_ids'][] = $j['id'];
				$total_judges++;
			} else {
				TRACE("      {$r['name']} no\n"); 
			}
		}
	}
	unset($j);
	unset($r);

	set_status("Creating Special Award Judging Teams (one team per award)");

	/* Load special awards */
	$q = "SELECT award_awards.name,award_awards.id FROM award_awards,award_types
		WHERE 
			award_awards.year='{$config['FAIRYEAR']}'
			AND award_types.id=award_awards.award_types_id
			AND award_awards.schedule_judges='yes'
			AND award_types.year='{$config['FAIRYEAR']}'
			AND award_types.type='Special' 
		";
	$r = mysql_query($q);
	print(mysql_error());
	/* sa_jteam for leftover judges, if any */
	$sa_jteam = array();
	$sa_jteam[0]['id'] = 0;
	$sa_jteam[0]['projects'] = array();
	$sa_jteam[0]['langs'] = array();
	$sa_jteam[0]['min_judges'] = 0;
	$sa_jteam[0]['max_judges'] = 0;
	$sa_jteam[0]['award_ids'] = array();

	$x=1;
	$required_judges = 0;
	while($i = mysql_fetch_object($r)) {
		$projects = getProjectsNominatedForSpecialAward($i->id);

		/* Construct an internal team for annealing, and create
		 * a DB team too */
		$sa_jteam[$x]['num'] = next_judges_teams_number();
		$sa_jteam[$x]['id'] = judge_team_create($sa_jteam[$x]['num'], $i->name);
		/* Note, we use $x instead of the ID, because the DB id could be zero. */
		$sa_jteam[$x]['projects'] = $projects;
		$sa_jteam[$x]['round'] = NULL;
		$sa_jteam[$x]['sub'] = 0;
		$sa_jteam[$x]['langs'] = array();
		$min = floor(count($projects) / $config['projects_per_special_award_judge']) + 1;
		$sa_jteam[$x]['min_judges'] = $min;
		$sa_jteam[$x]['max_judges'] = $min;
		$sa_jteam[$x]['award_ids'] = array($i->id);
		$sa_jteam[$x]['name'] = $i->name;

		$required_judges += $min;
		
		/* Link the award to this team */
		mysql_query("INSERT INTO judges_teams_awards_link (award_awards_id,judges_teams_id,year) 
				VALUES ('{$i->id}','{$sa_jteam[$x]['id']}','{$config['FAIRYEAR']}')");

		TRACE("Created Team: {$i->name}, ".count($projects)." projects => $min judges needed (db id:{$sa_jteam[$x]['id']}) \n");
		$x++;
	}
	TRACE("Total Judges: $total_judges, Required: $required_judges\n");

	/* ====================================================================*/
	set_status("Assigning Special Award Teams to Special Award Round(s)\n");

	/* Compute how many judges each round needs based on the total number
	 * of needed judges, e.g. if SAround1 has 10 judges available and SAround2
	 * has 20 judges available, and we total need 90 judges, then we
	 * want to assign jteams so that SAround1 has 30 slots, and SAround2 has
	 * 60 to balance the deficit */
	foreach($round_special_awards as &$r) {
		$x = count($r['available_judge_ids']);
		$target = ($x * $required_judges) / $total_judges;
		$r['target_judges'] = $target;
		TRACE("Round {$r['name']} should be assigned $target judge timeslots\n");

		/* Setup for the next step, always add special award
		 * judge team 0 to ALL rounds */
		$r['jteam_ids'] = array(0);
		$r['assigned_judges'] = 0;
	}
	unset($r);

	/* ====================================================================*/
	/* Scan the list of special awards, check each special award to see if
	 * it has special award only judges, we want those special awards pre-assigned
	 * to rounds where ALL SA-only judges are available, or, as best we can. */
	foreach($sa_jteam as $x=>&$jt) {
		if($x == 0) continue;

		$sa_judges = array();
		foreach($round_special_awards as $i=>$r) {
			$sa_round_count[$i] = 0;
		}

		foreach($jt['award_ids'] as $aid) {
			foreach($judges as $jid=>$j) {
				if($j['special_award_only'] == 'no') continue;
				if(in_array($aid, $j['special_awards'])) {
					$sa_judges[] = $jid;
					foreach($round_special_awards as $i=>$r) {
//						TRACE("Checking {$j['name']} in round {$r['name']}\n");
						if(judge_available_for_round($j, $r)) {
//							TRACE("   yes, round $i ++\n");
							$sa_round_count[$i]++;

						}
					}
				}
			}
		}

		/* If there are no SA-only judges, skip the pre-assignment */
		if(count($sa_judges) == 0) continue;

		/* There are count($sa_judges), find the round
		 * with the highest count */
		$highest_count = 0;
		$highest_offset = -1;
		foreach($round_special_awards as $i=>$r) {
			if($sa_round_count[$i] > $highest_count || $highest_offset == -1) {
				$highest_count = $sa_round_count[$i];
				$highest_offset = $i;
			}
		}
		/* Assign this jteam to that round */
		$round_special_awards[$highest_offset]['jteam_ids'][] = $x;
		$round_special_awards[$highest_offset]['assigned_judges'] += $jt['min_judges'];
		TRACE("Pre-assigning Team {$jt['name']} to Round {$round_special_awards[$highest_offset]['name']}\n");
		$jt['assigned'] = true;

		/* If the max judges for the jteam is less than the max, update the max, 
		 * this prevents the scheduler from trying to remove sa-only judges 
		 * from the jteam because of the over-max cost penalty */
		if($jt['max_judges'] < count($sa_judges)) {
			TRACE("   Changing max_judges to ". count($sa_judges)." to accomodate all SA-only judge requests.\n");
			$jt['max_judges'] = count($sa_judges);
		}
	}
	unset($jt);

	/* Use a greedy algorithm to assign the remaining jteams.  First sort 
	 * the teams by the number of judges needed so those can be assigned 
	 * first */
	function sa_cmp($a, $b) {
		return $b['min_judges'] - $a['min_judges'];
	}
	uasort($sa_jteam, 'sa_cmp');

	foreach($sa_jteam as $x=>$jt) {
		if($x == 0) continue;
		if($jt['assigned'] == true) continue;

		$highest = 0;
		$highest_offset = -1;
		/* Find the round with the highest missing judges, this works
		 * even if the $p computation is negative */
		foreach($round_special_awards as $o=>$r) {
			$p = $r['target_judges'] - $r['assigned_judges'];
//			TRACE("   Round {$r['name']} p=$p\n");
			if($highest_offset == -1 || $p > $highest) {
				$highest = $p;
				$highest_offset = $o;
			}
		}
		/* Assign this jteam id to the special award round */
		$round_special_awards[$highest_offset]['jteam_ids'][] = $x;
		$round_special_awards[$highest_offset]['assigned_judges'] += $jt['min_judges'];
		TRACE("Assigned Team {$jt['name']} to Round {$round_special_awards[$highest_offset]['name']}\n");
	}
	unset($jt);


	/* Now that teams have been assigned to rounds, search for all the
	 * SA only judges again, and duplicate the available judge id if they are signed
	 * up to judge more than one award in the round */
	foreach($judges as &$j) {
		if($j['special_award_only'] == 'no') continue;

		foreach($round_special_awards as &$r) {
			$count = 0;
			if(judge_available_for_round($j, $r) == false) continue;

			/* Find out how many of their special awards are in this round. */
			foreach($sa_jteam as $jt_id=>&$jt) {
				/* Is the team in this round? */
				if(!in_array($jt_id, $r['jteam_ids'])) continue;

				/* Is this SA judge requsing an award judged by this team? */
				foreach($jt['award_ids'] as $aid) {
					if(in_array($aid, $j['special_awards']))
						$count++;
				}
			}
			unset($jt);
			while($count > 1) {
				$r['available_judge_ids'][] = $j['id'];
				$count--;
				TRACE("   Duplicate {$j['firstname']} {$j['lastname']} for multiple SA-only request in round {$r['name']}\n");
			}
		}
		unset($r);
	}
	unset($j);
	

	/* Now, anneal in each special award round */
	foreach($round_special_awards as $r) {
		set_status("Assigning Judges in round {$r['name']}\n");

		$current_jteam_ids = $r['jteam_ids'];
		$judge_ids = $r['available_judge_ids'];

		$e = $config['effort'];
		$a = new annealer(count($r['jteam_ids']), 25, $e, 0.98, 
				judges_sa_cost_function, $judge_ids);
		//$a->set_update_callback(judges_to_teams_update);
		//$a->set_pick_move(judges_sa_pick_move);
		$a->anneal();

		$x=0;

		unset($t);
		unset($tid);

		foreach($r['jteam_ids'] as $tid) {
			if($tid == 0) {
				$x++;
				continue;
			}

			$t = &$sa_jteam[$tid];

			print("Judging Team {$t['id']} \"{$t['name']}\": cost={$a->bucket_cost[$x]} #=({$t['min_judges']},{$t['max_judges']}) ");

		//	print("langs=(");
		/*	$langstr="";
			for($y=0; $y<count($t['langs']); $y++) {
				if($y != 0) $langstr .= " ";
				$langstr .= $t['langs'][$y];
			}
			print("$langstr)");*/
			print("\n");

			/* Do timeslot and project timeslot assignment */
			mysql_query("INSERT INTO judges_teams_timeslots_link 
							(judges_teams_id,judges_timeslots_id,year)
							VALUES ('{$t['id']}', '{$r['timeslots'][0]['id']}', '{$config['FAIRYEAR']}')");
			echo mysql_error();

			foreach($t['projects'] as $proj) {
				$pid = $proj['id'];
				mysql_query("INSERT INTO judges_teams_timeslots_projects_link 
								(judges_teams_id,judges_timeslots_id,projects_id,year)
								VALUES ('{$t['id']}', '{$r['timeslots'][0]['id']}', '$pid', '{$config['FAIRYEAR']}')");
				echo mysql_error();
			}
			$ids = $a->bucket[$x];
			foreach($a->bucket[$x] as $jid) {
		//		pr_judge($t, $ids[$y]);

				$j = &$judges[$jid];
				print("   - {$j['name']}\n");
				
				/* Link Judges to the judging team we just inserted */
				judge_team_add_judge($t['id'], $jid);
			}
			$x++;
		}
	}
}


/* Resume normal flow now */
/****************************************************************************
 * Timeslot Scheduling
 *
 ***************************************************************************/

/* ====================================================================*/
set_status("Assigning Judging Teams and Projects to Timeslots");

TRACE("Loading Divisional1 Timeslot Data\n");
$available_timeslots=array();

$q=mysql_query("SELECT * FROM judges_timeslots WHERE 
			round_id='{$round_divisional1['id']}' 
			AND year='{$config['FAIRYEAR']}' 
			AND type='timeslot'
			ORDER BY date,starttime");
$x=0;
while($r=mysql_fetch_object($q)) {
        $available_timeslots[]=array("id"=>$r->id,
				"date"=>$r->date,
				"starttime"=>substr($r->starttime,0,-3),
				"endtime"=>substr($r->endtime,0,-3));
	print("   ".$available_timeslots[$x]['starttime']." -> ".
			$available_timeslots[$x]['endtime']."\n");
	$x++;
}

$n_timeslots = count($available_timeslots);


	/* First, check to see if the project is being judged 3 or
	 * more times in a row, OR, if it has large gaps that aren't
	 at the end of the judging */
/* I'm going to leave this here, for now, we shoudl do something like
 * this at some point in evaluating projects, but right now 
 * the randomness is pretty good. */
/*	for($x=0; $x<count($project_index); $x++) {
		$i_start = $x * $n_timeslots;
		$i_end = ($x+1) * $n_timeslots;

		$z_count = 0;
		$r_count = 0;
		for($y=$i_start; $y<$i_end; $y++) {
			$jteam_id = $ids[$y];

			if($jteam_id == 0) {
				$z_count++;
				$r_count=0;
			} else {*/
				/* Do the z_count cost here so we don;t
				 * count any zcount cost for the end
				 * of the timetable */
/*				if($z_count > 2) $cost += $z_count;
				$r_count++;
				$z_count=0;
				if($r_count > 2) $cost += $r_count;
			}
		}
	}
	*/

function timeslot_pick_move($a)
{
	/* Use the existing pick move, but we want the item numbers
	 * in each bucket to always be the same */
	list($b1, $i1, $b2, $i2) = $a->pick_move();
	$i2 = $i1;
	return array($b1, $i1, $b2, $i2);
}


function timeslot_cost_function($annealer, $bucket_id, $ids)
{	
	$cost = 0;

	/* Check to make sure a judge isn't judging two projects
	 * at the same time */
	$n_pids = count($ids);
	for($x=0; $x<$n_pids-1; $x++) {
		$jteam_id1 = $ids[$x];
		if($jteam_id1 == 0) continue;
		for($y=$x+1; $y<$n_pids; $y++) {
			$jteam_id2 = $ids[$y];
			if($jteam_id1 == $jteam_id2)
				$cost += 50;
		}
	}
	return $cost;
}


$keys = array_keys($jdiv);
$keys_count = count($keys);
for($k=0; $k<$keys_count; $k++) {
	$jdiv_id = $keys[$k];

	$pids = array_keys($jdiv[$jdiv_id]['projects']);
	$n_projects = count($pids);

	if($n_projects == 0) continue;

	unset($project_rlookup);
	$project_rlookup = array();

	for($x=0; $x<count($pids); $x++) {
		$project_rlookup[$pids[$x]] = $x;
	}

//	$current_jdiv = $jdiv_id;

	printf("jdiv $jdiv_id, $n_projects projects in this jdiv\n");
	unset($jteams_ids);
	$jteams_ids = array();
	/* Pad to the correct length */
	for($x=0; $x<($n_timeslots * $n_projects); $x++) 
		$jteams_ids[] = 0;

    printf("total of ".count($jteams_ids)." slots (should be $n_timeslots * $n_projects)\n");

	/* Fill out the jteam array with a jteam_id for each time the 
	 * jteam_id is supposed to judge a project */
	$jteams = $jdiv[$jdiv_id]['jteams'];

	foreach($jteams as $jteam_id) {

		for($y=0;$y<count($jteam[$jteam_id]['projects']); $y++) {
			$pid = $jteam[$jteam_id]['projects'][$y];
			$idx = $project_rlookup[$pid];

			for($o = $idx; ; $o+= $n_projects) {
				if($jteams_ids[$o] != 0) continue;

				$jteams_ids[$o] = $jteam_id;
				break;
			}
		}
	}


/*
	$y = 0;
        foreach($jteams as $jteam_id) {
		$o = 0;
	    print("setting up jteam $jteam_id\n");
        print_r($jteam[$jteam_id]);
		foreach($jteam[$jteam_id]['projects'] as $pid) {
			$jteams_ids[$y * $n_timeslots + $o] = $jteam_id;
			$o++;
		}
		$y++;
	}
	printf("jteams_ids=\n");
        print_r($jteams_ids);
*/	
	print("Jteams ids len=".count($jteams_ids));
	print("n_timeslots=$n_timeslots\n");

	set_percent(50 + ($k / $keys_count) * 50);

	$e = 500 + 50 * ($config['effort'] / 1000);
	$a = new annealer($n_timeslots, 100, $e, 0.98, timeslot_cost_function, $jteams_ids);
	$a->set_pick_move(timeslot_pick_move);
	$a->anneal();

	printf("             ");
	for($y=0;$y<$n_timeslots; $y++) {
		printf("%4d ", $y+1);
	}
	printf("\n");

	for($x=0; $x<count($pids); $x++) {
		$pid = $pids[$x];
		printf("Project %4d: ", $pid);

		for($y=0;$y<$n_timeslots; $y++) {
			$jteam_id = $a->bucket[$y][$x];
			printf("%4d ", $jteam[$jteam_id]['id']);

			if($jteam_id == 0) continue;

			/* if jteam_id isn't 0, instert it into the db */
			mysql_query("INSERT INTO judges_teams_timeslots_link ".
				" (judges_teams_id,judges_timeslots_id,year)".
				" VALUES ('{$jteam[$jteam_id]['team_id']}', ".
				" '{$available_timeslots[$y]['id']}', ".
				" '{$config['FAIRYEAR']}')");
														 
			mysql_query("INSERT INTO judges_teams_timeslots_projects_link ".
				" (judges_teams_id,judges_timeslots_id,projects_id,year) ".
				" VALUES ('{$jteam[$jteam_id]['team_id']}', ".
				" '{$available_timeslots[$y]['id']}', ".
				" '$pid', '{$config['FAIRYEAR']}')");

		}
		printf("\n");
	}

}

TRACE("All Done.\n");
echo "</pre>";

set_percent(-1);
set_status("Done");

//echo happy("Scheduler completed successfully");

//send_footer();
?>
