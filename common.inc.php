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
/// \note  DRE 2018 modified

//if we dont set the charset any page that doesnt call send_header() (where it used to be set) would defualt to the server's encoding,
//which in many cases (like ysf-fsj.ca/sfiab) is UTF-8.  This was causing a lot of the newly AJAX'd editors to fail on french characters,
//becuase they were being encoded improperly.  Ideally, all the databases will be switched to UTF-8, but thats not a near-term possibility,
//so this is kind of a band-aid solution until we can make everything UTF8.  Hope it doesnt break anything anywhere else!
header("Content-Type: text/html; charset=iso-8859-1"); 

//set error reporting to not show notices, for some reason some people's installation dont set this by default
//so we will set it in the code instead just to make sure
error_reporting(E_ALL ^ E_NOTICE); 

define('REQUIREDFIELD','<span class="requiredfield">*</span>');

//figure out the directory to prepend to directoroy names, depending on if we are in a subdirectory or not
if(substr(getcwd(),-6)=="/admin")
	$prependdir="../";
else if(substr(getcwd(),-7)=="/config")
	$prependdir="../";
else if(substr(getcwd(),-3)=="/db")
	$prependdir="../";
else if(substr(getcwd(),-8)=="/scripts")
	$prependdir="../";
else
	$prependdir="";

$sfiabversion=@file($prependdir."version.txt");
$config['version']=trim($sfiabversion[0]);


//make sure the data subdirectory is writable, if its not, then we're screwed, so make sure it is!
if(!is_writable($prependdir."data"))
{
	echo "<html><head><title>SFIAB ERROR</title></head><body>";
	echo "<h1>Science Fair In A Box - ERROR</h1>";
	echo "data/ subdirectory is not writable by the web server";
	echo "<br>";
	echo "<h2>Details</h2>";
	echo "The data/ subdirectory is used to store files uploaded through the SFIAB software.  The web server must have write access to this directory in order to function properly.  Please contact your system administrator (if you are the system administrator, chown/chmod the data directory appropriately).";
	echo "<br>";
	echo "</body></html>";
	exit;
}

if(file_exists($prependdir."data/config.inc.php"))
{
	require_once($prependdir."data/config.inc.php");
}
else
{
	echo "<html><head><title>SFIAB</title></head><body>";
	echo "<h1>Science Fair In A Box - Installation</h1>";
	echo "It looks like this is a new installation of SFIAB, and the database has not yet been configured.  Please choose from the following options: <br />";
	echo "<br />";
	echo "<a href=\"install.php\">Proceed with Fresh SFIAB Installation</a>";
	echo "<br />";
	echo "</body></html>";
	exit;
}

/*
difference between MySQL <5.1 and 5.1:
in <5.1 in must have internall truncated it at 16 before comparing with the hard-coded 16 character database limit
in 5.1 it doesnt truncate and compares the full string with the hardcoded 16 character limit, so all our very long usernames
are now failing
James - Dec 30 2010
*/
$DBUSER=substr($DBUSER,0,16);

if(!mysql_connect($DBHOST,$DBUSER,$DBPASS))
{
	echo "<html><head><title>SFIAB ERROR</title></head><body>";
	echo "<h1>Science Fair In A Box - ERROR</h1>";
	echo "Cannot connect to database!";
	echo "</body></html>";
	exit;
}
	
if(!mysql_select_db($DBNAME))
{
	echo "<html><head><title>SFIAB ERROR</title></head><body>";
	echo "<h1>Science Fair In A Box - ERROR</h1>";
	echo "Cannot select database!";
	echo "</body></html>";
	exit;
}

//this will silently fail on mysql 4.x, but is needed on mysql5.x to ensure we're only using iso-8859-1 (/latin1) encodings
@mysql_query("SET NAMES latin1");

//find out the fair year and any other 'year=0' configuration parameters (things that dont change as the years go on)
$q=@mysql_query("SELECT * FROM config WHERE year='0'");

//we might get an error if installation step 2 is not done (ie, the config table doesnt even exist)
if(mysql_error())
{
	echo "<html><head><title>SFIAB ERROR</title></head><body>";
	echo "<h1>Science Fair In A Box - ERROR</h1>";
	echo "SFIAB installation is not complete.  Please go to <A href=\"install2.php\">Installer Step 2</a> to complete the installation process";
	echo "<br>";
	echo "</body></html>";
	exit;
}
//if we have 0 (<1) then install2 is not done, which would get caught above, 
//if we have 1 (<2) then insatll3 is not done (no entries for FAIRYEAR and SFIABDIRECTORY)
if(mysql_num_rows($q)<2)
{
	echo "<html><head><title>SFIAB ERROR</title></head><body>";
	echo "<h1>Science Fair In A Box - ERROR</h1>";
	echo "SFIAB installation is not complete.  Please go to <A href=\"install3.php\">Installer Step 3</a> to complete the installation process";
	echo "<br>";
	echo "</body></html>";
	exit;

}
else
{
	while($r=mysql_fetch_object($q))
	{
		$config[$r->var]=$r->val;
	}
}

$dbdbversion=$config['DBVERSION'];
$dbcodeversion=@file($prependdir."db/db.code.version.txt");
$dbcodeversion=trim($dbcodeversion[0]);

if(!$dbdbversion)
{
	echo "<html><head><title>SFIAB ERROR</title></head><body>";
	echo "<h1>Science Fair In A Box - ERROR</h1>";
	echo "SFIAB installation is not complete.  Please go to <A href=\"install2.php\">Installer Step 2</a> to complete the installation process";
	echo "<br>";
	echo "</body></html>";
	exit;
}

if($dbcodeversion!=$dbdbversion)
{
	echo "<html><head><title>SFIAB ERROR</title></head><body>";
	echo "<h1>Science Fair In A Box - ERROR</h1>";
	echo "SFIAB database and code are mismatched";
	echo "<br>";
	echo "Please run the db_update.php script in order to update";
	echo "<br>";
	echo "your database to the same version as the code";
	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<h2>Details</h2>";
	echo "Current SFIAB codebase requires DB version: ".$dbcodeversion;
	echo "<br>";
	echo "Current SFIAB database is detected as version: ".$dbdbversion;
	echo "<br>";
	echo "</body></html>";
	exit;
}

