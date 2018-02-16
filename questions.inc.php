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


function questions_load_answers($section, $users_id)
{
	global $config;
	$yearq=mysql_query("SELECT `year` FROM users WHERE id='$users_id'");
	$yearr=mysql_fetch_object($yearq);
	$ans=array();
	$qs=questions_load_questions($section,$yearr->year);
	foreach($qs AS $id=>$question) {
		$q=mysql_query("SELECT * FROM question_answers WHERE users_id='$users_id' AND questions_id='$id'");
		$r=mysql_fetch_object($q);
		$ans[$id]=$r->answer;
	}
	return $ans;
}

function questions_load_questions($section, $year)
{
	$q = mysql_query('SELECT * FROM questions '.
			"WHERE year='$year' ".
			"   AND section='$section'  ".
			'ORDER BY ord ASC');
	print(mysql_error());

	$qs = array();
	while($r=mysql_fetch_object($q)) {
		$qs[$r->id]['id'] = $r->id;
		$qs[$r->id]['ord'] = $r->ord;
		$qs[$r->id]['section'] = $r->section;
		$qs[$r->id]['db_heading'] = $r->db_heading;
		$qs[$r->id]['type'] = $r->type;
		$qs[$r->id]['required'] = $r->required;
		$qs[$r->id]['question'] = $r->question;
	}
	return $qs;
}

