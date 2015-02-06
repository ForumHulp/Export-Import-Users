<?php
/**
*
* @package Export Import users
* Swedish translation by Holger (http://www.maskinisten.net)
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
	'ACP_EXPORT_IMPORT_USERS'	=> 'Exportera/importera användare',

	'ACP_EXPORT_IMPORT_USERS_EXPLAIN'	=> 'Exportera/importera användre från andra andra phpBB databastabeller. Existerande användare kommer att uppdateras, icke existerande användare läggs till. Du kan endast uppdatera eller lägga till användare om allt är validerat. XML-filen kommer att raderas efter proceduren. ',
	'LOG_USER_ERROR' => '<strong>Användare som ej uppdaterats eller lagts till</strong><br />» %s',
	'LOG_USER_CHANGE' => '<strong>Användare som har uppdaterats</strong><br />» %s'
));
