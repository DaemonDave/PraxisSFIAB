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
class lpdf
{
	var $pdf;
	var $yloc=10.25;
	var $page_header;
	var $page_subheader;
	var $pagenumber;
	var $logoimage;

	var $page_style="normal";

	var $page_margin=0.75;
	//these are defaults, they get overwritten with the first call to newPage(width,height)
	var $page_width=8.5;
	var $page_height=11;

	//all of these are overwritten by setLabelDimensions(width,height,xspacer,yspacer);
	var $label_width=4;
	var $label_height=2, $label_effective_height=0;
	var $label_xspacer=0.125;
	var $label_yspacer=0.125;
	var $labels_per_row=2;
	var $labels_per_column=5;
	var $labels_per_page=10;

	var $labels_start_xpos;
	var $labels_start_ypos;

	var $current_label_index=0;
	var $current_label_col_index=0;
	var $current_label_row_index=1;

	var $currentFontSize=12;
	var $defaultFontSize=10;

	var $normalfont;
	var $boldfont;

	function loc($inch)
	{
		return $inch*72;
	}


	function addHeaderAndFooterToPage()
	{
		//The title of the fair
		$this->yloc=$this->page_height-$this->page_margin;
		$height['title']=0.25;
		$height['subtitle']=0.22;

		pdf_setfont($this->pdf,$this->headerfont,18);
		pdf_show_boxed($this->pdf,$this->page_header,$this->loc($this->page_margin),$this->loc($this->yloc),$this->loc($this->content_width),$this->loc($height['title']),"center",null);
		$this->yloc-=$height['title'];

		pdf_setfont($this->pdf,$this->headerfont,14);
		pdf_show_boxed($this->pdf,$this->page_subheader,$this->loc($this->page_margin),$this->loc($this->yloc),$this->loc($this->content_width),$this->loc($height['subtitle']),"center",null);
		$this->yloc-=$height['subtitle'];

		//only put the logo on the page if we actually have the logo
		if($this->logoimage)
		{
			/* now place the logo image in the top-left-ish
			 * within a box width=height=0.70, fit to the box, and
			 * center in the box */
			$w = $this->loc(0.70);
			pdf_fit_image($this->pdf, $this->logoimage, 
				$this->loc($this->page_margin),
				$this->loc($this->yloc+.02),
				"boxsize { $w $w} position {50 50} fitmethod meet" ) ;
		}

		//header line
		pdf_moveto($this->pdf,$this->loc($this->page_margin-0.25),$this->loc($this->yloc));
		pdf_lineto($this->pdf,$this->loc($this->page_width-$this->page_margin+0.25),$this->loc($this->yloc));
		pdf_stroke($this->pdf);
		$this->yloc-=0.20;

		//now put a nice little footer at the bottom
		$footertext=date("Y-m-d h:ia")." - ".$this->page_header." - ".$this->page_subheader;

		$footerwidth=pdf_stringwidth($this->pdf,$footertext,$this->normalfont,9);
		pdf_setfont($this->pdf,$this->normalfont,9);
		pdf_show_xy($this->pdf,$footertext,$this->loc($this->page_width/2)-$footerwidth/2,$this->loc(0.5));

		pdf_setfont($this->pdf,$this->normalfont,11);
		pdf_show_xy($this->pdf,($this->pagenumber),$this->loc($this->page_width - 0.5),$this->loc(0.5));

		//footer line
		pdf_moveto($this->pdf,$this->loc($this->page_margin-0.25),$this->loc($this->page_margin-0.05));
		pdf_lineto($this->pdf,$this->loc($this->page_width-$this->page_margin+0.25),$this->loc($this->page_margin-0.05));
		pdf_stroke($this->pdf);
	}

