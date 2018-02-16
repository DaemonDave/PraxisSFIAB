<?
	//THIS FILE IS NEEDED to fix a bug in the rollover script that some people might have already used
	//the prizes werent properly rolled over, so if it detects that there are no prizes this year but there were
	//prizes last year, it re-rolls the prizes over properly.
	//it only does this if the number of awards matchces exactly (aka hasnt been modified yet since the rollover)
	//it is safe to include this script at any point, since it does all the checks required.
	//this file will eventually be deleted

	//we know the years needed, so hardcode them in
	$currentfairyear=2007;
	$newfairyear=2008;

	//first make sure they have indeed done the rollover...
	if($config['FAIRYEAR']==2008)
	{

	//make sure the number of awards are identical (aka they havent added any new ones)
	$nq1=mysql_query("SELECT * FROM award_awards WHERE year='$newfairyear'");
	$nq2=mysql_query("SELECT * FROM award_awards WHERE year='$currentfairyear'");
	if(mysql_num_rows($nq1)==mysql_num_rows($nq2)) 
	{
		$npq1=mysql_query("SELECT * FROM award_prizes WHERE year='$newfairyear'");
		$npq2=mysql_query("SELECT * FROM award_prizes WHERE year='$currentfairyear'");

		if(mysql_num_rows($npq2)>0 && mysql_num_rows($npq1)==0) 
		{


	echo "<br />";
	echo notice(i18n("A BUG WAS IDENTIFIED IN YOUR PREVIOUS YEAR ROLLOVER WHICH CAUSED AWARD PRIZES TO NOT BE ROLLED OVER PROPERLY.  THEY ARE NOW BEING RE-ROLLED OVER WITH THE PROPER PRIZE INFORMATION.  THIS WILL ONLY HAPPEN ONCE."))."<br />";
	mysql_query("DELETE FROM award_awards WHERE year='$newfairyear'");
	mysql_query("DELETE FROM award_prizes WHERE year='$newfairyear'");
	mysql_query("DELETE FROM award_contacts WHERE year='$newfairyear'");
	mysql_query("DELETE FROM award_types WHERE year='$newfairyear'");
	mysql_query("DELETE FROM award_awards_projectcategories WHERE year='$newfairyear'");
	mysql_query("DELETE FROM award_awards_projectdivisions WHERE year='$newfairyear'");

	echo i18n("Rolling awards")."<br />";
		//awards
		$q=mysql_query("SELECT * FROM award_awards WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
		{
			mysql_query("INSERT INTO award_awards (award_sponsors_id,award_types_id,name,criteria,presenter,`order`,year,excludefromac,cwsfaward) VALUES (
				'".mysql_escape_string($r->award_sponsors_id)."',
				'".mysql_escape_string($r->award_types_id)."',
				'".mysql_escape_string($r->name)."',
				'".mysql_escape_string($r->criteria)."',
				'".mysql_escape_string($r->presenter)."',
				'".mysql_escape_string($r->order)."',
				'".mysql_escape_string($newfairyear)."',
				'".mysql_escape_string($r->excludefromac)."',
				'".mysql_escape_string($r->cwsfaward)."')");
			$award_awards_id=mysql_insert_id();
			
			$q2=mysql_query("SELECT * FROM award_awards_projectcategories WHERE year='$currentfairyear' AND award_awards_id='$r->id'");
			echo mysql_error();
			while($r2=mysql_fetch_object($q2))
			{
				mysql_query("INSERT INTO award_awards_projectcategories (award_awards_id,projectcategories_id,year) VALUES (
				'".mysql_escape_string($award_awards_id)."',
				'".mysql_escape_string($r2->projectcategories_id)."',
				'".mysql_escape_string($newfairyear)."')");

			}

			$q2=mysql_query("SELECT * FROM award_awards_projectdivisions WHERE year='$currentfairyear' AND award_awards_id='$r->id'");
			echo mysql_error();
			while($r2=mysql_fetch_object($q2))
			{
				mysql_query("INSERT INTO award_awards_projectdivisions (award_awards_id,projectdivisions_id,year) VALUES (
				'".mysql_escape_string($award_awards_id)."',
				'".mysql_escape_string($r2->projectdivisions_id)."',
				'".mysql_escape_string($newfairyear)."')");

			}

			echo i18n("&nbsp; Rolling award prizes")."<br />";
			$q2=mysql_query("SELECT * FROM award_prizes WHERE year='$currentfairyear' AND award_awards_id='$r->id'");
			echo mysql_error();
			while($r2=mysql_fetch_object($q2))
			{
				mysql_query("INSERT INTO award_prizes (award_awards_id,cash,scholarship,`value`,prize,number,`order`,year,excludefromac) VALUES (
				'".mysql_escape_string($award_awards_id)."',
				'".mysql_escape_string($r2->cash)."',
				'".mysql_escape_string($r2->scholarship)."',
				'".mysql_escape_string($r2->value)."',
				'".mysql_escape_string($r2->prize)."',
				'".mysql_escape_string($r2->number)."',
				'".mysql_escape_string($r2->order)."',
				'".mysql_escape_string($newfairyear)."',
				'".mysql_escape_string($r2->excludefromac)."')");
			}
		}

		echo i18n("Rolling award contacts")."<br />";
		//award contacts
		$q=mysql_query("SELECT * FROM award_contacts WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO award_contacts (award_sponsors_id,salutation,firstname,lastname,position,email,phonehome,phonework,phonecell,fax,notes,year) VALUES (
				'".mysql_escape_string($r->award_sponsors_id)."',
				'".mysql_escape_string($r->salutation)."',
				'".mysql_escape_string($r->firstname)."',
				'".mysql_escape_string($r->lastname)."',
				'".mysql_escape_string($r->position)."',
				'".mysql_escape_string($r->email)."',
				'".mysql_escape_string($r->phonehome)."',
				'".mysql_escape_string($r->phonework)."',
				'".mysql_escape_string($r->phonecell)."',
				'".mysql_escape_string($r->fax)."',
				'".mysql_escape_string($r->notes)."',
				'".mysql_escape_string($newfairyear)."')");

		echo i18n("Rolling award types")."<br />";
		//award types
		$q=mysql_query("SELECT * FROM award_types WHERE year='$currentfairyear'");
		echo mysql_error();
		while($r=mysql_fetch_object($q))
			mysql_query("INSERT INTO award_types (id,type,`order`,year) VALUES (
				'".mysql_escape_string($r->id)."',
				'".mysql_escape_string($r->type)."',
				'".mysql_escape_string($r->order)."',
				'".mysql_escape_string($newfairyear)."')");

		}
	}
	}
?>
