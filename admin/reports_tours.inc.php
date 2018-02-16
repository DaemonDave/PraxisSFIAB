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

$report_tours_fields = array(
	'tour_name' =>  array(
		'name' => 'Tour -- Name',
		'header' => 'Tour Name',
		'width' => 3,
		'table' => 'tours.name' ),

	'tour_num' =>  array(
		'name' => 'Tour -- Number',
		'header' => 'Num',
		'width' => 0.75,
		'table' => 'tours.num' ),

	'tour_desc' => array(
		'name' => 'Tour -- Description',
		'header' => 'Tour Description',
		'width' => 3.0,
		'table' => 'tours.description'),

	'tour_capacity' => array(
		'name' => 'Tour -- Capacity',
		'header' => 'Cap',
		'width' => 0.4,
		'table' => 'tours.capacity' ),

	'tour_mingrade' => array(
		'name' => 'Tour -- Minimum Grade',
		'header' => 'Min Gr.',
		'width' => 0.4,
		'table' => 'tours.grade_min' ),

	'tour_maxgrade' => array(
		'name' => 'Tour -- Maximum Grade',
		'header' => 'Max Gr.',
		'width' => 0.4,
		'table' => 'tours.grade_max' ),

	'tour_location' =>  array(
		'name' => 'Tour -- Location',
		'header' => 'Tour Location',
		'width' => 2.0,
		'table' => 'tours.location'),

	'tour_contact' =>  array(
		'name' => 'Tour -- Contact',
		'header' => 'Contact',
		'width' => 1.5,
		'table' => 'tours.contact' ),

	'tour_id' =>  array(
		'name' => 'Tour -- Database ID',
		'header' => '#',
		'width' => 0.4,
		'table' => 'tours.id' ),

);

 function report_tours_fromwhere($report, $components)
 {
 	global $config, $report_tours_fields;
	
	$fields = $report_tours_fields;
	$year = $report['year'];

	$q = "	FROM
			tours
		WHERE
			tours.year='$year'
		";

	return $q;
}

?>
