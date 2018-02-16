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

require_once('../questions.inc.php');

/* Take the language array in users_judge, unserialize it, and join it
 * with a space */
function report_judges_languages(&$report, $field, $text)
{
	$l = unserialize($text);
	return ($l?join(' ', $l):'');
}

/* It's possible to get through this code and need to have access to more
 * than one year, so we're going to index this array by year first */
$report_judges_divs = array();
$report_judges_cats = array();

function report_judges_load_divs($year)
{
	global $report_judges_divs;
	/* Load divisions for this year, only once */
	if(!array_key_exists($year, $report_judges_divs)) {
		$report_judges_divs[$year] = array();
		$q = mysql_query("SELECT * FROM projectdivisions WHERE year='$year'");
		while(($d = mysql_fetch_assoc($q))) {
			$report_judges_divs[$year][$d['id']] = $d;
		}
	}
}
function report_judges_load_cats($year)
{
	global $report_judges_cats;
	if(!array_key_exists($year, $report_judges_cats)) {
	        $q = mysql_query("SELECT * FROM projectcategories WHERE year='$year'");
		while(($c = mysql_fetch_assoc($q))) {
			$report_judges_cats[$year][$c['id']] = $c;
		}
	}
}


/* Return all divisions rated at expertise level x */
function report_judges_divs_at_exp(&$report, $field, $text)
{
	global $report_judges_divs;

	/* field is divs_at_exp_x[_long] */
	$exp = substr($field, 12,1);
	$long = (strlen($field) == 13) ? false : true;

	/* Text is users_judge.div_prefs */
        $year = $report['year'];
	$divprefs = unserialize($text);
	if(!is_array($divprefs)) return '';

	report_judges_load_divs($year);
	
	/* Find all the requested selections, and add them to the return */
	$ret = array();
	$retl = array();
	foreach($divprefs as $div_id=>$sel) {
		if($sel != $exp) continue;
		$ret[] = $report_judges_divs[$year][$div_id]['division_shortform'];
		$retl[] = $report_judges_divs[$year][$div_id]['division'];
	}
	/* Join it all together with spaces */
	if($long == false) return join(' ', $ret);
	return join(', ', $retl);
}

function report_judges_cats_at_pref(&$report, $field, $text)
{
	global $report_judges_cats;
	$prefs = array('H' => 2, 'h' => 1, 'i' => 0, 'l' => -1, 'L' => -2);

	/* field is cats_at_pref_x[_long] */
	$pref = $prefs[substr($field, 13,1)];
	$long = (strlen($field) == 14) ? false : true;

	/* Text is users_judge.cat_prefs */
        $year = $report['year'];
	$catprefs = unserialize($text);
	if(!is_array($catprefs)) return '';

	report_judges_load_cats($year);

	/* Find all 2-highest selections, and add them to the return */
	$ret = array();
	$retl = array();
	foreach($catprefs as $cat_id=>$sel) {
		if($sel != $pref) continue;
		$ret[] = $report_judges_cats[$year][$cat_id]['category_shortform'];
		$retl[] = $report_judges_cats[$year][$cat_id]['category'];
	}
	/* Join it all together with spaces */
	if($long == false) return join(' ', $ret);
	return join(', ', $retl);
}

function report_judges_custom_question(&$report, $field, $text)
{
	/* Field is 'question_x', users_id is passed in $text */
	$q_ord = substr($field, 9);
        $year = $report['year'];
	$users_id = $text;

	/* Find the actual question ID */
	$q = mysql_query("SELECT * FROM questions WHERE year='$year' AND ord='$q_ord'");
	if(mysql_num_rows($q) != 1)
		return 'Question not specified';
	$question = mysql_fetch_assoc($q);

	$q = mysql_query("SELECT * FROM question_answers WHERE users_id='$users_id' AND questions_id='{$question['id']}'");
	if(mysql_num_rows($q) != 1)
		return '';
	$answer = mysql_fetch_assoc($q);
	return $answer['answer'];
}

