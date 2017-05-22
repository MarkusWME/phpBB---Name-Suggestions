<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace pcgf\namesuggestions\migrations;

use phpbb\db\migration\migration;

/** @version 1.0.0 */
class release_1_0_0 extends migration
{
    const NAMESUGGESTIONS_EVENTS_TABLE = 'namesuggestions_events';

    /**
     * Function that checks if the extension has been effectively installed
     *
     * @access public
     * @since  1.0.0
     *
     * @return bool If the extension has been effectively installed
     */
    public function effectively_installed()
    {
        return isset($this->config['pcgf_namesuggestions_suggestion_count'], $this->config['pcgf_namesuggestions_avatar_image_size']);
    }

    /**
     * Function for building the dependency tree
     *
     * @access public
     * @since  1.0.0
     *
     * @return array Dependency data
     */
    static public function depends_on()
    {
        return array('\phpbb\db\migration\data\v31x\v311');
    }

    /**
     * Function that updates module data and configuration values
     *
     * @access public
     * @since  1.0.0
     *
     * @return array Update information array
     */
    public function update_data()
    {
        return array(
            array('config.add', array('pcgf_namesuggestions_suggestion_count', 5)),
            array('config.add', array('pcgf_namesuggestions_avatar_image_size', 20)),
            array('module.add', array(
                'acp',
                'ACP_CAT_DOT_MODS',
                'ACP_CAT_PCGF_NAMESUGGESTIONS',
            )),
            array('module.add', array(
                'acp',
                'ACP_CAT_PCGF_NAMESUGGESTIONS',
                array(
                    'module_basename' => '\pcgf\namesuggestions\acp\namesuggestions_module',
                    'module_langname' => 'ACP_PCGF_NAMESUGGESTIONS_ADD',
                    'module_mode'     => 'add',
                    'module_display'  => 0,
                ),
            )),
            array('module.add', array(
                'acp',
                'ACP_CAT_PCGF_NAMESUGGESTIONS',
                array(
                    'module_basename' => '\pcgf\namesuggestions\acp\namesuggestions_module',
                    'module_langname' => 'ACP_PCGF_NAMESUGGESTIONS_EDIT',
                    'module_mode'     => 'edit',
                ),
            )),
            array('custom', array(array($this, 'insert_default_events'))),
        );
    }

    /**
     * Function that inserts the needed tables
     *
     * @access public
     * @since  1.0.0
     *
     * @return array Array with information about the table to insert
     */
    public function update_schema()
    {
        return array(
            'add_tables' => array(
                $this->table_prefix . self::NAMESUGGESTIONS_EVENTS_TABLE => array(
                    'COLUMNS'     => array(
                        'event_name'     => array('VCHAR_UNI', ''),
                        'input_selector' => array('VCHAR_UNI', ''),
                        'description'    => array('VCHAR_UNI', ''),
                        'suggest_users'  => array('BOOL', 1),
                        'suggest_groups' => array('BOOL', 0),
                        'enabled'        => array('BOOL', 1),
                    ),
                    'PRIMARY_KEY' => 'event_name, input_selector',
                ),
            ),
        );
    }

    /**
     * Function that reverts the table insertion
     *
     * @access public
     * @since  1.0.0
     *
     * @return array Array that contains the revert data
     */
    public function revert_schema()
    {
        return array(
            'drop_tables' => array(
                $this->table_prefix . self::NAMESUGGESTIONS_EVENTS_TABLE,
            ),
        );
    }

    /**
     * Function that inserts the default events
     *
     * @access public
     * @since  1.0.0
     */
    public function insert_default_events()
    {
        $insert_data = array(
            array(
                'event_name'     => 'core.ucp_pm_compose_modify_data',
                'input_selector' => '#username_list',
                'description'    => 'PM Name Suggestions',
                'suggest_users'  => 1,
                'suggest_groups' => 0,
                'enabled'        => 1,
            ),
            array(
                'event_name'     => 'core.adm_page_header_after',
                'input_selector' => '#username',
                'description'    => 'ACP username suggestions',
                'suggest_users'  => 1,
                'suggest_groups' => 0,
                'enabled'        => 1,
            ),
            array(
                'event_name'     => 'core.ucp_display_module_before',
                'input_selector' => '#add',
                'description'    => 'Add friends/foes',
                'suggest_users'  => 1,
                'suggest_groups' => 0,
                'enabled'        => 1,
            ),
        );
        $this->db->sql_multi_insert($this->table_prefix . self::NAMESUGGESTIONS_EVENTS_TABLE, $insert_data);
    }
}
