<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2010 David Grant <dave@lightbox.org>

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
require_once('judges.inc.php'); /* for getJudgingEligibilityCode() */
require_once('anneal.inc.php');

if($_SERVER['SERVER_ADDR']) {
	echo "This script must be run from the command line";
	exit;
}

$action = '';
switch($argv[1]) {
case '--images':
	$action = 'images';
	break;
case '--pn':
	$action = 'pn';
	break;
}

//function TRACE() { }
//function TRACE_R() { }
function TRACE($str) { print($str); }
function TRACE_R($array) { print_r($array); }


function point_rotate($x, $y, $deg)
{
	/* Use - orienttaiotn because rotation is always done from dest->src */
	$r = deg2rad(-$deg);
	return array(round($x*cos($r) - $y*sin($r), 6), round($x*sin($r) + $y*cos($r), 6));
}

function point_translate($x, $y, $dx, $dy)
{
	return array ($x+$dx, $y+$dy);
}

function is_point_in_object($x, $y, $o)
{
	/* Translate the point to the object origin */
	list($x, $y) = point_translate($x, $y, -$o['x'], -$o['y']);
	/* Rotate the point to the object's frame of reference*/
	list($x, $y) = point_rotate($x, $y, -$o['orientation']);
	/* Is it within the object now ? */
	if(abs($x) <= $o['w2'] && abs($y) <= $o['h2'])
		return true;
	return false;
}

function queue_new()
{
	return array('head' => NULL, 'tail' => NULL);
}


function grid_path_cmp($a, $b)
{
	/* This must return an integer! Strange-things(tm) happen if it doesn't */
	if($a['distance'] == $b['distance']) return 0;
	return ($a['distance'] < $b['distance']) ? -1 : 1;
}

function grid_path_check(&$i_eh, &$queue, &$loc, &$end, $ix, $iy)
{
	$next_loc =& $i_eh[$ix][$iy];
//	TRACE("Checking next loc($ix,$iy) ({$next_loc['x']},{$next_loc['y']})\n");

	/* Don't revisit anything */
	if($next_loc['visited'] == true) {
//		TRACE("   Already Visited.\n");
		return false;
	}
	$next_loc['visited'] = true;

	if(count($next_loc['ids']) != 0) {
//		TRACE("   Object occupying this gridloc.\n");
		/* There's something here, can't do anything */
		return false;
	}

		

	$next_loc['distance'] = distance($next_loc['x'], $next_loc['y'], $end['x'], $end['y']);
	$next_loc['path_length'] = $loc['path_length'] + 1;
//	TRACE("   distance={$next_loc['distance']}, path_length={$next_loc['path_length']}\n");

	/* Add to processing queue in order */
	array_push($queue, $next_loc);
}

function grid_path($src, $dst)
{
	global $exhibithall;
	$i_eh = &$exhibithall[$src['exhibithall_id']];
	$start = &$i_eh[$src['front_grid_ix']][$src['front_grid_iy']];
	$end = &$i_eh[$dst['front_grid_ix']][$dst['front_grid_iy']];

//	TRACE("Path ({$start['x']},{$start['y']}) -> ({$end['x']},{$end['y']})\n");

	/* Clean out temp data */
	for($x=0;$x<$i_eh['grid_w']; $x++) {
		for($y=0;$y<$i_eh['grid_h']; $y++) {
			$i_eh[$x][$y]['visited'] = false;
		}
	}

//	print_r($i_eh);

	/* Seed the exploration queue */
	$queue = array();
	$start['distance'] = distance($start['x'], $start['y'], $end['x'], $end['y']);
	$start['path_length'] = 0;
	$start['visited'] = true;
	array_push($queue, $start);

	while(1) {
		if(count($queue) == 0) break;

//		print_r($queue);
		/* Dequeue head */
		$loc = array_shift($queue);

		/* Cut it off after a long walk */
		if($loc['path_length'] > 25) break;

//		TRACE("Dequeue: ({$loc['x']},{$loc['y']})\n");

		/* Is this our destionation ? */
		if($loc['x'] == $end['x'] && $loc['y'] == $end['y']) {
//			TRACE("Found destination, path_length={$loc['path_length']}\n");
			return $loc['path_length'];
		}

		/* All 4 directions */
		if($loc['ix'] > 0) grid_path_check($i_eh, $queue,$loc, $end, $loc['ix']-1, $loc['iy']);
		if($loc['ix'] < $i_eh['grid_w']-1) grid_path_check($i_eh, $queue, $loc, $end, $loc['ix']+1, $loc['iy']);
		if($loc['iy'] > 0) grid_path_check($i_eh, $queue, $loc, $end, $loc['ix'], $loc['iy']-1);
		if($loc['iy'] < $i_eh['grid_h']-1) grid_path_check($i_eh, $queue, $loc, $end, $loc['ix'], $loc['iy']+1);
		usort($queue, 'grid_path_cmp');
	}
//	TRACE("No path found\n");
//	exit;
	return 100;

}