function report_judges_div_exp(&$report, $field, $text)
{
	/* Field is 'div_exp_x', users_id is passed in $text */
	$div_id = substr($field, 8);
	$year = $report['year'];
	$users_id = $text;

	$divprefs = unserialize($text);
	if(!is_array($divprefs)) return '';

	return $divprefs[$div_id];
}
function report_judges_cat_pref(&$report, $field, $text)
{
	$prefs = array(-2 => 'Lowest', -1 => 'Low',
			0 => '--',
			'1' => 'High', 2=>'Highest');
	/* Field is 'div_pref_x', users_id is passed in $text */
	$cat_id = substr($field, 9);
        $year = $report['year'];
	$users_id = $text;

	$catprefs = unserialize($text);
	if(!is_array($catprefs)) return '';

	return i18n($prefs[$catprefs[$cat_id]]);
}

function report_judges_team_members(&$report, $field, $text)
{
	$year = $report['year'];
	$judges_teams_id = $text;
	$q = mysql_query("SELECT * FROM judges_teams_link 
						LEFT JOIN users ON judges_teams_link.users_id=users.id
						WHERE judges_teams_link.year='$year'
						AND judges_teams_link.judges_teams_id='$judges_teams_id'");
	$ret = '';
	while( ($m = mysql_fetch_assoc($q))) {
		$add = false;
		switch($field) {
		case 'team_captain':
			if($m['captain'] == 'yes') $add = true;
			break;
	
		case 'team_members_all_except_this':
			/* Not implemented, need to pass teams_id AND users_id in here */
			break;

		case 'team_members_all_except_captain':
			if($m['captain'] == 'no') $add = true;
			break;

		case 'team_members_all':
			$add = true;
			break;
		}

		if($add) {
			if($ret != '') $ret .= ', ';
			$ret .= "{$m['firstname']} {$m['lastname']}";
		}
	}
	return $ret;
}



//$round_special_awards = array();
$report_judges_rounds = array();
function report_judges_load_rounds($year)
{	
	global $config, $report_judges_rounds;
	if(count($report_judges_rounds)) return ;

	$q = mysql_query("SELECT * FROM judges_timeslots WHERE round_id='0' AND `year`='$year'");
	/* Loads judges_timeslots.id, .starttime, .endtime, .date, .name */
	while($r = mysql_fetch_assoc($q)) {
        	$report_judges_rounds[] = $r;

		if($r['type'] == 'divisional1') $report_judges_rounds['divisional1'] = $r;
		if($r['type'] == 'divisional2') $report_judges_rounds['divisional2'] = $r;
	}
//        if($r['type'] == 'special') $round_special_awards[] = $r;
}

function report_judges_time_availability(&$report, $field, $text)
{
	global $config, $report_judges_rounds;
	$year = $report['year'];
	$users_id = $text;

	report_judges_load_rounds($year);

	switch($field) {
	case 'available_in_divisional1':
		$round = $report_judges_rounds['divisional1'];
		break;
	case 'available_in_divisional2':
		$round = $report_judges_rounds['divisional2'];
		break;
	default:
		echo "Not implemented.";
		exit;
	}

	$q = mysql_query("SELECT * FROM judges_availability WHERE users_id='$users_id'");
//	echo mysql_error();
	while(($r = mysql_fetch_assoc($q))) {
		 if($r['start'] <= $round['starttime'] 
                  && $r['end'] >= $round['endtime'] 
                  && $r['date'] == $round['date'] ) {
                        return 'Yes';
                }
	}
	return 'No';
}

/* Components:  languages, teams */

$report_judges_fields = array(
	'last_name' =>  array(
		'name' => 'Judge -- Last Name',
		'header' => 'Last Name',
		'width' => 1.0,
		'table' => 'users.lastname' ),

	'first_name' => array(
		'name' => 'Judge -- First Name',
		'header' => 'First Name',
		'width' => 1.0,
		'table' => 'users.firstname' ),

	'name' =>  array(
		'name' => 'Judge -- Full Name (last, first)',
		'header' => 'Name',
		'width' => 1.75,
		'table' => "CONCAT(users.lastname, ', ', users.firstname)",
		'table_sort'=> 'users.lastname' ),
		
	'namefl' =>  array(
		'name' => 'Judge -- Full Name (first last)',
		'header' => 'Name',
		'width' => 1.75,
		'table' => "CONCAT(users.firstname, ' ', users.lastname)",
		'table_sort'=> 'users.lastname' ),

	'email' =>  array(
		'name' => 'Judge -- Email',
		'header' => 'Email',
		'width' => 2.0,
		'table' => 'users.email'),

	'address' =>  array(
		'name' => 'Judge -- Address Street',
		'header' => 'Address',
		'width' => 2.0,
		'table' => "CONCAT(users.address, ' ', users.address2)"),

	'city' =>  array(
		'name' => 'Judge -- Address City',
		'header' => 'City',
		'width' => 1.5,
		'table' => 'users.city' ),

	'province' =>  array(
		'name' => 'Judge -- Address '.$config['provincestate'],
		'header' => $config['provincestate'],
		'width' => 0.75,
		'table' => 'users.province' ),

	'postal' =>  array(
		'name' => 'Judge -- Address '.$config['postalzip'],
		'header' => $config['postalzip'],
		'width' => 0.75,
		'table' => 'users.postalcode' ),
	
	'phone_home' => array(
		'name' => 'Judge -- Phone (Home)',
		'header' => 'Phone(Home)',
		'width' => 1,
		'table' => 'users.phonehome'),

	'phone_work' => array(
		'name' => 'Judge -- Phone (Work)',
		'header' => 'Phone(Work)',
		'width' => 1.25,
		'table' => "users.phonework"),

	'organization' => array(
		'name' => 'Judge -- Organization',
		'header' => 'Organization',
		'width' => 2,
		'table' => 'users.organization'),

	'languages' => array(
		'name' => 'Judge -- Languages',
		'header' => 'Lang',
		'width' => 0.75,
		'table' => 'users_judge.languages',
		'exec_function' => 'report_judges_languages',
		'components' => array('users_judge')),

	'complete' =>  array(
		'name' => 'Judge -- Registration Complete',
		'header' => 'Cmpl',
		'width' => 0.4,
		'table' => 'users_judge.judge_complete',
		'value_map' => array ('no' => 'No', 'yes' => 'Yes'),
		'components' => array('users_judge')),

	'active' =>  array(
		'name' => 'Judge -- Registration Active for this year',
		'header' => 'Act',
		'width' => 0.4,
		'table' => 'users_judge.judge_active',
		'value_map' => array ('no' => 'No', 'yes' => 'Yes'),
		'components' => array('users_judge')),

	'willing_chair' => array(
		'name' => 'Judge -- Willing Chair',
		'header' => 'Will Chair?',
		'width' => 1,
		'table' => 'users_judge.willing_chair',
		'value_map' => array ('no' => 'No', 'yes' => 'Yes'),
		'components' => array('users_judge')),

	'years_school' => array(
		'name' => 'Judge -- Years of Experience at School level',
		'header' => 'Sch',
		'width' => 0.5,
		'table' => 'users_judge.years_school',
		'components' => array('users_judge')),

	'years_regional' => array(
		'name' => 'Judge -- Years of Experience at Regional level',
		'header' => 'Rgn',
		'width' => 0.5,
		'table' => 'users_judge.years_regional',
		'components' => array('users_judge')),

	'years_national' => array(
		'name' => 'Judge -- Years of Experience at National level',
		'header' => 'Ntl',
		'width' => 0.5,
		'table' => 'users_judge.years_national',
		'components' => array('users_judge')),

	'highest_psd' => array(
		'name' => 'Judge -- Highest Post-Secondary Degree',
		'header' => 'Highest PSD',
		'width' => 1.25,
		'table' => 'users_judge.highest_psd',
		'components' => array('users_judge')),


/* Headers for Division Expertise/Preference Selection */

	'divs_at_exp_5' => array(
		'name' => 'Judge -- Divisions Selected as Expertise 5-Expert (Shortform)',
		'header' => 'Expert Div',
		'width' => 1,
		'table' => 'users_judge.div_prefs',
		'exec_function' => 'report_judges_divs_at_exp',
		'components' => array('users_judge')),

	'divs_at_exp_5_long' => array(
		'name' => 'Judge -- Divisions Selected as Expertise 5-Expert (Full division names)',
		'header' => 'Expert Div',
		'width' => 1.5,
		'table' => 'users_judge.div_prefs',
		'exec_function' => 'report_judges_divs_at_exp', /* Yes, the same function as divs_at_exp_5 */
		'components' => array('users_judge')),

	'divs_at_exp_4' => array(
		'name' => 'Judge -- Divisions Selected as Expertise 4 (Shortform)',
		'header' => '4 Div',
		'width' => 1,
		'table' => 'users_judge.div_prefs',
		'exec_function' => 'report_judges_divs_at_exp',
		'components' => array('users_judge')),

	'divs_at_exp_4_long' => array(
		'name' => 'Judge -- Divisions Selected as Expertise 4 (Full division names)',
		'header' => '4 Div',
		'width' => 1.5,
		'table' => 'users_judge.div_prefs',
		'exec_function' => 'report_judges_divs_at_exp', /* Yes, the same function as divs_at_exp_5 */
		'components' => array('users_judge')),

	'divs_at_exp_3' => array(
		'name' => 'Judge -- Divisions Selected as Expertise 3 (Shortform)',
		'header' => '3 Div',
		'width' => 1,
		'table' => 'users_judge.div_prefs',
		'exec_function' => 'report_judges_divs_at_exp',
		'components' => array('users_judge')),

	'divs_at_exp_3_long' => array(
		'name' => 'Judge -- Divisions Selected as Expertise 3 (Full division names)',
		'header' => '3 Div',
		'width' => 1.5,
		'table' => 'users_judge.div_prefs',
		'exec_function' => 'report_judges_divs_at_exp', /* Yes, the same function as divs_at_exp_5 */
		'components' => array('users_judge')),

	/* Fill these in below, they're all the same */
	'div_exp_1' => array(), 'div_exp_2' => array(), 'div_exp_3' => array(), 'div_exp_4' => array(), 'div_exp_5' => array(), 
	'div_exp_6' => array(), 'div_exp_7' => array(), 'div_exp_8' => array(), 'div_exp_9' => array(), 'div_exp_10' => array(), 
	'div_exp_11' => array(), 'div_exp_12' => array(), 'div_exp_13' => array(), 'div_exp_14' => array(), 'div_exp_15' => array(), 
	'div_exp_16' => array(), 'div_exp_17' => array(), 'div_exp_18' => array(), 'div_exp_19' => array(), 'div_exp_20' => array(), 
	'div_exp_21' => array(), 'div_exp_22' => array(), 'div_exp_23' => array(), 'div_exp_24' => array(), 'div_exp_25' => array(), 
	'div_exp_26' => array(), 'div_exp_27' => array(), 'div_exp_28' => array(), 'div_exp_29' => array(), 'div_exp_30' => array(), 
	'div_exp_31' => array(), 'div_exp_32' => array(), 'div_exp_33' => array(), 'div_exp_34' => array(), 'div_exp_35' => array(), 
	'div_exp_36' => array(), 'div_exp_37' => array(), 'div_exp_38' => array(), 'div_exp_39' => array(), 'div_exp_40' => array(), 
	'div_exp_41' => array(), 'div_exp_42' => array(), 'div_exp_43' => array(), 'div_exp_44' => array(), 'div_exp_45' => array(), 
	'div_exp_46' => array(), 'div_exp_47' => array(), 'div_exp_48' => array(), 'div_exp_49' => array(), 'div_exp_50' => array(), 

/* Category preferences */

	'cats_at_pref_H' => array(
		'name' => 'Judge -- Age Categories Selected as Preference: Highest (Shortform)',
		'header' => 'Highest',
		'width' => 0.8,
		'table' => 'users_judge.cat_prefs',
		'exec_function' => 'report_judges_cats_at_pref',
		'components' => array('users_judge')),

	'cats_at_pref_H_long' => array(
		'name' => 'Judge -- Age Categories Selected as Preference: Highest (Full category names)',
		'header' => 'Highest',
		'width' => 1.2,
		'table' => 'users_judge.cat_prefs',
		'exec_function' => 'report_judges_cats_at_pref',/* Yes, the same function as cats_at_pref_H */
		'components' => array('users_judge')),

	'cats_at_pref_h' => array(
		'name' => 'Judge -- Age Categories Selected as Preference: High (Shortform)',
		'header' => 'High',
		'width' => 0.8,
		'table' => 'users_judge.cat_prefs',
		'exec_function' => 'report_judges_cats_at_pref',
		'components' => array('users_judge')),

	'cats_at_pref_h_long' => array(
		'name' => 'Judge -- Age Categories Selected as Preference: High (Full category names)',
		'header' => 'High',
		'width' => 1.2,
		'table' => 'users_judge.cat_prefs',
		'exec_function' => 'report_judges_cats_at_pref',/* Yes, the same function as cats_at_pref_H */
		'components' => array('users_judge')),

	'cats_at_pref_i' => array(
		'name' => 'Judge -- Age Categories Selected as Preference: Indifferent (Shortform)',
		'header' => 'Indifferent',
		'width' => 0.8,
		'table' => 'users_judge.cat_prefs',
		'exec_function' => 'report_judges_cats_at_pref',
		'components' => array('users_judge')),

	'cats_at_pref_i_long' => array(
		'name' => 'Judge -- Age Categories Selected as Preference: Indifferent (Full category names)',
		'header' => 'Indifferent',
		'width' => 1.2,
		'table' => 'users_judge.cat_prefs',
		'exec_function' => 'report_judges_cats_at_pref',/* Yes, the same function as cats_at_pref_H */
		'components' => array('users_judge')),

	'cat_pref_1' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 1',
		'header' => 'cat1',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,  /* Only disables in the report editor, a report can still use it */
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),
	'cat_pref_2' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 2',
		'header' => 'cat2',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),
	'cat_pref_3' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 3',
		'header' => 'cat3',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),
	'cat_pref_4' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 4',
		'header' => 'cat4',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),
	'cat_pref_5' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 5',
		'header' => 'cat5',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),
	'cat_pref_6' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 6',
		'header' => 'cat6',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),
	'cat_pref_7' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 7',
		'header' => 'cat7',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),
	'cat_pref_8' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 8',
		'header' => 'cat8',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),
	'cat_pref_9' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 9',
		'header' => 'cat9',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),
	'cat_pref_10' => array(
		'name' => 'Judge -- Age Category Preference for Category ID 10',
		'header' => 'cat10',
		'width' => 0.5,
		'table' => 'users_judge.cat_prefs',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_cat_pref',
		'components' => array('users_judge')),

