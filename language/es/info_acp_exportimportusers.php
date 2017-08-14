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
	'ACP_EXPORT_IMPORT_USERS'	=> 'Expotar - Importar Usuarios',

	'ACP_EXPORT_IMPORT_USERS_EXPLAIN'	=> 'Exportar - Importar Usuarios de otros usuarios de phpBB. Los usuarios existentes se actualizarán, se agregarán usuarios no existentes. Sólo puede actualizar, o insertar usuarios si todos son válidos. El archivo xml se eliminará después de insertar o actualizar.',
	'LOG_USER_ERROR'	=> '<strong>Usuarios no insertados o actualizados</strong><br />» %s',
	'LOG_USER_CHANGE'	=> '<strong>Usuarios actualizados</strong><br />» %s',
	'FH_HELPER_NOTICE'	=> '¡La aplicación Forumhulp helper no existe!<br />Descargar <a href="https://github.com/ForumHulp/helper" target="_blank">forumhulp/helper</a> y copie la carpeta helper dentro de la carpeta de extensión forumhulp.',
	'EXIMPORT_NOTICE'	=> '<div class="phpinfo"><p class="entry">Esta extensión reside en %1$s » %2$s » %3$s.</p></div>'
));

// Description of extension
$lang = array_merge($lang, array(
	'DESCRIPTION_PAGE'		=> 'Descripción',
	'DESCRIPTION_NOTICE'	=> 'Nota de la extensión',
	'ext_details' => array(
		'details' => array(
			'DESCRIPTION_1'		=> 'Exportar e importar usuario en formato XML',
			'DESCRIPTION_2'		=> 'Datos del usuario',
			'DESCRIPTION_3'		=> 'Datos del perfil',
			'DESCRIPTION_4'		=> 'Validar datos de importación',
		),
		'note' => array(
			'NOTICE_1'			=> 'Preparado para phpBB 3.2'
		)
	)
));
