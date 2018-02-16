<?
/* 
   This file is part of the 'Science Fair In A Box' project
   SFIAB Website: http://www.sfiab.ca

   Copyright (C) 2007 James Grant <james@lightbox.org>
   Copyright (C) 2009 David Grant <dave@lightbox.org>

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
 user_auth_required('committee', 'admin');
 require_once('xml.inc.php');

 function xml_dearray(&$array)
 {
//	echo "<pre>";print_r($array);echo "</pre>";
 	$keys = array_keys($array);
	foreach($keys as $k) {
		if(!is_array($array[$k])) {
			echo "Not array at key $k";
			exit;
		}

		/* Special cases, leave these as arrays of entries */
		if($k == 'award' || $k == 'prize') {
			foreach($array[$k] as &$a) {
				xml_dearray($a);
			}
			continue;
		}

		if(count($array[$k]) != 1) {
			echo "Unexpected multielement array, stop.";
			exit;
		};
		$array[$k] = $array[$k][0];

		if(is_array($array[$k])) {
			xml_dearray($array[$k]);
		}
	}
 }
	


 function curl_query($fair, $data, $ysc_url='')
 {
 	global $output;
 	switch($fair['type']) {
	case 'sfiab':
		$url = $fair['url'].'/remote.php';
		$var = 'json';
		$d = array();
		$d['auth'] = array('username' => $fair['username'],
				'password' => $fair['password']);
		$str = json_encode(array_merge($d, $data));
		break;
	case 'ysc':
		if($ysc_url == '')
			$url = $fair['url'];
		else 
			$url = $ysc_url;
		$var = 'xml';
		$output="";
		xmlCreateRecurse($data);
		$str = $output;
		break;
	default:
		echo "Unknown fair type {$fair['type']}";
		break;
	}

//	debug_("Curl Send: (type:{$fair['type']}=>$url ysc_url=>$ysc_url)  $str");

	$ch = curl_init(); /// initialize a cURL session
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_HEADER, 0); /// Header control
	curl_setopt ($ch, CURLOPT_POST, 1);  /// tell it to make a POST, not a GET
	curl_setopt ($ch, CURLOPT_POSTFIELDS, "$var=".urlencode($str));  /// put the query string here starting with "?"
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); /// This allows the output to be set into a variable $datastream
	curl_setopt ($ch, CURLOPT_POSTFIELDSIZE, 0);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 360);
	curl_setopt ($ch, CURLOPT_SSLVERSION, 3);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);
	$datastream = curl_exec ($ch); /// execute the curl session and return the output to a variable $datastream
	curl_close ($ch); /// close the curl session

//	debug_("Server Returned: ".urldecode($datastream));

 	switch($fair['type']) {
	case 'sfiab':
		$ret=json_decode(urldecode($datastream), true);
		break;
	case 'ysc':
		$datastream = str_replace(" standalone=\"yes\"","",$datastream);
		/* Return is XML, make a return array */
		$response=xml_parsexml($datastream);

		if(!is_array($response)) {
			$ret['message']=$datastream;
			$ret['error']=0;
			return $ret;
		}
		/* De-array everything */
		xml_dearray($response);
		$key = array_keys($response);

//		echo "<pre>";print_r($response);echo "</pre>";

		switch($key[0]) {
		case 'awardresponse':
			/* Full response */
			$ret = $response['awardresponse'];

			/* Undo variable to array */
			$ret['awards'] = $ret['awards']['award'];
			foreach($ret['awards'] as &$a)
				$a['prizes'] = $a['prizes']['prize'];

			$ret['error'] = 0;
			$ret['message'] = '';
			break;
		case 'awardwinnersresponse':
			/* Parse return */
			$ret['error'] = ($response['awardwinnersresponse']['status'] == 'failed') ? 1 : 0;
			$ret['message'] = $response['awardwinnersresponse']['statusmessage'];
			break;

		}
		break;
	}
//n	debug_("Returning: ".print_r($ret, true));
	return $ret;
 }
?>