function questions_save_answers($section, $id, $answers)
{
   global $config;
	$qs = questions_load_questions($section,$config['FAIRYEAR']);
	$keys = array_keys($answers);
    $q=mysql_query("SELECT * FROM questions WHERE year='{$config['FAIRYEAR']}'");
    while($r=mysql_fetch_object($q)) {
        mysql_query("DELETE FROM question_answers WHERE users_id='$id' AND questions_id='$r->id'");
        echo mysql_error();
    }
		
	$keys = array_keys($answers);
	foreach($keys as $qid) {
		/* Poll key */
		mysql_query("INSERT INTO question_answers
				(users_id,questions_id,answer) VALUES(
				'$id','$qid',
				'".mysql_escape_string($answers[$qid])."')" );
	}
}

function questions_find_question_id($section, $dbheading)
{
	$q = mysql_query("SELECT id FROM questions WHERE ".
				" section='$section' ".
				" AND db_heading='$dbheading' ");
	if(mysql_num_rows($q) == 1) {
		$r = mysql_fetch_object($q);
		return $r->id;
	}
	return 0;
}


function questions_print_answer_editor($section, &$u, $array_name)
{
	$ans = questions_load_answers($section, $u['id']);
	$qs = questions_load_questions($section, $u['year']);
	$keys = array_keys($qs);
	foreach($keys as $qid) {
		print("<tr>\n");
		print(" <td colspan=\"2\">".i18n($qs[$qid]['question'])."</td>\n");
		print(" <td colspan=\"2\">");
		$iname = "{$array_name}[{$qid}]";
		switch($qs[$qid]['type']) {
		case 'yesno':
			if($ans[$qid]=="yes") $ch="checked=\"checked\""; else $ch="";
			print("<input onclick=\"fieldChanged()\" $ch type=\"radio\" name=\"$iname\" value=\"yes\" />".i18n("Yes"));
			print("&nbsp; &nbsp; ");
			if($ans[$qid]=="no") $ch="checked=\"checked\""; else $ch="";
			print("<input onclick=\"fieldChanged()\" $ch type=\"radio\" name=\"$iname\" value=\"no\" />".i18n("No"));
			break;
		case 'int':
			print("<input onclick=\"fieldChanged()\" type=\"text\" ".
				"name=\"$iname\" size=10 maxlen=11 ".
				"value=\"{$ans[$qid]}\" >\n");
			break;
	        case 'check':
        		if($ans[$qid]=="yes") $ch="checked=\"checked\""; else $ch=""; 
		        print("<input $ch type=\"checkbox\" name=\"$iname\" value=\"yes\">\n");
			break;
	        case 'text':
		        print("<input type=\"text\" name=\"$iname\" value=\"{$ans[$qid]}\">\n");
			break;

		}
		print("</td>\n");
		print("</tr>\n");
	}
}

function questions_print_answers($section, $id)
{
    global $config;
	$ans = questions_load_answers($section, $id);
	$qs = questions_load_questions($section,$config['FAIRYEAR']);
	$keys = array_keys($qs);
	foreach($keys as $qid) {
		echo "<tr>\n";
		echo " <th colspan=\"2\">".i18n($qs[$qid]['question'])."</th>\n";
		echo " <td colspan=\"2\">{$ans[$qid]}";
		echo "</tr>\n";
	}
}

function questions_parse_from_http_headers($array_name)
{
	$ans = array();
	if(!is_array($_POST[$array_name])) return $ans;

	$keys = array_keys($_POST[$array_name]);
	foreach($keys as $qid) {
		$ans[$qid] = stripslashes($_POST[$array_name][$qid]);
	}	
	return $ans;
}

function questions_update_question($qs)
{
	mysql_query("UPDATE questions SET 
			`question`='".mysql_escape_string($qs['question'])."',
			`type`='".mysql_escape_string($qs['type'])."',
			`db_heading`='".mysql_escape_string($qs['db_heading'])."',
			`required`='".mysql_escape_string($qs['required'])."',
			`ord`=".intval($qs['ord'])."
			WHERE id='{$qs['id']}' ");
	echo mysql_error();
}

function questions_save_new_question($qs, $year)
{
	mysql_query("INSERT INTO questions ".
		"(question,type,section,db_heading,required,ord,year) VALUES (".
			"'".mysql_escape_string($qs['question'])."',".
			"'".mysql_escape_string($qs['type'])."',".
			"'".mysql_escape_string($qs['section'])."',".
			"'".mysql_escape_string($qs['db_heading'])."',".
			"'".mysql_escape_string($qs['required'])."',".
			"'".mysql_escape_string($qs['ord'])."',".
			"'$year' )");
	echo mysql_error();
}


/* A complete question editor.  Just call it with the
 * section you want to edit, a year, the array_name to use for
 * POSTing and GETting the questions (so you can put more than one
 * edtior on a single page), and give it $_SERVER['PHP_SELF'], because
 * php_self inside this function is this file.
 * FUTURE WORK: it would be nice to hide the order, and just implement
 *  a bunch of up/down arrows, and dynamically compute the order for
 *  all elements */
function questions_editor($section, $year, $array_name, $self) 
{
	global $config;

	if($_POST['action']=="save") {

		$qs = questions_parse_from_http_headers('question');
		$qs['section'] = $section;
		if($qs['question']) {
			$qs['id'] = intval($_POST['save']);
			questions_update_question($qs, $year);
			echo happy(i18n("Question successfully saved"));
		} else {
			echo error(i18n("Question is required"));
		}
	}

	if($_POST['action']=="new") {
		$q = questions_load_questions($section, $year);
		$qs = questions_parse_from_http_headers('question');
		$qs['section'] = $section;
		$qs['ord'] = count($q) + 1;
		if($qs['question']) {
			questions_save_new_question($qs, $year);
			echo happy(i18n("Question successfully added"));
		} else {
			echo error(i18n("Question is required"));
		}
	}

	if($_GET['action']=="remove" && $_GET['remove'])
	{
		$qid = $_GET['remove'];
		$qs = questions_load_questions($section, $year);

		/* Delete this question */
		mysql_query("DELETE FROM questions WHERE id='$qid'");

		/* Update the order of all questions after this one */
		$keys = array_keys($qs);
		foreach($keys as $q) {
			if($q == $qid) continue;
			if($qs[$q]['ord'] > $qs[$qid]['ord']) {
				$qs[$q]['ord']--;
				mysql_query("UPDATE questions SET ord='{$qs[$q]['ord']}' WHERE id='$q'");
			}
		}
		echo happy(i18n("Question successfully removed"));
	}

	if($_GET['action']=="import" && $_GET['impyear'])
	{
		$x=0;
		$q = mysql_query("SELECT * FROM questions WHERE year='{$_GET['impyear']}'");
                while($r=mysql_fetch_object($q)) {
			$x++;
                        mysql_query("INSERT INTO questions (id,year,section,db_heading,question,type,required,ord)
 VALUES (
                                '', '$year',
                                '".mysql_escape_string($r->section)."',
                                '".mysql_escape_string($r->db_heading)."',
                                '".mysql_escape_string($r->question)."',
                                '".mysql_escape_string($r->type)."',
                                '".mysql_escape_string($r->required)."',
                                '".mysql_escape_string($r->ord)."')");
		}

		echo happy(i18n("%1 question(s) successfully imported",
				array($x)));
	}

	/* Load questions, then handle up and down, because with up and down we
	 * have to modify 2 questions to maintain the order */
	$qs = questions_load_questions($section, $year);

	/* Sanity check the order DG -- This is there to fix a bug in the .22
	 * database full import, anyone with a fresh install will see duplicate
	 * order items, anyone who doesn't, won't see this bug.  Anyone who has
	 * already used the default question import will need this piece of
	 * code to return their system to sane.   I have added a db.update
	 * script to fix the bug, but it won't fix any systems where the
	 * questions have already been imported.  This code will. */
	$keys = array_keys($qs);
	$x=1;
	foreach($keys as $qid) {
		if($qs[$qid]['ord'] != $x) {
			$qs[$qid]['ord'] = $x;
			questions_update_question($qs[$qid], $year);
		}
		$x++;
	}


	$qdir = 0;
	if($_GET['action']=="up" && $_GET['up']) {
		$qid = $_GET['up'];
		if($qs[$qid]['ord'] != 1) {
			$qdir = -1;
		}
		
	}
	if($_GET['action']=="down" && $_GET['down']) {
		$qid = $_GET['down'];
		if($qs[$qid]['ord'] != count($qs)) { 
			$qdir = 1;
		}
	}
	if($qdir != 0) {
		$qs[$qid]['ord'] += $qdir;
		/* Update the db */
		mysql_query("UPDATE questions SET ord='{$qs[$qid]['ord']}' WHERE id='$qid'");
		$keys = array_keys($qs);
		$originalq = $qs[$qid];

		foreach($keys as $q) {
			if($q == $qid) continue;
			if($qs[$q]['ord'] != $qs[$qid]['ord']) continue;
			if($qdir == 1) {
				$qs[$q]['ord']--;
				mysql_query("UPDATE questions SET ord='{$qs[$q]['ord']}' WHERE id='$q'");
			} else {
				$qs[$q]['ord']++;
				mysql_query("UPDATE questions SET ord='{$qs[$q]['ord']}' WHERE id='$q'");
			}
			/* Swap them so we don' thave to reaload the questions
			 * */
//			$qs[$qid] = $qs[$q];
//			$qs[$q] = $originalq;
			break;
		}

		/* Reload the questions */
		$qs = questions_load_questions($section, $year);
	}


	if(($_GET['action']=="edit" && $_GET['edit']) || $_GET['action']=="new") {

		$showform=true;
		echo "<form method=\"post\" action=\"$self\">";
		if($_GET['action']=="new")
		{
			$buttontext="Add a question";
			echo "<input type=\"hidden\" name=\"action\" value=\"new\">\n";
		}
		else if($_GET['action']=="edit")
		{
			$buttontext="Save question";
			echo "<input type=\"hidden\" name=\"action\" value=\"save\">\n";
			/* The question ID is passed on the URL */
			$qid = $_GET['edit'];
			/* Load the question */
			$q = $qs[$qid];
			echo "<input type=\"hidden\" name=\"save\" value=\"$qid\">\n";
			if(!is_array($q)) {
				$showform=false;
				echo error(i18n("Invalid question"));
			}
		}
		if($showform)
		{
			echo "<table class=\"summarytable\">";
			echo "<tr><td>".i18n("Question")."</td><td>";
			echo "<input size=\"60\" type=\"text\" name=\"${array_name}[question]\" value=\"".htmlspecialchars($q['question'])."\">\n";
			echo "</td></tr>";
			echo "<tr><td>".i18n("Table Heading")."</td><td>";
			echo "<input size=\"20\" type=\"text\" name=\"${array_name}[db_heading]\" value=\"".htmlspecialchars($q['db_heading'])."\">\n";
			echo "</td></tr>";
			echo "<tr><td>".i18n("Type")."</td><td>";
			echo "<select name=\"${array_name}[type]\">";
			if($q['type']=="check") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"check\">".i18n("Check box")."</option>\n";
			if($q['type']=="yesno") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"yesno\">".i18n("Yes/No")."</option>\n";
			if($q['type']=="text") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"text\">".i18n("Text")."</option>\n";
			if($q['type']=="int") $sel="selected=\"selected\""; else $sel="";
			echo "<option $sel value=\"int\">".i18n("Number")."</option>\n";

			echo "</select>";
			echo "</td>";
			echo "<tr><td>".i18n("Required?")."</td><td>";
			echo "<select name=\"${array_name}[required]\">";
			if($q['required']=="yes") $sel="selected=\"selected\""; else $sel="";
				echo "<option $sel value=\"yes\">".i18n("Yes")."</option>\n";
			if($q['required']=="no") $sel="selected=\"selected\""; else $sel="";
				echo "<option $sel value=\"no\">".i18n("No")."</option>\n";
			echo "</select>";
			echo "</td>";
//			echo "<tr><td>".i18n("Display Order")."</td><td>";
//			echo "<input size=\"5\" type=\"text\" name=\"${array_name}[ord]\" value=\"".htmlspecialchars($q['ord'])."\">\n";
//			echo "</td></tr>";
			echo "<tr><td colspan=\"2\" align=\"center\">";
			echo "<input type=\"submit\" value=\"".i18n("Save Question")."\" />\n";
			echo "</td></tr>";
			echo "</table>";
			echo "</form>";
			echo "<br />";
			echo "<hr />";
		}
	} else {
	}
	echo "<br />";
	echo "<p>The Question list can only handle 10 Judges questions MAX at this time. - DRE 2018 </p>"
	echo "<a href=\"$self?action=new\">".i18n("Add a new question")."</a>";

	echo "<table class=\"summarytable\">";
	echo "<tr><th></th>".
	"<th>".i18n("Question")."</th>".
	"<th>".i18n("Table Heading")."</th>".
	"<th>".i18n("Type")."</th>".
	"<th>".i18n("Required")."</th>".
	"<th width=10%>".i18n("Actions")."</th></tr>";


	$keys = array_keys($qs);
	$types = array( 'check' => i18n("Check box"),
			'yesno' => i18n("Yes/No"),
			'text' => i18n("Text"),
			'int' => i18n("Number") );

	foreach($keys as $qid) {	
 		echo "<tr><td>{$qs[$qid]['ord']}</td>";
 		echo "<td>{$qs[$qid]['question']}</td>";
 		echo "<td>{$qs[$qid]['db_heading']}</td>";
		echo "<td align=\"center\">{$types[$qs[$qid]['type']]}</td>";
		echo "<td align=\"center\">{$qs[$qid]['required']}</td>";
		echo "<td align=\"center\"><nobr>";
		echo "<a title=\"Up\" href=\"$self?action=up&amp;up=$qid\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/up.".$config['icon_extension']."\" border=0></a>";
		echo "&nbsp;&nbsp;";
		echo "<a title=\"Down\" href=\"$self?action=down&amp;down=$qid\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/down.".$config['icon_extension']."\" border=0></a>";
		echo "&nbsp;&nbsp;";
		echo "<a title=\"Edit\" href=\"$self?action=edit&amp;edit=$qid\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/edit.".$config['icon_extension']."\" border=0></a>";
		echo "&nbsp;&nbsp;";
		echo "<a title=\"Remove\" onClick=\"return confirmClick('".i18n("Are you sure you want to remove this question?")."');\" href=\"$self?action=remove&amp;remove=$qid\"><img src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\" border=0></a>";

		echo "</nobr></td>";
		echo "</tr>";
 	}
	echo "</table>";

	if(count($keys) == 0) {
		$default_qs = questions_load_questions($section, -1);
		if(count($default_qs) != 0) {
			print("<br>");
			print(i18n("There are no questions for year %1, but there are %2 default questions.  To import the default questions to year %1 click on the link below.", array($year, count($default_qs))));
			print("<br>");
			print("<a title=\"Import\" href=\"$self?action=import&amp;impyear=-1\">".i18n("Import default questions")."</a><br>");
		}
	}

}



?>
