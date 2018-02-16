<?

//make sure logs folder exists, and htaccess it to deny access
if(!file_exists("../data/logs"))
	@mkdir("../data/logs");

 if(!file_exists("../data/logs/.htaccess"))
 	@file_put_contents("../data/logs/.htaccess","Order Deny,Allow\r\nDeny From All\r\n");

//add PHP_SELF just so when we do a process listing on the server we know which fair its running for
//the argument does not get used by the script at all
exec("nice php judges_sa.php {$_SERVER['PHP_SELF']} >../data/logs/judge_scheduler_".date("YmdHis").".log 2>&1 &");
usleep(1500000); // 1.5 second to allow the judges_sa to update the % status to 0% otherwise the status page will think its not running if it gets there too soon
header("Location: judges_scheduler_status.php");
exit;
?>