/* Check that magic_quotes is OFF */
if(get_magic_quotes_gpc()) {
?>
	<html><head><title>SFIAB ERROR</title></head><body>
	<h1>Science Fair In A Box - ERROR</h1>
	<p>Your PHP configuration has magic_quotes ENABLED.  They should be
	disabled, and are disabled in the .htaccess file, so your server is
	ignoring the .htaccess file or overriding it.
	<p>Magic quotes is DEPRECATED as of PHP 5.3.0, REMOVE as of 6.0, but ON
	by default for any PHP &lt; 5.3.0.  
	<p>It's a pain in the butt because PHP runs urldecode() on all inputs 
	from GET and POST, but if it sees the string has quotes, then it escapes
	existing quotes before passing it to us.  This is a problem for json_decode
	where we do not want this behaviour, and thus need to pass through stripslashes()
	first, but only if magicquotes is ON.  If it's off, stripslashes will
	break json_decode.
	<p>Add <pre>php_flag magic_quotes_gpc off</pre> to the .htacces, or add
	<pre>php_flag magic_quotes_gpc=off</pre> to php.ini

	<br></body></html>
<?
	exit;
}

//now pull the rest of the configuration
$q=mysql_query("SELECT * FROM config WHERE year='".$config['FAIRYEAR']."'");
while($r=mysql_fetch_object($q))
{
	$config[$r->var]=$r->val;
}

//now pull the dates
$q=mysql_query("SELECT * FROM dates WHERE year='".$config['FAIRYEAR']."'");
while($r=mysql_fetch_object($q))
{
	$config['dates'][$r->name]=$r->date;
}

//and now pull the theme
require_once("theme/{$config['theme']}/theme.php");
require_once("theme/{$config['theme_icons']}/icons.php");

require_once("committee.inc.php");

if($config['SFIABDIRECTORY'] == '') {
	session_name("SFIABSESSID");
	session_set_cookie_params(0,'/');
} else {
	session_name("SFIABSESSID".ereg_replace("[^A-Za-z]","_",$config['SFIABDIRECTORY']));
	session_set_cookie_params(0,$config['SFIABDIRECTORY']);
}
session_start();

//detect the browser first, so we know what icons to use - we store this in the config array as well
//even though its not configurable by the fair
if(stristr($_SERVER['HTTP_USER_AGENT'],"MSIE"))
	$config['icon_extension']="gif";
else
	$config['icon_extension']="png";



//now get the languages, and make sure we have at least one active language
$q=mysql_query("SELECT * FROM languages WHERE active='Y' ORDER BY langname");
if(mysql_num_rows($q)==0)
{
	echo "No active languages defined, defaulting to English";
	$config['languages']['en']="English";
}
else
{
	while($r=mysql_fetch_object($q))
	{
		$config['languages'][$r->lang]=$r->langname;
	}
}
//now if no language has been set yet, lets set it to the default language
if(!$_SESSION['lang'])
{
	//first try the default language, if that doesnt work, use "en"
	if($config['default_language'])
		$_SESSION['lang']=$config['default_language'];
	else
		$_SESSION['lang']="en";
}

//only allow debug to get set if we're using a development version (odd numbered ending)
if(substr($config['version'], -1) % 2 != 0)
	if($_GET['debug']) $_SESSION['debug']=$_GET['debug'];

//if the user has switched languages, go ahead and switch the session variable
if($_GET['switchlanguage']) 
{
	//first, make sure its a valid language:
	if($config['languages'][$_GET['switchlanguage']])
	{
		$_SESSION['lang']=$_GET['switchlanguage'];

	}
	else
	{
		//invalid language, dont do anything
	}
}

function i18n($str,$args=array(),$argsdesc=array(),$forcelang="")
{
	if(!$str)
		return "";

	if($forcelang)
	{
		$savelang=$_SESSION['lang'];
		$_SESSION['lang']=$forcelang;
	}

	if($_SESSION['lang'])
	{
		if($_SESSION['lang']=="en")
		{
			for($x=1;$x<=count($args);$x++)
			{
				$str=str_replace("%$x",$args[$x-1],$str);
			}
			if($forcelang) $_SESSION['lang']=$savelang;
			return $str;
		}
		else
		{
			$q=mysql_query("SELECT * FROM translations WHERE lang='".$_SESSION['lang']."' AND strmd5='".md5($str)."'");
			if($r=@mysql_fetch_object($q))
			{
				if($r->val)
				{
					$ret=$r->val;

					for($x=1;$x<=count($args);$x++)
					{
						$ret=str_replace("%$x",$args[$x-1],$ret);
					}
					if($forcelang) $_SESSION['lang']=$savelang;
					return $ret;
				}
				else
				{
					for($x=1;$x<=count($args);$x++)
					{
						$str=str_replace("%$x",$args[$x-1],$str);
					}
					if($forcelang) $_SESSION['lang']=$savelang;
					return "{{".$str."}}";
				}
					
			}
			else
			{
				if(count($argsdesc))
				{
					$argsdescstring="";
					$n=1;
					foreach($argsdesc AS $ad)
					{
						$argsdescstring.="%$n=$ad, ";
						$n++;
					}
					$argsdescstring=substr($argsdescstring,0,-2);
					$argsdescstring="'".mysql_escape_string($argsdescstring)."'";
				}
				else
					$argsdescstring="null";

				mysql_query("INSERT INTO translations (lang,strmd5,str,argsdesc) VALUES ('".$_SESSION['lang']."','".md5($str)."','".mysql_escape_string($str)."',$argsdescstring)");
				for($x=1;$x<=count($args);$x++)
				{
					$str=str_replace("%$x",$args[$x-1],$str);
				}
				if($forcelang) $_SESSION['lang']=$savelang;
				return "{{".$str."}}";
			}
		}
	}
	else
	{
		//no language set, assume english
		if($forcelang) $_SESSION['lang']=$savelang;
		return $str;
	}
}

function error($str,$type="normal")
{
	if($type=="normal")
		return "<div class=\"error\">$str</div><br />";
	else if($type=="inline")
		return "<span class=\"error\">$str</span><br />";

}

function notice($str,$type="normal")
{
	if($type=="normal")
		return "<div class=\"notice\">$str</div><br />";
	else if($type=="inline")
		return "<span class=\"notice\">$str</span><br />";
}

function happy($str,$type="normal")
{
	if($type=="normal")
		return "<div class=\"happy\">$str</div><br />";
	else if($type=="inline")
		return "<span class=\"happy\">$str</span><br />";
}

function display_messages()
{
	/* Dump any messages in the queue */
	if(is_array($_SESSION['messages'])) {
		foreach($_SESSION['messages'] as $m) echo $m;
	}
	$_SESSION['messages'] = array();
}

