<?php
/**
*
* Export / Import Users extension for the phpBB Forum Software package.
* French translation by Galixte (http://www.galixte.com)
*
* @copyright (c) 2015 phpBB ForumHulp <http://www.forumhulp.com>
* @license GNU General Public License, version 2 (GPL-2.0)
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
	'ACP_EXPORT_IMPORT_USERS'	=> 'Export / Import d’utilisateurs',
	'ACP_EXPORT_IMPORT_USERS_EXPLAIN'	=> 'Cette extension permet l’export et l’import d’utilisateurs provenant d’autres tables utilisateurs de phpBB. Les utilisateurs existants seront mis à jour et les non existants seront créés. Il est possible uniquement de mettre à jour ou d’insérer des utilisateurs si toutes les informations sont validées. Le fichier XML sera supprimé après l’insertion ou la mise à jour. ',
	'USERS_UPDATED'		=> '%s users updated',
	'NOT_ALL_UPDATED'	=> 'Not all users are imported / updated',
	'MORE_THEN'			=> 'More then %s user\'s to update!',
	'FILE_NOT_EXCISTS'	=> 'File "update_users.xml" doesn\'t excists!',
	'PASS_OK'			=> 'Password ok',
	'PASS_NOK'			=> 'Password not ok',
	'LOG_USER_ERROR'	=> '<strong>Utilisateurs non insérés ni mis à jour</strong><br />» %s',
	'LOG_USER_CHANGE'	=> '<strong>Utilisateurs mis à jour</strong><br />» %s',
	'FH_HELPER_NOTICE'	=> 'L’extension : « Forumhulp Helper » n’est pas installée !<br />Il est nécessaire de télécharger son archive disponible sur cette page : <a href="https://github.com/ForumHulp/helper" target="_blank">Forumhulp Helper</a>, puis de l’envoyer sur son espace FTP et de l’activer.',
	'EXIMPORT_NOTICE'	=> '<div class="phpinfo"><p class="entry">Cette extension se trouve dans : %1$s » %2$s » %3$s.</p></div>'
));

// Description of extension
$lang = array_merge($lang, array(
	'DESCRIPTION_PAGE'		=> 'Description',
	'DESCRIPTION_NOTICE'	=> 'Note de l’extension',
	'ext_details' => array(
		'details' => array(
			'DESCRIPTION_1'		=> 'Export / Import d’utilisateurs au format XML',
			'DESCRIPTION_2'		=> 'Données utilisateur(s)',
			'DESCRIPTION_3'		=> 'Données de profil(s)',
			'DESCRIPTION_4'		=> 'Valide les données importées',
		),
		'note' => array(
			'NOTICE_1'			=> 'Prêt pour phpBB 3.2'
		)
	)
));
