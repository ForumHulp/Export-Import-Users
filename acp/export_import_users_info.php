<?php
/**
*
* @package Export Import users
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @package module_install
*/

namespace forumhulp\exportimportusers\acp;

class export_import_users_info
{
	function module()
	{
		return array(
			'filename'	=> '\forumhulp\export_import_users\acp\export_import_users_module',
			'title'		=> 'ACP_EXPORT_IMPORT_USERS',
			'version'	=> '3.1.0',
			'modes'     => array('index' => array('title' => 'ACP_EXPORT_IMPORT_USERS', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_USERS'))
			),
		);
	}
}
