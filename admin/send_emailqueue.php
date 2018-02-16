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
include "../common.inc.php";
include "communication.inc.php";
$sleepmin=500000;  // 0.5 seconds
$sleepmax=2000000; // 2.0 second

echo date("r")."\n";
if(!$config['emailqueue_lock'])  
{
	mysql_query("UPDATE config SET val='".date("r")."' WHERE var='emailqueue_lock'");

	//loop forever, but not really, it'll get break'd as soon as there's nothing left to send
	while(true) 
	{
		$q=mysql_query("SELECT * FROM emailqueue_recipients WHERE sent IS NULL AND result IS NULL LIMIT 1");
		if(mysql_num_rows($q)) 
		{
			$r=mysql_fetch_object($q);
			$eq=mysql_query("SELECT * FROM emailqueue WHERE id='$r->emailqueue_id'");
			$email=mysql_fetch_object($eq);

			$blank=array();
			$replacements=(array)json_decode($r->replacements);

			if($email->body)
				$body=communication_replace_vars($email->body,$blank,$replacements);
			else if($email->bodyhtml) 
			{
				$body=strip_tags(communication_replace_vars($email->bodyhtml,$blank,$replacements));
			}
			else
			{
				$body="No message body specified";
			}
			
			if($email->bodyhtml)
				$bodyhtml=communication_replace_vars($email->bodyhtml,$blank,$replacements);

			if($r->toname) {
				$to="\"$r->toname\" <$r->toemail>";
			}
			else {
				$to=$r->toemail;
 		}


//
/// ADDED by DRE 2018
//
	// create a local file to see variable contents for debugging purposes.
	$date =  date('Y-m-d');
	$now =  time();	
	$from = $email->from;
	$subject = $email->subject;
	
	$summary = array( $date, $now, $to, $from, $subject, $body );
	$file = 'email_send-logs.out';
	$data = implode(",", $summary);
	$handle = fopen($file, "a+");
	fwrite($handle, $data."\n");
	fclose($handle);
//
///
//

			$result=email_send_new($to,$email->from,$email->subject,$body,$bodyhtml);

			if($result) {
				mysql_query("UPDATE emailqueue_recipients SET sent=NOW(), `result`='ok' WHERE id='$r->id'");
				echo mysql_error();
				$newnumsent=$email->numsent+1;
				mysql_query("UPDATE emailqueue SET numsent=$newnumsent WHERE id='$email->id'");
				echo mysql_error();
				echo "ok\n";
			}
			else {
				mysql_query("UPDATE emailqueue_recipients SET `sent`=NOW(), `result`='failed' WHERE id='$r->id'");
				echo mysql_error();
				$newnumfailed=$email->numfailed+1;
				mysql_query("UPDATE emailqueue SET numfailed=$newnumfailed WHERE id='$email->id'");
				echo mysql_error();
				echo "failed\n";
			}
			//now check if we're done yet
			$rq=mysql_query("SELECT COUNT(*) AS num FROM emailqueue_recipients WHERE sent IS NULL AND emailqueue_id='$email->id'");
			$rr=mysql_fetch_object($rq);
			if($rr->num==0) {
				mysql_query("UPDATE emailqueue SET finished=NOW() WHERE id='$email->id'");
			}
			usleep(rand($sleepmin,$sleepmax));
		}
		else
			break;
	}
	mysql_query("UPDATE config SET val='' WHERE var='emailqueue_lock'");
}
else {
	echo "Already locked\n";
}

?>