	function newPage($width="",$height="",$pagenumber=0)
	{
		if($width && $height)
		{
			$this->page_width=$width;
			$this->page_height=$height;
			$this->content_width=$width-(2*$this->page_margin);
		}

		if($this->pagenumber>0)
			pdf_end_page($this->pdf);
		$this->pagenumber = ($pagenumber == 0) ? ($this->pagenumber + 1) : $pagenumber;

		//Letter size (8.5 x 11) is 612,792
		pdf_begin_page($this->pdf,$this->loc($this->page_width),$this->loc($this->page_height));
		pdf_setlinewidth($this->pdf,0.3);

		if($this->page_style=="normal")
		{
			$this->addHeaderAndFooterToPage();
			//make sure we set the font back to whatever it used to be
			//because adding header/footer changes the fontsize
			$this->setFontSize($this->currentFontSize);
		}
	}

	function vspace($space)
	{
		$this->yloc-=$space;
	}

	function setDefaultFontSize($size) {
		$this->defaultFontSize=$size;
	}

	function setFontSize($size)
	{
		$this->currentFontSize=$size;
		pdf_setfont($this->pdf,$this->normalfont,$size);
		$leading=round($size*1.3);
 		pdf_set_value($this->pdf,"leading",$leading);
	}

	function setFontBold()
	{
		pdf_setfont($this->pdf, $this->boldfont, $this->currentFontSize);
	}

	function setFontNormal()
	{
		pdf_setfont($this->pdf, $this->normalfont, $this->currentFontSize);
	}


	function addText($text,$align="left", $xloc=0, $displayfont="normalfont")
	{
		$fontsize=pdf_get_value($this->pdf,"fontsize",0);
		$lineheight=ceil($fontsize*1.3);
		//the line height should be 1.2 * fontsize (approx)
		$stringwidth=pdf_stringwidth($this->pdf,$text,$this->$displayfont,$fontsize);

		$textstr=$text;

		if($xloc == 0) {
			$xloc = $this->page_margin;
			$content_width = $this->content_width;
		} else {
			$content_width = $this->content_width - $xloc;
		}

		$nr=0;
		$prevnr=-1;
		do
		{
			//echo "textstr=$textstr";
			$len=strlen($textstr);
//			echo "(lh:$lineheight nr:$nr len:$len)".$textstr;

			$nl=false;
			//now lets handle a newline at the beginning, just rip it off and move the yloc ourself
			while($textstr[0]=="\n")
			{
				$textstr=substr($textstr,1);
				$this->yloc-=$lineheight/72;
				$nl=true;
			}

			if($nl == false) $this->yloc-=$lineheight/72;
		

	 		$nr=pdf_show_boxed($this->pdf,$textstr, $this->loc($xloc),$this->loc($this->yloc),$this->loc($content_width),$lineheight,$align,null);
			if($this->yloc< (0.9 + $lineheight/72) )
				$this->newPage();

			if($nr==$prevnr)
			{
//Comment this out, so if it ever does happen, the PDF will still generate, it just might be missing a small blurb somewhere, better than no PDF at all			
//				echo "breaking because nr==prevnr ($nr==$prevnr) trying to output [$textstr] (debug: fontsize=$fontsize, lineheight=$lineheight, stringwidth=$stringwidth, left=".$this->loc(0.75).", top=".$this->loc($this->yloc).", width=".$this->loc(7).", height=$lineheight)\n";
				break;
			}

			$prevnr=$nr;
//			printf("x=%f y=%f w=%f h=%f",$this->loc(0.75),$this->loc($this->yloc),$this->loc(7),$lineheight);
//			echo "$nr didnt fit";
//			echo "<br>doing: substr($textstr,-$nr) <br>";
			$textstr=substr($textstr,-$nr);
//			echo  "nr=$nr";
		} while($nr>0);

//		pdf_rect($this->pdf,$this->loc(0.75),$this->loc($this->yloc),$this->loc(7),$height);

	}