/* Time Availability */
	'available_in_divisional1' =>  array(
		'name' => 'Time Availability -- Available in Divisional Round 1 ',
		'header' => 'R1',
		'width' => 0.5,
		'exec_function' => 'report_judges_time_availability',
		'table' => 'users.id'),
	'available_in_divisional2' =>  array(
		'name' => 'Time Availability -- Available in Divisional Round 2 ',
		'header' => 'R2',
		'width' => 0.5,
		'exec_function' => 'report_judges_time_availability',
		'table' => 'users.id'),

/* Others */

	'special_award_only' =>  array(
		'name' => 'Judge -- Special Award Only Requested',
		'header' => 'SA Only',
		'width' => 0.8,
		'table' => 'users_judge.special_award_only',
		'components' => array('users_judge')),

	'year' =>  array(
		'name' => 'Judge -- Year',
		'header' => 'Year',
		'width' => 0.5,
		'table' => 'users.year'),

	'captain' => array(
		'name' => 'Judge Team -- Team Captain? (Is the judge the captain? Yes/No)',
		'header' => 'Cptn',
		'width' => 0.5,
		'table' => 'judges_teams_link.captain',
		'value_map' => array ('no' => 'No', 'yes' => 'Yes'),
		'components' => array('teams')),

	'team' => array(
		'name' => 'Judge Team -- Name',
		'header' => 'Team Name',
		'width' => 3.0,
		'table' => 'judges_teams.name',
		'components' => array('teams')),

	'teamnum' => array(
		'name' => 'Judge Team -- Team Number',
		'header' => 'Team',
		'width' => 0.5,
		'table' => 'judges_teams.num',
		'components' => array('teams')),

