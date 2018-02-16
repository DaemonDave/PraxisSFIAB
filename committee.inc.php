<?

function committee_auth_has_access($access="")
{

	switch($access) {
	case 'config': return ($_SESSION['access_config'] == 'yes') ? true : false;
	case 'admin': return ($_SESSION['access_admin'] == 'yes') ? true : false;
	case 'super': return ($_SESSION['access_super'] == 'yes') ? true : false;
	}

	return false;
}

function committee_status_update(&$u)
{
	global $config;

	if(   user_personal_info_status($u) == 'complete')
		$u['committee_complete'] = 'yes';
	else
		$u['committee_complete'] = 'no';

	return ($u['committee_complete'] == 'yes') ? 'complete' : 'incomplete';
}


?>