	function mailingLabel($to,$co,$address,$city,$province,$postalcode)
	{
		$this->setFontSize($this->currentFontSize);
		//mailing addresses are all uppercase, left aligned
		//see http://www.canadapost.ca/tools/pg/standards/cps1-05-e.asp
		$tmpY=0.1;

		$fontsize=pdf_get_value($this->pdf,"fontsize",0);
		$lineheight=ceil($fontsize*1.2);

		//this is to make sure if the name of the school goes onto two lines we handle it properly
		$l=$this->addLabelText($tmpY,mb_strtoupper(trim($to)));
		$tmpY+=($lineheight/72)*$l;
		if($co)
		{
			$l=$this->addLabelText($tmpY,trim(mb_strtoupper($co)));
			$tmpY+=($lineheight/72)*$l;
		}
		if($address[strlen($address)-1]==".") $address=substr($address,0,-1);
		$l=$this->addLabelText($tmpY,mb_strtoupper(trim($address)));
		$tmpY+=($lineheight/72)*$l;

		if(strlen($postalcode)==6) $pc=substr($postalcode,0,3)." ".substr($postalcode,3,3); else $pc=$postalcode;

		$this->addLabelText($tmpY,trim(mb_strtoupper("$city $province  $pc")));

	}

	function addLabelText($Y,$text,$align="left")
	{
		$this->setFontSize($this->currentFontSize);
		$fontsize=pdf_get_value($this->pdf,"fontsize",0);
		$lineheight=ceil($fontsize*1.35);
		$linemove=ceil($fontsize*1.2);

//		echo "fontsize=$fontsize lineheight=$lineheight";

		$textstr=$text;

		$texty=$this->label_current_ypos-$Y;

		$nr=0;
		$prevnr=-1;
		$numlines=0;
		do
		{
			$len=strlen($textstr);

			$nl=false;
			//now lets handle a newline at the beginning, just rip it off and move the yloc ourself
			while($textstr[0]=="\n")
			{
				$textstr=substr($textstr,1);
				$texty-=$linemove/72;
				$nl=true;
			}
			if(!$nl)
				$texty-=$linemove/72;

	 		$nr=pdf_show_boxed($this->pdf,$textstr, $this->loc($this->label_current_xpos+0.20),$this->loc($texty),$this->loc($this->label_width-0.3),$lineheight,$align,null);
			if($nr==$len)
			{
				$texty+=$linemove/72;
				//okay so it really doesnt fit. so lets just keep shortening it until it does!
				$textstr=substr($textstr,0,-1);
			}
			else
			{
				$textstr=substr($textstr,-$nr);
				$numlines++;
			}
			$prevnr=$nr;
		} while($nr>0);


		return $numlines;
	}

