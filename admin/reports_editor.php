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
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');

 require_once('reports_students.inc.php');
 require_once('reports_judges.inc.php');
 require_once('reports_awards.inc.php');
 require_once('reports_committees.inc.php');
 require_once('reports_schools.inc.php');
 require_once('reports_volunteers.inc.php');
 require_once('reports_tours.inc.php');
 require_once('reports_fairs.inc.php');
 require_once('reports_fundraising.inc.php');
 require_once('reports.inc.php');
 require_once('../tcpdf.inc.php');

 $fields = array();
 $locs = array('X' => 'x', 'Y' => 'y', 'W' => 'w', 'H' => 'h', 'Lines' => 'lines');

 function field_selector($name, $id, $selected)
 {
 	global $fields;
	$in_optgroup = false;
 	echo "<select name=\"$name\" id=\"$id\">";
	echo "<option value=\"\" />-- None --</option>";
	foreach($fields as $k=>$f) {
		if($f['editor_disabled'] == true) continue;
		if(array_key_exists('start_option_group', $f)) {
			if($in_optgroup) echo '</optgroup>';
			echo '<optgroup label="'.i18n($f['start_option_group']).'">';
		}
		$sel = ($selected == $k) ? 'selected=\"selected\"': '' ;
        	echo "<option value=\"$k\" $sel >{$f['name']}</option>";
	}	
	if($in_optgroup) echo '</optgroup>';
	echo "</select>"; 
 }

 function selector($name, $a, $selected, $onchange='')
 {
	echo "<select name=\"$name\" $onchange >";
	foreach($a as $v=>$val) {
		$sel = ($selected == $v) ? 'selected=\"selected\"' : '';
		echo "<option value=\"$v\" $sel>$val</option>";
	}
	echo '</select>';
 }

 function parse_fields($f) 
 {
 	global $locs;
 	$ret = array();
 	if(!is_array($_POST[$f])) return array();
	$x = 0;
	foreach($_POST[$f] as $o=>$d) {
		if(is_array($d)) {
			$a = array();
			foreach($d as $l=>$v) {
				/* Scrub the array data */
				$floatloc = array_values($locs);
				if($l == 'field' || $l == 'value') {
					$v = stripslashes($v);
				} else if(in_array($l, $floatloc)) {
					$v = floatval($v);
					if($l == 'lines' && $v==0) $v=1;
				} else if($l == 'face') {
					$v = ($v == 'bold') ? 'bold' : '';
				} else if($l == 'align') {
					$aligns = array('left', 'right', 'center');
					if(!in_array($v, $aligns)) {
						echo "Invalid alignment $v";
						exit;
					}
				} else if($l == 'valign') {
					$aligns = array('vtop', 'vbottom', 'vcenter', 'top','middle','bottom');
					if(!in_array($v, $aligns)) {
						echo "Invalid valignment $v";
						exit;
					}
				} 
				$a[$l] = $v;
			}
			if(trim($a['field']) == '') continue;
			$ret[$x] = $a;
		} else {
			if(trim($d) == '') continue;
			$ret[$x]['field'] = stripslashes($d);
		}
		$x++;
	}
	return $ret;
 }
 function parse_options($f) 
 {
 	$ret = array();
 	if(!is_array($_POST[$f])) return array();
	foreach($_POST[$f] as $c=>$v) {
		if(trim($c) == '') continue;
		$ret[$c] = stripslashes($v);
	}
	return $ret;
 }

 /* Decode the report */
 $report = array();
 $report['id'] = intval($_POST['id']);
 $report['name'] = stripslashes($_POST['name']);
 $report['creator'] = stripslashes($_POST['creator']);
 $report['desc'] = stripslashes($_POST['desc']);
 $report['type'] = stripslashes($_POST['type']);
 $report['col'] = parse_fields('col');
 $report['group'] = parse_fields('group');
 $report['sort'] = parse_fields('sort');
 $report['distinct'] = parse_fields('distinct');
 $report['option'] = parse_options('option');
 $report['filter'] = parse_fields('filter');

