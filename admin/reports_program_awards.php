<?
 require("../common.inc.php");
 require_once("../user.inc.php");
 user_auth_required('committee', 'admin');
 require("../lpdf.php");
 require("../lcsv.php");

 $type=$_GET['type'];
 if(!$type) $type="pdf";

	if($type=="pdf")
	{

		$rep=new lpdf(	i18n($config['fairname']),
				i18n("Program Awards"),
				$_SERVER['DOCUMENT_ROOT'].$config['SFIABDIRECTORY']."/data/logo-200.gif"
				);

		$rep->newPage();
		$rep->setFontSize(11);
	}
	else if($type=="csv")
	{
		$rep=new lcsv(i18n("Program Awards"));
	}
	$q=mysql_query("SELECT 
				award_awards.id,
				award_awards.name,
				award_awards.criteria,
				award_awards.presenter,
				award_awards.order AS awards_order,
				award_types.type
			FROM 
				award_awards,
				award_types
			WHERE 
					award_awards.year='".$config['FAIRYEAR']."'
				AND	award_types.year='".$config['FAIRYEAR']."'
				AND	award_awards.award_types_id=award_types.id
				AND	award_awards.excludefromac='0'
				AND	(award_types.type='special' OR award_types.type='grand')
			ORDER BY awards_order");

	echo mysql_error();

	if(mysql_num_rows($q))
	{
		while($r=mysql_fetch_object($q))
		{
			$rep->heading(i18n($r->name));

			//get teh age categories
			$acq=mysql_query("SELECT projectcategories.category FROM projectcategories, award_awards_projectcategories  WHERE projectcategories.year='".$config['FAIRYEAR']."' AND award_awards_projectcategories.year='".$config['FAIRYEAR']."' AND award_awards_projectcategories.award_awards_id='$r->id' AND award_awards_projectcategories.projectcategories_id=projectcategories.id ORDER BY projectcategories.id");
			echo mysql_error();
			$cats="";
			while($acr=mysql_fetch_object($acq))
			{
				$cats.=i18n($acr->category).", ";
			}
			$cats=substr($cats,0,-2);
			$rep->addText("$cats: ".i18n($r->criteria));

			$pq=mysql_query("SELECT 
						award_prizes.prize,
						award_prizes.number,
						award_prizes.id,
						award_prizes.cash,
						award_prizes.scholarship
					FROM 
						award_prizes 
					WHERE 
						award_awards_id='$r->id' 
						AND award_prizes.year='".$config['FAIRYEAR']."'
						AND award_prizes.excludefromac='0'
					ORDER BY 
						`order`");
					echo mysql_error();
			$prevprizeid=-1;
			while($pr=mysql_fetch_object($pq))
			{
				if($prevprizeid!=$pr->id)
				{
					$prizetext="";
					if($pr->number>1)
						$prizetext.=i18n("%1 prizes of",array($pr->number))." ";

					if($pr->prize)
						$prizetext.=i18n($pr->prize);

					if($pr->cash || $pr->scholarship)
					{
						if($pr->prize)
							$prizetext.=" (";
						if($pr->cash && $pr->scholarship)
							$prizetext.="\$$pr->cash / \$$pr->scholarship ".i18n("scholarship");
						else if($pr->cash)
							$prizetext.= "\$$pr->cash";
						else if($pr->scholarship)
							$prizetext.= "\$$pr->scholarship ".i18n("scholarship");

						if($pr->prize)
							$prizetext.= ")";

					
					}
					$rep->addText($prizetext);

					$prevprizeid=$pr->id;
				}
			}
			$rep->nextLine();
		}
	}
	$rep->output();
?>
