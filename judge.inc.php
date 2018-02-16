<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005 James Grant <james@lightbox.org>
   Copyright (C) 2009 David Grant <dave@lightbox.org>

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

$preferencechoices=array(
	-2=>"Very Low",
	-1=>"Low",
	0=>"Indifferent",
	1=>"Medium",
	2=>"High"
);

function judge_status_expertise(&$u)
{
	global $config;

	/* If the judging special awards are active, and the judge has
	 * selected "I am a special awards judge", then disable 
	 * expertise checking */
	if($config['judges_specialaward_only_enable'] == 'yes') {
		if($u['special_award_only'] == 'yes') 
			return 'complete';
	}

	/* Check to see if they have ranked all project age categories, and all divisions */
	$q=mysql_query("SELECT COUNT(id) AS num FROM projectcategories WHERE year='".$config['FAIRYEAR']."'");
	$r=mysql_fetch_object($q);
	$numcats=$r->num;
	if($numcats != count($u['cat_prefs'])) 	return 'incomplete';

	$q=mysql_query("SELECT COUNT(id) AS num FROM projectdivisions WHERE year='".$config['FAIRYEAR']."'");
	$r=mysql_fetch_object($q);
	$numdivisions=$r->num;
	if($numdivisions != count($u['div_prefs'])) return 'incomplete';

	return 'complete';
}

function judge_status_other(&$u)
{
	global $config;

	/* They must select a language to judge in */
	if(count($u['languages']) < 1) return 'incomplete';

	return 'complete';
}



function judge_status_special_awards(&$u)
{
	global $config;

	if($config['judges_specialaward_enable'] == 'no' && $u['special_award_only']=='no')
		return 'complete';

	/* Complete if:
	 * - judge has selected (none) "no special award preferences"
	 * - judge has selected between min and max preferences 
	 */

	$qq = mysql_query("SELECT COUNT(id) AS num FROM judges_specialaward_sel 
				WHERE users_id='{$u['id']}'");
	$rr = mysql_fetch_object($qq);
	$awards_selected = $rr->num;
//	echo "$awards_selected awards selected, ({$config['judges_specialaward_min']} - {$config['judges_specialaward_max']})";

	if($u['special_award_only'] == 'yes') {
		/* Judge for special award */
		/* They may judge more than one award, so don't limit them
		 * to one */
		if($awards_selected >= 1) return 'complete';
		return 'incomplete';
	}

	if( ($awards_selected >= $config['judges_specialaward_min']) 
	  &&($awards_selected <= $config['judges_specialaward_max']) ){
	  	return 'complete';
	}
	
	return 'incomplete';
}

function judge_status_availability(&$u)
{
	global $config;
	if($config['judges_availability_enable'] == 'no') return 'complete';

	$q = mysql_query("SELECT id FROM judges_availability 
			WHERE users_id=\"{$u['id']}\"");
	if(mysql_num_rows($q) > 0) return 'complete';

	return 'incomplete';
}

function judge_status_update(&$u)
{
	global $config;

	if(   user_personal_info_status($u) == 'complete'
	   && judge_status_expertise($u) == 'complete' 
	   && judge_status_other($u) == 'complete'
	   && judge_status_availability($u) == 'complete'
	   && judge_status_special_awards($u) == 'complete')
		$u['judge_complete'] = 'yes';
	else
		$u['judge_complete'] = 'no';

	user_save($u);
	return ($u['judge_complete'] == 'yes') ? 'complete' : 'incomplete';
}


?>
