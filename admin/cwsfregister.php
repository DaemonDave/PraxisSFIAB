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

include "xml.inc.php";
 require_once("../user.inc.php");

 function get_cwsf_award_winners()
 {
 	global $config;
 	$winners=array();

 	$q=mysql_query("SELECT * FROM award_awards WHERE cwsfaward='1' AND year='".$config['FAIRYEAR']."'");
	if(mysql_num_rows($q)==1)
	{
		$award=mysql_fetch_object($q);
		$pq=mysql_query("SELECT * FROM award_prizes WHERE award_awards_id='$award->id'");
		while($prize=mysql_fetch_object($pq))
		{
			$wq=mysql_query("SELECT 
						projects.id,
						projects.projectnumber,
						projects.title,
						projects.summary,
						projects.registrations_id,
						projects.cwsfdivisionid,
						projects.projectdivisions_id
						
					FROM 
						winners,
						projects
					WHERE 
						winners.projects_id=projects.id AND
						awards_prizes_id='$prize->id' AND 
						winners.year='".$config['FAIRYEAR']."'");
						echo mysql_error();
			while($project=mysql_fetch_object($wq))
			{
				$sq=mysql_query("SELECT * FROM students WHERE registrations_id='$project->registrations_id' AND year='".$config['FAIRYEAR']."'");
				$students=array();
				$cwsf_agecategory=0;
				while($s=mysql_fetch_object($sq))
				{
					if($s->grade>=7 && $s->grade<=8)
					{
						if($cwsf_agecategory<1) 
							$cwsf_agecategory=1;
					}
					if($s->grade>=9 && $s->grade<=10)
					{
						if($cwsf_agecategory<2) 
							$cwsf_agecategory=2;
					}
					if($s->grade>=11 && $s->grade<=13)
					{
						if($cwsf_agecategory<3) 
							$cwsf_agecategory=3;
					}
					$students[]=array(
								"xml_type"=>"student",
								"firstname"=>$s->firstname,
								"lastname"=>$s->lastname,
								"email"=>$s->email,
								"gender"=>$s->sex,
								"grade"=>$s->grade,
								"language"=>$s->lang,
								"birthdate"=>$s->dateofbirth,
								"address1"=>$s->address,
								"address2"=>"",
								"city"=>$s->city,
								"province"=>$s->province,
								"postalcode"=>$s->postalcode,
								"homephone"=>$s->phone,
								"cellphone"=>"",
							);
				}
				$winners[]=array(
						"xml_type"=>"project",
						"projectid"=>$project->id,
						"projectnumber"=>$project->projectnumber,
						"title"=>$project->title,
						"abstract"=>$project->summary,
						"category_id"=>$cwsf_agecategory,
						"division_id"=>$project->cwsfdivisionid,
						"projectdivisions_id"=>$project->projectdivisions_id,
						"students"=>$students,
						);
			}
		}
		//print_r($award);
	}
	return $winners;
 }



?>
<?
 require("../common.inc.php");
 require("../projects.inc.php");
 user_auth_required('committee', 'admin');
 send_header("One-Click CWSF Registration",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "one-click_cwsf_registration"
			);
 echo "<br />";

 if(count($_POST['cwsfdivision']))
 {
 	foreach($_POST['cwsfdivision'] AS $p=>$d)
	{
		mysql_query("UPDATE projects SET cwsfdivisionid='$d' WHERE id='$p'");
	}
	echo happy(i18n("CWSF Project Divisions saved"));
 }

 if($_POST['action']=="register" && $_POST['xml'])
 {
 	if(function_exists('curl_init'))
	{
		$ch = curl_init(); /// initialize a cURL session
		curl_setopt ($ch, CURLOPT_URL,"https://secure.ysf-fsj.ca/registration/xmlregister.php");
		curl_setopt ($ch, CURLOPT_HEADER, 0); /// Header control
		curl_setopt ($ch, CURLOPT_POST, 1);  /// tell it to make a POST, not a GET
		curl_setopt ($ch, CURLOPT_POSTFIELDS, "xml=".$_POST['xml']);  /// put the query string here starting with "?"
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); /// This allows the output to be set into a variable $datastream
		curl_setopt ($ch, CURLOPT_POSTFIELDSIZE, 0);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 360);
		curl_setopt ($ch, CURLOPT_SSLVERSION, 3);
	    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
		$datastream = curl_exec ($ch); /// execute the curl session and return the output to a variable $datastream
		$datastream = str_replace(" standalone=\"yes\"","",$datastream);
		// echo "curl close <br />";
		curl_close ($ch); /// close the curl session

		echo i18n("The YSC Registration Server said:")."<br />";
		echo notice($datastream);
	}
	else
	{
		echo error("CURL Support Missing");
		echo i18n("Your PHP installation does not support CURL.  You will need to login to the YSC system as the regional coodinator and upload the XML data manually");
	}
	send_footer();
	exit;
 }

 /* Load the YSC fair */
 $q = mysql_query("SELECT * FROM fairs WHERE abbrv='YSC'");
 if(mysql_num_rows($q) < 1) {
 	echo error(i18n("You have not defined the YSC upstream fair in the Science Fair Management area."));
	$ok = false;
 } else {
	 $f = mysql_fetch_assoc($q);
	 $ysc_region_id = $f['username'];
	 $ysc_region_password = $f['password'];
 }
 $ok=true;
 //make sure we have the ysc_region_id and ysc_region_password
 if($ysc_region_id == '') {
 	echo error(i18n("You have not yet specified a username for YSC (your Region ID).  Go to the <a href=\"sciencefairs.php\">Science Fair Management</a> page to set it"));
	$ok=false;
 }
 if($ysc_region_password == '') {
 	echo error(i18n("You have not yet specified a password for YSC (your Region Password).  Go to the <a href=\"sciencefairs.php\">Science Fair Management</a> page to set it"));
	$ok=false;
 }

 if($ok)
 {
 	$q=mysql_query("SELECT * FROM award_awards WHERE cwsfaward='1' AND year='".$config['FAIRYEAR']."'");
	if(!mysql_num_rows($q))
	{
		echo error(i18n("Cannot find an award that is specified as the Canada-Wide Science Fair Award"));
		echo i18n("Please go to the awards manager and select which award identifies your CWSF students");
	}
	else if(mysql_num_rows($q)>1)
	{
		echo error(i18n("There is more than one award that is identified as your Canada-Wide Science Fair award."));
		echo i18n("Please go to the awards manager and choose only one award that identifies your CWSF students");
	}
	else
	{
		$award=mysql_fetch_object($q);
		echo "<b>".i18n("CWSF Award").":</b> ".$award->name."<br />";
		echo i18n("Please review the list of winning projects/students below.  If it is all correct then you can click the 'Register for CWSF' button at the bottom of the page to send the information to YSC");
		echo "<br />";
		echo "<br />";
		$winners=get_cwsf_award_winners();
		echo "<b>".i18n("Found %1 CWSF prize winners",array(count($winners)))."</b>";
		echo "<br />";
		$error=false;
		echo "<form method=\"post\" action=\"cwsfregister.php\">";
		echo "<table class=\"tableview\"><thead>";
		echo "<tr><th>".i18n("Project Information")."</th>";
		echo "<th>".i18n("Project Division / CWSF Project Division")."</th>";
		echo "</tr></thead>";

		foreach($winners AS $winner)
		{
			echo "<tr><td>";
			echo "<b>";
			echo $winner['projectnumber']." - ".$winner['title'];
			echo "</b>";
			echo "<br />";
			foreach($winner['students'] AS $s)
			{
				echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;";
				echo i18n("Name").": ";
				echo $s['firstname']." ".$s['lastname'];
				echo "<br />";

				echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;";
				echo i18n("Email").": ".$s['email'];
				if(!$s['email'])
				{
					echo error(i18n("No Email Address"),"inline");
					$error=true;
				}
				echo "<br />";
				echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;"; echo "&nbsp;";

				echo i18n("Grade").": ".$s['grade'];
				if(!$s['grade'])
				{
					echo error(i18n("No Grade"),"inline");
					$error=true;
				}

				echo "<br />";
			}
			if(!$winner['division_id'])
			{
				echo "<br />";
				echo error(i18n("Choose a CWSF Division"),"inline");
				$error=true;
			}

			echo "</td><td>";

			$t=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' AND id='".$winner['projectdivisions_id']."'");
			$tr=mysql_fetch_object($t);
			echo $tr->division;
			echo "<br />";

			echo "<select name=\"cwsfdivision[".$winner['projectid']."]\">";
			echo "<option value=\"\">".i18n("No corresponding CWSF division")."</option>\n";
			foreach($CWSFDivisions AS $k=>$v)
			{
				if($winner['division_id'])
				{
					if($k==$winner['division_id']) $sel="selected=\"selected\""; else $sel="";
				}
				else
				{

					if($k==$tr->cwsfdivisionid) $sel="selected=\"selected\""; else $sel="";
				}

				echo "<option $sel value=\"$k\">".i18n($v)."</option>\n";
			}
			echo "</select>\n";

			echo "</td></tr>";

		}
		echo "<tr><td></td><td>";
		echo "<input type=\"submit\" value=\"Save CWSF Divisions\">";
		echo "</td></tr>";
		echo "</table>";
		echo "</form>";

		if(!$error)
		{
			$reg=array("registration"=>array(
					"ysf_region_id"=>$ysc_region_id,
					"ysf_region_password"=>$ysc_region_password,
					"projects"=>$winners
					)
				);

			$output="";
			xmlCreateRecurse($reg);
			$xmldata=$output;

			echo "<h3>".i18n("The following data will be sent to Youth Science Canada")."</h3>";
			echo "<form method=\"post\" action=\"cwsfregister.php\">";
			echo "<input type=\"hidden\" name=\"action\" value=\"register\">";
			echo "<textarea rows=\"15\" cols=\"80\" name=\"xml\">";
			echo $xmldata;
			echo "</textarea>";
			echo "<br />";
			echo "<br />";
			echo i18n("Warning!  You can only use this feature once, and it will send whatever data is listed above.  If you try to submit this data a second time to YSC it will not work.  So please make sure everything is correct before submitting!");
			echo "<br />";
			echo "<br />";
			echo "<input type=\"submit\" value=\"".i18n("Register for CWSF")."\">";
			echo "</form>";
		}
		else
		{
			echo error(i18n("You must correct the above errors before registration can proceed"));

		}
	}



 }

 send_footer();

?>
