<?
/**
 * Table Editor Class
 * 
 * This class can be used to very easily and quickly create a full database-driven table editor
 * @author James Grant <james@lightbox.org>
 * @version 1.1
 * @package tableeditor
 */
 
//Version 2006-04-21
//Version 2006-05-04
//Version 2006-05-07
//Version 2006-06-08 - add filtering capabilities to the display
//Version 2006-06-16 - add file uploads setUploadPath() and fieldname='filename'
//Version 2006-07-21 - add ability to autodetect options for enum() fields, and show them as a <select>
//Version 2006-07-27 - fix a bug when displaying an uploaded file that no longer exists
//Version 2006-08-09 - let select_or_text fields have default values assigned to the dropdown list via setFieldOptions
//			- add new function setYearSelectRange(min,max), which can specify the year spread to display on any input_year_select
//			- add feature so when displaying years that dont exist, if it doesnt find the selected value in the list it adds a new item at the b ottom of the list with the value
//Version 2006-08-10 - Add paganation support for those really really really long lists.  Default is 50, set using setRowsPerPage()
//Version 2006-08-13 - Add the option to add custom action icons to do various stuffs
//Version 2006-08-17 - Add an option to delete an uploaded file without having to re-upload overtop it.
//Version 2006-09-01 - Fixed abug when adding an entry that has a filename field but the filename was left blank
//Version 2006-09-05 - Fixed the style of date and time inputs because they are in their own table so the TD's need the tableedit class assigned to them or the stuff inside the table wont be the same as the rest of the editor stuff.
//Version 2006-09-25 - Fixed time formatting for 12/24 hour selection
//			- Fix INSERTING with a hidden field with value of NOW()


/*
interface TableEditorInterface {
	function tableEditorSetup($editor);
	function tableEditorLoad();
	function tableEditorSave($data);
	function tableEditorDelete();
	function tableEditorGetList($editor);
};
*/



//ironforge
//$icon_path="/phpscripts/icons/16";
//lightbox
//$icon_path="/icons/16";
//cfdc
//$icon_path="/phpscripts/images/16";
//sfiab
$icon_path="{$config['SFIABDIRECTORY']}/images/16";

if(!function_exists("i18n"))
{
	function i18n($str,$args=array())
	{
		for($x=1;$x<=count($args);$x++)
		{
			$str=str_replace("%$x",$args[$x-1],$str);
		}
		return htmlspecialchars($str);
	}
}

if(!function_exists("happy"))
{
	function happy($str)
	{
		return "<div class=\"happy\">$str</div>";
	}
}
if(!function_exists("error"))
{
	function error($str)
	{
		return "<div class=\"error\">$str</div>";
	}
}


if(!$icon_extension)
{
	//detect the browser first, so we know what icons to use
	if(stristr($_SERVER['HTTP_USER_AGENT'],"MSIE"))
		$icon_extension="gif";
	else
		$icon_extension="png";
}


/**
 * The main class
 * @package tableeditor
 */
class TableEditor
{
	/**#@+
	* @access private
	* @var array
	*/
	var $table;
	var $listfields=array();
	var $editfields=array();
	var $hiddenfields=array();

	var $fieldOptions=array();
	var $fieldValidation=array();
	var $fieldDefaults=array();
	var $fieldInputType=array();
	var $fieldInputOptions=array();
	var $fieldFilterList=array();
	var $actionButtons=array();
	var $additionalListTables=array();
	var $options=array();
	/**#@-*/

	/**#@+
	* @access private
	* @var string
	*/
	var $classname;
	var $sortDefault;
	var $sortCurrent;
	var $recordType;
	var $primaryKey;
	var $timeformat;
	var $dateformat;
	var $uploadPath;
	var $yearSelectRangeMin;
	var $yearSelectRangeMax;
	var $rowsPerPage;
	var $activePage;

	var $DEBUG;

	/**#@-*/

	/**#@+
	* @access private
	* @var boolean
	*/
	var $allowAdding;
	/**#@-*/

	/**
	 * Constructor
	 * @param array $table
	 * @param array $listfields
	 * @param array $editfields
	 * @param array $hiddenfields
	 */
	function TableEditor($classname,$listfields=null,$editfields=null,$hiddenfields=null)
	{
		//set defaults
		$this->timeformat="12hrs";
		$this->dateformat="Y-m-d";
		$this->allowAdding=true;
		$this->rowsPerPage=50;
		$this->activePage=1;
		$this->DEBUG=false;

		if($_GET['DEBUG']) $this->setDebug($_GET['DEBUG']);

		if(is_callable(array($classname, 'tableEditorSetup'))) {
			//grab the table
			$this->classname=$classname;
			call_user_func(array($this->classname, 'tableEditorSetup'), &$this);
		} else {
			//grab the list fields
			$this->listfields=$listfields;
			$this->table=$classname;

			//grab the edit fields, if there arent any, then edit==list
			if($editfields)
				$this->editfields=$editfields;
			else
				$this->editfields=$listfields;

			if($hiddenfields)
				$this->hiddenfields=$hiddenfields;
		}


	}

	function setListFields($f)
	{
		$keys = array_keys($f);
		$this->listfields = $f;
		$this->primaryKey = $keys[0];
	}
	function setEditFields($f)
	{
		$this->editfields = $f;
	}
	function setHiddenFields($f)
	{
		$this->hiddenfields = $f;
	}
	function setTable($t)
	{
		$this->table = $t;
	}