$HEADER_SENT=false;
function send_header($title="", $nav=null, $icon=null, $titletranslated=false)
{
	global $HEADER_SENT;
	global $config;
	global $prependdir;

	//do this so we can use send_header() a little more loosly and not worry about it being sent more than once.
	if($HEADER_SENT) return;
	else $HEADER_SENT=true;
	
	echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head><title><? if($title && !$titletranslated) echo i18n($title); else if($title) echo $title; else echo i18n($config['fairname']); ?></title>
<link rel="stylesheet" href="<?=$config['SFIABDIRECTORY']?>/theme/<?=$config['theme']?>/jquery-ui-1.7.2.custom.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?=$config['SFIABDIRECTORY']?>/theme/<?=$config['theme']?>/sfiab.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?=$config['SFIABDIRECTORY']?>/tableeditor.css" type="text/css" media="all" />
</head>
<body>
<script type="text/javascript" src="<?=$config['SFIABDIRECTORY']?>/js/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="<?=$config['SFIABDIRECTORY']?>/js/jqueryui/1.7.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?=$config['SFIABDIRECTORY']?>/js/sfiab.js"></script>
<script type="text/javascript" src="<?=$config['SFIABDIRECTORY']?>/js/tablesorter/jquery.tablesorter.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
        $('.tableview').tablesorter();
	});
</script>
<?
//if we're under /admin or /config we also want the translation editor
if(substr(getcwd(),-6)=="/admin" || substr(getcwd(),-7)=="/config")
	require_once("../translationseditor.inc.php");
?>

<div id="notice_area" class="notice_area"></div>
<div id="header">
<?
if(file_exists($prependdir."data/logo-100.gif"))
	echo "<img align=\"left\" height=\"50\" src=\"".$config['SFIABDIRECTORY']."/data/logo-100.gif\">";

echo "<h1>".i18n($config['fairname'])."</h1>";
echo "<div align=\"right\" style=\"font-size: 0.75em;\">";
if(isset($_SESSION['users_type'])) {
	$types = array('volunteer' => 'Volunteer', 'judge' => 'Judge', 
		'student'=>'Participant','committee'=>'Committee Member',
		'fair'=>'Science Fair');
	if($_SESSION['users_type'] != false) {
		echo i18n($types[$_SESSION['users_type']]);
	}
	echo " {$_SESSION['email']}: ";
	if($_SESSION['multirole'] == true) {
		echo "<a href=\"{$config['SFIABDIRECTORY']}/user_multirole.php\">[".i18n('Switch Roles')."]</a> ";
	}
	echo "<a href=\"{$config['SFIABDIRECTORY']}/user_login.php?action=logout\">[".i18n("Logout")."]</a>";
	
} else if(isset($_SESSION['email'])) {
	/* Backwards compatible login settings */
	if(isset($_SESSION['registration_id'])) {
		echo i18n('Participant');
		echo " {$_SESSION['email']}: ";
		echo "<a href=\"{$config['SFIABDIRECTORY']}/register_participants.php?action=logout\">[".i18n("Logout")."]</a>";
	} else {
		echo "&nbsp;";
	}

} else {
	echo i18n('Not Logged In');
}
echo "</div>";
?>
<hr />
</div>
<table cellpadding="5" width="100%">
<tr><td width="175">
<?
	//if the date is greater than the date/time that the confirmed participants gets posted,
	//then we will show the registration confirmation page as a link in the menu,
	$registrationconfirmationlink="";

	//only display it if a date is set to begin with.
	if($config['dates']['postparticipants'] && $config['dates']['postparticipants']!="0000-00-00 00:00:00")
	{
		$q=mysql_query("SELECT (NOW()>'".$config['dates']['regclose']."') AS test");
		$r=mysql_fetch_object($q);
		if($r->test==1)
		{
			$registrationconfirmationlink="<li><a href=\"".$config['SFIABDIRECTORY']."/confirmed_participants.php\">".i18n("Confirmed Participants")."</a></li>";
		}
	}
?>

<div id="left">
<?
if(is_array($nav)) {
		$navkeys=array_keys($nav);
		switch($navkeys[2]) {
			case "Fundraising":
					echo "<ul class=\"mainnav\">\n";
					echo "<li><h4 style=\"text-align: center;\">".i18n("Fundraising")."</h4></li>\n";
					echo "<li><a href=\"{$config['SFIABDIRECTORY']}/admin/fundraising.php\">".i18n("Fundraising Dashboard").'</a></li>';
					echo "<li><a href=\"{$config['SFIABDIRECTORY']}/admin/fundraising_setup.php\">".i18n("Fundraising Setup").'</a></li>';
					echo "<li><a href=\"{$config['SFIABDIRECTORY']}/admin/fundraising_campaigns.php\">".i18n("Manage Appeals").'</a></li>';
					echo "<li><a href=\"{$config['SFIABDIRECTORY']}/admin/donors.php\">".i18n("Manage Donors/Sponsors").'</a></li>';
					echo "<li><a href=\"{$config['SFIABDIRECTORY']}/admin/fundraising_reports.php\">".i18n("Fundraising Reports").'</a></li>';
					echo "</ul><br />\n";
			break;
			default:
				//no special menu
			break;
		}
}
?>
<ul class="mainnav">
<?
 echo "<li><a href=\"{$config['SFIABDIRECTORY']}/index.php\">".i18n("Home Page").'</a></li>';
 echo "<li><a href=\"{$config['SFIABDIRECTORY']}/important_dates.php\">".i18n("Important Dates").'</a></li>';
 echo $registrationconfirmationlink;
 /*
 echo "<li><a href=\"{$config['SFIABDIRECTORY']}/register_participants.php\">".i18n("Participant Registration").'</a></li>';
 echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_login.php?type=judge\">".i18n("Judges Registration").'</a></li>';
 if($config['volunteer_enable'] == 'yes') {
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_login.php?type=volunteer\">".i18n("Volunteer Registration").'</a></li>';
 }
 */
 echo "<li><a href=\"{$config['SFIABDIRECTORY']}/committees.php\">".i18n("Committee").'</a></li>';
 echo "<li><a href=\"{$config['SFIABDIRECTORY']}/winners.php\">".i18n("Winners").'</a></li>';
 echo '</ul>';
