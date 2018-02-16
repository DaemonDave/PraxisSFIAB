<?
//FIXME: I just ripped these out of the fair year rollover since they are no longer tied to the fair year, they are now tied to the FISCAL year, we'll need to implement a new fiscal year rollover mechanism similar to the fairyear rollover
//FIXME: The table names are also wrong since i've now renamed htem all, will fix when the fiscal rollover is implemented
include "../common.inc.php";

if(array_key_exists('action', $_POST)){
	switch($_POST['action']){
		case 'rollover':
			// error check the data that's getting posted
			$year = $_POST['year'];
			if(!is_numeric($year)){
				error_("Not a valid year");
				break;
			}
			if($year <= $config['FISCALYEAR']){
				error_("The new fiscal year must be after the current one");
				break;
			}

			// ok, the request checks out, let's go ahead and do the rollover
//			echo "Updating to the year $year";
			echo rolloverfiscalyear($year);
			break;
		default:

	}
	exit;
}

 send_header("Fiscal Year Rollover",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php')
            ,"rollover_fiscal_year"
			);
draw_body();
send_footer();
exit;
function draw_body(){
	global $config;
?>

	<script language="javascript" type="text/javascript">

	function confirmYearRollover(){
		var currentyear = <?=$config['FISCALYEAR']?>;
		var nextyear = document.forms.rollover.nextfiscalyear.value;

		if(nextyear<currentyear)
			alert('You cannot roll backwards in years!');
		else if(nextyear==currentyear)
			alert('You cannot roll to the same year!');
		else {
			var okay=confirm('Are you sure you want to roll the FISCALYEAR from '+currentyear+' to '+nextyear+'? This can not be undone and should only be done if you are absolutely sure!');
			if(okay){
				$.post('rolloverfiscal.php', {'action':'rollover', 'year':$('#nextfiscalyear').val()}, function(result){
					$('#results').html(result);
				});
			}
		}
		return false;
	}
	</script>
<?
	echo "<br />";
	echo "<a href=\"backuprestore.php\">".i18n("You should consider making a database backup before rolling over, just in case!")."</a><br />\n";
	echo "<br />";
	echo "<form name=\"rollover\" method=\"post\" action=\"rolloverfiscal.php\" onsubmit=\"return confirmYearRollover()\">";
	echo i18n("Current Fiscal Year").": <b>".$config['FISCALYEAR']."</b><br />";
	$nextfiscalyear = $config['FISCALYEAR'] + 1;
	echo i18n("Next Fiscal Year").": <input size=\"8\" type=\"text\" id=\"nextfiscalyear\" value=\"$nextfiscalyear\" />";
	echo "<br />";
	echo "<input type=\"submit\" value=\"".i18n("Rollover Fiscal Year")."\" />";
	echo "</form>";
	echo "<div id=\"results\"></div>";
}

function rolloverfiscalyear($newYear){
	global $config;
	$oldYear = $config['FISCALYEAR'];
	$yearDiff = $newYear - $oldYear;

	// first we'll roll over fundraising_campaigns:
	$fields = "`name`,`type`,`startdate`,`enddate`,`followupdate`,`active`,`target`,`fundraising_goal`,`filterparameters`";
	$q = mysql_query("SELECT $fields FROM fundraising_campaigns WHERE fiscalyear = $oldYear");
	while(mysql_error() == null && $r = mysql_fetch_assoc($q)){
		foreach(array('startdate','enddate','followupdate') as $dateField){
			$dateval = $r[$dateField];
			$parts = explode('-', $dateval);
			if($parts[0] != '0000')
				$parts[0] += $yearDiff;
			$r[$dateField] = implode('-', $parts);
		}
		$r['fiscalyear'] = $newYear;

		$fields = array_keys($r);
		$values = array_values($r);
		foreach($values as $idx => $val){
			$values[$idx] = mysql_real_escape_string($val);
		}
		$query = "INSERT INTO fundraising_campaigns (`" . implode("`,`", $fields) . "`) VALUES('" . implode("','", $values) . "')";
		mysql_query($query);
	}

	// next we'll hit findraising_donor_levels
	$fields = "`level`,`min`,`max`,`description`";
	if(mysql_error() == null)
		$q = mysql_query("SELECT $fields FROM fundraising_donor_levels WHERE fiscalyear = $oldYear");
	while(mysql_error() == null && $r = mysql_fetch_assoc($q)){
		$r['fiscalyear'] = $newYear;
		$fields = array_keys($r);
		$values = array_values($r);
		foreach($values as $idx => $val){
			$values[$idx] = mysql_real_escape_string($val);
		}
		$query = "INSERT INTO fundraising_donor_levels (`" . implode("`,`", $fields) . "`) VALUES('" . implode("','", $values) . "')";
		mysql_query($query);
	}

	// and now we'll do findraising_goals
	$fields = "`goal`,`name`,`description`,`system`,`budget`,`deadline`";
	if(mysql_error() == null){
		$q = mysql_query("SELECT $fields FROM fundraising_goals WHERE fiscalyear = $oldYear");
	}
	while(mysql_error() == null && $r = mysql_fetch_assoc($q)){
		$dateval = $r['deadline'];
		$parts = explode('-', $dateval);
		if($parts[0] != '0000')
			$parts[0] += $yearDiff;
		$r['deadline'] = implode('-', $parts);

		$r['fiscalyear'] = $newYear;

		$fields = array_keys($r);
		$values = array_values($r);
		foreach($values as $idx => $val){
			$values[$idx] = mysql_real_escape_string($val);
		}
		$query = "INSERT INTO fundraising_goals (`" . implode("`,`", $fields) . "`) VALUES('" . implode("','", $values) . "')";
		mysql_query($query);
	}

	// finally, let's update the fiscal year itself:
	if(mysql_error() == null){
		mysql_query("UPDATE config SET val='$newYear' WHERE var='FISCALYEAR'");
	}

	if(mysql_error() == null){
		$config['FISCALYEAR'] = $newYear;
		echo happy(i18n("Fiscal year has been rolled over from %1 to %2", array($oldYear, $newYear)));
	}else{
		echo error(mysql_error());
	}
	
}
