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
 require("../common.inc.php");
 require_once("../user.inc.php");

 user_auth_required('committee', 'admin');

 //first, insert any default fundraising donor levels
 $q=mysql_query("SELECT * FROM fundraising_donor_levels WHERE fiscalyear='".$config['FISCALYEAR']."'");
 if(!mysql_num_rows($q)) {
     $q=mysql_query("SELECT * FROM fundraising_donor_levels WHERE fiscalyear='-1'");
     while($r=mysql_fetch_object($q)) {
         mysql_query("INSERT INTO fundraising_donor_levels (`level`,`min`,`max`,`description`,`fiscalyear`) VALUES (
            '".mysql_real_escape_string($r->level)."',
            '".mysql_real_escape_string($r->min)."',
            '".mysql_real_escape_string($r->max)."',
            '".mysql_real_escape_string($r->description)."',
            '".$config['FISCALYEAR']."')");
     }
 }

 //first, insert any default fundraising goals
 $q=mysql_query("SELECT * FROM fundraising_goals WHERE fiscalyear='".$config['FISCALYEAR']."'");
 if(!mysql_num_rows($q)) {
     $q=mysql_query("SELECT * FROM fundraising_goals WHERE fiscalyear='-1'");
     while($r=mysql_fetch_object($q)) {
         mysql_query("INSERT INTO fundraising_goals (`goal`,`name`,`description`,`system`,`budget`,`fiscalyear`) VALUES (
            '".mysql_real_escape_string(stripslashes($r->goal))."',
            '".mysql_real_escape_string(stripslashes($r->name))."',
            '".mysql_real_escape_string(stripslashes($r->description))."',
            '".mysql_real_escape_string($r->system)."',
            '".mysql_real_escape_string($r->budget)."',
            '".$config['FISCALYEAR']."')");
     }
 }


 switch($_GET['gettab']) {
	case "levels": 
		$q=mysql_query("SELECT * FROM fundraising_donor_levels WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY max");
		echo "<div id=\"levelaccordion\" style=\"width: 75%;\">\n";
		while($r=mysql_fetch_object($q)) {
			echo "<h3><a href=\"#\">$r->level (".format_money($r->min,false)." to ".format_money($r->max,false).")</a></h3>\n";
			echo "<div id=\"level_$r->id\">\n";
            echo "<form id=\"level_form_$r->id\" onsubmit=\"return level_save($r->id)\">\n";
            echo "<input type=\"hidden\" name=\"id\" value=\"$r->id\">\n";
            echo "<table style=\"width: 100%;\">";
            echo "<tr><td>";
            echo i18n("Level Name").":</td><td><input type=\"text\" size=\"40\" name=\"level\" value=\"".htmlspecialchars($r->level)."\"></td></tr>\n";
            echo "<tr><td>";
            echo i18n("Value Range").":</td><td>\$<input size=\"5\" type=\"text\" name=\"min\" value=\"$r->min\"> to \$<input size=\"5\" type=\"text\" name=\"max\" value=\"$r->max\"><br />\n";
            echo "</td></tr>\n";
            echo "<tr><td colspan=\"2\">";
            echo i18n("Description/Benefits").":<br /><textarea name=\"description\" rows=\"4\" style=\"width: 100%;\">".htmlspecialchars($r->description)."</textarea>";
            echo "</td></tr>\n";
            echo "</table>\n";
            echo "<table style=\"width: 100%;\"><tr><td style=\"width: 50%; text-align: center;\">";
            echo "<input type=\"submit\" value=\"".i18n("Save Level")."\" >";
            echo "</td><td style=\"width: 50%; text-align: right;\">";
            echo "<input type=\"button\" value=\"".i18n("Delete Level")."\" onclick=\"return level_delete($r->id)\" >";
            echo "</td></tr></table>\n";
            echo "</form>";
			echo "</div>\n";
		}

		echo "<h3><a href=\"#\">Create New Level</a></h3>\n";
		echo "<div id=\"level_new\">\n";
            echo "<form id=\"level_form\" onsubmit=\"return level_save()\">\n";

            echo "<table style=\"width: 100%;\">";
            echo "<tr><td>";
            echo i18n("Level Name").":</td><td><input type=\"text\" size=\"40\" name=\"level\"></td></tr>\n";
            echo "<tr><td>";
            echo i18n("Value Range").":</td><td>\$<input size=\"5\" type=\"text\" name=\"min\"> to \$<input size=\"5\" type=\"text\" name=\"max\"><br />\n";
            echo "</td></tr>\n";
            echo "<tr><td colspan=\"2\">";
            echo i18n("Description/Benefits").":<br /><textarea name=\"description\" rows=\"4\" style=\"width: 100%;\"></textarea>";
            echo "</td></tr>\n";
            echo "</table>\n";

            echo "<table style=\"width: 100%;\"><tr><td style=\"width: 50%; text-align: center;\">";
            echo "<input type=\"submit\" value=\"".i18n("Create Level")."\">";
            echo "</td><td style=\"width: 50%; text-align: right;\">";
            echo "</td></tr></table>\n";
            echo "</form>\n";
		echo "</div>\n";

		echo "</div>\n";

	exit;
	break;

	case "goals": 
		$q=mysql_query("SELECT * FROM fundraising_goals WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY name");
		echo "<div id=\"goalaccordion\" style=\"width: 75%;\">\n";
		while($r=mysql_fetch_object($q)) {
			echo "<h3><a href=\"#\">$r->name (".format_money($r->budget,false).") Deadline: ".format_date($r->deadline)."</a></h3>\n";
			echo "<div id=\"goal_$r->id\">\n";
            echo "<form id=\"goal_form_$r->id\" onsubmit=\"return goal_save($r->id)\">\n";
            echo "<input type=\"hidden\" name=\"id\" value=\"$r->id\">\n";

            echo "<table style=\"width: 100%;\">";
            echo "<tr><td>";
            echo i18n("Purpose").":</td><td><input type=\"text\" size=\"40\" name=\"name\" value=\"".htmlspecialchars($r->name)."\"></td></tr>\n";
            echo "<tr><td>";
            echo i18n("Budget Amount").":</td><td>\$<input size=\"5\" type=\"text\" name=\"budget\" value=\"$r->budget\"></td></tr>";
            echo "<tr><td>";
            echo i18n("Deadline").":</td><td><input size=\"9\" type=\"text\" name=\"deadline\" value=\"$r->deadline\"></td></tr>";
            echo "<tr><td colspan=\"2\">";
            echo i18n("Description").":<br /><textarea name=\"description\" rows=\"4\" style=\"width: 100%;\">".htmlspecialchars($r->description)."</textarea>";
            echo "</td></tr>\n";
            echo "</table>\n";
            echo "<table style=\"width: 100%;\"><tr><td style=\"width: 50%; text-align: center;\">";
            echo "<input type=\"submit\" value=\"".i18n("Save Purpose")."\" >";
            echo "</td><td style=\"width: 50%; text-align: right;\">";
            echo "<input type=\"button\" value=\"".i18n("Delete Purpose")."\" onclick=\"return goal_delete($r->id)\" >";
            echo "</td></tr></table>\n";
            echo "</form>";
			echo "</div>\n";
		}

		echo "<h3><a href=\"#\">Create New Purpose</a></h3>\n";
		echo "<div id=\"goal_new\">\n";
            echo "<form id=\"goal_form\" onsubmit=\"return goal_save()\">\n";
            echo "<table style=\"width: 100%;\">";
            echo "<tr><td>";
            echo i18n("Purpose Name").":</td><td><input type=\"text\" size=\"40\" name=\"name\"></td></tr>\n";
            echo "<tr><td>";
            echo i18n("Budget Amount").":</td><td>\$<input size=\"5\" type=\"text\" name=\"budget\"></td></tr>";
            echo "<tr><td>";
            echo i18n("Deadline").":</td><td><input size=\"9\" type=\"text\" name=\"deadline\"></td></tr>";
            echo "<tr><td colspan=\"2\">";
            echo i18n("Description").":<br /><textarea name=\"description\" rows=\"4\" style=\"width: 100%;\"></textarea>";
            echo "</td></tr>\n";
            echo "</table>\n";

            echo "<table style=\"width: 100%;\"><tr><td style=\"width: 50%; text-align: center;\">";
            echo "<input type=\"submit\" value=\"".i18n("Create Purpose")."\">";
            echo "</td><td style=\"width: 50%; text-align: right;\">";
            echo "</td></tr></table>\n";
            echo "</form>\n";
		echo "</div>\n";

		echo "</div>\n";


    exit;
	break;

	case "setup": 
        echo "<form id=\"setup_form\" onsubmit=\"return setup_save()\">";
        echo "<table cellspacing=3 cellpadding=3>";
        echo "<tr><td>".i18n("Current Fiscal Year")."</td><td>";
        echo $config['FISCALYEAR'];
        echo "</td></tr>\n";
        echo "<tr><td>".i18n("Fiscal Year End")."</td><td>";
        list($month,$day)=split("-",$config['fiscal_yearend']);
        emit_month_selector("fiscalendmonth",$month);
        emit_day_selector("fiscalendday",$day);
        echo "</td></tr>\n";
        echo "<tr><td>".i18n("Is your organization a registered charity?")."</td>";
        echo "<td>";
        if($config['registered_charity']=="yes") $ch="checked=\"checked\""; else $ch="";
        echo "<label><input $ch type=\"radio\" name=\"registeredcharity\" value=\"yes\" id=\"registeredcharity_yes\" onchange=\"charitychange()\">".i18n("Yes")."</label>";
        echo "&nbsp;&nbsp;&nbsp;";
        if($config['registered_charity']=="no") $ch="checked=\"checked\""; else $ch="";
        echo "<label><input $ch type=\"radio\" name=\"registeredcharity\" value=\"no\" id=\"registeredcharity_no\" onchange=\"charitychange()\">".i18n("No")."</label>";
        echo "</td></tr>\n";
        echo "<tr>";
        echo "<td>".i18n("Charity Registration Number")."</td><td><input type=\"text\" name=\"charitynumber\" id=\"charitynumber\" value=\"{$config['charity_number']}\"></td>";
        echo "</tr>";
        echo "<tr><td colspan=\"2\" style=\"text-align: center;\"><input type=\"submit\" value=\"".i18n("Save")."\"></td></tr>\n";
        echo "</table>\n";
        echo "</form>\n";
    exit;
	break;
 }

 switch($_GET['action']) {
        case "level_save":
           $id=$_POST['id'];
           if(! ($_POST['level'] && $_POST['min'] && $_POST['max'])) {
                error_("Level name, minimum and maximum value range are required");
                exit;
           }
           if($_POST['min']>=$_POST['max']) {
                error_("Value range minimum must be smaller than range maximum");
                exit;
           }

           if($id) {
                mysql_query("UPDATE fundraising_donor_levels SET
                    min='".mysql_real_escape_string($_POST['min'])."',
                    max='".mysql_real_escape_string($_POST['max'])."',
                    level='".mysql_real_escape_string(stripslashes($_POST['level']))."',
                    description='".mysql_real_escape_string(stripslashes($_POST['description']))."'
                    WHERE id='$id' AND fiscalyear='{$config['FISCALYEAR']}'
                    ");
                happy_("Level Saved");
            }
            else {
                mysql_query("INSERT INTO fundraising_donor_levels (`level`,`min`,`max`,`description`,`fiscalyear`) VALUES (
                '".mysql_real_escape_string($_POST['level'])."',
                '".mysql_real_escape_string($_POST['min'])."',
                '".mysql_real_escape_string($_POST['max'])."',
                '".mysql_real_escape_string($_POST['description'])."',
                '{$config['FISCALYEAR']}')");
                happy_("Level Created");
            }
        exit;
        break;
        case "level_delete":
           $id=$_POST['id'];
           mysql_query("DELETE FROM fundraising_donor_levels WHERE id='$id' AND fiscalyear='{$config['FISCALYEAR']}'");
           happy_("Level Deleted");
       exit;
       break;

        case "goal_save":
           $id=$_POST['id'];
           if(! ($_POST['name'] && $_POST['budget'])) {
                error_("Purpose name and budget are required");
                exit;
           }
           if($id) {
                mysql_query("UPDATE fundraising_goals SET
                    budget='".mysql_real_escape_string($_POST['budget'])."',
                    deadline='".mysql_real_escape_string($_POST['deadline'])."',
                    name='".mysql_real_escape_string(stripslashes($_POST['name']))."',
                    description='".mysql_real_escape_string(stripslashes($_POST['description']))."'
                    WHERE id='$id' AND fiscalyear='{$config['FISCALYEAR']}'
                    ");
                happy_("Purpose Saved");
            }
            else {
                $goal=strtolower($_POST['name']);
                $goal=ereg_replace("[^a-z]","",$goal);
                $q=mysql_query("SELECT * FROM fundraising_goals WHERE goal='$goal' AND fiscalyear='{$config['FISCALYEAR']}'");
                echo mysql_error();
                if(mysql_num_rows($q)) {
                    error_("The automatically generated purpose key (%1) generated from (%2) is not unique.  Please try a different Purpose Name",array($goal,$_POST['name']));
                    exit;
                }

                mysql_query("INSERT INTO fundraising_goals (`goal`,`name`,`budget`,`deadline`,`description`,`fiscalyear`) VALUES (
                '".mysql_real_escape_string($goal)."',
                '".mysql_real_escape_string($_POST['name'])."',
                '".mysql_real_escape_string($_POST['budget'])."',
                '".mysql_real_escape_string($_POST['deadline'])."',
                '".mysql_real_escape_string($_POST['description'])."',
                '{$config['FISCALYEAR']}')");
                happy_("Purpose Created");
            }
        exit;
        break;
        case "goal_delete":
           $id=$_POST['id'];
           //they cant delete system ones
           $q=mysql_query("SELECT * FROM fundraising_goals WHERE id='$id' AND fiscalyear='{$config['FISCALYEAR']}'");
           if(!$r=mysql_fetch_object($q)) {
               error_("Invalid goal to delete");
               exit;
           }
           if($r->system=="yes") {
               error_("Fundraising goals created automatically and used by the system cannot be deleted");
               exit;
           }
           $q=mysql_query("SELECT * FROM fundraising_donations WHERE fundraising_goal='$r->goal' AND fiscalyear='{$config['FISCALYEAR']}'");
           if(mysql_num_rows($q)) {
               error_("This goal already has donations assigned to it, it cannot be deleted");
               exit;
           }

           mysql_query("DELETE FROM fundraising_goals WHERE id='$id' AND fiscalyear='{$config['FISCALYEAR']}'");
           happy_("Purpose Deleted");
       exit;
       break;

       case "setup_save":
       $fye=sprintf("%02d-%02d",intval($_POST['fiscalendmonth']),intval($_POST['fiscalendday']));
       mysql_query("UPDATE config SET val='$fye' WHERE var='fiscal_yearend' AND year='{$config['FAIRYEAR']}'");
       mysql_query("UPDATE config SET val='".mysql_real_escape_string($_POST['registeredcharity'])."' WHERE var='registered_charity' AND year='{$config['FAIRYEAR']}'");
       mysql_query("UPDATE config SET val='".mysql_real_escape_string($_POST['charitynumber'])."' WHERE var='charity_number' AND year='{$config['FAIRYEAR']}'");
       happy_("Fundraising module setup saved");
       exit;
       break;

 }

 send_header("Fundraising Setup",
		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Fundraising' => 'admin/fundraising.php')
		);

?>
<script type="text/javascript">
/* Setup the popup window */
$(document).ready(function() {

    $("#editor_tabs").tabs({
        show: function(event, ui) {
            switch(ui.panel.id) {
                case 'editor_tab_levels':
                    update_levels();
                    break;
                case 'editor_tab_goals':
                    update_goals();
                    break;
                break;
                case 'editor_tab_setup':
                    update_setup();
                    break;
                break;
            }
        },
        selected: 0
    });

//    $("#organizationinfo_fundingselectiondate").datepicker({ dateFormat: 'yy-mm-dd', showOn: 'button', buttonText: "<?=i18n("calendar")?>" });

});

function update_levels() {
	$("#editor_tab_levels").load("fundraising_setup.php?gettab=levels",null,
			function() {
					$("#levelaccordion").accordion();
			}
    );
}

function level_save(id) {
    if(id) var f=$("#level_form_"+id); 
    else var f=$("#level_form");

	$("#debug").load("fundraising_setup.php?action=level_save",f.serializeArray(), function() { update_levels(); });
    return false;
}

function level_delete(id) {
    if(confirmClick('Are you sure you want to delete this fundraising level?')) {
        var f=$("#level_form_"+id); 
        $("#debug").load("fundraising_setup.php?action=level_delete",f.serializeArray(), function() { update_levels(); });
    }
    return false;
}

function update_goals() {
	$("#editor_tab_goals").load("fundraising_setup.php?gettab=goals",null,
			function() {
					$("#goalaccordion").accordion();
                    $("[name=deadline]").datepicker({ dateFormat: 'yy-mm-dd'});
			}
    );
}

function update_setup() {
	$("#editor_tab_setup").load("fundraising_setup.php?gettab=setup",null,function() { charitychange(); });
}
function setup_save() {
	$("#debug").load("fundraising_setup.php?action=setup_save",$("#setup_form").serializeArray(), function() { update_setup(); });
    return false;
}

function goal_save(id) {
    if(id) var f=$("#goal_form_"+id); 
    else var f=$("#goal_form");

	$("#debug").load("fundraising_setup.php?action=goal_save",f.serializeArray(), function() { update_goals(); });
    return false;
}

function goal_delete(id) {
    if(confirmClick('Are you sure you want to delete this fundraising goal?')) {
        var f=$("#goal_form_"+id); 
        $("#debug").load("fundraising_setup.php?action=goal_delete",f.serializeArray(), function() { update_goals(); });
    }
    return false;
}

function charitychange() {
    if($("input[@name='registeredcharity']:checked").val()=="yes") {
        $("#charitynumber").attr("disabled","");
    }
    else {
        $("#charitynumber").attr("disabled","disabled");
    }

}


</script>

<div id="setup" style="width: 780px;">
    <div id="editor_tabs">
        <ul>
            <li><a href="#editor_tab_setup"><span><?=i18n('Module Setup')?></span></a></li>
            <li><a href="#editor_tab_levels"><span><?=i18n('Fundraising Levels')?></span></a></li>
            <li><a href="#editor_tab_goals"><span><?=i18n('Fundraising Purposes')?></span></a></li>
        </ul>

        <div id="editor_tab_setup">
        </div>
        <div id="editor_tab_levels">
        </div>
        <div id="editor_tab_goals">
        </div>
    </div>
</div>

<?
send_footer();
?>
