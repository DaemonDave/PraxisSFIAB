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
require_once('../common.inc.php');
require_once('../user.inc.php');
user_auth_required('committee', 'config');
send_header("Fair Logo Image",
 		array('Committee Main' => 'committee_main.php',
			'SFIAB Configuration' => 'config/index.php'),
            "images");

if($_POST['action']=="addimage") {
	if($_FILES['image']['error']==UPLOAD_ERR_OK) {
		//make sure its a JPEG
		$imagesize=getimagesize($_FILES['image']['tmp_name']);
		if($imagesize[2]==1 || $imagesize[2]==2 || $imagesize[2]==3)  // GIF or JPG or PNG
		{

			/* Here's how to do it with GD, if GD didn't absolutely suck at
			 * resizing an image (thought I'd try it again, Mar30, 2010, still sucks).
			$image_data = file_get_contents($_FILES['image']['tmp_name']);
			$image = imagecreatefromstring($image_data);

			$w = imagesx($image);
			$h = imagesy($image);

			$ratio = $h / $w;
			$image100 = imagecreate(100, $ratio * 100);
			imagecopyresampled($image100, $image, 0, 0, 0, 0, 100, $ratio * 100, $w, $h);
			imagejpeg($image100, "../data/logo-100.jpg");*/


			echo notice(i18n("Creating sized logo files:<br />&nbsp;logo-100.gif<br />&nbsp;logo-200.gif<br />&nbsp;logo-500.gif<br />&nbsp;logo.gif"));
			//Make the gif's
			system("convert -resize 100 \"".$_FILES['image']['tmp_name']."\" ../data/logo-100.gif");
			system("convert -resize 200 \"".$_FILES['image']['tmp_name']."\" ../data/logo-200.gif");
			system("convert -resize 500 \"".$_FILES['image']['tmp_name']."\" ../data/logo-500.gif");
			system("convert \"".$_FILES['image']['tmp_name']."\" ../data/logo.gif");

			if(file_exists("../data/logo-100.gif") && file_exists("../data/logo-200.gif") && file_exists("../data/logo-500.gif") &&  file_exists("../data/logo.gif"))
				echo happy(i18n("GIF Images successfully created"));
			else
				echo error(i18n("Error creating GIF Image files.  Make sure 'convert' binary is in your path, and that 'system' function can be used"));
				
			echo notice(i18n("Creating sized logo files:<br />&nbsp;logo-100.png<br />&nbsp;logo-200.png<br />&nbsp;logo-500.png<br />&nbsp;logo.png"));
			//make some PNG's as well
			system("convert -resize 100 \"".$_FILES['image']['tmp_name']."\" ../data/logo-100.png");
			system("convert -resize 200 \"".$_FILES['image']['tmp_name']."\" ../data/logo-200.png");
			system("convert -resize 500 \"".$_FILES['image']['tmp_name']."\" ../data/logo-500.png");
			system("convert \"".$_FILES['image']['tmp_name']."\" ../data/logo.png");

			if(file_exists("../data/logo-100.png") && file_exists("../data/logo-200.png") && file_exists("../data/logo-500.png") &&  file_exists("../data/logo.png"))
				echo happy(i18n("PNG Images successfully created"));
			else
				echo error(i18n("Error creating PNG Image files.  Make sure 'convert' binary is in your path, and that 'system' function can be used"));

			echo notice(i18n("Creating sized logo files:<br />&nbsp;logo-100.jpg<br />&nbsp;logo-200.jpg<br />&nbsp;logo-500.jpg<br />&nbsp;logo.jpg"));
			//make some PNG's as well
			system("convert -resize 100 \"".$_FILES['image']['tmp_name']."\" ../data/logo-100.jpg");
			system("convert -resize 200 \"".$_FILES['image']['tmp_name']."\" ../data/logo-200.jpg");
			system("convert -resize 500 \"".$_FILES['image']['tmp_name']."\" ../data/logo-500.jpg");
			system("convert \"".$_FILES['image']['tmp_name']."\" ../data/logo.jpg");

			if(file_exists("../data/logo-100.jpg") && file_exists("../data/logo-200.jpg") && file_exists("../data/logo-500.jpg") &&  file_exists("../data/logo.jpg"))
				echo happy(i18n("JPG Images successfully created"));
			else
				echo error(i18n("Error creating JPG Image files.  Make sure 'convert' binary is in your path, and that 'system' function can be used"));
		}
		else
		{
			echo error(i18n("Logo Image must be JPG, GIF or PNG"));
		}
	}
	else
		echo error(i18n("Error uploading Logo Image").": ".$_FILES['image']['error']);
}

