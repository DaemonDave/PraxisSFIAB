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


function reports_schools_principal(&$report, $field, $text)
{
	$year = $report['year'];
	if($text > 0) { /* text is the uid */
		$u = user_load_by_uid_year($text, $year);
		return $u['name'];
	}
	return '';
}
function reports_schools_sciencehead(&$report, $field, $text)
{
	$year = $report['year'];
	if($text > 0) { /* text is the uid */
		$u = user_load_by_uid_year($text, $year);
		return $u['name'];
	}
	return '';
}

function reports_schools_shphone(&$report, $field, $text)
{
	$year = $report['year'];
	if($text > 0) { /* text is the uid */
		$u = user_load_by_uid_year($text, $year);
		return $u['phonework'];
	}
	return '';
}

function reports_schools_shemail(&$report, $field, $text)
{
	$year = $report['year'];
	if($text > 0) { /* text is the uid */
		$u = user_load_by_uid_year($text, $year);
		return $u['email'];
	}
	return '';
}

$report_schools_fields = array(
	'school' =>  array(
		'name' => 'School -- Name',
		'header' => 'School Name',
		'width' => 2.25,
		'table' => 'schools.school' ),

	'schooladdr' => array(
		'name' => 'School -- Full Address',
		'header' => 'School Address',
		'width' => 3.0,
		'table' => "CONCAT(schools.address, ', ', schools.city, ', ', schools.province_code, ', ', schools.postalcode)" ),

	'school_phone' => array(
		'name' => 'School -- Phone',
		'header' => 'School Phone',
		'width' => 1,
		'table' => 'schools.phone' ),

	'school_fax' => array(
		'name' => 'School -- Fax',
		'header' => 'School Fax',
		'width' => 1,
		'table' => 'schools.fax' ),

	'school_email' => array(
		'name' => 'School -- Email',
		'header' => 'School Email',
		'width' => 1,
		'table' => 'schools.schoolemail' ),

	'school_address' =>  array(
		'name' => 'School Address -- Street Address',
		'header' => 'Address',
		'width' => 2.0,
		'table' => 'schools.address'),

	'school_city' =>  array(
		'name' => 'School Address -- City',
		'header' => 'City',
		'width' => 1.5,
		'table' => 'schools.city' ),

	'school_province' =>  array(
		'name' => 'School Address -- '.$config['provincestate'],
		'header' => $config['provincestate'],
		'width' => 0.75,
		'table' => 'schools.province_code' ),

	'school_city_prov' =>  array(
		'name' => 'School Address -- City, '.$config['provincestate'].' (for mailing)',
		'header' => 'City',
		'width' => 1.5,
		'table' => "CONCAT(schools.city, ', ', schools.province_code)" ),

	'school_postal' =>  array(
		'name' => 'School Address -- '.$config['postalzip'],
		'header' => $config['postalzip'],
		'width' => 0.75,
		'table' => 'schools.postalcode' ),

	'school_lang' =>  array(
		'name' => 'School -- Language Code',
		'header' => 'Lang',
		'width' => 0.5,
		'table' => 'schools.schoollang' ),

	'school_level' =>  array(
		'name' => 'School -- Grade Levels',
		'header' => 'Level',
		'width' => 1.0,
		'table' => 'schools.schoollevel' ),

	'school_board' =>  array(
		'name' => 'School -- Board',
		'header' => 'Board',
		'width' => 1.0,
		'table' => 'schools.board' ),

	'school_district' =>  array(
		'name' => 'School -- District',
		'header' => 'District',
		'width' => 1.0,
		'table' => 'schools.district' ),

	'school_principal' => array(
		'name' => 'School -- Principal',
		'header' => 'Principal',
		'width' => 1.25,
		'table' => 'schools.principal_uid',
		'exec_function' => 'reports_schools_principal'),

	'school_sh' => array(
		'name' => 'School -- Science Head',
		'header' => 'Science Head',
		'width' => 1.25,
		'table' => 'schools.sciencehead_uid',
		'exec_function' => 'reports_schools_sciencehead'),

	'school_shphone' => array(
		'name' => 'School -- Science Head Phone',
		'header' => 'Science Hd Phone',
		'width' => 1,
		'table' => 'schools.sciencehead_uid',
		'exec_function' => 'reports_schools_shphone'),

	'school_shemail' => array(
		'name' => 'School -- Science Head Email',
		'header' => 'Science Head Email',
		'width' => 1.5,
		'table' => 'schools.sciencehead_uid',
		'exec_function' => 'reports_schools_shemail'),

	'school_accesscode' => array(
		'name' => 'School -- Access Code',
		'header' => 'Access Code',
		'width' => 1.1,
		'table' => 'schools.accesscode' ),

	'school_registration_password' => array(
		'name' => 'School -- Registration Password',
		'header' => 'Reg Pass',
		'width' => 0.75,
		'table' => 'schools.registration_password' ),

	'school_project_limit' => array(
		'name' => 'School -- Project Limit',
		'header' => 'Limit',
		'width' => 0.75,
		'table' => 'schools.projectlimit' ),

	'school_project_limit_per' => array(
		'name' => 'School -- Project Limit Per',
		'header' => 'Limit Per',
		'width' => 1.0,
		'table' => 'schools.projectlimitper' ),

);

 function report_schools_fromwhere($report, $components)
 {
 	global $config, $report_schools_fields;
	
	$fields = $report_schools_fields;
	$year = $report['year'];

	$q = "	FROM
			schools
		WHERE
			schools.year='$year'
		";

	return $q;
}

?>