	/**
	 * Sets the date format to use when displaying dates.  Accepts anyting that php's date() function uses
	 * @link http://ca3.php.net/manual/en/function.date.php
	 * @param string $df The format string
	*/
	function setDateFormat($df)
	{
		$this->dateformat=$df;
	}

	/**
	 * Sets the time format to use when displaying times. 
	 * @param string $tf can be either "12hrs" or "24hrs"
	*/
	function setTimeFormat($tf)
	{
		if($tf=="12hrs" || $tf=="12") $this->timeformat=="12hrs"; else $this->timeformat="24hrs";
	}
	function setSortField($field)
	{
		$_SESSION["TableEditorSort{$this->table}"]=$field;
		$this->sortCurrent=$field;
	}

	/**
	 * Sets whether you are allowed to add rows to the table or not
	 * @param boolean $allowadd
	*/
	function setAllowAdding($allowadd=true)
	{
		$this->allowAdding=$allowadd;
	}

	/**
	 * Sets the primary autoincrement field from the database table to use to identify the rows.  Usually 'id'
	 * @param string $field The database field name
	*/
	function setPrimaryKey($field)
	{
		$this->primaryKey=$field;
	}

	function setDefaultSortField($field)
	{
		$this->sortDefault=$field;
	}

	function sortField()
	{
		if($_SESSION["TableEditorSort{$this->table}"])
			return $_SESSION["TableEditorSort{$this->table}"];
		else
			return $this->sortDefault;
	}

	function setRecordType($t)
	{
		$this->recordType=$t;
	}

	function setFieldOptions($f,$o)
	{
		$this->fieldOptions[$f]=$o;
	}

	function setFieldValidation($f,$v)
	{
		$this->fieldValidation[$f]=$v;
	}

	function setFieldDefaultValue($f,$v)
	{
		$this->fieldDefaults[$f]=$v;
	}

	function setFieldInputType($f, $t)
	{
		$this->fieldInputType[$f]=$t;
	}

	function setFieldInputOptions($f,$o)
	{
		$this->fieldInputOptions[$f]=$o;
	}

	function filterList($f,$v=false)
	{
		$this->fieldFilterList[$f]=$v;
	}

	function additionalListTable($t)
	{
		$this->additionalListTables[]=",`$t`";
	}
	function additionalListTableLeftJoin($t,$on)
	{
		$this->additionalListTables[]=" LEFT JOIN `$t` ON $on ";
	}

	function setUploadPath($p)
	{
		$this->uploadPath=$p;
	}
	function setDownloadLink($l)
	{
		$this->downloadLink=$l;
	}

	function setYearSelectRange($min,$max)
	{
		$this->yearSelectRangeMin=$min;
		$this->yearSelectRangeMax=$max;
	}

	function setRowsPerPage($numrows)
	{
		$this->rowsPerPage=$numrows;
	}

	function setActivePage($page)
	{
		$this->activePage=$page;
	}
	
	function addActionButton($name,$link,$icon)
	{
		$this->actionButtons[]=array("name"=>$name,"link"=>$link, "icon"=>$icon);
	}

	function setDebug($d)
	{
		$this->DEBUG=$d;
	}

	function createOption($o)
	{
		$this->options[$o] = null;
	}

	function setOption($o, $v) 
	{
		if(array_key_exists($o, $this->options)) {
			$this->options[$o] = $v;
			return;
		}
		echo "Attempt to setOption($o, $v): option doesn't exist (create it with createOption)";
		exit;
	}
	function getOption($o)
	{
		if(array_key_exists($o, $this->options)) {
			return $this->options[$o];
		}
		echo "Attempt to getOption($o): option doesn't exist (create it with createOption)";
		exit;
	}

	function getFieldType($f)
	{
		$inputtype = '';
		$inputmaxlen = 0;
		$inputsize = 0;

		//figure out what kind of input this should be
		$q=mysql_query("SHOW COLUMNS FROM `{$this->table}` LIKE '$f'");
		$r=mysql_fetch_object($q);

		if(ereg("([a-z]*)\(([0-9,]*)\)",$r->Type,$regs))
		{
			switch($regs[1])
			{
				case "varchar": 
					$inputtype="text";
					$inputmaxlen=$regs[2];
					if($regs[2]>50) $inputsize=50; else $inputsize=$regs[2];
				break;

				case "int":
					$inputtype="text";
					$inputmaxlen=10;
					$inputsize=10;
				break;

				case "decimal":
					$inputtype="text";
					$inputmaxlen=10;
					$inputsize=10;
					break;

				case "tinyint":
					$inputtype="text";
					$inputmaxlen=5;
					$inputsize=4;
				break;

				default:
					$inputtype="text";
					$inputmaxlen=$regs[2];
					if($regs[2]>50) $inputsize=50; else $inputsize=$regs[2];
				break;
			}
		}
		else if(ereg("([a-z]*)",$r->Type,$regs))
		{
			switch($regs[1])
			{
				case "tinytext":
					$inputmaxlen=255;
				case "text":
					$inputtype="textarea";
				break;
				case "date":
					$inputtype="date";
				break;
				case "time":
					$inputtype="time";
					break;
				case "datetime":
					$inputtype="datetime";
					break;
				case "enum":
					//an enum is a select box, but we already know what the options should be
					//so rip out the options right now and add them
					$inputtype="select";
					$enums=substr(ereg_replace("'","",$r->Type),5,-1);
					$toks=split(",",$enums);
					foreach($toks as $tok)
					{
						$this->fieldOptions[$f][]=$tok;
					}
					break;
			}
		}

		if(substr($f,0,4)=="sel_")
		{
			$inputtype="select_or_text";
		}

		if(substr($f,0,8)=="filename" && $this->uploadPath)
		{
			$inputtype="file";
		}


		if(array_key_exists($f,$this->fieldOptions))
		{
			//only change to select if the type is not select_or_Text
			//if we are already select or text, then the options will appear
			//first in the list, then any options that arent there by default
			//will appear under them in the dropdown
			if($inputtype!="select_or_text")
				$inputtype="select";
		}	
		return array($inputtype, $inputmaxlen, $inputsize);
	}

