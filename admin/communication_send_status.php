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

 if($_GET['action']=="status") {
	 $q=mysql_query("SELECT * FROM emailqueue WHERE finished IS NULL");

	 if($config['emailqueue_lock'] || mysql_num_rows($q)) {
		echo "<h4>".i18n("Active Send Queues")."</h4>\n";
		$q=mysql_query("SELECT *,UNIX_TIMESTAMP(started) AS ts FROM emailqueue WHERE finished IS NULL ORDER BY started DESC");

	 	if(!$config['emailqueue_lock']) {
			echo error(i18n("It looks like there's emails waiting to send, but the sending process isnt running.").
			"<br />".
			"<a href=\"communication.php?action=restartqueue\">".i18n("Click here to manually restart the process")."</a>");
		}


		echo "<table class=\"tableview\">";
		echo "<thead><tr>";
		echo " <th>".i18n("Name")."</th>\n";
		echo " <th>".i18n("Subject")."</th>\n";
		echo " <th>".i18n("Started")."</th>\n";
		echo " <th>".i18n("Progress")."</th>\n";
		echo " <th>".i18n("Duration")."</th>\n";
		echo " <th>".i18n("ETA")."</th>\n";
		echo " <th>".i18n("Cancel")."</th>\n";
		echo "</tr></thead>\n";

		while($r=mysql_fetch_object($q)) {
			echo "<tr>";
			echo " <td>$r->name</td>\n";
			echo " <td>$r->subject</td>\n";
			echo " <td>$r->started</td>\n";
			$remaining=$r->numtotal-$r->numsent;
			$now=time();
			$duration=$now-$r->ts;
			$num=$r->numsent+$r->numfailed;
			echo " <td align=\"center\">$num  / $r->numtotal</td>\n";
			echo "<td>";
			echo format_duration($duration);
			echo "</td>";
			echo "<td>";
			if($r->numsent || $r->numfailed) {
				$emailspersecond=($r->numsent+$r->numfailed)/$duration;
				$remainingduration=$remaining/$emailspersecond;
				echo format_duration($remainingduration);
			}
			else {
				echo "Unknown";
			}
			echo "</td>";
			echo "<td><a href=\"#\" onclick=\"return cancelQueue($r->id)\">".i18n("cancel")."</td>";
			echo "</tr>\n";
		}
		echo "</table>";
		echo "<br /><br />\n";
	 }
	 else {
		echo notice("No Email Communications are currently being sent out");
		?>
		<script type="text/javascript">
			stopRefreshing();
		</script>
		<?
	 }

	$q=mysql_query("SELECT * FROM emailqueue WHERE finished IS NOT NULL ORDER BY started DESC LIMIT 10");
	echo "<h4>".i18n("Completed Send Queues")."</h4>\n";
	echo "<table class=\"tableview\">\n";
	echo "<thead><tr>";
	echo " <th>".i18n("Name")."</th>\n";
	echo " <th>".i18n("Subject")."</th>\n";
	echo " <th>".i18n("Started")."</th>\n";
	echo " <th>".i18n("Finished")."</th>\n";
	echo " <th>".i18n("Total Emails")."</th>\n";
	echo " <th>".i18n("Success")."</th>\n";
	echo " <th>".i18n("Failed")."</th>\n";
	//FIXME: comment bounced until we implement it
//	echo " <th>".i18n("Bounced")."</th>\n";
	echo "</tr></thead>\n";
	while($r=mysql_fetch_object($q)) {
		echo "<tr>";
		echo " <td>$r->name</td>\n";
		echo " <td>$r->subject</td>\n";
		echo " <td>$r->started</td>\n";
		echo " <td>$r->finished</td>\n";
		echo " <td align=\"center\">$r->numtotal</td>\n";
		echo " <td align=\"center\">$r->numsent</td>\n";
		echo " <td align=\"center\">$r->numfailed</td>\n";
		//echo " <td align=\"center\">$r->numbounced</td>\n";
		echo "</tr>\n";
	}
	echo "</table>\n";
	 exit;
 }

 send_header("Communication Sending Status",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Communication' => 'admin/communication.php')
			);
 ?>
 <script type="text/javascript">
 	$(document).ready( function() {
		refreshStatus();
	});
 var refreshTimeout;
 function refreshStatus() {
	 $("#queuestatus").load("communication_send_status.php?action=status",null,function() {
		 <? if($config['emailqueue_lock']) { ?> 
		 	refreshTimeout=setTimeout('refreshStatus()',1000); 
		<?  } ?>
	 });
 }

 function stopRefreshing() {
	 if(refreshTimeout) {
		 clearTimeout(refreshTimeout);
		 window.location.href="communication_send_status.php";
	 }
 }

 function cancelQueue(id) {
	 $("#debug").load("communication.php?action=cancel&cancel="+id,null,function() { if(!refreshTimeout) refreshStatus(); });
 }

 </script>
 <?
 echo "<br />";
 echo "<div id=\"queuestatus\" style=\"margin-left: 20px;\">";
 echo "</div>";
 echo "<br />";

send_footer();
?>
