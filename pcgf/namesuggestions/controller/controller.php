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

/** @version 1.0.0 */
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
     */
    public function __construct(request $request, factory $db, user $user, config $config, $phpbb_root_path)
    {
        $this->request = $request;
        $this->db = $db;
        $this->user = $user;
        $this->config = $config;
        $this->phpbb_root_path = $phpbb_root_path;
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
                $count = 0;
                $search = $this->db->sql_escape($search);
                $query = 'SELECT *
					FROM ' . USERS_TABLE . '
					WHERE ' . $this->db->sql_in_set('user_type', array(USER_NORMAL, USER_FOUNDER)) . '
						AND username_clean ' . $this->db->sql_like_expression($this->db->get_any_char() . $search . $this->db->get_any_char()) . '
					ORDER BY username_clean ' . $this->db->sql_like_expression($search . $this->db->get_any_char()) . ' DESC, username DESC';
                $result = $this->db->sql_query($query);
                $default_avatar_url = $this->phpbb_root_path . 'styles/' . $this->user->style['style_path'] . '/theme/images/no_avatar.gif';
                if (!file_exists($default_avatar_url))
                {
                    $default_avatar_url = $this->phpbb_root_path . 'ext/pcgf/namesuggestions/styles/all/theme/images/no-avatar.gif';
                }
                if (!defined('PHPBB_USE_BOARD_URL_PATH'))
                {
                    define('PHPBB_USE_BOARD_URL_PATH', true);
                }
                while ($user = $this->db->sql_fetchrow($result))
                {
                    $avatar_image = phpbb_get_user_avatar($user);
                    if ($avatar_image == '')
                    {
                        $avatar_image = '<img src="' . $default_avatar_url . '"/>';
                    }
                    // Add the user to the user list
                    $suggestions['users'][$count++] = array('username' => $user['username'], 'user' => get_username_string('no_profile', $user['user_id'], $user['username'], $user['user_colour']), 'avatar' => $avatar_image);
                    if ($count == $this->config['pcgf_namesuggestions_suggestion_count'])
                    {
                        break;
                    }
                }
            }
        }
        $suggestions['event'] = $this->request->variable('event', '');
        $suggestions['selector'] = $this->request->variable('selector', '');
        $response->send($suggestions);
    }
}
