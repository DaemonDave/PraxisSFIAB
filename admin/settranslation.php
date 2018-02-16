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
?>
<?
include "../common.inc.php";
 require_once("../user.inc.php");
user_auth_required('committee', 'admin');
foreach($config['languages'] AS $l=>$ln) {
	if($l==$config['default_language']) continue;

	//check if it exists;
    $m=md5($_POST['translate_str_hidden']);

    if($_POST['translate_'.$l]) {
        $q=mysql_query("SELECT * FROM translations WHERE lang='$l' AND strmd5='$m'");
        if(mysql_num_rows($q))
            mysql_query("UPDATE translations SET val='".mysql_real_escape_string(iconv("UTF-8","ISO-8859-1",stripslashes($_POST['translate_'.$l])))."' WHERE lang='$l' AND strmd5='$m'");
        else
            mysql_query("INSERT INTO translations (lang,strmd5,str,val) VALUES ('$l','$m','".mysql_real_escape_string(iconv("UTF-8","ISO-8859-1",stripslashes($_POST['translate_str_hidden'])))."','".mysql_escape_string(iconv("UTF-8","ISO-8859-1",stripslashes($_POST['translate_'.$l])))."')");
    }
    else {
        mysql_query("DELETE FROM translations WHERE lang='$l' AND strmd5='$m'");
    }
}
echo "ok";

?>
