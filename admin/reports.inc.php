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

 require_once("reports_students.inc.php");  /* $report_students_fields */
 require_once("reports_judges.inc.php");  /* $report_students_fields */
 require_once("reports_awards.inc.php");  /* $report_students_fields */
 require_once("reports_committees.inc.php");  /* $report_students_fields */
 require_once("reports_volunteers.inc.php"); /* $report_volunteers_fields */
 require_once("reports_schools.inc.php");
 require_once("reports_tours.inc.php");
 require_once("reports_fairs.inc.php");
 require_once("reports_fundraising.inc.php");

 require_once('../lpdf.php');
 require_once('../lcsv.php');
 require_once('../tcpdf.inc.php');

 $filter_ops = array(	0 => '=',
 			1 => '<=',
			2 => '>=',
			3 => '<',
			4 => '>',
			5 => '!=',
			6 => 'IS',
			7 => 'IS NOT',
			8 => 'LIKE',
			9 => 'NOT LIKE ',
		);
 
 $report_options = array();
 $report_options['type'] = array('desc' => 'Report Format',
 				'values' => array('pdf'=>'PDF', 'csv'=>'CSV', 'label'=>'Label', 'tcpdf_label'=>'TCPDF Label (experimental)')
		);
 $report_options['group_new_page'] = array('desc' => 'Start each new grouping on a new page',
 					'values' => array('no'=>'No', 'yes'=>'Yes')
		);
 $report_options['allow_multiline'] = array('desc' => 'Allow table rows to span multiple lines',
 					'values' => array('no'=>'No', 'yes'=>'Yes')
		);
 $report_options['fit_columns'] = array('desc' => 'Scale column widths to fit on the page width',
 					'values' => array('no'=>'No', 'yes'=>'Yes')
		);
 $report_options['label_box'] = array('desc' => 'Draw a box around each label',
 					'values' => array('no'=>'No', 'yes'=>'Yes')
		);
 $report_options['field_box'] = array('desc' => 'Draw a box around each text field on the label',
 					'values' => array('no'=>'No', 'yes'=>'Yes')
		);
 $report_options['label_fairname'] = array('desc' => 'Print the fair name at the top of each label',
 					'values' => array('no'=>'No', 'yes'=>'Yes')
		);
 $report_options['label_logo'] = array('desc' => 'Print the fair logo at the top of each label',
 					'values' => array('no'=>'No', 'yes'=>'Yes')
		);
 $report_options['default_font_size'] = array('desc' => 'Default font size to use in the report',
 					'values' => array(
					'10'=>'10', 
					'11'=>'11', '12'=>'12', 
										'13'=>'13', '14'=>'14', '15'=>'15', '16'=>'16', '18'=>'18',
										'20'=>'20', '22'=>'22', '24'=>'24'
										)
		);
  

/*
Viceroy		Grand	Avery	rows?	w x h"		per page
		& Toy	
LRP 130		99180	5960	3	2 5/8 x 1	30
LRP 120		99189	5961	2	4 x 1		20
LRP 114		99179	5959	7	4 x 1 1/2	14
LRP 214		99190	5962	7	4 x 1 1/3	14
LRP 110		99181	5963	5	4 x 2		10
LRP 106		99763	5964	3	4 x 3 1/3	6
LRP 100		99764	5965	1	8 1/2 x 11	1
LRP 180		99765	5967	4	1 3/4 x 1/2 	80 */