	function addLabelText2($xp,$yp,$wp,$hp,$lh,$text,$options) 
	{
//		print("$xp,$yp,$wp,$hp,$lh,$text,$options,$this->label_width");
		/* Some assumptions:
			- we will scale the font instead of doing line wrapping
			- all coords are given in percentages of the label 
			- a width or height of 0 means "don't care"
			- xp or yp can also be 'center', meaning center the text */

		if($xp == 'center') {
			if($wp == 0) $wp = 100;
			$xp = 50 - ($wp / 2);
		}
		if($yp == 'center') {
			if($hp == 0) $hp = 100;
			$yp = 50 - ($hp / 2);
		}
		$xpos = ($xp * $this->label_width) /100;
		$ypos = ($yp * $this->label_effective_height) / 100;

		$desired_width = ($this->label_width * $wp) / 100;
		$desired_height = ($this->label_effective_height * $hp) / 100;

		/* Pick a font */
		if(in_array('bold', $options)) {
			$font = $this->boldfont;
		} else {
			$font = $this->normalfont;
		}

		$align = 'left';
		$valign = 'top';
		$boxtext = false;
		if(in_array('left', $options)) $align = 'left';
		if(in_array('right', $options)) $align = 'right';
		if(in_array('center', $options)) $align = 'center';
		if(in_array('vtop', $options)) $valign = 'top';
		if(in_array('vcenter', $options)) $valign = 'center';
		if(in_array('vbottom', $options)) $valign = 'bottom';
		if(in_array('field_box', $options)) $boxtext = true;


		/* Find the correct font size for the lineheight */
		if($lh == 0) $lh = $hp;
		$desired_line_height = ($this->label_effective_height * $lh) / 100;
		$desired_line_height_loc = $desired_line_height * 72;

//		print("Desired line height=[$desired_line_height => $desired_line_height_loc]");

		$fontpt = intval($desired_line_height_loc);

		pdf_setfont($this->pdf, $font, $fontpt);


	//		print("$xpos, $ypos x $desired_width, $desired_height<br>");

		if($boxtext == true) {
			pdf_rect($this->pdf,
				$this->loc($this->label_current_xpos + $xpos),
				$this->loc($this->label_current_ypos - ($ypos + $desired_height)),
				$this->loc($desired_width),
				$this->loc($desired_height));
			pdf_stroke($this->pdf);
		}

		$x = $this->label_current_xpos + $xpos;
		$y = $this->label_current_ypos - ($ypos + $desired_height);

		
		$lines = 1;
 		$nr=pdf_show_boxed($this->pdf, $text, 
			$this->loc($x), $this->loc($y),
			$this->loc($desired_width), $this->loc($desired_line_height),
			$align,'blind');
			$prevnr=$nr;
		while($nr > 0) {
 		$nr=pdf_show_boxed($this->pdf, substr($text, -$nr), 
			$this->loc($x), $this->loc($y),
			$this->loc($desired_width), $this->loc($desired_line_height),
			$align,'blind');
			$lines ++;
			if($nr==$prevnr) break;
			$prevnr=$nr;

		}
		
		/* Now adjust the ypos, and do it for real */
		if($valign == 'top') {
			$y = $this->label_current_ypos - ($ypos + $desired_line_height);
		} else if($valign == 'center') {
			$extra = ($desired_height - ($lines * $desired_line_height)) / 2;
			$y = $this->label_current_ypos - ($ypos + $desired_line_height) - $extra;
		} else {
			echo "Unimplemented valign [$valign]";
			exit();
		}
 		$nr=pdf_show_boxed($this->pdf, $text, 
			$this->loc($x), $this->loc($y),
			$this->loc($desired_width), $this->loc($desired_line_height),
			$align,null);
			$prevnr=$nr;
		while($nr > 0) {
			$y -= $desired_line_height;
	 		$nr=pdf_show_boxed($this->pdf, substr($text, -$nr), 
				$this->loc($x), $this->loc($y),
				$this->loc($desired_width), $this->loc($desired_line_height),
				$align,null);
			if($nr==$prevnr) break;
			$prevnr=$nr;
		}
	}

	function addLabelBox($xp,$yp,$wp,$hp) 
	{
		$xpos = ($xp * $this->label_width) /100;
		$ypos = ($yp * $this->label_effective_height) / 100;

		$desired_width = ($this->label_width * $wp) / 100;
		$desired_height = ($this->label_effective_height * $hp) / 100;

		pdf_rect($this->pdf,
			$this->loc($this->label_current_xpos + $xpos),
			$this->loc($this->label_current_ypos - ($ypos + $desired_height)),
			$this->loc($desired_width),
			$this->loc($desired_height));
		pdf_stroke($this->pdf);
	}


