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


function fair_status_info($u)
{
	global $config;
	if(trim($u['fairname']) != '') {
		return "complete";
	}
	return "incomplete";
}


function fair_status_update(&$u)
{
	global $config;

	if(   user_personal_info_status($u) == 'complete'
	   && fair_status_info($u) == 'complete' )
		$u['fair_complete'] = 'yes';
	else
		$u['fair_complete'] = 'no';

	user_save($u);
	return ($u['fair_complete'] == 'yes') ? 'complete' : 'incomplete';
}

?>
