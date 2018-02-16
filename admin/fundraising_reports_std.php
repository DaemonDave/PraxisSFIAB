<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2009 James Grant <james@lightbox.org>

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
 require("fundraising_common.inc.php");
 require_once("../lpdf.php");
 require_once("../lcsv.php");
 
 $id=intval($_GET['id']);
 $type=$_GET['type'];


 if($id && $type) {
	 switch($id) {
		 case 1:
			 if($type=="csv") {
				 $rep=new lcsv($config['FAIRNAME']);

			 } else if($type=="pdf") {
				$rep=new lpdf(	i18n($config['fairname']),
						i18n("List of Prospects By Appeal"),
						$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
						);
				$rep->newPage();
				$rep->setFontSize(8);
			 }
			 $sql="SELECT * FROM fundraising_campaigns WHERE fiscalyear='{$config['FISCALYEAR']}' ";
			 if($_GET['fundraising_campaigns_id']) {
				 $sql.=" AND id='".intval($_GET['fundraising_campaigns_id'])."'";
			 }
			 $sql.=" ORDER BY name";
			 $q=mysql_query($sql);
			 echo mysql_error();
			 while($r=mysql_fetch_object($q)) {
				 $rep->heading($r->name);
				 $table=array();
				 $table['header']=array("Name","Contact","Phone","Address","$ appeal","$ this year","$ last year","%chg");
				 $table['widths']=array(1.5,1,1,1,0.9,0.9,0.9,0.5);
				 $table['dataalign']=array("left","left","left","left","right","right","right","center");

				$thisyear=$config['FISCALYEAR'];
				$lastyear=$config['FISCALYEAR']-1;

				 $pq=mysql_query("SELECT * FROM fundraising_campaigns_users_link WHERE fundraising_campaigns_id='$r->id'");
				 while($pr=mysql_fetch_object($pq)) {
					 $u=user_load_by_uid($pr->users_uid);
					 //hopefully this never returns false, but who knows..
					 if($u) {
						 //we only want the primaries, yea, i know... we have this werid confusing between a USER being linked to a sponsor and then a sponsor having multiple users
						 //and then only getting the primary contact for the sponsor even if it might not be the user thats in teh campaign... my brain hurts
						// if($u['primary']=="no")
						//	 continue;
						//gah i dont know what the heck to do here

						if($u['sponsors_id']) {
							$cq=mysql_query("SELECT SUM(value) AS total FROM fundraising_donations WHERE sponsors_id='{$u['sponsors_id']}' AND fundraising_campaigns_id='$r->id' AND status='received' AND fiscalyear='$thisyear'");
							$cr=mysql_fetch_object($cq);
							$thisappeal=$cr->total;
							$cq=mysql_query("SELECT SUM(value) AS total FROM fundraising_donations WHERE sponsors_id='{$u['sponsors_id']}' AND status='received' AND fiscalyear='$thisyear'");
							$cr=mysql_fetch_object($cq);
							$thisyeartotal=$cr->total;
							$cq=mysql_query("SELECT SUM(value) AS total FROM fundraising_donations WHERE sponsors_id='{$u['sponsors_id']}' AND status='received' AND fiscalyear='$lastyear'");
							$cr=mysql_fetch_object($cq);
							$lastyeartotal=$cr->total;
							if($lastyeartotal)
								$change=round(($thisyeartotal-$lastyeartotal)/$lastyeartotal*100);
							else
								$change="N/A";
							$name=$u['sponsor']['organization'];

						}
						else {
							$name=$u['firstname']." " .$u['lastname'];
							$thisappeal=0;
							$thisyeartotal=0;
							$lastyeartotal=0;
							$change=0;	

						}
						$table['data'][]=array(
						$name,
						$u['firstname']." " .$u['lastname'],
						$u['phonework'],
						$u['address']." ".$u['address2'],
						$thisappeal,
						$thisyeartotal,
						$lastyeartotal,
						$change	
						);

					}
				 }
				 $rep->addTable($table);

			 }

		 break;

		 case 2:
			 if($type=="csv") {
				 $rep=new lcsv($config['FAIRNAME'],'Results of Appeal by Purpose',"");

			 } else if($type=="pdf") {
				$rep=new lpdf(	i18n($config['fairname']),
						i18n("Results of Appeal by Purpose"),
						$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
						);
				$rep->newPage();
				$rep->setFontSize(8);
			 }
			 $sql="SELECT * FROM fundraising_goals WHERE fiscalyear='{$config['FISCALYEAR']}' ";
			 if($_GET['goal']) {
				 $sql.=" AND goal='".mysql_real_escape_string($_GET['goal'])."'";
			 }
			 $sql.=" ORDER BY name";
			 $q=mysql_query($sql);
			 echo mysql_error();

			 while($r=mysql_fetch_object($q)) {
				 $rep->heading($r->name)." (".$r->budget.")";

				 $table=array();
				 $table['header']=array("Appeal Name","Target","Received","% to Budget","# of Prospects","# of Donors/Sponsors","Rate of Response","Average Amount Given");
				 $table['widths']=array(1.5,0.5,0.5,0.75,0.9,0.9,0.9,0.5);
				 $table['dataalign']=array("left","right","right","center","center","center","center","right");

				 $cq=mysql_query("SELECT * FROM fundraising_campaigns WHERE fundraising_goal='$r->goal' AND fiscalyear='{$config['FISCALYEAR']}'");
				 while($cr=mysql_fetch_object($cq)) {
					 $table['data'][]=array(
						$cr->name,
						$cr->target,
						$received,
						$percenttobudget,
						$numprospects,
						$numdonors,
						$rate,
						$avgamount);
				 }

				 $rep->addTable($table);
			 }


		 break;
	}

	$rep->output();
 }
 else
	 header("Location: fundraising_reports.php");

?>
