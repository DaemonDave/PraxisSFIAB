<?

function judges_scheduler_check_timeslots()
{
	global $config;

	$q=mysql_query("SELECT * FROM judges_timeslots WHERE ".
		" year='".$config['FAIRYEAR']."'".
		" AND `type`='divisional1'" );
	if(mysql_num_rows($q)) {
		$round=mysql_fetch_object($q);
		$q=mysql_query("SELECT * FROM judges_timeslots WHERE round_id='$round->id' AND type='timeslot'");
		return mysql_num_rows($q);
	}
	else
		return 0;
}

function judges_scheduler_check_timeslots_sa()
{
	global $config;
	$rows = 0;

	$q=mysql_query("SELECT * FROM judges_timeslots WHERE ".
		" year='".$config['FAIRYEAR']."'".
		" AND `type`='special'" );
	if(mysql_num_rows($q)) {
		while((	$round=mysql_fetch_object($q))) {
			$rq=mysql_query("SELECT * FROM judges_timeslots WHERE round_id='$round->id' AND type='timeslot'");
			$rows += mysql_num_rows($rq);
		}
	}
	return $rows;
}

function judges_scheduler_check_awards()
{
	global $config;

	$q=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
	while($r=mysql_fetch_object($q))
		$div[$r->id]=$r->division;

	$q=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
	while($r=mysql_fetch_object($q))
		$cat[$r->id]=$r->category;

	$dkeys = array_keys($div);
	$ckeys = array_keys($cat);

	if($config['filterdivisionbycategory']=="yes") {
		$q=mysql_query("SELECT * FROM projectcategoriesdivisions_link WHERE year='".$config['FAIRYEAR']."' ORDER BY projectdivisions_id,projectcategories_id");
		$divcat=array();
		while($r=mysql_fetch_object($q)) {
			$divcat[]=array("c"=>$r->projectcategories_id,"d"=>$r->projectdivisions_id);
		}

	}
	else {
		$divcat=array();
		foreach($dkeys AS $d) {
			foreach($ckeys AS $c) {
				$divcat[]=array("c"=>$c,"d"=>$d);
			}
		}
	}


	$missing_awards = array();
	foreach($divcat AS $dc) {
		$d=$dc['d'];
		$c=$dc['c'];
			$q=mysql_query("SELECT award_awards.id FROM 
							award_awards,
							award_awards_projectcategories,
							award_awards_projectdivisions
						WHERE 
							award_awards.year='{$config['FAIRYEAR']}'
							AND award_awards_projectcategories.year='{$config['FAIRYEAR']}'
							AND award_awards_projectdivisions.year='{$config['FAIRYEAR']}'
							AND award_awards.id=award_awards_projectcategories.award_awards_id
							AND award_awards.id=award_awards_projectdivisions.award_awards_id
							AND award_awards_projectcategories.projectcategories_id='$c'
							AND award_awards_projectdivisions.projectdivisions_id='$d'
							AND award_awards.award_types_id='1'
						");
						echo mysql_error();
			if(mysql_num_rows($q)!=1) {
				$missing_awards[] = "{$cat[$c]} - {$div[$d]} (".i18n("%1 found",array(mysql_num_rows($q))).")";
			}
	}
	return $missing_awards;
}


function judges_scheduler_check_jdivs()
{
	global $config;

	$q=mysql_query("SELECT DISTINCT jdiv_id FROM judges_jdiv ");
	$rows = mysql_num_rows($q);

	return $rows;
}


function judges_scheduler_check_judges()
{
	global $config;
	$ok = 1;

	$jdiv = array();
	$q=mysql_query("SELECT * FROM judges_jdiv ORDER BY jdiv_id");
	while($r=mysql_fetch_object($q)) {
		/* Ignore any div/cat with jdiv 0 */
		if($r->jdiv_id == 0) continue;

		$d = $r->projectdivisions_id;
		$c = $r->projectcategories_id;
		$l = $r->lang;

		$qp = mysql_query("SELECT COUNT(projects.id) as cnt FROM projects, registrations WHERE ".
						" projects.year='".$config['FAIRYEAR']."' AND ".
						" projectdivisions_id='$d' AND ".
						" projectcategories_id='$c' AND ".
						" language='$l' AND " .
						" registrations.id = projects.registrations_id " .
						getJudgingEligibilityCode()
				);
		$qr = mysql_fetch_object($qp);
		
		$jdiv[$r->jdiv_id]['num_projects']['total'] += $qr->cnt;
		$jdiv[$r->jdiv_id]['num_projects'][$l] += $qr->cnt;

        $projectlanguagetotal[$l]+=$qr->cnt;
        $projecttotal+=$qr->cnt;
		
	}

	$totalteams['total'] = 0;
	echo "<table border=1 width=\"85%\"><tr><th></th>".
		"<th colspan=\"".(count($config['languages'])+1)."\">".i18n("Projects")."</th>".
		"<th colspan=\"".(count($config['languages'])+1)."\">".i18n("Estimated Required Teams")."</th></tr>";

        echo "<tr>";
        echo "<th></th><th>".i18n("Total")."</th>";
        foreach($config['languages'] AS $lkey=>$lname)
            echo "<th>$lkey</th>";
        echo "<th>".i18n("Total")."</th>";
        foreach($config['languages'] AS $lkey=>$lname)
            echo "<th>$lkey</th>";
        echo "</tr>\n";

		foreach($jdiv AS $jdiv_id=>$jd) {
			$c = $jd['num_projects']['total'];

			//total judge teams calculation
			$t['total']=ceil($c/$config['max_projects_per_team']*$config['times_judged']);
			if($t['total'] < $config['times_judged'] && $c>0) $t['total'] = $config['times_judged'];
			$jdiv[$jdiv_id]['num_jteams']['total'] = $t['total'];
			$totalteams['total']+=$t['total'];
			//language teams calculation
			foreach($config['languages'] AS $lkey=>$lname) {
				$c = $jd['num_projects'][$lkey];
				$t['total_'.$lkey]=ceil($c/$config['max_projects_per_team']*$config['times_judged']);
				if($t['total_'.$lkey] < $config['times_judged'] && $c>0) $t['total_'.$lkey] = $config['times_judged'];
				$jdiv[$jdiv_id]['num_jteams']['total_'.$lkey] = $t['total_'.$lkey];
				$totalteams['total_'.$lkey]+=$t['total_'.$lkey];
			}

			echo "<tr><td>Judging Division Group $jdiv_id</td>";
			echo "<td align=\"center\">$c</td>";
			$langstr="";
			foreach($config['languages'] AS $lkey=>$lname) {
				$clang=($jd['num_projects'][$lkey]?$jd['num_projects'][$lkey]:0);
				echo "<td align=\"center\">$clang</td>";
        }
        echo "<td align=\"center\">{$t['total']}</td>";
		foreach($config['languages'] AS $lkey=>$lname) {
			$clang=($jd['num_projects'][$lkey]?$jd['num_projects'][$lkey]:0);
			//echo "<td align=\"center\">{$t['total']}</td>";
			echo "<td align=\"center\">{$t['total_'.$lkey]}</td>";
		}

        echo "</tr>";
	}
	echo "</table>";

	echo "<br />";
	echo "<b>";
	echo i18n("Total judging teams required: %1",array($totalteams['total']));
	echo "<br />";
	echo "<br />";
	$minjudges['total']=($totalteams['total']*$config['min_judges_per_team']);
	$maxjudges['total']=($totalteams['total']*$config['max_judges_per_team']);
	echo i18n("Minimum number of judges required: %1",array($minjudges['total']))."<br />";

    foreach($config['languages'] AS $lkey=>$lname) {
        if($minjudges['total'] && $projecttotal)
            $minjudges[$lkey]=round($totalteams['total_'.$lkey]*$config['min_judges_per_team']); //$projectlanguagetotal[$lkey]/$projecttotal*$minjudges['total']);
        else
            $minjudges[$lkey]=0;

        echo "&nbsp;&nbsp; ".i18n("Minimum number of %1 judges required: %2",array($lname,$minjudges[$lkey]))."<br />";
    }

	echo i18n("Maximum number of judges needed: %1",array($maxjudges['total']));
	echo "<br />";
	echo "<br />";

/*	$jq=mysql_query("SELECT COUNT(judges.id) AS num FROM judges,judges_years WHERE complete='yes' AND deleted='no' AND judges_years.year='{$config['FAIRYEAR']}' AND judges_years.judges_id=judges.id");
	$jr=mysql_fetch_object($jq);
	$currentjudges=$jr->num;*/
	/* FIXME: this his highly inefficient :), but won't be done very often */
	$judges = judges_load_all();
	$currentjudges = count($judges);
	echo "Current number of registered judges: $currentjudges";
	echo "</b>";
	echo "<br />";
	if($currentjudges<$minjudges['total']) {
		echo error(i18n("You do not have sufficient number of total judges based on your parameters"));
        $ok=false;
    }

    foreach($config['languages'] AS $lkey=>$lname) {
		$lcount=0;
		foreach($judges AS $j) {
			foreach($j['languages'] AS $jlang) {
				if($jlang==$lkey) $lcount++;
			}
		}

        $currentjudges=$lcount;
        echo "&nbsp;&nbsp;<b>".i18n("Current number of registered judges that can judge in %1: %2",array($lname,$currentjudges))."</b>";
        echo "<br />";
        if($currentjudges<$minjudges[$lkey]) {
            echo error(i18n("You do not have sufficient number of %1 judges based on your parameters",array($lname)));
            $ok=false;
        }

    }

    if(!$ok) {
		echo "&nbsp;&nbsp;";
		echo "<a href=\"judges_schedulerconfig.php\">".i18n("Update Scheduler Configuration")."</a> (".i18n("or get more judges!").")";
	}
	else
		echo happy(i18n("You have a sufficient number of judges based on your parameters"));

	//now check if we can find a divisional award for each division and category
	return $ok;
}

?>
