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
	require_once("../user.inc.php");

	user_auth_required('committee', 'admin');

	if($_GET['id']) {
		$id=intval($_GET['id']);
		$q=mysql_query("SELECT * FROM fundraising WHERE id='$id'");
	//	echo "<h2>Edit Fund</h2>";
		$fund=mysql_fetch_object($q);
		$formaction="fundedit";
	}
	else {
	//	echo "<h2>Create New Fund</h2>";
		$formaction="fundadd";
	}
    echo "<form id=\"fundraisingfundraising\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"$formaction\">";
	echo "<input type=\"hidden\" name=\"fundraising_id\" value=\"$id\">";

	echo "<table class=\"tableedit\" style=\"width: 90%;\">";
	if($fund->system=="yes") {
		echo "<tr><th>".i18n("Type")."</th><td>".i18n("System (non-editable)")."</td></tr>\n";
		echo "<tr><th>".i18n("Name")."</th><td>".htmlspecialchars($fund->name)."</td></tr>\n";
		echo "<tr><th>".i18n("Key")."</th><td>".htmlspecialchars($fund->type)."</td></tr>\n";
	}
	else {
		echo "<tr><th>".i18n("Type")."</th><td>".i18n("Custom (editable)")."</td></tr>\n";
		echo "<tr><th>".i18n("Name")."</th><td><input type=\"text\" name=\"name\" value=\"".htmlspecialchars($fund->name)."\"></td></tr>\n";
		echo "<tr><th>".i18n("Key")."</th><td><input type=\"text\" name=\"type\" value=\"".htmlspecialchars($fund->type)."\"></td></tr>\n";
	}
	echo "<tr><th>".i18n("Description")."</th><td><textarea style=\"width: 100%; height: 4em;\" name=\"description\">".htmlspecialchars($fund->description)."</textarea></td></tr>\n";
	echo "<tr><th>".i18n("Goal")."</th><td><input type=\"text\" size=\"8\" name=\"goal\" value=\"$fund->goal\"></td></tr>\n";
	echo "</table>\n";
    echo "</form>\n";

?>