?>
<br />
<ul class="mainnav">
<?
if($_SESSION['users_type'] == 'committee') {
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_personal.php\">".i18n("My Profile").'</a></li>';
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/committee_main.php\">".i18n("Committee Home").'</a></li>';
	if(committee_auth_has_access("admin")){ 
		echo "<li><a href=\"{$config['SFIABDIRECTORY']}/admin/\">".i18n("Fair Administration").'</a></li>';
	} 
	if(committee_auth_has_access("config")){
		echo "<li><a href=\"{$config['SFIABDIRECTORY']}/config/\">".i18n("Configuration").'</a></li>';
	} 
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_login.php?action=logout\">".i18n("Logout").'</a></li>';
} else if($_SESSION['users_type']=="judge") {
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_personal.php\">".i18n("My Profile").'</a></li>';
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/judge_main.php\">".i18n("Judge Home").'</a></li>';
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_login.php?action=logout\">".i18n("Logout").'</a></li>';
} else if($_SESSION['users_type']=="volunteer") {
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_personal.php\">".i18n("My Profile").'</a></li>';
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/volunteer_main.php\">".i18n("Volunteer Home").'</a></li>';
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_login.php?action=logout\">".i18n("Logout").'</a></li>';
} else if($_SESSION['users_type']=="sponsor") {
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_personal.php\">".i18n("My Profile").'</a></li>';
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/sponsor_main.php\">".i18n("Sponsor Home").'</a></li>';
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/user_login.php?action=logout\">".i18n("Logout").'</a></li>';
} else if($_SESSION['schoolid'] && $_SESSION['schoolaccesscode']) {
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/schoolaccess.php\">".i18n("School Home").'</a></li>';
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/schoolaccess.php?action=logout\">".i18n("Logout").'</a></li>';
}
else if($_SESSION['registration_number'] && $_SESSION['registration_id']) {
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/register_participants_main.php\">".i18n("Participant Home").'</a></li>';
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/register_participants.php?action=logout\">".i18n("Logout")."</a></li>\n";
} else {
	echo "<li><a href=\"{$config['SFIABDIRECTORY']}/login.php\">".i18n("Login/Register").'</a></li>';
}
?></ul>
<div class="aligncenter">
<?
if(count($config['languages'])>1) {
	echo "<br />";
	echo "<form name=\"languageselect\" method=\"get\" action=\"".$_SERVER['PHP_SELF']."\">";
	echo "<select name=\"switchlanguage\" onchange=\"document.forms.languageselect.submit()\">\n";
	foreach($config['languages'] AS $key=>$val) {
		if($_SESSION['lang']==$key) $selected="selected=\"selected\""; else $selected="";

		echo "<option $selected value=\"$key\">$val</option>";
	}
	echo "</select>";
	echo "</form>";
}

?>
</div>
<?
echo "<br /><ul class=\"mainnav\">\n";
echo "<li><a href=\"{$config["SFIABDIRECTORY"]}/contact.php\">".i18n("Contact Us")."</a></li>\n";
echo "</ul>";
?>
</div>
<?
echo "<br /><ul class=\"mainnav\">\n";
echo "<li><a href=\"http://seab-sciencefair.com/mediawiki/index.php\">".i18n("Praxis Open Science Fair Wiki")."</a></li>\n";
echo "</ul>";
echo "<br /><ul class=\"mainnav\">\n";
echo "<li><a href=\"http://praxismedhat.com/\">".i18n("Praxis Society ")."</a></li>\n";
echo "</ul>";
?>
</td><td>
<?

if(is_array($nav)) {
	echo "<div id=\"mainwhere\">".i18n('You are here:').' ';
	foreach($nav as $t=>$l) {
		echo "<a href=\"{$config['SFIABDIRECTORY']}/$l\">".i18n($t).'</a> &raquo; ';
	}
	if(!$titletranslated)
		echo i18n($title);
	else
		echo $title;
	echo '</div>';
}
?>

<div id="main">
<?

if(committee_auth_has_access("config") || committee_auth_has_access("admin"))
	committee_warnings();
if(committee_auth_has_access("config"))
	config_warnings();
if(committee_auth_has_access("admin"))
	admin_warnings();

echo "<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\"><tr>";

if($icon && theme_icon($icon)) {
        echo "<td width=\"40\">";
        echo theme_icon($icon);
        echo "</td><td>";
}
else
    echo "<td>";

if($title && !$titletranslated)
	echo "<h2>".i18n($title)."</h2>";
else if($title)
	echo "<h2>".$title."</h2>";

//if we're under /admin or /config then we want to show the ? help icon
if(substr(getcwd(),-6)=="/admin" || substr(getcwd(),-7)=="/config")
{
	if($_SERVER['REDIRECT_SCRIPT_URL'])
		$fname=substr($_SERVER['REDIRECT_SCRIPT_URL'],strlen($config['SFIABDIRECTORY'])+1);
	else
		$fname=substr($_SERVER['PHP_SELF'],strlen($config['SFIABDIRECTORY'])+1);	
	echo "</td><td align=\"right\"><a target=\"_sfiabhelp\" href=\"http://www.sfiab.ca/wiki/index.php/Help_$fname\"><img border=\"0\" src=\"".$config['SFIABDIRECTORY']."/images/32/help.".$config['icon_extension']."\"></a>";
}
"</td></tr>";
echo "</table>";

	display_messages();
}
/* END OF send_header */

function send_footer()
{
global $config;
?>
</td></tr></table>
</div>
<div id="footer">
<? 
//we only show the debug session variables if we have an ODD numbered version.
if(substr($config['version'], -1) % 2 != 0)
{
	$revision=exec("svn info |grep Revision");
	$extra=" (Development $revision)";
	if($_SESSION['debug']=="true")
		$extra.=" DEBUG: ".print_r($_SESSION,true);
}
echo "<a target=\"blank\" href=\"http://www.sfiab.ca\">SFIAB Version ".$config['version']."{$extra}</a>"; 
?>
</div>
<div id="debug" style="display:<?=($_SESSION['debug']=='true')?'block':'none'?>; font-family:monospace; white-space:pre; " >Debug...</div>
<iframe id="content" src="" style="visibility:hidden; width:0px; height:0px"></iframe>

</body>
</html>

<?
}

