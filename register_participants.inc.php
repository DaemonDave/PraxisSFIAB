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
function registrationFormsReceived($reg_id="")
{
	if($reg_id) $rid=$reg_id;
	else $rid=$_SESSION['registration_id'];
	$q=mysql_query("SELECT status FROM registrations WHERE id='$rid'");
	$r=mysql_fetch_object($q);
	if($r->status=="complete" || $r->status=="paymentpending")
		return true;
	else
		return false;

}
function registrationDeadlinePassed()
{
	global $config;
	$q=mysql_query("SELECT (NOW()<'".$config['dates']['regclose']."') AS datecheck");
	$datecheck=mysql_fetch_object($q);
	if($datecheck->datecheck==1)
		return false;
	else
		return true;

}

function studentStatus($reg_id="")
{
	global $config;
	if($config['participant_student_personal']=="yes")
		$required_fields=array("firstname","lastname","address","city","postalcode","phone","email","grade","dateofbirth","schools_id","sex");
	else
		$required_fields=array("firstname","lastname","email","grade","schools_id");

	if($config['participant_student_tshirt']=="yes")
		$required_fields[]="tshirt";

	if($reg_id) $rid=$reg_id;
	else $rid=$_SESSION['registration_id'];

	$q=mysql_query("SELECT * FROM students WHERE registrations_id='$rid' AND year='".$config['FAIRYEAR']."'");

	//if we dont have the minimum, return incomplete
	if(mysql_num_rows($q)<$config['minstudentsperproject'])
		return "incomplete";

	while($r=mysql_fetch_object($q))
	{
		foreach ($required_fields AS $req)
		{
			if($req=="dateofbirth")
			{
				if($r->$req=="0000-00-00" || !$r->$req)
					return "incomplete";
			}
			else
			{
				if(!$r->$req)
					return "incomplete";
			}
		}
	}

	//if it made it through without returning incomplete, then we must be complete
	return "complete";
}

function emergencycontactStatus($reg_id="")
{
	global $config;
	$required_fields=array("firstname","lastname","relation","phone1");

	if($reg_id) $rid=$reg_id;
	else $rid=$_SESSION['registration_id'];

	$sq=mysql_query("SELECT id FROM students WHERE registrations_id='$rid' AND year='".$config['FAIRYEAR']."'");
	$numstudents=mysql_num_rows($sq);

	while($sr=mysql_fetch_object($sq))
	{
		$q=mysql_query("SELECT * FROM emergencycontact WHERE registrations_id='$rid' AND year='".$config['FAIRYEAR']."' AND students_id='$sr->id'");

		$r=mysql_fetch_object($q);

		foreach ($required_fields AS $req)
		{
			if(!$r->$req)
			{
				return "incomplete";
			}
		}
	}

	//if it made it through without returning incomplete, then we must be complete
	return "complete";
}

function projectStatus($reg_id="")
{
	global $config;
	$required_fields=array("title","projectcategories_id","projectdivisions_id","language","req_table","req_electricity","summarycountok");

	if($config['participant_short_title_enable'] == 'yes') 
		$required_fields[] = 'shorttitle';

	if($config['participant_project_summary_wordmin'] > 0) 
		$required_fields[] = 'summary';

	if($reg_id) $rid=$reg_id;
	else $rid=$_SESSION['registration_id'];

	$q=mysql_query("SELECT * FROM projects WHERE registrations_id='$rid' AND year='".$config['FAIRYEAR']."'");

	//if we dont have a project entry yet, return empty
	if(!mysql_num_rows($q))
		return "empty";

	while($r=mysql_fetch_object($q))
	{
		foreach ($required_fields AS $req)
		{
			if(!$r->$req) {
				return "incomplete";
			}
		}
	}

	//if it made it through without returning incomplete, then we must be complete
	return "complete";
}