// print("<pre>");print_r($_POST);print("</pre>");
// print("<pre>");print_r($report);print("</pre>");

 $reloadaction = $_POST['reloadaction'];
 $loadaction = $_POST['loadaction'];
 $colaction = $_POST['colaction'];
 $repaction = $_POST['repaction'];

 $repaction_save = $repaction;

 /* Sort out priorities */
 if($reloadaction != '') {
 	$loadaction = '';
	$colaction = '';
	$repaction = '';
 }

 if($loadaction != '') {
	$id = intval($_POST['id']);
	$report = report_load($id);
	if($id == 0) $report['type'] = 'student';
	$colaction = '';
	$repaction = '';
 }

 if($colaction != '') {
 	$repaction = '';
 }

 if($repaction == 'try') {
 	/* Generate the report from what was passed through POST */
	report_gen($report);
	exit;
 }


 send_header("Reports Editor",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "report_management"
			);


?>
<script type="text/javascript">
function reportReload()
{
	document.forms.report.reloadaction.value = 'reload';
	document.forms.report.submit();
}

var canvasWidth=0;
var canvasHeight=0;
var canvasObjectIndex=0;
var labelWidth=0;
var labelHeight=0;

function initCanvas(w,h,lw,lh) {
	canvasWidth=w;
	canvasHeight=h;
	labelWidth=lw;
	labelHeight=lh;
}

function createData(x,y,w,h,l,face,align,valign,value) {
	var canvas=document.getElementById('layoutcanvas');
	var newdiv=document.createElement('div');
	if(valign=="vcenter") verticalAlign="middle";
	else if(valign=="vtop") verticalAlign="top";
	else if(valign=="vbottom") verticalAlign="bottom";
	else verticalAlign="top";
//	alert(verticalAlign);

	//convert x,y,w,h from % to absolute

	var dx=Math.round(x*canvasWidth/100);
	var dy=Math.round(y*canvasHeight/100);
	var dw=Math.round(w*canvasWidth/100);
	var dh=Math.round(h*canvasHeight/100);
//	alert(dx+','+dy+','+dw+','+dh);

	var fontheight=Math.round(dh/l);

	newdiv.setAttribute('id','o_'+canvasObjectIndex);
	newdiv.style.display="table-cell";
	newdiv.style.position="absolute";
	newdiv.style.width=dw+"px";
	newdiv.style.height=dh+"px";
	newdiv.style.left=dx+"px";
	newdiv.style.top=dy+"px";
	newdiv.style.textAlign=align;
	newdiv.style.verticalAlign=verticalAlign;
	newdiv.style.padding="0 0 0 0";
	newdiv.style.margin="0 0 0 0";
//	newdiv.style.vertical-align=valign;
	newdiv.style.border="1px solid blue";
	newdiv.style.fontSize=fontheight+"px";
	newdiv.style.lineHeight=fontheight+"px";
	newdiv.style.fontFamily="Verdana";
	newdiv.style.fontSizeAdjust=0.65;

	var maxlength=Math.floor(dw/(fontheight*0.7))*l;
	if(value.length>maxlength) value=value.substring(0,maxlength);
	newdiv.innerHTML=value; //"Maple Test xxxx"; //value;

	canvas.appendChild(newdiv);

	canvasObjectIndex++;
}

function createDataTCPDF(x,y,w,h,align,valign,fontname,fontstyle,fontsize,value) {

	var canvas=document.getElementById('layoutcanvas');
	var newdiv=document.createElement('div');

	var dx = Math.round(x * canvasWidth / labelWidth);
	var dy = Math.round(y * canvasHeight / labelHeight);
	var dw = Math.round(w * canvasWidth / labelWidth);
	var dh = Math.round(h * canvasHeight / labelHeight);

	
	var fontheight=(fontsize * 25.4 / 72) * canvasHeight / labelHeight;
	var l = Math.floor(h/fontheight);
	if(fontheight == 0) fontheight=10;
	if(l==0) l=1;

//	alert(dh + ", fh="+fontheight);

	newdiv.setAttribute('id','o_'+canvasObjectIndex);
	newdiv.style.display="table-cell";
	newdiv.style.position="absolute";
	newdiv.style.width=dw+"px";
	newdiv.style.height=dh+"px";
	newdiv.style.left=dx+"px";
	newdiv.style.top=dy+"px";
	newdiv.style.textAlign=align;
	newdiv.style.verticalAlign=valign;
	newdiv.style.padding="0 0 0 0";
	newdiv.style.margin="0 0 0 0";
//	newdiv.style.vertical-align=valign;
	newdiv.style.border="1px solid blue";
	newdiv.style.fontSize=fontheight+"px";
	newdiv.style.lineHeight=fontheight+"px";
	newdiv.style.fontFamily=fontname;
	newdiv.style.fontSizeAdjust=0.65;

	var maxlength=Math.floor(dw/(fontheight*0.7))*l;
	if(value.length>maxlength) value=value.substring(0,maxlength);

	newdiv.innerHTML=value; 

	canvas.appendChild(newdiv);

	canvasObjectIndex++;
}