	function newLabel($show_box=false, $show_fairname=false, $show_logo=false)
	{
		if($this->current_label_index==$this->labels_per_page)
		{

			$this->newPage();
			$this->current_label_index=1;
			$this->current_label_col_index=0;
			$this->current_label_row_index=1;
		}
		else
		{
			$this->current_label_index++;
		}

		if($this->current_label_col_index==$this->labels_per_row)
		{

			$this->current_label_col_index=1;
			$this->current_label_row_index++;
		}
		else
		{
			$this->current_label_col_index++;
		}

		$this->label_current_ypos=$this->labels_start_ypos-(($this->current_label_row_index-1)*($this->label_height + $this->label_yspacer))-$this->label_height;
		$this->label_current_xpos=$this->labels_start_xpos+(($this->current_label_col_index-1)*($this->label_width + $this->label_xspacer));

		if($show_box == true) {
			pdf_rect($this->pdf,
				$this->loc($this->label_current_xpos),
				$this->loc($this->label_current_ypos),
				$this->loc($this->label_width),
				$this->loc($this->label_height));
			pdf_stroke($this->pdf);
		}
		$this->label_current_ypos+=$this->label_height;//-0.15;

		//only put the logo on the label if we actually have the logo

		if($show_logo == true && $this->logoimage)
		{
			/* now place the logo image in the top-left-ish
			 * within a box width=height=0.70, fit to the box, and
			 * center in the box */
			$w = $this->loc(0.70);
			pdf_fit_image($this->pdf, $this->logoimage, 
				$this->loc($this->label_current_xpos+0.05),
				$this->loc($this->label_current_ypos-0.75),
				"boxsize { $w $w} position {50 50} fitmethod meet" ) ;
		}

		$this->label_effective_height = $this->label_height;
		if($show_fairname) {
			$height['title']=0.50;
			$this->label_current_ypos -= $height['title'];
			$this->label_effective_height -= $height['title'];

			pdf_setfont($this->pdf,$this->headerfont,13);
			pdf_show_boxed($this->pdf,$this->page_header,
					$this->loc($this->label_current_xpos+0.65),
					$this->loc($this->label_current_ypos-0.15),
					$this->loc($this->label_width-0.70),
					$this->loc($height['title']),
					"center",
					null);
		} 
	}

	function addTextX($text,$xpos)
	{
		$fontsize=pdf_get_value($this->pdf,"fontsize",0);
		$lineheight=ceil($fontsize*1.2);

		//we do it before here, to make sure we never get too low
		if($this->yloc< (0.9 + $lineheight/72) )
			$this->newPage();
		pdf_show_xy($this->pdf,$text,$this->loc($xpos),$this->loc($this->yloc));
	}

	function stringWidth($text, $font=null, $size=null)
	{
		if($size == null) $size = pdf_get_value($this->pdf,"fontsize",0);
		if($font == null) $font = $this->normalfont;
		return pdf_stringwidth($this->pdf, $text, $font, $size);
	}

	function nextLine()
	{
		$fontsize=pdf_get_value($this->pdf,"fontsize",0);
		$lineheight=ceil($fontsize*1.2);

		$this->yloc-=$this->currentFontSize*1.4/72;

		//new page check can come after the nextline call
		if($this->yloc< (0.9 + $lineheight/72) )
			$this->newPage();

	}

	function prevLine()
	{
		$fontsize=pdf_get_value($this->pdf,"fontsize",0);
		$lineheight=ceil($fontsize*1.2);

		$this->yloc+=$this->currentFontSize*1.4/72;
		
	}

	function hr()
	{
		$fontsize=pdf_get_value($this->pdf,"fontsize",0);
		$lineheight=ceil($fontsize*1.2);

		pdf_moveto($this->pdf,$this->loc($this->page_margin-0.25),$this->loc($this->yloc));
		pdf_lineto($this->pdf,$this->loc($this->page_width-$this->page_margin+0.25),$this->loc($this->yloc));
		pdf_stroke($this->pdf);
		$this->yloc-=0.25;

		//again we do it after the nextline call
		if($this->yloc< (0.9 + $lineheight/72) )
			$this->newPage();
	}

	function hline($x1,$x2)
	{
		pdf_moveto($this->pdf,$this->loc($x1),$this->loc($this->yloc));
		pdf_lineto($this->pdf,$this->loc($x2),$this->loc($this->yloc));
		pdf_stroke($this->pdf);
	}