	function defaultLoad()
	{
		$query="SELECT {$this->primaryKey}";
		foreach($this->editfields AS $f=>$n) 
			$query.=", `$f`";
		$query.=" FROM `{$this->table}`";
		$query.=" WHERE {$this->primaryKey}='{$_GET['edit']}'";
		if($this->DEBUG) echo $query;
		$editquery=mysql_query($query);
		$editdata=mysql_fetch_assoc($editquery);
		return $editdata;
	}

	function defaultSave($insert_mode, $keyval, $editdata)
	{
		$query = "";
		if($insert_mode) {
			$query="INSERT INTO `{$this->table}` (";
			//create list of fields to insert
			foreach($editdata AS $f=>$n)
				$query.="`$f`,";
			//rip off the last comma
			$query=substr($query,0,-1);
			$query.=") VALUES (";
		} else {
			$query="UPDATE `{$this->table}` SET ";
		}

		foreach($editdata AS $f=>$n)
		{
			if($insert_mode) $field = '';
			else $field = "`$f`=";

			$query .= $field.$n.",";
		}
		//rip off the last comma
		$query=substr($query,0,-1);

		if($insert_mode) {
			$query.=")";
		} else {
			$query.=" WHERE {$this->primaryKey}='{$keyval}'";
		}

		if($this->DEBUG) echo $query;
		mysql_query($query);
	}

	function defaultDelete($keyval)
	{
		mysql_query("DELETE FROM {$this->table} WHERE {$this->primaryKey}='{$keyval}'");
		echo happy(i18n("Successfully deleted %1",array($this->recordType)));
	}

