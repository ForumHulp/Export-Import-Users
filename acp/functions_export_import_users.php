<?php
/**
*
* @package Export Import users
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

class export_import_users_update {
	var $user_id;
	var $username;
	var $user_email;
	var $user_from;
	var $user_website;
	var $user_occ;
	var $user_password;
    
    function export_import_users_update ($aa) 
    {
        foreach ($aa as $k => $v) $this->$k = $aa[$k];
    }
}

function readDatabase($filename) 
{
    // read the XML database of asaf_updates
    $data = implode('', file($filename));
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, $data, $values, $tags);
    xml_parser_free($parser);

    // loop through the structures
    foreach ($tags as $key => $val) 
	{
        if ($key == "user") {
            for ($i = 0; $i < count($val); $i+= 2) 
			{
                $offset = $val[$i] + 1;
                $len = $val[$i + 1] - $offset;
                $tdb[] = parse_user(array_slice($values, $offset, $len));
            }
        } else 
		{
            continue;
        }
    }
    return $tdb;
}

function parse_user($mvalues) 
{

    for ($i = 0; $i < count($mvalues); $i++) 
	{
        $user_values[$mvalues[$i]['tag']] = (isset($mvalues[$i]['value'])) ? $mvalues[$i]['value'] : '';
    }
    return new export_import_users_update($user_values);
}

function validate_import($importname = '', $newpassword = '', $newemail = '')
{
	global $db, $config, $phpbb_root_path, $phpEx,  $disabled;
	$enablename = $enablepass = $enableemail = false;
	if (!function_exists('validate_string'))
	{
		include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);
	}

	if (validate_string($importname, false, $config['min_name_chars'], $config['max_name_chars']) === false && !validate_username($importname))
	{
		$enablename = true;
	}
	if (filter_var($newemail, FILTER_VALIDATE_EMAIL))
	{
		$enableemail = true;
	}
	
	if (strlen($newpassword) == 60 || strlen($newpassword) == 34)
	{
		if ((strlen($newpassword) == 34 && (substr($newpassword, 0,3) == '$H$' || substr($newpassword, 0,3) == '$P$')) || (strlen($newpassword) == 60 && (substr($newpassword, 0,3) == '$2y')))
		{
			$enablepass = true;
			$disabled = $enablename & $enablepass & $enableemail;
			return $enablename & $enablepass & $enableemail;
		}
	} else 
	{
		$disabled = false;
		return false;
	}
}
?>