	function heading($text)
	{
		//if we are close to the bottom, lets just move the whole heading to the next page.
		//no point putting the heading here then the new text on the next page.

		//12/72 is height of the heading
		//4/72 is the space under the heading
		if($this->yloc< (1.1 + 12/72 + 4/72) )
			$this->newPage();

		pdf_setfont($this->pdf,$this->headerfont, round($this->defaultFontSize*1.2));
		$this->addText($text,"left",0,"headerfont");
		pdf_setfont($this->pdf,$this->normalfont,$this->currentFontSize);
		//now leave some space under the heading (4 is 1/3 of 12, so 1/3 of the line height we leave)
		$this->yloc-=4/72;

	}

 
 	function addTableStart(&$table, $xpos_of_table, $table_width)
	{
		if(is_array($table['header'])) {
			$table_cols=count($table['header']);
			$height_header=round(round($this->defaultFontSize*1.2)/50,2);
		} else {
			$table_cols=count($table['data']);
			$height_header=0;
		}

		$this->yloc-=$height_header;
		$top_of_table=$this->yloc;

		//draw the top line of the table (above the table header)
		pdf_moveto($this->pdf,$this->loc($xpos_of_table),$this->loc($this->yloc+$height_header));
		pdf_lineto($this->pdf,$this->loc($xpos_of_table+$table_width),$this->loc($this->yloc+$height_header));
		pdf_stroke($this->pdf);

		//do the header first
		if(is_array($table['header']))
		{
			//draw the top line of the table (below the table header)
			pdf_moveto($this->pdf,$this->loc($xpos_of_table),$this->loc($this->yloc));
			pdf_lineto($this->pdf,$this->loc($xpos_of_table+$table_width),$this->loc($this->yloc));
			pdf_stroke($this->pdf);

			$xpos=$xpos_of_table;
			pdf_setfont($this->pdf,$this->headerfont,round($this->defaultFontSize*1.2));

			for($c=0;$c<$table_cols;$c++)
			{
				$head=$table['header'][$c];
				$width=$table['widths'][$c];

				pdf_show_boxed($this->pdf,$head,$this->loc($xpos),$this->loc($this->yloc),$this->loc($width),$this->loc($height_header),"center",null);
				$xpos+=$width;
			}
			pdf_setfont($this->pdf,$this->normalfont,$this->defaultFontSize);
		}

		return $top_of_table;
	}

	function addTableEnd(&$table, $xpos_of_table, $top_of_table)
	{
		if(is_array($table['header'])) {
			$table_cols=count($table['header']);
			$height_header=round(round($this->defaultFontSize*1.2)/50,2);
		} else {
			$table_cols=count($table['data']);
			$height_header=0;
		}

		//now draw all the vertical lines
		$xpos=$xpos_of_table;
		for($c=0;$c<$table_cols;$c++)
		{
			$width=$table['widths'][$c];
			//draw the line below the table data)
			pdf_moveto($this->pdf,$this->loc($xpos),$this->loc($top_of_table+$height_header));
			pdf_lineto($this->pdf,$this->loc($xpos),$this->loc($this->yloc));
			pdf_stroke($this->pdf);
			$xpos+=$width;
		}

		//and the final line on the right side of the table:
		pdf_moveto($this->pdf,$this->loc($xpos),$this->loc($top_of_table+$height_header));
		pdf_lineto($this->pdf,$this->loc($xpos),$this->loc($this->yloc));
		pdf_stroke($this->pdf);
	}


