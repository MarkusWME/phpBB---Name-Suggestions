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
    'ACP_CAT_PCGF_NAMESUGGESTIONS'                       => 'Namensvorschläge',
    'ACP_PCGF_NAMESUGGESTIONS'                           => 'Namensvorschläge',
    'ACP_PCGF_NAMESUGGESTIONS_EXPLAIN'                   => 'Hier kannst du die Einstellungen zu den Namensvorschlägen anpassen.',
    'ACP_PCGF_NAMESUGGESTIONS_SETTINGS'                  => 'Einstellungen',
    'ACP_PCGF_NAMESUGGESTIONS_SETTINGS_SAVED'            => 'Die Einstellungen wurden erfolgreich gespeichert!',
    'ACP_PCGF_NAMESUGGESTIONS_ADD'                       => 'Namensvorschlag hinzufügen',
    'ACP_PCGF_NAMESUGGESTIONS_ADD_DESCRIPTION'           => 'Hier kannst du ein neues Event einfügen.',
    'ACP_PCGF_NAMESUGGESTIONS_ADD_SUCCESS'               => 'Das Event wurde erfolgreich hinzugefügt.',
    'ACP_PCGF_NAMESUGGESTIONS_EDIT'                      => 'Namensvorschläge verwalten',
    'ACP_PCGF_NAMESUGGESTIONS_EDIT_EXPLAIN'              => 'Hier kannst du einen Namensvorschlag anpassen.',
    'ACP_PCGF_NAMESUGGESTIONS_EDIT_SUCCESS'              => 'Das Event wurde erfolgreich bearbeitet.',
    'ACP_PCGF_NAMESUGGESTIONS_USER_COUNT'                => 'Benutzeranzahl',
    'ACP_PCGF_NAMESUGGESTIONS_USER_COUNT_EXPLAIN'        => 'Hier kann eingestellt werden wie viele Benutzernamen vorgeschlagen werden sollen (0 liefert alle gefundenen Benutzernamen). Die eingestellte Anzahl wird auch für vorgeschlagene Gruppen übernommen.',
    'ACP_PCGF_NAMESUGGESTIONS_AVATAR_IMAGE_SIZE'         => 'Avatar Bildgröße',
    'ACP_PCGF_NAMESUGGESTIONS_AVATAR_IMAGE_SIZE_EXPLAIN' => 'Hier kann eingestellt werden wie groß der Benutzeravatar dargestellt werden soll (0 deaktiviert die Anzeige des Benutzeravatars).',
    'ACP_PCGF_NAMESUGGESTIONS_EVENTS'                    => 'Events an welche die Namensvorschläge gebunden sind',
    'ACP_PCGF_NAMESUGGESTIONS_NO_EVENTS'                 => 'Es wurden noch keine Events festgelegt.',
    'ACP_PCGF_NAMESUGGESTIONS_EVENT_NAME'                => 'Event',
    'ACP_PCGF_NAMESUGGESTIONS_EVENT_NAME_EXPLAIN'        => 'Wähle hier das Event aus bei welchem Namensvorschläge geladen werden sollen. Eine vollständige Liste der Events findest du <a href="https://wiki.phpbb.com/Event_List#PHP_Events_.28Hook_Locations.29">hier</a>. Du kannst auch Events nutzen, welche durch andere Extensions definiert wurden.',
    'ACP_PCGF_NAMESUGGESTIONS_DESCRIPTION'               => 'Beschreibung',
    'ACP_PCGF_NAMESUGGESTIONS_DESCRIPTION_EXPLAIN'       => 'Hier kannst du eine Beschreibung eingeben um später zu erkennen an welcher Stelle dieses Event Namensvorschläge liefert.',
    'ACP_PCGF_NAMESUGGESTIONS_INPUT_SELECTOR'            => 'Input-Selektor',
    'ACP_PCGF_NAMESUGGESTIONS_INPUT_SELECTOR_EXPLAIN'    => 'Hier kannst du auswählen auf welches Eingabefeld reagiert werden soll.',
    'ACP_PCGF_NAMESUGGESTIONS_SUGGEST_USERS'             => 'Benutzer vorschlagen',
    'ACP_PCGF_NAMESUGGESTIONS_SUGGEST_GROUPS'            => 'Gruppen vorschlagen',
    'ACP_PCGF_NAMESUGGESTIONS_STATUS'                    => 'Status',
    'ACP_PCGF_NAMESUGGESTIONS_ACTIONS'                   => 'Aktionen',
    'ACP_PCGF_NAMESUGGESTIONS_ACTION_FAILED'             => 'Die Aktion ist fehlgeschlagen! Versuche es später erneut.',
    'ACP_PCGF_NAMESUGGESTIONS_CONFIRM_DELETION'          => 'Wollen Sie das Event wirklich löschen?',
    'ACP_PCGF_NAMESUGGESTIONS_CONFIRM_DELETION_TEXT'     => 'Sie sind dabei das Event "%s" zu löschen. Wollen Sie fortfahren?',
    'ACP_PCGF_NAMESUGGESTIONS_BACK_TO_EDIT'              => '&laquo; Zurück zur Verwaltung',
));
