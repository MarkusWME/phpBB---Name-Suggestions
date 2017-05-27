<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace pcgf\namesuggestions\event;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\factory;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/** @version 1.0.1 */
class listener implements EventSubscriberInterface
{
    /** @var factory $db Database object */
    protected $db;

    /** @var config $config Configuration object */
    protected $config;

    /** @var helper $helper Helper object */
    protected $helper;

    /** @var template $template Template object */
    protected $template;

    /** @var string $table_prefix The phpBB table prefix */
    protected $table_prefix;

    /** @var string $events_table Name Suggestions events table name */
    protected $events_table;

    /**
     * Listener constructor
     *
     * @access public
     * @since  1.0.0
     *
     * @param factory  $db           Database object
     * @param config   $config       Configuration object
     * @param helper   $helper       Helper Object
     * @param template $template     Template object
     * @param string   $table_prefix The phpBB table prefix
     * @param string   $events_table Name Suggestions events table name
     */
    public function __construct(factory $db, config $config, helper $helper, template $template, $table_prefix, $events_table)
    {
        $this->db = $db;
        $this->config = $config;
        $this->helper = $helper;
        $this->template = $template;
        $this->table_prefix = $table_prefix;
        $this->events_table = $events_table;
    }

    /**
     * Function that returns the subscribed events
     *
     * @access public
     * @since  1.0.0
     *
     * @return array Array with the subscribed events
     */
    static public function getSubscribedEvents()
    {
        global $phpbb_container;
        $db = $phpbb_container->get('dbal.conn');
        $events_table = $phpbb_container->getParameter('tables.pcgf.namesuggestions.events');
        $events = array();
        $query = 'SELECT event_name
                    FROM ' . $events_table . '
                    WHERE enabled = 1';
        // Cache the result for 365 days
        $result = $db->sql_query($query, 31536000);
        while ($event = $db->sql_fetchrow($result))
        {
            $events[$event['event_name']] = 'add_name_suggestions';
        }
        $db->sql_freeresult($result);
        return $events;
    }

    /**
     * Function that adds the name suggestions to the template
     *
     * @access public
     * @since  1.0.0
     *
     * @param array  $event      Event data
     * @param string $event_name The name of the event
     */
    public function add_name_suggestions($event, $event_name)
    {
        $selectors = '';
        $query = 'SELECT input_selector
                    FROM ' . $this->events_table . "
                    WHERE event_name = '" . $this->db->sql_escape($event_name) . "'";
        $result = $this->db->sql_query($query);
        while ($event_data = $this->db->sql_fetchrow($result))
        {
            $selectors .= ($selectors == '' ? '' : ';') . $event_data['input_selector'];
        }
        if ($selectors != '')
        {
            $this->template->assign_vars(array(
                'NAME_SUGGESTIONS'                => true,
                'PCGF_NAME_SUGGESTION_URL'        => $this->helper->route('pcgf_namesuggestions_controller'),
                'PCGF_NAME_SUGGESTION_IMAGE_SIZE' => $this->config['pcgf_namesuggestions_avatar_image_size'],
                'PCGF_NAME_SUGGESTION_EVENT'      => addslashes($event_name),
                'PCGF_NAME_SUGGESTION_SELECTORS'  => addslashes($selectors),
            ));
        }
        $this->db->sql_freeresult($result);
    }
}
