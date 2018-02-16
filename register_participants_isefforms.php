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
 require("common.inc.php");
 include "register_participants.inc.php";
 
 //authenticate based on email address and registration number from the SESSION
 if(!$_SESSION['email'])
 {
 	header("Location: register_participants.php");
	exit;
 }
 if(!$_SESSION['registration_number'])
 {
 	header("Location: register_participants.php");
	exit;
 }

 $q=mysql_query("SELECT registrations.id AS regid, students.id AS studentid, students.firstname FROM registrations,students ".
 	"WHERE students.email='".$_SESSION['email']."' ".
	"AND registrations.num='".$_SESSION['registration_number']."' ". 
	"AND registrations.id='".$_SESSION['registration_id']."' ".
	"AND students.registrations_id=registrations.id ".
	"AND registrations.year=".$config['FAIRYEAR']." ".
	"AND students.year=".$config['FAIRYEAR']);
echo mysql_error();

 if(mysql_num_rows($q)==0)
 {
 	header("Location: register_participants.php");
	exit;
 
 }
 $authinfo=mysql_fetch_object($q);

 //send the header
 send_header("Participant Registration - ISEF Forms");

 echo "<a href=\"register_participants_main.php\">&lt;&lt; ".i18n("Back to Participant Registration Summary")."</a><br />";
 echo "<br />";

	if($_POST['action']=="save")
	{
		//do the "No" responses first
		if(count($_POST['yesno']))
		{
			foreach($_POST['yesno'] AS $k=>$v)
			{
				//always delete it, because if we had previousy selected "no" then we change it to "yes" we want the record to be removed
				//because it will be added below by the _FILES, and if its not added there then that means we just said yes and didnt upload anything
				//so removing it makes it go all red again so you are aware

				mysql_query("DELETE FROM TC_ProjectForms WHERE ProjectID='$r->id' AND FormID='$k' AND `year`='$CURRENT_FAIRYEAR'");

				//just look at hte first letter, since its either   "no:<id>" or "yes:<id>";
				if($v[0]=="n")
				{
					mysql_query("INSERT INTO TC_ProjectForms (`FormID`,`ProjectID`,`uploaded`,`dt`,`year`) VALUES (
						
						'$k',
						'$r->id',
						'0',
						NOW(),
						'$CURRENT_FAIRYEAR'
					)");

				}


			}
		}

		if(!file_exists($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR))
			mkdir($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR);

		if(!file_exists($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/".$r->id))
			mkdir($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/".$r->id);


		if(is_array($_FILES['form']))
			$keys=array_keys($_FILES['form']['name']);
		else
			$keys=null;
		if(is_array($keys))
		{
			foreach($keys AS $k)
			{
				if($_FILES['form']['error'][$k]==UPLOAD_ERR_OK) //==0
				{
				//	echo "Processing $k: ".$_FILES['form']['name'][$k];

					//make sure its a PDF, not just a file renamed to PDF extension
					$fp=fopen($_FILES['form']['tmp_name'][$k],"r");
					$firstline=fgets($fp,512);
					fclose($fp);
					if(substr($firstline,0,4)=="%PDF")
					{
						//make sure the year folder exists
						$display_happy[]=i18n("PDF form [%1] detected, accepting upload",array($_FILES['form']['name'][$k]));
						move_uploaded_file($_FILES['form']['tmp_name'][$k],$TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/$r->id/$k.pdf");

						$pdfinfo=exec("pdfinfo ".$TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/$r->id/$k.pdf |grep \"Pages\"");
						list($pgtext,$pgs)=split(":",$pdfinfo);
						$pgs=trim($pgs);
						if($pgs) $p="'$pgs'";
						else $p="null";

						mysql_query("DELETE FROM TC_ProjectForms WHERE ProjectID='$r->id' AND FormID='$k' AND `year`='$CURRENT_FAIRYEAR'");
						mysql_query("INSERT INTO TC_ProjectForms (`FormID`,`ProjectID`,`uploaded`,`filename`,`pages`,`dt`,`year`) VALUES (
							'$k',
							'$r->id',
							'1',
							'".mysql_escape_string($_FILES['form']['name'][$k])."',
							$p,
							NOW(),
							'$CURRENT_FAIRYEAR'
							)");

					}
					else
						$display_error[]=i18n("Could not save form [%1].  It's not a PDF!",array($_FILES['form']['name'][$k]));
				}
			}
		}
			/*
			if($_FILES['report']['error']==UPLOAD_ERR_OK)
			{
				//make sure its a PDF, not just a file renamed to PDF extension
				$fp=fopen($_FILES['report']['tmp_name'],"r");
				$firstline=fgets($fp,512);
				fclose($fp);
				if(substr($firstline,0,4)=="%PDF")
				{
					//make sure the year folder exists
					if(!file_exists($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR))
						mkdir($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR);

					$display_happy=i18n("PDF report detected, accepting upload");
					move_uploaded_file($_FILES['report']['tmp_name'],$TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/$r->id.pdf");
					
					//now figur out some other info about the PDF
					$pdfinfo=exec("pdfinfo ".$TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/$r->id.pdf |grep \"Pages\"");
					list($pgtext,$pgs)=split(":",$pdfinfo);
					$pgs=trim($pgs);
					if($pgs) $pgquery=", ReportPages='$pgs'";
					mysql_query("UPDATE TC_Projects SET ReportFile='".$r->id.".pdf' $pgquery WHERE id='".$r->id."'");
				}
				else
					$display_error=i18n("Could not save uploaded report.  It's not a PDF!");
			}
			*/
		$display_notice=i18n("Changes successfully saved");
	}


	if($_GET['action']=="delete" && $_GET['delete'])
	{
		//first we need to make sure that this is their own!
		$chq=mysql_query("SELECT * FROM TC_ProjectForms WHERE id='".$_GET['delete']."' AND ProjectID='$r->id' AND `year`='$CURRENT_FAIRYEAR'");
		if($chr=mysql_fetch_object($chq))
		{
			@unlink($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/$r->id/$chr->FormID.pdf");
			mysql_query("DELETE FROM TC_ProjectForms WHERE id='".$_GET['delete']."' AND ProjectID='$r->id' AND `year`='$CURRENT_FAIRYEAR'");
			$display_happy=i18n("Form successfully deleted");
		}
		else
		{
			$display_error=i18n("You do not have access to delete that form");
		}
	}

	send_header("ISEF Forms");
?>

<script type="text/javascript">
function radiochange(o)
{
//	alert(o.value);
	var v=o.value.split(":");
	
	var nodivobjname="nodiv"+v[1];
	var yesdivobjname="yesdiv"+v[1];

	var nodiv=document.getElementById(nodivobjname);
	var yesdiv=document.getElementById(yesdivobjname);

	if(v[0]=="no")
	{
		nodiv.style.display="";
		yesdiv.style.display="none";

	}
	else if(v[0]=="yes")
	{
		nodiv.style.display="none";
		yesdiv.style.display="";

	}

//	alert(divobjname);
}
</script>

<?
	echo "<form enctype=\"multipart/form-data\" name=\"tcforms\" method=\"post\" action=\"tcforms.php\">";
	echo "<input type=\"hidden\" name=\"action\" value=\"save\" />";
	//echo "<h3>".htmlspecialchars($r->ProjectTitle)."</h3>";
//	echo "<br />";
	echo i18n("Please use the ISEF Rules Wizard to determine which forms you need to upload");
	echo "<br />";
	//FIXME: this redirects to societyforscience.org somewhere, fix the url
	echo "<a href=\"http://www.sciserv.org/isef/students/wizard/index.asp\" target=\"_blank\">www.sciserv.org/isef/students/wizard/index.asp</a>";
	echo "<br />";
	echo "<br />";

	echo "<table class=\"tableedit\">";

/*
//FIXME: put this back in once hte table is created, took it out to avoid errors temporarily
	$ufq=mysql_query("SELECT * FROM TC_ProjectForms WHERE ProjectID='$r->id' AND `year`='".$CURRENT_FAIRYEAR."'");
	$uploadedforms=array();
	while($ufr=mysql_fetch_object($ufq))
	{
		$uploadedforms[$ufr->FormID]=$ufr;
	}
	*/

	$fq=mysql_query("SELECT * FROM isefforms ORDER BY name");

	echo "<tr>";
	echo "<th>".i18n("Form")."</th>";
	echo "<th>".i18n("Required?")."</th>";
	echo "<th></th>";
	echo "<th>".i18n("Upload / File")."</th>";
	echo "</tr>";

	while($fr=mysql_fetch_object($fq))
	{
		echo "<tr>";
		echo "<td valign=\"top\">";
		if($fr->filename) {
			echo "<a title=\"$fr->description\" href=\"tcforms/$CURRENT_FAIRYEAR/".rawurlencode($fr->filename)."\">";
		}
		echo $fr->name;
		if($fr->filename)
			echo "</a>";
		echo "<br />$fr->description</td>";
		echo "<td valign=\"top\">";
		if($fr->required=="Y")
			echo i18n("required");
		else
			echo i18n("optional");
		echo "</td>";
		echo "<td>";
		if($uploadedforms[$fr->id]->uploaded)
		{
			echo "<a title=\"".i18n("Delete this uploaded form")."\" onclick=\"return confirmClick('".i18n("Are you sure you want to delete this uploaded form?")."')\" href=\"tcforms.php?action=delete&delete={$uploadedforms[$fr->id]->id}\"><img src=\"/icons/16/button_cancel.$icon_extension\" border=0></a>";
		}
		echo "</td><td>";

	//	if(file_exists("$TCFORMSLOCATION/$CURRENT_FAIRYEAR/$r->id/$fr->id.pdf"))
		if($uploadedforms[$fr->id]->uploaded)
		{
			$uf=$uploadedforms[$fr->id];

		//	echo happy("<a href=\"tcformdownload.php?id=$uf->id\">".htmlspecialchars($uf->filename)."</a>",true);
			echo "<a href=\"tcformdownload.php?id=$uf->id\">".htmlspecialchars($uf->filename)."</a>";
		}
		else
		{
			if($fr->required=="Y")
				echo error(i18n("No file uploaded"),true);

			if($fr->required=="N")
			{
				if($uploadedforms[$fr->id])
					echo "<div>";
				else
					echo "<div class=\"error\">";

				echo i18n("Upload this form?")." ";
				if($uploadedforms[$fr->id] && !$uploadedforms[$fr->id]->uploaded)
				{
					$ch="checked=\"checked\""; 
					$nodivdisplay="";
				}
				else
				{
					$nodivdisplay="none";
					$ch="";
				}
				echo "<nobr>";
				echo "<input $ch onchange=\"radiochange(this)\" type=\"radio\" name=\"yesno[$fr->id]\" value=\"no:$fr->id\">No ";
				echo "<input onchange=\"radiochange(this)\" type=\"radio\" name=\"yesno[$fr->id]\" value=\"yes:$fr->id\">Yes ";
				echo "</nobr>";
				echo "</div>";
				echo "<div style=\"display: none;\" id=\"yesdiv$fr->id\">";
				echo "<input type=\"file\" name=\"form[$fr->id]\">";
				echo "</div>";
				echo "<div style=\"display: $nodivdisplay;\" id=\"nodiv$fr->id\">";
				echo i18n("I do not need to upload this form");
				echo "</div>";
			}
			else
			{
				echo "<input type=\"file\" name=\"form[$fr->id]\">";

			}
		}

		echo "</td>";

		echo "</tr>";
//	echo "<tr><td colspan=\"4\"></td></tr>";

	}

/*
	if(file_exists($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/$r->id.pdf"))
	{
		echo i18n("Your report has been uploaded.  Use the link below to download your report for confirmation, or you may re-upload your report by using the 'Re-Upload Report' file selector below");
		echo "<br />";
		echo "<br />";
		echo "<table class=\"tableedit\"><tr><td valign=\"top\">";
		echo "<a href=\"tcreportdownloader.php?id=$r->id\">".i18n("Download Report")."</a></td><td>";
		echo i18n("Filesize: %1 bytes",array(filesize($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/$r->id.pdf")))."<br />";
		echo i18n("Uploaded: %1",array(date("Y-m-d H:i:s",filemtime($TCFORMSLOCATION."/".$CURRENT_FAIRYEAR."/$r->id.pdf"))))."<br />";
		echo i18n("Pages: %1",array($r->ReportPages))."<br />";
		echo "</td></tr></table>";
		$uploadtext="Re-Upload Report";
		echo "<br />";
	}
	else
	{
		echo i18n("Please upload your new project report (max 6MB).  It must be a PDF file.");
		$uploadtext="Upload Report";
	}
	*/

	echo "<tr><td colspan=\"4\">";
	echo i18n("After selecting your files, you must click 'Save and Upload Forms' at the bottom of this page to finish uploading the selected forms.  Please be patient as it might take a few minutes depending on the speed of your internet connection");
	echo "</td></tr>";

	echo "<tr><td colspan=\"4\">&nbsp;</td></tr>";

	echo "</table>";
	echo "<input type=\"submit\" value=\"".i18n("Save and Upload Forms")."\">";
	echo "</form>";
send_footer();
?>