TRACE("<pre>\n");

/* Load exhibit halls */
$exhibithall = array();
$q = mysql_query("SELECT * FROM exhibithall WHERE type='exhibithall'");
TRACE("Loading exhibit halls...\n");
while(($r = mysql_fetch_assoc($q))) {
	$r['divs'] = unserialize($r['divs']);
	$r['cats'] = unserialize($r['cats']);
	$exhibithall[$r['id']] = $r;
	TRACE("   - {$r['name']}\n");
}

/* Load objects */
$objects = array();
$q = mysql_query("SELECT * FROM exhibithall WHERE type='wall' OR type='project'");
TRACE("Loading objects...\n");
while(($r = mysql_fetch_assoc($q))) {
	$r['divs'] = unserialize($r['divs']);
	$r['cats'] = unserialize($r['cats']);
	$objects[$r['id']] = $r;
}
TRACE(count($objects)." objects loaded.\n");

/* Compute stuff */
foreach($objects as $oid=>$o) {
	$objects[$oid]['w2'] = $o['w']/2;
	$objects[$oid]['h2'] = $o['h']/2;
}

/* The grid size is the smallest object dimension */
$grid_size = 100;
foreach($objects as $oid=>$o) {
	if($grid_size > $o['w']) $grid_size = $o['w'];
	if($grid_size > $o['h']) $grid_size = $o['h'];
}
$grid_size /= 2;

TRACE("Grid size: {$grid_size}m\n");


//print_r($exhibithall);

//print_r($objects);

$div = array();
TRACE("Loading Project Divisions...\n");
$q=mysql_query("SELECT * FROM projectdivisions WHERE year='{$config['FAIRYEAR']}' ORDER BY id");
while($r=mysql_fetch_object($q))
{
	$divshort[$r->id]=$r->division_shortform;
	$div[$r->id]=$r->division;
	TRACE("   {$r->id} - {$div[$r->id]}\n");
}

TRACE("Loading Project Age Categories...\n");
$cat = array();
$q=mysql_query("SELECT * FROM projectcategories WHERE year='{$config['FAIRYEAR']}' ORDER BY id");
while($r=mysql_fetch_object($q)) {
	$catshort[$r->id]=$r->category_shortform;
	$cat[$r->id]=$r->category;
	TRACE("   {$r->id} - {$r->category}\n");
}