	function addTable($table,$align="center")
	{
		//if we get a table passed in that doesnt look like a table (not an array) then just return doing nothing
		if(!is_array($table)) return;

		if(is_array($table['header'])) {
			$table_cols=count($table['header']);
		} else {
			$table_cols=count($table['data']);
		}
		$line_height=round(round($this->defaultFontSize)/64,2);

		$table_width=array_sum($table['widths']);
		$table_padding=0.03;
	
		$allow_multiline = false;
		if(is_array($table['option'])) {
			$allow_multiline = ($table['option']['allow_multiline'] == true) ? true : false;
		}

		switch($align)
		{
			case "center"; 	$xpos_of_table=($this->page_width-$table_width)/2; break;
			case "left"; 	$xpos_of_table=$this->page_margin; break;
			case "right"; 	$xpos_of_table=$this->page_width-$this->page_margin-$table_width; break;
		}

		$top_of_table = $this->addTableStart($table, $xpos_of_table, $table_width);

		//now do the data in the table
		if($table['data'])
		{
			pdf_setfont($this->pdf,$this->normalfont,$this->defaultFontSize);
			foreach($table['data'] AS $dataline)
			{
//				$this->yloc-=$line_height;
				$xpos=$xpos_of_table;

				/* Fit first */
				$col_width = array();
				$col_height = 1;
				for($c=0;$c<$table_cols;$c++)
				{
					$width=$table['widths'][$c];
					$textstr=trim($dataline[$c]);
					$try=0;
					$h = $col_height;
					$last_notfit = 0;

					while(1) {
//						echo "h=$h, width=$width, text=[$textstr]\n";
						$notfit=pdf_show_boxed($this->pdf,$textstr,
							$this->loc($xpos+$table_padding),$this->loc($this->yloc-($h)*$line_height),
							$this->loc($width-2*$table_padding),$this->loc($line_height*$h),
							$table['dataalign'][$c],'blind');
//					 	echo "  nofit=$notfit\n";

						/* It fits, break and do it for real */
						if($notfit == 0) break;

						/* If we're not allowed to use multiple lines, we're done. */ 
						if($allow_multiline == false) break; 

						if($last_notfit == $notfit) {
							/* Height was increased, but it didn't help the fit at all
							 * Try again up to 5 times. */
							if($try == 5) {
								/* Text in is the same as text out for 5 line increments,
								 * we're probably in an infinite loop.  So, instead
								 * of trying to just add vspace, fudge the hspace and
								 * restart */
								$h = 1;
								$width += 0.1;
								$try=0;
								continue;
							}
							$try++;
						} else {
							/* We found a line height that helped the fit */
							$try=0;
						}
						$last_notfit = $notfit;

						/* Increase the height and try again */
						$h++;
					}
					$col_width[$c] = $width;
					if($h > $col_height) $col_height = $h;
				}

				/* If this entry goes off the bottom of the
				 * page, start a new page, and then blindly
				 * dump this entry on it (but try to squeeze on
				 * as much as possible) */
				if($this->yloc - ($line_height * $col_height) < 0.75)
				{
					$this->addTableEnd($table, $xpos_of_table, $top_of_table);
					$this->newPage($this->page_width,$this->page_height);
					$top_of_table = $this->addTableStart($table, $xpos_of_table, $table_width);
				}

				/* Do it for real */
				for($c=0;$c<$table_cols;$c++)
				{
					$width = $col_width[$c];
					$h = $col_height * $line_height;
					$textstr=trim($dataline[$c]);

					$notfit = pdf_show_boxed($this->pdf,$textstr,
						$this->loc($xpos+$table_padding),$this->loc($this->yloc-$h),
						$this->loc($width-2*$table_padding),$this->loc($h),
						$table['dataalign'][$c],null);

					//put a little "..." at the end of the field
					if($notfit)
					{
						pdf_setfont($this->pdf,$this->normalfont,8);
						pdf_show_boxed($this->pdf,"...",
							$this->loc($xpos+$width-0.10),$this->loc($this->yloc-$line_height-0.05),
							$this->loc(0.10),$this->loc($line_height),
							$table['dataalign'][$c],null);
						pdf_setfont($this->pdf,$this->normalfont,$this->defaultFontSize);
					}

					$xpos+=$width;
				}
				$this->yloc -= $line_height*$col_height;

				//draw the line below the table data)
				pdf_moveto($this->pdf,$this->loc($xpos_of_table),$this->loc($this->yloc));
				pdf_lineto($this->pdf,$this->loc($xpos_of_table+$table_width),$this->loc($this->yloc));
				pdf_stroke($this->pdf);

				if($this->yloc<1.1)
				{
					$this->addTableEnd($table, $xpos_of_table, $top_of_table);
					$this->newPage($this->page_width,$this->page_height);
					$top_of_table = $this->addTableStart($table, $xpos_of_table, $table_width);
				}
			}
		}

		/* Finish the table */
		$this->addTableEnd($table, $xpos_of_table, $top_of_table);

		// print the total in th etable at the bottom of the table 
		if($table['total'] != 0) {
			$this->addText("(Total: {$table['total']})", 'right');
		} else {
			$t = count($table['data']);
			$this->addText("(Rows: $t)", 'right');
		}
		

	}