	function execute()
	{
		if($_GET['TableEditorAction']=="sort" && $_GET['sort'])
		{
			$this->setSortField($_GET['sort']);
		}
		
		if($_GET['TableEditorAction']=="delete" && $_GET['delete'])
		{
			if($this->classname)
				$data = new $this->classname($_GET['delete']);
			if(method_exists($data, 'tableEditorDelete')) {
				$data->tableEditorDelete();
			} else {
				$this->defaultDelete($_GET['delete']);
			}
		}

		if($_GET['TableEditorAction']=="page" && $_GET['page'])
		{
			$this->setActivePage($_GET['page']);
		}

		if( ($_POST['TableEditorAction']=="editsave" && $_POST['editsave']) 
		 || ($_POST['TableEditorAction']=="addsave")  )
		{
			if($_POST['TableEditorAction']=="addsave") {
				if($this->classname)
					$data = new $this->classname();
				$insert_mode = 1;
			} else {
//				print("Insesrt mode=0\n");
				if($this->classname)
					$data = new $this->classname($_POST['editsave']);
				$insert_mode = 0;
			}

			$editdata = array();

			foreach($this->editfields AS $f=>$n)
			{
				$inputtype = '';
				if(isset($_POST['tableeditor_fieldtype'])) {
					if(array_key_exists($f, $_POST['tableeditor_fieldtype'])) {
						$inputtype = $_POST['tableeditor_fieldtype'][$f];
					}
				}

				if($inputtype == 'date' || $inputtype == 'datetime') //r->Type=="date")
				{
					if($_POST[$f."_year"] && $_POST[$f."_month"] && $_POST[$f."_day"]) {
						$yy = intval($_POST[$f."_year"]);
						$mm = intval($_POST[$f."_month"]);
						$dd = intval($_POST[$f."_day"]);

						$editdata[$f] = "'$yy-$mm-$dd";

						if($inputtype == 'date') {
							$editdata[$f] .= "'";
						} else if($_POST[$f."_hour"]!="" && $_POST[$f."_minute"]!="") {
							$hh = intval($_POST[$f."_hour"]);
							$mi = intval($_POST[$f."_minute"]);

							$editdata[$f] .= " $hh:$mi:00'";
						} else {
							$editdata[$f] = 'NULL';
						}
					} else {
						$editdata[$f] = 'NULL';
					}
				}
				else if($inputtype == 'time') //r->Type=="time")
				{
					if($_POST[$f."_hour"]!="" && $_POST[$f."_minute"]!="") {
						$editdata[$f] = "'".mysql_escape_string(stripslashes($_POST[$f."_hour"])).":".
							mysql_escape_string(stripslashes($_POST[$f."_minute"])).":00'";
					} else {
						$editdata[$f] = 'NULL';
					}
				}
				else if($inputtype == 'multicheck')
				{
					/* This one has no direct quoted translation, hope the user specified
					 * a save routine to handle this */
					$editdata[$f] = array();
					if($_POST[$f]) {
						$a = $_POST[$f];
						foreach($a as $k=>$val) {
							$editdata[$f][$k] = $val;
						}
					}
				}
				else if(substr($f,0,4)=="sel_")
				{
					//chose the text field first, if its been filled in, otherwise, go with the select box
					if($_POST[$f."_text"])
						$editdata[$f] = "'".mysql_escape_string(stripslashes($_POST[$f."_text"]))."'";
					else if($_POST[$f."_select"])
						$editdata[$f] = "'".mysql_escape_string(stripslashes($_POST[$f."_select"]))."'";
					else
					{
						//maybe the options were over-wridden, if so, just check the field name
						$editdata[$f] = "'".mysql_escape_string(stripslashes($_POST[$f]))."'";
					}

				}
				else if(strtolower($f)=="website" && $_POST[$f])
				{
					//intelligently handle website fields, making sure they have the protocol to use
					//but allow them to enter http:// or https:// themselves.
					//if no protocol is given, assume http://
					if(substr(strtolower($_POST[$f]),0,4)=="http")
						$editdata[$f] = "'".mysql_escape_string(stripslashes($_POST[$f]))."'";
					else
						$editdata[$f] = "'http://".mysql_escape_string(stripslashes($_POST[$f]))."'";

				}
				else if(substr($f,0,8)=="filename" && $this->uploadPath)
				{
					//accept the upload
					if($_FILES[$f]['size']>0)
					{
						if(file_exists($this->uploadPath."/".$_FILES[$f]['name']))
							echo error(i18n("A file with that filename already exists, it will be overwritten"));
						move_uploaded_file($_FILES[$f]['tmp_name'],$this->uploadPath."/".$_FILES[$f]['name']);
						$editdata[$f] = "'".mysql_escape_string(stripslashes($_FILES[$f]['name']))."'";
					}
					else
					{
						//maybe they want to delete it, in which case $_POST['clear'] will be an array and have this field name in it.
						if(is_array($_POST['clear']))
						{
							if(in_array($f,$_POST['clear']))
								$editdata[$f] = 'NULL';
						}

					}
				}
				else
				{
					if($this->fieldValidation[$f])
						$editdata[$f] = "'".mysql_escape_string(stripslashes(ereg_replace($this->fieldValidation[$f],"",$_POST[$f])))."'";
					else
						$editdata[$f] = "'".mysql_escape_string(stripslashes($_POST[$f]))."'";
				}
			}


			if(count($this->hiddenfields))
			{
				foreach($this->hiddenfields AS $f=>$n)
				{
					//well well... sometimes we want to use a function here, such as NOW(), so if thats the case then we dont want the ' ' around the value, so, lets check for NOW() and handle it differently
					if(strtolower($n)=="now()")
						$editdata[$f] = "$n";
					else
						$editdata[$f] = "'$n'";
				}
			}

			if(method_exists($data, 'tableEditorSave')) {
				$data->tableEditorSave($editdata);
			} else {
				$keyval = ($insert_mode == 0) ? $_POST['editsave'] : 0;
				$this->defaultSave($insert_mode, $keyval, $editdata);
			}
			

			if($inser_tmode) {
				$text_error = "adding new";
				$text_happy = "added new";
			} else {
				$text_error = "saving";
				$text_happy = "saved";
			}

//			if($this->DEBUG) echo $query;

//			mysql_query($query);
			if(mysql_error())
			{
				echo error(i18n("Error $text_error %1: %2",array($this->recordType,mysql_error())));
			}
			else
			{
				echo happy(i18n("Successfully $text_happy %1",array($this->recordType)));
			}
		}

		if($_GET['TableEditorAction']=="add" || ($_GET['TableEditorAction']=="edit" && $_GET['edit']) )
		{
			if($this->uploadPath)
				echo "<form name=\"TableEditor\" enctype=\"multipart/form-data\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">";
			else
				echo "<form name=\"TableEditor\" method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">";
			$action=$_GET['TableEditorAction'];
			if($action=="add")
			{
				echo "<h2>".i18n("Add New %1",array($this->recordType))."</h2>";
				echo "<input type=\"hidden\" name=\"TableEditorAction\" value=\"addsave\">";

				//check for any defaults
				if(count($this->fieldDefaults))
				{
					foreach($this->fieldDefaults AS $f=>$n)
						$editdata[$f]=$n;
				}
			}
			else if($action=="edit")
			{
				echo "<h2>".i18n("Edit %1",array($this->recordType))."</h2>";
				echo "<input type=\"hidden\" name=\"TableEditorAction\" value=\"editsave\">";
				echo "<input type=\"hidden\" name=\"editsave\" value=\"{$_GET['edit']}\">";

				if($this->classname)
					$data = new $this->classname($_GET['edit']);

				if(method_exists($data, 'tableEditorLoad')) {
					$editdata = $data->tableEditorLoad();
				} else {
					$editdata = $this->defaultLoad();
				}
			}
		
		
			echo "<script language=\"javascript\" type=\"text/javascript\">
				<!--
				function do_maxlength(obj, maxlength)
				{
					if(obj.value.length > maxlength) {
						obj.value = obj.value.substr(0, maxlength);
					}
				}
				-->
				</script>";
			echo "<table class=\"tableedit\">";
			foreach($this->editfields AS $f=>$n)
			{
				$pos = strpos($n, "|");
				$n2 = "";
				if($pos != false) {
					$n2 = i18n(substr($n, $pos + 1)).' ';
					$n = substr($n, 0, $pos);
				}
					
				echo "<tr><th valign=\"top\">".i18n($n)."</th><td>$n2";
		
				/* If we know the input type, assume the user knows what they are doing, else,
				 * try to query it from the databse */
				if(isset($this->fieldInputType[$f])) {
					$inputtype = $this->fieldInputType[$f];
					$inputmaxlen = 0; // FIXME
					$inputsize = 0; // FIXME
				} else {
					list($inputtype, $inputmaxlen, $inputsize) = $this->getFieldType($f);
				}

				switch($inputtype)
				{
					case "text":
						if($this->fieldInputOptions[$f])
							echo "<input type=\"text\" ".$this->fieldInputOptions[$f]." id=\"$f\" name=\"$f\" value=\"".htmlspecialchars($editdata[$f])."\"/>";
						else
							echo "<input type=\"text\" size=\"$inputsize\" maxlength=\"$inputmaxlen\" id=\"$f\" name=\"$f\" value=\"".htmlspecialchars($editdata[$f])."\"/>";

						break;
					case "textarea":
						$maxlen = ($inputmaxlen > 0) ? " onkeypress=\"return do_maxlength(this, $inputmaxlen);\" " : '';
						if($this->fieldInputOptions[$f])
							echo "<textarea id=\"$f\" name=\"$f\" $maxlen".$this->fieldInputOptions[$f].">".htmlspecialchars($editdata[$f])."</textarea>";
						else
							echo "<textarea id=\"$f\" name=\"$f\" $maxlen rows=\"5\" cols=\"50\">".htmlspecialchars($editdata[$f])."</textarea>";
						break;
					case "select":
						if($this->fieldInputOptions[$f])
							echo "<select ".$this->fieldInputOptions[$f]." id=\"$f\" name=\"".$f."\">";
						else
							echo "<select id=\"$f\" name=\"".$f."\">";
						echo "<option value=\"\">".i18n("Choose")."</option>\n";

						foreach($this->fieldOptions[$f] AS $opt)
						{
							if(is_array($opt))
							{
								if("{$opt['key']}" == "{$editdata[$f]}") $sel="selected=\"selected\""; else $sel="";
								echo "<option $sel value=\"".$opt['key']."\">".i18n($opt['val'])."</option>\n";
							}
							else
							{
								if("{$opt}" == "{$editdata[$f]}") $sel="selected=\"selected\""; else $sel="";
								echo "<option $sel value=\"".$opt."\">".i18n($opt)."</option>\n";
							}
						}
						echo "</select>";

						break;
					case "enum":
						break;
					case "select_or_text":
						$optq=mysql_query("SELECT DISTINCT($f) AS $f FROM `{$this->table}` ORDER BY $f");
						if($this->fieldInputOptions[$f])
							echo "<select ".$this->fieldInputOptions[$f]." id=\"".$f."_select\" name=\"".$f."_select\">";
						else
							echo "<select id=\"".$f."_select\" name=\"".$f."_select\">";
						echo "<option value=\"\">".i18n("Choose or type")."</option>\n";

						if($this->fieldOptions[$f])
						{
							foreach($this->fieldOptions[$f] AS $opt)
							{
								if($opt == $editdata[$f]) $sel="selected=\"selected\""; else $sel="";
								echo "<option $sel value=\"".$opt."\">".i18n($opt)."</option>\n";
							}
							echo "<option value=\"\">-------------</option>";
						}
//							print_r($this->fieldOptions[$f]);

						while($opt=mysql_fetch_object($optq))
						{
							if(is_array($this->fieldOptions[$f]) && in_array($opt->$f,$this->fieldOptions[$f]))
								continue;

							if($opt->$f == $editdata[$f]) $sel="selected=\"selected\""; else $sel="";
							echo "<option $sel value=\"".$opt->$f."\">".i18n($opt->$f)."</option>\n";
						}
						echo "</select>";
						echo " or ";
						//only show the input half-sized because its beside the select box which is already taking up space.
						$inputsize=round($inputsize/2);
						//input always starts emptpy as well, because we already have it selected in the list
						if($this->fieldInputOptions[$f])
							echo "<input type=\"text\" ".$this->fieldInputOptions[$f]." id=\"".$f."_text\" name=\"".$f."_text\" value=\"\" />";
						else
							echo "<input type=\"text\" size=\"$inputsize\" maxlength=\"$inputmaxlen\" id=\"".$f."_text\" name=\"".$f."_text\" value=\"\" />";
						break;
					case "multicheck":
						$ks = array_keys($this->fieldOptions[$f]);
						foreach($ks as $k) {
							$ch = '';
							if(is_array($editdata[$f])) {
								if(array_key_exists($k, $editdata[$f])) {
									$ch = ' checked=checked ';
								} 
							}
							echo "<input type=\"checkbox\" name=\"{$f}[$k]\" value=\"1\" $ch> {$this->fieldOptions[$f][$k]}<br>";
						}
						echo "<input type=\"hidden\" name=\"tableeditor_fieldtype[$f]\" value=\"multicheck\">";
						break;

					case "date":
					case "datetime":
						$a = split('[- :]',$editdata[$f]);
						if($inputtype == 'date') {
							list($yy,$mm,$dd)=$a;
							$w = 10;
						} else {
							list($yy,$mm,$dd,$hh,$mi,$ss)=$a;
							$w=15;
						}
						//if we put a small width here, then it prevents it from expanding to whatever width it feels like.
						echo "<table width=\"$w\" align=\"left\" cellspacing=0 cellpadding=0>";
						echo "<tr><td class=\"tableedit\" >";
						$this->month_selector($f."_month",$mm);
						echo "</td><td class=\"tableedit\">";
						$this->day_selector($f."_day",$dd);
						echo "</td><td class=\"tableedit\">";
						$this->year_selector($f."_year",$yy);
						echo "</td>";
						if($inputtype == 'date') {
							echo "</tr>";
							echo "</table>";
							echo "<input type=\"hidden\" name=\"tableeditor_fieldtype[$f]\" value=\"date\">";
							break;
						} 
						/* Else, fall through, with hh, mi, ss already set */


					case "time":
						if($inputtype == 'time') {
							list($hh,$mi,$ss)=split(":",$editdata[$f]);

							echo "<table width=\"10\" cellspacing=0 cellpadding=0>";
							echo "<tr>";
						}
						/* Common code for time, and datetime */
						echo "<td class=\"tableedit\">";
						$this->hour_selector($f."_hour",$hh,false,"12hr");
						echo "</td><td class=\"tableedit\">";
						$this->minute_selector($f."_minute",$mi);
						echo "</td></tr>";
						echo "</table>";
						echo "<input type=\"hidden\" name=\"tableeditor_fieldtype[$f]\" value=\"$inputtype\">";
						break;
					case "file":
						if($editdata[$f])
						{
							if(file_exists($this->uploadPath."/".$editdata[$f]))
							{
								//only show a link to the file if the upload path is inside the document root
								if(strstr(realpath($this->uploadPath),$_SERVER['DOCUMENT_ROOT']))
								{
									echo "<A href=\"{$this->uploadPath}/{$editdata[$f]}\">{$editdata[$f]}</a>";
								}
								else
								{
									echo $editdata[$f];
								}
								echo " (".filesize($this->uploadPath."/".$editdata[$f])." bytes) <input type=\"checkbox\" name=\"clear[]\" value=\"$f\">delete<br />";
							}
							else
							{
								echo $editdata[$f]." (does not exist)<br />";
							}
						}
						echo "<input type=\"file\" ".$this->fieldInputOptions[$f]." id=\"$f\" name=\"$f\" />";

					break;


					default:
						echo "<input type=\"text\" id=\"$f\" name=\"$f\" value=\"".htmlspecialchars($editdata[$f])."\"/>";
				}

				echo "</td></tr>";
			}
			echo "<tr><td align=\"center\" colspan=\"2\">";

			echo "<table width=\"80%\"><tr><td valign=\"top\" align=\"center\" width=\"50%\">";
			if($action=="add")
				echo "<input type=\"submit\" value=\"".i18n("Add New %1",array($this->recordType))."\">";
			else if($action=="edit")
				echo "<input type=\"submit\" value=\"".i18n("Save %1",array($this->recordType))."\">";
			echo "</form>";
			echo "</td><td valign=\"top\" align=\"center\" width=\"50%\">";
			echo "<form method=\"get\" action=\"{$_SERVER['PHP_SELF']}\">";
			echo "<input type=\"submit\" value=\"".i18n("Cancel")."\">";
			echo "</form>";
			echo "</td></tr></table>";
			echo "</td></tr>";
			echo "</table>";
		}
		else if($_GET['TableEditorAction']=="export")
		{
			//fixme: how to do an export? we cant send headers because its possible that output has already started!

		}
		else
		{
			$this->displayTable();
		}

	}