TRACE("Loading Projects...\n");
$projects = array();
$q = mysql_query("SELECT projects.* FROM projects, registrations 
				WHERE
					projects.year='{$config['FAIRYEAR']}' 
					AND registrations.id = projects.registrations_id
				".getJudgingEligibilityCode());
while($p = mysql_fetch_object($q)) {
	$qq = mysql_query("SELECT grade,schools_id FROM students WHERE registrations_id='{$p->registrations_id}'");
	$num_students = mysql_num_rows($qq);
	$grade = 0;
	$schools_id = 0;
	while($s = mysql_fetch_assoc($qq)) {
		if($s['grade'] > $grade) {
			$grade = $s['grade'];
			$schools_id = $s['schools_id'];
		}
	}
	$projects[$p->id] = array( 
			'projects_id' => $p->id,
			'div' => $p->projectdivisions_id,
			'cat' => $p->projectcategories_id,
			'grade' => $grade,
			'schools_id' => $schools_id,
			'req_electricity' => $p->req_electricity,
			'projectnumber' => $p->projectnumber,
			'floornumber' => $p->floornumber,
			'num_students' => $num_students);
}
TRACE(count($projects)." projects loaded.\n");

if($action == 'pn') {
	TRACE("Generating Project Numbers from Floor Locations...\n");
	foreach($projects as $p) {
		$c = $catshort[$p['cat']];
		$d = $divshort[$p['div']];
		$n = sprintf("%03d", $p['floornumber']);
		$pn = "$c $n $d";
		TRACE("Project {$p['projects_id']} at loc {$p['floornumber']}: $pn\n");
		mysql_query("UPDATE projects SET projectnumber='$pn' WHERE id='{$p['projects_id']}'");
	}
	TRACE("Done.\n");
	exit;
}


/* Assign objects to grid locations */
foreach($exhibithall as &$i_eh) {
	TRACE("Assigning objects to grid locations for {$i_eh['name']}...\n");
	$ix = 0;
	$i_eh['grid_w'] = 0;
	$i_eh['grid_h'] = 0;
	for($x=0;$x<=$i_eh['w'];$x+=$grid_size,$ix++) {
		if($ix <= $i_eh['grid_w']) $i_eh['grid_w'] = $ix+1;
		$iy = 0;
		for($y=0;$y<=$i_eh['h'];$y+=$grid_size,$iy++) {
			if($iy <= $i_eh['grid_h']) $i_eh['grid_h'] = $iy+1;
			/* Initialize element if required */
			if(!is_array($i_eh[$ix][$iy])) {
				$i_eh[$ix][$iy] = array( 'x' => $x, 'ix' => $ix,
							'y' => $y, 'iy' => $iy,
							'ids' => array(),
							'project_front' => 0);
			}

			/* Scan all objects */
			foreach($objects as $oid=>$o) {
				if($o['exhibithall_id'] != $i_eh['id']) continue;
				if(is_point_in_object($x, $y, $o)) {
					$i_eh[$ix][$iy]['ids'][] = $oid;
				}
			}
		}
	}
	TRACE("Grid locations: {$i_eh['grid_w']}x{$i_eh['grid_h']}\n");
}
TRACE("Done.\n");

function distance($x1,$y1,$x2,$y2)
{
	return sqrt( ($x1-$x2)*($x1-$x2)+($y1-$y2)*($y1-$y2) );
}

TRACE("Computing gridlocation of front of projects...\n");
foreach($objects as $oid=>$o) {
	if($o['type'] != 'project') continue;

	/* Get a pointer to the exhibit hall */
	$i_eh = &$exhibithall[$o['exhibithall_id']];

	/* Compute the xy of the front (that's the bottom edge of the unrotate project) */
	$fx = 0;
	$fy = $o['h2'];
//	TRACE("Front orig: ($fx,$fy)\n");

	/* Rotate the point */
	list($fx, $fy) = point_rotate($fx, $fy, $o['orientation']);
//	TRACE("Front rotate by {$o['orientation']}: ($fx,$fy) \n");
	/* Translate it to the proper position of the object */
	list($fx, $fy) = point_translate($fx, $fy, $o['x'], $o['y']);

//	TRACE("Front: ($fx,$fy) $grid_size\n");
	/* Snap to grid offsets  */
	$gx = intval($fx / $grid_size); //* $grid_size;
	$gy = intval($fy / $grid_size); //* $grid_size;

//	TRACE("Search grid around $gx, $gy\n");
	/* Search around that grid for a free spot, closest to $fx,$fy,
	 * with no objects and no object_front */
	$smallest_d = $i_eh['w'];
	$smallest_ix = 0;
	$smallest_iy = 0;
	$found = false;
	for($x = $gx - 1; $x <= $gx + 1; $x++) {
		for($y = $gy - 1; $y <= $gy + 1; $y++) {
//			TRACE("At ($x, $y) :\n");
//			print_r($i_eh[$x][$y]);
			if(count($i_eh[$x][$y]['ids'])) continue;
			if($i_eh[$x][$y]['project_front'] > 0) continue;

			/* Check distance */
			$d = distance($i_eh[$x][$y]['x'], $i_eh[$x][$y]['y'], $fx, $fy);
			if($d < $smallest_d) {
				$smallest_d = $d;
				$smallest_ix = $x;
				$smallest_iy = $y;
				$found = true;
			}
		}
	}

	if($found == false) {
		echo "ERROR: couldn't find project front for:\n";
		print_r($o);
		exit;
	}

	$i_eh[$smallest_ix][$smallest_iy]['project_front'] = $oid;
	$objects[$oid]['front_x'] = $fx;
	$objects[$oid]['front_y'] = $fy;
	$objects[$oid]['front_grid_ix'] = $smallest_ix;
	$objects[$oid]['front_grid_iy'] = $smallest_iy;
}
TRACE("Done.\n");

switch($action) {
case 'images':
	exhibithall_images();
	exit;
}

/* Compute closest projects to each project */
$project_distance = 100 / $grid_size;
foreach($objects as $oid=>$o) {
	if($o['type'] != 'project') continue;

	TRACE("Computing paths for {$o['name']}...\n");

	/* Get a pointer to the exhibit hall */
	$i_eh = &$exhibithall[$o['exhibithall_id']];

	/* Starting grid location */
	$grid_start = &$i_eh[$o['front_grid_ix']][$o['front_grid_iy']];

	/* Path to all other objects in the same exhibit hall */
	foreach($objects as $d_oid=>$d_o) {
		if($d_oid == $oid) continue;
		if($o['exhibithall_id'] != $d_o['exhibithall_id']) continue;

		$d = grid_path($o, $d_o);
		$objects[$oid]['nearby_projects'][] = array('distance' => $d, 'id'=>$d_oid);
		if($d < $project_distance) $project_distance = $d;
	}
	/* Use the grid_path_cmp to sort the projects based on 'distance' */
	usort($objects[$oid]['nearby_projects'], 'grid_path_cmp');
}
TRACE("Project Distance: {$project_distance} hops\n");

/* Compute project distances */
foreach($objects as $oid=>$o) {
	if($o['type'] != 'project') continue;
	foreach($objects[$oid]['nearby_projects'] as &$nearby_project) {
		$nearby_project['project_distance'] = $nearby_project['distance'] / $project_distance;
	}
}



/* Build a list of floor objects for the annealer*/
$floor_objects = array();
$x = 0;
foreach($objects as $oid=>$o) {
	if($o['type'] != 'project') continue;
	$objects[$oid]['floor_object_offset'] = $x; /* The same as the annealer bucket id */
	$floor_objects[$x++] = &$objects[$oid];
}


/* Project floor location cost:
 * - Keep divisions together
 * - keep grades together
 * - a project should have one of the same school nearby or adjacent, but not a lot nearby 
 */
function project_cost(&$annealer, $bucket_id, $ids)
{
	global $floor_objects, $projects, $exhibithall, $objects;
	$cost = 0;

	/* Get the floor object */
	$o = &$floor_objects[$bucket_id];

	/* The exhibit hall */
	$eh = &$exhibithall[$o['exhibithall_id']];

	if(count($ids) == 0) {
//		TRACE("No items in bucket, returning 0.\n");
		return 0;
	}
	if(count($ids) > 1) {
		echo "More than one item in bucket! Bug somewhere.\n";
		exit;
	}

	/* Get the project info */
	$p = &$projects[$ids[0]];

	$school_match = 0;
	$div_match = 0;
	$grade_match = 0;

	$x = 0;
//	TRACE("Cost for bucket $bucket_id...\n");
	$min = $p['grade'];
	$max = $p['grade'];
	foreach($o['nearby_projects'] as &$n) {
		
		/* Get the nearby project object*/
		$nearby_o = &$objects[$n['id']];

//		TRACE("   Scanning nearby location {$n['id']} (distance={$n['distance']})\n");
//		print_r($nearby_o);

		$nearby_bucket_id = $nearby_o['floor_object_offset'];
		$nearby_bkt = &$annealer->bucket[$nearby_bucket_id];

//		TRACE("      Bucket id: {$nearby_bucket_id} with  ".count($nearby_bkt)." items\n");
		if(count($nearby_bkt) == 0) continue;

		$nearby_p = &$projects[$nearby_bkt[0]];

		/* Only consider closest 5 projects for school */
		if($nearby_p['schools_id'] == $p['schools_id']) {
			if($x < 5) $school_match++;
		}

		/* Closest 5 projects for divs */
		if($nearby_p['div'] == $p['div']) {
			if($x < 5) $div_match++;
		}

		/* Closest 10 for grade variance */
		if($x < 10) {
			if($nearby_p['grade'] < $min) $min = $nearby_p['grade'];
			if($nearby_p['grade'] > $max) $max = $nearby_p['grade'];
		}
		$x++;
		if($x == 10) break;
	}

	if($school_match == 0) {
//		TRACE("   No school nearby\n");
		$cost += 5;
	}
	if($school_match > 2) {
//		TRACE("   Too many schools bunched up\n");
		$cost += 2 * ($school_match-1);
	}

	if($div_match < 2) {
//		TRACE("   No divs nearby\n");
		$cost += 20;
	}
	if($div_match > 4) {
//		TRACE("   Divs bunching up\n");
		$cost += 10 * ($div_match - 4);
	}
	
	if($max - $min > 0) {
		/* Don't want bunching up grades eitehr */
//		TRACE("   Grades too spread out\n");
		$cost += 50 * ($max-$min);
	}

	/* Make sure this project is allowed in this exhibit hall */
	if(!in_array($p['div'], $eh['divs']))
		$cost += 1000;
	if(!in_array($p['cat'], $eh['cats']))
		$cost += 1000;

	/* Make sure this project is allowed in this floor object too */
	if(count($o['divs']) > 0 && !in_array($p['div'], $o['divs']))
		$cost += 1000;
	if(count($o['cats']) > 0 && !in_array($p['cat'], $o['cats']))
		$cost += 1000;

	/* Match electricity */
	if($p['req_electricity'] == 'yes' && $o['has_electricity'] == 'no') {
		$cost += 1000;
	}
	
//	TRACE("Cost for bucket $bucket_id = $cost\n");
	return $cost;

}

function project_bucket_ids($annealer, $bucket_id)
{
	global $floor_objects, $objects;
	$recompute_ids = array($bucket_id);

	/* Get the floor object */
	$o = &$floor_objects[$bucket_id];

	/* Find the 10 closest projects */
	$x = 0;
	foreach($o['nearby_projects'] as &$n) {
		$nearby_o = &$objects[$n['id']];
		$recompute_ids[] = $nearby_o['floor_object_offset'];
		$x++;
		if($x == 15) break;
	}
	return $recompute_ids;
}

 

$e =  10 * ($config['effort'] / 1000) * pow(count($projects), 1.3333);
$project_ids = array_keys($projects);

//array_splice($project_ids, 20);

$a = new annealer(count($floor_objects), 125, $e, 0.9, 
		project_cost, $project_ids);
$a->set_max_items_per_bucket(1);
//$a->set_delta_cost_bucket_ids_callback(project_bucket_ids);
$a->anneal();

for($x=0;$x<$a->num_buckets; $x++) {
	$bkt = $a->bucket[$x];
	if(count($bkt) > 1) {
		TRACE("Assigned more than one project to bucket $x\n");
		exit;
	}
	if(count($bkt) == 0) continue;

	/* Get the project id in this bucket */
	$projects_id = array_shift($bkt);
	echo "p:$projects_id, n:{$floor_objects[$x]['floornumber']}\n";
	/* Get the floor object for the same bucket and floor number */
	$projects[$projects_id]['floornumber'] = $floor_objects[$x]['floornumber'];
}

print_r($projects);

/* Assign floor numbers */
mysql_query("UPDATE projects SET floornumber=0 WHERE year='{$config['FAIRYEAR']}'");

foreach($projects as $pid=>$p) {
	mysql_query("UPDATE projects SET floornumber='{$p['floornumber']}' WHERE id='$pid'");
	TRACE("Project $pid => Floor number {$p['floornumber']}\n");
}

TRACE("</pre>");



function exhibithall_images()
{
	global $exhibithall, $objects, $projects;

	/* Assign project IDs to objects */
	foreach($objects as $oid=>$o) {
		foreach($projects as $pid=>$p) {
			if($p['floornumber'] == $o['floornumber']) {
				$objects[$oid]['projects_id'] = $pid;
				break;
			}
		}
	}

	foreach($exhibithall as &$i_eh) {

		$i = imagecreatetruecolor($i_eh['w']*100, $i_eh['h']*100);
		$c_grey = imagecolorallocate($i, 128, 128, 128);
		$c_white = imagecolorallocate($i, 255, 255, 255);
		$c_black = imagecolorallocate($i, 0, 0, 0);

		// Fill the background with the color selected above.
		imagefill($i, 0, 0, $c_white);
		imagerectangle($i, 0, 0, $i_eh['w']*100 - 1, $i_eh['h']*100 - 1, $c_black);

		for($ix=0;$ix<=$i_eh['grid_w'];$ix++) {
			for($iy=0;$iy<=$i_eh['grid_h'];$iy++) {
				$l = $i_eh[$ix][$iy];
				if(count($l['ids']) > 0) {
					imageellipse($i, $l['x']*100, $l['y']*100, 1, 1, $c_black);
				} else {
					imageellipse($i, $l['x']*100, $l['y']*100, 1, 1, $c_grey);
				}
			}
		}
		foreach($objects as $oid=>$o) {
			if($o['exhibithall_id'] != $i_eh['id']) continue;

			list($x1,$y1) = point_rotate(-$o['w2'], -$o['h2'], $o['orientation']);
			list($x2,$y2) = point_rotate($o['w2'], $o['h2'], $o['orientation']);
			imagerectangle($i, ($o['x']+$x1)*100, ($o['y']+$y1)*100, ($o['x']+$x2)*100, ($o['y']+$y2)*100, $c_black);

			$p = $projects[$o['projects_id']];
			imagestring($i, 4, $o['x']*100 - 30, $o['y']*100 - 35, "{$o['floornumber']} ({$p['projects_id']})", $c_black);
			imagestring($i, 4, $o['x']*100 - 30, $o['y']*100 - 20, "gr:{$p['grade']}  ", $c_black);
			$d = $divshort[$p['div']];
			imagestring($i, 4, $o['x']*100 - 30, $o['y']*100 - 5, "d:$d ({$p['div']})", $c_black);
			imagestring($i, 4, $o['x']*100 - 30, $o['y']*100 + 10, "s:{$p['schools_id']}", $c_black);
			imagestring($i, 4, $o['x']*100 - 30, $o['y']*100 + 25, "e:{$p['req_electricity']}", $c_black);

		
		}


		imagepng($i, "./eh-{$i_eh['id']}.png");

	}
}



?>