function send_popup_header($title="")
{
	global $HEADER_SENT;
	global $config;

	//do this so we can use send_header() a little more loosly and not worry about it being sent more than once.
	if($HEADER_SENT) return;
	else $HEADER_SENT=true;
	
	echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head><title><?=i18n($title)?></title>
<link rel="stylesheet" href="<?=$config['SFIABDIRECTORY']?>/theme/<?=$config['theme']?>/jquery-ui-1.7.2.custom.css" type="text/css" media="all" />
<link rel="stylesheet" href="<?=$config['SFIABDIRECTORY']?>/theme/<?=$config['theme']?>/sfiab.css" type="text/css" media="all" />
<link media=all href="<?=$config['SFIABDIRECTORY']?>/tableeditor.css" type=text/css rel=stylesheet>
</head>
<body onload="window.focus()">
<script type="text/javascript" src="<?=$config['SFIABDIRECTORY']?>/js/jquery/1.3.2/jquery.min.js"></script>
<script type="text/javascript" src="<?=$config['SFIABDIRECTORY']?>/js/jqueryui/1.7.2/jquery-ui.min.js"></script> 
<script type="text/javascript" src="<?=$config['SFIABDIRECTORY']?>/js/sfiab.js"></script>
<div id="notice_area" class="notice_area"></div>

<?
if($title)
	echo "<h2>".i18n($title)."</h2>";

}

function send_popup_footer()
{
?>
<br />
<br />
<div id="footer">
<?
global $config;
$lastdigit=$config['version'][strlen($config['version'])-1];
if($lastdigit%2!=0)
{
	echo "DEBUG:";
	print_r($_SESSION); 
}
echo "SFIAB Version ".$config['version']; 
?>
</div>
<div id="debug" style="display:<?=($_SESSION['debug']=='true')?'block':'none'?>">Debug...</div>
<iframe id="content" src="" style="visibility:hidden; width:0px; height:0px"></iframe>

</body>
</html>
<?
}


function emit_month_selector($name,$selected="")
{
	echo "<select name=\"$name\">\n";
	$months=array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	echo "<option value=\"\">".i18n("Month")."</option>\n";
	for($x=1;$x<=12;$x++)
	{
		if($x==$selected)
			$s="selected=\"selected\"";
		else
			$s="";
		echo "<option $s value=\"$x\">".$months[$x]."</option>\n";
	}

	echo "</select>\n";

}


function emit_day_selector($name,$selected="")
{
	echo "<select name=\"$name\">\n";
	echo "<option value=\"\">".i18n("Day")."</option>\n";

	for($x=1;$x<=31;$x++)
		echo "<option value=\"".($x<10?"0":"")."$x\" ".($selected==$x?"selected=\"selected\"":"").">$x</option>\n";

	echo "</select>\n";

}

function emit_year_selector($name,$selected="",$min=0,$max=0)
{
	$curyear=date("Y");
	echo "<select name=\"$name\">\n";
	echo "<option value=\"\">".i18n("Year")."</option>\n";

	if($min&&$max)
	{
		for($x=$min;$x<=$max;$x++)
			echo "<option value=\"$x\" ".($selected==$x?"selected=\"selected\"":"").">$x</option>\n";

	}
	else 
	{
		//if we arent given a min and max, lets show current year + 5
		for($x=$curyear;$x<$curyear+5;$x++)
			echo "<option value=\"$x\" ".($selected==$x?"selected=\"selected\"":"").">$x</option>\n";
	}
	echo "</select>\n";
}

function emit_date_selector($name,$selected="")
{
	if($selected)
	{
		list($year,$month,$day)=split("-",$selected);
	}
	echo "<table cellpadding=0>";
	echo "<tr><td>";
	emit_year_selector($name."_year",$year);
	echo "</td><td>";
	emit_month_selector($name."_month",$month);
	echo "</td><td>";
	emit_day_selector($name."_day",$day);
	echo "</td></tr>";
	echo "</table>";
}

function emit_hour_selector($name,$selected="")
{
	if($selected!="") $selected=(int)$selected;
	echo "<select name=\"$name\">\n";
	echo "<option value=\"\">HH</option>\n";


	for($x=0;$x<=23;$x++)
	{
		if($x===$selected)
			$sel="selected";
		else
			$sel="";
		echo "<option value=\"$x\" $sel>".sprintf("%02d",$x)."</option>\n";
	}

	echo "</select>\n";


}
function emit_minute_selector($name,$selected="")
{
	$mins=array("00","05","10","15","20","25","30","35","40","45","50","55");
	echo "<select name=\"$name\">\n";
	echo "<option value=\"\">MM</option>\n";

	for($x=0;$x<count($mins);$x++)
		echo "<option value=\"".$mins[$x]."\" ".($selected==$mins[$x]?"selected":"").">$mins[$x]</option>\n";

	echo "</select>\n";


}

function emit_time_selector($name,$selected="")
{

	if($selected)
	{
		list($hour,$minute,$second)=split(":",$selected);
	}
	echo "<table cellpadding=0>";
	echo "<tr><td>";
	emit_hour_selector($name."_hour",$hour);
	echo "</td><td>";
	emit_minute_selector($name."_minute",$minute);
	echo "</td></tr>";
	echo "</table>";

}

function emit_province_selector($name,$selected="",$extra="")
{
	global $config;
	$q=mysql_query("SELECT * FROM provinces WHERE countries_code='".mysql_escape_string($config['country'])."' ORDER BY province");
	if(mysql_num_rows($q)==1)
	{
		$r=mysql_fetch_object($q);
		echo "<input type=\"hidden\" name=\"$name\" value=\"$r-code\">";
		echo i18n($r->province);
	}
	else
	{
		echo "<select name=\"$name\" $extra>\n";
		echo "<option value=\"\">".i18n("Select a {$config['provincestate']}")."</option>\n";
		while($r=mysql_fetch_object($q))
		{
			if($r->code == $selected) $sel="selected=\"selected\""; else $sel="";

			echo "<option $sel value=\"$r->code\">".i18n($r->province);
			echo "</option>\n";
		}

		echo "</select>\n";
	}

}


function outputStatus($status)
{
	$ret="";
	switch($status)
	{
		case 'incomplete': 
			$ret.="<div class=\"incomplete\">";
			$ret.= i18n("Incomplete");
			$ret.= "</div>";
			break;
		case 'complete':
			$ret.= "<div class=\"complete\">";
			$ret.= i18n("Complete");
			$ret.= "</div>";
			break;
		case 'empty':
			$ret.="<div class=\"incomplete\">";
			$ret.= i18n("Empty");
			$ret.= "</div>";
			break;

		default:
			$ret.=i18n("Unknown");
			break;
	}
	return $ret;
}

//returns true if its a valid email address, false if its not
function isEmailAddress($str) {
	if(eregi('^[+a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$', $str))
		return true;
	else
		return false;
}

