<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2005-2009 James Grant <james@lightbox.org>

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
 require_once("../config_editor.inc.php");
 user_auth_required('committee', 'config');
 send_header("Year Rollover",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"rollover_fair_year"
			);
 ?>

 <script language="javascript" type="text/javascript">
 function confirmYearRollover()
 {
 	var currentyear=<?=$config['FAIRYEAR']?>;
	var nextyear=document.forms.rollover.nextfairyear.value;
	if(nextyear<currentyear)
		alert('You cannot roll backwards in years!');
	else if(nextyear==currentyear)
		alert('You cannot roll to the same year!');
	else
	{

		var okay=confirm('Are you sure you want to roll the FAIRYEAR from '+currentyear+' to '+nextyear+'? This can not be undone and should only be done if you are absolutely sure!');
		if(okay)
			return true; 
	}
	return false;
 }
 </script>
 <?

 function roll($currentfairyear, $newfairyear, $table, $where='', $replace=array())
 {
	/*  Field 	Type 			Null 	Key 	Default 	Extra
	 * id 		int(10) unsigned 	NO 	PRI 	NULL 	auto_increment
	 * sponsors_id 	int(10) unsigned 	NO 	MUL 	0 	 
	 * award_source_fairs_id int(10) unsigned YES  	   	NULL  	 
	*/

	/* Get field list for this table */
 	$q = mysql_query("SHOW COLUMNS IN `$table`");
	while(($c = mysql_fetch_assoc($q))) {
		$col[$c['Field']] = $c;
	}

	/* Record fields we care about */
	$fields = array();
	$keys = array_keys($col);
	foreach($keys as $k) {
		/* Skip id field */
		if($col[$k]['Extra'] == 'auto_increment') continue;
		/* Skip year field */
		if($k == 'year') continue;

		$fields[] = $k;
	} 

	if($where == '') $where='1';

	/* Get data */
	$q=mysql_query("SELECT * FROM $table WHERE year='$currentfairyear' AND $where");
	echo mysql_error();
	$names = '`'.join('`,`', $fields).'`';

	/* Process data */
	while($r=mysql_fetch_assoc($q)) {
		$vals = '';
		foreach($fields as $f) {
			if(array_key_exists($f, $replace))
				$vals .= ",'".mysql_real_escape_string($replace[$f])."'";
			else if($col[$f]['Null'] == 'YES' && $r[$f] == NULL)
				$vals .= ',NULL';
			else 
				$vals .= ",'".mysql_real_escape_string($r[$f])."'";
		}
		mysql_query("INSERT INTO `$table`(`year`,$names) VALUES ('$newfairyear'$vals)");
		echo mysql_error();	
	}
 }

 if($_POST['action']=="rollover" && $_POST['nextfairyear'])
 {
 	$newfairyear=intval($_POST['nextfairyear']);
	$currentfairyear=intval($config['FAIRYEAR']);

	$cy = $currentfairyear;
	$ny = $newfairyear;

	if($newfairyear<$currentfairyear)
		echo error(i18n("You cannot roll backwards in years!"));
	else if($newfairyear==$currentfairyear)
		echo error(i18n("You cannot roll to the same year!"));
	else
	{
		//okay here we go! this is going to get to be a pretty big script me thinks!

		//first, lets do all of the configuration variables
		echo i18n("Rolling configuration variables")."<br />";
		config_update_variables($newfairyear, $currentfairyear);

		//now the dates
		echo i18n("Rolling dates")."<br />";
		$q=mysql_query("SELECT DATE_ADD(date,INTERVAL 365 DAY) AS newdate,name,description FROM dates WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO dates (date,name,description,year) VALUES (
				'".mysql_real_escape_string($r->newdate)."',
				'".mysql_real_escape_string($r->name)."',
				'".mysql_real_escape_string($r->description)."',
				'".mysql_real_escape_string($newfairyear)."')");
		

		//page text
		echo i18n("Rolling page texts")."<br />";
		$q=mysql_query("SELECT * FROM pagetext WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO pagetext (textname,textdescription,text,lastupdate,year,lang) VALUES (
				'".mysql_real_escape_string($r->textname)."',
				'".mysql_real_escape_string($r->textdescription)."',
				'".mysql_real_escape_string($r->text)."',
				'".mysql_real_escape_string($r->lastupdate)."',
				'".mysql_real_escape_string($newfairyear)."',
                '".mysql_real_escape_string($r->lang)."')");

		echo i18n("Rolling project categories")."<br />";
		//project categories
		$q=mysql_query("SELECT * FROM projectcategories WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO projectcategories (id,category,category_shortform,mingrade,maxgrade,year) VALUES (
				'".mysql_real_escape_string($r->id)."',
				'".mysql_real_escape_string($r->category)."',
				'".mysql_real_escape_string($r->category_shortform)."',
				'".mysql_real_escape_string($r->mingrade)."',
				'".mysql_real_escape_string($r->maxgrade)."',
				'".mysql_real_escape_string($newfairyear)."')");

		echo i18n("Rolling project divisions")."<br />";
		//project divisions
		$q=mysql_query("SELECT * FROM projectdivisions WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO projectdivisions (id,division,division_shortform,cwsfdivisionid,year) VALUES (
				'".mysql_real_escape_string($r->id)."',
				'".mysql_real_escape_string($r->division)."',
				'".mysql_real_escape_string($r->division_shortform)."',
				'".mysql_real_escape_string($r->cwsfdivisionid)."',
				'".mysql_real_escape_string($newfairyear)."')");

		echo i18n("Rolling project category-division links")."<br />";
		//project categories divisions links
		$q=mysql_query("SELECT * FROM projectcategoriesdivisions_link WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO projectcategoriesdivisions_link (projectdivisions_id,projectcategories_id,year) VALUES (
				'".mysql_real_escape_string($r->projectdivisions_id)."',
				'".mysql_real_escape_string($r->projectcategories_id)."',
				'".mysql_real_escape_string($newfairyear)."')");

		echo i18n("Rolling project sub-divisions")."<br />";
		//project subdivisions
		$q=mysql_query("SELECT * FROM projectsubdivisions WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO projectsubdivisions (id,projectdivisions_id,subdivision,year) VALUES (
				'".mysql_real_escape_string($r->id)."',
				'".mysql_real_escape_string($r->projectsubdivisions_id)."',
				'".mysql_real_escape_string($r->subdivision)."',
				'".mysql_real_escape_string($newfairyear)."')");

		echo i18n("Rolling safety questions")."<br />";
		//safety questions 
		$q=mysql_query("SELECT * FROM safetyquestions WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO safetyquestions (question,type,required,ord,year) VALUES (
				'".mysql_real_escape_string($r->question)."',
				'".mysql_real_escape_string($r->type)."',
				'".mysql_real_escape_string($r->required)."',
				'".mysql_real_escape_string($r->ord)."',
				'".mysql_real_escape_string($newfairyear)."')");

		echo i18n("Rolling awards")."<br />";
		//awards


		$q=mysql_query("SELECT * FROM award_awards WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q)) {
			/* Roll the one award */
			roll($cy, $ny, 'award_awards', "id='{$r->id}'");
			$award_awards_id=mysql_insert_id();

			roll($cy, $ny, 'award_awards_projectcategories', "award_awards_id='{$r->id}'",
								array('award_awards_id' => $award_awards_id));

			roll($cy, $ny, 'award_awards_projectdivisions', "award_awards_id='{$r->id}'",
								array('award_awards_id' => $award_awards_id));
			echo i18n("&nbsp; Rolling award prizes")."<br />";
			roll($cy, $ny, 'award_prizes', "award_awards_id='{$r->id}'",
								array('award_awards_id' => $award_awards_id));
		}

		echo i18n("Rolling award types")."<br />";
		//award types
		$q=mysql_query("SELECT * FROM award_types WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO award_types (id,type,`order`,year) VALUES (
				'".mysql_real_escape_string($r->id)."',
				'".mysql_real_escape_string($r->type)."',
				'".mysql_real_escape_string($r->order)."',
				'".mysql_real_escape_string($newfairyear)."')");

		echo i18n("Rolling schools")."<br />";
		//award types
		$q=mysql_query("SELECT * FROM schools WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q)) {
			$puid = ($r->principal_uid == null) ? 'NULL' : ("'".intval($r->principal_uid)."'");
			$shuid = ($r->sciencehead_uid == null) ? 'NULL' : ("'".intval($r->sciencehead_uid)."'");


			mysql_query("INSERT INTO schools (school,schoollang,schoollevel,board,district,phone,fax,address,city,province_code,postalcode,principal_uid,schoolemail,sciencehead_uid,accesscode,lastlogin,junior,intermediate,senior,registration_password,projectlimit,projectlimitper,year) VALUES (
				'".mysql_real_escape_string($r->school)."',
				'".mysql_real_escape_string($r->schoollang)."',
				'".mysql_real_escape_string($r->schoollevel)."',
				'".mysql_real_escape_string($r->board)."',
				'".mysql_real_escape_string($r->district)."',
				'".mysql_real_escape_string($r->phone)."',
				'".mysql_real_escape_string($r->fax)."',
				'".mysql_real_escape_string($r->address)."',
				'".mysql_real_escape_string($r->city)."',
				'".mysql_real_escape_string($r->province_code)."',
				'".mysql_real_escape_string($r->postalcode)."',$puid,
				'".mysql_real_escape_string($r->schoolemail)."',$shuid,
				'".mysql_real_escape_string($r->accesscode)."',
				NULL,
				'".mysql_real_escape_string($r->junior)."',
				'".mysql_real_escape_string($r->intermediate)."',
				'".mysql_real_escape_string($r->senior)."',
				'".mysql_real_escape_string($r->registration_password)."',
				'".mysql_real_escape_string($r->projectlimit)."',
				'".mysql_real_escape_string($r->projectlimitper)."',
				'".mysql_real_escape_string($newfairyear)."')");
		}

		echo i18n("Rolling questions")."<br />";
		$q = mysql_query("SELECT * FROM questions WHERE year='$currentfairyear'");
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO questions (id,year,section,db_heading,question,type,required,ord) VALUES (
				'',
				'$newfairyear',
				'".mysql_real_escape_string($r->section)."',
				'".mysql_real_escape_string($r->db_heading)."',
				'".mysql_real_escape_string($r->question)."',
				'".mysql_real_escape_string($r->type)."',
				'".mysql_real_escape_string($r->required)."',
				'".mysql_real_escape_string($r->ord)."')");

		//regfee items
		echo i18n("Rolling registration fee items")."<br />";
		roll($cy, $ny, 'regfee_items');

		//volunteer positions
		echo i18n('Rolling volunteer positions')."<br />";
		roll($cy, $ny, 'volunteer_positions');

		//timeslots and rounds
		echo i18n('Rolling judging timeslots and rounds')."<br />";
		$q=mysql_query("SELECT * FROM judges_timeslots WHERE year='$currentfairyear' AND round_id='0'");
                echo mysql_error();
		while($r=mysql_fetch_assoc($q)) {
			$d = $newfairyear - $currentfairyear;
			mysql_query("INSERT INTO judges_timeslots (`year`,`round_id`,`type`,`date`,`starttime`,`endtime`,`name`)
				VALUES ('$newfairyear','0','{$r['type']}',DATE_ADD('{$r['date']}', INTERVAL $d YEAR),
					'{$r['starttime']}','{$r['endtime']}','{$r['name']}')");
			echo mysql_error();
			$round_id = mysql_insert_id();
			$qq = mysql_query("SELECT * FROM judges_timeslots WHERE round_id='{$r['id']}'");
			echo mysql_error();
			while($rr=mysql_fetch_assoc($qq)) {
				mysql_query("INSERT INTO judges_timeslots (`year`,`round_id`,`type`,`date`,`starttime`,`endtime`)
						VALUES ('$newfairyear','$round_id','timeslot',DATE_ADD('{$rr['date']}', INTERVAL $d YEAR),
							'{$rr['starttime']}','{$rr['endtime']}')");
			}
		}

		echo "<br /><br />";
		mysql_query("UPDATE config SET val='$newfairyear' WHERE var='FAIRYEAR' AND year=0");
		echo happy(i18n("Fair year has been rolled over from %1 to %2",array($currentfairyear,$newfairyear)));
		send_footer();
		exit;
	}
 }

 echo "<br />";
 echo "<a href=\"backuprestore.php\">".i18n("You should consider making a database backup before rolling over, just in case!")."</a><br />\n";
 echo "<br />";
 echo "<form name=\"rollover\" method=\"post\" action=\"rollover.php\" onsubmit=\"return confirmYearRollover()\">";
 echo "<input type=\"hidden\" name=\"action\" value=\"rollover\" />";
 echo i18n("Current Fair Year").": <b>".$config['FAIRYEAR']."</b><br />";
 $nextfairyear=$config['FAIRYEAR']+1;
 echo i18n("Next Fair Year").": <input size=\"8\" type=\"text\" name=\"nextfairyear\" value=\"$nextfairyear\" />";
 echo "<br />";
 echo "<input type=\"submit\" value=\"".i18n("Rollover Fair Year")."\" />";
 echo "</form>";

 send_footer();
?>
