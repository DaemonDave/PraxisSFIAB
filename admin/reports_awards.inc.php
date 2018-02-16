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

$report_awards_fields = array(
	'name' =>  array(
		'start_option_group' => 'Award Information',
		'name' => 'Award -- Name',
		'header' => 'Award Name',
		'width' => 3.0,
		'table' => 'award_awards.name' ),

	'criteria' =>  array(
		'name' => 'Award -- Criteria',
		'header' => 'Award Criteria',
		'width' => 3.0,
		'table' => 'award_awards.criteria' ),

	'presenter' =>  array(
		'name' => 'Award -- Presenter',
		'header' => 'Award Presenter',
		'width' => 1.5,
		'table' => 'award_awards.presenter' ),

	'order' =>  array(
		'name' => 'Award -- Order',
		'header' => 'Award Order',
		'width' => 0.5,
		'table' => 'award_awards.order' ),

	'cwsfaward' =>  array(
		'name' => 'Award -- CWSF Award',
		'header' => 'CWSF',
		'width' => 0.5,
		'table' => 'award_awards.cwsfaward',
		'value_map' => array ('0' => 'No', '1' => 'Yes')),

	'type' => array(
		'name' => 'Award -- Type',
		'header' => 'Award Type',
		'width' => 1.0,
		'table' => 'award_types.type' ),

	'empty_winner_box' => array(
		'name' => 'Award -- Empty Winner Box (for hand entry on printed reports)',
		'header' => 'Winner',
		'width' => 1.0,
		'table' => "CONCAT('')" ),

	'sponsor_organization' => array(
		'start_option_group' => 'Sponsor Information',
		'name' => 'Sponsor -- Organization',
		'header' => 'Sponsor Organization',
		'width' => 2.0,
		'table' => 'sponsors.organization' ),

	'sponsor_phone' => array(
		'name' => 'Sponsor -- Phone',
		'header' => 'Sp. Phone',
		'width' => 1,
		'table' => 'sponsors.phone' ),

	'sponsor_fax' => array(
		'name' => 'Sponsor -- Fax',
		'header' => 'Sp. Fax',
		'width' => 1,
		'table' => 'sponsors.fax' ),

	'sponsor_address' =>  array(
		'name' => 'Sponsor -- Street Address',
		'header' => 'Sponsor Address',
		'width' => 2.0,
		'table' => 'sponsors.address'),

	'sponsor_city' =>  array(
		'name' => 'Sponsor -- City',
		'header' => 'Sp. City',
		'width' => 1.5,
		'table' => 'sponsors.city' ),

	'sponsor_province' =>  array(
		'name' => 'Sponsor -- '.$config['provincestate'],
		'header' => 'Sp. '.$config['provincestate'],
		'width' => 0.75,
		'table' => 'sponsors.province_code' ),

	'sponsor_postal' =>  array(
		'name' => 'Sponsor -- '.$config['postalzip'],
		'header' => 'Sp. '.$config['postalzip'],
		'width' => 0.75,
		'table' => 'sponsors.postalcode' ),

	'sponsor_notes' =>  array(
		'name' => 'Sponsor -- Notes',
		'header' => 'Sponsor Notes',
		'width' => 3,
		'table' => 'sponsors.notes' ),

	'sponsorship_status' =>  array(
		'name' => 'Sponsorship -- Status',
		'header' => 'Sp. Status',
		'width' => .5,
		'table' => 'sponsorships.status',
		'value_map' => array ('pending' => 'Pending', 'confirmed' => 'Confirmed'), "received"=>"Received"),

	'pcontact_salutation' =>  array(
		'start_option_group' => 'Sponsor Primary Contact',
		'name' => 'Primary Contact -- Salutation',
		'header' => 'Cnct. Salutation',
		'width' => 1.0,
		'table' => 'PRIMARYCONTACTUSER.salutation' ),

	'pcontact_last_name' =>  array(
		'name' => 'Primary Contact -- Last Name',
		'header' => 'Cnct. Last Name',
		'width' => 1.0,
		'table' => 'PRIMARYCONTACTUSER.lastname' ),

	'pcontact_first_name' => array(
		'name' => 'Primary Contact -- First Name',
		'header' => 'Cnct. First Name',
		'width' => 1.0,
		'table' => 'PRIMARYCONTACTUSER.firstname' ),

	'pcontact_name' =>  array(
		'name' => 'Primary Contact -- Full Name (last, first)',
		'header' => 'Contact Name',
		'width' => 1.75,
		'table' => "CONCAT(PRIMARYCONTACTUSER.lastname, ', ', PRIMARYCONTACTUSER.firstname)",
		'table_sort'=> 'PRIMARYCONTACTUSER.lastname' ),
		
	'pcontact_namefl' =>  array(
		'name' => 'Primary Contact -- Full Name (salutation first last)',
		'header' => 'Contact Name',
		'width' => 1.75,
		'table' => "CONCAT(PRIMARYCONTACTUSER.salutation, ' ', PRIMARYCONTACTUSER.firstname, ' ', PRIMARYCONTACTUSER.lastname)",
		'table_sort'=> 'PRIMARYCONTACTUSER.lastname' ),

	'pcontact_position' =>  array(
		'name' => 'Primary Contact -- Position',
		'header' => 'Cnct. Position',
		'width' => 1.25,
		'table' => 'PRIMARYCONTACT.position'),

	'pcontact_email' =>  array(
		'name' => 'Primary Contact -- Email',
		'header' => 'Cnct. Email',
		'width' => 2.0,
		'table' => 'PRIMARYCONTACTUSER.email'),

	'pcontact_hphone' => array(
		'name' => 'Primary Contact -- Home Phone',
		'header' => 'Cnct. Home',
		'width' => 1,
		'table' => 'PRIMARYCONTACTUSER.phonehome' ),

	'pcontact_wphone' => array(
		'name' => 'Primary Contact -- Work Phone',
		'header' => 'Cnct. Work',
		'width' => 1,
		'table' => 'PRIMARYCONTACTUSER.phonework' ),

	'pcontact_cphone' => array(
		'name' => 'Primary Contact -- Cell Phone',
		'header' => 'Cnct. Cell',
		'width' => 1,
		'table' => 'PRIMARYCONTACTUSER.phonecell' ),

	'pcontact_fax' => array(
		'name' => 'Primary Contact -- Fax',
		'header' => 'Cnct. Fax',
		'width' => 1,
		'table' => 'PRIMARYCONTACTUSER.fax' ),
		
	'pcontact_notes' =>  array(
		'name' => 'Primary Contact -- Notes',
		'header' => 'Contact Notes',
		'width' => 3,
		'table' => 'PRIMARYCONTACT.notes' ),

	'pcontact_address' =>  array(
		'name' => 'Primary Contact Address -- Street',
		'header' => 'Address',
		'width' => 2.0,
		'table' => "CONCAT(PRIMARYCONTACTUSER.address, ' ', PRIMARYCONTACTUSER.address2)"),

	'pcontact_city' =>  array(
		'name' => 'Primary Contact Address -- City',
		'header' => 'City',
		'width' => 1.5,
		'table' => 'PRIMARYCONTACTUSER.city'),

	'pcontact_province' =>  array(
		'name' => 'Primary Contact Address -- '.$config['provincestate'],
		'header' => $config['provincestate'],
		'width' => 0.75,
		'table' => 'PRIMARYCONTACTUSER.province'),

	'pcontact_postal' =>  array(
		'name' => 'Primary Contact Address -- '.$config['postalzip'],
		'header' => $config['postalzip'],
		'width' => 0.75,
		'table' => 'PRIMARYCONTACTUSER.postalcode' ),

 	'pcontact_city_prov' =>  array(
		'name' => 'Primary Contact Address -- City, '.$config['provincestate'].' (for mailing)',
		'header' => 'City',
		'width' => 1.5,
		'table' => "CONCAT(PRIMARYCONTACTUSER.city, ', ', PRIMARYCONTACTUSER.province)"),

	'judgeteamname' => array(
		'start_option_group' => 'Judging Team',
		'components' => array('judgingteam'),
		'name' => 'Judging Team -- Name',
		'header' => 'Judging Team',
		'width' => 3.0,
		'table' => 'judges_teams.name'),

	'judgeteamnum' => array(
		'components' => array('judgingteam'),
		'name' => 'Judging Team -- Number',
		'header' => 'Team',
		'width' => 0.5,
		'table' => 'judges_teams.num'),

	'judgeteammembers_name' => array(
		'components' => array('judgingteam', 'judgingteammembers'),
		'name' => 'Judging Team -- Judge Name',
		'header' => 'Judge Name',
		'width' => 1.5,
		'table' => "CONCAT(judges.firstname, ' ', judges.lastname)"),

	'judgeteammembers' => array(
		'components' => array('judgingteam', 'judgingteammembers'),
		'name' => 'Judging Team -- Members (REQUIRES MySQL 5.0)',
		'header' => 'Team Members',
		'width' => 3.0,
		'table' => "GROUP_CONCAT(judges.firstname, ' ', judges.lastname ORDER BY judges.lastname SEPARATOR ', ')",
		'group_by' => array('award_awards.id', 'judges_teams.num') ),

	'prize_name' => array(
		'start_option_group' => 'Prize Info (Duplicates award data for each prize, omits awards with no prizes)',
		'name' => 'Prize -- Name',
		'header' => 'Prize Name',
		'width' => 2,
		'table' => 'award_prizes.prize',
		'components' => array('prizes')),

	'prize_cash' => array(
		'name' => 'Prize --  Cash Amount',
		'header' => 'Cash',
		'width' => 0.5,
		'table' => 'award_prizes.cash',
		'components' => array('prizes')),

	'prize_scholarship' => array(
		'name' => 'Prize --  Scholarship Amount',
		'header' => 'Scholarship',
		'width' => 0.75,
		'table' => 'award_prizes.scholarship',
		'components' => array('prizes')),

	'prize_value' => array(
		'name' => 'Prize --  Value Amount',
		'header' => 'Value',
		'width' => 0.5,
		'table' => 'award_prizes.value',
		'components' => array('prizes')),

/* Don't have projectcategories and projectdivisions
	'prize_fullname' => array(
		'name' => 'Prize --  Name, Category, Division',
		'header' => 'Prize Name',
		'width' => 4,
		'table' => "CONCAT(award_prizes.prize,' in ',projectcategories.category,' ', projectdivisions.division)",
		'table_sort' => 'award_prizes.order',
		'components' => array('prizes')),
*/
	'prize_trophy_any' => array(
		'name' => 'Prize -- Trophy (\'Yes\' if the award has a trophy)',
		'header' => 'Trophy',
		'width' => 0.5,
		'table' => "IF ( award_prizes.trophystudentkeeper=1
				OR award_prizes.trophystudentreturn=1
				OR award_prizes.trophyschoolkeeper=1
				OR award_prizes.trophyschoolreturn=1, 'Yes', 'No')",
		'components' => array('prizes')),

	'prize_trophy_return' => array(
		'name' => 'Prize -- Annual Trophy (\'Yes\' if the award has a school or student trophy that isn\'t a keeper)',
		'header' => 'Trophy',
		'width' => 0.5,
		'table' => "IF ( award_prizes.trophystudentreturn=1
				OR award_prizes.trophyschoolreturn=1, 'Yes', 'No')",
		'components' => array('prizes')),

	'prize_trophy_return_student' => array(
		'name' => 'Prize -- Annual Student Trophy (\'Yes\' if the award has astudent trophy that isn\'t a keeper)',
		'header' => 'Ind.',
		'width' => 0.5,
		'table' => "IF ( award_prizes.trophystudentreturn=1, 'Yes', 'No')",
		'components' => array('prizes')),

	'prize_trophy_return_school' => array(
		'name' => 'Prize -- Annual School Trophy (\'Yes\' if the award has a school trophy that isn\'t a keeper)',
		'header' => 'Sch.',
		'width' => 0.5,
		'table' => "IF ( award_prizes.trophyschoolreturn=1, 'Yes', 'No')",
		'components' => array('prizes')),

	'prize_all' => array(
		'name' => 'Prize -- Lists all prize data (name, cash, scholarship, value, trophies)',
		'header' => 'Prize',
		'width' => 2,
		'table' => "CONCAT(
				IF(award_prizes.prize != '', CONCAT(award_prizes.prize,'\n', ''),''),
				IF(award_prizes.cash != '', CONCAT('$',award_prizes.cash,'\n'), ''),
				IF(award_prizes.scholarship != '', CONCAT('$',award_prizes.scholarship,' scholarship\n'), ''),
				IF(award_prizes.value != '', CONCAT('$',award_prizes.value,' value\n'), ''),
				IF(award_prizes.trophystudentkeeper != '', CONCAT('Student Keeper Trophy\n'), ''),
				IF(award_prizes.trophystudentreturn != '', CONCAT('Student Annual-Return Trophy\n'), ''),
				IF(award_prizes.trophyschoolkeeper != '', CONCAT('School Keeper Trophy\n'), ''),
				IF(award_prizes.trophyschoolreturn != '', CONCAT('School Annual-Return Trophy\n'), '')
				)",
		'components' => array('prizes')),


);

 function report_awards_fromwhere($report, $components)
 {
 	global $config, $report_awards_fields;
	
	$fields = $report_awards_fields;
	$year = $report['year'];

	$judges_join = '';
	$judges_where = '';
	if(in_array('judgingteam', $components)) {
	   	$judges_join = 'LEFT JOIN judges_teams_awards_link ON judges_teams_awards_link.award_awards_id=award_awards.id
				LEFT JOIN judges_teams ON judges_teams.id=judges_teams_awards_link.judges_teams_id';
		$judges_where = "AND judges_teams_awards_link.year='$year'
				AND judges_teams.year='$year'";
	}

	$judges_members_join = '';
	$judges_members_where = '';
	if(in_array('judgingteammembers', $components)) {
		$judges_members_join = 'LEFT JOIN judges_teams_link ON judges_teams_link.judges_teams_id=judges_teams.id
				LEFT JOIN judges ON judges.id=judges_teams_link.judges_id';

		$judges_members_where = "AND judges_teams_link.year='$year'";
	}

	$prizes_join = '';
	if(in_array('prizes', $components)) {
		$prizes_join = 'LEFT JOIN award_prizes ON award_prizes.award_awards_id=award_awards.id';
		/* Don't need a where filter, the prize is attached by unique ID to an award
		 * that is already from the correct year. */
	}


	$q = "	FROM  award_awards 
			LEFT JOIN sponsors ON (
				sponsors.id=award_awards.sponsors_id)
			LEFT JOIN award_types ON award_types.id=award_types_id
			LEFT JOIN users_sponsor AS PRIMARYCONTACT ON (
					PRIMARYCONTACT.sponsors_id=sponsors.id
					AND PRIMARYCONTACT.`primary`='yes')
			LEFT JOIN users AS PRIMARYCONTACTUSER ON (
				PRIMARYCONTACT.users_id=PRIMARYCONTACTUSER.id)
			$judges_join
			$judges_members_join
			$prizes_join
		WHERE
			award_awards.year='$year'
			AND award_types.year='$year'
			$judges_where 
			$judges_members_where
		";

	return $q;
}

?>
