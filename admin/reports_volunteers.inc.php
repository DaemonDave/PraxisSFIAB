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

$report_volunteers_fields = array(

	'last_name' =>  array(
		'name' => 'Volunteer -- Last Name',
		'header' => 'Last Name',
		'width' => 1.0,
		'table' => 'users.lastname' ),

	'first_name' => array(
		'name' => 'Volunteer -- First Name',
		'header' => 'First Name',
		'width' => 1.0,
		'table' => 'users.firstname' ),

	'name' =>  array(
		'name' => 'Volunteer -- Full Name (last, first)',
		'header' => 'Name',
		'width' => 1.75,
		'table' => "CONCAT(users.lastname, ', ', users.firstname)",
		'table_sort'=> 'users.lastname' ),

	'namefl' => array(
		'name' => 'Volunteer -- Full Name (first last)',
		'header' => 'Name',
		'width' => 1.75,
		'table' => "CONCAT(users.firstname, ' ', users.lastname)",
		'table_sort'=> 'users.lastname' ),

	'email' => array(
		'name' => 'Volunteer -- Email',
		'header' => 'Email',
		'width' => 1.75,
		'table' => 'users.email'),

	'phone' => array(
		'name' => 'Volunteer -- Phone',
		'header' => 'Phone',
		'width' => 1.0,
		'table' => 'users.phonehome'),

	'cell' => array(
		'name' => 'Volunteer -- Cell',
		'header' => 'Cell',
		'width' => 1.0,
		'table' => 'users.phonecell'),

	'organization' => array(
		'name' => 'Volunteer -- Phone',
		'header' => 'Organziation',
		'width' => 1.0,
		'table' => 'users.organization'),

	'firstaid' => array(
		'name' => 'Volunteer -- First Aid Training',
		'header' => 'F.Aid',
		'width' => 0.5,
		'table' => 'users.firstaid',
		'value_map' =>array ('no' => 'no', 'yes' => 'YES')),

	'cpr' => array(
		'name' => 'Volunteer -- CPR Training',
		'header' => 'CPR',
		'width' => 0.5,
		'table' => 'users.cpr',
		'value_map' =>array ('no' => 'no', 'yes' => 'YES')),

	'complete' =>  array(
		'name' => 'Volunteer -- Registration Complete',
		'header' => 'Cmpl',
		'width' => 0.4,
		'table' => 'users_volunteer.volunteer_complete',
		'value_map' => array ('no' => 'No', 'yes' => 'Yes'),
		'components' => array('users_volunteer')),

	'position_name' => array (
		'name' => 'Volunteer Position -- Name',
		'header' => 'Position',
		'width' => 3,
		'table' => 'volunteer_positions.name',
		'components' => array('signup')),

	'fair_year' => array (
		'name' => 'Fair -- Year',
		'header' => 'Year',
		'width' => 0.5,
		'table' => "{$config['FAIRYEAR']}"),

	'fair_name' => array (
		'name' => 'Fair -- Name',
		'header' => 'Fair Name',
		'width' => 3,
		'table' => "'".mysql_escape_string($config['fairname'])."'"),

	'static_text' => array (
		'name' => 'Static Text (useful for labels)',
		'header' => '',
		'width' => 0.1,
		'table' => "CONCAT(' ')"),

);

 function report_volunteers_fromwhere($report, $components)
 {
 	global $config, $report_volutneers_fields;
	
	$fields = $report_volutneers_fields;
	$year = $report['year'];

	if(in_array('users_volunteer', $components)) {
		$uv_from = 'LEFT JOIN users_volunteer ON users_volunteer.users_id=users.id';
	}

	$signup_join = '';
	$signup_where = '';

	if(in_array('signup', $components)) {
		$signup_join = "LEFT JOIN volunteer_positions_signup 
					ON (users.id=volunteer_positions_signup.users_id)
				LEFT JOIN volunteer_positions 
					ON (volunteer_positions_signup.volunteer_positions_id=volunteer_positions.id)";
		$signup_where = "AND (volunteer_positions_signup.year = '$year' OR volunteer_positions_signup.year IS NULL)";
	} 

	$q = "	FROM 
			users 
			$signup_join
			$uv_from
		WHERE
			users.types LIKE '%volunteer%'
			AND users.year='$year'
			$signup_where
		";

	return $q;
}


?>
