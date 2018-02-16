<?

/* Just the fields in the tours table, we use this twice */
$tours_fields = array(	'name' => 'Tour Name',
			'num' => 'Tour Number',
			'description' => 'Description',
			'capacity' => 'Capacity',
			'grade_min' => 'Minimum Grade',
			'grade_max' => 'Maximum Grade',
			'year' => 'Year',
			'contact' => 'Contact',
			'location' => 'Location');

class tours {

/* Static members for the table editor */
function tableEditorSetup($editor) 
{
	global $tours_fields;
	global $config;

	/* Setup the table editor with the fields we want to display
	 * when displaying a list of tours, and also the type of each
	 * field where required */
	$l = array( 'num' => 'Tour Number',
			'name' => 'Tour Name',
			'capacity' => 'Capacity',
			'grade_min' => 'Minimum Grade',
			'grade_max' => 'Maximum Grade',
			'year' => 'Year',
			);

	/* Most of these should be moved to the base class, as they
	 * will be the same for all person groups */
	
	$editor->setTable('tours');
	$editor->setRecordType('Tour');
	$editor->setListFields($l);
	$editor->setPrimaryKey('id');
	$editor->setEditFields($tours_fields);

	$editor->setFieldOptions('year', array(
				array('key' => 'NULL', 'val' => 'Inactive'),
				array('key' => $config['FAIRYEAR'], 'val' => $config['FAIRYEAR'])));

//	print_r($e);
	print("<br>\n");

	/* Build an array of grades */
	$gradechoices = array();
	for($g = $config['mingrade']; $g <= $config['maxgrade']; $g++) {
		$gradechoices[] = array('key' => $g, 'val' => "Grade $g");
	}

	$editor->setFieldOptions("grade_min", $gradechoices);
	$editor->setFieldInputType("grade_min", 'select');
	$editor->setFieldOptions("grade_max", $gradechoices);
	$editor->setFieldInputType("grade_max", 'select');
}

/* Functions for $this */


function tours($tour_id=NULL)
{
	if($tour_id == NULL) {
		$this->id = FALSE;
	} else {
		$this->id = $tour_id;
	}
}

function tableEditorLoad()
{
	global $config;

	$id = $this->id;

//	print("Loading Judge ID  $id\n");

	$q=mysql_query("SELECT	tours.*
			FROM 	tours 
			WHERE 	tours.id='$id'");
	echo mysql_error();


	/* We assume that the field names in the array we want to return
	 * are the same as those in the database, so we'll turn the entire
	 * query into a single associative array */
	$j = mysql_fetch_assoc($q);

	return $j;
}

function tableEditorSave($data)
{
	/* If $this->id == false, then we need to INSERT a new record.
	 * if it's a number, then we want an UPDATE statement */
	global $tours_fields;
	global $config;

	$query = "";

	/* Construct an insert query if we have to */
	if($this->id == false) {
		$query = "INSERT INTO tours (id) VALUES ('')";
		mysql_query($query);
		$this->id = mysql_insert_id();
	}

	/* Give it a proper year when saving */

	/* Now just update the record */
	$query="UPDATE `tours` SET ";

	foreach($tours_fields AS $f=>$n) {
		$n = $data[$f];
		$query .= "`$f`=$n,";
	}
	//rip off the last comma
	$query=substr($query,0,-1);

	$query .= " WHERE id='{$this->id}'";

//	echo $query;
	mysql_query($query);

}

function tableEditorDelete()
{
	global $config;

	$id = $this->id;

	mysql_query("DELETE FROM tours_choice WHERE tour_id='$id' AND year=".$config['FAIRYEAR']."'");
	mysql_query("DELETE FROM tours WHERE id='$id' AND year='".$config['FAIRYEAR']."'");

	echo happy(i18n("Successfully removed tour from this year's fair"));
}

};

?>
