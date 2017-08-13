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

$lang = array_merge($lang, array(
	'ACP_EXPORT_IMPORT_USERS'	=> 'Ex- Import Users',

	'ACP_EXPORT_IMPORT_USERS_EXPLAIN'	=> 'Ex- Import users from other phpBB usertables. Existing users will be updated, non existing users will be added. You can only update or insert users if all are valid. The xml file will be deleted after insert or update.',
	'LOG_USER_ERROR'	=> '<strong>Users not inserted or updated</strong><br />» %s',
	'LOG_USER_CHANGE'	=> '<strong>Users updated</strong><br />» %s',
	'FH_HELPER_NOTICE'	=> 'Forumhulp helper application does not exist!<br />Download <a href="https://github.com/ForumHulp/helper" target="_blank">forumhulp/helper</a> and copy the helper folder to your forumhulp extension folder.',
	'EXIMPORT_NOTICE'	=> '<div class="phpinfo"><p class="entry">This extension resides in %1$s » %2$s » %3$s.</p></div>'
));

// Description of extension
$lang = array_merge($lang, array(
	'DESCRIPTION_PAGE'		=> 'Description',
	'DESCRIPTION_NOTICE'	=> 'Extension note',
	'ext_details' => array(
		'details' => array(
			'DESCRIPTION_1'		=> 'Ex- and Import users in xmlformat',
			'DESCRIPTION_2'		=> 'Userdata',
			'DESCRIPTION_3'		=> 'Profiledata',
			'DESCRIPTION_4'		=> 'Validate import-data',
		),
		'note' => array(
			'NOTICE_1'			=> 'phpBB 3.2 ready'
		)
	)
));
