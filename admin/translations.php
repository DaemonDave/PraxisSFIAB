<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2005 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2008 James Grant <james@lightbox.org>

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

 send_header("Translations",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "translations_management"
			);

//by default, we will edit the french translations
if($_GET['translang']) $_SESSION['translang']=$_GET['translang'];

if(!$_SESSION['translang'])
	$_SESSION['translang']="fr";


if($_GET['show']) $show=$_GET['show'];
else if($_POST['show']) $show=$_POST['show'];
if(!$show) $show="missing";


if($_POST['action']=="save") {
    //first, delete anything thats supposed to eb deleted
    if(count($_POST['delete'])) {
        foreach($_POST['delete'] AS $del) {
            mysql_query("DELETE FROM translations WHERE lang='".mysql_real_escape_string($_SESSION['translang'])."' AND strmd5='".mysql_real_escape_string($del)."'"); 
        }
        echo happy(i18n("Translation(s) deleted"));
    }
    if($_POST['changedFields']) {
        $changed=split(",",$_POST['changedFields']);
        foreach($changed AS $ch) {
            mysql_query("UPDATE translations SET val='".mysql_escape_string(stripslashes($_POST['val'][$ch]))."' WHERE strmd5='".mysql_real_escape_string($ch)."' AND lang='".mysql_real_escape_string($_SESSION['translang'])."'");
        }
        echo happy(i18n("Translation(s) saved"));
    }
}

echo "<table>";
echo "<tr><td>";
echo i18n("Choose a language to manage translations for");
echo "</td><td>";
echo "<form name=\"langswitch\" method=\"get\" action=\"translations.php\">";
echo "<select name=\"translang\" onchange=\"document.forms.langswitch.submit()\">";
$q=mysql_query("SELECT * FROM languages WHERE lang!='en'");
while($r=mysql_fetch_object($q))
{
	if($_SESSION['translang']==$r->lang){ $sel="selected=\"selected\""; $translangname=$r->langname;} else $sel="";
	echo "<option $sel value=\"$r->lang\">$r->langname</option>";
}
echo "</select>";
echo "</form>";
echo "</td></tr>";
echo "</table>";

if($show=="missing") {
	echo i18n("Show missing translations");
	echo "&nbsp; | &nbsp;";
	echo "<a href=\"translations.php?show=all\">".i18n("Show all translations")."</a>";
}
else {
	echo "<a href=\"translations.php?show=missing\">".i18n("Show missing translations")."</a>";
	echo "&nbsp; | &nbsp;";
	echo i18n("Show all translations");
}

echo "<br />";
echo "<br />";
echo i18n("Instructions: Enter the translation below the string and click Save.  Only one translation can be saved at a time.  The terms %1, %2, etc get substituded with various arguments to the string, so they must appear in the translation if they are in the original string.");
echo "<br />";
echo "<br />";

if($show=="missing") $showquery="AND ( val is null OR val='' )";
else $showquery="";

$q=mysql_query("SELECT * FROM translations WHERE lang='".$_SESSION['translang']."' $showquery ORDER BY str");
$num=mysql_num_rows($q);
echo i18n("Showing %1 translation strings",array($num),array("number of strings"));

echo "<form method=\"post\" action=\"translations.php\">";
echo "<input type=\"hidden\" name=\"show\" value=\"$show\" />";
echo "<input type=\"hidden\" name=\"action\" value=\"save\" />";
echo "<input id=\"changedFields\" type=\"hidden\" name=\"changedFields\" value=\"\">";
?>
<script type="text/javascript">
function doFocus(strmd5) {
    var obj=document.getElementById('val_'+strmd5);
    var ch=document.getElementById('changedFields');
    obj.style.backgroundColor="#FFBFF2";
    if(ch.value)
        ch.value=ch.value+","+strmd5;
    else
        ch.value=strmd5;
    return true;
}
</script>

<?
echo "<table class=\"tableedit\">";
echo "<tr><th>";
echo "<img border=\"0\" src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\">\n";
echo "</th>";
echo "<th>".i18n("English")." / ".$translangname."</th></tr>\n";
while($r=mysql_fetch_object($q))
{
	echo "<tr>";
	echo "<td valign=\"top\" rowspan=\"2\">";
    echo "<input type=\"checkbox\" name=\"delete[]\" value=\"$r->strmd5\">\n";
    echo "</td><td>";
	echo htmlspecialchars($r->str);
	if($r->argsdesc)
		echo "<br /><i>".i18n("Arguments:")." $r->argsdesc </i>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td valign=\"top\"><input id=\"val_{$r->strmd5}\" onchange=\"return doFocus('{$r->strmd5}');\" style=\"width: 95%\" type=\"text\" name=\"val[{$r->strmd5}]\" value=\"".htmlspecialchars($r->val)."\" /></td>";
	echo "</tr>";
}
echo "</table>";
echo "<input type=\"submit\" value=\"".i18n("Save")."\">";
echo "</form>\n";

send_footer();
?>
