<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2010 David Grant <dave@lightbox.org>

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

function award_delete($award_awards_id)
{
	/* Delete all winners attached to this award */
	$q = mysql_query("SELECT id FROM award_prizes WHERE award_awards_id='$award_awards_id'");
	while(($p = mysql_fetch_assoc($q))) {
		mysql_query("DELETE FROM winners WHERE award_prizes_id='{$p['id']}'");
	}

	/* FIXME: maybe delte judging teams and judge 
	 * assignments and timeslots?

	/* Delete the award */
	mysql_query("DELETE FROM award_prizes WHERE award_awards_id='$award_awards_id'");
	mysql_query("DELETE FROM award_awards_projectcategories WHERE award_awards_id='$award_awards_id'");
	mysql_query("DELETE FROM award_awards_projectdivisions WHERE award_awards_id='$award_awards_id'");
	mysql_query("DELETE FROM award_awards WHERE id='$award_awards_id'");
}

function award_prize_delete($award_prizes_id)
{
	mysql_query("DELETE FROM winners WHERE award_prizes_id='$award_prizes_id'");
	mysql_query("DELETE FROM award_prizes WHERE id='$award_prizes_id'");
}

?>
