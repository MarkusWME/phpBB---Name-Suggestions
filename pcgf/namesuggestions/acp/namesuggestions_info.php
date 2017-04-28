<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace pcgf\namesuggestions\acp;

/** @version 1.0.0 */
class namesuggestions_info
{
    public function module()
    {
        return array(
            'filename' => '\pcgf\namesuggestions\acp\namesuggestions_module',
            'title'    => 'ACP_PCGF_NAMESUGGESTIONS',
            'modes'    => array(
                'add'  => array(
                    'title' => 'ACP_PCGF_NAMESUGGESTIONS_ADD',
                    'auth'  => 'ext_pcgf/namesuggestions',
                    'cat'   => array('ACP_CAT_PCGF_NAMESUGGESTIONS'),
                ),
                'edit' => array(
                    'title' => 'ACP_PCGF_NAMESUGGESTIONS_EDIT',
                    'auth'  => 'ext_pcgf/namesuggestions',
                    'cat'   => array('ACP_CAT_PCGF_NAMESUGGESTIONS'),
                ),
            ),
        );
    }
}
