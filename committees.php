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
 require_once('common.inc.php');
 require_once('user.inc.php');
 send_header("Committee List", null, "committee_management");

	echo "<table>";
	$q=mysql_query("SELECT * FROM committees ORDER BY ord,name");
	while($r=mysql_fetch_object($q)) {
		/* Select all the users in the committee, using MAX(year) for the most recent year */
		$q2=mysql_query("SELECT committees_link.*,users.uid,MAX(users.year),users.lastname
							FROM committees_link LEFT JOIN users ON users.uid = committees_link.users_uid 
							WHERE committees_id='{$r->id}' 
							GROUP BY users.uid ORDER BY ord,users.lastname ");
			
		//if there's nobody in this committee, then just skip it and go on to the next one.
		if(mysql_num_rows($q2)==0)
			continue;

		echo "<tr>";
		echo "<td colspan=\"3\"><h3>{$r->name}</h3>";
		echo "</td></tr>\n";

		echo mysql_error();
		while($r2=mysql_fetch_object($q2)) {

			$uid = $r2->users_uid;
			$u = user_load_by_uid($uid);

			$output=$config['committee_publiclayout'];

			$name=$r2->firstname.' '.$r2->lastname;
			$output=str_replace("name",$u['name'],$output);
			$output=str_replace("title",$r2->title,$output);

			//make sure we do emailprivate before email so we dont match the wrong thing
			if($u['emailprivate'] && $u['displayemail']=='yes') {
				list($b,$a)=split("@",$u['emailprivate']);
				$output=str_replace("emailprivate","<script language=\"javascript\" type=\"text/javascript\">em('$b','$a')</script>",$output);
			} else
				 $output=str_replace("emailprivate","",$output);

			if($u['email'] && $u['displayemail']=='yes') {
				list($b,$a)=split("@",$u['email']);
				$output=str_replace("email","<script language=\"javascript\" type=\"text/javascript\">em('$b','$a')</script>",$output);
			} else
				$output=str_replace("email","",$output);

			$output=str_replace("phonehome",$u['phonehome'],$output);
			$output=str_replace("phonework",$u['->phonework'],$output);
			$output=str_replace("phonecell",$u['->phonecell'],$output);
			$output=str_replace("fax",$u['fax'],$output);

			echo $output;

			
			/*
			echo "<td>";
			echo "&nbsp; &nbsp; <b>$r2->name</b>";
			echo "</td><td>";
			if($r2->title) echo "&nbsp; &nbsp; $r2->title";
			else echo "&nbsp;";
			echo "</td><td>";
			if($r2->email)
			{
				echo "&nbsp; &nbsp; &nbsp;";
				list($b,$a)=split("@",$r2->email);
				echo "<script language=javascript>em('$b','$a')</script>";
			}
			else
				echo "&nbsp;";

			echo "</td></tr>\n";
			*/
		}
		echo "<tr><td>&nbsp;</td></tr>\n";
	}
	echo "</table>";

	echo "<br />";
	echo "<a href=\"contact.php\">".i18n("Contact a committee member")."</a>";

	send_footer();
?>
