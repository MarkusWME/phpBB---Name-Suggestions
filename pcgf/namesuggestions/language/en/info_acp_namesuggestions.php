<?php

/**
 * @author    MarkusWME <markuswme@pcgamingfreaks.at>
 * @copyright 2017 MarkusWME
 * @license   http://opensource.org/gpl-2.0.php GNU General Public License v2
 * @version   1.0.0
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

// Merge name suggestion ACP language data to the existing language data
$lang = array_merge($lang, array(
    'ACP_CAT_PCGF_NAMESUGGESTIONS'                       => 'Name suggestions',
    'ACP_PCGF_NAMESUGGESTIONS'                           => 'Name suggestions',
    'ACP_PCGF_NAMESUGGESTIONS_EXPLAIN'                   => 'Here you can adjust the settings for the name suggestions.',
    'ACP_PCGF_NAMESUGGESTIONS_SETTINGS'                  => 'Settings',
    'ACP_PCGF_NAMESUGGESTIONS_SETTINGS_SAVED'            => 'The settings have been saved successfully!',
    'ACP_PCGF_NAMESUGGESTIONS_ADD'                       => 'Add name suggestion',
    'ACP_PCGF_NAMESUGGESTIONS_ADD_DESCRIPTION'           => 'Here you can add a new event.',
    'ACP_PCGF_NAMESUGGESTIONS_ADD_SUCCESS'               => 'The event has been added successfully.',
    'ACP_PCGF_NAMESUGGESTIONS_EDIT'                      => 'Maintain name suggestions',
    'ACP_PCGF_NAMESUGGESTIONS_EDIT_EXPLAIN'              => 'Here you can adjust a name suggestion.',
    'ACP_PCGF_NAMESUGGESTIONS_EDIT_SUCCESS'              => 'The event has been edited successfully.',
    'ACP_PCGF_NAMESUGGESTIONS_USER_COUNT'                => 'User count',
    'ACP_PCGF_NAMESUGGESTIONS_USER_COUNT_EXPLAIN'        => 'Here you can adjust how many users should be suggested (0 means that all found usernames will be shown). The amount will also be used for the amount of groups that will be shown.',
    'ACP_PCGF_NAMESUGGESTIONS_AVATAR_IMAGE_SIZE'         => 'Avatar image size',
    'ACP_PCGF_NAMESUGGESTIONS_AVATAR_IMAGE_SIZE_EXPLAIN' => 'Here you can adjust the avatar image size (0 disables user avatars).',
    'ACP_PCGF_NAMESUGGESTIONS_EVENTS'                    => 'Events to which name suggestions are bound',
    'ACP_PCGF_NAMESUGGESTIONS_NO_EVENTS'                 => 'No events have been defined so far.',
    'ACP_PCGF_NAMESUGGESTIONS_EVENT_NAME'                => 'Event',
    'ACP_PCGF_NAMESUGGESTIONS_EVENT_NAME_EXPLAIN'        => 'Choose an event to which a name suggestion should be bound. A full list of events can be found <a href="https://wiki.phpbb.com/Event_List#PHP_Events_.28Hook_Locations.29">here</a>. You can also use events that are defined by other extensions.',
    'ACP_PCGF_NAMESUGGESTIONS_DESCRIPTION'               => 'Description',
    'ACP_PCGF_NAMESUGGESTIONS_DESCRIPTION_EXPLAIN'       => 'Here you can add a description so that you later on know where this event will be executed.',
    'ACP_PCGF_NAMESUGGESTIONS_INPUT_SELECTOR'            => 'Input selector',
    'ACP_PCGF_NAMESUGGESTIONS_INPUT_SELECTOR_EXPLAIN'    => 'Here you can set to which input element should be listened.',
    'ACP_PCGF_NAMESUGGESTIONS_SUGGEST_USERS'             => 'Suggest users',
    'ACP_PCGF_NAMESUGGESTIONS_SUGGEST_GROUPS'            => 'Suggest groups',
    'ACP_PCGF_NAMESUGGESTIONS_STATUS'                    => 'Status',
    'ACP_PCGF_NAMESUGGESTIONS_ACTIONS'                   => 'Actions',
    'ACP_PCGF_NAMESUGGESTIONS_ACTION_FAILED'             => 'The action has failed! Please try again later.',
    'ACP_PCGF_NAMESUGGESTIONS_CONFIRM_DELETION'          => 'Do you really want to delete the event?',
    'ACP_PCGF_NAMESUGGESTIONS_CONFIRM_DELETION_TEXT'     => 'You are going to delete the event "%s". Do you want to continue?',
    'ACP_PCGF_NAMESUGGESTIONS_BACK_TO_EDIT'              => '&laquo; Back to management section',
));
