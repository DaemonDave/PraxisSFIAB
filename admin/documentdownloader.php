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
 user_auth_required('committee', 'admin');
 $q=mysql_query("SELECT * FROM documents WHERE id='".$_GET['id']."'");
 if($r=mysql_fetch_object($q))
 {
	header("Content-type: ".trim(exec("file -bi ../data/documents/$r->filename")));
	header("Content-disposition: inline; filename=\"".$r->filename."\"");
	header("Content-length: ".filesize("../data/documents/$r->filename"));
	readfile("../data/documents/$r->filename");
 }
?>
