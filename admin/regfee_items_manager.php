<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2007 James Grant <james@lightbox.org>

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
 require("../tableeditor.class.php");
 require_once("../user.inc.php");

 user_auth_required('committee', 'admin');
 send_header("Registration Fee Items Manager",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "registration_fee_items_management"
			);

 $editor=new TableEditor("regfee_items",
 			array(  'name' => 'Name (for regfee line)',
				'description' => 'Description',
				'cost' => 'Cost',
				'per' => 'Cost Per',
			), null,
			array('year' => $config['FAIRYEAR'])
			);

 $editor->setPrimaryKey("id");
 $editor->setDefaultSortField("description");
 $editor->setRecordType("Registration Fee Item");
 $editor->setFieldOptions("per", array( array('key' => 'student', 'val' => "Student"),
					array('key' => 'project', 'val' => "Project") 
			              )  );
 $editor->setFieldInputType("per", 'select');
 $editor->filterList('year',$config['FAIRYEAR']);
 
 $editor->execute();

 send_footer();
?>
