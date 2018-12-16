<?php
/**
*
* @package Export Import users
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\exportimportusers\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	protected $user;

	/**
	* Constructor
	*/
	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.get_logs_modify_entry_data'	=> 'edit_additional_data',
		);
	}

	public function edit_additional_data($event)
	{
		$additional_data = $event['row'];
		foreach ($additional_data as $key => $value)
		{
			if ($key == 'log_data' && strpos($value, '{_SID}') && $value != '')
			{
				$additional_data['log_data'] = serialize(str_replace('{_SID}', $this->user->session_id, unserialize($value)));
			}
		}
		$event['row'] = $additional_data;
	}
}
