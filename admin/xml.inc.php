<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2007 James Grant <james@lightbox.org>

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

function xmlCreateRecurse($d)
{
	global $indent;
	global $output;
	foreach($d AS $key=>$val)
	{
		if(is_numeric($key))
		{ 
			if($val['xml_type'])
			{
				for($x=0;$x<$indent;$x++) $output.=" ";
				$output.="<".$val['xml_type'].">\n";
				$indent++;
				xmlCreateRecurse($val);
				$indent--;
				for($x=0;$x<$indent;$x++) $output.=" ";
				$output.="</".$val['xml_type'].">\n";
			}
			else
			{
				for($x=0;$x<$indent;$x++) $output.=" ";
				$output.="<$key>\n";
				$indent++;
				xmlCreateRecurse($val);
				$indent--;
				for($x=0;$x<$indent;$x++) $output.=" ";
				$output.="</$key>\n";

			}
		}
		else if(is_array($val))
		{
			for($x=0;$x<$indent;$x++) $output.=" ";
				$output.="<$key>\n";
			$indent++;
			xmlCreateRecurse($val);
			$indent--;
			for($x=0;$x<$indent;$x++) $output.=" ";
				$output.="</$key>\n";
		}
		else
		{
			if($key!="xml_type" && $key!="projectid" && $key!="projectdivisions_id")
			{
				for($x=0;$x<$indent;$x++) $output.=" ";
				$output.="<$key>$val</$key>\n";
			}
		}
	}
}

 # Mainfunction to parse the XML defined by URL
 function xml_parsexml ($String) {
  $Encoding=xml_encoding($String);
  $String=xml_deleteelements($String,"?");
  $String=xml_deleteelements($String,"!");
  $Data=xml_readxml($String,$Data,$Encoding);
  return($Data);
 }
 
 # Get encoding of xml
 function xml_encoding($String) {
  if(substr_count($String,"<?xml")) {
   $Start=strpos($String,"<?xml")+5;
   $End=strpos($String,">",$Start);
   $Content=substr($String,$Start,$End-$Start);
   $EncodingStart=strpos($Content,"encoding=\"")+10;
   $EncodingEnd=strpos($Content,"\"",$EncodingStart);
   $Encoding=substr($Content,$EncodingStart,$EncodingEnd-$EncodingStart);
  }else {
   $Encoding="";
  }
  return $Encoding;
 }
 
 # Delete elements
 function xml_deleteelements($String,$Char) {
  while(substr_count($String,"<$Char")) {
   $Start=strpos($String,"<$Char");
   $End=strpos($String,">",$Start+1)+1;
   $String=substr($String,0,$Start).substr($String,$End);
  }
  return $String;
 }
 
 # Read XML and transform into array
 function xml_readxml($String,$Data,$Encoding='') {
  while($Node=xml_nextnode($String)) {
   $TmpData="";
   $Start=strpos($String,">",strpos($String,"<$Node"))+1;
   $End=strpos($String,"</$Node>",$Start);
   $ThisContent=trim(substr($String,$Start,$End-$Start));
   $String=trim(substr($String,$End+strlen($Node)+3));
   if(substr_count($ThisContent,"<")) {
    $TmpData=xml_readxml($ThisContent,$TmpData,$Encoding);
    $Data[$Node][]=$TmpData;
   }else {
    if($Encoding=="UTF-8") { $ThisContent=utf8_decode($ThisContent); }
    $ThisContent=str_replace("&gt;",">",$ThisContent);
    $ThisContent=str_replace("&lt;","<",$ThisContent);
    $ThisContent=str_replace("&quote;","\"",$ThisContent);
    $ThisContent=str_replace("&#39;","'",$ThisContent);
    $ThisContent=str_replace("&amp;","&",$ThisContent);
    $Data[$Node][]=$ThisContent;
   }
  }
  return $Data;
 }
 
 # Get next node
 function xml_nextnode($String) {
  if(substr_count($String,"<") != substr_count($String,"/>")) {
   $Start=strpos($String,"<")+1;
   while(substr($String,$Start,1)=="/") {
    if(substr_count($String,"<")) { return ""; }
    $Start=strpos($String,"<",$Start)+1;
   }
   $End=strpos($String,">",$Start);
   $Node=substr($String,$Start,$End-$Start);
   if($Node[strlen($Node)-1]=="/") {
    $String=substr($String,$End+1);
    $Node=xml_nextnode($String);
   }else {
    if(substr_count($Node," ")){ $Node=substr($Node,0,strpos($String," ",$Start)-$Start); }
   }
  }
  return $Node;
 }


?>
