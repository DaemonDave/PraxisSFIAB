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

function config_editor_load($category, $year)
{
	$query = "SELECT * FROM config WHERE year='$year' AND category='$category' ORDER BY ord";
	$q = mysql_query($query);
	print(mysql_error());

	$var = array();
	while($r=mysql_fetch_object($q)) {
		$var[$r->var]['val'] = $r->val;
		$var[$r->var]['desc'] = $r->description;
		$var[$r->var]['category'] = $r->category;
		$var[$r->var]['ord'] = $r->ord;
		$var[$r->var]['type'] = $r->type;
		$var[$r->var]['type_values'] = $r->type_values;
	}
	return $var;
}

function config_editor_parse_from_http_headers($array_name)
{
	$ans = array();
	if(!is_array($_POST[$array_name])) return $ans;

	$keys = array_keys($_POST[$array_name]);
	foreach($keys as $id) {
		if(is_array($_POST[$array_name][$id])) {
			$ans[$id] = array();
			foreach($_POST[$array_name][$id] as $k=>$v) {
				if($v != '') {
					$ans[$id][$k]=stripslashes($v);
				}
			}
		} else {
			$ans[$id] = stripslashes($_POST[$array_name][$id]);
		}
	}	
	return $ans;
}

/* Ensure the fairyear has all variables that are in -1.  This is called:
 * - From the database update script (which could add new variables to
 *   the -1 year, and we want them automatically copied to the current year
 * - From the rollover script to copy all last year variables to 
 *   the new year
 * - After an install to copy all the variables to the current year
 */
function config_update_variables($fairyear=NULL, $lastfairyear=NULL)
{
	global $config;

	/* if fairyear isn't specified... */
	if($fairyear == NULL) $fairyear = $config['FAIRYEAR'];
	if($lastfairyear == NULL) $lastfairyear = $fairyear - 1;

	/* The master list of variables is the year=-1, grab
	 * ALL config variables that exist for -1 but
	 * do NOT exist for $fairyear */
	$q = "SELECT config.var FROM `config` 
		LEFT JOIN `config` AS C2 ON(config.var=C2.var 
					AND C2.year='$fairyear') 
		WHERE config.year=-1 AND C2.year IS NULL";
	$r = mysql_query($q);
	while($i = mysql_fetch_assoc($r)) {
		$var = $i['var'];
		/* See if this var exists for last year or
		 * the -1 year, prefer last year's value */
		$q = "SELECT * FROM `config` 
			WHERE config.var='$var'
				AND (config.year='$lastfairyear' 
					OR config.year='-1')
			ORDER BY config.year DESC";
		$r2 = mysql_query($q);
		if(mysql_num_rows($r2) < 1) {
			/* Uhoh, this shouldn't happen */
			echo "ERROR, Variable '$var' doesn't exist";
			exit;
		}
		$v = mysql_fetch_object($r2);

		mysql_query("INSERT INTO config (var,val,category,type,type_values,ord,description,year) VALUES (
	                '".mysql_escape_string($v->var)."',
			'".mysql_escape_string($v->val)."',
			'".mysql_escape_string($v->category)."',
			'".mysql_escape_string($v->type)."',
			'".mysql_escape_string($v->type_values)."',
			'".mysql_escape_string($v->ord)."',
			'".mysql_escape_string($v->description)."',
			'$fairyear')");
	}
}

$config_editor_actions_done = false;
$config_editor_updated = false;