function communication_get_user_replacements(&$u) {
	global $config;
	$rep = array('FAIRNAME' => $config['fairname'],
			'NAME' => $u['name'],
			'EMAIL' => $u['email'],
			'PASSWORD' => $u['password'],
			'SALUTATION' => $u['salutation'], 
			'FIRSTNAME' => $u['firstname'],
			'LASTNAME' => $u['lastname'],
			'ORGANIZATION' => $u['sponsor']['organization'],
	);
	return $rep;
}

function communication_replace_vars($text, &$u, $otherrep=array()) {
	global $config;
	if($u) {
		$userrep=communication_get_user_replacements($u);
	}
	else { 
		$userrep=array();
	}

	$rep=array_merge($userrep,$otherrep);
	foreach($rep AS $k=>$v) {
		$text=ereg_replace("\[$k\]",$v,$text);
	}
	return $text;
}
//
/// DRE 2018 MODIFIED
// email_send is James' string evaluation and concatenation function 
// that uses a database entry to craft emails from
// $val is the name of the 'emails' table entry that is chosen to find the boilerplate text 
// $to is a fully formed email address - usually from a  MySQL table
// $sub_subject - is an array of "subject modifiers" that are variable string substitutions into the boilerplate that is 
// $sub_body - is a similar array of  "body modifiers"  that are specific entries in the database.
// This function crafts the email and then it is ready to be sent out into the wild.
function email_send($val,$to,$sub_subject=array(),$sub_body=array())
{
	global $config;

	/* Standard substitutions that are constant no matter who
	 * the $to is */
	$urlproto = $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
	$urlmain = "$urlproto{$_SERVER['HTTP_HOST']}{$config['SFIABDIRECTORY']}";
	$urllogin = "$urlmain/login.php";
	$stdsub = array("FAIRNAME"=>i18n($config['fairname']),
			"URLMAIN"=>$urlmain,
			"URLLOGIN"=>$urllogin,
		);
	/* Add standard subs to existing sub arrays */
	// Testing remove automatics, does it stop email send?
	//$sub_subject = array_merge($sub_subject, $stdsub);
	//$sub_body = array_merge($sub_body, $stdsub);


	//if our "to" doesnt look like a valid email, then forget about sending it.
	if(!isEmailAddress($to))
		return false;

  // fetches data from the database
	$q=mysql_query("SELECT * FROM emails WHERE val='$val'");
	// extracts an object from the 
	if($r=mysql_fetch_object($q)) 
	{
// DRE 2018 removed internationalization
//		$subject=i18n($r->subject);
		$subject=$r->subject;
//		$body=i18n($r->body);
		$body=$r->body;
		
		// DRE 2018 - This is where he does the [FAIRNAME] array substitution into the text
		/* Eventually we should just do this with communication_replace_vars() */
		if(count($sub_subject)) 
		{
			foreach($sub_subject AS $sub_k=>$sub_v) 
			{
				$subject=ereg_replace("\[$sub_k\]","$sub_v",$subject);
			}
		}
		if(count($sub_body)) 
		{
			foreach($sub_body AS $sub_k=>$sub_v) 
			{
				$body=ereg_replace("\[$sub_k\]","$sub_v",$body);
			}
		}

//
/// MODIFIED DRE 2018 -- forced single email out
//
	//	if($r->from)
	//		$fr=$r->from;
	//	else if ($config['fairmanageremail'])
	$fr=$config['fairmanageremail'];
	//	else
	//		$fr="";


		//only send the email if we have a from
		if($fr) 
		{
//
/// DRE 2018 MODIFIED
//
	
			//send using RMail
			//email_send_new($to,$fr,$subject,$body, NULL);
			// send using PHP mail()
			// This version doesnt like in-string implosion - using function calls
			// Not sure if server sendmail is stripping out from: email or not.
			$from_consolidated = implode(array( 'From: ', $fr  ));
			$headers = $from_consolidated;
			//$headers .= 'Reply-To: webmaster@seab-sciencefair.com \r\n';
			//$headers .=  implode(array('X-Mailer: PHP/', phpversion(), '\r\n'));			
			
//
/// ADDED by DRE 2018 
//
	// create a local file to see variable contents for debugging purposes.
	$date =  date('Y-m-d');
	$now =  time();	
	$summary = array( $date, $now, $to, $from, $subject, $body, $headers );
	$file = 'email_send-logs.out';
	$data = implode(",", $summary);
	$handle = fopen($file, "a+");
	fwrite($handle, $data."\n");
	fclose($handle);
//
///
//			
			
			
			// attempted using mail()
			mail($to,$subject,$body, $headers);
		}
		else
			echo error(i18n("CRITICAL ERROR: email '%1' does not have a 'From' and the Fair Manager Email is not configured",array($val),array("email key name")));	
	}
	else {
		echo error(i18n("CRITICAL ERROR: email '%1' not found",array($val),array("email key name")));	
	}
}

require_once("Rmail/Rmail.php");
require_once("Rmail/RFC822.php");

//this sends out an all-ready-to-go email, it does no substitution or changes or database lookups or anything
function email_send_new($to,$from,$subject,$body,$bodyhtml="") 
{
	$mail=new RMail();
	$mail->setFrom($from);
	$mail->setSubject($subject);
	$mail->setText($body);

	$r=new Mail_RFC822($from);
	$structure = $r->parseAddressList($from);
	$s=$structure[0];
	$ret=sprintf("%s@%s",$s->mailbox,$s->host);
	$mail->setReturnPath($ret);
	$mail->setHeader("Bounce-To",$ret);
//
/// ADDED by DRE 2018
//
	// create a local file to see variable contents for debugging purposes.
	$date =  date('Y-m-d');
	$now =  time();	
	$summary = array( $date, $now, $to, $from, $subject, $body );
	$file = 'email-logs.out';
	$data = implode(",", $summary);
	$handle = fopen($file, "a+");
	fwrite($handle, $data);
	fclose($handle);
//
///
//	
	//only add the html if we have it
	if($bodyhtml) 
	{
		$mail->setHTML($bodyhtml);
	}

	if(is_array($to)) 
	{
		return $mail->send($to);
	} else {
		return $mail->send(array($to));
	}
}


