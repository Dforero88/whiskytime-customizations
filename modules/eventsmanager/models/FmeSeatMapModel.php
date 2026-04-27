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
class FmeSeatMapModel extends ObjectModel
{
    public $event_id;

    public $id_seat_map;

    public $seat_map;

    public static $definition = [
        'table' => 'fme_events_seat_map',
        'primary' => 'id_seat_map',
        'fields' => [
            'id_seat_map' => ['type' => self::TYPE_INT],
            'event_id' => ['type' => self::TYPE_INT],
            'seat_map' => ['type' => self::TYPE_HTML],
        ],
    ];

    public function __construct($event_id = null, $id_seat_map = null)
    {
        parent::__construct($event_id, $id_seat_map);
    }

    public static function removeMap($id)
    {
        if (!$id) {
            return false;
        }

        return Db::getInstance()->delete('fme_events_seat_map', 'event_id = ' . (int) $id);
    }

    public static function getBookingSeatsMapById($id)
    {
        $sql = new DbQuery();
        $sql->select('seat_map');
        $sql->from('fme_events_seat_map');
        $sql->where('`event_id` = ' . (int) $id);

        return Db::getInstance()->executeS($sql);
    }
}
