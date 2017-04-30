<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace pcgf\namesuggestions\acp;

use pcgf\namesuggestions\migrations\release_1_0_0;

/** @version 1.0.0 */
class namesuggestions_module
{
    /** @var string $page_title Title of the page */
    public $page_title;

    /** @var string $tpl_name Name of the template file */
    public $tpl_name;

    /** @var object $u_action Action object */
    public $u_action;

    public function main($id, $mode)
    {
        global $config, $db, $phpbb_admin_path, $phpEx, $request, $table_prefix, $template, $user;
        switch ($mode)
        {
            case 'add':
                $this->page_title = $user->lang('ACP_PCGF_NAMESUGGESTIONS_ADD');
                $this->tpl_name = 'acp_namesuggestions_add';
                add_form_key('pcgf/namesuggestions_add');
            break;
            case 'edit':
                $this->page_title = $user->lang('ACP_PCGF_NAMESUGGESTIONS_EDIT');
                $this->tpl_name = 'acp_namesuggestions_edit';
                add_form_key('pcgf/namesuggestions_edit');
                $action = $request->variable('action', 'void');
                if ($request->is_set_post('submit'))
                {
                    if (!check_form_key('pcgf/namesuggestions_edit') || !$request->is_set_post('action'))
                    {
                        trigger_error('FORM_INVALID', E_USER_WARNING);
                    }
                    switch ($action)
                    {
                        case 'delete':
                            if ($request->variable('submit', $user->lang('NO')) == $user->lang('YES'))
                            {
                                $query = 'DELETE FROM ' . $table_prefix . release_1_0_0::NAMESUGGESTIONS_EVENTS_TABLE . ' WHERE event_name = "' . $db->sql_escape($request->variable('event', 'NULL')) . '"';
                                $db->sql_query($query);
                                if ($db->sql_affectedrows() != 1)
                                {
                                    trigger_error($user->lang('ACP_PCGF_NAMESUGGESTIONS_ACTION_FAILED') . adm_back_link($this->u_action), E_USER_WARNING);
                                }
                            }
                        break;
                        case 'save':
                            $config->set('pcgf_namesuggestions_suggestion_count', $request->variable('namesuggestion-user-count', 5));
                            $config->set('pcgf_namesuggestions_avatar_image_size', $request->variable('namesuggestions-avatar-image-size', 20));
                            trigger_error($user->lang('ACP_PCGF_NAMESUGGESTIONS_SETTINGS_SAVED') . adm_back_link($this->u_action));
                        break;
                    }
                }
                else if ($action == 'delete')
                {
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
                $events_defined = false;
                $query = 'SELECT event_name, description, enabled
                            FROM ' . $table_prefix . release_1_0_0::NAMESUGGESTIONS_EVENTS_TABLE;
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
                $template->assign_var('PCGF_NAMESUGGESTIONS', $events_defined);
            break;
        }
    }
}