	function defaultGetList()
	{
		$sel = array();
		$from = array();
		$where = array();

		foreach($this->listfields AS $f=>$n)
			$sel[] = "`$f`";

		$from[] = "`{$this->table}`";
		if(count($this->additionalListTables)) {
			foreach($this->additionalListTables as $t) {
				$from[] = "$t ";
			}
		}

		if(count($this->fieldFilterList)) {
			foreach($this->fieldFilterList AS $k=>$v) {
				$where[] = ($v == false) ? $k : "`$k`='$v'";
			}
		}
		return array($sel, $from, $where);
	}

	function displayTable()
	{
		global $icon_path;
		global $icon_extension;

		$query="SELECT SQL_CALC_FOUND_ROWS {$this->primaryKey}";

		if(is_callable(array($this->classname, 'tableEditorGetList'))) {
			list($sel, $from, $where) = call_user_func(array($this->classname, 'tableEditorGetList'), &$this);
		} else {
			list($sel, $from, $where) = $this->defaultGetList();
		}

		foreach($sel as $s) $query .= ",$s";
		$query .= " FROM ";
		foreach($from as $f) $query .= "$f ";
		$query .= " WHERE 1 ";
		if(is_array($where)) {
			foreach($where as $w) $query .= "AND $w ";
		}

		if($this->sortField())
			$query.=" ORDER BY ".$this->sortField()."";

		if($this->rowsPerPage>0)
		{
			//first, we treat page 1 as the first page, but really, "row 0" is the first page, so we need
			//to be careful.
			//LIMIT offset,number
			$offset=($this->activePage-1)*$this->rowsPerPage;
			//just to make sure nothing funky is goin on.
			if($offset<0) $offset=0;
			$query.=" LIMIT $offset,$this->rowsPerPage";
		}

		if($this->allowAdding)
		{
			echo "<a href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=add\">".i18n("Add new %1",array($this->recordType))."</a><br /><br />";
		}
		if($this->DEBUG) echo $query;

//		print("query[$query]");
		$q=mysql_query($query);
		if($q == false) {
			echo "Sorry, MYSQL query failed: <pre>$query</pre><br />";
			echo "Error: ".mysql_error(); 
			exit;
		}

		//put in some paganation stuff here.
		$foundrowsq=mysql_query("SELECT FOUND_ROWS() AS f");
		$foundrowsr=mysql_fetch_object($foundrowsq);
		$foundrows=$foundrowsr->f;

		if($foundrows>$this->rowsPerPage)
		{
			$numpages=ceil($foundrows/$this->rowsPerPage);

			if($this->activePage>1)
			{
				$prevpage=$this->activePage-1;
				echo "<a href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=page&page=$prevpage\">&lt; Prev</a>";
			}
			else
				echo "&lt; Prev";

			echo "&nbsp;";

			if($numpages<10)
			{
				//if there's less than 10 pages, lets show links for all the pages
				for($x=1;$x<=$numpages;$x++)
				{
					echo "&nbsp;";
					if($x==$this->activePage)
						echo "<b>$x</b>";
					else
						echo "<a href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=page&page=$x\">$x</a>";
					echo "&nbsp;";
				}
			}
			else
			{
				//if we have more than 10 pages, lets show the previous 5 ..currentpage.. next 5
				//if there's less than 10 pages, lets show links for all the pages
				$start=$this->activePage-4;
				$end=$this->activePage+4;
				if($start<1)
				{
					$end+=abs(1-$start);
					$start=1;
				}

				if($end>$numpages)
				{
					$start-=abs($numpages-$end);
					$end=$numpages;

				}

				if($start>1)
				{
					echo "&nbsp;";
					echo "<a href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=page&page=1\">1</a>";
					echo "&nbsp;";
					echo "...";
				}

				for($x=$start;$x<=$end;$x++)
				{
					echo "&nbsp;";
					if($x==$this->activePage)
						echo "<b>$x</b>";
					else
						echo "<a href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=page&page=$x\">$x</a>";
					echo "&nbsp;";
				}

				if($end<$numpages)
				{
					echo "...";
					echo "&nbsp;";
					echo "<a href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=page&page=$numpages\">$numpages</a>";
					echo "&nbsp;";

				}


			}
			echo "&nbsp;";
			if($this->activePage<$numpages)
			{
				$nextpage=$this->activePage+1;
				echo "<a href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=page&page=$nextpage\">Next &gt;</a>";
			}
			else
				echo "Next &gt;";




		}
//		else
//			echo "no need to paganate, foundrows=$foundrows, rowsPerPage=".$this->rowsPerPage;

		echo "  (Total: $foundrows)\n";
		
		if(mysql_num_rows($q))
		{
			echo "<table cellspacing=\"0\" class=\"tableview\">";
			echo "<tr>";
			foreach($this->listfields AS $f=>$n)
			{
				if($this->sortField()==$f)
				echo "<th>".i18n($n)."</th>";
				else
				echo "<th><a href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=sort&amp;sort=`$f`\">".i18n($n)."</a></th>";
			}
			echo "<th>".i18n("Actions")."</th>";
			echo "</tr>";
			while($r=mysql_fetch_object($q))
			{
				echo "<tr>";
				foreach($this->listfields AS $f=>$n)
				{
					//figure out what kind of input this should be
					$typeq=mysql_query("SHOW COLUMNS FROM `{$this->table}` LIKE '$f'");
					$typer=mysql_fetch_object($typeq);
					if($typer->Type=="time")
						echo "<td valign=\"top\">".$this->format_time($r->$f)."</td>";
					else if($typer->Type=="date")
						echo "<td valign=\"top\">".$this->format_date($r->$f)."</td>";
					else if($typer->Type=="datetime")
						echo "<td valign=\"top\">".$this->format_datetime($r->$f)."</td>";
					else if(substr($f,0,8)=="filename" && $this->uploadPath)
					{
						echo "<td valign=\"top\">";
						if($this->downloadLink) {
							$pk=$this->primaryKey;
							echo "<a href=\"{$this->downloadLink}?{$pk}={$r->$pk}\">{$r->$f}</a>";
						}
						else
						{
							//only show a link to the file if the upload path is inside the document root
							if(strstr(realpath($this->uploadPath),$_SERVER['DOCUMENT_ROOT']) && file_exists($this->uploadPath."/".$editdata[$f]))
							{
								echo "<A href=\"{$this->uploadPath}/{$r->$f}\">{$r->$f}</a>";
							}
							else
							{
								echo $r->$f;
							}
						}
						echo "</td>";
					}
					else if(substr($f,0,7)=="website")
					{
						echo "<td valign=\"top\">";
							echo "<a href=\"{$r->$f}\" target=\"_blank\">{$r->$f}</a>";
						echo "</td>";
					}
					else
					{
						if($this->fieldOptions[$f])
						{
							if(is_array($this->fieldOptions[$f][0]))
							{
								//if it is an aray, then we have a key=> and val=> so we need
								//to lookup the key and display the val
								$TE_Found_Field=false;
								foreach($this->fieldOptions[$f] AS $i=>$o)
								{
									if($o['key']==$r->$f)
									{
										echo "<td valign=\"top\">{$o['val']}</td>";
										$TE_Found_Field=true;
										break;
									}
								}
								if(!$TE_Found_Field)
								{
									echo "<td valign=\"top\"></td>";
								}
							}
							else
							{
								//if its not an array, then each element is simply a data value anyways, so we can just show the value
								echo "<td valign=\"top\">{$r->$f}</td>";
								

							}

						}
						else
						{
							echo "<td valign=\"top\">{$r->$f}</td>";
						}
					}
				}
				echo "<td align=\"center\"><nobr>";
				$pk=$this->primaryKey;

				//custom action buttons go first
				if(count($this->actionButtons))
				{
					foreach($this->actionButtons AS $button)
					{
						echo "<a title=\"{$button["name"]}\" href=\"{$button["link"]}?$pk=".$r->$pk."\"><img src=\"{$button["icon"]}.$icon_extension\" border=0></a>";
						echo "&nbsp;&nbsp;";
					}
				}
				//now the default action buttons that you cant get rid of :)
				echo "<a title=\"Edit this ".$this->recordType."\" href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=edit&amp;edit=".$r->$pk."\"><img src=\"$icon_path/edit.$icon_extension\" border=0></a>";
				echo "&nbsp;&nbsp;";
				if($this->deleteTitle) {
					$title = $this->deleteTitle;
				} else {
					$title = "Delete this ".$this->recordType;
				}					
				echo "<a title=\"$title\" onclick=\"return confirmClick('".i18n("Are you sure you want to delete this %1?",array($this->recordType))."')\" href=\"{$_SERVER['PHP_SELF']}?TableEditorAction=delete&amp;delete=".$r->$pk."\"><img src=\"$icon_path/button_cancel.$icon_extension\" border=0></a>";
				echo "</nobr></td>";
				echo "</tr>";
			}
			echo "</table>";
		}

		echo "<br />";
	}