	//page styles: "normal" "empty" "labels"
	function setPageStyle($style="normal")
	{
		$this->page_style=$style;
	}

	function setLabelDimensions($width,$height,$xspacer=0.125,$yspacer=0.125,$fontsize=10,$toppadding=0)
	{
		$this->label_width=$width;
		$this->label_height=$height;
		$this->label_xspacer=$xspacer;
		$this->label_yspacer=$yspacer;
		$this->label_toppadding=$toppadding;

		$this->labels_per_row=floor($this->page_width/($width+$xspacer));
		$this->labels_per_column=floor(($this->page_height-$toppadding*2)/($height+$yspacer));
		$this->labels_per_page=$this->labels_per_row * $this->labels_per_column;

		$this->labels_start_xpos=($this->page_width-$this->labels_per_row*$width - $this->label_xspacer*($this->labels_per_row-1))/2;
		$this->labels_start_ypos=$this->page_height
						- ($this->page_height-$this->labels_per_column*$height-$this->label_yspacer*($this->labels_per_column-1))/2;
		$this->setFontSize($fontsize);
	}


	function outputArray()
	{
		$ret = array();

		pdf_end_page($this->pdf);

		//only close the image if it was opened to begin with
		if($this->logoimage)
			pdf_close_image($this->pdf,$this->logoimage);

		pdf_close($this->pdf);

		$ret['data'] = pdf_get_buffer($this->pdf);

		$filename=strtolower($this->page_subheader);
		$filename=ereg_replace("[^a-z0-9]","_",$filename);

		$ret['header'][] = "Content-type: application/pdf";
		$ret['header'][] = "Content-disposition: inline; filename=sfiab_".$filename.".pdf";
		$ret['header'][] = "Content-length: ".strlen($ret['data']);
		$ret['header'][] = "Pragma: public";
		return $ret;
	}

	function output()
	{
		$data = $this->outputArray();
		foreach($data['header'] as $h) header($h);
		echo $data['data'];
	}


	function lpdf($header,$subheader,$logo)
	{
		$this->pdf=pdf_new();
		pdf_open_file($this->pdf,null);

		//calculate this now, becauasae aparently we cant calculated up top in the class definition
		$this->content_width=$this->page_width-($this->page_margin*2);

		//open up the first page
		//Letter size (8.5 x 11) is 612,792
//		pdf_begin_page($this->pdf,612,792);
		// pdf_set_parameter($this->pdf, "FontOutline", "Arial=/home/sfiab/www.sfiab.ca/sfiab/arial.ttf");
		//$arial=pdf_findfont($this->pdf,"Arial","host",1);
		$this->normalfont=pdf_findfont($this->pdf,"Times-Roman","host",0);
		$this->boldfont=pdf_findfont($this->pdf,"Times-Bold","host",0);
		$this->headerfont=pdf_findfont($this->pdf,"Times-Bold","host",0);

		if(file_exists($logo))
			$this->logoimage=pdf_open_image_file($this->pdf,"gif",$logo,"",0);

		pdf_set_info($this->pdf,"Author","SFIAB");
		pdf_set_info($this->pdf,"Creator","SFIAB");
		pdf_set_info($this->pdf,"Title","SFIAB - $subheader");
		pdf_set_info($this->pdf,"Subject","$subheader");

		$this->page_header=$header;
		$this->page_subheader=$subheader;
		$this->pagenumber=0;


		//add the stuff to the first page
//		$this->addHeaderAndFooterToPage();
	}
}
?>