function mentorStatus($reg_id="")
{
	global $config;
	$required_fields=array("firstname","lastname","phone","email","organization","description");

	if($reg_id) $rid=$reg_id;
	else $rid=$_SESSION['registration_id'];

	//first check the registrations table to see if 'nummentors' is set, or if its null
	$q=mysql_query("SELECT nummentors FROM registrations WHERE id='$rid' AND year='".$config['FAIRYEAR']."'");
	$r=mysql_fetch_object($q);
	if($r->nummentors==null)
		return "incomplete";

	$q=mysql_query("SELECT * FROM mentors WHERE registrations_id='$rid' AND year='".$config['FAIRYEAR']."'");

	//if we dont have the minimum, return incomplete
	if(mysql_num_rows($q)<$config['minmentorserproject'])
		return "incomplete";

	while($r=mysql_fetch_object($q))
	{
		foreach ($required_fields AS $req)
		{
			if(!$r->$req)
			{
				return "incomplete";
			}
		}
	}

	//if it made it through without returning incomplete, then we must be complete
	return "complete";

}

function safetyStatus($reg_id="")
{
	global $config;

	if($reg_id) $rid=$reg_id;
	else $rid=$_SESSION['registration_id'];

	//grab all of their answers
	$q=mysql_query("SELECT * FROM safety WHERE registrations_id='$rid'");
	while($r=mysql_fetch_object($q))
	{
		$safetyanswers[$r->safetyquestions_id]=$r->answer;
	}

	//now grab all the questions
	$q=mysql_query("SELECT * FROM safetyquestions WHERE year='".$config['FAIRYEAR']."' ORDER BY ord");
	while($r=mysql_fetch_object($q))
	{
		if($r->required=="yes" && !$safetyanswers[$r->id])
		{
			return "incomplete";
		}
	}
	return "complete";

}

