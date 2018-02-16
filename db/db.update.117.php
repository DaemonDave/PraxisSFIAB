<?
function db_update_117_post()
{
	global $config;

	$qmap = array('years_school' => 'Years School',
				'years_regional' => 'Years Regional',
				'years_national' => 'Years National',
				'willing_chair' => 'Willing Chair');

	foreach($qmap as $field=>$head) {
		$q = mysql_query("SELECT id FROM questions WHERE db_heading='{$head}'");
		while($i = mysql_fetch_object($q)) {
			$id = $i->id;

			/* Drop all answers for this question */
			mysql_query("DELETE FROM question_answers
					WHERE questions_id='$id'");
		}

		/* Now dump the question itself */
		mysql_query("DELETE FROM questions 
				WHERE id='$id'");

	}
}





