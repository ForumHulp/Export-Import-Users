<?php
/**
*
* @package Export Import users
* @copyright (c) 2014 ForumHulp.com
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace forumhulp\export_import_users\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
    /* @var \phpbb\controller\helper */
    protected $helper;
  
    /**
    * Constructor
    *
    * @param \phpbb\controller\helper    $helper        Controller helper object
    */
    public function __construct(\phpbb\controller\helper $helper)
    {
        $this->helper = $helper;
    }

    static public function getSubscribedEvents()
    {
        return array(
            'core.get_logs_modify_entry_data'	=> 'edit_additional_data',
		);
    }

    public function edit_additional_data($event)
    {
		global $_SID ;
		$additional_data = $event['row'];
		foreach($additional_data as $key => $value)
		{
			if ($key == 'log_data')
			{
				$additional_data['log_data'] = serialize(str_replace('{_SID}', $_SID, unserialize($value)));
			}
		}
		$event['row'] = $additional_data;
   }
}