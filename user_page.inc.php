<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>
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

$user_page_overall_status = 'incomplete';

function user_page_summary_begin()
{
	global $user_page_overall_status;
	echo "<table class=\"summarytable\">";
	echo "<tr><th>".i18n("Item")."</th><th>".i18n("Status")."</th></tr>";
	$user_page_overall_status = 'complete';
}

function user_page_summary_item($name, $link, $status_function, $args=array())
{
	global $user_page_overall_status;
	echo "<tr><td>";
	echo "<a href=\"$link\">";
	echo i18n("$name");
	echo "</a>";
	echo "</td><td>";
	//check to see if its complete
	$status=call_user_func_array($status_function, $args);
	echo outputStatus($status);
	echo "</td></tr>";
	if($status != 'complete') {
		$user_page_overall_status = 'incomplete';
	}
}

function user_page_summary_end($print_overall)
{
	global $user_page_overall_status;
	if($print_overall) {
		echo "<tr><td colspan=\"2\"><hr></td></tr>";
		echo "<tr><td>".i18n("Overall Status")."</td><td>";
		echo outputStatus($user_page_overall_status);
		echo "</td></tr>";
	}
	echo "</table>";
	return $user_page_overall_status;
}

?>
