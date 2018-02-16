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
 send_header("Mailing Label Generator",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php',
			'Reports' => 'admin/reports.php')
			);

?>

<script type="text/javascript">
function stockChange()
{
	var val=document.forms.mailinglabels.stock.options[document.forms.mailinglabels.stock.selectedIndex].value;
	var v=val.split(":");

	document.forms.mailinglabels.height.value=v[1];
	document.forms.mailinglabels.width.value=v[2];
	document.forms.mailinglabels.yspacer.value=v[3];
	document.forms.mailinglabels.xspacer.value=v[4];
	document.forms.mailinglabels.fontsize.value=v[5];
	document.forms.mailinglabels.toppadding.value=v[6];
	document.forms.mailinglabels.type.value=v[7];
}

function reportChange()
{
	var val=document.forms.mailinglabels.reportselect.options[document.forms.mailinglabels.reportselect.selectedIndex].value;
	var v=val.split(":");
	document.forms.mailinglabels.report.value=v[0];
	document.forms.mailinglabels.reportname.value=v[1];
}
</script>

<?
 echo "<br />";

 echo "<form method=\"get\" name=\"mailinglabels\" action=\"reports_mailinglabels_generator.php\">";
 echo "<input type=\"hidden\" name=\"type\" value=\"pdf\">";
 echo "<input type=\"hidden\" name=\"report\" value=\"pdf\">";
 echo "<input type=\"hidden\" name=\"reportname\" value=\"pdf\">";

 echo "<select name=\"reportselect\" onchange=\"reportChange()\">";
 echo "<option value=\"\">".i18n("Choose which labels")."</option>\n";
 echo "<option value=\"sponsors:Award Sponsors\">".i18n("Award Sponsors")."</option>\n";
 echo "<option value=\"judges:Judges\">".i18n("Judges")."</option>\n";
 echo "<option value=\"schools:Schools\">".i18n("Schools")."</option>\n";
 echo "</select>";
 echo "<br />";


 echo "<select name=\"stock\" onchange=\"stockChange()\">";
 echo "<option value=\"Custom::::::\">".i18n("Choose label stock")."</option>\n";
 echo "<option value=\"Avery #05161:1:4:0.00:0.25:10:0.5:pdf\">Avery #05161 1\"x4\"</option>\n";
 echo "<option value=\"Avery #05162:1.3333:4:0.00:0.25:10:0.75:pdf\">Avery #05162 1 1/3\"x4\"</option>\n";
 echo "<option value=\"Avery #05163:2:4:0.0:0.25:12:0:pdf\">Avery #05163 2\"x4\"</option>\n";
 echo "<option value=\"Custom:::::::pdf\">Custom</option>\n";
 echo "<option value=\"CSV:::::::csv\">CSV (Plain Text)</option>\n";
 echo "</select>";

 echo "<table>";
 echo "<tr><td>Label Height:</td><td><input size=\"5\" type=\"text\" name=\"height\" id=\"height\">\"</td></tr>";
 echo "<tr><td>Label Width:</td><td><input size=\"5\" type=\"text\" name=\"width\" id=\"width\">\"</td></tr>";
 echo "<tr><td>Label Y-Space:</td><td><input size=\"5\" type=\"text\" name=\"yspacer\" id=\"yspacer\">\"</td></tr>";
 echo "<tr><td>Label X-Space:</td><td><input size=\"5\" type=\"text\" name=\"xspacer\" id=\"xspacer\">\"</td></tr>";
 echo "<tr><td>Font Size:</td><td><input size=\"5\" type=\"text\" name=\"fontsize\" id=\"fontsize\">pt</td></tr>";
 echo "<tr><td>Top Padding:</td><td><input size=\"5\" type=\"text\" name=\"toppadding\" id=\"toppadding\">\"</td></tr>";
 echo "</table>";
 echo "<input type=\"submit\" value=\"Generate Mailing Labels\">";

 echo "</form>";

 send_footer();
?>
