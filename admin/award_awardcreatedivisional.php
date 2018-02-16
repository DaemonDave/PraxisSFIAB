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

 send_header('Create All Divisional Awards',
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Awards Main' => 'admin/awards.php')
		);

 if($_GET['sponsors_id']) $sponsors_id=$_GET['sponsors_id'];
 else if($_POST['sponsors_id']) $sponsors_id=$_POST['sponsors_id'];

 if($_GET['award_types_id']) $award_types_id=$_GET['award_types_id'];
 else if($_POST['award_types_id']) $award_types_id=$_POST['award_types_id'];

	//first, we can only do this if we dont have any type=divisional awards created yet
	$q=mysql_query("SELECT COUNT(id) AS num FROM award_awards WHERE award_types_id='1' AND year='{$config['FAIRYEAR']}'");
	$r=mysql_fetch_object($q);
	if($r->num)
	{
		echo error(i18n("%1 Divisional awards already exist.  There must not be any divisional awards in order to run this wizard",array($r->num)));
	}
	else
	{

		$q=mysql_query("SELECT * FROM projectdivisions WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
		while($r=mysql_fetch_object($q))
			$div[$r->id]=$r->division;

		$q=mysql_query("SELECT * FROM projectcategories WHERE year='".$config['FAIRYEAR']."' ORDER BY id");
		while($r=mysql_fetch_object($q))
			$cat[$r->id]=$r->category;

		$dkeys = array_keys($div);
		$ckeys = array_keys($cat);

		if($config['filterdivisionbycategory']=="yes") {
			$q=mysql_query("SELECT * FROM projectcategoriesdivisions_link WHERE year='".$config['FAIRYEAR']."' ORDER BY projectdivisions_id,projectcategories_id");
			$divcat=array();
			while($r=mysql_fetch_object($q)) {
				$divcat[]=array("c"=>$r->projectcategories_id,"d"=>$r->projectdivisions_id);
			}

		}
		else {
			$divcat=array();
			foreach($dkeys AS $d) {
				foreach($ckeys AS $c) {
					$divcat[]=array("c"=>$c,"d"=>$d);
				}
			}
		}


		if($_GET['action']=="create" && $_GET['sponsors_id'])
		{
			$q=mysql_query("SELECT * FROM award_prizes WHERE year='-1' AND award_awards_id='0' ORDER BY `order`");
			$prizes=array();
			while($r=mysql_fetch_object($q))
			{
				$prizes[]=array(
						"cash"=>$r->cash,
						"scholarship"=>$r->scholarship,
						"value"=>$r->value,
						"prize"=>$r->prize,
						"number"=>$r->number,
						"excludefromac"=>$r->excludefromac,
						"trophystudentkeeper"=>$r->trophystudentkeeper,
						"trophystudentreturn"=>$r->trophystudentreturn,
						"trophyschoolkeeper"=>$r->trophyschoolkeeper,
						"trophyschoolreturn"=>$r->trophyschoolreturn,
						"order"=>$r->order);
			}

			$ord=1;
			echo "<br />";
			foreach($divcat AS $dc) {
				$d_id=$dc['d'];
				$c_id=$dc['c'];
				$d_division=$div[$d_id];
				$c_category=$cat[$c_id];
					
					echo i18n("Creating %1 - %2",array($c_category,$d_division))."<br />";
					mysql_query("INSERT INTO award_awards (sponsors_id,award_types_id,name,criteria,`order`,year) VALUES (
						'{$_GET['sponsors_id']}',
						'1',
						'$c_category - $d_division',
						'".i18n("Best %1 projects in the %2 division",array($c_category,$d_division))."',
						'$ord',
						'{$config['FAIRYEAR']}'
					)");
					echo mysql_error();
					$award_awards_id=mysql_insert_id();


					mysql_query("INSERT INTO award_awards_projectcategories (award_awards_id,projectcategories_id,year) VALUES ('$award_awards_id','$c_id','{$config['FAIRYEAR']}')");
					mysql_query("INSERT INTO award_awards_projectdivisions (award_awards_id,projectdivisions_id,year) VALUES ('$award_awards_id','$d_id','{$config['FAIRYEAR']}')");

					$ord++;

					echo "&nbsp;&nbsp;".i18n("Prizes: ");
					foreach($prizes AS $prize)
					{
						mysql_query("INSERT INTO award_prizes (award_awards_id,cash,scholarship,value,prize,number,`order`,excludefromac,trophystudentkeeper,trophystudentreturn,trophyschoolkeeper,trophyschoolreturn,year) VALUES (
							'$award_awards_id',
							'{$prize['cash']}',
							'{$prize['scholarship']}',
							'{$prize['value']}',
							'{$prize['prize']}',
							'{$prize['number']}',
							'{$prize['order']}',
							'{$prize['excludefromac']}',
							'{$prize['trophystudentkeeper']}',
							'{$prize['trophystudentreturn']}',
							'{$prize['trophyschoolkeeper']}',
							'{$prize['trophyschoolreturn']}',
							'{$config['FAIRYEAR']}'
							)");
						echo $prize['prize'].",";
					}
					echo "<br />";
			}
			echo happy(i18n("All divisional awards and prizes successfully created"));
			echo "<a href=\"award_awards.php\">".i18n("Go to awards manager")."</a>\n";
		}
		else
		{
			echo "<br />";
			echo i18n("Please choose the sponsor and create the prizes that will be added to all divisional awards");
			echo "<form method=\"get\" action=\"award_awardcreatedivisional.php\">";


			echo "<table>";
			echo "<tr><td>".i18n("Sponsor").":</td><td>";
			$sq=mysql_query("SELECT id,organization FROM sponsors ORDER BY organization");
			echo "<select name=\"sponsors_id\">";
			//only show the "choose a sponsor" option if we are adding,if we are editing, then they must have already chosen one.
			echo $firstsponsor;
			while($sr=mysql_fetch_object($sq))
			{
				if($sr->id == $sponsors_id)
					$sel="selected=\"selected\"";
				else
					$sel="";
				echo "<option $sel value=\"$sr->id\">".i18n($sr->organization)."</option>";
			}
			echo "</select>";
			echo "</td></tr>";

			echo "<tr><td>".i18n("Prizes")."</td><td><a href=\"award_prizes.php?award_awards_id=-1\">Edit prize template for divisional awards</a>";
			//the 'generic' template prizes for the awards are stored with year =-1 and award_awards_id=0

			$q=mysql_query("SELECT * FROM award_prizes WHERE year='-1' AND award_awards_id='0' ORDER BY `order`");

			if(mysql_num_rows($q))
			{
			/*
				echo "<form method=\"post\" action=\"award_prizes.php\">";
				echo "<input type=\"hidden\" name=\"action\" value=\"reorder\">";
				echo "<input type=\"hidden\" name=\"award_awards_id\" value=\"$award_awards_id\">";
			*/

				echo "<table class=\"summarytable\">";
				echo "<tr>";
			//	echo " <th>".i18n("Order")."</th>";
				echo " <th>".i18n("Prize Description")."</th>";
				echo " <th>".i18n("Cash Amount")."</th>";
				echo " <th>".i18n("Scholarship Amount")."</th>";
				echo " <th>".i18n("Value")."</th>";
				echo " <th>".i18n("# of Prizes")."</th>";
	//			echo " <th>Actions</th>";
				echo "</tr>\n";


				while($r=mysql_fetch_object($q))
				{
					echo "<tr>\n";
					echo " <td>$r->prize</td>\n";
					echo " <td align=\"right\">";
					if($r->cash) echo "\$$r->cash";
					else echo "&nbsp;";
					echo " </td>";
					echo " <td align=\"right\">";
					if($r->scholarship) echo "\$$r->scholarship";
					else echo "&nbsp;";
					echo " </td>";
					echo " <td align=\"right\">";
					if($r->value) echo "\$$r->value";
					else echo "&nbsp;";
					echo " </td>";
					echo " <td align=\"center\">$r->number</td>\n";
					echo "</tr>\n";
				}

				echo "</table>\n";
			}

			echo "</td></tr>";
			echo "</table>";

			echo "<b>".i18n("We will create the following awards with the prizes listed above").":</b>";
			echo "<br />";

			foreach($divcat AS $dc) {
				$d_id=$dc['d'];
				$c_id=$dc['c'];
				$d_division=$div[$d_id];
				$c_category=$cat[$c_id];
					
				echo i18n($c_category)." - ".i18n($d_division)."<br />";
			}

			echo "<input type=\"hidden\" name=\"action\" value=\"create\">";
			echo "<input type=\"submit\" value=\"".i18n("Create all divisional awards")."\">";
			echo "</form>";
		}

	}
 send_footer();
?>
