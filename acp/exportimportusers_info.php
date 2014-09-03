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

class exportimportusers_info
{
	function module()
	{
		return array(
			'filename'	=> '\forumhulp\exportimportusers\acp\exportimportusers_module',
			'title'		=> 'ACP_EXPORT_IMPORT_USERS',
			'version'	=> '3.1.0',
			'modes'     => array('index' => array('title' => 'ACP_EXPORT_IMPORT_USERS', 'auth' => 'acl_a_board', 'cat' => array('ACP_CAT_USERS'))
			),
		);
	}
}