</script>
<?

 if($repaction == 'save') {
 	/* Save the report */
	$report['id'] = report_save($report);
	echo happy(i18n("Report Saved"));
 }

 if($repaction == 'del') {
 	report_delete($report['id']);
	echo happy(i18n("Report Deleted"));
 }

 if($repaction == 'dupe') {
 	$report['id'] = 0;
 	$report['id'] = report_save($report);
	echo happy(i18n("Report Duplicated"));
 }

 if($repaction == 'export') {
 	echo "<pre>";
	$q = mysql_query("SELECT system_report_id FROM reports WHERE 1 ORDER BY system_report_id DESC");
	$r = mysql_fetch_assoc($q);
	$sid = $r['system_report_id'] + 1;
	$n = mysql_escape_string($report['name']);
	$c = mysql_escape_string($report['creator']);
	$d = mysql_escape_string($report['desc']);
	$t = mysql_escape_string($report['type']);

 	echo "INSERT INTO `reports` (`id`, `system_report_id`, `name`, `desc`, `creator`, `type`) VALUES\n";
	echo "\t('', '$sid', '$n', '$d', '$c', '$t');\n";

	echo "INSERT INTO `reports_items` (`id`, `reports_id`, `type`, `ord`, `field`, `value`, `x`, `y`, `w`, `h`, `lines`, `face`, `align`) VALUES ";

	/* Do the options */
	$x = 0;
	foreach($report['option'] as $k=>$v) {
		echo "\n\t('', LAST_INSERT_ID(), 'option', $x, '$k', '$v', 0, 0, 0, 0, 0, '', ''),";
		$x++;
	}
	/* Do the fields */
	$fs = array('col', 'group', 'sort', 'distinct', 'filter');
	$first = true;
	foreach($fs as $f) {
		foreach($report[$f] as $x=>$v) {
			$k = $v['field'];
			$vx = intval($v['x']);
			$vy = intval($v['y']);
			$vw = intval($v['w']);
			$vh = intval($v['h']);
			$vlines = intval($v['lines']);
			if($vlines == 0) $vlines = 1;
			$face = $v['face'];
			$align = $v['align']. ' ' . $v['valign'];
			$value=mysql_escape_string(stripslashes($v['value']));
			if(!$first) echo ',';
			$first = false;
			echo "\n\t('', LAST_INSERT_ID(), '$f', $x, '$k', '$value', $vx, $vy, $vw, $vh, $vlines, '$face', '$align')";
		}
	}
	echo ";\n";
	echo "</pre>";
 }
 	


 /* ---- Setup  ------ */

 $n_columns = intval($_POST['ncolumns']);
 $n = count($report['col']) + 1;
 if($n > $n_columns) $n_columns = $n;
 if($colaction == 'add') $n_columns+=3;

 $fieldvar = "report_{$report['type']}s_fields";
 if(isset($$fieldvar)) $fields = $$fieldvar;


 echo "<br />";

 echo "<form method=\"post\" name=\"reportload\" action=\"reports_editor.php\" onChange=\"document.reportload.submit()\">";
 echo "<input type=\"hidden\" name=\"loadaction\" value=\"load\" />";
 echo "<select name=\"id\" id=\"report\">";
 echo "<option value=\"0\">".i18n("Create New Report")."</option>\n";

 $reports = report_load_all();
 $x=0;
 foreach($reports as $r) {
 	$sel = ($report['id'] == $r['id']) ? 'selected=\"selected\"' : '';
 	echo "<option value=\"{$r['id']}\" $sel>{$r['name']}</option>\n";
}
 echo "</select>";
 echo "<input type=\"submit\" value=\"Load\"></form>";
 echo "<hr />";
 

 echo "<form method=\"post\" name=\"report\" action=\"reports_editor.php\">";
 echo "<input type=\"hidden\" name=\"id\" value=\"{$report['id']}\" />";
 echo "<input type=\"hidden\" name=\"ncolumns\" value=\"$n_columns\" />";

 echo "<h4>Report Information</h4>";
 echo "<table>";
 echo "<tr><td>Name: </td>";
 echo "<td><input type=\"text\" name=\"name\" size=\"80\" value=\"{$report['name']}\" /></td>";
 echo "</tr>";
 echo "<tr><td>Created By: </td>";
 echo "<td><input type=\"text\" name=\"creator\" size=\"80\" value=\"{$report['creator']}\" /></td>";
 echo "</tr>";
 echo "<tr><td>Description: </td>";
 echo "<td><textarea name=\"desc\" rows=\"3\" cols=\"60\">{$report['desc']}</textarea></td>";
 echo "</tr>";
 echo "<tr><td>Type: </td>";
 echo "<td>";
 selector('type', array('student' => 'Student Report', 'judge' => 'Judge Report', 
 			'award' => 'Award Report', 'committee' => 'Committee Member Report',
			'school' => 'School Report', 'volunteer' => 'Volunteer Report',
			'tour' => 'Tour Report', 'fair' => 'Feeder Fair Report',
			'fundraising' => 'Fundraising Report'),
		$report['type'],
		"onChange=\"reportReload();\"");
 echo "<input type=\"hidden\" name=\"reloadaction\" value=\"\">";
 echo "</td>";
 echo "</tr></table>";
 
 echo "<h4>Report Data</h4>";
 echo "<table>";
 $x=0;
 //only go through the columns if there are columns to go through
 if(count($report['col'])) {
	 foreach($report['col'] as $o=>$d) {
		echo "<tr><td>Column&nbsp;".($x + 1).": </td>";
		echo "<td>";
		if(intval($x) != intval($o)) {
			echo ("WARNING, out of order!");
		}
		field_selector("col[$o][field]", "col$o", $d['field']);
		echo "</td></tr>"; 
		$x++;
		$canvasLabels[]=$fields[$report['col'][$o]['field']]['name']; //['field'];
	 }
 }
 for(;$x<$n_columns;$x++) {
	echo "<tr><td>Column&nbsp;".($x + 1).": </td>";
	echo "<td>";
	field_selector("col[$x][field]", "col$x", '');
	echo "</td></tr>"; 

 }
 echo "<tr><td></td>";
 echo "<td align=\"right\">";
 echo "<select name=\"colaction\"><option value=\"\"></option><option value=\"add\">Add more columns</option></select>";
 echo "<input type=\"submit\" value=\"Go\">";
 echo "</td></tr>";
 echo "</table>\n";
 
