<?
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');
 require("../lpdf.php");
 require("../lcsv.php");

 if($_GET['year']) $foryear=$_GET['year'];
 else $foryear=$config['FAIRYEAR'];

 if($_GET['awardtype']=="All") $awardtype="";
 else if($_GET['awardtype']) $awardtype=" AND award_types.type='".mysql_escape_string($_GET['awardtype'])."'";
 else $awardtype="";

 if($_GET['show_unawarded_awards']=="on") $show_unawarded_awards="yes";
 else $show_unawarded_awards="no";

 if($_GET['show_unawarded_prizes']=="on") $show_unawarded_prizes="yes";
 else $show_unawarded_prizes="no";

 $show_pronunciation= ($_GET['show_pronunciation'] == 'on') ? TRUE : FALSE;
 $group_by_prize= ($_GET['group_by_prize'] == 'on') ? true : false;

 if(is_array($_GET['show_category'])) {
	 $show_category = array();
 	foreach($_GET['show_category'] as $id=>$val) {
		$show_category[] = "award_awards_projectcategories.projectcategories_id='$id'";
	}
	$and_categories = join(' OR ', $show_category);
 } else {
 	$and_categories = '1';
 }

 $show_criteria = ($_GET['show_criteria']=='on') ? true : false;

 $type=$_GET['type'];
 if(!$type) $type="pdf";

