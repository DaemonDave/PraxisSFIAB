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
class ltxt
{
	var $txtdata;
	var $str_separator;
	var $str_newline;
	var $page_subheader;

	function separator()
	{
		return $this->str_separator;
	}
	function setSeparator($s)
	{
		$this->str_separator=$s;
	}


	function newline()
	{
		return $this->str_newline;
	}
	function setNewline($s)
	{
		$this->str_newline=$s;
	}

	function centerText($text, $width)
	{
		// FIXME, there must be an easier way of doing this, 
		// probably with printf :)
		$t = "";
		$space = $width - strlen($text);
		$w2 = floor($space / 2);
		for($x=0; $x<$w2; $x++) $t .= ' ';
		$t .= $text;
		$w2 = $space - $w2;
		for($x=0; $x<$w2; $x++) $t .= ' ';
		return $t;
	}
	function leftText($text, $width)
	{
		$t = "";
		$t .= $text;
		for($x=strlen($text); $x<$width; $x++) $t .= ' ';
		return $t;
	}
	function dashText($width) 
	{
		$t='';
		for($x=0; $x<$width; $x++) $t .= '-';
		return $t;
	}

	function addTable($table)
	{
		$widths = array();

		if($table['header']) {
			$table_cols=count($table['header']);
		} else {
			$table_cols=count($table['data'][0]);
		}

		/* Find the width of each column */
		if($table['header']) {
			for($c=0;$c<$table_cols;$c++) {
				$w = strlen($table['header'][$c]);
				if($w > $widths[$c]) $widths[$c] = $w;
			}
		}
		foreach($table['data'] AS $dataline) {
			for($c=0;$c<$table_cols;$c++) {
				$w = strlen($dataline[$c]);
				if($w > $widths[$c]) $widths[$c] = $w;
			}
		}



		if($table['header']) {
			$this->txtdata.=$this->str_separator;
			for($c=0;$c<$table_cols;$c++) {
				$head = $this->centerText($table['header'][$c], $widths[$c]);
				$this->txtdata.=$head;
				$this->txtdata.=$this->str_separator;
			}
			$this->txtdata.=$this->newline();
			$this->txtdata.=$this->str_separator;
			for($c=0;$c<$table_cols;$c++) {
				$this->txtdata.=$this->dashText($widths[$c]);
				$this->txtdata.=$this->str_separator;
			}
			$this->txtdata.=$this->newline();

		}

		//now do the data in the table
		if($table['data']) {
			foreach($table['data'] AS $dataline) {
				$this->txtdata.=$this->str_separator;
				for($c=0;$c<$table_cols;$c++) {
					$d = $this->leftText($dataline[$c], $widths[$c]);
					$this->txtdata.=$d;
					$this->txtdata.=$this->str_separator;
				}
				$this->txtdata.=$this->newline();
			}
		}
	}

	function heading($str)
	{
		//we need to put it in quotes incase it contains a comma we dont want it going to the next 'cell'
		$this->txtdata.="\"".$str."\"";
		$this->txtdata.=$this->newline();
	}

	function addText($str,$align="")
	{
		//we need to put it in quotes incase it contains a comma we dont want it going to the next 'cell'
		$this->txtdata.="\"".$str."\"";
		$this->txtdata.=$this->newline();
	}

	function nextline()
	{
		$this->txtdata.=$this->newline();

	}

	function newPage()
	{
		//well we cant really go to a new page, so in teh absense of a new page, lets put a few blank lines in?
//		$this->txtdata.=$this->newline();
//		$this->txtdata.=$this->newline();
//		$this->txtdata.=$this->newline();
	}


	function output()
	{
		if($this->txtdata)
		{
			$filename=strtolower($this->page_subheader);
			$filename=ereg_replace("[^a-z0-9]","_",$filename);
			//header("Content-type: application/csv");
			header("Content-type: text/plain");
			header("Content-disposition: inline; filename=sfiab_".$filename.".txt");
			header("Content-length: ".strlen($this->txtdata));
			echo $this->txtdata;
		}
	}

	function ltxt($subheader,$sep=" | ",$nl="\r\n")
	{
		$this->page_subheader=$subheader;
		$this->setSeparator($sep);
		$this->setNewline($nl);

	}
}
?>