/* FIXME: put these in a databse */
 $report_stock = array();
 $report_stock['fullpage'] = array('name' => 'Letter 8.5 x 11 (3/4" margin)',
			'page_width' => 8.5,
			'page_height' => 11,
			'label_width' => 7,
			'x_spacing' => 0,
			'cols' => 1,
			'label_height' => 9.5,
			'y_spacing' => 0,
			'rows' => 1,
			'page_format' => 'LETTER',
			'page_orientation' => 'P',
			);

 $report_stock['fullpage_landscape'] = array('name' => 'Letter 8.5 x 11 Landscape (3/4" margin)',
			'page_width' => 11,
			'page_height' => 8.5,
			'label_width' => 9.5,
			'x_spacing' => 0,
			'cols' => 1,
			'label_height' => 7,
			'y_spacing' => 0,
			'rows' => 1,
			'page_format' => 'LETTER',
			'page_orientation' => 'L',
			);

 $report_stock['fullpage_full'] = array('name' => 'Letter 8.5 x 11 (no margin)',
			'page_width' => 8.5,
			'page_height' => 11,
			'label_width' => 8.5,
			'x_spacing' => 0,
			'cols' => 1,
			'label_height' => 11,
			'y_spacing' => 0,
			'rows' => 1,
			'page_format' => 'LETTER',
			'page_orientation' => 'P',
			);

 $report_stock['fullpage_landscape_full'] = array('name' => 'Letter 8.5 x 11 Landscape (no margin)',
			'page_width' => 11,
			'page_height' => 8.5,
			'label_width' => 11,
			'x_spacing' => 0,
			'cols' => 1,
			'label_height' => 8.5,
			'y_spacing' => 0,
			'rows' => 1,
			'page_format' => 'LETTER',
			'page_orientation' => 'L',
			);

 $report_stock['5161'] = array('name' => 'Avery 5161/5261/5961/8161, G&T 99189 (1"x4")',
			'page_width' => 8.5,
			'page_height' => 11,
			'label_width' => 4,
			'x_spacing' => 0.15,
			'cols' => 2,
			'label_height' => 1,
			'y_spacing' => 0.00,
			'y_padding' => 0.05,
			'rows' => 10,
			'page_format' => 'LETTER',
			'page_orientation' => 'P',
			);
	
 $report_stock['5162'] = array('name' => 'Avery 5162/5262/5962/8162/8462, G&T 99190 (1 1/3"x4")',
			'page_width' => 8.5,
			'page_height' => 11,
			'label_width' => 3.99,
			'x_spacing' => 0.187,
			'cols' => 2,
			'label_height' => 1.326,
			'y_spacing' => 0.00,
			'y_padding' => 0.30,
			'rows' => 7,
			'page_format' => 'LETTER',
			'page_orientation' => 'P',
			);
 $report_stock['5163'] = array('name' => 'Avery 5163/5263/5963/8163/8463, G&T 99181 (2"x4")',
			'page_width' => 8.5,
			'page_height' => 11,
			'label_width' => 4,
			'x_spacing' => 0.1719,
			'cols' => 2,
			'label_height' => 2,
			'y_spacing' => 0.00,
			'rows' => 5,
			'page_format' => 'LETTER',
			'page_orientation' => 'P',
			);

/* This is combined with 5161
 $report_stock['5961'] = array('name' => 'Avery 5961, G&T 99189 (1"x4")',
			'page_width' => 8.5,
			'page_height' => 11,
			'label_width' => 4,
			'x_spacing' => 0.08,
			'cols' => 2,
			'label_height' => 1,
			'y_spacing' => 0.08,
			'rows' => 10,
			);
*/		

 $report_stock['5164'] = array('name' => 'Avery 5164/5264/5964/8164, G&T 99763 (4"x3 1/3")',
			'page_width' => 8.5,
			'page_height' => 11,
			'label_width' => 4,
			'x_spacing' => 3/16,
			'cols' => 2,
			'label_height' => 3 + 1/3,
			'y_spacing' => 0,
			'rows' => 3,
			'page_format' => 'LETTER',	/* tcpdf format */
			'page_orientation' => 'P',	/* tcpdf orientation */

			);
 $report_stock['nametag'] = array('name' => 'Cards 4"x3"',
			'page_width' => 8.5,
			'page_height' => 11,
			'label_width' => 4,
			'x_spacing' => 0,
			'cols' => 2,
			'label_height' => 3,
			'y_spacing' => 0,
			'rows' => 3,
			'page_format' => 'LETTER',
			'page_orientation' => 'P',
			);

 $report_stock['letter_4up'] = array('name' => 'Fullpage, 4up',
			'page_width' => 8.5,
			'page_height' => 11,
			'label_width' => 4,
			'x_spacing' => 0.25,
			'cols' => 2,
			'label_height' => 5,
			'y_spacing' => 0.25,
			'rows' => 2,
			'page_format' => 'LETTER',
			'page_orientation' => 'P',
			);

 $report_stock['ledger'] = array('name' => 'Ledger/Tabloid 11 x 17',
			'page_width' => 11,
			'page_height' => 17,
			'label_width' => 11,
			'x_spacing' => 0,
			'cols' => 1,
			'label_height' => 17,
			'y_spacing' => 0,
			'rows' => 1,
			'page_format' => 'LETTER',
			'page_orientation' => 'P',
			);
	
 $report_stock['ledger_landscape'] = array('name' => 'Ledger/Tabloid 11 x 17 Landscape',
			'page_width' => 17,
			'page_height' => 11,
			'label_width' => 17,
			'x_spacing' => 0,
			'cols' => 1,
			'label_height' => 11,
			'y_spacing' => 0,
			'rows' => 1,
			'page_format' => 'LETTER',
			'page_orientation' => 'P',
			);


