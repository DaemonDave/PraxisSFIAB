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
function getProjectsEligibleForAward($award_id)
{
	global $config;

	$prjq=mysql_query("SELECT
				award_awards.id,
				award_awards_projectcategories.projectcategories_id, 
				award_awards_projectdivisions.projectdivisions_id,
				projects.projectnumber,
				projects.title,
				projects.id AS projects_id,
				projects.fairs_id
			FROM
				award_awards,
				award_awards_projectcategories,
				award_awards_projectdivisions,
				projects
			WHERE
				award_awards.id='$award_id'
				AND award_awards.id=award_awards_projectcategories.award_awards_id
				AND award_awards.id=award_awards_projectdivisions.award_awards_id
				AND projects.projectcategories_id=award_awards_projectcategories.projectcategories_id
				AND projects.projectdivisions_id=award_awards_projectdivisions.projectdivisions_id
				AND projects.projectnumber is not null
				AND projects.year='".$config['FAIRYEAR']."'
			ORDER BY
				projectsort
				");
	$projects=array();
	while($prjr=mysql_fetch_object($prjq))
	{
		$projects[$prjr->projectnumber]=array(
					"id"=>$prjr->projects_id,
					"projectnumber"=>$prjr->projectnumber,
					"title"=>$prjr->title,
					"fairs_id"=>$prjr->fairs_id
				);
	}
	return $projects;
}

function getProjectsEligibleOrNominatedForAwards($awards_ids_array)
{
	$projects=array();
	foreach($awards_ids_array AS $award_id)
	{
		$q=mysql_query("SELECT award_types.type FROM award_awards, award_types WHERE award_awards.id='$award_id' AND award_awards.award_types_id=award_types.id");
		$r=mysql_fetch_object($q);

		$awardprojects=array();

		//for special awards, we only want the ones that were nominated for it.
		//for everything else, we weant all the eligible projects
		if($r->type=="Special")
			$awardprojects=getProjectsNominatedForSpecialAward($award_id);
		else
			$awardprojects=getProjectsEligibleForAward($award_id);

//		$projects[$award_id]=$awardprojects;

		//this will just overwrite ones that already exist, but still keep things in order because the main key is the projectnumber (i hope)
		foreach($awardprojects AS $proj)
			$projects[$proj['projectnumber']]=$proj;
	}

	return $projects;
}

function getSpecialAwardsEligibleForProject($projectid)
{
	global $config;

	$awardsq=mysql_query("SELECT
				award_awards.id,
				award_awards.name,
				award_awards.criteria,
				award_awards_projectcategories.projectcategories_id, 
				award_awards_projectdivisions.projectdivisions_id,
				projects.id AS projects_id,
				award_awards.self_nominate
			FROM
				award_awards,
				award_awards_projectcategories,
				award_awards_projectdivisions,
				award_types,
				projects
			WHERE
				award_types.type='Special'
				AND award_types.id=award_awards.award_types_id
				AND award_awards.id=award_awards_projectcategories.award_awards_id
				AND award_awards.id=award_awards_projectdivisions.award_awards_id
				AND projects.projectcategories_id=award_awards_projectcategories.projectcategories_id
				AND projects.projectdivisions_id=award_awards_projectdivisions.projectdivisions_id
				AND award_awards.id is not null
				AND projects.year='".$config['FAIRYEAR']."'
				AND projects.id='$projectid'
				AND award_types.year='".$config['FAIRYEAR']."'
				AND award_awards.year='".$config['FAIRYEAR']."'
			ORDER BY
				award_awards.name
				");
	$awards=array();
	 echo mysql_error();
	while($r=mysql_fetch_object($awardsq))
	{
		$awards[$r->id]=array(
					"id"=>$r->id,
					"criteria"=>$r->criteria,
					"name"=>$r->name,
					"self_nominate"=>$r->self_nominate
				);
	}
	return $awards;
}

function getSpecialAwardsNominatedForProject($projectid)
{
	global $config;

	$awardsq=mysql_query("SELECT
				award_awards.id,
				award_awards.name,
				award_awards.criteria,
				projects.id AS projects_id,
				projects.fairs_id
			FROM
				award_awards,
				project_specialawards_link,
				projects
			WHERE
				project_specialawards_link.projects_id='$projectid'
				AND project_specialawards_link.award_awards_id=award_awards.id
				AND projects.year='".$config['FAIRYEAR']."'
				AND projects.id='$projectid'
			ORDER BY
				award_awards.name
				");
	$awards=array();
	 echo mysql_error();
	while($r=mysql_fetch_object($awardsq))
	{
		$awards[$r->id]=array(
					"id"=>$r->id,
					"criteria"=>$r->criteria,
					"name"=>$r->name,
					"fairs_id"=>$r->fairs_id
				);
	}
	return $awards;
}

function getNominatedForNoSpecialAwardsForProject($projectid)
{
	global $config;
	$awardsq=mysql_query("SELECT
				projects.id AS projects_id
			FROM
				project_specialawards_link,
				projects
			WHERE
				project_specialawards_link.projects_id='$projectid'
				AND projects.year='".$config['FAIRYEAR']."'
				AND projects.id='$projectid'
				AND project_specialawards_link.award_awards_id IS NULL
				");
	if(mysql_num_rows($awardsq) == 1) return true;
	return false;	
}

function getProjectsNominatedForSpecialAward($award_id)
{
	global $config;

	//if they dont use special award nominations, then we will instead get all of the projects that
	//are eligible for the award, instead of nominated for it.
	if($config['specialawardnomination']!="none")
	{
		$prjq=mysql_query("SELECT
					projects.projectnumber,
					projects.title,
					projects.id AS projects_id
				FROM
					project_specialawards_link,
					projects
				WHERE
					project_specialawards_link.award_awards_id='$award_id'
					AND project_specialawards_link.projects_id=projects.id
					AND projects.projectnumber is not null
					AND projects.year='".$config['FAIRYEAR']."'
				ORDER BY
					projectsort
					");
		$projects=array();
		while($prjr=mysql_fetch_object($prjq))
		{
			$projects[$prjr->projectnumber]=array(
						"id"=>$prjr->projects_id,
						"projectnumber"=>$prjr->projectnumber,
						"title"=>$prjr->title
					);
		}
		//return the projects that have self-nominated themselves for the award
		return $projects;
	}
	else
	{
		//return the projects that are eligible for the award instead
		return getProjectsEligibleForAward($award_id);
	}
}

function getSpecialAwardsNominatedByRegistrationID($id)
{
	global $config;

	$awardq=mysql_query("SELECT
				award_awards.id,
				award_awards.name,
				award_awards_projectcategories.projectcategories_id, 
				award_awards_projectdivisions.projectdivisions_id,
				projects.id AS projects_id
			FROM
				award_awards,
				award_awards_projectcategories,
				award_awards_projectdivisions,
				projects
			WHERE
				award_awards.id='$award_id'
				AND award_awards.id=award_awards_projectcategories.award_awards_id
				AND award_awards.id=award_awards_projectdivisions.award_awards_id
				AND projects.projectcategories_id=award_awards_projectcategories.projectcategories_id
				AND projects.projectdivisions_id=award_awards_projectdivisions.projectdivisions_id
				AND projects.projectnumber is not null
				AND projects.year='".$config['FAIRYEAR']."'
			ORDER BY
				projectsort
				");
	$projects=array();
	while($prjr=mysql_fetch_object($prjq))
	{
		$projects[$prjr->projectnumber]=array(
					"id"=>$prjr->projects_id,
					"projectnumber"=>$prjr->projectnumber,
					"title"=>$prjr->title
				);
	}
	return $projects;

}

function project_load($pid)
{
	/* Load this project */
	$q = mysql_query("SELECT * FROM projects WHERE id='$pid'");
	$proj = mysql_fetch_array($q);

	/* Load the students */
	$q = mysql_query("SELECT students.*,schools.school FROM students 
		LEFT JOIN schools ON schools.id=students.schools_id
		WHERE registrations_id='{$proj['registrations_id']}' AND students.year='{$proj['year']}' ORDER BY students.id");
	$proj['num_students'] = 0;
	while($s = mysql_fetch_assoc($q)) {
		$proj['num_students']++;
		$proj['student'][] = $s;
	}
	return $proj;
}



?>
