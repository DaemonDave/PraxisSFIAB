<?
//149 user inc works fine here
include "db.update.149.user.inc.php";

function db_update_149_post() {
	$q=mysql_query("SELECT * FROM emergencycontact");
	while($r=mysql_fetch_object($q))  {
		$relation=strtolower(trim($r->relation));
		if(			levenshtein('parent',$relation)<2 
				|| levenshtein('mother',$relation)<3 
				|| levenshtein('father',$relation)<3 
				|| levenshtein('mom',$relation)<2 
				|| levenshtein('mere',$relation)<3 
				|| levenshtein('dad',$relation)<2
				|| levenshtein('pere',$relation)<3
				|| strstr($relation,'dad') 
				|| strstr($relation,'mom') 
				|| (strstr($relation,"mother") && !strstr($relation,"grand"))
				|| (strstr($relation,"father") && !strstr($relation,"grand"))
		)  
		{ 
			echo "YES: $r->firstname $r->lastname with relation '$r->relation' looks like a parent\n";
			if($r->email) {
				echo "  Have email, creating record - $r->email\n";
				if($u=db149_user_load_by_email($r->email)) {
					echo "  This user already exists, linking parent record to their account!\n";
					if(!in_array("parent",$u['types']))
						db149_user_create("parent",$r->email,$u);
					else
						echo "   - Already a parent, no need to re-add!\n";
				}
				else {
					echo "Creating new parent record\n";
					$u=db149_user_create("parent",$r->email);
					$u['firstname']=$r->firstname;
					$u['lastname']=$r->lastname;
					$u['phonehome']=$r->phone1;
					$u['phonework']=$r->phone2;
					db149_user_save($u);
				}
			}
			else {
				echo "  No email address, skipping\n";
			}
		}   
		else {
			echo "NO:  $r->firstname $r->lastname with relation '$r->relation' is NOT a parent\n";
		}
	}
}
?>
