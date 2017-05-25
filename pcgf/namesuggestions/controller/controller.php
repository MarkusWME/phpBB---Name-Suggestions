<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace pcgf\namesuggestions\controller;

use phpbb\config\config;
use phpbb\db\driver\factory;
use phpbb\json_response;
use phpbb\request\request;
use phpbb\user;

/** @version 1.0.1 */
class controller
{
    /** @var request $request Request object */
    protected $request;

    /** @var factory $db Database object */
    protected $db;

    /** @var user $user User object */
    protected $user;

    /** @var  config $config Configuration object */
    protected $config;

    /** @var  string $phpbb_root_path The forum root path */
    protected $phpbb_root_path;

    /** @var string $table_prefix The phpBB table prefix */
    protected $table_prefix;

    /** @var string $events_table Name Suggestions events table name */
    protected $events_table;

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     *
     * @param request $request         Request object
     * @param factory $db              Database object
     * @param user    $user            User object
     * @param config  $config          Configuration object
     * @param string  $phpbb_root_path The forum root path
     * @param string  $table_prefix    The phpBB table prefix
     * @param string  $events_table    Name Suggestions events table name
     */
    public function __construct(request $request, factory $db, user $user, config $config, $phpbb_root_path, $table_prefix, $events_table)
    {
        $this->request = $request;
        $this->db = $db;
        $this->user = $user;
        $this->config = $config;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->table_prefix = $table_prefix;
        $this->events_table = $events_table;
    }

    /**
     * Function to get names matching a given username
     *
     * @access public
     * @since  1.0.0
     */
    public function get_suggestions()
    {
        $response = new json_response();
        $suggestions = array();
        // Only allow AJAX requests
        if ($this->request->is_ajax())
        {
            $search = strtolower($this->request->variable('search', '', true));
            if (strlen($search) > 0)
            {
                // Search if a name is given
                $event = $this->db->sql_escape($this->request->variable('event', ''));
                $selector = $this->db->sql_escape($this->request->variable('selector', ''));
                $default_avatar_url = $this->phpbb_root_path . 'styles/' . $this->user->style['style_path'] . '/theme/images/no_avatar.gif';
                if (!file_exists($default_avatar_url))
                {
                    $default_avatar_url = $this->phpbb_root_path . (strpos($event, 'core.adm') === 0 ? '../' : '') . 'ext/pcgf/namesuggestions/styles/all/theme/images/no-avatar.gif';
                }
                else if (strpos($event, 'core.adm') === 0)
                {
                    $default_avatar_url = $this->phpbb_root_path . '../styles/' . $this->user->style['style_path'] . '/theme/images/no_avatar.gif';
                }
                if (!defined('PHPBB_USE_BOARD_URL_PATH'))
                {
                    define('PHPBB_USE_BOARD_URL_PATH', true);
                }
                $query = 'SELECT suggest_users, suggest_groups
                            FROM ' . $this->events_table . "
                            WHERE event_name = '" . $event . "'
                                AND input_selector = '" . $selector . "'";
                $result = $this->db->sql_query($query);
                $suggest = $this->db->sql_fetchrow($result);
                $this->db->sql_freeresult($result);
                if ($suggest)
                {
                    if ($suggest['suggest_users'] == 1)
                    {
                        $suggestions['users'] = array();
                        $count = 0;
                        $search = $this->db->sql_escape($search);
                        $query = 'SELECT *
                            FROM ' . USERS_TABLE . '
                            WHERE ' . $this->db->sql_in_set('user_type', array(USER_NORMAL, USER_FOUNDER)) . '
                                AND username_clean ' . $this->db->sql_like_expression($this->db->get_any_char() . $search . $this->db->get_any_char()) . '
                            ORDER BY username_clean ' . $this->db->sql_like_expression($search . $this->db->get_any_char()) . ' DESC, username DESC';
                        $result = $this->db->sql_query($query);
                        while ($user = $this->db->sql_fetchrow($result))
                        {
                            $avatar_image = phpbb_get_user_avatar($user);
                            if ($avatar_image == '')
                            {
                                $avatar_image = '<img src="' . $default_avatar_url . '"/>';
                            }
                            // Add the user to the suggestion list
                            $suggestions['users'][$count++] = array(
                                'username' => $user['username'],
                                'user'     => get_username_string('no_profile', $user['user_id'], $user['username'], $user['user_colour']),
                                'avatar'   => $avatar_image,
                            );
                            if ($count == $this->config['pcgf_namesuggestions_suggestion_count'])
                            {
                                break;
                            }
                        }
                        $this->db->sql_freeresult($result);
                    }
                    if ($suggest['suggest_groups'] == 1)
                    {
                        $suggestions['groups'] = array();
                        $count = 0;
                        $query = 'SELECT *
                            FROM ' . GROUPS_TABLE . '
                            WHERE LOWER(group_name) ' . $this->db->sql_like_expression($this->db->get_any_char() . $search . $this->db->get_any_char());
                        $result = $this->db->sql_query($query);
                        while ($group = $this->db->sql_fetchrow($result))
                        {
                            $avatar_image = phpbb_get_group_avatar($group);
                            if ($avatar_image == '')
                            {
                                $avatar_image = '<img src="' . $default_avatar_url . '"/>';
                            }
                            $group_name = $group['group_type'] == GROUP_SPECIAL ? $this->user->lang('G_' . $group['group_name']) : $group['group_name'];
                            // Add the group to the suggestion list
                            $suggestions['groups'][$count++] = array(
                                'groupname' => $group_name,
                                'group'     => '<span' . ($group['group_colour'] != '' ? ' style="color: #' . $group['group_colour'] . '"' : '') . '>' . $group_name . '</span>',
                                'avatar'    => $avatar_image,
                            );
                            if ($count == $this->config['pcgf_namesuggestions_suggestion_count'])
                            {
                                break;
                            }
                        }
                    }
                }
            }
        }
        $response->send($suggestions);
    }
}
