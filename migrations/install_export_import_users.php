<?php
/**
*
* @package Export Import users
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\exportimportusers\migrations;

class install_export_import_users extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['export_import_users_version']) && version_compare($this->config['export_import_users_version'], '3.1.0', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(
			array('module.add', array('acp', 'ACP_CAT_USERS', array(
					'module_basename'	=> '\forumhulp\exportimportusers\acp\exportimportusers_module',
					'module_langname'	=> 'ACP_EXPORT_IMPORT_USERS',
					'module_mode'		=> 'index',
			))),
			array('config.add', array('export_import_users_version', '3.1.0')),
		);
	}
}
