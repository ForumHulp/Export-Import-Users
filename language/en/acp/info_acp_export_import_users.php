<?php
/**
*
* @package Export Import users
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$revision = 'v3.1.0';
$name = 'Export_import_users ' . $revision;

$lang = array_merge($lang, array(
	'ACP_EXPORT_IMPORT_USERS'	=> 'Ex- Import Users',
	
	'ACP_EXPORT_IMPORT_USERS_EXPLAIN'	=> 'Ex- Import users from other phpBB usertables. Existing users will be updated, non excisting users will be added. You can only update or insert users if all are valid. The xml file will be deleted after insert or update. ',
	'LOG_USER_ERROR' => '<strong>Users not inserted or updated</strong><br />» %s',
	'LOG_USER_CHANGE' => '<strong>Users updated</strong><br />» %s'
));

?>
