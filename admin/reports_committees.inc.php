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

$report_committees_fields = array(
	'name' =>  array(
		'name' => 'Committee Member -- Full Name ',
		'header' => 'Name',
		'width' => 1.75,
		'table' => "CONCAT(users.firstname, ' ', users.lastname)",
		'table_sort' => 'users.lastname'),

	'last_name' =>  array(
		'name' => 'Committee Member -- Last Name',
		'header' => 'Last Name',
		'width' => 1.0,
		'table' => 'users.lastname' ),

	'first_name' => array(
		'name' => 'Committee Member -- First Name',
		'header' => 'First Name',
		'width' => 1.0,
		'table' => 'users.firstname' ),

	'email' =>  array(
		'name' => 'Committee Member -- Email',
		'header' => 'Email',
		'width' => 2.0,
		'table' => 'users.email'),

	'phone_home' => array(
		'name' => 'Committee Member -- Phone (Home)',
		'header' => 'Phone(Home)',
		'width' => 1,
		'table' => 'users.phonehome'),

	'phone_work' => array(
		'name' => 'Committee Member -- Phone (Work)',
		'header' => 'Phone(Work)',
		'width' => 1.25,
		'table' => 'users.phonework'),
		
	'phone_cel' => array(
		'name' => 'Committee Member -- Phone (Cel)',
		'header' => 'Phone(Cel)',
		'width' => 1,
		'table' => 'users.phonecell'),

	'address' =>  array(
		'name' => 'Committee Member -- Address Street',
		'header' => 'Address',
		'width' => 2.0,
		'table' => "CONCAT(users.address, ' ', users.address2)"),

	'city' =>  array(
		'name' => 'Committee Member -- Address City',
		'header' => 'City',
		'width' => 1.5,
		'table' => 'users.city' ),

	'province' =>  array(
		'name' => 'Committee Member -- Address '.$config['provincestate'],
		'header' => $config['provincestate'],
		'width' => 0.75,
		'table' => 'users.province' ),

	'postal' =>  array(
		'name' => 'Committee Member -- Address '.$config['postalzip'],
		'header' => $config['postalzip'],
		'width' => 0.75,
		'table' => 'users.postalcode' ),

	'organization' => array(
		'name' => 'Committee Member -- Organization',
		'header' => 'Organization',
		'width' => 2,
		'table' => 'users.organization'),

	'firstaid' => array(
		'name' => 'Committee Member -- First Aid Training',
		'header' => 'F.Aid',
		'width' => 0.5,
		'table' => 'users.firstaid',
		'value_map' =>array ('no' => 'no', 'yes' => 'YES')),

	'cpr' => array(
		'name' => 'Committee Member -- CPR Training',
		'header' => 'CPR',
		'width' => 0.5,
		'table' => 'users.cpr',
		'value_map' =>array ('no' => 'no', 'yes' => 'YES')),
		
	'static_text' =>  array(
		'name' => 'Static Text (useful for labels)',
		'header' => '',
		'width' => 0.1,
		'table' => "CONCAT(' ')"),
);

 function report_committees_fromwhere($report, $components)
 {
 	global $config, $report_committees_fields;
	
	$fields = $report_committees_fields;
	$year = $report['year'];

/*
	$teams_from = '';
	$teams_where = '';
	if(in_array('teams', $components)) {
		$teams_from = ",committees_teams_link, committees_teams";
		$teams_where = "AND committees_teams_link.committees_id=users.id
                	        AND committees_teams_link.year='$year'
                        	AND committees_teams.id=committees_teams_link.committees_teams_id
				AND committees_teams.year='$year'";
	}
*/										
	$q = "	FROM 
			users
		WHERE
			users.types LIKE '%committee%'
		";

	return $q;
}

?>