function spawardStatus($reg_id="")
{
	global $config;
	if($reg_id) $rid=$reg_id;
	else $rid=$_SESSION['registration_id'];

	$q=mysql_query("SELECT * FROM projects WHERE registrations_id='$rid'");
	$project=mysql_fetch_object($q);

	/* We want this query to get any awards with a NULL award_awards_id */
	$awardsq=mysql_query("SELECT
				projects.id AS projects_id
			FROM
				project_specialawards_link,
				projects
			WHERE
				project_specialawards_link.projects_id='".$project->id."'
				AND projects.year='".$config['FAIRYEAR']."'
				");

	if(mysql_num_rows($awardsq))
		return "complete";
	else
		return "incomplete";
}

function tourStatus($reg_id="")
{
	global $config;

	if($reg_id) $rid=$reg_id;
	else $rid=$_SESSION['registration_id'];

	/* Get the students for this project */
	$q=mysql_query("SELECT * FROM students WHERE registrations_id='$rid' AND year='".$config['FAIRYEAR']."'");
	$num_found = mysql_num_rows($q);

	$ret = "complete";
	while($s=mysql_fetch_object($q)) {
		//grab all of their tour prefs
		$sid = $s->id;
		$qq=mysql_query("SELECT * FROM tours_choice WHERE students_id='$sid' and year='{$config['FAIRYEAR']}' ORDER BY rank");

		$n_tours = mysql_num_rows($qq);
		if($n_tours > 0) {
			/* See if there's a rank 0 tour (rank 0 == their tour assignment) */
			$i = mysql_fetch_object($qq);
			if($i->rank == 0) {
				/* Yes, there is, no matter what, this student's tour
				 * selection is complete. */
				continue;
			}
		}

		/* Else, they haven't been assigned a tour, see if they've made
		 * the appropraite selection(s) */
		if( ($n_tours >= $config['tours_choices_min']) && ($n_tours <= $config['tours_choices_max']) ){
			continue;
		}
		$ret = "incomplete";
		break;
	}
	return $ret;
}
function namecheckStatus($reg_id="")
{
	global $config;

	if($reg_id) {
		$q=mysql_query("SELECT * FROM students WHERE 
				registrations_id='$reg_id' 
				AND year='".$config['FAIRYEAR']."'");
	} else {
		$q=mysql_query("SELECT * FROM students WHERE 
				id='{$_SESSION['students_id']}'");
	}

	/* Get the students for this project */
	while($s=mysql_fetch_object($q)) {
		if($s->namecheck_complete == 'no') {
			return 'incomplete';
		}
	}
	return 'complete';
}


function generateProjectNumber($registration_id)
{
	global $config;

	$reg_id = $registration_id;
	
	$q=mysql_query("SELECT 	projects.projectcategories_id, 
				projects.projectdivisions_id,
				projectcategories.category_shortform,
				projectdivisions.division_shortform
			FROM 
				projects,
				projectcategories,
				projectdivisions
			WHERE 
				registrations_id='$reg_id'
			AND	projects.projectdivisions_id=projectdivisions.id
			AND	projects.projectcategories_id=projectcategories.id
			AND	projectcategories.year='{$config['FAIRYEAR']}'
			AND	projectdivisions.year='{$config['FAIRYEAR']}'
				");
				echo mysql_error();
	$r=mysql_fetch_object($q);

	$p=array('number'=>array(), 'sort'=>array() );
	$p['number']['str'] = $config['project_num_format'];
	$p['sort']['str'] = trim($config['project_sort_format']);

	if($p['sort']['str'] == '') $p['sort']['str'] = $p['number']['str'];

	/* Replace each letter with {letter}, so that we can do additional
	 * replacements below, without risking subsituting in a letter that may
	 * get replaced. */
	foreach(array('number','sort') as $x) {
		$p[$x]['str']=ereg_replace('[CcDd]', '{\\0}', $p[$x]['str']);
		$p[$x]['str']=ereg_replace('(N|X)([0-9])?', '{\\0}', $p[$x]['str']);
	}

	/* Do some replacements that we don' thave to do anything fancy with,
	 * and setup some variables for future queries */
	foreach(array('number','sort') as $x) {
		$p[$x]['str']=str_replace('{D}',$r->projectdivisions_id,$p[$x]['str']);
		$p[$x]['str']=str_replace('{C}',$r->projectcategories_id,$p[$x]['str']);
		$p[$x]['str']=str_replace('{d}',$r->division_shortform,$p[$x]['str']);
		$p[$x]['str']=str_replace('{c}',$r->category_shortform,$p[$x]['str']);
		$p[$x]['n_used'] = array();
		$p[$x]['x_used'] = array();
	}

	/* Build a total list of projects for finding a global number, and
	 * while constructing the list, build a list for the division/cat 
	 * sequence number */
	$q = mysql_query("SELECT projectnumber_seq,projectsort_seq,
				projectdivisions_id,projectcategories_id 
			FROM projects 
			WHERE year='{$config['FAIRYEAR']}'
				AND projectnumber_seq!='0'
				AND projectnumber IS NOT NULL");
	echo mysql_error();
	while($i = mysql_fetch_object($q)) {
		if( ($r->projectdivisions_id == $i->projectdivisions_id)
		  &&($r->projectcategories_id == $i->projectcategories_id) ) {
			$p['number']['n_used'][] = $i->projectnumber_seq;
			$p['sort']['n_used'][] = $i->projectsort_seq;
		}

		$p['number']['x_used'][] = $i->projectnumber_seq;
		$p['sort']['x_used'][] = $i->projectsort_seq;
	}
	
	/* We only support one N or X to keep things simple, find which
	 * one we need and how much to pad it */
	foreach(array('number','sort') as $x) {
		if(ereg("(N|X)([0-9])?", $p[$x]['str'], $regs)) {
			$p[$x]['seq_type'] = $regs[1];
			if($regs[2] != '') 
				$p[$x]['seq_pad'] = $regs[2];
			else
				$p[$x]['seq_pad'] = ($regs[1] == 'N') ? 2 : 3;

			if($regs[1] == 'N') 
				$p[$x]['used'] = $p[$x]['n_used'];
			else 
				$p[$x]['used'] = $p[$x]['x_used'];
		} else {
			/* FIXME: maybe we should error here?  Not having an N
			 * or an X in the projectnumber or projectsort is a bad
			 * thing */
			$p[$x]['seq_type'] = '';
			$p[$x]['seq_pad'] = 0;
			$p[$x]['used'] = array();
		}
	}

	/* Find the lowest unused number.  FIXME: this could be a config
	 * option, we could search for the lowest unused number (if projects
	 * get deleted), or we could just go +1 beyond the highest */
	foreach(array('number','sort') as $x) {
		if($p[$x]['seq_type'] == '') continue;
		$n = 0;
		while(1) {
			$n++;
			if(in_array($n, $p[$x]['used'])) continue;

			$r = sprintf("%'0{$p[$x]['seq_pad']}d", $n);
			$str = ereg_replace("{(N|X)([0-9])?}", $r, $p[$x]['str']);
			$p[$x]['str'] = $str;
			$p[$x]['n'] = $n;
			break;
		}

		/* If we're using the same number type for sorting, then we, in
		 * theory, know what that number is, so we can go ahead and
		 * blindly use it */
		if($p['number']['seq_type'] == $p['sort']['seq_type']) {
			$r = sprintf("%'0{$p['sort']['seq_pad']}d", $n);
			$p['sort']['str'] = ereg_replace("{(N|X)([0-9])?}", $r, $p['sort']['str']);
			$p['sort']['n'] = $n;
			break;
		}
	}

	return array($p['number']['str'], $p['sort']['str'],
			$p['number']['n'], $p['sort']['n']);
}

function computeRegistrationFee($regid)
{
 	global $config;
	$ret = array();

	$regfee_items = array();
	$q = mysql_query("SELECT * FROM regfee_items
				WHERE year='{$config['FAIRYEAR']}'");
	while($i = mysql_fetch_assoc($q)) $regfee_items[] = $i;

	$q=mysql_query("SELECT * FROM students WHERE registrations_id='$regid' AND year='".$config['FAIRYEAR']."'");
	$n_students = mysql_num_rows($q);
	$n_tshirts = 0;
	$sel = array();
	while($s = mysql_fetch_object($q)) {
		if($s->tshirt != 'none') $n_tshirts++;

		/* Check their regfee items too */
		if($config['participant_regfee_items_enable'] != 'yes') continue;

		$sel_q = mysql_query("SELECT * FROM regfee_items_link 
					WHERE students_id={$s->id}");
		while($info_q = mysql_fetch_assoc($sel_q)) {
			$sel[] = $info_q['regfee_items_id'];
		}
	}

	if($config['regfee_per'] == 'student') {
		$f = $config['regfee'] *  $n_students;
		$ret[] = array( 'id' => 'regfee',
				'text' => "Fair Registration (per student)",
				'base' => $config['regfee'],
				'num' => $n_students,
				'ext' => $f );
 		$regfee += $f; 
	} else {
		$ret[] = array( 'id' => 'regfee',
				'text' => "Fair Registration (per project)",
				'base' => $config['regfee'],
				'num' => 1,
				'ext' => $config['regfee'] );
		$regfee += $config['regfee'];
	}

	if($config['participant_student_tshirt'] == 'yes') {
		$tsc = floatval($config['participant_student_tshirt_cost']);
		if($tsc != 0.0) {
			$f = $n_tshirts * $tsc;
			$regfee += $f;
			if($n_tshirts != 0) {
				$ret[] = array( 'id' => 'tshirt',
						'text' => "T-Shirts",
						'base' => $tsc,
						'num' => $n_tshirts,
						'ext' => $f);
			} 
		}
	}

	/* $sel will be empty if regfee_items is disabled */
	foreach($regfee_items as $rfi) {
		$cnt = 0;
		foreach($sel as $s) if($rfi['id'] == $s) $cnt++;

		if($cnt == 0) continue;

		$tsc = floatval($rfi['cost']);

		/* If it's per project, force the count to 1 */
		if($rfi['per'] == 'project') {
			$cnt = 1;
		}

		$f = $tsc * $cnt;
		$ret[] = array( 'id' => "regfee_item_{$rfi['id']}",
				'text' => "{$rfi['name']} (per {$rfi['per']})" ,
				'base' => $tsc,
				'num' => $cnt,
				'ext' => $f);
		$regfee += $f;
	}
	return array($regfee, $ret);
}



?>
