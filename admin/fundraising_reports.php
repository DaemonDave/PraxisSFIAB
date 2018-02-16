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
 require("fundraising_common.inc.php");

send_header("Fundraising Reports",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Fundraising' => 'admin/fundraising.php'),
            "fundraising"
			);
?>
<script type="text/javascript">
$(document).ready( function(){
	$("#standardreportsaccordion").accordion();

});

</script>
<h3>Standard Reports</h3>
<div id="standardreportsaccordion" style="width: 600px;">
 <h3><a a href="#">List of Prospects by Appeal</a></h3>
 <div>
 <table><tr><td>
  Choose an appeal: 
  </td><td>
  <form method=get action="fundraising_reports_std.php">
  <input type="hidden" name="id" value="1">
  <select name="fundraising_campaigns_id">
   <option value="">All appeals</option>
   <?
   $q=mysql_query("SELECT * FROM fundraising_campaigns WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY name");
   while($r=mysql_fetch_object($q)) {
	   echo "<option value=\"$r->id\">$r->name</option>\n";
   }
   ?>
  </select>
  </td></tr>
  <tr><td>
  Report Type:
  </td><td>
  <select name="type">
   <option value="pdf">PDF</option>
   <option value="csv">CSV</option>
  </select>
  </td></tr>
  <tr><td colspan="2" style="text-align: center;">
  <input type="submit" value="Generate Report">
  </td></tr></table>
  </form>
 </div>
 <h3><a href="#">Results of Appeal by Purpose</a></h3>
 <div>
  <form method=get action="fundraising_reports_std.php">
  <input type="hidden" name="id" value="2">
 <table><tr><td>
  Choose a Purpose: 
  </td><td>
  <select name="goal">
   <option value="">All purposes</option>
   <?
   $q=mysql_query("SELECT * FROM fundraising_goals WHERE fiscalyear='{$config['FISCALYEAR']}' ORDER BY name");
   while($r=mysql_fetch_object($q)) {
	   echo "<option value=\"$r->goal\">$r->name</option>\n";
   }
   ?>
  </select>
  </td></tr>
  <tr><td>
  Report Type:
  </td><td>
  <select name="type">
   <option value="pdf">PDF</option>
   <option value="csv">CSV</option>
  </select>
  </td></tr>
  <tr><td colspan="2" style="text-align: center;">
  <input type="submit" value="Generate Report">
  </td></tr></table>
  </form>
 </div>
</div>
<br />
<br />

<h3>Custom Reports</h3>
<ul>
 <li><a href="#">(custom reports will be here)</a></li>
</ul>
<?
 send_footer();
?>