/*
	returns an array of arrays
	[ 0 ] = array ( to, firstname, lastname, email )
	[ 1 ] = array ( to, firstname, lastname, email )
	...etc

*/
function getEmailRecipientsForRegistration($reg_id)
{
	global $config;
	//okay first grab the registration record, to see if we should email the kids, the teacher, and/or the parents
	$q=mysql_query("SELECT * FROM registrations WHERE id='$reg_id' AND year='{$config['FAIRYEAR']}'");
	$registration=mysql_fetch_object($q);

	if($registration->emailcontact && isEmailAddress($registration->emailcontact)) {
			$ret[]=array("to"=>$registration->emailcontact,
					"firstname"=>"",
					"lastname"=>"",
					"email"=>$registration->emailcontact,
				);
	}

	$sq=mysql_query("SELECT * FROM students WHERE registrations_id='$reg_id' AND year='{$config['FAIRYEAR']}'");
	$ret=array();
	while($sr=mysql_fetch_object($sq)) {
		if($sr->email && isEmailAddress($sr->email)) {
			if($sr->firstname && $sr->lastname)
				$to=$sr->firstname." ".$sr->lastname." <".$sr->email.">";
			else if($sr->firstname)
				$to=$sr->firstname." <".$sr->email.">";
			else if($sr->lastname)
				$to=$sr->lastname." <".$sr->email.">";
			else
				$to=$sr->email;

			$ret[]=array("to"=>$to,
					"firstname"=>$sr->firstname,
					"lastname"=>$sr->lastname,
					"email"=>$sr->email,
				);
		}
	}
	return $ret;
}

function output_page_text($textname)
{
	global $config;
	$q=mysql_query("SELECT * FROM pagetext WHERE textname='$textname' AND year='".$config['FAIRYEAR']."' AND lang='".$_SESSION['lang']."'");
	if(mysql_num_rows($q))
		$r=mysql_fetch_object($q);
	else
	{
		//not defined, lets grab the default text
		$q=mysql_query("SELECT * FROM pagetext WHERE textname='$textname' AND year='-1' AND lang='".$config['default_language']."'");
		$r=mysql_fetch_object($q);
	}

	//if it looks like we have HTML content, dont do a nl2br, if there's no html, then do the nl2br
	if(strlen($r->text)==strlen(strip_tags($r->text)))
		echo nl2br($r->text);
	else
		echo $r->text;
}

function output_page_cms($filename)
{
	global $config;
	$q=mysql_query("SELECT * FROM cms WHERE filename='".mysql_escape_string($filename)."' AND lang='".$_SESSION['lang']."' ORDER BY dt DESC LIMIT 1");
	if(mysql_num_rows($q))
	{
		$r=mysql_fetch_object($q);
		send_header($r->title,null,null,true);

		if(file_exists("data/logo-200.gif") && $r->showlogo==1)
			echo "<img align=\"right\" src=\"".$config['SFIABDIRECTORY']."/data/logo-200.gif\" border=\"0\">";

		//if it looks like we have HTML content, dont do a nl2br, if there's no html, then do the nl2br
		if(strlen($r->text)==strlen(strip_tags($r->text)))
			echo nl2br($r->text);
		else
			echo $r->text;
	}
	else {
		send_header("Error: File not found");
		echo error(i18n("The file you have requested (%1), does not exist on the server.",array($filename)));
		return;
		//not defined, lets grab the default text
	}

	send_footer();
}

function generatePassword($pwlen=8)
{
	//these are good characters that are not easily confused with other characters :)
	$available="ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789";
	$len=strlen($available) - 1;

	$key="";
	for($x=0;$x<$pwlen;$x++)
		$key.=$available{rand(0,$len)};
	return $key;
}


//config specific warning
function config_warnings()
{

}

//admin specific warnings
function admin_warnings()
{

}

