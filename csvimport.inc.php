<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2006 Sci-Tech Ontario Inc <info@scitechontario.org>
   Copyright (C) 2006 James Grant <james@lightbox.org>

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
class CSVParser
{
var $delimiter;         // Field delimiter
var $enclosure;         // Field enclosure character
var $data;              // CSV data

function CSVParser()
{
	$this->delimiter = ",";
	$this->enclosure = '"';
	$this->data = array();
}

function parseFile($filename)
{
	$content = file_get_contents($filename);
	$content = str_replace( "\r\n", "\n", $content );
	$content = str_replace( "\r", "\n", $content );
	if($content[strlen($content)-1] != "\n")   // Make sure it always end with a newline
		$content .= "\n";

	// Parse the content character by character
	$row = array( "" );
	$idx = 0;
	$quoted = false;
	for ( $i = 0; $i < strlen($content); $i++ )
	{
		$ch = $content[$i];
		if ($ch == $this->enclosure)
			$quoted = !$quoted;

		// End of line
		if ($ch=="\n" && !$quoted)
		{
			// Remove enclosure delimiters
			for ($k=0;$k<count($row);$k++ )
			{
				if($row[$k]!="" && $row[$k][0]==$this->enclosure )
				{
					$row[$k] = substr($row[$k], 1, strlen($row[$k]) - 2);
				}
				$row[$k] = str_replace(str_repeat($this->enclosure, 2), $this->enclosure, $row[$k]);
			}

			// Append row into table
			$this->data[] = $row;
			$row = array( "" );
			$idx = 0;
		}

		// End of field
		else if ($ch==$this->delimiter && !$quoted)
			$row[++$idx]="";

		// Inside the field
		else
			$row[$idx].=$ch;
	}
	return true;
}

}

?>