function config_editor_handle_actions($category, $year, $array_name)
{
	global $config;
	global $config_editor_actions_done;

	$config_vars = config_editor_load($category, $year);
	
	$config_editor_actions_done = true;
	$updated = false;
	if($_POST['action']=="update") {
		$var = config_editor_parse_from_http_headers($array_name);
		$varkeys = array_keys($var);
		foreach($varkeys as $k) {
			if(is_array($var[$k]))
				$val = implode(',',$var[$k]);
			else 
				$val = $var[$k];

			/* If it hasn't changed, don't update it (do a string
			 * compare so numbers aren't interpreted.. php thinks
			 * "1.0" == "1") */
			if(strcmp($config[$k], $val) == 0) continue;

			switch($config_vars[$k]['type']) {
			case 'number':
				if(ereg("[0-9]+(\.[0-9]+)?", $val, $regs)) {
					$val = $regs[0];
				} else {
					$val = 0;
				}
				break;
			}

			/* Prep for MySQL update */
			$val = mysql_escape_string($val);
			$v = mysql_escape_string(stripslashes($k));
			mysql_query("UPDATE config SET val=\"$val\" 
					WHERE var=\"$v\" 
					AND `year`='$year'");
			print mysql_error();
//			echo "Saving {$v} = $val<br>";
			$config_editor_updated = true;
			$updated = true;
		}
		if($updated == true) {
			message_push(happy(i18n("Configuration Updated")));;
		}
		return 'update';
	}
}

/* A complete question editor.  Just call it with the
 * section you want to edit, a year, the array_name to use for
 * POSTing and GETting the questions (so you can put more than one
 * edtior on a single page), and give it $_SERVER['PHP_SELF'], because
 * php_self inside this function is this file.
 * FUTURE WORK: it would be nice to hide the order, and just implement
 *  a bunch of up/down arrows, and dynamically compute the order for
 *  all elements */
function config_editor($category, $year, $array_name, $self) 
{
	global $config;
	global $config_editor_actions_done, $config_editor_updated;

	if($config_editor_actions_done == false) {
		config_editor_handle_actions($category, $year, $array_name);
	}

	/* Load questions, then handle up and down, because with up and down we
	 * have to modify 2 questions to maintain the order */
	$var = config_editor_load($category, $year);

	echo "<form method=\"post\" action=\"$self\">";

	echo "<table cellpadding=\"3\">";

	$varkeys = array_keys($var);
	//compute the optimal input size to use
	$biggest=0;
	foreach($varkeys as $k) {
		if(strlen($var[$k]['val'])>$biggest)
			$biggest=strlen($var[$k]['val']);
	}
	if($biggest>30) $size=30;
	else $size=$biggest+1;

	//make sure size is at minimum 8, this way if all fields are empty you dont end up with 1 character long text boxes
	if($size<8) $size=8;

	$line = 1;
	foreach($varkeys as $k) {
		$trclass = ($line % 2 == 0) ? "even" : "odd";
		$line++;

		print("<tr class=\"$trclass\">");
		print("<td>".i18n($var[$k]['desc'])."</td>");
		print("<td>");

		$val = htmlspecialchars($var[$k]['val']);
		$name = "${array_name}[$k]";

		switch($var[$k]['type']) {
		case "yesno":
			print("<select name=\"$name\">");
			$sel = ($val == 'yes') ? 'selected=selected' : '';
			print("<option $sel value=\"yes\">".i18n("Yes")."</option>");
			$sel = ($val == 'no') ? 'selected=selected' : '';
			print("<option $sel value=\"no\">".i18n("No")."</option>");
			print("</select>");
			break;
		case "enum":
			$val = split(',', $val);
			$values = $var[$k]['type_values'];
			/* Split values */
			/* The PERL regex here matches any string of the form
			 * key=val|   , where the = and 'val' and '|' are
			 * optional.  val is allowed to contain spaces. Using
			 * preg_match_all runs this regex multiple times, and
			 * creates arrays for each subpattern that matches.
			 * For example, "aa=Aye|bb=Bee Bee|cc|dd=Dee"
			 * Would construct the following Array of Arrays:
			 * Array ( [0] => Array ( [0] => "aa=Aye|",
			 			  [1] => "bb=Bee Bee|",
						  [2] => "cc|",
						  [3] => "dd=Dee" ),
				   [1] => Array ( [0] => "aa",
			 			  [1] => "bb",
						  [2] => "cc",
						  [3] => "dd" ),
				   [2] => Array ( [0] => "Aye",
			 			  [1] => "Bee Bee",
						  [2] => "",
						  [3] => "Dee" )  )
			* neat eh?  :)  We use [1] and [2] to form the keys and
			* values that we show the user */

			preg_match_all("/([^\|=]+)(?:=([^\|]+))?\|?/", $values, $regs);
//			print_r($regs);
			print("<select name=\"$name\">");
			for($x=0; $x<count($regs[1]); $x++) {
				$e_key = trim($regs[1][$x]);
				$e_val = trim($regs[2][$x]);
				if($e_val == "") $e_val = $e_key;

				$sel = in_array($e_key, $val) ? 'selected=selected' : '';
				print("<option $sel value=\"$e_key\">".i18n($e_val)."</option>");
			}
			print("</select>");
			break;
		case 'multisel':
			/* same PERL parse statements as above */
			$val = split(',', $val);
			$values = $var[$k]['type_values'];
			preg_match_all("/([^\|=]+)(?:=([^\|]+))?\|?/", $values, $regs);
			/* Decide which way to show this list */
			$c = count($regs[1]);
			$rows = 0;
			if($c > 5) {
				$rows = intval(($c+2) / 3);
				print("</td></tr><tr class=\"$trclass\"><td colspan=2>");
				print("<table width=\"100%\"><tr><td width=\"10%\">&nbsp;");
			} else {
				$rows = $c;
			}

			for($x=0; $x<$c; $x++) {
				if(($x % $rows) == 0 && $rows > 0 && $c > 5) {
					print("</td><td width=\"30%\">");
				}

				$e_key = trim($regs[1][$x]);
				$e_val = trim($regs[2][$x]);
				if($e_val == "") $e_val = $e_key;

				$ch = (in_array($e_key, $val)) ? 'checked="checked"' : '';
				print("<input type=\"checkbox\" name=\"{$name}[]\" value=\"$e_key\" $ch />");
				print(i18n($e_val)."<br />");
			}
			if($c > 5) print("</td></tr></table>");

			break;
		case 'language':
			print("<select name=\"$name\">");
			foreach($config['languages'] as $k=>$lang) {
				$sel = ($config['default_language'] == $k) ? 'selected=selected' : '';
				print("<option $sel value=\"$k\">$lang</option>");
			}
			print("</select>");
			break;

		case 'theme':
			print("<select name=\"$name\">");
			/* Find all theme directories */
			$cwd=getcwd();
			$themeroot = $cwd."/../theme";
//			$themeroot = "{$_SERVER['DOCUMENT_ROOT']}{$config['SFIABDIRECTORY']}/theme";
			$d = opendir($themeroot);
			while(($f = readdir($d))) {
				/* Load the theme.  Loads theme into a local theme, not overwriting
				 * the global $theme */

				if($var[$k]['type_values'] == 'icons') {
					$theme_php = "$themeroot/$f/icons.php";
					$cur = $config['theme_icons'];
					$rvar = 'theme_icons';
				} else {
					$theme_php = "$themeroot/$f/theme.php";
					$cur = $config['theme'];
					$rvar = 'theme';
				}

				if(!file_exists($theme_php)) continue;
				include($theme_php);
				$t = $$rvar; 

				$sel = ($cur == $f) ? 'selected=selected' : '';
				print("<option $sel value=\"$f\">{$t['name']}</option>");
			}
			closedir($d);

			print("</select>");
			break;

		default:
			print("<input size=\"$size\" type=\"text\" name=\"$name\" value=\"$val\">\n");
			break;
		}
		echo "</td></tr>";
	}
	print("</table>");
	print("<input type=\"hidden\" name=\"category\" value=\"$category\" >\n");
	print("<input type=\"hidden\" name=\"action\" value=\"update\" >\n");
	print("<input type=\"submit\" value=\"".i18n("Save Configuration")."\" />\n");

	echo "</form>";

	/* Returns TRUE if config variables were updated */
	return $updated;
}

?>