$report_options['stock'] = array('desc' => "Paper Type",
                                'values' => array() );
				

/* Add more types to the report format */
foreach($report_stock as $n=>$v) {
	$report_options['stock']['values'][$n] = $v['name'];
}


 $allow_options = array_keys($report_options);

 /* A list of custom reports, as close as possible to a real report
  * format, but with the 'custom_url' attached. */
 $report_custom = array();
 $x = 1;
 $report_custom[$x] = array('id' => $x, 'name' => 'Custom -- Mailing Labels',
 			'desc' => 'Mailing Label Generator',
			'custom_url' => 'admin/reports_mailinglabels.php',
			'creator' => 'The Grant Brothers');
 $x++;
 $report_custom[$x] = array('id' => $x, 'name' => 'Custom -- Project Details (PDF)',
 			'desc' => 'Project Details',
			'custom_url' => 'admin/reports_projects_details.php?type=pdf',
			'creator' => 'The Grant Brothers');
 $x++;
 $report_custom[$x] = array('id' => $x, 'name' => 'Custom -- Judges List (CSV)',
 			'desc' => 'Judges List',
			'custom_url' => 'admin/reports_judges.php?type=csv',
			'creator' => 'The Grant Brothers');
 $x++;
 $report_custom[$x] = array('id' => $x, 'name' => 'Custom -- Judging Teams Project Assignments (PDF)',
 			'desc' => 'Judging Teams Project Assignments',
			'custom_url' => 'admin/reports_judges_teams_projects.php?type=pdf',
			'creator' => 'The Grant Brothers');
 $x++;
 $report_custom[$x] = array('id' => $x, 'name' => 'Custom -- Judging Teams Project Assignments (CSV)',
 			'desc' => 'Judging Teams Project Assignments',
			'custom_url' => 'admin/reports_judges_teams_projects.php?type=csv',
			'creator' => 'The Grant Brothers');
 $x++;
 $report_custom[$x] = array('id' => $x, 'name' => 'Custom -- Projects Judging Team Assignments (PDF)',
 			'desc' => 'Projects Judging Team Assignments',
			'custom_url' => 'admin/reports_projects_judges_teams.php?type=pdf',
			'creator' => 'The Grant Brothers');
 $x++;
 $report_custom[$x] = array('id' => $x, 'name' => 'Custom -- Projects Judging Team Assignments (CSV)',
 			'desc' => 'Projects Judging Team Assignments',
			'custom_url' => 'admin/reports_projects_judges_teams.php?type=csv',
			'creator' => 'The Grant Brothers');
 $x++;
 $report_custom[$x] = array('id' => $x, 'name' => 'Custom -- Award List for Award Ceremony Program (CSV)',
 			'desc' => 'Award List for Award Ceremony Program creation',
			'custom_url' => 'admin/reports_program_awards.php?type=csv',
			'creator' => 'The Grant Brothers');

 function report_save_field($report, $type, $loc)
 {
 	global $allow_options;
	global $report_students_fields, $report_judges_fields, $report_awards_fields;
	global $report_committees_fields, $report_schools_fields;
	global $report_volunteers_fields, $report_fairs_fields;
	global $report_tours_fields, $report_fundraisings_fields;
	

	$fieldvar = "report_{$report['type']}s_fields";
	$allow_fields = array_keys($$fieldvar);

	/* First delete all existing fields */
	mysql_query("DELETE FROM reports_items 
			WHERE `reports_id`='{$report['id']}'
			AND `type`='$type'");
	/* Now add new ones */

	if(count($report[$type]) == 0) return;
	
	$q = '';
	$x = 0;
	foreach($report[$type] as $k=>$v) {
		if($type == 'option') {
			/* field, value, x, y, w, h, lines, face, align, valign, fn, fs, fsize, overflow */
			$vals = "'$k','$v','0','0','0','0','0','','','','','','0','truncate'";
		} else {
			if($v['lines'] == 0) $v['lines'] =1;
			$fs = is_array($v['fontstyle']) ? implode(',',$v['fontstyle']) : '';
			$opts = "{$v['align']} {$v['valign']}";
			$vals = "'{$v['field']}','{$v['value']}',
				'{$v['x']}','{$v['y']}','{$v['w']}',
				'{$v['h']}','{$v['lines']}','{$v['face']}',
				'$opts','{$v['valign']}',
				'{$v['fontname']}','$fs','{$v['fontsize']}',
				'{$v['on_overflow']}'";
		}
		if($q != '') $q .= ',';
		$q .= "({$report['id']}, '$type','$x',$vals)";
		$x++;
	}
	
	mysql_query("INSERT INTO reports_items(`reports_id`,`type`,`ord`,
				`field`,`value`,`x`, `y`, `w`, `h`,
				`lines`, `face`, `align`,`valign`,
				`fontname`,`fontstyle`,`fontsize`,`on_overflow`) 
			VALUES $q;");

	echo mysql_error();
	
 }
	
 function report_load($report_id)
 {
 	global $allow_options, $report_students_fields, $report_judges_fields;
	global $report_committees_fields, $report_awards_fields;
	global $report_schools_fields, $report_volunteers_fields;
	global $report_tours_fields, $report_fairs_fields;
	global $report_fundraisings_fields;

	$report = array();

	$q = mysql_query("SELECT * FROM reports WHERE id='$report_id'");
	$r = mysql_fetch_assoc($q);
	$report['name'] = $r['name'];
	$report['id'] = $r['id'];
	$report['system_report_id'] = $r['system_report_id']; 
	$report['desc'] = $r['desc'];
	$report['creator'] = $r['creator'];
	$report['type'] = $r['type'];

	$report['col'] = array();
	$report['sort'] = array();
	$report['group'] = array();
	$report['distinct'] = array();
	$report['options'] = array();
	$report['filter'] = array();
	$report['loc'] = array();

	$fieldvar = "report_{$report['type']}s_fields";
	if(is_array($$fieldvar)) 
		$allow_fields = array_keys($$fieldvar);
	else
		$allow_fields=array();

 	$q = mysql_query("SELECT * FROM reports_items 
			WHERE reports_id='{$report['id']}' 
			ORDER BY `ord`");
	print(mysql_error());
	
	if(mysql_num_rows($q) == 0) return $ret;

	while($a = mysql_fetch_assoc($q)) {
		$f = $a['field'];
		$t = $a['type'];
		switch($t) {
		case 'option':
			/* We dont' care about order, just construct
			 * ['option'][name] = value; */
			if(!in_array($f, $allow_options)) {
				print("Type[$type] Field[$f] not allowed.\n");
				continue;
			}
			$report['option'][$f] = $a['value'];
			break;
		default:
			if(!in_array($f, $allow_fields)) {
				print("Type[$type] Field[$f] not allowed.\n");
				continue;
			}
			/* Pull out all the data */
			$val = array();
			$col_fields = array('field', 'x', 'y', 'w', 'h', 'lines', 'face', 'align', 'valign', 'value', 'fontname','fontsize','on_overflow');
			foreach($col_fields as $lf) $val[$lf] = $a[$lf];
			$val['fontstyle'] = explode(',', $a['fontstyle']);
			/* valign, fontname, fontsize,fontstyle are unused, except in tcpdf reports 
			(i.e. nothign has changed, only adding on */

			if($val['lines'] == 0) $val['lines'] = 1;
			$opts = explode(" ", $val['align']);
			$align_opts = array ('left', 'right', 'center');
			$valign_opts = array ('vtop', 'vbottom', 'vcenter');
			$style_opts = array ('bold');
			foreach($opts as $o) {
				if(in_array($o, $align_opts)) $val['align'] = $o;
				if(in_array($o, $valign_opts)) $val['valign'] = $o;
				if(in_array($o, $valign_opts)) $val['face'] = $o;
			}

			$report[$t][$a['ord']] = $val;
			break;
		}
	}
//`int_r($report);
	return $report;
 }

 function report_save($report)
 {
 	if($report['id'] == 0) {
		/* New report */
		mysql_query("INSERT INTO reports (`id`) VALUES ('')");
		$report['id'] = mysql_insert_id();
	} else {
		/* if the report['id'] is not zero, see if this is a
		 * systeim report before doing anything. */
		$q = mysql_query("SELECT system_report_id FROM reports WHERE id='{$report['id']}'");
		$i = mysql_fetch_assoc($q);
		if(intval($i['system_report_id']) != 0) {
			/* This is a system report, the editor (should)
			 * properly setup the editor pages so that the user
			 * cannot save this report.  The only way to get here
			 * is by directly modifying the POST variables.. so..
			 * we don't have to worry about being user friendly. */
			echo "ERROR: attempt to save a system report (reports.id={$report['id']})";
			exit;
		}
	}


/*	print("<pre>");
	print_r($_POST);
	print_r($report);
	print("</pre>");
*/

 	mysql_query("UPDATE reports SET 
			`name`='".mysql_escape_string($report['name'])."',
			`desc`='".mysql_escape_string($report['desc'])."',
			`creator`='".mysql_escape_string($report['creator'])."',
			`type`='".mysql_escape_string($report['type'])."'
			WHERE `id`={$report['id']}");

	report_save_field($report, 'col', $report['loc']);
	report_save_field($report, 'group', array());
	report_save_field($report, 'sort', array());
	report_save_field($report, 'distinct', array());
	report_save_field($report, 'option', array());
	report_save_field($report, 'filter', array());
	return $report['id'];
 }

 function report_load_all() 
 {
 	$ret = array();
 	$q = mysql_query("SELECT * FROM reports ORDER BY `name`");

	while($r = mysql_fetch_assoc($q)) {
		$report = array();
	        $report['name'] = $r['name'];
	        $report['id'] = $r['id'];
	        $report['desc'] = $r['desc'];
	        $report['creator'] = $r['creator'];
	        $report['type'] = $r['type'];
		$ret[]  = $report;
	}
	return $ret; 
 }

 function report_delete($report_id)
 {
 	$r = intval($report_id);
	/* if the report['id'] is not zero, see if this is a
	 * systeim report before doing anything. */
	$q = mysql_query("SELECT system_report_id FROM reports WHERE id='$r'");
	$i = mysql_fetch_assoc($q);
	if(intval($i['system_report_id']) != 0) {
		/* This is a system report, the editor (should)
		 * properly setup the editor pages so that the user
		 * cannot delete this report.  The only way to get here
		 * is by directly modifying the POST variables.. so..
		 * we don't have to worry about being user friendly. */
		echo "ERROR: attempt to delete a system report (reports.id=$r)";
		exit;
	}
 	mysql_query("DELETE FROM reports WHERE `id`=$r");
	mysql_query("DELETE FROM reports_items WHERE `reports_id`=$r");
 }

 function report_gen($report) 
 {
 	global $config, $report_students_fields, $report_judges_fields, $report_awards_fields, $report_schools_fields;
	global $report_stock, $report_committees_fields, $report_volunteers_fields;
	global $report_tours_fields, $report_fairs_fields;
	global $report_fundraisings_fields;
	global $filter_ops;

	//print_r($report);
	$fieldvar = "report_{$report['type']}s_fields";
	$fields = $$fieldvar;

	$gen_mode = '';
	$fieldname = array();
	$thead = array();
		
	$table['header']=array();
	$table['widths']=array();
	$table['dataalign']=array();
	$table['option']=array();
	$table['total']=0;

	/* Validate the stock */
	if($report['option']['stock'] != '') {
		if(!array_key_exists($report['option']['stock'], $report_stock)) {
			echo "Invalid stock [{$report['option']['stock']}]";
			exit;
		}
	}

	switch($report['option']['type']) {
	case 'csv':
		$rep=new lcsv(i18n($report['name']));
		$gen_mode = 'table';
		break;
	case 'label':
		/* Label */
		$label_stock = $report_stock[$report['option']['stock']];
		$rep=new lpdf(	i18n($config['fairname']),
				i18n($report['name']),
				$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo.gif");
		$rep->setPageStyle("labels");
		$rep->newPage($label_stock['page_width'], $label_stock['page_height']);
		$rep->setFontSize(11);
		$rep->setLabelDimensions($label_stock['label_width'], $label_stock['label_height'],
				$label_stock['x_spacing'], $label_stock['y_spacing'],11,$label_stock['y_padding']);
		$gen_mode = 'label';
		break;
	case 'pdf': case '':
		/* FIXME: handle landscape pages in here */
		$label_stock = $report_stock[$report['option']['stock']];
		$rep=new lpdf(	i18n($config['fairname']),
				i18n($report['name']),
				$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo.gif");
		$rep->newPage($label_stock['page_width'], $label_stock['page_height']);
		if($report['option']['default_font_size']) {
			$rep->setDefaultFontSize($report['option']['default_font_size']);
			$rep->setFontSize($report['option']['default_font_size']);
		}
		else {
			$rep->setDefaultFontSize(11);
			$rep->setFontSize(11);
		}

		$gen_mode = 'table';
		if($report['option']['allow_multiline'] == 'yes') 
			$table['option']['allow_multiline'] = true;
		break;
	case 'tcpdf_label':
		$label_stock = $report_stock[$report['option']['stock']];
		$show_box = ($report['option']['label_box'] == 'yes') ? true : false;
		$show_fair = ($report['option']['label_fairname'] == 'yes') ? true : false;
		$show_logo = ($report['option']['label_logo'] == 'yes') ? true : false;

		$rep=new pdf($report['name'], $label_stock['page_format'], $label_stock['page_orientation']);
		$rep->setup_for_labels($show_box, $show_fair, $show_logo, 
				$label_stock['label_width'] * 25.4, $label_stock['label_height'] * 25.4,
				$label_stock['x_spacing'] * 25.4, $label_stock['y_spacing'] * 25.4,
				$label_stock['rows'], $label_stock['cols']);
		$gen_mode = 'tcpdf_label';
		break;

	default:
		echo "Invalid type [{$report['option']['type']}]";
		exit;
	}
	
	$sel = array();
	$x=0;
	$group_by = array();
	$post_group_by = array();
	$components = array();
	$order = array();

	$total_width = 0;
	$scale_width = 0;
	/* Add up the column widths, and figure out which
	 * ones are scalable, just in case */
	foreach($report['col'] as $o=>$d) {
		$f = $d['field'];
		$total_width += $fields[$f]['width'];
		if($fields[$f]['scalable'] == true) 
			$scale_width += $fields[$f]['width'];
	}

	/* Determine the scale factor (use the label width so
	 * we can enforce margins) */
	if($report['option']['fit_columns'] == 'yes') { // && $total_width > $label_stock['label_width'])  {
		$static_width = $total_width - $scale_width;
        if($scale_width) 
            $scale_factor = ($label_stock['label_width'] - $static_width) / $scale_width;
        else
            $scale_factor = 1.0;
	} else {
		$scale_factor = 1.0;
	}

	/* Select columns to display */
	foreach($report['col'] as $o=>$d) {
		$f = $d['field'];
		$table['header'][] = i18n($fields[$f]['header']);
		$sf = ($fields[$f]['scalable'] == true) ? $scale_factor : 1.0;
		$table['widths'][] = $fields[$f]['width'] * $sf;
		$table['dataalign'][] = 'left';
		$sel[] = "{$fields[$f]['table']} AS C$x";
		$fieldname[$f] = "C$x";
		/* We want to add these to group by, but AFTER all the other group bys */
		if(is_array($fields[$f]['group_by']))
			$post_group_by = array_merge($group_by, $fields[$f]['group_by']);

		if(is_array($fields[$f]['components'])) {
			$components = array_merge($components, 
					$fields[$f]['components']);
		}
		$x++;
	}

	/* We also want to select any column groupings, but we won't display them */
	foreach($report['group'] as $o=>$d) {
		$f = $d['field'];
		if(!isset($fieldname[$f])) {
			$sel[] = "{$fields[$f]['table']} AS G$o";
			$fieldname[$f] = "G$o";
		}

		if(isset($fields[$f]['table_sort']))
			$order[] = $fields[$f]['table_sort'];
		else
			$order[] = $fieldname[$f];

		if(is_array($fields[$f]['components'])) { 
			$components = array_merge($components, 
					$fields[$f]['components']);
		}
	}

	/* If no sort order is specified, make the first field the order */
	if(count($report['sort']) == 0) 
		$report['sort'] = array(0 => array('field' => $report['col'][0]['field']));

	foreach($report['sort'] as $o=>$d) {
		$f = $d['field'];
		if(!isset($fieldname[$f])) {
			$sel[] = "{$fields[$f]['table']} AS S$o";
			$fieldname[$f] = "S$o";
		}

		if(isset($fields[$f]['table_sort']))
			$order[] = $fields[$f]['table_sort'];
		else
			$order[] = $fieldname[$f];
	}
	
	foreach($report['distinct'] as $o=>$d) {
		$f = $d['field'];
		if(!isset($fieldname[$f])) {
			$sel[] =  "{$fields[$f]['table']} AS D$o";
			$fieldname[$f] = "D$o";
		}
		$group_by[] = $fieldname[$f];
	}

	foreach($report['filter'] as $o=>$d) {
		$f = $d['field'];
		if(!isset($fieldname[$f])) {
			$sel[] =  "{$fields[$f]['table']} AS F$o";
			$fieldname[$f] = "F$o";
		}
		$t = $filter_ops[$d['x']];
		$filter[] = "{$fields[$f]['table']} $t '{$d['value']}'";
		if(is_array($fields[$f]['components'])) { 
			$components = array_merge($components, 
					$fields[$f]['components']);
		}
	}
	$sel = implode(",", $sel);
	$order = implode(",", $order);
		
	
	if(!isset($report['year'])) {
		$report['year'] = $config['FAIRYEAR'];
	}
	
	$group_by = array_merge($group_by, $post_group_by);
	$group_query = "";
	if(count($group_by)) {
		$group_query = "GROUP BY ".implode(",", $group_by);
	}

	$filter_query = "";
	if(count($filter)) {
		$filter_query = " AND ".implode(" AND ", $filter);
	}
	
	$func = "report_{$report['type']}s_fromwhere";
	$q = call_user_func_array($func, array($report, $components));

	$q = "SELECT $sel  $q  $filter_query $group_query ORDER BY $order";
	$r = mysql_query($q);

//	print_r($report);
//	print_r($report['filter']);
//	echo "$q";

	if($r == false) {
		echo "The report database query has failed.  This is
		unfortunate but not your fault.  Please send the following to
		your fair administrator, or visit <a
		href=\"http://www.sfiab.ca\">http://www.sfiab.ca</a> and submit
		a bug report so we can get this fixed.<br />"; 
		echo "<pre>";
		echo "Query: [$q]<br />";
		echo "Error: [".mysql_error()."]<br />";
		echo "</pre>";
		exit;
	}
	echo mysql_error();

	$ncols = count($report['col']);
	$n_groups = count($report['group']);
	$last_group_data = array();

//	echo "<pre>";print_r($rep);

	while($i = mysql_fetch_assoc($r)) {

		if($n_groups > 0) {
			$group_change = false;
			/* See if any of the "group" fields have changed */
			foreach($report['group'] as $x=>$g) {
				$c = $fieldname[$g['field']];				

				if($fields[$g['field']]['exec_function'])
					$i_c=call_user_func_array($fields[$g['field']]['exec_function'], array($report,$f,$i[$c]));
				else
					$i_c=$i[$c];

				if($last_group_data[$c] != $i_c)
					$group_change = true;

				$last_group_data[$c] = $i_c;
			}

			if($group_change) {
				/* Dump the last table */
				if(count($table['data'])) {
				//	print_r($table);
					$rep->addTable($table);
					$rep->nextLine();
					$table['data'] = array();
					$table['total'] = 0;
					/* Start a new page AFTER a table is
					* dumped, so the first page doesn't
					* end up blank */
					if($report['option']['group_new_page'] == 'yes') {
						$rep->newPage();
					} else {
						$rep->hr();
						$rep->vspace(-0.1);
					}
				}
				
				/* Construct a new header */
				$h = implode(" -- ", $last_group_data);
				$rep->heading($h);
				$rep->nextLine();
			}
			
		}

		$data = array();
		if($gen_mode == 'label') {
			$show_box = ($report['option']['label_box'] == 'yes') ? true : false;
			$show_fair = ($report['option']['label_fairname'] == 'yes') ? true : false;
			$show_logo = ($report['option']['label_logo'] == 'yes') ? true : false;
			$rep->newLabel($show_box, $show_fair, $show_logo);
		} else if($gen_mode == 'tcpdf_label') {
			$rep->label_new();
		}

		foreach($report['col'] as $o=>$d) {
			$f = $d['field'];
			if(is_array($fields[$f]['value_map'])) {
				$v = $fields[$f]['value_map'][$i["C$o"]];
			} else if(is_callable($fields[$f]['exec_function'])) {
				$v = call_user_func_array($fields[$f]['exec_function'], array($report, $f, $i["C$o"]));
//			} else if(isset($fields[$f]['exec_code'])) {
//				Somethign like this, how do we pass $i["C$o"] in?
//				$v = exec($fields[$f]['exec_code']);
			} else {
				$v =  $i["C$o"];
			}
			if($gen_mode == 'table') {
				$data[] = $v;
			} else if($gen_mode == 'label') {
				$opt = array();
				if($d['face'] == 'bold') $opt[] = 'bold';
				$opt[] = $d['align'];
				$opt[] = $d['valign'];
				if($report['option']['field_box'] == 'yes') 
					$opt[] = 'field_box';


				/* Special column, draw a box */
				if($f == 'static_box') {
					$rep->addLabelBox($d['x'], $d['y'], $d['w'],
								$d['h']);
				} else {
					/* Special column, override result with static text */
					if($f == 'static_text') $v = $d['value'];

					$lh = ($d['lines'] == 0) ? 0 : $d['h']/$d['lines'];
					$rep->addLabelText2($d['x'], $d['y'], $d['w'],
							$d['h'], $lh,
							$v, $opt);
				}
			} else if($gen_mode == 'tcpdf_label') {
				/* Setup additional options */
				$show_box = ($report['option']['field_box'] == 'yes') ? true : false;

//				echo "<pre>"; print_r($d);

				if($f == 'static_box') {
					$rep->label_rect($d['x'], $d['y'], $d['w'], $d['h']);
				} else {
					if($f == 'static_text') $v = $d['value'];

					$v = iconv("ISO-8859-1//TRANSLIT", "UTF-8", $v);

					$rep->label_text($d['x'], $d['y'], $d['w'], $d['h'],
							$v, $show_box, $d['align'], $d['valign'],
							$d['fontname'],$d['fontstyle'],$d['fontsize'],
							$d['on_overflow']);
				}

			}

			if($fields[$f]['total'] == true)
				$table['total'] += $v;
		}
		if(count($data)) $table['data'][] = $data;
	}
	if(count($table['data'])) {
		$rep->addTable($table);
	}


	$rep->output();
}

?>