	function month_selector($name,$selected="")
	{
		echo "<select name=\"$name\">\n";
		$months=array("","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		echo "<option value=\"\">mmm</option>\n";
		for($x=1;$x<=12;$x++)
		{
			if($x==$selected)
				$s="selected";
			else
				$s="";
			echo "<option $s value=\"$x\">".$months[$x]."</option>\n";
		}

		echo "</select>\n";

	}

	function year_selector($name,$selected="")
	{
		$now=date("Y");

		if($this->yearSelectRangeMin)
			$start=$this->yearSelectRangeMin;
		else
			$start=$now-2;


		if($this->yearSelectRangeMax)
			$end=$this->yearSelectRangeMax;
		else
			$end=$now+3;

		echo "<select name=\"$name\">\n";
		echo "<option value=\"\">yyyy</option>\n";
		$foundselected=false;
		for($x=$start;$x<$end;$x++)
		{
			if($x==$selected)
			{
				$s="selected=\"selected\"";
				$foundselected=true;
			}
			else
				$s="";
			
			echo " <option $s value=\"$x\">$x</option>\n";
		}
		//make sure if we didnt find the selected one in the list of possible options, that we add a new option for it
		//at the bottom of the list, otherwise, the display woudlnt display what is actually stored in the database!
		if(!$foundselected && $selected)
		{
			echo "<option value=\"\">----</option>\n";
			echo "<option selected=\"selected\" value=\"$selected\">$selected</option>\n";
		}
		echo "</select>\n";
	}

	function day_selector($name,$selected="")
	{
		echo "<select name=\"$name\">\n";
		echo "<option value=\"\">dd</option>\n";
		for($x=1;$x<=31;$x++)
		{
			if($x==$selected)
				$s="selected=\"selected\"";
			else
				$s="";
			
			echo " <option $s value=\"$x\">$x</option>\n";
		}
		echo "</select>\n";
	}

	function hour_selector($name,$selected="", $disabled=false)
	{
		echo "<select name=\"$name\" size=1 ".($disabled==true?"disabled":"").">\n";
		echo "<option value=\"\">hh</option>\n";

		if($selected=="")
			$selected=-1;

		for($x=0;$x<=23;$x++)
		{
			if($this->timeformat=="24hrs")
				$disp=sprintf("%02d",$x);
			else if($this->timeformat=="12hrs")
			{
				if($x==0)
					$disp="12 AM";
				else if($x<12)
					$disp="$x AM";
				else if($x==12)
					$disp="12 PM";
				else
					$disp=($x-12)." PM";
			}
			
			echo "<option value=\"$x\" ".($selected==$x?"selected":"").">$disp</option>\n";
		}

		echo "</select>\n";

	}

	function minute_selector($name,$selected="",$disabled=false)
	{
		if($selected=="")
			$selected=-1;
		for($x=0;$x<60;$x++)
			$mins[]=sprintf("%02d",$x);
//		$mins=array("00","15","30","45");
		echo "<select name=\"$name\" size=1 ".($disabled==true?"disabled":"").">\n";
		echo "<option value=\"\">mm</option>\n";

		for($x=0;$x<60;$x++)
			echo "<option value=\"$mins[$x]\" ".($selected==$mins[$x]?"selected":"").">$mins[$x]</option>\n";

		echo "</select>\n";
	}

	function format_date($d)
	{
		if(!$d) return;
		list($y,$m,$d)=split("-",$d);
		$t=mktime(0,0,0,$m,$d,$y);
		return date($this->dateformat,$t);
	}

	function format_time($t)
	{
		if(!$t) return;
		if($this->timeformat=="12hrs")
		{
			list($hh,$mm,$ss)=split(":",$t);
			//hack to get rid of leading "0" and turn it into a number
			$hh++;
			$hh--;

			if($hh==0)
				$ret="12:$mm AM";
			else if($hh<12)
				$ret="$hh:$mm AM";
			else if($hh==12)
				$ret="12:$mm PM";
			else
				$ret=($hh-12).":$mm PM";
		}
		else if($this->timeformat=="24hrs")
		{
			$ret=substr($t,0,-3);
		}
		return $ret;
	}
	function format_datetime($d)
	{
		list($d,$t)=split(' ', $d);
		$ret = $this->format_date($d).' '.$this->format_time($t);
		return $ret;
	}


}

?>
