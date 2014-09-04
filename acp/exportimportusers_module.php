<?php
/**
*
* @package Export Import users
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\exportimportusers\acp;

class exportimportusers_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $cache;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx, $phpbb_container,  $phpbb_extension_manager, $disabled, $request;
		include($phpbb_root_path . 'ext/forumhulp/exportimportusers/vendor/functions_export_import_users.' . $phpEx);

		$submit	= (isset($_POST['submit'])) ? true : false;
		$action	= $request->variable('action', '');
		$user_ids = $request->variable('userid', array(0));

		$this->tpl_name = 'acp_export_import_users';

		$sql = 'SELECT field_name FROM ' . PROFILE_FIELDS_TABLE . ' WHERE field_active = 1';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$profilearay[] = $row['field_name'];
		}

		$filename = $phpbb_root_path . 'store/update_users.xml';
		$updated = $notupdated = $parsed = array();

		switch ($action)
		{
			case 'details':

			$user->add_lang(array('install', 'acp/extensions', 'migrator'));
			$ext_name = 'forumhulp/exportimportusers';
			$md_manager = new \phpbb\extension\metadata_manager($ext_name, $config, $phpbb_extension_manager, $template, $user, $phpbb_root_path);
			try
			{
				$this->metadata = $md_manager->get_metadata('all');
			}
			catch(\phpbb\extension\exception $e)
			{
				trigger_error($e, E_USER_WARNING);
			}

			$md_manager->output_template_data();

			try
			{
				$updates_available = $this->version_check($md_manager, $request->variable('versioncheck_force', false));

				$template->assign_vars(array(
					'S_UP_TO_DATE'		=> empty($updates_available),
					'S_VERSIONCHECK'	=> true,
					'UP_TO_DATE_MSG'	=> $user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $md_manager->get_metadata('display-name')),
				));

				foreach ($updates_available as $branch => $version_data)
				{
					$template->assign_block_vars('updates_available', $version_data);
				}
			}
			catch (\RuntimeException $e)
			{
				$template->assign_vars(array(
					'S_VERSIONCHECK_STATUS'			=> $e->getCode(),
					'VERSIONCHECK_FAIL_REASON'		=> ($e->getMessage() !== $user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
				));
			}

			$template->assign_vars(array(
				'U_BACK'				=> $this->u_action . '&amp;action=list',
			));

			$this->tpl_name = 'acp_ext_details';
			break;

			case 'delete':
				$parsed_array = readDatabase($filename);
				if (sizeof($parsed_array))
				{
					$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<USERS>\n\n";
					foreach ($parsed_array as $key => $value)
					{
						$value = get_object_vars($value);
						if (request_var('id', 0) == $value['user_id'])
						{
							unset($parsed_array[$key]);
						} else
						{
							$xml .= "\t<user>\n";
							$xml .= "\t\t<user_id>".$value['user_id']."</user_id>\n";
							$xml .= "\t\t<user_ip>".$value['user_ip']."</user_ip>\n";
							$xml .= "\t\t<user_regdate>".$value['user_regdate']."</user_regdate>\n";
							$xml .= "\t\t<username>".$value['username']."</username>\n";
							$xml .= "\t\t<user_email>".$value['user_email']."</user_email>\n";
							$xml .= "\t\t<user_birthday>".$value['user_birthday']."</user_birthday>\n";

							foreach($profilearay as $id => $fieldvalue)
							{
								$xml .= "\t\t<".$fieldvalue.">".isset($value[$fieldvalue]) ? $value[$fieldvalue] : ''."</".$fieldvalue.">\n";
							}

							$xml .= "\t\t<user_from>".$profile_fields[$value['user_id']]['phpbb_location']['value']."</user_from>\n";
							$xml .= "\t\t<user_website>".$profile_fields[$value['user_id']]['phpbb_website']['value']."</user_website>\n";
							$xml .= "\t\t<user_occ>".$profile_fields[$value['user_id']]['phpbb_occupation']['value']."</user_occ>\n";
							$xml .= "\t\t<user_password>".$value['user_password']."</user_password>\n";
							$xml .= "\t</user>\n\n";
						}
					}
					$xml .= "</USERS>";
					file_put_contents($filename, $xml);
				}
			break;

			case 'save':
				if ($submit && sizeof($user_ids))
				{
					$parsed_array = readDatabase($filename);
					if (sizeof($parsed_array))
					{
						$parsed = array();
						foreach ($parsed_array as $key => $value)
						{
							if (in_array($value['user_id'], $user_ids))
							{
								$parsed[$value['user_id']] = array(
									'user_ip'		=> $value['user_ip'],
									'user_regdate'	=> $value['user_regdate'],
									'username' 		=> $value['username'],
									'user_password'	=> $value['user_password'],
									'user_email'	=> $value['user_email'],
									'user_birthday'	=> $value['user_birthday']);
							} else
							{
								$sql = 'SELECT user_id, username, user_email FROM ' . USERS_TABLE . '
										WHERE (username_clean = "' . utf8_clean_string($value['username']) . '" AND user_email = "' . $value['user_email'] . '")';
								$result = $db->sql_query($sql);
								$row = $db->sql_fetchrow($result);
								$parsed[$row['user_id']] = array(
									'user_ip'		=> $value['user_ip'],
									'user_regdate'	=> $value['user_regdate'],
									'username' 		=> $value['username'],
									'user_password'	=> $value['user_password'],
									'user_birthday'	=> $value['user_birthday'],
									'user_email'	=> $value['user_email']);
							}
							$cp_data = array();
							foreach($profilearay as $id => $fieldvalue)
							{
								$parsed[$value['user_id']] += array($fieldvalue => utf8_normalize_nfc($value[$fieldvalue]));
							}
						}
					}
					unset($parsed_array);
					foreach ($user_ids as $userid)
					{
						$sql_aray = array(
									'user_ip'			=> $parsed[$userid]['user_ip'],
									'user_regdate'		=> ($parsed[$userid]['user_regdate']) ? $parsed[$userid]['user_regdate'] : time(),
									'username' 			=> $parsed[$userid]['username'],
									'username_clean'	=> utf8_clean_string($parsed[$userid]['username']),
									'user_email'		=> $parsed[$userid]['user_email'],
									'user_email_hash'	=> phpbb_email_hash($parsed[$userid]['user_email']),
									'user_password' 	=> $parsed[$userid]['user_password'],
									'user_birthday'		=> $parsed[$userid]['user_birthday']);

						$cp_data = array();
						foreach($profilearay as $id => $fieldvalue)
						{
							$cp_data['pf_' . $fieldvalue] = $parsed[$userid][$fieldvalue];
						}

						if ($request->variable('submit', '') == 'Insert')
						{
							$user_id = 0;
							$sql_aray += array(
								'user_permissions'	=> '',
								'user_timezone'		=> $config['board_timezone'],
								'user_dateformat'	=> $config['default_dateformat'],
								'user_lang'			=> $config['default_lang'],
								'user_style'		=> (int) $config['default_style'],
								'user_actkey'		=> '',
								'user_passchg'		=> time(),
								'user_options'		=> 230271,
								// We do not set the new flag here - registration scripts need to specify it
								'user_new'			=> 0,

								'user_inactive_reason'	=> 0,
								'user_inactive_time'	=> 0,
								'user_lastmark'			=> time(),
								'user_lastvisit'		=> 0,
								'user_lastpost_time'	=> 0,
								'user_lastpage'			=> '',
								'user_posts'			=> 0,
								'user_colour'			=> '',
								'user_avatar'			=> '',
								'user_avatar_type'		=> '',
								'user_avatar_width'		=> 0,
								'user_avatar_height'	=> 0,
								'user_new_privmsg'		=> 0,
								'user_unread_privmsg'	=> 0,
								'user_last_privmsg'		=> 0,
								'user_message_rules'	=> 0,
								'user_full_folder'		=> PRIVMSGS_NO_BOX,
								'user_emailtime'		=> 0,

								'user_notify'			=> 0,
								'user_notify_pm'		=> 1,
								'user_notify_type'		=> NOTIFY_EMAIL,
								'user_allow_pm'			=> 1,
								'user_allow_viewonline'	=> 1,
								'user_allow_viewemail'	=> 1,
								'user_allow_massemail'	=> 1,

								'user_sig'					=> '',
								'user_sig_bbcode_uid'		=> '',
								'user_sig_bbcode_bitfield'	=> '',

								'user_form_salt'			=> unique_id(),
							);

						//	$db->sql_return_on_error(true);
							$sql = 'INSERT INTO ' . USERS_TABLE . ' ' . $db->sql_build_array('INSERT', $sql_aray);
							$db->sql_query($sql);
							$user_id = $db->sql_nextid();
						//	$db->sql_return_on_error(false);

							if ($user_id )
							{
								$cp = $phpbb_container->get('profilefields.manager');
								$cp_data['user_id'] = (int) $user_id;
								$sql = 'INSERT INTO ' . PROFILE_FIELDS_DATA_TABLE . ' ' . $db->sql_build_array('INSERT', $cp->build_insert_sql_array($cp_data));
								$db->sql_query($sql);

								// Place into appropriate group...
								$sql = 'SELECT group_id FROM ' . GROUPS_TABLE . ' WHERE group_name = "REGISTERED" AND group_type = ' . GROUP_SPECIAL;
								$result = $db->sql_query($sql);
								$group_id = $db->sql_fetchfield('group_id');
								$db->sql_freeresult($result);

								$sql = 'INSERT INTO ' . USER_GROUP_TABLE . ' ' . $db->sql_build_array('INSERT', array(
									'user_id'		=> (int) $user_id,
									'group_id'		=> (int) $group_id,
									'user_pending'	=> 0)
								);
								$db->sql_query($sql);

								// Now make it the users default group...
								if (!function_exists('group_set_user_default'))
								{
									include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);
								}
								group_set_user_default($group_id, array($user_id), false);

								$updated[] = '<a href="/adm/index.php?i=users&mode=overview&u=' .$user_id . '&amp;sid={_SID}">' . $sql_aray['username'] . '</a>';
								unset($parsed[$userid]);
								set_config('newest_user_id', $user_id, true);
								set_config('newest_username', $sql_aray['username'], true);
								set_config_count('num_users', 1, true);
							}

						} else
						{
							$sql = 'SELECT user_id FROM ' . USERS_TABLE . ' WHERE user_id = ' . (int) $userid;
							$result = $db->sql_query($sql);
							$user_id = (int) $db->sql_fetchfield('user_id');

							$db->sql_query('UPDATE ' . USERS_TABLE . ' SET ' . $db->sql_build_array('UPDATE', $sql_aray) . ' WHERE user_id = ' . $user_id);
							$cp = $phpbb_container->get('profilefields.manager');
							$profile_fields = $cp->update_profile_field_data($user_id, $cp_data);

							$updated[] = '<a href="/adm/index.php?i=users&mode=overview&u=' .$user_id . '&amp;sid={_SID}">' . $sql_aray['username'] . '</a>';
							unset($parsed[$userid]);
						}
					}
					if (sizeof($updated))
					{
						add_log('admin', 'LOG_USER_CHANGE', implode(', ', $updated));
					}

					if (sizeof($parsed))
					{
						foreach ($parsed as $user_id => $value)
						{
							$notupdated[] = $value['username'];
						}
						add_log('admin', 'LOG_USER_ERROR', implode(', ', $notupdated));
					}
					@rename($filename, $phpbb_root_path . 'store/user_updates/'. ((sizeof($updated)) ? 'user' : 'nouser' ). '_update ' . date('d-m-Y H i s', time()) . '.xml');
				}
			break;

			case 'add_file':
				if ($submit)
				{
					@move_uploaded_file($_FILES['uploadfile']['tmp_name'], $phpbb_root_path . 'store/update_users.xml');
				}
			break;

			case 'export':
				$sql = 'SELECT user_id, user_ip, user_regdate, username, user_password, user_email, user_birthday FROM ' . USERS_TABLE . ' WHERE user_type <> 2 ORDER BY user_id';
				$result = $db->sql_query($sql);
				$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<USERS>\n\n";
				while ($row = $db->sql_fetchrow($result))
				{
					$cp = $phpbb_container->get('profilefields.manager');
					$profile_fields = $cp->grab_profile_fields_data($row['user_id']);

					$xml .= "\t<user>\n";
					$xml .= "\t\t<user_id>".$row['user_id']."</user_id>\n";
					$xml .= "\t\t<user_ip>".$row['user_ip']."</user_ip>\n";
					$xml .= "\t\t<user_regdate>".$row['user_regdate']."</user_regdate>\n";
					$xml .= "\t\t<username>".$row['username']."</username>\n";
					$xml .= "\t\t<user_email>".$row['user_email']."</user_email>\n";
					$xml .= "\t\t<user_birthday>".$row['user_birthday']."</user_birthday>\n";
					foreach($profilearay as $id => $fieldvalue)
					{
						$xml .= "\t\t<".$fieldvalue.">".$profile_fields[$row['user_id']][$fieldvalue]['value']."</".$fieldvalue.">\n";
					}
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
		} else if (sizeof($parsed))
		{
			$template->assign_vars(array(
				'S_ERROR'	=> true,
				'ERROR'		=> 'Not all users are imported / updated',
				'BOX'		=> 'errorbox'
			));
		} else if (!sizeof($updated && sizeof($error)))
		{
			$template->assign_vars(array(
				'S_ERROR'	=> true,
				'ERROR'		=> implode('<br />» ', $error),
				'BOX'		=> 'errorbox'
			));
		}  else if (sizeof($parsed_array) > $maxusertoupdate)
		{
			$template->assign_vars(array(
				'S_ERROR'	=> true,
				'ERROR'		=> '<br />» More then ' . $maxusertoupdate . ' user\'s to update!',
				'BOX'		=> 'errorbox'
			));
		} else if (!file_exists($filename))
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
			foreach ($parsed_array as $key => $value)
			{
				$pass = ((strlen($value['user_password']) == 34 && (substr($value['user_password'], 0,3) == '$H$' ||
						substr($value['user_password'], 0,3) == '$P$')) || (strlen($value['user_password']) == 60 &&
						(substr($value['user_password'], 0,3) == '$2y')))  ? ' Password ok' : 'Password not ok';
				$sql = 'SELECT user_id, username, user_password, user_email FROM ' . USERS_TABLE . '
						WHERE user_id = ' . $value['user_id'] . ' OR (username_clean = "' . utf8_clean_string($value['username']) . '" AND user_email = "' . $value['user_email'] . '")';

				$result = $db->sql_query($sql);
				$row = $db->sql_fetchrow($result);
				$disabledinsert = $disableupdate = true;
				if (!$row)
				{
					$disabled = validate_import($value['username'], $value['user_password'], $value['user_email'], true);
					$template->assign_block_vars('membersnotfound', array(
						'NEWID'		=> $value['user_id'],
						'NEWNAME' 	=> $value['username'],
						'NEWEMAIL'	=> $value['user_email'],
						'NEWCITY' 	=> $value['user_from'],
						'TOOLTIP'	=> 'Username: ' . htmlspecialchars($value['username']) . "\n" . 'Password: ' . $pass . "\n" . 'Emailaddress: ' . htmlspecialchars($value['user_email']) . "\n" .	(sizeof($disabled) ? 'Errors:' . implode("\n",  $disabled) : ''),
						'VALIDATED'	=> (!sizeof($disabled)) ? '&radic;' : '<a style="color:red;" href="'.$this->u_action . '&amp;action=delete&amp;id='.$value['user_id'].'">Delete</a>'
					));
					$disabledinsert = $disabledinsert & !sizeof($disabled);
				} else
				{
					$cp = $phpbb_container->get('profilefields.manager');
					$profile_fields = $cp->grab_profile_fields_data($row['user_id']);

					$disabled = validate_import($value['username'], $value['user_password'], $value['user_email']);
					$template->assign_block_vars('members', array(
						'ID'		=> $row['user_id'],
						'NEWID'		=> $value['user_id'],
						'NAME' 		=> $row['username'],
						'NEWNAME' 	=> $value['username'],
						'EMAIL'		=> $row['user_email'],
						'NEWEMAIL'	=> $value['user_email'],
						'CITY' 		=> isset($profile_fields[$row['user_id']]['phpbb_location']['value']) ? $profile_fields[$row['user_id']]['phpbb_location']['value'] : '',
						'NEWCITY' 	=> isset($value['phpbb_location']) ? $value['phpbb_location'] : '',
						'TOOLTIP'	=> 'Username: ' . htmlspecialchars($value['username']) . "\n" . 'Password: ' . $pass . "\n" . 'Emailaddress: ' . htmlspecialchars($value['user_email']) . "\n" . (sizeof($disabled) ? 'Errors:' . implode("\n",  $disabled) : ''),
						'VALIDATED'	=> (!sizeof($disabled)) ? '&radic;' : '<a style="color:red;" href="'.$this->u_action . '&amp;action=delete&amp;id='.$value['user_id'].'">Delete</a>'
					));
					$disableupdate = $disableupdate & !sizeof($disabled);
				}
			}
			$template->assign_vars(array('DISABLEINSERT' => ($disabledinsert) ? '' : ' disabled="disabled"',
										'DISABLEUPDATE' => ($disableupdate) ?  '' : 'disabled="disabled"',
										'MAXEXISTINGUSERS' => $maxusertoupdate));
		}

	$template->assign_vars(array('U_ACTION' =>  $this->u_action, 'EXPORTURL' => $this->u_action . '&amp;action=export'));
	}

	/**
	* Check the version and return the available updates.
	*
	* @param \phpbb\extension\metadata_manager $md_manager The metadata manager for the version to check.
	* @param bool $force_update Ignores cached data. Defaults to false.
	* @param bool $force_cache Force the use of the cache. Override $force_update.
	* @return string
	* @throws RuntimeException
	*/
	protected function version_check(\phpbb\extension\metadata_manager $md_manager, $force_update = false, $force_cache = false)
	{
		global $cache, $config, $user;
		$meta = $md_manager->get_metadata('all');

		if (!isset($meta['extra']['version-check']))
		{
			throw new \RuntimeException($this->user->lang('NO_VERSIONCHECK'), 1);
		}

		$version_check = $meta['extra']['version-check'];

		$version_helper = new \phpbb\version_helper($cache, $config, $user);
		$version_helper->set_current_version($meta['version']);
		$version_helper->set_file_location($version_check['host'], $version_check['directory'], $version_check['filename']);
		$version_helper->force_stability($config['extension_force_unstable'] ? 'unstable' : null);

		return $updates = $version_helper->get_suggested_updates($force_update, $force_cache);
	}
}
