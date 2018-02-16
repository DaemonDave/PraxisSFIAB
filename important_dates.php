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
 include "common.inc.php";
 send_header("Important Dates",null,"important_dates");

 echo "<table>";

 $q=mysql_query("SELECT *,UNIX_TIMESTAMP(date) AS udate FROM dates WHERE year='{$config['FAIRYEAR']}' ORDER BY date");
 while($r=mysql_fetch_object($q))
 {
 	$trclass = ($trclass == 'odd') ? 'even' : 'odd';
	if($r->date != '0000-00-00 00:00:00') {
		$d =  format_datetime($r->udate);
		echo "<tr class=\"$trclass\"><td>".i18n($r->description)."</td><td>$d</td></tr>";
	}
 }
 echo "</table>";

 send_footer();
?>
