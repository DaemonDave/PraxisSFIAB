<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 David Grant <dave@lightbox.org>

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

$report_fairs_fields = array(

	'fair_name' =>  array(
		'name' => 'Fair -- Fair Name',
		'header' => 'Name',
		'width' => 1.5,
		'table' => 'fairs.name'),

	'fairstats_year' => array (
		'name' => 'Fair Stats -- Year',
		'header' => 'Year',
		'width' => 1,
		'table' => 'fairs_stats.year',
		'components' => array('fairs_stats')),

	'fairstats_year' => array (
		'name' => 'Fair Stats -- Year',
		'header' => 'Year',
		'width' => 1,
		'table' => 'fairs_stats.year',
		'components' => array('fairs_stats')),

	'fairstats_start_date' => array (
		'name' => 'Fair Stats -- Fair Start',
		'header' => 'Fair Start',
		'width' => 1,
		'table' => 'fairs_stats.start_date',
		'components' => array('fairs_stats')), 

	'fairstats_end_date' => array (
		'name' => 'Fair Stats -- Fair End',
		'header' => 'Fair End',
		'width' => 1,
		'table' => 'fairs_stats.end_date',
		'components' => array('fairs_stats')), 

	'fairstats_budget' => array (
		'name' => 'Fair Stats -- Budget',
		'header' => 'Budget',
		'width' => 1,
		'table' => 'fairs_stats.budget',
		'components' => array('fairs_stats')), 

	'fairstats_address' => array (
		'name' => 'Fair Stats -- Fair Location',
		'header' => 'Fair Location',
		'width' => 1,
		'table' => 'fairs_stats.address',
		'components' => array('fairs_stats')), 
	'fairstats_ysf_affiliation_complete' => array (
		'name' => 'Fair Stats -- YSC Affilitation Complete',
		'header' => 'YSC Affilitation Complete',
		'width' => 1,
		'table' => 'fairs_stats.ysf_affiliation_complete',
		'components' => array('fairs_stats')), 
	'fairstats_charity' => array (
		'name' => 'Fair Stats -- Charity Number/Info',
		'header' => 'Charity Number/Info',
		'width' => 1,
		'table' => 'fairs_stats.charity',
		'components' => array('fairs_stats')), 
	'fairstats_scholarships' => array (
		'name' => 'Fair Stats -- Scholarship Info',
		'header' => 'Scholarship Info',
		'width' => 1,
		'table' => 'fairs_stats.scholarships',
		'components' => array('fairs_stats')), 
	'fairstats_male_1' => array (
		'name' => 'Fair Stats -- Males Grade 1-3',
		'header' => 'Males Grade 1-3',
		'width' => 1,
		'table' => 'fairs_stats.male_1',
		'components' => array('fairs_stats')), 
	'fairstats_male_4' => array (
		'name' => 'Fair Stats -- Males Grade 4-6',
		'header' => 'Males Grade 4-6',
		'width' => 1,
		'table' => 'fairs_stats.male_4',
		'components' => array('fairs_stats')), 
	'fairstats_male_7' => array (
		'name' => 'Fair Stats -- Males Grade 7-8',
		'header' => 'Males Grade 7-8',
		'width' => 1,
		'table' => 'fairs_stats.male_7',
		'components' => array('fairs_stats')), 
	'fairstats_male_9' => array (
		'name' => 'Fair Stats -- Males Grade 9-10',
		'header' => 'Males Grade 9-10',
		'width' => 1,
		'table' => 'fairs_stats.male_9',
		'components' => array('fairs_stats')), 
	'fairstats_male_11' => array (
		'name' => 'Fair Stats -- Males Grade 11-12',
		'header' => 'Males Grade 11-12',
		'width' => 1,
		'table' => 'fairs_stats.male_11',
		'components' => array('fairs_stats')), 
	'fairstats_female_1' => array (
		'name' => 'Fair Stats -- Females Grade 1-3',
		'header' => 'Females Grade 1-3',
		'width' => 1,
		'table' => 'fairs_stats.female_1',
		'components' => array('fairs_stats')), 
	'fairstats_female_4' => array (
		'name' => 'Fair Stats -- Females Grade 4-6',
		'header' => 'Females Grade 4-6',
		'width' => 1,
		'table' => 'fairs_stats.female_4',
		'components' => array('fairs_stats')), 
	'fairstats_female_7' => array (
		'name' => 'Fair Stats -- Females Grade 7-8',
		'header' => 'Females Grade 7-8',
		'width' => 1,
		'table' => 'fairs_stats.female_7',
		'components' => array('fairs_stats')), 
	'fairstats_female_9' => array (
		'name' => 'Fair Stats -- Females Grade 9-10',
		'header' => 'Females Grade 9-10',
		'width' => 1,
		'table' => 'fairs_stats.female_9',
		'components' => array('fairs_stats')), 
	'fairstats_female_11' => array (
		'name' => 'Fair Stats -- Females Grade 11-12',
		'header' => 'Females Grade 11-12',
		'width' => 1,
		'table' => 'fairs_stats.female_11',
		'components' => array('fairs_stats')), 
	'fairstats_projects_1' => array (
		'name' => 'Fair Stats -- Projects Grade 1-3',
		'header' => 'Projects Grade 1-3',
		'width' => 1,
		'table' => 'fairs_stats.projects_1',
		'components' => array('fairs_stats')), 
	'fairstats_projects_4' => array (
		'name' => 'Fair Stats -- Projects Grade 4-6',
		'header' => 'Projects Grade 4-6',
		'width' => 1,
		'table' => 'fairs_stats.projects_4',
		'components' => array('fairs_stats')), 
	'fairstats_projects_7' => array (
		'name' => 'Fair Stats -- Projects Grade 7-8',
		'header' => 'Projects Grade 7-8',
		'width' => 1,
		'table' => 'fairs_stats.projects_7',
		'components' => array('fairs_stats')), 
	'fairstats_projects_9' => array (
		'name' => 'Fair Stats -- Projects Grade 9-10',
		'header' => 'Projects Grade 9-10',
		'width' => 1,
		'table' => 'fairs_stats.projects_9',
		'components' => array('fairs_stats')), 
	'fairstats_projects_11' => array (
		'name' => 'Fair Stats -- Projects Grade 11-12',
		'header' => 'Projects Grade 11-12',
		'width' => 1,
		'table' => 'fairs_stats.projects_11',
		'components' => array('fairs_stats')), 
	'fairstats_firstnations' => array (
		'name' => 'Fair Stats -- First Nations Students',
		'header' => 'First Nations Students',
		'width' => 1,
		'table' => 'fairs_stats.firstnations',
		'components' => array('fairs_stats')), 
	'fairstats_students_atrisk' => array (
		'name' => 'Fair Stats -- Inner City Students',
		'header' => 'Inner City Students',
		'width' => 1,
		'table' => 'fairs_stats.students_atrisk',
		'components' => array('fairs_stats')), 
	'fairstats_schools_atrisk' => array (
		'name' => 'Fair Stats -- Inner City Schools',
		'header' => 'Inner City Schools',
		'width' => 1,
		'table' => 'fairs_stats.schools_atrisk',
		'components' => array('fairs_stats')), 
	'fairstats_students_total' => array (
		'name' => 'Fair Stats -- Total Participants',
		'header' => 'Total Participants',
		'width' => 1,
		'table' => 'fairs_stats.students_total',
		'components' => array('fairs_stats')), 
	'fairstats_schools_total' => array (
		'name' => 'Fair Stats -- Total Schools',
		'header' => 'Total Schools',
		'width' => 1,
		'table' => 'fairs_stats.schools_total',
		'components' => array('fairs_stats')), 
	'fairstats_schools_active' => array (
		'name' => 'Fair Stats -- Active Schools',
		'header' => 'Active Schools',
		'width' => 1,
		'table' => 'fairs_stats.schools_active',
		'components' => array('fairs_stats')), 
	'fairstats_students_public' => array (
		'name' => 'Fair Stats -- Participants from Public',
		'header' => 'Participants from Public',
		'width' => 1,
		'table' => 'fairs_stats.students_public',
		'components' => array('fairs_stats')), 
	'fairstats_schools_public' => array (
		'name' => 'Fair Stats -- Public Schools',
		'header' => 'Public Schools',
		'width' => 1,
		'table' => 'fairs_stats.schools_public',
		'components' => array('fairs_stats')), 
	'fairstats_students_private' => array (
		'name' => 'Fair Stats -- Participants from Independent',
		'header' => 'Participants from Independent',
		'width' => 1,
		'table' => 'fairs_stats.students_private',
		'components' => array('fairs_stats')), 
	'fairstats_schools_private' => array (
		'name' => 'Fair Stats -- Independent Schools',
		'header' => 'Independent Schools',
		'width' => 1,
		'table' => 'fairs_stats.schools_private',
		'components' => array('fairs_stats')), 
	'fairstats_schools_districts' => array (
		'name' => 'Fair Stats -- School Districts',
		'header' => 'School Districts',
		'width' => 1,
		'table' => 'fairs_stats.schools_districts',
		'components' => array('fairs_stats')), 
	'fairstats_studentsvisiting' => array (
		'name' => 'Fair Stats -- Students Visiting',
		'header' => 'Students Visiting',
		'width' => 1,
		'table' => 'fairs_stats.studentsvisiting',
		'components' => array('fairs_stats')), 
	'fairstats_publicvisiting' => array (
		'name' => 'Fair Stats -- Public Guests Visting',
		'header' => 'Public Guests Visting',
		'width' => 1,
		'table' => 'fairs_stats.publicvisiting',
		'components' => array('fairs_stats')), 
	'fairstats_teacherssupporting' => array (
		'name' => 'Fair Stats -- Teachers Supporting Projects',
		'header' => 'Teachers Supporting Projects',
		'width' => 1,
		'table' => 'fairs_stats.teacherssupporting',
		'components' => array('fairs_stats')), 
	'fairstats_increasedinterest' => array (
		'name' => 'Fair Stats -- Students Increased Interest in Science',
		'header' => 'Students Increased Interest in Science',
		'width' => 1,
		'table' => 'fairs_stats.increasedinterest',
		'components' => array('fairs_stats')), 
	'fairstats_consideringcareer' => array (
		'name' => 'Fair Stats -- Students Considering Career in Science',
		'header' => 'Students Considering Career in Science',
		'width' => 1,
		'table' => 'fairs_stats.consideringcareer',
		'components' => array('fairs_stats')), 
	'fairstats_committee_members' => array (
		'name' => 'Fair Stats -- Committee Members',
		'header' => 'Committee Members',
		'width' => 1,
		'table' => 'fairs_stats.committee_members',
		'components' => array('fairs_stats')), 
	'fairstats_judges' => array (
		'name' => 'Fair Stats -- Judges',
		'header' => 'Judges',
		'width' => 1,
		'table' => 'fairs_stats.judges',
		'components' => array('fairs_stats')), 
	'fairstats_next_chair_name' => array (
		'name' => 'Fair Stats -- Regional Chairperson Name',
		'header' => 'Regional Chairperson Name',
		'width' => 1,
		'table' => 'fairs_stats.next_chair_name',
		'components' => array('fairs_stats')), 
	'fairstats_next_chair_email' => array (
		'name' => 'Fair Stats -- Email',
		'header' => 'Email',
		'width' => 1,
		'table' => 'fairs_stats.next_chair_email',
		'components' => array('fairs_stats')), 
	'fairstats_next_chair_hphone' => array (
		'name' => 'Fair Stats -- Home Phone',
		'header' => 'Home Phone',
		'width' => 1,
		'table' => 'fairs_stats.next_chair_hphone',
		'components' => array('fairs_stats')), 
	'fairstats_next_chair_bphone' => array (
		'name' => 'Fair Stats -- Business Phone',
		'header' => 'Business Phone',
		'width' => 1,
		'table' => 'fairs_stats.next_chair_bphone',
		'components' => array('fairs_stats')), 
	'fairstats_next_chair_fax' => array (
		'name' => 'Fair Stats -- Fax',
		'header' => 'Fax',
		'width' => 1,
		'table' => 'fairs_stats.next_chair_fax',
		'components' => array('fairs_stats')), 
	'fairstats_delegate1' => array (
		'name' => 'Fair Stats -- Delegate 1',
		'header' => 'Delegate 1',
		'width' => 1,
		'table' => 'fairs_stats.delegate1',
		'components' => array('fairs_stats')), 
	'fairstats_delegate2' => array (
		'name' => 'Fair Stats -- Delegate 2',
		'header' => 'Delegate 2',
		'width' => 1,
		'table' => 'fairs_stats.delegate2',
		'components' => array('fairs_stats')), 
	'fairstats_delegate3' => array (
		'name' => 'Fair Stats -- Delegate 3',
		'header' => 'Delegate 3',
		'width' => 1,
		'table' => 'fairs_stats.delegate3',
		'components' => array('fairs_stats')), 
	'fairstats_delegate4' => array (
		'name' => 'Fair Stats -- Delegate 4',
		'header' => 'Delegate 4',
		'width' => 1,
		'table' => 'fairs_stats.delegate4',
		'components' => array('fairs_stats')), 
	'fairstats_delegate1_email' => array (
		'name' => 'Fair Stats -- Delegate 1 Email',
		'header' => 'Delegate 1 Email',
		'width' => 1,
		'table' => 'fairs_stats.delegate1_email',
		'components' => array('fairs_stats')), 
	'fairstats_delegate2_email' => array (
		'name' => 'Fair Stats -- Delegate 2 Email',
		'header' => 'Delegate 2 Email',
		'width' => 1,
		'table' => 'fairs_stats.delegate2_email',
		'components' => array('fairs_stats')), 
	'fairstats_delegate3_email' => array (
		'name' => 'Fair Stats -- Delegate 3 Email',
		'header' => 'Delegate 3 Email',
		'width' => 1,
		'table' => 'fairs_stats.delegate3_email',
		'components' => array('fairs_stats')), 
	'fairstats_delegate4_email' => array (
		'name' => 'Fair Stats -- Delegate 4 Email',
		'header' => 'Delegate 4 Email',
		'width' => 1,
		'table' => 'fairs_stats.delegate4_email',
		'components' => array('fairs_stats')), 
	'fairstats_delegate1_size' => array (
		'name' => 'Fair Stats -- Delegate 1 Jacket Size',
		'header' => 'Delegate 1 Jacket Size',
		'width' => 1,
		'table' => 'fairs_stats.delegate1_size',
		'components' => array('fairs_stats')), 
	'fairstats_delegate2_size' => array (
		'name' => 'Fair Stats -- Delegate 2 Jacket Size',
		'header' => 'Delegate 2 Jacket Size',
		'width' => 1,
		'table' => 'fairs_stats.delegate2_size',
		'components' => array('fairs_stats')), 
	'fairstats_delegate3_size' => array (
		'name' => 'Fair Stats -- Delegate 3 Jacket Size',
		'header' => 'Delegate 3 Jacket Size',
		'width' => 1,
		'table' => 'fairs_stats.delegate3_size',
		'components' => array('fairs_stats')), 
	'fairstats_delegate4_size' => array (
		'name' => 'Fair Stats -- Delegate 4 Jacket Size',
		'header' => 'Delegate 4 Jacket Size',
		'width' => 1,
		'table' => 'fairs_stats.delegate4_size',
		'components' => array('fairs_stats')), 


	'static_text' => array (
		'name' => 'Static Text (useful for labels)',
		'header' => '',
		'width' => 0.1,
		'table' => "CONCAT(' ')"),

);

 function report_fairs_fromwhere($report, $components)
 {
 	global $config, $report_fairs_fields;
	
	$fields = $report_fairs_fields;
	$year = $report['year'];

	if(in_array('fairs_stats', $components)) {
		$fs_from = 'LEFT JOIN fairs_stats ON fairs_stats.fairs_id=fairs.id';
		$fs_where = "fairs_stats.year='$year'";

	}

	$q = "	FROM 	fairs 
			$fs_from
		WHERE
			1 AND 
			$fs_where
		";

	return $q;
}


?>
