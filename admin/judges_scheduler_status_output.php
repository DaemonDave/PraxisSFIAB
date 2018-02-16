<?
include "../data/config.inc.php";
mysql_connect($DBHOST,$DBUSER,$DBPASS);
mysql_select_db($DBNAME);
$q=mysql_query("SELECT val FROM config WHERE year='0' AND var='judge_scheduler_percent'");
$r=mysql_fetch_object($q);
$percent=$r->val;

$q=mysql_query("SELECT val FROM config WHERE year='0' AND var='judge_scheduler_activity'");
$r=mysql_fetch_object($q);
$status=$r->val;

echo "$percent:$status\n";
?>