/* Fixme, this requires passing 2 args to the function, can't do that yet 
	'team_members_all_except_this' => array(
		'name' => 'Judge Team -- All other team members',
		'header' => 'Members',
		'width' => 2,
		'table' => 'judges_teams.id',
		'exec_function' => 'report_judges_team_members',
		'components' => array('teams')),
*/
	'team_captain' => array(
		'name' => 'Judge Team -- Name of the Team Captain',
		'header' => 'Captain',
		'width' => 1.75,
		'table' => 'judges_teams.id',
		'exec_function' => 'report_judges_team_members',
		'components' => array('teams')),
	
	'team_members_all_except_captain' => array(
		'name' => 'Judge Team -- All team members, except the Captain',
		'header' => 'Members',
		'width' => 2,
		'table' => 'judges_teams.id',
		'exec_function' => 'report_judges_team_members',
		'components' => array('teams')),
	
	'team_members_all' => array(
		'name' => 'Judge Team -- All team members including the Captain',
		'header' => 'Members',
		'width' => 2,
		'table' => 'judges_teams.id',
		'exec_function' => 'report_judges_team_members',
		'components' => array('teams')),
	
	'project_pn' => array(
		'name' => 'Project -- Number',
		'header' => 'Number',
		'width' => 0.5,
		'table' => 'projects.projectnumber',
		'components' => array('teams', 'projects')),

	'project_title' => array(
		'name' => 'Project -- Title',
		'header' => 'Project',
		'width' => 3,
		'table' => 'projects.title',
		'components' => array('teams', 'projects')),

	'project_summary' => array(
		'name' => 'Project -- Summary',
		'header' => 'Summary',
		'width' => 5,
		'table' => 'projects.summary',
		'components' => array('teams', 'projects')),

	'project_language' => array(
		'name' => 'Project -- Language',
		'header' => 'Lang',
		'width' => 0.4,
		'table' => 'projects.language',
		'components' => array('teams', 'projects')),
		
	'project_students' => array(
		'name' => 'Project -- Student Name(s) (REQUIRES MYSQL 5.0) ',
		'header' => 'Student(s)',
		'width' => 3.0,
		'table' => "GROUP_CONCAT(students.firstname, ' ', students.lastname ORDER BY students.lastname SEPARATOR ', ')",
		'group_by' => array('users.id','judges_teams_timeslots_projects_link.id'),
		'components' => array('teams', 'projects', 'students')),

	'project_timeslot_start' => array(
		'name' => 'Project -- Timeslot Start Time (HH:MM)',
		'header' => 'Start',
		'width' => 0.75,
		'table' => "TIME_FORMAT(judges_timeslots.starttime,'%H:%i')",
		'components' => array('teams', 'projects')),

	'project_timeslot_end ' => array(
		'name' => 'Project -- Timeslot End Time (HH:MM)',
		'header' => 'End',
		'width' => 0.75,
		'table' => "TIME_FORMAT(judges_timeslots.endtime,'%H:%i')",
		'components' => array('teams', 'projects')),

	'project_timeslot' => array(
		'name' => 'Project -- Timeslot Start - End (HH:MM - HH:MM)',
		'header' => 'Timeslot',
		'width' => 1.5,
		'table' => "CONCAT(TIME_FORMAT(judges_timeslots.starttime,'%H:%i'),'-',TIME_FORMAT(judges_timeslots.endtime,'%H:%i'))",
		'components' => array('teams', 'projects')),

	'project_timeslot_date' => array(
		'name' => 'Project -- Timeslot Date - (YYYY-MM-DD)',
		'header' => 'Timeslot Date',
		'width' => 1,
		'table' => "judges_timeslots.date",
		'components' => array('teams', 'projects')),

	'rank' => array(
		'name' => 'Project -- Rank (left blank for judges to fill out)',
		'header' => 'Rank',
		'width' => 1.00,
		'table' => '""' ),

	'question_1' => array(
		'name' => 'Judge -- Custom Judge Registration Question 1',
		'header' => 'Q1',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,  /* Only disables in the report editor, a report can still use it */
		'exec_function' => 'report_judges_custom_question'),

	'question_2' => array(
		'name' => 'Judge -- Custom Judge Registration Question 2',
		'header' => 'Q2',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_custom_question'),

	'question_3' => array(
		'name' => 'Judge -- Custom Judge Registration Question 3',
		'header' => 'Q3',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_custom_question'),

	'question_4' => array(
		'name' => 'Judge -- Custom Judge Registration Question 4',
		'header' => 'Q4',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_custom_question'),

	'question_5' => array(
		'name' => 'Judge -- Custom Judge Registration Question 5',
		'header' => 'Q5',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_custom_question'),

	'question_6' => array(
		'name' => 'Judge -- Custom Judge Registration Question 6',
		'header' => 'Q6',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_custom_question'),

	'question_7' => array(
		'name' => 'Judge -- Custom Judge Registration Question 7',
		'header' => 'Q7',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_custom_question'),

	'question_8' => array(
		'name' => 'Judge -- Custom Judge Registration Question 8',
		'header' => 'Q8',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_custom_question'),

	'question_9' => array(
		'name' => 'Judge -- Custom Judge Registration Question 9',
		'header' => 'Q9',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_custom_question'),

	'question_10' => array(
		'name' => 'Judge -- Custom Judge Registration Question 10',
		'header' => 'Q10',
		'width' => 1,
		'table' => 'users.id',
		'editor_disabled' => true,
		'exec_function' => 'report_judges_custom_question'),

	'static_text' =>  array(
		'name' => 'Static Text (useful for labels)',
		'header' => '',
		'width' => 0.1,
		'table' => "CONCAT(' ')"),
);

/* Fill in big expansions */
/* div_exp */
for($x=1;$x<=50;$x++) {
	$f = "div_exp_$x";
	$report_judges_fields["div_exp_$x"] = array(
		'name' => "Judge -- Expertise for Division ID $x",
		'header' => "div$x",
		'width' => 0.5,
		'table' => 'users_judge.div_prefs',
		'editor_disabled' => true,  /* Only disables in the report editor, a report can still use it */
		'exec_function' => 'report_judges_div_exp',
		'components' => array('users_judge'));
}

/* Overwrite the question_1 .. question_10 fields with the 
 * question name and header from the list of questions */
function report_judges_update_questions($year)
{
	global $report_judges_fields;
	$qs = questions_load_questions('judgereg', $year);
	if(count($qs) > 10) {
		echo "Not enough judge question fields, please file a bug report at sfiab.ca and report that you have ".count($qs)." custom judge questions, but the system can handle a maximum of 10.";
		exit;
	}
	foreach($qs as $qid=>$q) {
		$f = "question_{$q['ord']}";
		$report_judges_fields[$f]['header'] = $q['db_heading'];
		$report_judges_fields[$f]['name'] = 'Judge -- Custom Judge Question: '.$q['question'];
		$report_judges_fields[$f]['editor_disabled'] = false;
	}
}

function report_judges_update_divs($year)
{
	global $report_judges_fields, $report_judges_divs;

	report_judges_load_divs($year);

	if(count($report_judges_divs[$year]) > 50) {
		echo "Not enough judge division fields, please file a bug report at sfiab.ca and report that you have ".count($report_judges_divs[$year])." divisions, but the system can handle a maximum of 50.";
		exit;
	}
	foreach($report_judges_divs[$year] as $div_id=>$d) {
		$f = "div_exp_$div_id";
		$report_judges_fields[$f]['header'] = "{$d['division_shortform']} - {$d['division']}";
		$report_judges_fields[$f]['name'] = 'Judge -- Expertise in Division: '.$d['division'];
		$report_judges_fields[$f]['editor_disabled'] = false;
	}
}
function report_judges_update_cats($year)
{
	global $report_judges_fields, $report_judges_cats;

	report_judges_load_cats($year);

	if(count($report_judges_cats[$year]) > 10) {
		echo "Not enough judge age category fields, please file a bug report at sfiab.ca and report that you have ".count($report_judges_cats[$year])." age categories, but the system can handle a maximum of 10.";
		exit;
	}
	if(is_array($report_judges_cats[$year])){
		foreach($report_judges_cats[$year] as $cat_id=>$d) {
			$f = "cat_pref_$cat_id";
			$report_judges_fields[$f]['header'] = "{$d['category_shortform']} - {$d['category']}";
			$report_judges_fields[$f]['name'] = 'Judge -- Preference for Age Category: '.$d['category'];
			$report_judges_fields[$f]['editor_disabled'] = false;
		}
	}
}

$report_judges_questions_updated = false;
/* Do the overwrites for the current year, this is for the editor, because
 * it doesn't call a _fromwhere */
report_judges_update_questions($config['FAIRYEAR']);
report_judges_update_divs($config['FAIRYEAR']);
report_judges_update_cats($config['FAIRYEAR']);

function report_judges_fromwhere($report, $components)
{
 	global $config, $report_judges_fields;

	$year = $report['year'];

	if($report_judges_questions_updated == false) {
		/* Do overwrites for the report year, overwriting the previous
		 * overwrites for the current year, because the report year
		 * could be different, and the questions may have changed */
		report_judges_update_questions($year);
		$report_judges_questions_updated = true;
	}

	if(in_array('users_judge', $components)) {
		$uj_from = 'LEFT JOIN users_judge ON users_judge.users_id=users.id';
	}
					
	$teams_from = '';
	$teams_where = '';
	if(in_array('teams', $components)) {
		$teams_from = "LEFT JOIN judges_teams_link ON judges_teams_link.users_id=users.id
				LEFT JOIN judges_teams ON judges_teams.id=judges_teams_link.judges_teams_id";
		$teams_where = "AND judges_teams_link.year='$year'
				AND judges_teams.year='$year'";
	}

	$projects_from='';
	$projects_where='';
	if(in_array('projects', $components)) {
		$projects_from = "LEFT JOIN judges_teams_timeslots_projects_link ON
					judges_teams_timeslots_projects_link.judges_teams_id=judges_teams.id
				LEFT JOIN projects ON projects.id=judges_teams_timeslots_projects_link.projects_id
				LEFT JOIN judges_timeslots ON judges_timeslots.id=judges_teams_timeslots_projects_link.judges_timeslots_id";
		$projects_where = "AND judges_teams_timeslots_projects_link.year='$year'
				AND projects.year='$year'";
	}

	$students_from='';
	$students_where='';
	if(in_array('students', $components)) {
		$students_from = "LEFT JOIN students ON students.registrations_id=projects.registrations_id";
		$students_where = "AND students.year='$year'";
	}

	/* Search the report for a filter based on judge year */
	$year_where = "AND users.year='$year'";
	foreach($report['filter'] as $d) {
		if($d['field'] == 'year') {
			/* Don't interally filter on year, we'll do it externally */
			$year_where = '';
		}
	}
										
	$q = "	FROM 	users
			$teams_from
			$projects_from
			$students_from
			$uj_from
		WHERE
			users.types LIKE '%judge%'
			$year_where
			$teams_where
			$projects_where
			$students_where 
            AND deleted='no'
		";

	return $q;
}

?>
