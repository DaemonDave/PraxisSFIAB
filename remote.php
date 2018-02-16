<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>
   Copyright (C) 2009 David Grant <dave@lightbox.org>

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
require_once('common.inc.php');
require_once('user.inc.php');


require_once('fair_additional_materials.inc.php');

function handle_getstats(&$u, $fair,&$data, &$response)
{
	$year = $data['getstats']['year'];

	/* Send back the stats we'd like to collect */
	$response['statconfig'] = split(',', $fair['gather_stats']);

	/* Send back the stats we currently have */
	$q = mysql_query("SELECT * FROM fairs_stats WHERE fairs_id='{$u['fairs_id']}'
				AND year='$year'");
	$response['stats'] = mysql_fetch_assoc($q);
	unset($response['stats']['id']);
	$response['error'] = 0;
}

function handle_stats(&$u,$fair, &$data, &$response)
{
	$stats = $data['stats'];
	foreach($stats as $k=>$v) {
		$stats[$k] = mysql_escape_string($stats[$k]);
	}

//	$str = join(',',$stats);
	$keys = '`fairs_id`,`'.join('`,`', array_keys($stats)).'`';
	$vals = "'{$u['fairs_id']}','".join("','", array_values($stats))."'";
	mysql_query("DELETE FROM fairs_stats WHERE fairs_id='{$u['fairs_id']}'
		AND year='{$stats['year']}'");
	echo mysql_error();
	mysql_query("INSERT INTO fairs_stats (`id`,$keys) VALUES ('',$vals)");
	echo mysql_error();

	$response['message'] = 'Stats saved';
	$response['error'] = 0;
}

function handle_getawards(&$u, $fair, &$data, &$response)
{
	$awards = array();
	$year = $data['getawards']['year'];

	$ids = array();
	/* Load a list of awards linked to the fair id */
	$q = mysql_query("SELECT * FROM fairs_awards_link WHERE fairs_id='{$fair['id']}'");
	while($r = mysql_fetch_assoc($q)) {
		$aaid = $r['award_awards_id'];
		if($r['download_award'] == 'yes') $ids[] = $aaid;
		$ul[$aaid] = $r['upload_winners'];
	}

	/* Load the awards this fair is allowed to download */
	$where = "(id='".join("' OR id='", $ids)."')";
	$q = mysql_query("SELECT * FROM award_awards WHERE $where AND year='$year'" );

	while($a = mysql_fetch_assoc($q)) {
		$award = array();
		$award['identifier'] = $a['external_identifier'];
		$award['external_additional_materials'] = $a['external_additional_materials'];
		$award['external_register_winners'] = $a['external_register_winners'];
		$award['year'] = $a['year'];
		$award['name_en'] = $a['name'];
		$award['criteria_en'] = $a['criteria'];
		$award['upload_winners'] = $ul[$a['id']];
		$award['self_nominate'] = $a['self_nominate'];
		$award['schedule_judges'] = $a['schedule_judges'];
		
		if($a['sponsors_id']) {
			$sq = mysql_query("SELECT * FROM sponsors WHERE id='{$a['sponsors_id']}'");
			if(mysql_num_rows($sq)) {
				$s =  mysql_fetch_assoc($sq);
				$award['sponsor'] = $s['organization'];
			}
		}

		$award['prizes'] = array();
		$pq = mysql_query("SELECT * FROM award_prizes WHERE award_awards_id='{$a['id']}'");
		while($p = mysql_fetch_assoc($pq)) {
			/* Map array keys -> local database field */
			$map = array(	'cash' => 'cash', 'scholarship' => 'scholarship',
					'value' => 'value', 'prize_en' => 'prize', 'number'=>'number',
					'ord'=>'order',
					'trophystudentkeeper'=>'trophystudentkeeper',
					'trophystudentreturn'=>'trophystudentreturn',
					'trophyschoolkeeper'=>'trophyschoolkeeper',
					'trophyschoolreturn'=>'trophyschoolreturn');
			$prize = array('identifier' => '');
			foreach($map as $k=>$field) $prize[$k] = $p[$field];

			$award['prizes'][] = $prize;
		}
		$awards[] = $award;
	}
	$response['awards'] = $awards;
	$response['postback'] = 'http://localhost';
}

function award_upload_update_school(&$mysql_query, &$school, $school_id = -1)
{

	/* transport name => mysql name */
	$school_fields = array( //'schoolname'=>'school',
				'schoollang'=>'schoollang',
				'schoollevel'=>'schoollevel',
				'board'=>'board',
				'district'=>'district',
				'phone'=>'phone',
				'fax'=>'fax',
				'address'=>'address',
				'city'=>'city',
				'province_code'=>'province_code',
				'postalcode'=>'postalcode',
				'schoolemail'=>'schoolemail');
/*				'principal'=>'principal',
				'sciencehead'=>'sciencehead',
				'scienceheademail'=>'scienceheademail',
				'scienceheadphone'=>'scienceheadphone');*/

	if($school_id == -1) {
		$our_school = mysql_fetch_assoc($mysql_query);
		$sid = $our_school['id'];
	} else {
		$sid = $school_id;
		$our_school = array();
	}
	$set = '';
	foreach($school_fields as $t=>$m) {
		if($our_school[$m] == $school[$t]) continue;
		if($set != '') $set.=',';
		$set .= "`$m`='".mysql_real_escape_string($school[$t])."'";
	}
	mysql_query("UPDATE schools SET $set WHERE id='$sid'");
	return $sid;
}

function award_upload_school(&$student, &$school, $year, &$response)
{

	$school_name = mysql_real_escape_string($school['schoolname']);
	$school_city = mysql_real_escape_string($school['city']);
	$school_phone = mysql_real_escape_string($school['phone']);
	$school_addr = mysql_real_escape_string($school['address']);
	$student_city = $student['city'];

	/* Find school by matching name, city, phone, year */
	$q = mysql_query("SELECT * FROM schools WHERE school='$school_name' AND city='$school_city' AND phone='$school_phone' AND year='$year'");
	if(mysql_num_rows($q) == 1) return award_upload_update_school($q, $school);

	/* Find school by matching name, city, address, year */
	$q = mysql_query("SELECT * FROM schools WHERE school='$school_name' AND city='$school_city' AND address='$school_addr' AND year='$year'");
	if(mysql_num_rows($q) == 1) return award_upload_update_school($q, $school);

	/* Find school by matching name, city, year */
	$q = mysql_query("SELECT * FROM schools WHERE school='$school_name' AND city='$school_city' AND year='$year'");
	if(mysql_num_rows($q) == 1) return award_upload_update_school($q, $school);

	/* Find school by matching name, student city, year */
	$q = mysql_query("SELECT * FROM schools WHERE school='$school_name' AND city='$student_city' AND year='$year'");
	if(mysql_num_rows($q) == 1) return award_upload_update_school($q, $school);

	$response['notice'][] = "      - Creating new school: $school_name";
	/* No? ok, make a new school */
	mysql_query("INSERT INTO schools(`school`,`year`) VALUES ('".mysql_real_escape_string($school['schoolname'])."','$year')");
	$school_id = mysql_insert_id();
	return award_upload_update_school($q, $school, $school_id);
}

function award_upload_assign(&$fair, &$award, &$prize, &$project, $year, &$response)
{
	$reg_email_needs_update = false;
	$new_reg = false;
	/* Copied from admin/award_upload.php, this is the
	 *  transport name => sql name mapping */
	$student_fields = array('firstname'=>'firstname',
				'lastname'=>'lastname',
				'email'=>'email',
				'gender'=>'sex',
				'grade'=>'grade',
				'language'=>'lang',
				'birthdate'=>'dateofbirth',
				'address'=>'address',
				'city'=>'city',
				'province'=>'province',
				'postalcode'=>'postalcode',
				'phone'=>'phone',
				'teachername'=>'teachername',
				'teacheremail'=>'teacheremail');

	/* See if this project already exists */
	$pn = mysql_real_escape_string($project['projectnumber']);
	$q = mysql_query("SELECT * FROM projects WHERE projectnumber='$pn' AND fairs_id='{$fair['id']}' AND year='$year'");
	echo mysql_error();
	if(mysql_num_rows($q) == 1) {
		$our_project = mysql_fetch_assoc($q);
		$registrations_id = $our_project['registrations_id'];
		$pid = $our_project['id'];
		$response['notice'][] = "   - Found existing project: {$project['title']}";
	} else {
		$response['notice'][] = "   - Creating new project: {$project['title']}";
		/* Create a registration */
		$regnum=0;
		//now create the new registration record, and assign a random/unique registration number to then.
		do {
			//random number between
			//100000 and 999999  (six digit integer)
			$regnum=rand(100000,999999);
			$q=mysql_query("SELECT * FROM registrations WHERE num='$regnum' AND year=$year");
			echo mysql_error();
		}while(mysql_num_rows($q)>0);

		//actually insert it
		mysql_query("INSERT INTO registrations (num,email,start,status,schools_id,year) VALUES (".
				"'$regnum','$regnum',NOW(),'open',NULL,'$year')");
		$registrations_id = mysql_insert_id();
		/* We'll fill in the email address later */

		/* Add the project */
		mysql_query("INSERT INTO projects (`registrations_id`,`projectnumber`,`year`,`fairs_id`)
				VALUES('$registrations_id',
					'".mysql_real_escape_string($project['projectnumber'])."',
					'$year', '{$fair['id']}');");
		$pid = mysql_insert_id();
		$reg_email_needs_update = true;
		$new_reg = true;
	}
	$q = mysql_query("SELECT * FROM registrations WHERE id='$registrations_id'");
	$registration = mysql_fetch_assoc($q);

	/* Update the project in case anythign changed */
	mysql_query("UPDATE projects SET title='".mysql_real_escape_string($project['title'])."',
					summary='".mysql_real_escape_string($project['abstract'])."',
					projectcategories_id='".intval($project['projectcategories_id'])."',
					projectdivisions_id='".intval($project['projectdivisions_id'])."'
				WHERE id='$pid'");

	/* Record the winner */
	mysql_query("INSERT INTO winners(`awards_prizes_id`,`projects_id`,`year`,`fairs_id`)
			VALUES('{$prize['id']}','$pid','$year','{$fair['id']}')");

	/* Delete the students attached to this project */
	mysql_query("DELETE FROM students WHERE registrations_id='$registrations_id'");

	/* Add new */
	foreach($project['students'] as &$student) {

		$response['notice'][] = "      - Student {$student['firstname']} {$student['lastname']} saved";

		$schools_id = award_upload_school($student, $student['school'], $year, $response);

		$keys = ",`".join("`,`", array_values($student_fields))."`";
		$values = "";
		foreach($student_fields as $k=>$v) 
			$values .= ",'".mysql_real_escape_string($student[$k])."'";
		/* Note lack of comma before $keys, we added it above for both keys and values */
		mysql_query("INSERT INTO students (`registrations_id`,`fairs_id`, `schools_id`,`year` $keys)
				VALUES('$registrations_id','{$fair['id']}','$schools_id','$year' $values )");

		/* Update the registration email */
		if($reg_email_needs_update) {
			mysql_query("UPDATE registrations SET email='".mysql_real_escape_string($student['email'])."'
					WHERE id='$registrations_id'");
			$reg_email_needs_update = false;
		}

		if($award['external_register_winners'] == 1 && $new_reg == true) {
			/* This award is for students who are participating in this fair, we need
			 * to get their reg number to them if this is a new registration */
			email_send("new_participant",$student['email'],array(),
							array(	"EMAIL"=>$student['email'],
								"REGNUM"=>$registration['num'])
							);
			$response['notice'][] = "         - Sent welcome registration email to: {$student['firstname']} {$student['lastname']} &lt;{$student['email']}&gt;";
		}
	}
	if($award['external_register_winners'] == 0) {
		/* It's not an external, so we don't need the student to login 
		 * or antyhing, we probably want to include it in reports, so set 
		 * it to complete */
		mysql_query("UPDATE registrations SET status='complete' WHERE id='$registrations_id'");
	}
}

function handle_award_upload(&$u, &$fair, &$data, &$response)
{
	$response['notice'][] = 'Handle Award Upload deprecated , please upgrade your SFIAB';
	$response['error'] = 1;
}

function handle_awards_upload(&$u, &$fair, &$data, &$response)
{

//	$response['debug'] = array_keys($data['awards_upload']);
//	$response['error'] = 0;
//	return;
	foreach($data['awards_upload'] as $award_data) {
		$external_identifier = $award_data['external_identifier'];
		$year = intval($award_data['year']);
	
		/* Find the award */
		$eid = mysql_real_escape_string($external_identifier);

		$q = mysql_query("SELECT * FROM award_awards WHERE external_identifier='$eid' AND year='$year'");
		if(mysql_num_rows($q) != 1) {
			$response['message'] = "Unknown award identifier '$eid' for year $year";
			$response['error'] = 1;
			return;
		}
		$award = mysql_fetch_assoc($q);
		$aaid = $award['id'];

		$response['notice'][] = "Found award: {$award['name']}";

		/* Load prizes, we fetched the right award by year, so we don't need to 
		 * check the year as long as we query by aaid */
		$prizes = array();
		$q = mysql_query("SELECT * FROM award_prizes WHERE award_awards_id='$aaid'");
		while($prize = mysql_fetch_assoc($q)) {
			$response['notice'][] = " - Prize: {$prize['prize']}";

			/* Clean out existing winners for this prize */
			mysql_query("DELETE FROM winners WHERE 
					award_prize_id='{$prize['id']}' 
					AND fairs_id='{$fair['id']}'");

			/* Assign projects to this prize */
			$ul_p =& $award_data['prizes'][$prize['prize']];
			if(!is_array($ul_p['projects'])) continue;

			foreach($ul_p['projects'] as &$project) {
				award_upload_assign($fair, $award, $prize, $project, $year, $response);
			}
		}
	}
	$response['notice'][] = 'All awards and winners saved';
	$response['error'] = 0;
}

function handle_get_categories(&$u, &$fair, &$data, &$response)
{
	$year = intval($data['get_categories']['year']);
	$cat = array();
	$q=mysql_query("SELECT * FROM projectcategories WHERE year='$year' ORDER BY id");
	while($r=mysql_fetch_object($q)) {
	        $cat[$r->id]=array('id' => $r->id,
				'category' => $r->category,
				'mingrade' => $r->mingrade,
				'maxgrade' => $r->maxgrade);
	}
	$response['categories'] = $cat;
	$response['error'] = 0;
}

function handle_get_divisions(&$u, &$fair, &$data, &$response)
{
	$year = intval($data['get_divisions']['year']);
	$div = array();
	$q=mysql_query("SELECT * FROM projectdivisions WHERE year='$year' ORDER BY id");
	while($r=mysql_fetch_object($q)) {
		$div[$r->id] = array('id' => $r->id,
				'division' => $r->division);
	}
	$response['divisions'] = $div;
	$response['error'] = 0;
}

function handle_award_additional_materials(&$u, &$fair, &$data, &$response)
{
	$year = intval($data['award_additional_materials']['year']);
	$external_identifier = $data['award_additional_materials']['identifier'];

	$eid = mysql_real_escape_string($external_identifier);
	$q = mysql_query("SELECT * FROM award_awards WHERE external_identifier='$eid' AND year='$year'");
	if(mysql_num_rows($q) != 1) {
		$response['message'] = "Unknown award identifier '$eid'";
		$response['error'] = 1;
		return;
	}
	$award = mysql_fetch_assoc($q);

	$pdf = fair_additional_materials($fair, $award, $year);
	$response['award_additional_materials']['pdf']['header'] = $pdf['header'];
	$response['award_additional_materials']['pdf']['data64'] = base64_encode($pdf['data']);
	$response['error'] = 0;
}

 /* magic quotes DEPRECATED as of PHP 5.3.0, REMOVE as of 6.0, on by default *
  * for any PHP < 5.3.0.  Pain in the ASS.  php is running the urldecode for us,
  * seeing that the string has quotes, then adding quotes before we can
  * json_decode() 
  * It only does this in POST and GET */
 if(get_magic_quotes_gpc())
	$data = json_decode(stripslashes($_POST['json']), true);
 else 
	$data = json_decode($_POST['json'], true);

// echo "post:";print_r($_POST);
// echo "json post: ".htmlspecialchars($_POST['json'])."<br>";
// echo "stripslashes(json post): ".stripslashes($_POST['json'])."<br>";
// echo "data:";print_r($data);
// echo "<br />";
// exit;
 
 $username = $data['auth']['username'];
 $password = $data['auth']['password'];

 $response['query'] = $data;
 
// echo "Authenticating... ";
 $u = user_load_by_email($username);
 if($u == false) {
 	$response['error'] = 1;
	$response['message'] = "Authentication Failed";
	echo json_encode($response);
	exit;
 }
 if(!is_array($u) || $u['password'] == '') {
 	$response['error'] = 1;
	$response['message'] = "Authentication Failed2";
	echo json_encode($response);
	exit;
 }

 if($u['password'] != $password) {
 	$response['error'] = 1;
	$response['message'] = "Authentication Failed3";
	echo json_encode($response);
	exit;
 }

 $q = mysql_query("SELECT * FROM fairs WHERE id='{$u['fairs_id']}'");
 $fair = mysql_fetch_assoc($q);

 $response = array();
 if(array_key_exists('getstats', $data)) handle_getstats($u,$fair, $data, $response);
 if(array_key_exists('stats', $data)) handle_stats($u,$fair, $data, $response);
 if(array_key_exists('getawards', $data)) handle_getawards($u,$fair,$data, $response);
 if(array_key_exists('awards_upload', $data)) handle_awards_upload($u,$fair,$data, $response);
 if(array_key_exists('award_upload', $data)) handle_award_upload($u,$fair,$data, $response);
 if(array_key_exists('get_categories', $data)) handle_get_categories($u,$fair,$data, $response);
 if(array_key_exists('get_divisions', $data)) handle_get_divisions($u,$fair,$data, $response);
 if(array_key_exists('award_additional_materials', $data)) handle_award_additional_materials($u,$fair,$data, $response);

// $response['hi'] = 'hi';
 echo urlencode(json_encode($response));
// echo "Success!<br />";


?>
