<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class EventTags extends ObjectModel
{
    public $id_fme_tags;
    public $active;
    public $title;
    public $friendly_url;
    public $description;
    public static $definition = [
        'table' => 'fme_tags',
        'primary' => 'id_fme_tags',
        'multilang' => true,
        'fields' => [
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true],
            'friendly_url' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true],
            'description' => ['type' => self::TYPE_STRING, 'lang' => true],
        ],
    ];

    public function __construct($id = null, $id_lang = null)
    {
        parent::__construct($id, $id_lang);
    }

    public static function getAllTagNames($id_lang, $activeOnly = false)
    {
        $active = '';
        if ($activeOnly) {
            $active = ' AND t.active = true';
        }
        $sql = 'SELECT t.id_fme_tags, tl.friendly_url, tl.title AS `name`
        FROM `' . _DB_PREFIX_ . 'fme_tags` t
        LEFT JOIN `' . _DB_PREFIX_ . 'fme_tags_lang` tl
        ON t.id_fme_tags = tl.id_fme_tags
        WHERE  tl.id_lang = ' . (int) $id_lang . ' ' . $active;

        return Db::getInstance()->executeS($sql);
    }

    public static function addEventTags($event_id, $tags)
    {
        Db::getInstance()->delete('fme_events_tags', 'event_id = ' . (int) $event_id);
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                Db::getInstance()->insert(
                    pSQL('fme_events_tags'),
                    [
                        'event_id' => (int) $event_id,
                        'id_fme_tags' => pSQL($tag),
                    ]
                );
            }
        }

        return Db::getInstance()->Insert_ID();
    }

    public static function getEventTags($event_id, $id_lang)
    {
        $sql = 'SELECT t.id_fme_tags, tl.friendly_url, tl.title AS `name`
        FROM `' . _DB_PREFIX_ . 'fme_tags` t
        LEFT JOIN `' . _DB_PREFIX_ . 'fme_tags_lang` tl ON tl.id_fme_tags = t.id_fme_tags
        LEFT JOIN `' . _DB_PREFIX_ . 'fme_events_tags` et ON et.id_fme_tags = t.id_fme_tags
        WHERE  tl.id_lang = ' . (int) $id_lang . ' AND et.event_id = ' . (int) $event_id;

        return Db::getInstance()->executeS($sql);
    }

    public static function getEventIdsByTagId($id_tag)
    {
        $sql = 'SELECT event_id
        FROM `' . _DB_PREFIX_ . 'fme_events_tags`
        WHERE  id_fme_tags = ' . (int) $id_tag;

        return Db::getInstance()->executeS($sql);
    }

    public static function getSelectedTagsById($id_tag, $id_lang)
    {
        $sql = new DbQuery();
        $sql->select('t.id_fme_tags, tl.title AS `name`');
        $sql->from('fme_tags', 't');
        $sql->where('t.id_fme_tags=' . (int) $id_tag);
        $sql->leftJoin('fme_tags_lang', 'tl', 't.`id_fme_tags` = tl.`id_fme_tags`');
        $sql->where('tl.id_lang = ' . (int) $id_lang);

        return Db::getInstance()->getRow($sql);
    }

    public static function upgradeEventsManager()
    {
        $return = true;
        if (self::columnExists('event_permalinks', 'fme_events_lang')) {
            $return = true;
        } else {
            $return = Db::getInstance()->execute('
                ALTER TABLE `' . _DB_PREFIX_ . 'fme_events_lang`
                ADD `event_permalinks` varchar(250) default NULL
           ');
        }
        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_tags(
            `id_fme_tags`   int(11) NOT NULL AUTO_INCREMENT,
            `active`        tinyint(1),
            PRIMARY KEY(`id_fme_tags`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_tags_lang(
            `id_fme_tags`   int(11) NOT NULL,
            `id_lang`       int(11) NOT NULL,
            `title`         varchar(250) default NULL,
            `friendly_url` varchar(250) default NULL,
            `description`   text,
            PRIMARY KEY  (`id_fme_tags`, `id_lang`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $return &= Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_tags(
            `event_id`  int(11) NOT NULL,
            `id_fme_tags`  int(11) NOT NULL,
            PRIMARY KEY  (`event_id`, `id_fme_tags`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');
        $return &= Configuration::updateValue('EVENTS_TAGS_ENABLE_DISABLE', 0);

        return $return;
    }

    public static function columnExists($column_name, $table_name)
    {
        $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns
            WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . pSQL($table_name) . '"');
        if (isset($columns) && $columns) {
            foreach ($columns as $column) {
                if ($column['COLUMN_NAME'] == $column_name) {
                    return true;
                }
            }
        }

        return false;
    }
}
