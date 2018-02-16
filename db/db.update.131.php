<?

function db_update_131_pre()
{
	global $config;
	$year = $config['FAIRYEAR'];
    //since there's only ever been award sponsors in the system, we can 
    //add a sponsorship entry with a value of the total sum of the prizes given
    //for each sponsor

    $q=mysql_query("SELECT * FROM sponsors");
    while($r=mysql_fetch_object($q)) {
        $total=0;
        $awardq=mysql_query("SELECT * FROM award_awards WHERE sponsors_id='$r->id' AND year='$year'");
        while($awardr=mysql_fetch_object($awardq)) {
            $prizeq=mysql_query("SELECT cash,scholarship,value,number FROM award_prizes WHERE award_awards_id='$awardr->id'");
            while($prizer=mysql_fetch_object($prizeq)) {
                //some people never set the value for some reason, i dunno why.. 
                $realvalue=max($prizer->cash+$prizer->scholarship,$prizer->value);
                $totalvalue=$realvalue*$prizer->number;
                $total+=$totalvalue;
            }
        }
        echo "Creating sponsorship for ID: $r->id value: $total\n";
        mysql_query("INSERT INTO sponsorships (sponsors_id,fundraising_type,value,status,probability,year) VALUES (
            '$r->id',
            'sfawards',
            '$total',
            'pending',
            '25',
            '$year')");
        mysql_query("INSERT INTO sponsors_logs (sponsors_id,dt,users_id,log) VALUES ('$r->id',NOW(),0,'Automatically created sponsorship from existing sponsor. type=award, value=\$$total, status=pending, probability=25%')");
    }
	
}

?>
