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

 //make sure storage folder exists
 if(!file_exists("../data/documents"))
 	mkdir("../data/documents");
 if(!file_exists("../data/documents/.htaccess"))
 	file_put_contents("../data/documents/.htaccess","Order Deny,Allow\r\nDeny From All\r\n");

 user_auth_required('committee', 'admin');
 send_header("Internal Document Manager",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "internal_document_management"
			);

 $editor=new TableEditor("documents",
 			array("date"=>"Date",
				"title"=>"Document Title",
				"sel_category"=>"Category",
				"filename"=>"Filename",
			)
			);

 $editor->setPrimaryKey("id");
 $editor->setUploadPath("../data/documents");
 $editor->setDefaultSortField("sel_category,date");
 $editor->setRecordType("Document");
 $editor->setFieldDefaultValue("date",date("Y-m-d"));
 $editor->setDownloadLink("documentdownloader.php");
 $editor->execute();

 send_footer();
?>
