<?php
/**
*
* @package Export Import users
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

function export_import_users_update($aa)
{
	foreach ($aa as $k => $v)
	{
		$this->$k = $aa[$k];
	}
}

function readDatabase($filename, $time = false)
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
		if ($key == "user")
		{
			for ($i = 0; $i < count($val); $i+= 2)
			{
				$offset = $val[$i] + 1;
				$len = $val[$i + 1] - $offset;
				$ik = parse_user(array_slice($values, $offset, $len));
				($time) ? $ik['time'] = $time : null;
				$tdb[] = $ik;
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
	return $user_values;
}

function history($dir)
{
	$history = array();
	$files = @scandir($dir);

	foreach ($files as $file)
	{
		if (strpos($file, '.xml') !== false)
		{
			$time = strtotime(substr(str_replace(array('user_update ', '.xml'), array('', ''), $file), 0, -9));
			$his = readDatabase($dir . '/' . $file, $time);
			$history = array_merge($history, $his);
		}
	}

	$history = array_msort($history, array('time' => SORT_DESC, 'username' => SORT_ASC));
	return $history;
}

function array_msort($array, $cols)
{
	$colarr = array();
	foreach ($cols as $col => $order)
	{
		$colarr[$col] = array();
		foreach ($array as $k => $row) {$colarr[$col]['_'.$k] = strtolower($row[$col]);}
	}
	$eval = 'array_multisort(';
	foreach ($cols as $col => $order)
	{
		$eval .= '$colarr[\''.$col.'\'],'.$order.',';
	}
	$eval = substr($eval,0,-1).');';
	eval($eval);
	$ret = array();
	foreach ($colarr as $col => $arr)
	{
		foreach ($arr as $k => $v)
		{
			$k = substr($k,1);
			if (!isset($ret[$k])) $ret[$k] = $array[$k];
			$ret[$k][$col] = $array[$k][$col];
		}
	}
	return $ret;
}

function validate_import($importname = '', $newpassword = '', $newemail = '', $checkdbname = false)
{
	global $db, $config, $user, $phpbb_root_path, $phpEx;
	if (!function_exists('validate_string'))
	{
		include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);
	}

	$user->add_lang(array('ucp'));
	$error = array();
	$err = validate_string($importname, false, $config['min_name_chars'], $config['max_name_chars']);
	($err) ? $error[] = $user->lang[$err] : null;
	$err = validate_username($importname, 'allowed name');
	($checkdbname && $err == 'USERNAME_TAKEN') ? $error[] = $user->lang[$err . '_USERNAME'] : null;
	($err && $err != 'USERNAME_TAKEN') ? $error[] = $user->lang[$err . '_USERNAME'] : null;
	$err = filter_var($newemail, FILTER_VALIDATE_EMAIL);
	(!$err) ? $error[] = $user->lang['NO_EMAILS_DEFINED'] : null;
	if (strlen($newpassword) == 60 || strlen($newpassword) == 34)
	{
		if ((strlen($newpassword) == 34 && (substr($newpassword, 0,3) == '$H$' || substr($newpassword, 0,3) == '$P$')) || (strlen($newpassword) == 60 && (substr($newpassword, 0,3) == '$2y')))
		{
			$err = false;
		}
	} else
	{
		$err = $user->lang['WRONG_PASSWORD'];
	}
	($err) ? $error[] = $err : null;
	return $error;
}
