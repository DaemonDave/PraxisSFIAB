<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

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
 require("../tableeditor.class.php");
 require_once("../user.inc.php");

 user_auth_required('committee', 'admin');
 
 include ("fundraising_sponsorship_handler.inc.php");
 include ("fundraising_goals_handler.inc.php");
 include ("fundraising_main.inc.php");

 send_header("Donations",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Fundraising' => 'admin/fundraising.php'),
            "fundraising"
			);

?>
<script type="text/javascript">
$(document).ready(function() {
    //initialize the dialog
    $("#sponsorship_editor").dialog({
        bgiframe: true, autoOpen: false,
        modal: true, resizable: false,
        draggable: false
    });

    $("#fund_editor").dialog({
        bgiframe: true, autoOpen: false,
        modal: true, resizable: falsefundraising
        draggable: false
    });

refresh_fundraising_table(); 

});

function popup_sponsorship_editor(url) {
    var w = (document.documentElement.clientWidth * 0.6);
    $('#sponsorship_editor').dialog('option','width',w);
    //let the height autocalculate
/*    
    var h = (document.documentElement.clientHeight * 0.6);
    $('#sponsorship_editor').dialog('option','height',h);
    */
    $('#sponsorship_editor').dialog('option','buttons',{ "<?=i18n("Save")?>": function() { save_sponsorship(); },
    "<?=i18n("Cancel")?>": function(){ $(this).dialog("close");}});
    $('#sponsorship_editor').dialog('open');

    $('#sponsorship_editor_content').load(url);

    return false;
}

function save_sponsorship() {
    $('#debug').load("<?=$config['SFIABDIRECTORY']?>/admin/fundraising.php",
    $("#fundraisingsponsorship").serializeArray(),
    function() {
        $('#sponsorship_editor').dialog('close');
        refresh_fundraising_table();
    });
    return false;
}

function popup_fund_editor(url) {
    var w = (document.documentElement.clientWidth * 0.6);
    $('#fund_editor').dialog('option','width',w);
    //let the height autocalculate
/*    
    var h = (document.documentElement.clientHeight * 0.6);
    $('#fund_editor').dialog('option','height',h);
    */
    $('#fund_editor').dialog('option','buttons',{ "<?=i18n("Save")?>": function() { save_fund(); },
    "<?=i18n("Cancel")?>": function(){ $(this).dialog("close");}});
    $('#fund_editor').dialog('open');

    $('#fund_editor_content').load(url);

    return false;
}

function save_fund() {
    $("#debug").load("<?=$config['SFIABDIRECTORY']?>/admin/fundraising.php",
    $("#fundraisingfundraising").serializeArray(),
    function(data) {
        $('#fund_editor').dialog('close');
        refresh_fundraising_table();
    });
    return false;
}

function delete_fund(id) {
    if(confirmClick('Are you sure you want to remove this fund?')) {
        $('#debug').load("<?=$config['SFIABDIRECTORY']?>/admin/fundraising.php",
        { action: 'funddelete', delete: id },
        function() {
            refresh_fundraising_table();
        }
        );
    }
    return false;
}

function delete_sponsorship(id) {
    if(confirmClick('Are you sure you want to remove this sponsorship?')) {
        $('#debug').load("<?=$config['SFIABDIRECTORY']?>/admin/fundraising.php",
        { action: 'sponsorshipdelete', delete: id },
        function() {
            refresh_fundraising_table();
        }
        );
    }
    return false;
}

function refresh_fundraising_table() {
    $("#fundraisingmain").load("fundraising.php?action=fundraisingmain");
}
</script>
<?

 //first, insert any defaults
 $q=mysql_query("SELECT * FROM fundraising WHERE year='".$config['FAIRYEAR']."'");
 if(!mysql_num_rows($q)) {
	 $q=mysql_query("SELECT * FROM fundraising WHERE year='-1'");
	 while($r=mysql_fetch_object($q)) {
		 mysql_query("INSERT INTO fundraising (`type`,`name`,`description`,`system`,`goal`,`year`) VALUES ('$r->type','".mysql_real_escape_string($r->name)."','".mysql_real_escape_string($r->description)."','$r->system','$r->goal','".$config['FAIRYEAR']."')");
	 }
 }

echo "<div id=\"fundraisingmain\">";
echo "</div>";

 echo "<br />\n";
 echo "<br />\n";
 echo "<a href=\"sponsorship_levels.php\">Manage Donation Levels</a>\n";
 echo "<br />\n";
 echo "<a href=\"donors.php\">Manage Donors</a>\n";
 echo "<br />\n";

?>
<div style="display: none" title="<?=i18n("Donation Editor")?>" id="sponsorship_editor">
<div id="sponsorship_editor_content">
</div>
</div>
<div style="display: none" title="<?=i18n("Fund Editor")?>" id="fund_editor">
<div id="fund_editor_content">
</div>
</div>
<?
 send_footer();
?>
