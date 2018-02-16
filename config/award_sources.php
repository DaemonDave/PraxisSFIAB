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
 require_once("../user.inc.php");
 require("../tableeditor.class.php");

 user_auth_required('committee', 'config');
 send_header("External Award Sources Manager",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php'),
             "external_award_sources");


 $editor=new TableEditor("award_sources",
 			array(
				"enabled"=>"Enabled?",
				"name"=>"Name",
				"website"=>"Help URL",
				"username"=>"Username",
			     )
			     ,
 			array(
				"enabled"=>"Enabled?",
				"name"=>"Name",
				"url"=>"Source URL",
				"website"=>"Help URL",
				"username"=>"Username",
				"password"=>"Password"
			     )
			);

 $editor->setPrimaryKey("id");
 $editor->setDefaultSortField("name");
 $editor->setRecordType("Award Source");
 $editor->execute();

 echo "<br />";
 echo i18n("Open the 'Help URL' in your browser to see if the award source applies to your fair and to obtain the username/password for the source if it does.");

 send_footer();
?>
