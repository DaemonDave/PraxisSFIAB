<?
	$mailqueries=array(
		"committee_all"=>array("name"=>"Committee members (all)","query"=>
			"SELECT firstname, lastname, organization, email FROM users WHERE types LIKE '%committee%' AND deleted='no' GROUP BY uid"),

		/* The WHERE clause evaluates which rows to add to the GROUP
		BY, the HAVING clase evaluates which grouped rows show up.  We
		want to to evaluate 'deleted' AFTER the grouping, so we catch
		the case where the MAX(year) has deleted='yes'.  If we use WHERE
		deleted='no', we'll only add non-deleted rows to the group, and
		end up picking up a user active in, say 2007 and 2008, but
		deleted in 2009. */
		"judges_all"=>array("name"=>"Judges from all years (except deleted judges)","query"=>
			"SELECT firstname, lastname, email, deleted, MAX(year) 
				FROM users WHERE types LIKE '%judge%' GROUP BY uid HAVING deleted='no' ORDER BY email"),

		"judges_active_thisyear"=>array("name"=>"Judges active for this year", "query"=>
			"SELECT firstname, lastname, email FROM users LEFT JOIN users_judge ON users_judge.users_id=users.id WHERE types LIKE '%judge%' AND year='{$config['FAIRYEAR']}' AND deleted='no' AND users_judge.judge_active='yes' ORDER BY email"),

		"judges_inactive"=>array("name"=>"Judges not active for this year", "query"=>
			"SELECT firstname, lastname, email, judge_active, deleted, MAX(year) 
				FROM users LEFT JOIN users_judge ON users_judge.users_id=users.id 
				WHERE types LIKE '%judge%'
				GROUP BY uid HAVING  deleted='no' AND ((max(year)='{$config['FAIRYEAR']}' AND judge_active='no') OR max(year)<'{$config['FAIRYEAR']}')
				ORDER BY email"),

		"judges_active_complete_thisyear"=>array("name"=>"Judges active for this year and complete", "query"=>
			"SELECT firstname, lastname, email FROM users LEFT JOIN users_judge ON users_judge.users_id=users.id WHERE types LIKE '%judge%' AND year='{$config['FAIRYEAR']}' AND users_judge.judge_complete='yes' AND deleted='no' AND users_judge.judge_active='yes' ORDER BY email"),

		"judges_active_incomplete_thisyear"=>array("name"=>"Judges active for this year but not complete", "query"=>
			"SELECT firstname, lastname, email FROM users LEFT JOIN users_judge ON users_judge.users_id=users.id WHERE types LIKE '%judge%' AND year='{$config['FAIRYEAR']}' AND users_judge.judge_complete='no' AND deleted='no' AND users_judge.judge_active='yes' ORDER BY email"),

		"participants_complete_thisyear"=>array("name"=>"Participants complete this year","query"=>
			"SELECT firstname, lastname, students.email FROM students,registrations WHERE students.registrations_id=registrations.id AND registrations.year='".$config['FAIRYEAR']."' AND ( registrations.status='complete' OR registrations.status='paymentpending') ORDER BY students.email"),

		"participants_complete_paymentpending_thisyear"=>array("name"=>"Participants complete this year but payment pending","query"=>
			"SELECT firstname, lastname, students.email FROM students,registrations WHERE students.registrations_id=registrations.id AND registrations.year='".$config['FAIRYEAR']."' AND registrations.status!='complete' AND registrations.status='paymentpending' ORDER BY students.email"),

		"participants_notcomplete_thisyear"=>array("name"=>"Participants not complete this year","query"=>
			"SELECT firstname, lastname, students.email FROM students,registrations WHERE students.registrations_id=registrations.id AND registrations.year='".$config['FAIRYEAR']."' AND registrations.status!='complete' AND registrations.status!='new' ORDER BY students.email"),

		"participants_complete_lastyear"=>array("name"=>"Participants complete last year","query"=>
			"SELECT firstname, lastname, students.email FROM students,registrations WHERE students.registrations_id=registrations.id AND registrations.year='".($config['FAIRYEAR']-1)."' AND ( registrations.status='complete' OR registrations.status='paymentpending') ORDER BY students.email"),

		"participants_complete_allyears"=>array("name"=>"Participants complete all years","query"=>
			"SELECT DISTINCT firstname, lastname, students.email FROM students,registrations WHERE students.registrations_id=registrations.id AND ( registrations.status='complete' OR registrations.status='paymentpending') ORDER BY students.email"),

		"participants_cwsf_thisyear"=>array("name"=>"CWSF Winners for this year","query"=>"
		SELECT DISTINCT students.firstname, students.lastname, students.email 
			FROM award_awards
			JOIN award_prizes ON award_prizes.award_awards_id=award_awards.id
			JOIN winners ON winners.awards_prizes_id=award_prizes.id
			JOIN projects ON winners.projects_id=projects.id
			JOIN registrations ON projects.registrations_id=registrations.id
			JOIN students ON students.registrations_id=registrations.id
			WHERE award_awards.cwsfaward='1' AND winners.year='".$config['FAIRYEAR']."'
			ORDER BY students.email"),

		"sponsors"=>array("name"=>"Organization sponsors","query"=>
			"SELECT id, organization, email FROM sponsors WHERE email!='' ORDER BY email"),

		"sponsors_primarycontacts"=>array("name"=>"Organization sponsors (primary contacts)","query"=>
			"SELECT uid, MAX(users.year) AS year, sponsors.organization, users.firstname, users.lastname, users.email, deleted, users_sponsor.primary 
                FROM sponsors, 
                        users_sponsor, 
                        users 
                WHERE 
                    users.id=users_sponsor.users_id
                    AND users_sponsor.sponsors_id=sponsors.id
                    AND users.types LIKE '%sponsor%'
                    AND users.email!=''
                GROUP BY uid 
                HAVING deleted='no' AND users_sponsor.primary='yes'
                ORDER BY users.email
                "),

		"sponsors_allcontacts"=>array("name"=>"Organization sponsors (all contacts)","query"=>
			"SELECT DISTINCT(users.email), sponsors.organization, users.firstname, users.lastname, users.email 
                FROM sponsors, 
                        users_sponsor, 
                        users 
                WHERE 
                    users.id=users_sponsor.users_id
                    AND users_sponsor.sponsors_id=sponsors.id
                    AND users.types LIKE '%sponsor%'
                    AND users.deleted='no'
                    AND users.email!=''
                ORDER BY users.email
                "),

		"sponsors_specialawards"=>array("name"=>"Organization sponsors for Special Awards","query"=>
			"SELECT DISTINCT sponsors.id, organization, email 
			FROM sponsors 
			JOIN award_awards ON sponsors.id=award_awards.sponsors_id
			WHERE 
				email!='' 
				AND award_awards.award_types_id=2
			ORDER BY email"),


		"sponsors_primarycontacts_specialawards"=>array("name"=>"Organization sponsors for Special Awards (primary contacts)","query"=>
			"SELECT DISTINCT uid, MAX(users.year) AS year, sponsors.organization, users.firstname, users.lastname, users.email, deleted, users_sponsor.primary 
                FROM sponsors, 
                        users_sponsor, 
                        users,
						award_awards 
                WHERE 
                    users.id=users_sponsor.users_id
                    AND users_sponsor.sponsors_id=sponsors.id
                    AND users.types LIKE '%sponsor%'
                    AND users.email!=''
					AND sponsors.id=award_awards.sponsors_id
					AND award_awards.award_types_id=2
                GROUP BY uid 
                HAVING deleted='no' AND users_sponsor.primary='yes'
                ORDER BY users.email
                "),

		"sponsors_allcontacts_specialawards"=>array("name"=>"Organization sponsors for Special Awards (all contacts)","query"=>
			"SELECT DISTINCT(users.email), sponsors.organization, users.firstname, users.lastname, users.email 
                FROM sponsors, 
                        users_sponsor, 
                        users,
						award_awards 
                WHERE 
                    users.id=users_sponsor.users_id
                    AND users_sponsor.sponsors_id=sponsors.id
                    AND users.types LIKE '%sponsor%'
                    AND users.deleted='no'
                    AND users.email!=''
					AND sponsors.id=award_awards.sponsors_id
					AND award_awards.award_types_id=2
                ORDER BY users.email
                "),

/*
		"special_award_sponsors_unconfirmed"=>array("name"=>"Special award sponsors (unconfirmed only)","query"=>
			"SELECT DISTINCT(award_sponsors.id), organization, firstname, lastname, award_contacts.email FROM award_sponsors, award_awards, award_contacts WHERE award_awards.sponsors_id=award_sponsors.id AND award_contacts.award_sponsors_id=award_sponsors.id AND award_sponsors.confirmed='no' AND award_awards.award_types_id='2' AND award_contacts.year='".$config['FAIRYEAR']."'"),

		"special_award_sponsors_all"=>array("name"=>"Special award sponsors (all)","query"=>
			"SELECT DISTINCT(award_sponsors.id), organization, firstname, lastname, award_contacts.email FROM award_sponsors, award_awards, award_contacts WHERE award_awards.sponsors_id=award_sponsors.id AND award_contacts.award_sponsors_id=award_sponsors.id AND award_awards.award_types_id='2' AND award_contacts.year='".$config['FAIRYEAR']."'"),

*/
		"school_principals"=>array("name"=>"School principals","query"=>
			"SELECT schools.principal_uid AS uid, schools.school, users.firstname AS firstname, users.lastname AS lastname, users.email AS email FROM schools 
			JOIN users ON schools.principal_uid=users.uid AND users.id=(SELECT id FROM users WHERE users.uid=schools.principal_uid ORDER BY `year` DESC LIMIT 1)
				WHERE schools.year='".$config['FAIRYEAR']."' AND users.email!=''"),

		"school_scienceheads"=>array("name"=>"School science heads","query"=>
			"SELECT schools.sciencehead_uid AS uid, schools.school, users.firstname AS firstname, users.lastname AS lastname, users.email AS email FROM schools 
			JOIN users ON schools.sciencehead_uid=users.uid AND users.id=(SELECT id FROM users WHERE users.uid=schools.sciencehead_uid ORDER BY `year` DESC LIMIT 1)
				WHERE schools.year='".$config['FAIRYEAR']."' AND users.email!=''"),

		"school_teachers_thisyear"=>array("name"=>"Teachers (as entered by students) this year","query"=>
			"SELECT DISTINCT(teacheremail) AS email, teachername AS firstname FROM students WHERE year='".$config['FAIRYEAR']."' AND teacheremail!=''"),

		"school_teachers_lastyear"=>array("name"=>"Teachers (as entered by students) last year","query"=>
			"SELECT DISTINCT(teacheremail) AS email, teachername AS firstname FROM students WHERE year='".($config['FAIRYEAR']-1)."' AND teacheremail!=''"),

		"school_teachers_allyears"=>array("name"=>"Teachers (as entered by students) all years","query"=>
			"SELECT DISTINCT(teacheremail) AS email, teachername AS firstname FROM students WHERE teacheremail!=''"),
/* Volunteers */
		"volunteers_active_complete_thisyear"=>array("name"=>"Volunteers active for this year and complete", "query"=>
			"SELECT id, firstname, lastname, email FROM users LEFT JOIN users_volunteer ON users_volunteer.users_id=users.id WHERE users.year='{$config['FAIRYEAR']}' AND users_volunteer.volunteer_complete='yes' AND users_volunteer.volunteer_active='yes' AND users.deleted='no' AND types LIKE '%volunteer%' ORDER BY email"),

		"volunteers_active_incomplete_thisyear"=>array("name"=>"Volunteers active for this year but not complete", "query"=>
			"SELECT id, firstname, lastname, email FROM users LEFT JOIN users_volunteer ON users_volunteer.users_id=users.id WHERE users.year='{$config['FAIRYEAR']}' AND users_volunteer.volunteer_complete='no' AND users_volunteer.volunteer_active='yes' AND users.deleted='no' AND users.types LIKE '%volunteer%' ORDER BY email"),

		);
?>
