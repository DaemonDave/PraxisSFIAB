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

function report_students_i18n_fr(&$report, $field, $text)
{
	return i18n($text, array(), array(), 'fr');
}

function reports_students_numstudents(&$report, $field, $text)
{
	$year = $report['year'];
	$q = mysql_query("SELECT students.id FROM students 
				WHERE students.registrations_id='$text'
					AND students.year='$year'");
	return mysql_num_rows($q);
}

function reports_students_award_selfnom_num(&$report, $field, $text, $n)
{
	$year = $report['year'];
	$q = mysql_query("SELECT award_awards.name FROM 
				projects 
				LEFT JOIN project_specialawards_link ON project_specialawards_link.projects_id=projects.id
				LEFT JOIN award_awards ON award_awards.id=project_specialawards_link.award_awards_id
				WHERE projects.id='$text'
					AND projects.year='$year'
					AND project_specialawards_link.year='$year'
				LIMIT $n,1");
	echo mysql_error();
	$i = mysql_fetch_assoc($q);
	return $i['name'];
}
function reports_students_award_selfnom_1(&$report, $field, $text)
{
	return reports_students_award_selfnom_num(&$report, $field, $text, 0);
}
function reports_students_award_selfnom_2(&$report, $field, $text)
{
	return reports_students_award_selfnom_num(&$report, $field, $text, 1);
}
function reports_students_award_selfnom_3(&$report, $field, $text)
{
	return reports_students_award_selfnom_num(&$report, $field, $text, 2);
}
function reports_students_award_selfnom_4(&$report, $field, $text)
{
	return reports_students_award_selfnom_num(&$report, $field, $text, 3);
}
function reports_students_award_selfnom_5(&$report, $field, $text)
{
	return reports_students_award_selfnom_num(&$report, $field, $text, 4);
}
function reports_students_school_principal(&$report, $field, $text)
{
	$year = $report['year'];
	if($text > 0) { /* text is the uid */
		$u = user_load_by_uid_year($text, $year);
		return $u['name'];
	}
	return '';
}


$report_students_fields = array(
	'pn' => array(
		'name' => 'Project Number',
		'header' => '#',
		'width' => 0.6,
		'table' => 'projects.projectnumber',
		'table_sort' => 'projects.projectsort, projects.projectnumber'),

	'last_name' =>  array(
		'start_option_group' => 'Student Name Information',
		'name' => 'Student -- Last Name',
		'header' => 'Last Name',
		'width' => 1.0,
		'table' => 'students.lastname' ),

	'first_name' => array(
		'name' => 'Student -- First Name',
		'header' => 'First Name',
		'width' => 1.0,
		'table' => 'students.firstname' ),

	'name' =>  array(
		'name' => 'Student -- Full Name (last, first)',
		'header' => 'Name',
		'width' => 1.75,
		'scalable' => true,
		'table' => "CONCAT(students.lastname, ', ', students.firstname)",
		'table_sort'=> 'students.lastname' ),

	'namefl' => array(
		'name' => 'Student -- Full Name (first last)',
		'header' => 'Name',
		'width' => 1.75,
		'scalable' => true,
		'table' => "CONCAT(students.firstname, ' ', students.lastname)",
		'table_sort'=> 'students.lastname' ),

	'partner' =>  array(
		'name' => 'Student -- Partner Name (last, first)',
		'header' => 'Partner',
		'width' => 1.5,
		'scalable' => true,
		'table' => "CONCAT(students2.lastname, ', ', students2.firstname)",
		'components' => array('partner') ),

	'partnerfl' =>  array(
		'name' => 'Student -- Partner Name (first last)',
		'header' => 'Partner',
		'width' => 1.5,
		'scalable' => true,
		'table' => "CONCAT(students2.firstname, ' ', students2.lastname)",
		'components' => array('partner') ),

	'bothnames' =>  array(
		'name' => "Student -- Both Student Names",
		'header' => 'Student(s)',
		'width' => 3.0,
		'scalable' => true,
		'table' => "CONCAT(students.firstname, ' ', students.lastname, IF(students2.lastname IS NULL,'', CONCAT(', ', students2.firstname, ' ', students2.lastname)))",
		'table_sort' => 'students.lastname',
		'components' => array('partner') ),

	'allnames' =>  array(
		'name' => "Student -- All Student Names (REQUIRES MYSQL 5.0) ",
		'header' => 'Student(s)',
		'width' => 3.0,
		'scalable' => true,
		'table' => "GROUP_CONCAT(students.firstname, ' ', students.lastname ORDER BY students.lastname SEPARATOR ', ')",
		'group_by' => array('students.registrations_id')),

	'pronunciation'  =>  array(
		'name' => 'Student -- Name Pronunciation',
		'header' => 'Pronunciation',
		'width' => 2.0,
		'table' => 'students.pronunciation'),

	'email' => array(
		'start_option_group' => 'Student Contact Information',
		'name' => 'Student -- Email',
		'header' => 'Email',
		'width' => 2.25,
		'scalable' => true,
		'table' => 'students.email'),

	'phone' => array(
		'name' => 'Student -- Phone',
		'header' => 'Phone',
		'width' => 1.0,
		'table' => 'students.phone'),

	'address' =>  array(
		'name' => 'Student -- Street Address',
		'header' => 'Address',
		'width' => 2.0,
		'scalable' => true,
		'table' => 'students.address'),

	'city' =>  array(
		'name' => 'Student -- City',
		'header' => 'City',
		'width' => 1.5,
		'table' => 'students.city' ),

	'province' =>  array(
		'name' => 'Student -- '.$config['provincestate'],
		'header' => $config['provincestate'],
		'width' => 0.75,
		'table' => 'students.province' ),

	'postal' =>  array(
		'name' => 'Student -- '.$config['postalzip'],
		'header' => $config['postalzip'],
		'width' => 0.75,
		'table' => 'students.postalcode' ),

	'address_full' => array(
		'name' => 'Student -- Full Address',
		'header' => 'Address',
		'width' => 3.0,
		'scalable' => true,
		'table' => "CONCAT(students.address, ', ', students.city, ', ', students.province, ', ', students.postalcode)" ),


	'grade' =>  array(
		'start_option_group' => 'Other Student Information',
		'name' => 'Student -- Grade',
		'header' => 'Gr.',
		'width' => 0.3,
		'table' => 'students.grade'),

	'grade_str' =>  array(
		'name' => 'Student -- Grade ("Grade x", not just the number)',
		'header' => 'Gr.',
		'width' => 0.3,
		'table_sort' => 'students.grade',
		'table' => "CONCAT('Grade ', students.grade)"),

	'gender' =>  array(
		'name' => 'Student -- Gender',
		'header' => 'Gender',
		'width' => 0.5,
		'table' => 'students.sex',
		'value_map' =>array ('male' => 'Male', 'female' => 'Female')),

	'birthdate' => array(
		'name' => 'Student -- Birthdate',
		'header' => 'Birthdate',
		'width' => 0.9,
		'table' => 'students.dateofbirth'),

	'age' => array(
		'name' => 'Student -- Age (when this report is created)',
		'header' => 'Age',
		'width' => 0.4,
		'table' => "DATE_FORMAT(FROM_DAYS(TO_DAYS(NOW())-TO_DAYS(students.dateofbirth)), '%Y')+0",
		'table_sort' => 'students.dateofbirth'),

	'tshirt' =>  array(
		'name' => 'Student -- T-Shirt Size',
		'header' => 'T-Shirt',
		'width' => 0.55,
		'table' => 'students.tshirt',
		'value_map' => array ('none' => '', 'xsmall' => 'X-Small', 'small' => 'Small', 'medium' => 'Medium',
					'large' => 'Large', 'xlarge' => 'X-Large')),

	'medicalalert' => array(
		'name' => 'Student -- Medical Alert Info',
		'header' => 'medical',
		'width' => 2.0,
		'table' => 'students.medicalalert'),

	'foodreq' => array(
		'name' => 'Student -- Food Requirements',
		'header' => 'Food.Req.',
		'width' => 2.0,
		'table' => 'students.foodreq'),

	'registrations_num' => array(
		'start_option_group' => 'Student Registration Information',
		'name' => 'Student -- Registration Number',
		'header' => 'RegNum',
		'width' => 1,
		'table' => 'registrations.num' ),

	'paid' => array(
		'name' => 'Paid',
		'header' => 'Paid',
		'width' => '0.4',
		'table' => 'registrations.status',
		'value_map' => array ('complete' => '', 'paymentpending' => 'No')),

/* Project Information */
	'title' => array(
		'start_option_group' => 'Project Information',
		'name' => 'Project -- Title',
		'header' => 'Project Title',
		'width' => 2.75,
		'scalable' => true,
		'table' => 'projects.title' ),

	'shorttitle' => array(
		'name' => 'Project -- Short Title',
		'header' => 'Short Title',
		'width' => 2,
		'table' => 'projects.shorttitle' ),

	'division' =>  array(
		'name' => 'Project -- Division',
		'header' => 'Division',
		'width' => 3.0,
		'table' => 'projectdivisions.division' ),

	'div' => array(
		'name' => 'Project -- Division Short Form' ,
		'header' => 'Div',
		'width' => 0.4,
		'table' => 'projectdivisions.division_shortform' ),

	'fr_division' =>  array(
		'name' => 'Project -- Division (French)',
		'header' => i18n('Division', array(), array(), 'fr'),
		'width' => 3.0,
		'table' => 'projectdivisions.division',
		'exec_function' => 'report_students_i18n_fr'),

	'category' => array(
		'name' => 'Project -- Category',
		'header' => 'Category',
		'width' => 1,
		'table_sort' => 'projectcategories.id',
		'table' => 'projectcategories.category' ),

	'cat' => array(
		'name' => 'Project -- Category Short Form' ,
		'header' => 'cat',
		'width' => 0.4,
		'table' => 'projectcategories.category_shortform' ),

	'fr_category' => array(
		'name' => 'Project -- Category (French)',
		'header' => i18n('Category', array(), array(), 'fr'),
		'width' => 1,
		'table_sort' => 'projectcategories.id',
		'table' => 'projectcategories.category',
		'exec_function' => 'report_students_i18n_fr'),

	'categorydivision' => array(
		'name' => 'Project -- Category and Division',
		'header' => 'Category/Division',
		'width' => 3.5,
		'table_sort' => 'projectcategories.id',
		'table' => "CONCAT(projectcategories.category,' - ', projectdivisions.division)"),

	'divisioncategory' => array(
		'name' => 'Project -- Division and Category',
		'header' => 'Division/Category',
		'width' => 3.5,
		'table_sort' => 'projectdivisions.id',
		'table' => "CONCAT(projectdivisions.division,' - ',projectcategories.category)"),

	'summary' => array(
		'name' => 'Project -- Summary',
		'header' => 'Project Summary',
		'width' => 4.00,
		'scalable' => true,
		'table' => 'projects.summary' ),

	'language' => array(
		'name' => 'Project -- Language',
		'header' => 'Lang',
		'width' => 1.00,
		'table' => 'projects.language' ),

	'numstudents' => array(
		'name' => 'Project -- Number of Students',
		'header' => 'Stu.',
		'width' => 0.5,
		'table' => 'students.registrations_id',
		'exec_function' => 'reports_students_numstudents'),
		
	'rank' => array(
		'name' => 'Project -- Rank (left blank for judges to fill out)',
		'header' => 'Rank',
		'width' => 1.00,
		'table' => '""' ),

	'req_elec' => array(
		'name' => 'Project -- If the project requires electricity',
		'header' => 'Elec',
		'width' => .5,
		'table' => "projects.req_electricity",
		'value_map' => array ('no' => '', 'yes' => 'Yes')),
		
	'req_table' => array(
		'name' => 'Project -- If the project requires a table',
		'header' => 'Table',
		'width' => .5,
		'table' => "projects.req_table",
		'value_map' => array ('no' => '', 'yes' => 'Yes')),
		
	'req_special' => array(
		'name' => 'Project -- Any special requirements the project has',
		'header' => 'Special Requirements',
		'width' => 3,
		'table' => "projects.req_special"),


	'school' =>  array(
		'start_option_group' => 'School Information',
		'name' => 'School -- Name',
		'header' => 'School Name',
		'width' => 2.25,
		'scalable' => true,
		'table' => 'schools.school' ),

	'schooladdr' => array(
		'name' => 'School -- Full Address',
		'header' => 'School Address',
		'width' => 3.0,
		'scalable' => true,
		'table' => "CONCAT(schools.address, ', ', schools.city, ', ', schools.province_code, ', ', schools.postalcode)" ),

	'teacher' => array(
		'name' => 'School -- Teacher Name (as entered by the student)',
		'header' => 'Teacher',
		'width' => 1.5,
		'table' => 'students.teachername' ),

	'teacheremail' => array(
		'name' => 'School -- Teacher Email (as entered by the student)',
		'header' => 'Teacher Email',
		'width' => 2.0,
		'table' => 'students.teacheremail' ),

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


	'school_address' =>  array(
		'name' => 'School -- Street Address',
		'header' => 'Address',
		'width' => 2.0,
		'table' => 'schools.address'),

	'school_city' =>  array(
		'name' => 'School -- City',
		'header' => 'City',
		'width' => 1.5,
		'table' => 'schools.city' ),

	'school_province' =>  array(
		'name' => 'School -- '.$config['provincestate'],
		'header' => $config['provincestate'],
		'width' => 0.75,
		'table' => 'schools.province_code' ),

	'school_city_prov' =>  array(
		'name' => 'School -- City, '.$config['provincestate'].' (for mailing)',
		'header' => 'City',
		'width' => 1.5,
		'table' => "CONCAT(schools.city, ', ', schools.province_code)" ),

	'school_postal' =>  array(
		'name' => 'School -- '.$config['postalzip'],
		'header' => $config['postalzip'],
		'width' => 0.75,
		'table' => 'schools.postalcode' ),

	'school_principal' => array(
		'name' => 'School -- Principal',
		'header' => 'Principal',
		'width' => 1.25,
		'table' => 'schools.principal_uid',
		'exec_function' => 'reports_students_school_principal'),

	'school_board' =>  array(
		'name' => 'School -- Board ID',
		'header' => 'Board',
		'width' => 0.75,
		'table' => 'schools.board' ),

	'awards' =>  array(
		'start_option_group' => 'Awards assigned to student (warning: duplicates student entries for multiple awards won!)',
		'name' => 'Award -- Type + Name',
		'header' => 'Award Name',
		'width' => 4,
		'table' => "CONCAT(IF(award_types.type='Other','Special',award_types.type),' ', award_awards.name)",
		'table_sort' => 'award_awards.name',
		'components' => array('awards')),

	'award_name' =>  array(
		'name' => 'Award -- Name',
		'header' => 'Award Name',
		'width' => 4,
		'table' => 'award_awards.name',
		'components' => array('awards')),

	'award_excludefromac' => array(
		'name' => 'Award -- Exclude from Award Ceremony (Yes/No)',
		'header' => 'Exclude',
		'width' => .5,
		'table' => "award_awards.excludefromac",
		'value_map' => array ('no' => 'No', 'yes' => 'Yes')),

	'order' =>  array(
		'name' => 'Award -- Order',
		'header' => 'Award Order',
		'width' => 0.5,
		'table' => 'award_awards.order',
		'table_sort' => 'award_awards.order',
		'components' => array('awards')),

	'award_type' => array(
		'name' => 'Award -- Type (Divisional, Special, etc.)',
		'header' => 'Award Type',
		'width' => 1,
		'table' => 'award_types.type',
		'components' => array('awards')),
		
	'sponsor' =>  array(
		'name' => 'Award -- Sponsor DB ID',
		'header' => 'Award Sponsor',
		'width' => 1.5,
		'table' => 'award_awards.sponsors_id',
		'table_sort' => 'award_awards.sponsors_id',
		'components' => array('awards')),

	'pn_awards' =>  array(
		'name' => 'Award -- Project Num + Award Name (will be unique for each award)',
		'header' => 'Award Name',
		'width' => 4,
		'table' => "CONCAT(projects.projectnumber,' ', award_awards.name)",
		'table_sort' => 'award_awards.order',
		'components' => array('awards')),

	'award_prize_name' => array(
		'name' => 'Award -- Prize Name',
		'header' => 'Prize Name',
		'width' => 2,
		'table' => 'award_prizes.prize',
		'components' => array('awards')),

	'award_prize_cash' => array(
		'name' => 'Award -- Prize Cash Amount',
		'header' => 'Cash',
		'width' => 0.5,
		'table' => 'award_prizes.cash',
		'components' => array('awards')),

	'award_prize_scholarship' => array(
		'name' => 'Award -- Prize Scholarship Amount',
		'header' => 'Scholarship',
		'width' => 0.75,
		'table' => 'award_prizes.scholarship',
		'components' => array('awards')),

	'award_prize_value' => array(
		'name' => 'Award -- Prize Value Amount',
		'header' => 'Value',
		'width' => 0.5,
		'table' => 'award_prizes.value',
		'components' => array('awards')),

	'award_prize_fullname' => array(
		'name' => 'Award -- Prize Name, Category, Division',
		'header' => 'Prize Name',
		'width' => 4,
		'table' => "CONCAT(award_prizes.prize,' in ',projectcategories.category,' ', projectdivisions.division)",
		'table_sort' => 'award_prizes.order',
		'components' => array('awards')),

	'award_prize_trophy_any' => array(
		'name' => 'Award -- Trophy (\'Yes\' if the award has a trophy)',
		'header' => 'Trophy',
		'width' => 0.5,
		'table' => "IF ( award_prizes.trophystudentkeeper=1
				OR award_prizes.trophystudentreturn=1
				OR award_prizes.trophyschoolkeeper=1
				OR award_prizes.trophyschoolreturn=1, 'Yes', 'No')",
		'components' => array('awards')),

	'award_prize_trophy_return' => array(
		'name' => 'Award -- Annual Trophy (\'Yes\' if the award has a school or student trophy that isn\'t a keeper)',
		'header' => 'Trophy',
		'width' => 0.5,
		'table' => "IF ( award_prizes.trophystudentreturn=1
				OR award_prizes.trophyschoolreturn=1, 'Yes', 'No')",
		'components' => array('awards')),

	'award_prize_trophy_return_student' => array(
		'name' => 'Award -- Annual Student Trophy (\'Yes\' if the award has astudent trophy that isn\'t a keeper)',
		'header' => 'Ind.',
		'width' => 0.5,
		'table' => "IF ( award_prizes.trophystudentreturn=1, 'Yes', 'No')",
		'components' => array('awards')),

	'award_prize_trophy_return_school' => array(
		'name' => 'Award -- Annual School Trophy (\'Yes\' if the award has a school trophy that isn\'t a keeper)',
		'header' => 'Sch.',
		'width' => 0.5,
		'table' => "IF ( award_prizes.trophyschoolreturn=1, 'Yes', 'No')",
		'components' => array('awards')),

	'nom_awards' => array(
		'start_option_group' => 'Nominated Awards  (warning: duplicates student for multiple awards!)',
		'name' => 'Award Nominations -- Award Name',
		'header' => 'Award Name',
		'width' => 4,
		'table' => "CONCAT(award_types.type,' -- ',award_awards.name)",
		'table_sort' => 'award_awards.name',
		'components' => array('awards_nominations')),

	'nom_pn_awards' =>  array(
		'name' => 'Award Nominations -- Project Num + Award Name(will be unique)',
		'header' => 'Award Name',
		'width' => 4,
		'table' => "CONCAT(projects.projectnumber,' ', award_awards.name)",
		'table_sort' => 'award_awards.name',
		'components' => array('awards_nominations')),

	'nom_awards_name_1' =>  array(
		'name' => 'Award Nominations -- Self-Nominated Special Award 1',
		'header' => 'Award Name',
		'width' => 3,
		'table' => 'projects.id',
		'table_sort' => 'projects.id',
		'exec_function' => 'reports_students_award_selfnom_1'),

	'nom_awards_name_2' =>  array(
		'name' => 'Award Nominations -- Self-Nominated Special Award 2',
		'header' => 'Award Name',
		'width' => 3,
		'table' => 'projects.id',
		'table_sort' => 'projects.id',
		'exec_function' => 'reports_students_award_selfnom_2'),

	'nom_awards_name_3' =>  array(
		'name' => 'Award Nominations -- Self-Nominated Special Award 3',
		'header' => 'Award Name',
		'width' => 3,
		'table' => 'projects.id',
		'table_sort' => 'projects.id',
		'exec_function' => 'reports_students_award_selfnom_3'),

	'nom_awards_name_4' =>  array(
		'name' => 'Award Nominations -- Self-Nominated Special Award 4',
		'header' => 'Award Name',
		'width' => 3,
		'table' => 'projects.id',
		'table_sort' => 'projects.id',
		'exec_function' => 'reports_students_award_selfnom_4'),

	'nom_awards_name_5' =>  array(
		'name' => 'Award Nominations -- Self-Nominated Special Award 5',
		'header' => 'Award Name',
		'width' => 3,
		'table' => 'projects.id',
		'table_sort' => 'projects.id',
		'exec_function' => 'reports_students_award_selfnom_5'),

/* Emergency Contact Info */
	'emerg_name' => array(
		'start_option_group' => 'Emergency Contact Information',
		'name' => 'Emergency Contact -- Name',
		'header' => 'Contact Name',
		'width' => 1.5,
		'table' => "CONCAT(emergencycontact.firstname, ' ', emergencycontact.lastname)",
		'components' => array('emergencycontacts')),

	'emerg_relation' => array(
		'name' => 'Emergency Contact -- Relationship',
		'header' => 'Relation',
		'width' => 1,
		'table' => "emergencycontact.relation",
		'components' => array('emergencycontacts')),

	'emerg_phone' => array(
		'name' => 'Emergency Contact -- Phone',
		'header' => 'Emrg.Phone',
		'width' => 1,
		'table' => "CONCAT(emergencycontact.phone1, ' ', emergencycontact.phone2, ' ', emergencycontact.phone3, ' ', emergencycontact.phone4)",
		'components' => array('emergencycontacts')),

/* Tour Information */
	'tour_assign_name' => array(
		'start_option_group' => 'Tour Information',
		'name' => 'Tours -- Assigned Tour Name',
		'header' => 'Tour',
		'width' => 4,
		'table' => "tours.name",
		'components' => array('tours')),

	'tour_assign_num' => array(
		'name' => 'Tours -- Assigned Tour Number',
		'header' => 'Tour',
		'width' => 0.5,
		'table' => "tours.num",
		'components' => array('tours')),

	'tour_assign_numname' => array(
		'name' => 'Tours -- Assigned Tour Number and Name',
		'header' => 'Tour',
		'width' => 4,
		'table' => "CONCAT(tours.num,': ', tours.name)",
		'table_sort' => 'tours.num',
		'components' => array('tours')),

/* Mentor Information */
	'mentor_name_proj' => array(
		'start_option_group' => 'Mentor Information',
		'name' => 'Mentor -- Project and Name (Distinct for each Project+Mentor pair)',
		'header' => 'Mentor Name',
		'width' => 1.75,
		'scalable' => true,
		'table' => "CONCAT('projects.projectnumber', ' - ', mentors.firstname, ', ', mentors.lastname)",
		'table_sort'=> 'mentors.lastname',
		'components' => array('mentors')),

	'mentor_last_name' =>  array(
		'name' => 'Mentor -- Last Name',
		'header' => 'Last Name',
		'width' => 1.0,
		'table' => 'mentors.lastname',
		'components' => array('mentors')),

	'mentor_first_name' => array(
		'name' => 'Mentor -- First Name',
		'header' => 'First Name',
		'width' => 1.0,
		'table' => 'mentors.firstname',
		'components' => array('mentors')),

	'mentor_name' =>  array(
		'name' => 'Mentor -- Full Name (last, first)',
		'header' => 'Mentor Name',
		'width' => 1.75,
		'scalable' => true,
		'table' => "CONCAT(mentors.lastname, ', ', mentors.firstname)",
		'table_sort'=> 'mentors.lastname',
		'components' => array('mentors')),

	'mentor_namefl' => array(
		'name' => 'Mentor -- Full Name (first last)',
		'header' => 'Mentor Name',
		'width' => 1.75,
		'scalable' => true,
		'table' => "CONCAT(mentors.firstname, ' ', mentors.lastname)",
		'table_sort'=> 'mentors.lastname',
		'components' => array('mentors')),
		
	'mentor_email' => array(
		'name' => 'Mentor -- Email',
		'header' => 'Mentor Email',
		'width' => 2.0,
		'scalable' => true,
		'table' => 'mentors.email',
		'components' => array('mentors')),

	'mentor_phone' => array(
		'name' => 'Mentor -- Phone',
		'header' => 'Mentor Phone',
		'width' => 1,
		'table' => 'mentors.phone',
		'components' => array('mentors')),

	'mentor_organization' => array(
		'name' => 'Mentor -- Organization',
		'header' => 'Mentor Org.',
		'width' => 1.5,
		'scalable' => true,
		'table' => 'mentors.organization',
		'components' => array('mentors')),

	'mentor_position' => array(
		'name' => 'Mentor -- Position',
		'header' => 'Position',
		'width' => 1,
		'scalable' => true,
		'table' => 'mentors.position',
		'components' => array('mentors')),

	'mentor_description' => array(
		'name' => 'Mentor -- Description of Help',
		'header' => 'Description of Help',
		'width' => 3.0,
		'scalable' => true,
		'table' => 'mentors.description',
		'components' => array('mentors')),

/* Fair Information */
	'feeder_fair_name' => array (
		'start_option_group' => 'Fair Information',
		'name' => 'Feeder Fair -- Name',
		'header' => 'Fair Name',
		'width' => 1.5,
		'table' => 'fairs.name',
		'components' => array('fairs')),

	'feeder_fair_abbrv' => array (
		'name' => 'Feeder Fair -- Abbreviation',
		'header' => 'Fair',
		'width' => 0.75,
		'table' => 'fairs.abbrv',
		'components' => array('fairs')),


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

/* Special/Misc/Other */
	'static_text' => array (
		'start_option_group' => 'Special Fields',
		'name' => 'Label -- Static Text',
		'header' => '',
		'width' => 0.1,
		'table' => "CONCAT(' ')"),

	'static_box' => array (
		'name' => 'Label -- Static Box',
		'header' => '',
		'width' => 0.1,
		'table' => "CONCAT(' ')"),

	'easyparse_allnames' =>  array(
		'name' => "Easy Parse -- All Student Names (REQUIRES MYSQL 5.0) ",
		'header' => 'Student(s)',
		'width' => 3.0,
		'table' => "GROUP_CONCAT(students.lastname, ',', students.firstname ORDER BY students.lastname SEPARATOR ':')",
		'group_by' => array('students.registrations_id')),

	'special_tshirt_count' =>  array(
		'name' => 'Special -- T-Shirt Size Count',
		'header' => 'Count',
		'width' => 0.5,
		'table' => 'COUNT(*)',
		'total' => true,
		'group_by' => array('students.tshirt')),


);

 function report_students_fromwhere($report, $components)
 {
 	global $config, $report_students_fields;
	
	$fields = $report_students_fields;
	$year = $report['year'];

	$awards_join = '';
	$awards_where = '';
	
	if(in_array('awards', $components)) {
		/* This requires some extra gymnastics and will duplicate
		 * students/projects if they have won multiple awards */
		$awards_join = "LEFT JOIN winners ON winners.projects_id = projects.id
				LEFT JOIN award_prizes ON award_prizes.id = winners.awards_prizes_id
				LEFT JOIN award_awards ON award_awards.id = award_prizes.award_awards_id
				LEFT JOIN award_types ON award_types.id=award_awards.award_types_id";
		$awards_where = " AND winners.year='$year'
				AND award_awards.year='$year'
				AND award_prizes.year='$year'
				AND award_types.year='$year' ";
	}

	if(in_array('awards_nominations', $components)) {
		$awards_join = "LEFT JOIN project_specialawards_link 
					ON(projects.id=project_specialawards_link.projects_id),
					award_awards,award_types";
		$awards_where = " AND project_specialawards_link.award_awards_id=award_awards.id
					AND award_types.id=award_awards.award_types_id
					AND award_awards.year='$year'
					AND award_types.year='$year' ";
	}

	$partner_join = '';
	if(in_array('partner', $components)) {
		$partner_join = "LEFT JOIN students AS students2 
					ON(students2.registrations_id=students.registrations_id 
					AND students2.id != students.id)";
	} 

	$tour_join = '';
	$tour_where = '';
	if(in_array('tours', $components)) {
		$tour_join = "LEFT JOIN tours_choice ON (students.id=tours_choice.students_id AND tours_choice.rank=0), tours";
		$tour_where = "AND tours.year='$year'
				AND tours.id=tours_choice.tour_id";
	}

	$emergencycontact_join = '';
	if(in_array('emergencycontacts', $components)) {
		/* No need to put the year in here, students.id is unique across years */
		$emergencycontact_join = "LEFT JOIN emergencycontact ON
					emergencycontact.students_id=students.id ";
	}

	$mentor_join = '';
	$mentor_where = '';
	if(in_array('mentors', $components)) {
		$mentor_join = "LEFT JOIN mentors ON
					mentors.registrations_id=students.registrations_id";
		$mentor_where = "AND mentors.year='$year'";
	}

	$fairs_join = '';
	if(in_array('fairs', $components)) {
		$fairs_join = "LEFT JOIN fairs ON fairs.id=projects.fairs_id";
	}

	if($report['option']['include_incomplete_registrations'] == 'yes') 
		$reg_where = '';
	else 
		$reg_where = "AND (registrations.status='complete' OR registrations.status='paymentpending')";


	$q = "	FROM  students 
			LEFT JOIN registrations ON registrations.id=students.registrations_id
			LEFT JOIN schools ON schools.id=students.schools_id
			LEFT JOIN projects ON projects.registrations_id=students.registrations_id
			LEFT JOIN projectdivisions ON projectdivisions.id=projects.projectdivisions_id
			LEFT JOIN projectcategories ON projectcategories.id=projects.projectcategories_id
			$emergencycontact_join
			$partner_join
			$mentor_join
			$tour_join
			$awards_join
			$fairs_join
		WHERE
			students.year='$year'
			AND projects.year='$year'
			AND registrations.year='$year'
			AND projectcategories.year='$year'
			AND projectdivisions.year='$year'
			$reg_where
			$mentor_where
			$awards_where
			$tour_where
		";

	return $q;
}

?>