$scriptformat=$_GET['scriptformat'];
if(!$scriptformat) $scriptformat="default";

	if($type=="pdf") {
		$rep=new lpdf(	i18n($config['fairname']),
				i18n("Awards Ceremony Script"),
				$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
				);

		$rep->newPage();
		if($scriptformat=="default") $rep->setFontSize(12);
		if($scriptformat=="formatted") $rep->setFontSize(14);
	}
	else if($type=="csv") {
		$rep=new lcsv(i18n("Awards Ceremony Script"));
	}
	$q=mysql_query("SELECT 
				award_awards.id,
				award_awards.name,
				award_awards.presenter,
				award_awards.description,
				award_awards.criteria,
				award_awards.order AS awards_order,
				award_types.type,
				sponsors.organization
			FROM 
				award_awards,
				award_types,
				sponsors,
				award_awards_projectcategories
			WHERE 
					award_awards.year='$foryear'
				AND	award_types.year='$foryear'
				AND	award_awards.award_types_id=award_types.id
				AND	award_awards.sponsors_id=sponsors.id
				AND     award_awards.id=award_awards_projectcategories.award_awards_id
				AND	award_awards.excludefromac='0'
				AND ($and_categories)
				$awardtype
			GROUP BY award_awards.id
			ORDER BY awards_order");

	echo mysql_error();
//	echo "<pre>";
	if(!mysql_num_rows($q)) {
		$rep->output();
		exit;
	}
	$awards = array();

	while($r=mysql_fetch_object($q)) {

		$pq=mysql_query("SELECT 
						award_prizes.prize,
						award_prizes.number,
						award_prizes.id,
						award_prizes.cash,
						award_prizes.scholarship,
						winners.projects_id,
						projects.projectnumber,
						projects.title,
						projects.projectcategories_id,
						projects.registrations_id AS reg_id
					FROM 
						award_prizes 
						LEFT JOIN winners ON winners.awards_prizes_id=award_prizes.id
						LEFT JOIN projects ON projects.id=winners.projects_id
					WHERE 
						award_awards_id='{$r->id}' 
						AND award_prizes.year='$foryear'
						AND award_prizes.excludefromac='0'
					ORDER BY 
						`order`,
						projects.projectnumber");
					echo mysql_error();

		$r->winners = array();
		$r->awarded_count = 0;
		while($w = mysql_fetch_object($pq)) {
			if($w->projects_id)
			{
				$r->awarded_count++;
			}
			if($r->type == 'Divisional' && $group_by_prize==true) {
				/* Search awards for an award name that matches this prize */
				$found = false;
				foreach($awards as &$p_award) {
					if($p_award->name == $w->prize) {
						/* Match!  Set the prize name to the award name, 
						 * and add the prize to the award */
						$w->prize = $r->name;
						$p_award->winners[] = $w;
						$found = true;
//						echo "Add to award {$p_award->name}: ";		print_r($w);
						break;
					}
				}
				if($found == false) {
					/* Make a new award and set it equal to the prize name */
					$n = $r->name;
					$new_award = clone($r);
					$new_award->name = $w->prize;
					/* Now add the prize with the award's name */
					$w->prize = $n;
					$new_award->winners[] = $w;
					$awards[] = $new_award;
//					echo "Create Award:"; print_r($new_award);
				}

			} else {
//				echo "Add non-div winner\n";
				$r->winners[] = $w;
			}
		}

		if($show_unawarded_awards=="no" && $r->awarded_count == 0)  {
			/* No winners */
			continue;
		}


		if($r->type == 'Divisional' && $group_by_prize == true) {
			/* Do nothing */
		} else {
			$awards[] = $r;
		}
	}
//	echo '<pre>';	print_r($awards);

	foreach($awards as $r) {

		if($scriptformat=="formatted") 
			$rep->newPage();

		if($scriptformat=="default") 
			$rep->heading("$r->name  ($r->type)");
		if($scriptformat=="formatted") {
			$rep->setFontBold();
			$rep->addText("$r->name  ($r->type)");
			$rep->setFontNormal();
		}
		if($r->type!="Divisional")
			$rep->addText(i18n("Sponsored by: %1",array($r->organization)));
		if($r->presenter)
			$rep->addText(i18n("Presented by: %1",array($r->presenter)));
		if($r->description)
			$rep->addText(i18n("Description: %1",array($r->description)));
		if($show_criteria)
			$rep->addText(i18n("Criteria: %1",array($r->criteria)));

		if($scriptformat=="formatted") $rep->nextline();

		if($r->awarded_count == 0)
		{
			$rep->addText("Not awarded");
		}

		$prevprizeid=-1;

		foreach($r->winners as $pr) {

				if($pr->projectnumber || $show_unawarded_prizes=="yes") {
					if($prevprizeid!=$pr->id) {
						$prizetext=$pr->prize;
	
						if($pr->cash || $pr->scholarship) {
							$prizetext.=" (";
							if($pr->cash && $pr->scholarship)
								$prizetext.="\$$pr->cash cash / \$$pr->scholarship scholarship";
							else if($pr->cash)
								$prizetext.= "\$$pr->cash cash";
							else if($pr->scholarship)
								$prizetext.= "\$$pr->scholarship scholarship";
							$prizetext.= ")";
					
						}
						if($scriptformat=="default") 
							$rep->addText($prizetext);
						if($scriptformat=="formatted") {
							$rep->setFontBold();
							$rep->addText($prizetext);
							$rep->setFontNormal();
							$rep->nextline();
						}
	
						$prevprizeid=$pr->id;
					}

					if($pr->projectnumber) {
						if($scriptformat=="default") 
							$rep->addText( "    ($pr->projectnumber) $pr->title");

						$sq=mysql_query("SELECT students.firstname,
									students.lastname,
									students.pronunciation,
									students.schools_id,
									schools.school
								FROM
									students,
									schools
								WHERE
									students.registrations_id='$pr->reg_id'
									AND students.schools_id=schools.id
								");
	
						$students="       Students: ";
						$studnum=0;
						$pronounce = "";
						$rawpronounce = "";
						while($studentinfo=mysql_fetch_object($sq)) {
							if($studnum>0) $students.=", ";
							$students.="$studentinfo->firstname $studentinfo->lastname";

							if($studnum>0) $pronounce .= ", ";
							$pronounce .= "\"{$studentinfo->pronunciation}\"";
							$rawpronounce .= "{$studentinfo->pronunciation}";

							$student_winner[$studnum] = "$studentinfo->firstname $studentinfo->lastname";
							$student_win_pronunc[$studnum] = "$studentinfo->pronunciation";
							$student_school[$studnum] = $studentinfo->school;
							$studnum++;
						}

						if($scriptformat=="default") {
							$rep->addText($students);
							if(trim($rawpronounce) != "" && $show_pronunciation == TRUE)
								$rep->addText("       Pronunciation: $pronounce");
							$rep->addText("       School: {$student_school[0]}");
						}
						if($scriptformat=="formatted") {
							$rep->addTextX("$pr->projectnumber",0.5);
							for($x=0; $x<$studnum; $x++) {
								$rep->addTextX($student_winner[$x],1.4);
								$rep->addTextX($student_school[$x],5.5);
								if($show_pronunciation == TRUE && $student_win_pronunc[$x]) {
									$rep->nextline();
									$rep->addTextX("({$student_win_pronunc[$x]})",2.0);
								}
								if($type=="pdf") 
									$rep->nextline();
							}
							if(($studnum==1) && ($type == "csv")) $rep->addTextX("");
							if(($studnum==1) && ($type == "csv")) $rep->addTextX("");
							$rep->addText($pr->title,'left', 1.4);
							if($type=="pdf") $rep->nextline();
							$rep->nextline();
						}
					}
					else {
						$rep->addText("    Prize not awarded");
					}
				}
			}
			$rep->nextLine();
		}

	$rep->output();
?>
