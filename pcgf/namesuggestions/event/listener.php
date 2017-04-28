<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace pcgf\namesuggestions\event;

use phpbb\config\config;
use phpbb\db\driver\factory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/** @version 1.0.0 */
class listener implements EventSubscriberInterface
{
    /** @var factory $db Database object */
    protected $db;

    /** @var config $config Configuration object */
    protected $config;

    public function __construct(factory $db, config $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    static public function getSubscribedEvents()
    {
        return array();
    }
}