$doCanvasSample = false;
$doCanvasSampletcpdf = false;
 $l_w=$report_stock[$report['option']['stock']]['label_width'];
 $l_h=$report_stock[$report['option']['stock']]['label_height'];
 if($l_w && $l_h && $report['option']['type']=="label") {
     echo "<h4>Label Data Locations</h4>";

	$doCanvasSample=true;
	$ratio=$l_h/$l_w;
	$canvaswidth=600;
	$canvasheight=round($canvaswidth*$ratio);
	echo "<div id=\"layoutcanvas\" style=\"border: 1px solid red; position: relative; width: {$canvaswidth}px; height: {$canvasheight}px;\">";
	echo "</div>\n";
	echo "<script type=\"text/javascript\">initCanvas($canvaswidth,$canvasheight,$l_w,$l_h)</script>\n";
 }

 if($l_w && $l_h && $report['option']['type']=="tcpdf_label") {
     echo "<h4>Label Data Locations - TCPDF</h4>";

	$l_w *= 25.4; 
	$l_h *= 25.4; 
	$doCanvasSampletcpdf=true;
	$ratio=$l_h/$l_w;
	$canvaswidth=600;
	$canvasheight=round($canvaswidth*$ratio);
	echo "<div id=\"layoutcanvas\" style=\"border: 1px solid red; position: relative; width: {$canvaswidth}px; height: {$canvasheight}px;\">";
	echo "</div>\n";
	echo "<script type=\"text/javascript\">initCanvas($canvaswidth,$canvasheight,$l_w,$l_h)</script>\n";
 }


 echo "<table>";
 $x=0;

 
 if($report['option']['type'] == 'label' || $report['option']['type'] == 'tcpdf_label') {
 	$fontlist = array('' => 'Default');
	$fl = PDF::getFontList();
	foreach($fl as $f) $fontlist[$f] = $f;
//	print_r($fl);
			  
 	foreach($report['col'] as $o=>$d) {
		$f = $d['field'];
		echo "<tr><td align=\"right\">Loc ".($o+1).": </td>";
		echo "<td>";
		$script="";
		foreach($locs as $k=>$v) {
			if($k=='Lines' && $report['option']['type'] != 'label') continue;
			echo "$k=<input type=\"text\" size=\"3\" name=\"col[$x][$v]\" value=\"{$d[$v]}\">";
			$script.="{$d[$v]},";
		}

		if($report['option']['type'] == 'label') {
			echo 'Face=';
			selector("col[$x][face]", array('' => '', 'bold' => 'Bold'), $d['face']);
		}
		echo 'Align';
		selector("col[$x][align]", array('center' => 'Center', 'left' => 'Left', 'right' => 'Right'), 
				$d['align']);
		echo 'vAlign';
		if($report['option']['type'] == 'label') {
			selector("col[$x][valign]", array('vcenter' => 'Center', 'vtop' => 'Top', 'vbottom' => 'Bottom'), 
					$d['valign']);
		} else {
			selector("col[$x][valign]", array('middle' => 'Middle', 'top' => 'Top', 'bottom' => 'Bottom'), 
					$d['valign']);
			
			echo 'Font=';
			selector("col[$x][fontname]", $fontlist, $d['fontname']);
			selector("col[$x][fontstyle]", array('' => '', 'bold' => 'Bold'), $d['fontstyle']);
			echo "<input type=\"text\" size=\"3\" name=\"col[$x][fontsize]\" value=\"{$d['fontsize']}\">";
			echo 'pt  ';
			echo 'OnOverflow=';
			selector("col[$x][on_overflow]", array('tuncate'=>'Truncate','...'=>'Add ...', 'scale'=>'Scale'), $d['on_overflow']);
		}

		if($f == 'static_text') {
			echo "<br />Text=<input type=\"text\" size=\"40\" name=\"col[$x][value]\" value=\"{$d['value']}\">";
		} else {
			echo "<input type=\"hidden\" name=\"col[$x][value]\" value=\"\">";
		}
		if($doCanvasSample)
			echo "<script type=\"text/javascript\">createData({$script}'{$d['face']}','{$d['align']}','{$d['valign']}','{$canvasLabels[$x]}')</script>\n";
		if($doCanvasSampletcpdf)
			echo "<script type=\"text/javascript\">createDataTCPDF({$script}'{$d['align']}','{$d['valign']}','{$d['fontname']}','{$d['fontstyle']}','{$d['fontsize']}','{$canvasLabels[$x]}')</script>\n";

		$x++;
	}
 	for(;$x<$n_columns;$x++) {
		echo "<tr><td align=\"right\">Loc ".($x+1).": </td>";
		echo "<td>";
		foreach($locs as $k=>$v) {
			if($k=='Lines' && $report['option']['type'] != 'label') continue;
			echo "$k=<input type=\"text\" size=\"3\" name=\"col[$x][$v]\" value=\"0\">";
		}
		if($report['option']['type'] == 'label') {
			echo 'Face=';
			selector("col[$x][face]", array('' => '', 'bold' => 'Bold'), '');
		}

		echo 'Align';
		selector("col[$x][align]", array('center' => 'Center', 'left' => 'Left', 'right' => 'Right'), 
				'center');
		echo 'vAlign';
		if($report['option']['type'] == 'label') {
			selector("col[$x][valign]", array('vcenter' => 'Center', 'vtop' => 'Top', 'vbottom' => 'Bottom'), 
					'top');
		} else {
			selector("col[$x][valign]", array('middle' => 'Middle', 'top' => 'Top', 'bottom' => 'Bottom'), 'middle');
			
			echo 'Font=';
			selector("col[$x][fontname]", $fontlist, '');
			selector("col[$x][fontstyle]", array('' => '', 'bold' => 'Bold'), '');
			echo "<input type=\"text\" size=\"3\" name=\"col[$x][fontsize]\" value=\"\">";
			echo 'pt  ';
			echo 'OnOverflow=';
			selector("col[$x][on_overflow]", array('Truncate'=>'truncate','Add ...'=>'...', 'Scale'=>'scale'),'');
		}
		echo "<input type=\"hidden\" name=\"col[$x][value]\" value=\"\">";
		echo "</td></tr>"; 
	}
 }
 echo "</table>\n";
 echo "<h4>Grouping</h4>";
 for($x=0;$x<2;$x++) {
	echo "Group By".($x + 1).": ";
	$f = $report['group'][$x]['field'];
	field_selector("group[$x]", "group$x", $f);
	echo "<br />"; 
 }
 echo "<h4>Sorting</h4>";
 for($x=0;$x<3;$x++) {
	echo "Sort By".($x + 1).": ";
	$f = $report['sort'][$x]['field'];
	field_selector("sort[$x]", "sort$x",$f); 
	echo "<br />"; 
 }
 echo "<h4>Distinct</h4>";
 echo "Distinct Column:   ";
 $x=0;
 $f = $report['distinct'][$x]['field'];
 field_selector("distinct[$x]", "distinct0", $f);

 echo "<h4>Filtering</h4>";
 echo "<table>";
 for($x=0;$x<3;$x++) {
	echo "<tr><td>Filter".($x + 1).":</td><td>";
	field_selector("filter[$x][field]", "filter$x",$report['filter'][$x]['field']); 
	echo "<br />";
	selector("filter[$x][x]", $filter_ops,$report['filter'][$x]['x']); 
	$v = $report['filter'][$x]['value'];
	echo "Text=<input type=\"text\" size=\"20\" name=\"filter[$x][value]\" value=\"$v\">";
	echo "</td></tr>"; 
 }
 echo "</table>";

 echo "<h4>Options</h4>";
 foreach($report_options as $ok=>$o) {
 	echo "{$o['desc']}: <select name=\"option[$ok]\" id=\"$ok\">";
	foreach($o['values'] as $k=>$v) {
		$sel = ($report['option'][$ok] == $k) ? 'selected=\"selected\"' : '';
		echo "<option value=\"$k\" $sel>$v</option>";
	}
	echo "</select><br />\n";
 } 

 echo "<br />";
 if($report['system_report_id'] != 0) {
 	echo notice(i18n('This is a system report, it cannot be changed or deleted.  To save changes you have made to it, please select the \'Save as a new report\' option.'));
 }
 echo "<select name=\"repaction\">";
 if($report['system_report_id'] == 0) {
	$sel = ($repaction_save == 'save') ? "selected=\"selected\"" : '';
	echo " <option value=\"save\" $sel>Save this report</option>";
	$sel = ($repaction_save == 'try') ? "selected=\"selected\"" : '';
	echo " <option value=\"try\" $sel>Try this report</option>";
	echo " <option value=\"export\">Export this report</option>";
	echo " <option value=\"\" ></option>";
	echo " <option value=\"dupe\" >Save as a new report(duplicate)</option>";
	echo " <option value=\"\" ></option>";
	echo " <option value=\"del\" >Delete this report</option>";
 } else {
 	echo " <option value=\"dupe\" >Save as a new report(duplicate)</option>";
	$sel = ($repaction_save == 'try') ? "selected=\"selected\"" : '';
	echo " <option value=\"try\" $sel>Try this report</option>";
	echo " <option value=\"export\">Export this report</option>";
 }
 	
 echo "</select>";
 echo "<input type=\"submit\" value=\"Go\">";

 echo "</form>";

 send_footer();
?>