//warnings to show to both config and/or admin people
function committee_warnings()
{
	global $config;
	//it is vital that each year the system be rolled over before we start it again
	//we should do this, say, 4 months after the FAIRDATE, so its soon enough that they should see
	//the message as soon as they login to start preparing for hte new year, but not too late to do it
	//properly :)

	$q=mysql_query("SELECT DATE_ADD('".$config['dates']['fairdate']."', INTERVAL 4 MONTH) < NOW() AS rollovercheck");
	$r=mysql_fetch_object($q);
	if($r->rollovercheck) {
		echo error(i18n("It has been more than 4 months since your fair.  In order to prepare the system for the next year's fair, you should go to the SFIAB Configuration page, and click on 'Rollover Fair Year'.  Do not start updating the system with new information until the year has been properly rolled over."));
	}

	$warn = false;
	$q = mysql_query("SELECT * FROM award_prizes WHERE `external_identifier` IS NOT NULL 
				AND external_identifier=prize");
	if(mysql_num_rows($q) > 0) {
		/* The bug was that the external_identifier was set to the prize name.. so only display the warning
		 * if we find that case for a non-sfiab external fair */
		while(($p = mysql_fetch_assoc($q) )) {
			$qq = mysql_query("SELECT * FROM award_awards 
						LEFT JOIN fairs ON fairs.id=award_awards.award_source_fairs_id
						WHERE award_awards.id='{$p['award_awards_id']}'
						AND year='{$config['FAIRYEAR']}'
						AND award_awards.award_source_fairs_id IS NOT NULL
						AND fairs.type='ysc' ");
						echo mysql_error();
			if(mysql_num_rows($qq) > 0) {
				$warn = true;
				break;
			}
		}
	}
	if($warn) {
		//let everyone know about the need to re-download awards before being able to upload
		echo notice(i18n("March 30, 2010 - There was a minor issue with uploading award results that has now been corrected, however, you will need to re-download your awards from all external sources, before you will be able to upload the award winners back to those external sources.  Re-downloading the awards will not affect the awards in any visible way, it will just allow the winners to be uploaded properly.  Click on Fair Administration -> Awards Management -> Download awards from external sources -> and click 'check' for each award source"));
	}

}

$CWSFDivisions=array(
	1=>"Automotive",
	2=>"Biotechnology & Pharmaceutical Sciences",
	3=>"Computing & Information Technology",
	4=>"Earth & Environmental Sciences",
	5=>"Engineering",
	6=>"Environmental Innovation",
	7=>"Health Sciences",
	8=>"Life Sciences",
	9=>"Physical & Mathematical Sciences"
);

function theme_icon($icon, $width=0) {
	global $theme_icons, $config;

	$w = ($width == 0) ? '' : "width=\"$width\"" ;
	if($theme_icons['icons'][$icon])
		return "<img src=\"{$config['SFIABDIRECTORY']}/theme/{$config['theme_icons']}/{$theme_icons['icons'][$icon]}\" border=\"0\" $w alt=\"".htmlspecialchars($icon)."\">";

	return "";
}

//$d can be a unix timestamp integer, OR a text string, eg 2008-01-22
function format_date($d) {
    global $config;
    if(is_numeric($d))
        return date($config['dateformat'],$d);
    else
        return date($config['dateformat'],strtotime($d));
}

//$t can be a unix timestamp integer, or a text string, eg 10:23:48
function format_time($t) {
    global $config;
    if(is_numeric($t))
        return date($config['timeformat'],$t);
    else
        return date($config['timeformat'],strtotime($t));
}

//$dt can be a unix timestamp integer, or a text string, eg 2008-01-22 10:23:48
function format_datetime($dt) {
    if(is_numeric($dt)) {
        return format_date($dt)." ".i18n("at")." ".format_time($dt);
    }
    else {
        list($d,$t)=split(" ",$dt);
        return format_date($d)." ".i18n("at")." ".format_time($t);
    }
}

function format_money($n,$decimals=true)
{
	if($n<0){
		$neg=true;
		$n=$n*-1;
	}
	//get the part before the decimal
	$before=floor($n);
	$out="";

	//space it out in blocks of three
	for($x=strlen($before);$x>3;$x-=3) {
		$out=substr($before,$x-3,3)." ".$out;
	}
	if($x>0)
		$out=substr($before,0,$x)." ".$out;

	//trim any leading/trailing space that was added
	$out=trim($out);

	if($neg) $negdisp="-"; else $negdisp="";

    if($decimals) {
        //get everything after the decimal place, and %02f it.
        $after=substr(strstr(sprintf("%.02f",$n),"."),1);

        //finally display it with the right language localization
        if($_SESSION['lang']=="fr")
            return sprintf("%s%s,%s \$",$negdisp,$out,$after);
        else
            return sprintf("%s\$%s.%s",$negdisp,$out,$after);
    }
    else {
        if($_SESSION['lang']=="fr")
            return sprintf("%s%s \$",$negdisp,$out);
        else
            return sprintf("%s\$%s",$negdisp,$out);

    }
}

function message_push($m)
{
	if(!is_array($_SESSION['messages'])) $_SESSION['messages'] = array();
	$_SESSION['messages'][] = $m;
}

function notice_($str, $i18n_array=array(), $timeout=-1, $type='notice')
{
	if($timeout == -1) $timeout = 5000;
	echo "<script type=\"text/javascript\">
		notice_create('$type',\"".i18n($str,$i18n_array)."\",$timeout);
		</script>";
}

function happy_($str, $i18n_array=array(), $timeout=-1)
{
	notice_($str, $i18n_array, $timeout, 'happy');
}
function error_($str, $i18n_array=array(), $timeout=-1)
{
	notice_($str, $i18n_array, $timeout, 'error');
}

function debug_($str)
{
	if($_SESSION['debug'] != true) return;
	$s = str_replace("\n", "", nl2br(htmlspecialchars($str))).'<br />';
	echo "<script type=\"text/javascript\">
		$(document).ready(function() {
			$(\"#debug\").append(\"$s\");
		});
		</script>";
}

//this function returns a HTML colour code ranging between red and green, with yellow in the middle based on the percent passed into it
function colour_to_percent($percent)
{
	//0 is red
	//50 is yellow
	//100 is green

	if($percent<=50) $red=255;
	else $red=(100-$percent)*2/100*255;;

	if($percent>50) $green=255;
	else $green=($percent)*2/100*255;;

//    echo "red=$red";
//    echo "green=$green";
	$str="#".sprintf("%02s",dechex($red)).sprintf("%02s",dechex($green))."00";
	return $str;
}


function format_duration($seconds, $granularity = 2)
{
	$units = array(
	'1 year|:count years' => 31536000,
	'1 week|:count weeks' => 604800,
	'1 day|:count days' => 86400,
	'1 hour|:count hours' => 3600,
	'1 min|:count min' => 60,
	'1 sec|:count sec' => 1);
	$output = '';
	// $output.=time()." - ".$timestamp." = ".$seconds;
	foreach ($units as $key => $value) {
		$key = explode('|', $key);
		if ($seconds >= $value) {
			$count = floor($seconds / $value);
			$output .= ($output ? ' ' : '');
			$output .= ($count == 1) ? $key[0] : str_replace(':count', $count, $key[1]);
			$seconds %= $value;
			$granularity--;
		}
		if ($granularity == 0) {
			break;
		}
	}
	return $output ? $output : '0 sec';
}

function getTextFromHtml($html) {
	//first, replace an </p> with </p><br />
	$text=str_replace("</p>","</p><br />",$html);
	//next, replace a </div> with </div><br />
	$text=str_replace("</div>","</div><br />",$html);
	//now replace any <br /> with newlines
	$text=eregi_replace('<br[[:space:]]*/?[[:space:]]*>',chr(13).chr(10),$text);
	//and strip the rest of the tags
	$text=strip_tags($text);

	//a few common html entities
	//replace &amp; with & first, so multiply-encoded entities will decode (like "&amp;#160;")
	$text=str_replace("&amp;","&",$text);
	$text=str_replace("&nbsp;"," ",$text);
	$text=str_replace("&#160;"," ",$text);
	$text=str_replace("&lt;","<",$text);
	$text=str_replace("&gt;",">",$text);

	//text version should always wrap at 75 chars, some mail severs wont accept
	//mail with very long lines
	$text=wordwrap($text,75,"\n",true);

	return $text;
}

function getUserForSponsor($sponsor_id) {
	// loop through each contact and draw a form with their data in it.
	$q = mysql_query("SELECT *,MAX(year) FROM users LEFT JOIN users_sponsor ON users_sponsor.users_id=users.id
	WHERE 
	sponsors_id='" . $sponsor_id . "' 
    AND types LIKE '%sponsor%'
    GROUP BY uid
	HAVING deleted='no' 
	ORDER BY users_sponsor.primary DESC,lastname,firstname
	LIMIT 1
    ");
	$r=mysql_fetch_object($q);
	return user_load_by_uid($r->uid);
}

function projectdivisions_load($year = false)
{
	global $config;
	if($year == false) $year = $config['FAIRYEAR'];
	$divs = array();
	$q = mysql_query("SELECT * FROM projectdivisions WHERE year='$year'");
	while(($d = mysql_fetch_assoc($q))) $divs[$d['id']] = $d;
	return $divs;
}
function projectcategories_load($year = false)
{
	global $config;
	if($year == false) $year = $config['FAIRYEAR'];
	$cats = array();
	$q = mysql_query("SELECT * FROM projectcategories WHERE year='$year'");
	while(($c = mysql_fetch_assoc($q))) $cats[$c['id']] = $d;
	return $cats;
}


?>
