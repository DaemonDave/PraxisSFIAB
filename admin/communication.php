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
 require_once("../common.inc.php");
 require_once("../user.inc.php");
 include "communication.inc.php";
 user_auth_required('committee', 'admin');

 function launchQueue() {
	 if(!file_exists("../data/logs")) {
		 mkdir("../data/logs");
	 }
	 exec("php -q send_emailqueue.php >>../data/logs/emailqueue.log 2>&1 &");
 }

/* dialog_choose
 * select:  comm_dialog_choose_select(emails_id) 
 * cancel:  comm_dialog_choose_cancel() */

switch($_GET['action']) {
case 'dialog_choose_load':
	$emails_id = intval($_GET['emails_id']);
	$q = mysql_query("SELECT * FROM emails WHERE id='$emails_id'");
	$e = mysql_fetch_assoc($q);
	?>
	<table class="editor">
	<tr><td class="label" style="width:15%"><?=i18n('Name')?>:</td><td class="input"><?=$e['name']?></td></tr>
	<tr><td class="label"><?=i18n('Subject')?>:</td><td class="input"><?=$e['subject']?></td></tr>
	<tr><td class="label"><?=i18n('From Address')?>:</td><td class="input"><?=$e['from']?></td></tr>
	<tr><td></td><td>
		<div style="border:1px solid black; overflow:auto; height=300px;"><?=$e['bodyhtml']?></div>
	</td></tr></table>
	<?
	exit;

case 'dialog_choose':
	?>
	<div id="comm_dialog_choose" title="Select a Communication" style="display: none">
	<h4><?=i18n("Select a Communication")?>:</h4>
	<form id="choose" onchange="dialog_choose_change()" onkeypress="dialog_choose_change()" >
	<table style="width:100%"><tr><td>
	<select id="comm_dialog_choose_emails_id">
		<option value="-1">-- <?=i18n('Choose a Communication')?> --</option>
	<?
	$type = mysql_real_escape_string($_GET['type']);
	$q = mysql_query("SELECT * FROM emails WHERE type='$type'");
	while($e = mysql_fetch_assoc($q)) {
		echo "<option value=\"{$e['id']}\">{$e['name']}</option>";
	}
	?>
	</select>
	</td><td style="text-align:right">
	<input class="comm_dialog_choose_email_button" disabled="disabled" type="submit" value="<?=i18n('Choose')?>" >
	<input class="comm_dialog_choose_cancel_button" type="submit" value="<?=i18n('Cancel')?>" >
	</td></tr></table>
	<hr />
	<div id="comm_dialog_choose_info"></div>
	<hr />
	<input class="comm_dialog_choose_email_button" disabled="disabled" type="submit" value="<?=i18n('Choose')?>" >
	<input class="comm_dialog_choose_cancel_button" type="submit" value="<?=i18n('Cancel')?>" >
	</form>
	</div>
	<script type="text/javascript">
		var comm_dialog_choose_selected = -1;
		$(".comm_dialog_choose_email_button").click(function () { 
						var sel = $("#comm_dialog_choose_emails_id").val();
						comm_dialog_choose_selected = sel;
						$('#comm_dialog_choose').dialog("close");
						return false;
					});
		$(".comm_dialog_choose_cancel_button").click(function () { 
						$('#comm_dialog_choose').dialog("close");
						return false;
					});
	

		function dialog_choose_change()
		{
			var sel = $("#comm_dialog_choose_emails_id").val();
			$("#comm_dialog_choose_info").html("Loading...");
			$("#comm_dialog_choose_info").load("<?=$config['SFIABDIRECTORY']?>/admin/communication.php?action=dialog_choose_load&emails_id="+sel);
			if(sel == -1) {
				$(".comm_dialog_choose_email_button").attr('disabled','disabled');
			} else {
				$(".comm_dialog_choose_email_button").removeAttr('disabled');
			}
	        return false;
		}

		$("#comm_dialog_choose").dialog({
				bgiframe: true, autoOpen: true,
				modal: true, resizable: false,
				draggable: false,
				width: 700, //(document.documentElement.clientWidth * 0.8);
				height: (document.documentElement.clientHeight * 0.8),
				close: function() {
						$(this).dialog('destroy');
						$('#comm_dialog_choose').remove();
						/* Run callbacks */
						if(comm_dialog_choose_selected != -1) {
							if(typeof(comm_dialog_choose_select) == 'function') {
								comm_dialog_choose_select(comm_dialog_choose_selected);
							}
						} else {
							if(typeof(comm_dialog_choose_cancel) == 'function') {
								comm_dialog_choose_cancel();
							}
						}
					}
				});

	</script>
	<?
	exit;

case 'email_save':
	$id = intval($_POST['emails_id']);

	//we need to character encode BEFORE we myql_real_escape_strintg
	//otherwise, a smartquote ' will turn into a normal ' that ends up
	//not being escaped!
	$name=$_POST['name'];
	$description=$_POST['description'];
	$from=$_POST['from'];
	$subject=$_POST['subject'];
	$bodyhtml=$_POST['bodyhtml'];

	//add //TRANSLIT to approximate any characters (eg smartquotes) that it doesnt know
	$bodyhtml=iconv("UTF-8","ISO-8859-1//TRANSLIT",$bodyhtml);
	$name=iconv("UTF-8","ISO-8859-1//TRANSLIT",$name);
	$description=iconv("UTF-8","ISO-8859-1//TRANSLIT",$description);
	$from=iconv("UTF-8","ISO-8859-1//TRANSLIT",$from);
	$subject=iconv("UTF-8","ISO-8859-1//TRANSLIT",$subject);

	//Now its safe to escape it for the db query
	$name = mysql_real_escape_string(stripslashes($name));
	$description = mysql_real_escape_string(stripslashes($description));
	$from = mysql_real_escape_string(stripslashes($from));
	$subject = mysql_real_escape_string(stripslashes($subject));
	$bodyhtml = mysql_real_escape_string(stripslashes($bodyhtml));

	$type = mysql_real_escape_string($_POST['type']);
	$key = mysql_real_escape_string($_POST['key']);
	$fcid = mysql_real_escape_string($_POST['fcid']);

	if($id == 0) {
		if($key && $name) {
			mysql_query("INSERT INTO emails(type,val) VALUES('$type','$key')");
			echo mysql_error();
			$id = mysql_insert_id();
		} else {
			error_("Email Key and Name are required");
			exit;
		}
	}

	/* Allow the fundraising campaigns id to be NULL, it'll never be 0 */
	$fcstr = ($fcid == 0) ? 'NULL' : "'$fcid'";

	$body=getTextFromHtml($bodyhtml);
	mysql_query("UPDATE emails SET 
								name='$name',
								description='$description',
								`from`='$from',
								subject='$subject',
								body='$body',
								bodyhtml='$bodyhtml',
								fundraising_campaigns_id=$fcstr
						WHERE id='$id'");
	echo mysql_error();
	happy_("Email Saved");
	exit;

case 'dialog_edit':

	if(array_key_exists('id', $_GET)) {
		$id = intval($_GET['id']);
		$cloneid = 0;
	} else if(array_key_exists('cloneid', $_GET)) {
		$id = intval($_GET['cloneid']);
		$clone_id = $id;
	} else {
		/* new email, set defaults which may be specified */
		$id = 0;
		$key = htmlspecialchars($_GET['key']);
		if(array_key_exists('fundraising_campaigns_id', $_GET)) {
			$fcid = intval( $_GET['fundraising_campaigns_id']);
			$type = 'fundraising';
			$q=mysql_query("SELECT * FROM fundraising_campaigns WHERE id='$fcid'");
			$fc=mysql_fetch_object($q);
			$name=i18n("%1 communication for %2",array(ucfirst($key),$fc->name));
		} else {
			$fcid = 0;
			$type = (array_key_exists('type',$_GET)) ? $_GET['type'] : 'user';
		}

		$from=$_SESSION['name']." <".$_SESSION['email'].">";
	}
	if($id) {
		$q = mysql_query("SELECT * FROM emails WHERE id='$id'");
		if(mysql_num_rows($q) != 1) {
			echo "Ambiguous edit";
			exit;
		}
		$e = mysql_fetch_assoc($q);

		/* If we're supposed to clone it, load it then zero out the
		 * id so we make a new record on save, and override the key */
		if($clone_id) {
			$e['id'] = 0;
			$e['val'] = $_GET['key'];
			$e['fundraising_campaigns_id'] = $_GET['fundraising_campaigns_id'];
		}
		$emails_id = $e['id'];
		$name = htmlspecialchars($e['name']);
		$key = htmlspecialchars($e['val']);
		$description = htmlspecialchars($e['description']);
		$from = htmlspecialchars($e['from']);
		if(!$from && $config['fairmanageremail']) $from="Fair Manager <".$config['fairmanageremail'].">";
		$subject = htmlspecialchars($e['subject']);
		$body = $e['body'];
		$bodyhtml = $e['bodyhtml'];
		$fcid = intval($e['fundraising_campaigns_id']);
		if($bodyhtml == '') $bodyhtml = nl2br($body);
	}


	?>
	<div id="comm_dialog_edit" title="Edit a Communication" style="display: none">
	<br />
	<form id="comm_dialog_edit_form">
	<input type="hidden" name="type" value="<?=$type?>" />
	<input type="hidden" name="fcid" value="<?=$fcid?>" />
	<table class="editor" style="width: 95%">
	<? 
		if($emails_id) {
			?>
			<input type="hidden" name="emails_id" value="<?=$emails_id?>" />
			<input type="hidden" name="key" value="<?=$key?>" />
			<tr>
				<td class="label"><?=i18n("Email Key")?>:</td>
				<td class="input"><?=$key?></td>
			</tr>
			<?
		}
		else if($key)  {
			echo "<input type=\"hidden\" name=\"key\" value=\"$key\" />\n";
		}
		else {
			?>
			<tr>
				<td class="label"><?=i18n("Email Key")?>:</td>
				<td class="input"><input type="text" name="key" size="60" value="" /></td>
			</tr>
<?
		}
	/* ="fcid=$fcid, key=$key, type=$type"*/ ?>
	
	<tr>
		<td class="label"><?=i18n("Name")?>:</td>
		<td class="input"><input type="text" name="name" size="60" value="<?=$name?>" /></td>
	</tr>
	<tr>
		<td class="label"><?=i18n("Description")?>:</td>
		<td class="input"><input type="text" name="description" size="60" value="<?=$description?>" /></td>
	</tr><tr>
		<tr><td colspan="2"><hr /></td>
	</tr><tr>
		<td class="label"><?=i18n("From Address")?>:</td>
		<td class="input"><input type="text" name="from" size="60" value="<?=$from?>" /></td>
	</tr><tr>
		<td class="label"><?=i18n("Subject")?>:</td>
		<td class="input"><input type="text" name="subject" size="60" value="<?=$subject?>" /></td>
	</tr><tr>
		<td colspan="2" class="input">
			<table width="100%"><tr><td width="85%">
				<div id="fck">
				<textarea id="bodyhtml" name="bodyhtml" rows=6 cols=80><?=$bodyhtml?></textarea>
				</div>
				</td><td width="15%">
					<select id="comm_dialog_insert_field" name="insert_field" size="20" style="height:300" >
					<option value="EMAIL">[EMAIL]</option>
					<option value="FAIRNAME">[FAIRNAME]</option>
					<option value="FIRSTNAME">[FIRSTNAME]</option>
					<option value="LASTNAME">[LASTNAME]</option>
					<option value="NAME">[NAME]</option>
					<option value="SALUTATION">[SALUTATION]</option>
					<option value="PASSWORD">[PASSWORD]</option>
					<option value="REGNUM">[REGNUM]</option>
					<option value="URLMAIN">[URLMAIN]</option>
					<option value="URLLOGIN">[URLLOGIN]</option>
					<option value="ACCESSCODE" title="School Access Code">[ACCESSCODE]</option>
					</select>
				</td></tr></table>
		</td>
	</tr></table>
	<hr />
	<div align="right">
		<input type="submit" id="comm_dialog_edit_save_button" value="<?=i18n('Save')?>" />
		<input type="submit" id="comm_dialog_edit_cancel_button" value="<?=i18n('Cancel')?>" />
	</div>
	</form>
	</div>
	<script type="text/javascript" src="<?=$config['SFIABDIRECTORY']?>/fckeditor/fckeditor.js"></script>
	<script type="text/javascript">
		var comm_dialog_edit_saved = false;
		$("#comm_dialog_edit_save_button").click(function () { 
						var oFCKeditor = FCKeditorAPI.GetInstance('bodyhtml') ;
						var value = oFCKeditor.GetHTML();
						$('#bodyhtml').val(value);
						$("#debug").load("<?=$config['SFIABDIRECTORY']?>/admin/communication.php?action=email_save", $("#comm_dialog_edit_form").serializeArray(),
									function() {
										comm_dialog_edit_saved = true;
										$('#comm_dialog_edit').dialog("close");
									});
						return false;
					}
				);
		$("#comm_dialog_edit_cancel_button").click(function () { 
						$('#comm_dialog_edit').dialog("close");
						return false;
					}
				);
				
		$("#comm_dialog_edit").dialog({
				bgiframe: true, autoOpen: true,
				modal: true, resizable: false,
				draggable: false,
				width: 800, //(document.documentElement.clientWidth * 0.8);
				height: (document.documentElement.clientHeight * 0.8),
				close: function() {
							$(this).dialog('destroy');
							$('#comm_dialog_edit').remove();
							/* Run callbacks */
							if(comm_dialog_edit_saved == true) {
								if(typeof(comm_dialog_edit_save) == 'function') {
									comm_dialog_edit_save(<?=$emails_id?>);
								}
							} else {
								if(typeof(comm_dialog_edit_cancel) == 'function') {
									comm_dialog_edit_cancel();
								}
							}
							if(typeof(refreshEmailList) == 'function') {
								refreshEmailList();
							}

						}
				});

		$("#comm_dialog_insert_field").click(function () { 
						var oFCKeditor = FCKeditorAPI.GetInstance('bodyhtml') ;
						var value = oFCKeditor.GetHTML();
						oFCKeditor.InsertHtml("["+this.value+"]");
						return false;
					}
				);


		var oFCKeditor = new FCKeditor( 'bodyhtml' ) ;
		oFCKeditor.BasePath = "../fckeditor/" ;
		oFCKeditor.ToolbarSet = 'sfiab';
		oFCKeditor.Width="100%";
		oFCKeditor.Height=300;
//		$('#fck').html(oFCKeditor.CreateHtml());
		oFCKeditor.ReplaceTextarea() ;
	</script>
	<?
	exit;


case 'dialog_send':
	?>
	<div id="comm_dialog_send" title="Send Communication" style="display: none">
	<?
	$fcid=intval($_GET['fundraising_campaigns_id']);
	$emailid=intval($_GET['emails_id']);

	$fcq=mysql_query("SELECT * FROM fundraising_campaigns WHERE id='$fcid'");
	$fc=mysql_fetch_object($fcq);

	$emailq=mysql_query("SELECT * FROM emails WHERE id='$emailid'");
	$email=mysql_fetch_object($emailq);

	?>
	<form id="send">
	<table style="width:100%">
	<?
	$q=mysql_query("SELECT COUNT(*) AS num FROM fundraising_campaigns_users_link WHERE fundraising_campaigns_id='$fcid'");
	$r=mysql_fetch_object($q);
	$numrecipients=$r->num;

	echo "<tr><td>".i18n("Appeal")."</td><td>".$fc->name." - ".i18n(ucfirst($email->val))."</td></tr>\n";
	echo "<tr><td>".i18n("From")."</td><td>".htmlspecialchars($email->from)."</td></tr>\n";
	echo "<tr><td>".i18n("Subject")."</td><td>".htmlspecialchars($email->subject)."</td></tr>\n";
	echo "<tr><td>".i18n("Recipients")."</td><td>".$numrecipients."</td></tr>\n";
	?>
	</table>
	<hr />
	<div id="comm_dialog_send_info">
	<?
	if($numrecipients>0) {
		echo i18n("Please confirm you wish to send this email to %1 recipients.  Clicking the Send button below will begin sending the emails immediately.",array($numrecipients));
		echo "<br />\n";
		echo "<br />\n";
		echo "<input class=\"comm_dialog_send_send_button\"  type=\"submit\" value=\"".i18n('Send')."\" />\n";
	}
	else {
		echo i18n("You have not selected any recipients on the Prospects tab.  Press Cancel and click on Prospects to add recipients");
		echo "<br />\n";
		echo "<br />\n";
	}
	?>
	<input class="comm_dialog_send_cancel_button" type="submit" value="<?=i18n('Cancel')?>" >
	</div>
	<div id="comm_dialog_send_processing" style="display: none;">
	<?=i18n("Please wait while the email queue is initialized...")?>
	<br />
	<img src="../images/ajax-loader.gif">
	</div>
	<div id="comm_dialog_send_status" style="display: none;">
	<?=i18n("The email has been queued to send");?>
	<br /><br /><input class="comm_dialog_send_status_button" type="submit" value="<?=i18n('Close and view sending status')?>" >
	<input class="comm_dialog_send_close_button" type="submit" value="<?=i18n('Close and continue')?>" >
	</div>
	</form>
	</div>
	<script type="text/javascript">
		var comm_dialog_choose_selected = -1;
		$(".comm_dialog_send_send_button").click(function () { 
				$("#comm_dialog_send_info").hide();
				$("#comm_dialog_send_processing").show();
				$.post("communication.php?action=sendqueue",{fundraising_campaigns_id: <?=$fcid?>, emails_id: <?=$emailid?>}, function() {
					$("#comm_dialog_send_processing").hide();
					$("#comm_dialog_send_status").show();
				});
			//			$('#comm_dialog_send').dialog("close");
				return false;
		});

		$(".comm_dialog_send_cancel_button").click(function () { 
						$('#comm_dialog_send').dialog("close");
						return false;
					});
	
		$(".comm_dialog_send_close_button").click(function () { 
						$('#comm_dialog_send').dialog("close");
						return false;
					});
	
		$(".comm_dialog_send_status_button").click(function () { 
						$('#comm_dialog_send').dialog("close");
						window.location.href="communication_send_status.php";
						return false;
					});
	
		$("#comm_dialog_send").dialog({
				bgiframe: true, autoOpen: true,
				modal: true, resizable: false,
				draggable: false,
				width: 600, //(document.documentElement.clientWidth * 0.8);
				close: function() {
						$(this).dialog('destroy');
						$('#comm_dialog_send').remove();
						/* Run callbacks */
						if(typeof(update_tab_communications) == 'function') {
							update_tab_communications();
						}
					}
				});

	</script>
	<?
	exit;


//dialog_sender is used to send a one-off communication based on a given template to a given user
//receives 'uid' and an optional 'template'
case 'dialog_sender':
	$u=user_load_by_uid(intval($_GET['uid']));

	if($_GET['template']) {
		$emailq=mysql_query("SELECT * FROM emails WHERE `val`='".mysql_real_escape_string($_GET['template'])."'");
		$e=mysql_fetch_assoc($emailq);
	}
	else
		$e=null;

	$from=htmlspecialchars($_SESSION['name']." <".$_SESSION['email'].">");
	$to=htmlspecialchars($u['emailrecipient']);
	$subject = htmlspecialchars($e['subject']);

	//useless but we might as well have it
	$name = htmlspecialchars($e['name']);
	$key = htmlspecialchars($e['val']);
	$description = htmlspecialchars($e['description']);

	//do the replacements from the template now, so what the person see's is what gets sent.
	$body = communication_replace_vars($e['body'],$u);
	$bodyhtml = communication_replace_vars($e['bodyhtml'],$u);

	//if there's no html,. grab the html from the non-html version
	if($bodyhtml == '') $bodyhtml = nl2br($body);
	?>
	<div id="comm_dialog_sender" title="Send an Email" style="display: none">
	<br />
	<form id="comm_dialog_sender_form">
	<? /* ="fcid=$fcid, key=$key, type=$type"*/ ?>

	<table class="editor" style="width:95%">
	<?
	if($e) {
		echo "<tr><td class=\"label\">".i18n("Using Template").":</td><td class=\"input\"><a href=\"communication.php?action=edit&val=$key\">$name (".i18n("click to edit template").")</a></td></tr>\n";
		echo "<tr><td colspan=\"2\"><hr /></td></tr>\n";
	}
	?>
	<tr>
		<td class="label"><?=i18n("From")?>:</td>
		<td class="input"><input type="text" name="from" size="60" value="<?=$from?>" /></td>
	</tr><tr>
		<td class="label"><?=i18n("To")?>:</td>
		<td class="input"><input type="text" name="to" size="60" value="<?=$to?>" /></td>
	</tr><tr>
		<td class="label"><?=i18n("Subject")?>:</td>
		<td class="input"><input type="text" name="subject" size="60" value="<?=$subject?>" /></td>
	</tr><tr>
		<td colspan="2" class="input">
			<div id="fck">
			<textarea id="bodyhtml" name="bodyhtml" rows=6 cols=80><?=$bodyhtml?></textarea>
			</div>
		</td>
	</tr></table>
	<hr />
	<div align="right">
		<input type="submit" id="comm_dialog_sender_send_button" value="<?=i18n('Send')?>" />
		<input type="submit" id="comm_dialog_sender_cancel_button" value="<?=i18n('Cancel')?>" />
	</div>
	</form>
	</div>
	<script type="text/javascript" src="<?=$config['SFIABDIRECTORY']?>/fckeditor/fckeditor.js"></script>
	<script type="text/javascript">
		$("#comm_dialog_sender_send_button").click(function () { 
						var oFCKeditor = FCKeditorAPI.GetInstance('bodyhtml') ;
						var value = oFCKeditor.GetHTML();
						$('#bodyhtml').val(value);
						$("#debug").load("<?=$config['SFIABDIRECTORY']?>/admin/communication.php?action=email_send", $("#comm_dialog_sender_form").serializeArray(),
									function() {
										$('#comm_dialog_sender').dialog("close");
									});
						return false;
					}
				);
		$("#comm_dialog_sender_cancel_button").click(function () { 
						$('#comm_dialog_sender').dialog("close");
						return false;
					}
				);

		$("#comm_dialog_sender").dialog({
				bgiframe: true, autoOpen: true,
				modal: true, resizable: false,
				draggable: false,
				width: 800, //(document.documentElement.clientWidth * 0.8);
				close: function() {
							$(this).dialog('destroy');
							$('#comm_dialog_sender').remove();
							/* Run callbacks */
						}
				});

		var oFCKeditor = new FCKeditor( 'bodyhtml' ) ;
		oFCKeditor.BasePath = "../fckeditor/" ;
		oFCKeditor.ToolbarSet = 'sfiab';
		oFCKeditor.Width="100%";
		oFCKeditor.Height=300;
//		$('#fck').html(oFCKeditor.CreateHtml());
		oFCKeditor.ReplaceTextarea() ;
	</script>
	<?
	exit;

case "email_send":
		$body=getTextFromHtml($_POST['bodyhtml']);
		email_send_new(stripslashes($_POST['to']),stripslashes($_POST['from']),stripslashes($_POST['subject']),stripslashes($body),stripslashes($_POST['bodyhtml']));
		happy_("Email Successfully Sent");
	exit;

case "email_get_list":

	 $q=mysql_query("SELECT * FROM emails ORDER BY type,name");
	 echo "<table class=\"tableview\">";
	 echo "<thead><tr>";
	 echo " <th>".i18n("Name")."</th>";
	 echo " <th>".i18n("Type")."</th>";
	 echo " <th>".i18n("Actions")."</th>";
	 echo "</tr></thead>";
	 while($r=mysql_fetch_object($q)) {
		if($r->fundraising_campaigns_id) $fcid=$r->fundraising_campaigns_id;
		else $fcid='null';
		if($r->name) $name=$r->name;
		else $name=i18n("no email name specified");

		echo "<tr><td><a href=\"#\" onclick=\"return opencommunicationeditor('".addslashes($r->val)."',$r->id,$fcid)\">",htmlspecialchars($name)."</a></td>";
		echo "<td>$r->type</td>";

		echo " <td align=\"center\">";
		//only user emails can be deleted, system ones are required and cannot be removed
		if($r->type=="user") {
			echo "&nbsp;";
			echo "<a onclick=\"return confirmClick('Are you sure you want to remove email?')\" href=\"communication.php?action=delete&delete=$r->id\"><img border=0 src=\"".$config['SFIABDIRECTORY']."/images/16/button_cancel.".$config['icon_extension']."\"></a>";
			echo "&nbsp;";
			echo "<a href=\"communication.php?action=send&send=$r->id\">".i18n("Send")."</a>";
		}
		echo " </td>\n";
		echo "</tr>";
	 }
	 echo "</table>";
	 exit;

	 case 'cancel':
	 if($_GET['cancel']) {
		mysql_query("UPDATE emailqueue SET finished=NOW() WHERE id='".intval($_GET['cancel'])."'");
		mysql_query("UPDATE emailqueue_recipients SET result='cancelled' WHERE emailqueue_id='".intval($_GET['cancel'])."' AND sent IS NULL AND result IS NULL");
		echo "ok";
	 }
	 exit;

	case 'loadaddresses':
		if($_GET['query'] && array_key_exists($_GET['query'],$mailqueries)) {
			$q=mysql_query($mailqueries[$_GET['query']]['query']);
			while($r=mysql_fetch_object($q)) {
				if($r->organization) $s="($r->organization) ";
				else $s="";
				echo "$r->firstname $r->lastname {$s}&lt;$r->email&gt;<br />";
			}
		}

	exit;
}


 if($_GET['action']=="sendqueue") {
	$fcid=intval($_POST['fundraising_campaigns_id']);
	$emailid=intval($_POST['emails_id']);

	$fcq=mysql_query("SELECT * FROM fundraising_campaigns WHERE id='$fcid'");
	$fc=mysql_fetch_object($fcq);

	$emailq=mysql_query("SELECT * FROM emails WHERE id='$emailid'");
	$email=mysql_fetch_object($emailq);

	$recipq=mysql_query("SELECT * FROM fundraising_campaigns_users_link
						WHERE fundraising_campaigns_id='$fcid'");
	echo mysql_error();

	$numtotal=mysql_num_rows($recipq);
	mysql_query("INSERT INTO emailqueue (val,name,users_uid,`from`,subject,body,bodyhtml,`type`,fundraising_campaigns_id,started,finished,numtotal,numsent) VALUES (
			'".mysql_real_escape_string($email->val)."',
			'".mysql_real_escape_string($email->name)."',
			'".$_SESSION['users_uid']."',
			'".mysql_real_escape_string($email->from)."',
			'".mysql_real_escape_string($email->subject)."',
			'".mysql_real_escape_string($email->body)."',
			'".mysql_real_escape_string($email->bodyhtml)."',
			'".mysql_real_escape_string($email->type)."',
			$fcid,
			NOW(),
			NULL,
			$numtotal,
			0)");
	$emailqueueid=mysql_insert_id();
	echo mysql_error();

	$urlproto = $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
	$urlmain = "$urlproto{$_SERVER['HTTP_HOST']}{$config['SFIABDIRECTORY']}";
	$urllogin = "$urlmain/login.php";
	while($r=mysql_fetch_object($recipq)) {
		$u=user_load_by_uid($r->users_uid);

		//we only send school access codes to science heads or principals
		$acq=mysql_query("SELECT accesscode FROM schools WHERE (sciencehead_uid='{$u['uid']}' OR principal_uid='{$u['uid']}') AND `year`='{$config['FAIRYEAR']}'");
		$acr=mysql_fetch_object($acq);
		$accesscode=$acr->accesscode;

		$replacements=array(
					"FAIRNAME"=>$config['fairname'],
					"SALUTATION"=>$u['salutation'],
					"FIRSTNAME"=>$u['firstname'],
					"LASTNAME"=>$u['lastname'],
					"NAME"=>$u['name'],
					"EMAIL"=>$u['email'],
					"ORGANIZATION"=>$u['sponsor']['organization'],
					"URLMAIN"=>$urlmain,
					"URLLOGIN"=>$urllogin,
					"ACCESSCODE"=>$accesscode,
					);

		if($u['email'] && $u['email'][0] != '*') {
			mysql_query("INSERT INTO emailqueue_recipients (emailqueue_id,toemail,toname,replacements,sent) VALUES (
				'$emailqueueid',
				'".mysql_real_escape_string($u['email'])."',
				'".mysql_real_escape_string($u['name'])."',
				'".mysql_real_escape_string(json_encode($replacements))."',
				NULL)");
			echo mysql_error();
		}
		mysql_query("UPDATE emails SET lastsent=NOW() WHERE id='$emailid'");
	}
	echo "ok";
	launchQueue();
	exit;

 }
 send_header("Communication",
 		array('Committee Main' => 'committee_main.php',
			'Administration' => 'admin/index.php'),
            "communication"
			);
 echo "<br />";
 ?>
 <script type="text/javascript">
 function toggleAddresses() {
	 if($("#toaddresses").is(":visible")) {
		 $("#toaddresses").hide();
		 $("#toaddresses-view").html("Show Recipients");
	 } else {
		 $("#toaddresses").show();
		 $("#toaddresses-view").html("Hide Recipients");
	 }
	 return false;
 }
 function loadAddresses() {
	 $("#toaddresses").load("communication.php?action=loadaddresses&query="+$("#to").val());
 }
 </script>
 <?

 if($_GET['action']=="delete" && $_GET['delete']) {
 	mysql_query("DELETE FROM emails WHERE id='".$_GET['delete']."' AND `type`='user'");
	echo happy("Email successfully deleted");
 }

	if($_GET['action']=="send" && $_GET['send']) {
		echo mysql_error();
		$q=mysql_query("SELECT * FROM emails WHERE id='".$_GET['send']."'");
		$r=mysql_fetch_object($q);

		echo i18n("Please confirm you would like to send the following email, and choose who to send it to");
		echo "<br>";
		echo "<br>";
		echo "<form method=\"post\" action=\"communication.php\">";
		echo "<table cellspacing=0 cellpadding=3 border=1>";
		echo "<tr><td><b>From:</b></td><td>".htmlspecialchars($r->from)."</td></tr>";
		echo "<tr><td><b>To:</b></td><td>";
		echo "<select name=\"to\" id=\"to\" onchange=\"loadAddresses();\">";
		echo " <option value=\"\">Choose Email Recipients</option>";
		$str="";
		foreach($mailqueries AS $k=>$mq) {
			$tq=mysql_query($mq['query']);
            if(mysql_error()) {
                echo mysql_error();
                exit;
            }
			$num=mysql_num_rows($tq);
			$str.="<h2>".$mq['name']." $num </h2>";
			while($tr=mysql_fetch_object($tq)) {
				$str.="[".$tr->uid."][".$tr->year."] ".$tr->firstname." ".$tr->lastname." &lt;{$tr->email}&gt;<br />";
			}
			echo " <option value=\"$k\">".i18n($mq['name'])." (".i18n("%1 recipients",array($num),array("number")).")</option>";
		}
		echo "</select>";
		echo "<div id=\"toaddresses-view-wrapper\"><a href=\"#\" onclick=\"return toggleAddresses()\"><span id=\"toaddresses-view\">View Recipients</span></a></div>";
		echo "<div id=\"toaddresses\" style=\"width: 100%; height: 300px; overflow: auto; border: 1px solid grey; background-color: #FFFFFF; display: none;\">empty</div>";
		echo "</td></tr>";
		echo "<tr><td><b>Date:</b></td><td>".date("r")."</td></tr>";
		echo "<tr><td><b>Subject:</b></td><td>".htmlspecialchars($r->subject)."</td></tr>";
		if($r->bodyhtml) {
			$body=$r->bodyhtml;
		}
		else {
			$body=nl2br(htmlspecialchars($r->body));
		}

		echo "<tr><td colspan=2>".$body."<br />(".mb_detect_encoding($body).")</td></tr>";

		echo "</table>";

		if(!function_exists("exec")) {
			echo "<div class=\"error\">Sending requires php's exec() function to be available</div>\n";
		}
		else {
		echo "<table border=0 cellspacing=0 cellpadding=30 width=\"100%\">";
		echo "<tr><td align=center>";
		echo "<input type=hidden name=action value=\"reallysend\">";
		echo "<input type=hidden name=reallysend value=\"".$_GET['send']."\">";
		echo "<input type=submit value=\"Yes, Send Email\">";
		echo "</form>";
		echo "</td><td>";
		echo "<form method=get action=\"communication.php\">";
		echo "<input type=submit value=\"No, Do Not Send\">";
		echo "</form>";
		echo "</td></tr>";
		echo "</table>";
		}
		//echo $str;
	}
	else if($_POST['action']=="reallysend" && $_POST['reallysend'] && $_POST['to']) {
		$emailid=intval($_POST['reallysend']);
		$emailq=mysql_query("SELECT * FROM emails WHERE id='$emailid'");
		$email=mysql_fetch_object($emailq);
		$to=$_POST['to'];

		if(array_key_exists($to,$mailqueries)) {
				$recipq=mysql_query($mailqueries[$to]['query']);
		}

		$numtotal=mysql_num_rows($recipq);
		mysql_query("INSERT INTO emailqueue (val,name,users_uid,`from`,subject,body,bodyhtml,`type`,fundraising_campaigns_id,started,finished,numtotal,numsent) VALUES (
				'".mysql_real_escape_string($email->val)."',
				'".mysql_real_escape_string($email->name)."',
				'".$_SESSION['users_uid']."',
				'".mysql_real_escape_string($email->from)."',
				'".mysql_real_escape_string($email->subject)."',
				'".mysql_real_escape_string($email->body)."',
				'".mysql_real_escape_string($email->bodyhtml)."',
				'".mysql_real_escape_string($email->type)."',
				NULL,
				NOW(),
				NULL,
				$numtotal,
				0)");
		$emailqueueid=mysql_insert_id();
		echo mysql_error();

		$urlproto = $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
		$urlmain = "$urlproto{$_SERVER['HTTP_HOST']}{$config['SFIABDIRECTORY']}";
		$urllogin = "$urlmain/login.php";

		while($r=mysql_fetch_object($recipq)) {
			if($r->uid)
				$u=user_load_by_uid($r->uid);
			else if($r->users_uid) 
				$u=user_load_by_uid($r->users_uid);
			else {
				$toname=$r->firstname." ".$r->lastname;
				$toemail=$r->email;

				$replacements=array(
							"FAIRNAME"=>$config['fairname'],
							"FIRSTNAME"=>$r->firstname,
							"LASTNAME"=>$r->lastname,
							"NAME"=>$r->firstname." ".$r->lastname,
							"EMAIL"=>$r->email,
							"ORGANIZATION"=>$r->organization,
							"URLMAIN"=>$urlmain,
							"URLLOGIN"=>$urllogin,
							"ACCESSCODE"=>"unknown",
							);
			}
			if($u) {

				//we only send school access codes to science heads or principals
				$acq=mysql_query("SELECT accesscode FROM schools WHERE (sciencehead_uid='{$u['uid']}' OR principal_uid='{$u['uid']}') AND `year`='{$config['FAIRYEAR']}'");
				echo mysql_error();
				$acr=mysql_fetch_object($acq);
				$accesscode=$acr->accesscode;

				$replacements=array(
							"FAIRNAME"=>$config['fairname'],
							"SALUTATION"=>$u['salutation'],
							"FIRSTNAME"=>$u['firstname'],
							"LASTNAME"=>$u['lastname'],
							"NAME"=>$u['name'],
							"EMAIL"=>$u['email'],
							"ORGANIZATION"=>$u['sponsor']['organization'],
							"URLMAIN"=>$urlmain,
							"URLLOGIN"=>$urllogin,
							"ACCESSCODE"=>$accesscode,
							);

				$toname=$u['name'];
				$toemail=$u['email'];
			}

			if($toemail) {
				mysql_query("INSERT INTO emailqueue_recipients (emailqueue_id,toemail,toname,replacements,sent) VALUES (
					'$emailqueueid',
					'".mysql_real_escape_string($toemail)."',
					'".mysql_real_escape_string($toname)."',
					'".mysql_real_escape_string(json_encode($replacements))."',
					NULL)");
				echo mysql_error();
			}
			mysql_query("UPDATE emails SET lastsent=NOW() WHERE id='$emailid'");
		}
		launchQueue();
		echo "<br />";
		echo happy("Email Communication sending has started!");
		echo "<br>";
		echo "<a href=\"communication_send_status.php\">Click here to see the sending progress</a>";

	}
	else if($_GET['action']=="restartqueue")
	{
		launchQueue();
		echo "<br />";
		echo happy("Email Communication sending has started!");
		echo "<br>";
		echo "<a href=\"communication_send_status.php\">Click here to see the sending progress</a>";
	}
 	else {
		if(!$config['fairmanageremail'])
			echo notice(i18n("Warning: The 'Fair Manager Email' has not been set in SFIAB Configuration / Configuration Variables / Global.  Please set it.  The 'Fair Manager Email' is the default 'From' address for all emails and without a 'From' address, no emails can be sent!"));

		 echo "<a href=\"communication_send_status.php\">".i18n("Email Queue Status and History")."</a><br />";
		 echo "<a href=\"#\" onclick=\"return opencommunicationeditor(null,null,null)\">".i18n("Add New Email")."</a>";
		 echo "<br />\n";
		 echo "<br />\n";
		 echo "<div id=\"emaillist\"></div>";
		 ?>
		<script type="text/javascript">
		function refreshEmailList() {
			$("#emaillist").load("communication.php?action=email_get_list",null,function(){
				$('.tableview').tablesorter();
			});

		}
		$(document).ready(function() {
			refreshEmailList();
		}
		);
		</script>
		 <?
	 }

	send_footer();
?>
