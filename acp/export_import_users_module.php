<?php
/**
*
* @package Export Import users
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\export_import_users\acp;

class export_import_users_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $table_prefix, $disabled, $request;
		include('functions_export_import_users.' . $phpEx);
	
		$submit	= (isset($_POST['submit'])) ? true : false;
		$action	= $request->variable('action', '');
		$user_ids = $request->variable('userid', array(0));

		$this->tpl_name = 'acp_export_import_users';
		$filename = $phpbb_root_path . 'store/update_users.xml';
		$updated = $notupdated = $parsed = array();

		switch ($action)
		{
			case 'save':
				if ($submit && sizeof($user_ids))
				{
				}
			break;
			
			case 'add_file':
				if ($submit)
				{
					@move_uploaded_file($_FILES['uploadfile']['tmp_name'], $phpbb_root_path . 'store/update_users.xml');
				}
			break;
			
			case 'export':
				$sql = 'SELECT user_id, username, user_password, user_from, user_website, user_occ, user_email FROM ' . USERS_TABLE . ' WHERE user_type <> 2 ORDER BY user_id';
				$result = $db->sql_query($sql);
				$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<USERS>\n\n"; 
				while ($row = $db->sql_fetchrow($result))
				{
					$xml .= "\t<user>\n"; 
					$xml .= "\t\t<user_id>".$row['user_id']."</user_id>\n";   
					$xml .= "\t\t<username>".$row['username']."</username>\n"; 
					$xml .= "\t\t<user_email>".$row['user_email']."</user_email>\n";   
					$xml .= "\t\t<user_from>".$row['user_from']."</user_from>\n";     
					$xml .= "\t\t<user_website>".$row['user_website']."</user_website>\n";     
					$xml .= "\t\t<user_occ>".$row['user_occ']."</user_occ>\n";     
					$xml .= "\t\t<user_password>".$row['user_password']."</user_password>\n";     
					$xml .= "\t</user>\n\n";   
				} 
				$xml .= "</USERS>"; 
				
				header('Content-type: text/xml');
				header('Content-Disposition: attachment; filename="export_users.xml"');
				echo $xml; 
				exit;
			break;
		}
		
		$error = array();
		$maxusertoupdate = 200;
		$parsed_array = (file_exists($filename)) ? readDatabase($filename) : array();

		if (sizeof($updated))
		{
			$template->assign_vars(array(
				'S_ERROR'	=> true,
				'ERROR'		=> sizeof($updated) . ' users updated',
				'BOX'		=> 'successbox'
			));	
		} elseif (sizeof($parsed))
		{
			$template->assign_vars(array(
				'S_ERROR'	=> true,
				'ERROR'		=> 'Not all users are imported / updated',
				'BOX'		=> 'errorbox'
			));	
		} elseif (!sizeof($updated && sizeof($error)))
		{
			$template->assign_vars(array(
				'S_ERROR'	=> true,
				'ERROR'		=> implode('<br />» ', $error),
				'BOX'		=> 'errorbox'
			));	
		}  elseif (sizeof($parsed_array) > $maxusertoupdate)
		{
			$template->assign_vars(array(
				'S_ERROR'	=> true,
				'ERROR'		=> '<br />» More then ' . $maxusertoupdate . ' user\'s to update!',
				'BOX'		=> 'errorbox'
			));	
		} elseif (!file_exists($filename))
		{
			$template->assign_vars(array(
				'S_ERROR'	=> true,
				'ERROR'		=> 'File "update_users.xml" doesn\'t excists!',
				'BOX'		=> 'errorbox'
			));	
			$parsed_array = array();
		}

		if (sizeof($parsed_array))
		{
			$disabled = false;
			foreach ($parsed_array as $key => $value)
			{
				$value = get_object_vars($value);
				
				$pass = ((strlen($value['user_password']) == 34 && (substr($value['user_password'], 0,3) == '$H$' || 
						substr($value['user_password'], 0,3) == '$P$')) || (strlen($value['user_password']) == 60 && 
						(substr($value['user_password'], 0,3) == '$2y')))  ? ' Password ok' : 'Password not ok';
				$sql = 'SELECT user_id, username, user_password, user_email, user_from FROM ' . $table_prefix . 'users 
						WHERE user_id = ' . $value['user_id'] . ' OR (username = "' . $value['username'] . '" AND user_email = "' . $value['user_email'] . '")';
				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				if (!$row)
				{
					$template->assign_block_vars('membersnotfound', array(
						'ID'		=> $row['user_id'],
						'NEWID'		=> $value['user_id'],
						'NEWNAME' 	=> $value['username'],
						'NEWEMAIL'	=> $value['user_email'],
						'NEWCITY' 	=> $value['user_from'],
						'TOOLTIP'	=> 'Username: ' . $value['username'] . "\n" . 'Password: ' . $pass . "\n" . 'Emailaddress: ' . $value['user_email'] . "\n",
						'VALIDATED'	=> (validate_import($value['username'], $value['user_password'], $value['user_email'])) ? '&radic;' : ''
					));
				} else
				{
					$template->assign_block_vars('members', array(
						'ID'		=> $row['user_id'],
						'NEWID'		=> $value['user_id'],
						'NAME' 		=> $row['username'],
						'NEWNAME' 	=> $value['username'],
						'EMAIL'		=> $row['user_email'],
						'NEWEMAIL'	=> $value['user_email'],
						'CITY' 		=> $row['user_from'],
						'NEWCITY' 	=> $value['user_from'],
						'TOOLTIP'	=> 'Username: ' . $value['username'] . "\n" . 'Password: ' . $pass . "\n" . 'Emailaddress: ' . $value['user_email'] . "\n",
						'VALIDATED'	=> (validate_import($value['username'], $value['user_password'], $value['user_email'])) ? '&radic;' : ''
					));
				}
			}
			$template->assign_vars(array('DISABLED' => ($disabled) ? '' : ' disabled="disabled"', 'MAXEXISTINGUSERS' => $maxusertoupdate));
		}

	$template->assign_vars(array('EXPORTURL' => $this->u_action . '&amp;action=export'));
	}
}
?>
