<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2007 James Grant <james@lightbox.org>
   Copyright (C) 2007 David Grant <dave@lightbox.org>

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
 require("../common.inc.php");
 require_once("../user.inc.php");
 require("../tableeditor.class.php");

 user_auth_required('committee', 'admin');
 send_header("Volunteer Positions Manager",
		 array("Committee Main" => 'committee_main.php',
		 	"Administration" => "admin/",
			"Volunteer Management" => "admin/volunteers.php") 
		                 );


 $editor=new TableEditor("volunteer_positions",
 			array("name"=>"Name",
				"desc"=>"Description",
				"meet_place"=>"Location Information",
				"start"=>"Start Day/Time",
				"end"=>"End Day/Time"
			), null, 
			array("year" => $config['FAIRYEAR'] )
			);

 $editor->setPrimaryKey("id");
 $editor->setDefaultSortField("start,name");
 $editor->setRecordType("Volunteer Position");
 $editor->filterList('year',$config['FAIRYEAR']);
 $editor->execute();

 send_footer();
?>
