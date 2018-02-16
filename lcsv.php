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
class lcsv
{
	var $csvdata;
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

	function addTable($table)
	{
		if($table['header'])
		{
			$table_cols=count($table['header']);
			for($c=0;$c<$table_cols;$c++)
			{
				$head=$table['header'][$c];
				$this->csvdata.="\"".$head."\"";
				if($c<$table_cols-1)
					$this->csvdata.=$this->separator();
			}
			$this->csvdata.=$this->newline();
		}
		else
		{
			//is this right ?
			$table_cols=count($table['data'][0]);
		}

		//now do the data in the table
		if($table['data']) {
			foreach($table['data'] AS $dataline) {
				for($c=0;$c<$table_cols;$c++) {
					//escape a single " with ""
					$dataline_c=str_replace('"','""',$dataline[$c]);

					//lets always quote it
					$this->csvdata.="\"".$dataline_c."\"";

					if($c<$table_cols-1)
						$this->csvdata.=$this->separator();
				}
				$this->csvdata.=$this->newline();
			}
		}
	}

	function heading($str)
	{
		//we need to put it in quotes incase it contains a comma we dont want it going to the next 'cell'
		$this->csvdata.="\"".$str."\"";
		$this->csvdata.=$this->newline();
	}

	function addText($str,$align="")
	{
		//we need to put it in quotes incase it contains a comma we dont want it going to the next 'cell'
		$this->csvdata.="\"".$str."\"";
		$this->csvdata.=$this->newline();
	}

	function addTextX($str,$align="")
	{
		//we need to put it in quotes incase it contains a comma we dont want it going to the next 'cell'
		$this->csvdata.="\"".$str."\",";
	}

	function setFontBold()
	{
	}

	function setFontNormal()
	{
	}

	function nextline()
	{
		$this->csvdata.=$this->newline();

	}

	function newPage()
	{
		//well we cant really go to a new page, so in teh absense of a new page, lets put a few blank lines in?
		$this->csvdata.=$this->newline();
		$this->csvdata.=$this->newline();
		$this->csvdata.=$this->newline();
	}

	function hr()
	{
		// what are we supposed to do.. nothing I guess?  blank line?  
	}

	function vspace()
	{
		// do nothing
	}


	function output()
	{
		if($this->csvdata)
		{
			$filename=strtolower($this->page_subheader);
			$filename=ereg_replace("[^a-z0-9]","_",$filename);
			//header("Content-type: application/csv");
			header("Content-type: text/x-csv");
			header("Content-disposition: inline; filename=sfiab_".$filename.".csv");
			header("Content-length: ".strlen($this->csvdata));
			header("Pragma: public");
			echo $this->csvdata;
		}
	}

	function lcsv($subheader,$sep=",",$nl="\r\n")
	{
		$this->page_subheader=$subheader;
		$this->setSeparator($sep);
		$this->setNewline($nl);

	}
}
?>
