<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>
   Copyright (C) 2008 Daivd Grant <dave@lightbox.org>

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
 require_once("../common.inc.php");
 require_once("../user.inc.php");
 require_once("anneal.inc.php");

if($_SERVER['SERVER_ADDR']) {
	echo "This script must be run from the command line";
	exit;
}

//function TRACE() { }
//function TRACE_R() { }
function TRACE($str) { print($str); }
function TRACE_R($array) { print_r($array); }


TRACE("<pre>");


function set_status($txt)
{
	TRACE("Status: $txt\n");
	mysql_query("UPDATE config SET val='$txt' WHERE 
			var='tours_assigner_activity' AND year=0");
}

$set_percent_last_percent = -1;
function set_percent($n)
{
	global $set_percent_last_percent;
	$p = floor($n);
	if($p == $set_percent_last_percent) return;
	TRACE("Progress: $p\%\n");
	$set_percent_last_percent = $p;
	mysql_query("UPDATE config SET val='$p' WHERE 
			var='tours_assigner_percent' AND year=0");
}

set_status("Initializing...");
set_percent(0);

/* The cost function is:
	- Foreach student in a tour
		+15 - Above the grade level
		+25 - Below the grade level
		+2 - Noone from the same school
		If ranked (rank=1,2,3,4,...):
		+(rank*rank*5 - 5) = +0, +15, +40, +75
		If not ranked and max choices specified
		+(max_choices*max_choices*5) (always greater than ranked)
		else max choices not specified 
		+((max_choices-1)*(max_choices-1)*5)
	- Foreach tour
		+100 for each student above the capacity
		+200 for each student below 1/4 the capacity,but
			zero if the tour is empty

Notes:
	- If a student doesn't fill in all their choices, we don't want to give
	  them an unfair scheduling advantage.  They'll significantly increase
	  the cost if they don't get their chosen tour, whereas someone who
	  specifies all the choices will gradually increase the cost.  So, we
	  want to make it "more ok" for the annealer to place someone who
	  hasn't ranked their max number of tours in any tour, and make it
	  "less ok" for someone who has specified all the rankings to be placed
	  anywhere. 
*/

function tour_cost_function($annealer, $bucket_id, $ids)
{
	global $config;
	global $tid;
	global $tours;
	global $students;
	/* Bucket ID is the tour number */
	/* ids are the student ids currently in the bucket */

//	TRACE("Bucket id=$bucket_id, ids=");
//	TRACE_R($ids);

	$cost = 0;
	
	$t =& $tours[$bucket_id];
	$tid = $t['id'];
	
	/* Compute the over max / under min costs */
	$c = count($ids);
	$over = ($c > $t['capacity']) ? $c - $t['capacity'] : 0;
	if($c > 0) 
		$under = ($c < ($t['capacity']/4)) ? ($t['capacity']/4) - $c : 0;
	else 
		$under = 0;
	
	$cost += $over * 100;
	$cost += $under * 200;

//	TRACE("Under min=$min, over max=$max\n");
//	TRACE("($bucket_id) {$t['id']} #{$t['num']} {$t['name']}  (cap:{$t['capacity']} grade:{$t['grade_min']}-{$t['grade_max']})\n");

	$schools = array();	
	/* For each student on the tour */
	foreach($ids as $x=>$sid) {
		$s =& $students[$sid];

//		$tids = implode(' ', $s['rank']);
//		TRACE("   - {$s['name']} ($tids) (g:{$s['grade']} sid:{$sid} sch:{$s['schools_id']})\n");
		/* Score the rank */
		if(count($s['rank']) == 0) {
			/* The student hasn't made any selection, assume they
			 * are ok whereever we put them. */
			$rank_cost = 0;
//			TRACE("   -> No choices!\n");
		} else {
			$rank_cost = -1;
			foreach($s['rank'] as $rank=>$rank_tid) {
//				TRACE("   -> Searching for tid $tid at rank $rank -> $rank_tid\n");
				if($rank_tid != $tid) continue;
				$rank_cost = ($rank * $rank * 5) - 5;
//				TRACE("   -> matched tid $tid at rank $rank\n");
				break;
			}
		}

		if($rank_cost == -1) {
			/* Coulnd't find tour id in the student ranks*/
			if(count($s['rank']) < $config['tours_choices_max']) {
				/* Student didn't choose their max # of tours,
				 * give a slightly lower cost */
				$rank_cost = ($config['tours_choices_max']-1) * ($config['tours_choices_max']-1) * 5;
			} else {
				/* Student chose max tours and they're in a
				 * tour they didn't pick, big cost. */
				$rank_cost = $config['tours_choices_max'] * $config['tours_choices_max'] * 5;
			}
		}
//		TRACE("      -> rank cost $rank_cost\n");
		$cost += $rank_cost;

		/* Check for student below/above grade range */
		if($s['grade'] < $t['grade_min']) $cost += 15;
		if($s['grade'] > $t['grade_max']) $cost += 25;

		/* Record the school */
		$schools[$s['schools_id']]++;
	}

	/* Search the schools array for insteances of '1' */
	foreach($schools as $sid=>$cnt) {
		if($cnt == 1) $cost += 2;
	}

//	TRACE("Final for bucket  $bucket_id, cost is $cost\n");

	return $cost;
}

set_status("Cleaning existing tour assignments...");
TRACE("\n\n");
$q=mysql_query("DELETE FROM tours_choice 
		WHERE year='{$config['FAIRYEAR']}'
		AND rank='0'");

set_status("Loading Data From Database...");
TRACE("\n\n");
TRACE("Tours...\n");
$tours = array();
$q=mysql_query("SELECT * FROM tours WHERE year='{$config['FAIRYEAR']}'");
$x=0;
/* Index with $x here, because these need to match up with the bucket ids of
 * the annealer */
while($r=mysql_fetch_object($q)) {
	$tours[$x]['capacity'] = $r->capacity; 
	$tours[$x]['grade_min'] = $r->grade_min; 
	$tours[$x]['grade_max'] = $r->grade_max; 
	$tours[$x]['id'] = $r->id; 
	$tours[$x]['name'] = $r->name; 
	TRACE("  ($x) ${$r->id}: #{$r->num} {$r->name}  (cap:{$r->capacity} grade:{$r->grade_min}-{$r->grade_max})\n");
	$x++;
}

$students = array();
TRACE("Loading Students...\n");
$q=mysql_query("SELECT students.id,students.grade,
			students.registrations_id,
			students.schools_id,
			students.firstname, students.lastname
		FROM students
			LEFT JOIN registrations ON registrations.id=students.registrations_id
		WHERE
			students.year='{$config['FAIRYEAR']}'
			AND ( registrations.status='complete'
				OR registrations.status='paymentpending' )
		ORDER BY
			students.id
			");
$last_sid = -1;
TRACE(mysql_error());
while($r=mysql_fetch_object($q)) {
	$sid = $r->id;
	$students[$sid]['name'] = $r->firstname.' '.$r->lastname;
	$students[$sid]['grade'] = $r->grade;
	$students[$sid]['registrations_id'] = $r->registrations_id;
	$students[$sid]['rank'] = array();
	$students[$sid]['schools_id'] = $r->schools_id;
}
$student_ids = array_keys($students);
TRACE("   ".(count($student_ids))." students loaded\n");

TRACE("Loading Tour Selection Preferences...\n");
$q=mysql_query("SELECT * FROM tours_choice WHERE
			tours_choice.year='{$config['FAIRYEAR']}' 
			ORDER BY rank ");
TRACE(mysql_error());
$x=0;
while($r=mysql_fetch_object($q)) {
	$sid = $r->students_id;
	if(!array_key_exists($sid, $students)) continue;
	$students[$sid]['rank'][$r->rank] = $r->tour_id;
	$x++;
}
TRACE("   $x preferences loaded.\n");


function tours_assignment_update($progress, $total)
{
	set_percent(($progress * 50) / $total);
}

TRACE("Effort: {$config['tours_assigner_effort']}\n");
set_status("Assigning students to tours");
$e = 100 + 10 * ($config['tours_assigner_effort'] / 100);
$a = new annealer(count($tours), 50, $e, 0.98,
		tour_cost_function, $student_ids);
$a->set_update_callback(tours_assignment_update);
$a->anneal();

/* Record the assignments */
foreach($tours as $x=>$t) {
	TRACE("($x) {$t['id']} #{$t['num']} {$t['name']}  (cap:{$t['capacity']} grade:{$t['grade_min']}-{$t['grade_max']})\n");

	$sids = $a->bucket[$x];

	TRACE("   - Cost:{$a->bucket_cost[$x]}  Students: ".(count($sids))."\n");
	foreach($sids as $sid) {
		$s = $students[$sid];
		$tids = implode(' ', $s['rank']);
		TRACE("   - {$s['name']} ($tids) (g:{$s['grade']} sid:{$sid} sch:{$s['schools_id']})\n");
		mysql_query("INSERT INTO tours_choice 
				(`students_id`,`registrations_id`,
						`tour_id`,`year`,`rank`)
				VALUES (
				'$sid', '{$s['registrations_id']}', 
				'{$t['id']}', '{$config['FAIRYEAR']}',
				'0')");
	}
}

TRACE("All Done.\n");
echo "</pre>";

set_percent(-1);
set_status("Done");

//echo happy("Scheduler completed successfully");

//send_footer();
?>