if($_POST['action']=="delimage") {
	@unlink("../data/logo.gif");
	@unlink("../data/logo-100.gif");
	@unlink("../data/logo-200.gif");
	@unlink("../data/logo-500.gif");
	@unlink("../data/logo.png");
	@unlink("../data/logo-100.png");
	@unlink("../data/logo-200.png");
	@unlink("../data/logo-500.png");
	@unlink("../data/logo.jpg");
	@unlink("../data/logo-100.jpg");
	@unlink("../data/logo-200.jpg");
	@unlink("../data/logo-500.jpg");
	
	echo happy(i18n("Deleted any existing logo files"));
}



 echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"images.php\">";
 echo "<input type=\"hidden\" name=\"action\" value=\"addimage\">\n";
 echo "<table>";
 if(file_exists("../data/logo.gif"))
 {
 	echo "<tr><td colspan=\"2\">".i18n("GIF Images")."</td></tr>";
 	echo "<tr><td>";
	echo "<img src=\"../data/logo-100.gif\" border=\"0\">";
	echo "</td><td>";
	echo "<a target=\"_blank\" href=\"../data/logo.gif\">",i18n("Original size")."</a><br />";
	echo "<a target=\"_blank\" href=\"../data/logo-100.gif\">".i18n("100 Pixel width")."</a><br />";
	echo "<a target=\"_blank\" href=\"../data/logo-200.gif\">".i18n("200 Pixel width")."</a><br />";
	echo "<a target=\"_blank\" href=\"../data/logo-500.gif\">500 Pixel width</a><br />";
	echo "</td></tr>";
 }
 if(file_exists("../data/logo.png"))
 {
 	echo "<tr><td colspan=\"2\">".i18n("PNG Images")."</td></tr>";
 	echo "<tr><td>";
	echo "<img src=\"../data/logo-100.png\" border=\"0\">";
	echo "</td><td>";
	echo "<a target=\"_blank\" href=\"../data/logo.png\">",i18n("Original size")."</a><br />";
	echo "<a target=\"_blank\" href=\"../data/logo-100.png\">".i18n("100 Pixel width")."</a><br />";
	echo "<a target=\"_blank\" href=\"../data/logo-200.png\">".i18n("200 Pixel width")."</a><br />";
	echo "<a target=\"_blank\" href=\"../data/logo-500.png\">500 Pixel width</a><br />";
	echo "</td></tr>";
 }
 if(file_exists("../data/logo.jpg"))
 {
 	echo "<tr><td colspan=\"2\">".i18n("JPG Images")."</td></tr>";
 	echo "<tr><td>";
	echo "<img src=\"../data/logo-100.jpg\" border=\"0\">";
	echo "</td><td>";
	echo "<a target=\"_blank\" href=\"../data/logo.jpg\">",i18n("Original size")."</a><br />";
	echo "<a target=\"_blank\" href=\"../data/logo-100.jpg\">".i18n("100 Pixel width")."</a><br />";
	echo "<a target=\"_blank\" href=\"../data/logo-200.jpg\">".i18n("200 Pixel width")."</a><br />";
	echo "<a target=\"_blank\" href=\"../data/logo-500.jpg\">500 Pixel width</a><br />";
	echo "</td></tr>";
 }
 echo "<tr><td colspan=2>";
 echo "<input type=\"file\" name=\"image\">";
 echo "<input type=\"submit\" value=\"".i18n("Upload Logo")."\" />\n";
 echo "</td></tr>";
 echo "</table>";
 echo "</form>";

if( file_exists("../data/logo.gif") || file_exists("../data/logo.png")  || file_exists("../data/logo.jpg")) {
 echo "<br />";
 echo "<form enctype=\"multipart/form-data\" method=\"post\" action=\"images.php\">";
 echo "<input type=\"hidden\" name=\"action\" value=\"delimage\">\n";
 echo "<input type=\"submit\" value=\"".i18n("Delete Logo")."\" />\n";
 echo "</form>";
}

 send_footer();
?>
