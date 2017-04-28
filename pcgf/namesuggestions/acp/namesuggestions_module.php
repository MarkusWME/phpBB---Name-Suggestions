<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace pcgf\namesuggestions\acp;

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
        global $config, $request, $template, $user, $phpbb_admin_path, $phpEx;
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
                if ($request->is_set_post('submit'))
                {
                    if (!check_form_key('pcgf/namesuggestions_edit') || !$request->is_set_post('action'))
                    {
                        trigger_error('FORM_INVALID', E_USER_WARNING);
                    }
                    switch ($request->variable('action', 'void'))
                    {
                        case 'save':
                            $config->set('pcgf_namesuggestions_suggestion_count', $request->variable('namesuggestion-user-count', 5));
                            $config->set('pcgf_namesuggestions_avatar_image_size', $request->variable('namesuggestions-avatar-image-size', 20));
                            trigger_error($user->lang('ACP_PCGF_NAMESUGGESTIONS_SETTINGS_SAVED') . adm_back_link($this->u_action));
                        break;
                    }
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
            break;
        }
    }
}
