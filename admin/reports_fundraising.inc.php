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


/* Take the language array in users_fundraising, unserialize it, and join it
 * with a space */
function report_fundraisings_languages(&$report, $field, $text)
{
		$l = unserialize($text);
		return join(' ', $l);
}



/* Components: */
/* Yes, fundraisings, the generator takes the report type (also the user.type in many cases) and
 * just adds an 's' to find the fields and the functions. */
$report_fundraisings_fields = array(
	'fundraising_campaigns_id' => array(
		'start_option_group' => 'Campaign ID',
		'name' => 'Fundraising Campaign numerical ID',
		'header' => 'ID',
		'width' => 0.25,
		'table' => "fundraising_campaigns.id"),
	
	'salutation' =>  array(
		'start_option_group' => 'Contact Information',
		'name' => 'Contact -- Salutation (Mr., Mrs., etc.)',
		'header' => 'Sal',
		'width' => 0.5,
		'table' => "users.salutation",
		'components' => array('users') ),

	'namefl' =>  array(
		'name' => 'Contact -- Full Name (first last)',
		'header' => 'Name',
		'width' => 1.75,
		'table' => "CONCAT(users.firstname, ' ', users.lastname)",
		'table_sort'=> 'users.lastname',
		'components' => array('users') ),

	'email' =>  array(
		'name' => 'Contact -- Email',
		'header' => 'Email',
		'width' => 2.0,
		'table' => 'users.email',
		'components' => array('users') ),

	'phone_home' => array(
		'name' => 'Contact -- Phone (Home)',
		'header' => 'Phone(Home)',
		'width' => 1,
		'table' => 'users.phonehome',
		'components' => array('users') ),

	'phone_work' => array(
		'name' => 'Contact -- Phone (Work)',
		'header' => 'Phone(Work)',
		'width' => 1.25,
		'table' => "users.phonework",
		'components' => array('users') ),

	'organization' => array(
		'name' => 'Contact -- Organization',
		'header' => 'Organization',
		'width' => 2,
		'table' => 'users.organization',
		'components' => array('users') ),

	'position' => array(
		'name' => 'Contact -- Position',
		'header' => 'Position',
		'width' => 2,
		'table' => 'users_sponsor.position',
		'components' => array('users') ),

	'address' =>  array(
		'start_option_group' => 'Contact Address',
		'name' => 'Contact Address -- Street',
		'header' => 'Address',
		'width' => 2.0,
		'table' => "CONCAT(users.address, ' ', users.address2)",
		'components' => array('users') ),

	'city' =>  array(
		'name' => 'Contact Address -- City',
		'header' => 'City',
		'width' => 1.5,
		'table' => 'users.city',
		'components' => array('users') ),

	'province' =>  array(
		'name' => 'Contact Address -- '.$config['provincestate'],
		'header' => $config['provincestate'],
		'width' => 0.75,
		'table' => 'users.province',
		'components' => array('users') ),

	'postal' =>  array(
		'name' => 'Contact Address -- '.$config['postalzip'],
		'header' => $config['postalzip'],
		'width' => 0.75,
		'table' => 'users.postalcode' ,
		'components' => array('users') ),

       'city_prov' =>  array(
                'name' => 'Contact Address -- City, '.$config['provincestate'].' (for mailing)',
                'header' => 'City',
                'width' => 1.5,
                'table' => "CONCAT(users.city, ', ', users.province)",
		'components' => array('users') ),
	
	'year' =>  array(
		'start_option_group' => 'Miscellaneous',
		'name' => 'Contact -- Year',
		'header' => 'Year',
		'width' => 0.5,
		'table' => 'users.year',
		'components' => array('users') ),

	'user_filter' => array(
		'name' => 'User Filter by MAX(year)',
		'header' => '',
		'width' => 0.1,
		'table' => 'MAX(users.year)',
		'group_by' => array('users.uid'),
		'components' => array('users') ),

	'static_text' =>  array(
		'name' => 'Static Text (useful for labels)',
		'header' => '',
		'width' => 0.1,
		'table' => "CONCAT(' ')"),

);

 function report_fundraisings_fromwhere($report, $components)
 {
 	global $config, $report_fundraisings_fields;
	
	$fields = $report_fundraisings_fields;
	$year = $report['year'];

	if(in_array('users', $components)) {
		$users_from = 'LEFT JOIN fundraising_campaigns_users_link ON fundraising_campaigns.id=fundraising_campaigns_users_link.fundraising_campaigns_id
				LEFT JOIN users ON users.uid=fundraising_campaigns_users_link.users_uid
				LEFT JOIN users_sponsor ON users_sponsor.users_id=users.id';
		$users_where = "users.deleted!='yes' AND";
	}

/*
	$teams_from = '';
	$teams_where = '';
	if(in_array('teams', $components)) {
		$teams_from = "LEFT JOIN fundraisings_teams_link ON judges_teams_link.users_id=users.id
				LEFT JOIN fundraisings_teams ON judges_teams.id=judges_teams_link.judges_teams_id";
		$teams_where = "AND fundraisings_teams_link.year='$year'
				AND fundraisings_teams.year='$year'";
	}

	$projects_from='';
	$projects_where='';
	if(in_array('projects', $components)) {
		$projects_from = "LEFT JOIN fundraisings_teams_timeslots_projects_link ON
					fundraisings_teams_timeslots_projects_link.judges_teams_id=judges_teams.id
				LEFT JOIN projects ON projects.id=fundraisings_teams_timeslots_projects_link.projects_id
				LEFT JOIN fundraisings_timeslots ON judges_timeslots.id=judges_teams_timeslots_projects_link.judges_timeslots_id";
		$projects_where = "AND fundraisings_teams_timeslots_projects_link.year='$year'
				AND projects.year='$year'";
	}
*/

	$q = "  FROM 	fundraising_campaigns
			$users_from
		WHERE
			$users_where
			1
		";

	return $q;
}

?>
