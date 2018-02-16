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
 require("../lpdf.php");
 require("../lcsv.php");
 require("../questions.inc.php");

 if(!$_GET['type']) $type="csv";
 else $type=$_GET['type'];

if($type=="pdf")
{
	$rep=new lpdf(	i18n($config['fairname']),
			i18n("Judge List"),
			$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
			);

	$rep->newPage();
	$rep->setFontSize(11);
}
else if($type=="csv")
{
	$rep=new lcsv(i18n("Judge List"));
}

$table=array();
$table['header']=array( 
			i18n("ID"),	
			i18n("Unique ID"),	
			i18n("Last Name"),
			i18n("First Name"),
			i18n("Email"),
			i18n("Phone Home"),
			i18n("Phone Work"),
			i18n("Phone Work Ext"),
			i18n("Phone Cell"),
			i18n("Languages"),
			i18n("Organization"),
			i18n("Address 1"),
			i18n("Address 2"),
			i18n("City"),
			i18n($config['provincestate']),
			i18n($config['postalzip']),
			i18n("Highest PostSecDeg"),
			i18n("Professional Quals"),
			i18n("Expertise Other"));

/* Append headers for all the custom questions */
$qs=questions_load_questions('judgereg', $config['FAIRYEAR']);
$keys = array_keys($qs);
foreach($keys as $qid) {
	$table['header'][] = i18n($qs[$qid]['db_heading']);
}


//grab the list of divisions, because the last fields of the table will be the sub-divisions 
$q=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
$numcats=mysql_num_rows($q);
$catheadings=array();
while($r=mysql_fetch_object($q))
{
	$cats[]=$r->id;
	$catheadings[]="$r->category (out of 5)";
}
//grab the list of divisions, because the last fields of the table will be the sub-divisions 
$q=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
$divheadings=array();
while($r=mysql_fetch_object($q))
{
	$divs[]=$r->id;
	$divheadings[]="$r->division (out of 5)";
	$divheadings[]="$r->division subdivisions";
}

//now append the arrays together
$table['header']=array_merge($table['header'],array_merge($catheadings,$divheadings));


//fill these in if we ever make this PDFable
$table['widths']=array();
$table['dataalign']=array();

$q=mysql_query("SELECT 
				users.*,
				users_judge.*
			FROM 
				users
				JOIN users_judge ON users.id=users_judge.users_id
			WHERE 
				users.deleted='no' AND 
				users.year='".$config['FAIRYEAR']."'
				AND users.types LIKE '%judge%'

			ORDER BY 
				lastname,
				firstname");
echo mysql_error();
while($r=mysql_fetch_object($q)) {
	$u=user_load($r->id);

	$expertise_other=str_replace("\n"," ",$r->expertise_other);
	$expertise_other=str_replace("\r","",$expertise_other);

	if(isset($divdata)) unset($divdata); $divdata=array();
	if(isset($catdata)) unset($catdata); $catdata=array();
	$languages="";

	foreach($u['cat_prefs'] AS $c) {
		$catdata[]=$c+2;
	}

	foreach($u['div_prefs'] AS $d) {
		$divdata[]=$d;
		//FIXME: 2010-01-22 - James - get the sub divisions for now we use a placeholder
		$divdata[]="";
	}

	foreach($u['languages'] AS $k=>$v) {
		$languages.="$v/";
	}
	$languages=substr($languages,0,-1);

	$qarray = array();
	$qans = questions_load_answers('judgereg', $r->id, $config['FAIRYEAR']);
	$keys = array_keys($qans);
	foreach($keys as $qid) {
		$qarray[] = $qans[$qid];
	}
		
	$tmp=array(
		$r->id,
		$r->uid,
		$r->lastname,
		$r->firstname,
		$r->email,
		$r->phonehome,
		$r->phonework,
		$r->phoneworkext,
		$r->phonecell,
		$languages,
		$r->organization,
		$r->address,
		$r->address2,
		$r->city,
		$r->province,
		$r->postalcode,
		$r->highest_psd,
		$r->professional_quals,
		$expertise_other
		);
	$tmp = array_merge($tmp, $qarray);

	$extradata=array_merge($catdata,$divdata);
	$table['data'][]=array_merge($tmp,$extradata);
}

$rep->addTable($table);
$rep->output();

?>
