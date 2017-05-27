<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace pcgf\namesuggestions\acp;

/** @version 1.0.1 */
class namesuggestions_module
{
    /** @var string $page_title Title of the page */
    public $page_title;

    /** @var string $tpl_name Name of the template file */
    public $tpl_name;

    /** @var object $u_action Action object */
    public $u_action;

    /**
     * Main function of the module
     *
     * @access public
     * @since  1.0.0
     *
     * @param string $id   The module id
     * @param string $mode The mode the module is being called with
     */
    public function main($id, $mode)
    {
        global $config, $db, $phpbb_admin_path, $phpbb_container, $phpEx, $request, $template, $user;
        $cache = $phpbb_container->get('cache');
        $events_table = $phpbb_container->getParameter('tables.pcgf.namesuggestions.events');
        $cached_query = 'SELECT event_name
                            FROM ' . $events_table . '
                            WHERE enabled = 1';
        switch ($mode)
        {
            case 'add':
                $this->page_title = $user->lang('ACP_PCGF_NAMESUGGESTIONS_ADD');
                $this->tpl_name = 'acp_namesuggestions_add';
                add_form_key('pcgf/namesuggestions_add');
                // Generate back link (links to the edit module)
                $back_link = '<a href="' . append_sid("{$phpbb_admin_path}index.$phpEx", array(
                        'i'    => str_replace('\\', '-', $id),
                        'mode' => 'edit',
                    )) . '">' . $user->lang('ACP_PCGF_NAMESUGGESTIONS_BACK_TO_EDIT') . '</a>';
                $event_name = $request->variable('event', '');
                if ($request->is_set_post('submit-add'))
                {
                    if (!check_form_key('pcgf/namesuggestions_add'))
                    {
                        trigger_error('FORM_INVALID', E_USER_WARNING);
                    }
                    $event_name_new = $request->variable('event-name', '');
                    $insert_data = array(
                        'event_name'     => $event_name_new,
                        'input_selector' => $request->variable('input-selector', ''),
                        'description'    => $request->variable('description', ''),
                        'suggest_users'  => $request->variable('suggest-users', 0),
                        'suggest_groups' => $request->variable('suggest-groups', 0),
                        'enabled'        => $request->variable('enabled', 0),
                    );
                    if ($event_name == '')
                    {
                        // Insert a new event
                        $query = 'INSERT INTO ' . $events_table . ' ' . $db->sql_build_array('INSERT', $insert_data);
                    }
                    else
                    {
                        // Update an existing event
                        $query = 'UPDATE ' . $events_table . ' SET ' . $db->sql_build_array('UPDATE', $insert_data) . ' WHERE event_name = "' . $db->sql_escape($event_name) . '"';
                    }
                    $db->sql_query($query);
                    if ($db->sql_affectedrows() == 1)
                    {
                        // Clear cached events so that changes will be used instantly
                        $cached_query_id = $cache->sql_load($cached_query);
                        if ($cache->sql_exists($cached_query_id))
                        {
                            $cache->destroy('sql', $events_table);
                        }
                        if ($event_name == '')
                        {
                            // Show add success message
                            trigger_error($user->lang('ACP_PCGF_NAMESUGGESTIONS_ADD_SUCCESS') . adm_back_link($this->u_action . '&amp;event=' . $event_name_new) . '<br/>' . $back_link, E_USER_NOTICE);
                        }
                        else
                        {
                            // Show edit success message
                            trigger_error($user->lang('ACP_PCGF_NAMESUGGESTIONS_EDIT_SUCCESS') . adm_back_link($this->u_action . '&amp;event=' . $event_name_new) . '<br/>' . $back_link, E_USER_NOTICE);
                        }
                    }
                    else
                    {
                        // Show action failed message
                        trigger_error($user->lang('ACP_PCGF_NAMESUGGESTIONS_ACTION_FAILED') . adm_back_link($this->u_action), E_USER_WARNING);
                    }
                }
                $event = false;
                if ($event_name != '')
                {
                    $query = 'SELECT *
                            FROM ' . $events_table . "
                            WHERE event_name = '" . $db->sql_escape($event_name) . "'";
                    $result = $db->sql_query($query);
                    $event = $db->sql_fetchrow($result);
                    $db->sql_freeresult($result);
                }
                if ($event)
                {
                    // Load the settings of the selected event
                    $template->assign_vars(array(
                        'PCGF_NAMESUGGESTIONS_EVENT_NAME'     => $event['event_name'],
                        'PCGF_NAMESUGGESTIONS_INPUT_SELECTOR' => $event['input_selector'],
                        'PCGF_NAMESUGGESTIONS_DESCRIPTION'    => $event['description'],
                        'PCGF_NAMESUGGESTIONS_SUGGEST_USERS'  => $event['suggest_users'],
                        'PCGF_NAMESUGGESTIONS_SUGGEST_GROUPS' => $event['suggest_groups'],
                        'PCGF_NAMESUGGESTIONS_ENABLED'        => $event['enabled'],
                    ));
                }
                $template->assign_vars(array(
                    'EVENT'       => $event_name,
                    'U_ACTION'    => $this->u_action,
                    'EDIT_ACTION' => $back_link,
                ));
            break;
            case 'edit':
                $this->page_title = $user->lang('ACP_PCGF_NAMESUGGESTIONS_EDIT');
                $this->tpl_name = 'acp_namesuggestions_edit';
                add_form_key('pcgf/namesuggestions_edit');
                $action = $request->variable('action', '');
                if ($request->is_set_post('submit'))
                {
                    if (!check_form_key('pcgf/namesuggestions_edit') || !$request->is_set_post('action'))
                    {
                        trigger_error('FORM_INVALID', E_USER_WARNING);
                    }
                    switch ($action)
                    {
                        case 'delete':
                            // Delete the selected event when deletion has been confirmed
                            if ($request->variable('submit', $user->lang('NO')) == $user->lang('YES'))
                            {
                                $query = 'DELETE FROM ' . $events_table . " WHERE event_name = '" . $db->sql_escape($request->variable('event', 'NULL')) . "'";
                                $db->sql_query($query);
                                if ($db->sql_affectedrows() == 1)
                                {
                                    // Clear cached events so that changes will be used instantly
                                    $cached_query_id = $cache->sql_load($cached_query);
                                    if ($cache->sql_exists($cached_query_id))
                                    {
                                        $cache->destroy('sql', $events_table);
                                    }
                                }
                                else
                                {
                                    trigger_error($user->lang('ACP_PCGF_NAMESUGGESTIONS_ACTION_FAILED') . adm_back_link($this->u_action), E_USER_WARNING);
                                }
                            }
                        break;
                        case 'save':
                            // Save the settings
                            $config->set('pcgf_namesuggestions_suggestion_count', $request->variable('namesuggestion-user-count', 5));
                            $config->set('pcgf_namesuggestions_avatar_image_size', $request->variable('namesuggestions-avatar-image-size', 20));
                            trigger_error($user->lang('ACP_PCGF_NAMESUGGESTIONS_SETTINGS_SAVED') . adm_back_link($this->u_action));
                        break;
                    }
                }
                else if ($action == 'delete')
                {
                    // Show confirmation page
                    $this->tpl_name = 'acp_namesuggestions_confirm';
                    $event_name = $request->variable('event', 'NULL');
                    $template->assign_vars(array(
                        'EVENT'                                          => $event_name,
                        'ACP_PCGF_NAMESUGGESTIONS_CONFIRM_DELETION_TEXT' => $user->lang('ACP_PCGF_NAMESUGGESTIONS_CONFIRM_DELETION_TEXT', $event_name),
                    ));
                    break;
                }
                $template->assign_vars(array(
                    'PCGF_NAMESUGGESTIONS_USER_COUNT'        => $config['pcgf_namesuggestions_suggestion_count'],
                    'PCGF_NAMESUGGESTIONS_AVATAR_IMAGE_SIZE' => $config['pcgf_namesuggestions_avatar_image_size'],
                    'U_ACTION'                               => $this->u_action,
                    'ADD_ACTION'                             => append_sid("{$phpbb_admin_path}index.$phpEx", array(
                        'i'    => str_replace('\\', '-', $id),
                        'mode' => 'add',
                    )),
                ));
                // Load defined events
                $events_defined = false;
                $query = 'SELECT event_name, description, enabled
                            FROM ' . $events_table;
                $result = $db->sql_query($query);
                while ($event = $db->sql_fetchrow($result))
                {
                    $events_defined = true;
                    $template->assign_block_vars('namesuggestion_event_list', array(
                        'EVENT'       => $event['event_name'],
                        'DESCRIPTION' => $event['description'],
                        'ENABLED'     => $event['enabled'],
                    ));
                }
                $db->sql_freeresult($result);
                $template->assign_var('PCGF_NAMESUGGESTIONS', $events_defined);
            break;
        }
    }
}
