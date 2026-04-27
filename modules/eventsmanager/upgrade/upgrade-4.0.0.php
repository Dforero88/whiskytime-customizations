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

function upgrade_module_4_0_0($module)
{
    $module = $module;
    if (columnExist('seat_selection')
    && columnExist('longitude')
    && columnExist('latitude')) {
        return true;
    } else {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fme_events`
            ADD `seat_selection`  tinyint(1) default \'0\',
            ADD `longitude` varchar(250) default NULL,
            ADD `latitude` varchar(250) default NULL ');
    }

    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_seat_map(
        `id_seat_map`              int(11) NOT NULL auto_increment,
        `event_id`               int(11) NOT NULL,
        `seat_map`      longtext default NULL,
        PRIMARY KEY             (`id_seat_map`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'fme_events_customer(
        `id_events_customer`              int(11) NOT NULL auto_increment,
        `event_id`               int(11) NOT NULL,
        `id_product`      int(11) NOT NULL,
        `quantity`      int(11) default NULL,
        `id_customer`      int(11) default NULL,
        `id_guest`      int(11) default NULL,
        `id_cart`      int(11) default NULL,
        `id_order`      int(11) default NULL,
        `order_status`       varchar(255) default NULL,
        `customer_name`      varchar(255) default NULL,
        `customer_phone`     varchar(255) default NULL,
        `reserve_seats`     varchar(255) default NULL,
        `reserve_seats_num`     varchar(255) default NULL,
        `date`    varchar(255) default NULL,
        PRIMARY KEY             (`id_events_customer`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

    return
        $module->addTab()
        && Configuration::updateValue('RELATED_PRODUCT_IMAGE', '1')
        && Configuration::updateValue('FME_WAIT_MIN', '2')
        && Configuration::updateValue('FME_REQ_PHONE', '1')
        && $module->registerHook('displayPaymentTop')
        && $module->registerHook('newOrder')
        && $module->registerHook('updateOrderStatus')
    ;
}

function columnExist($column_name)
{
    $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns
        WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . 'fme_events"');
    if (isset($columns) && $columns) {
        foreach ($columns as $column) {
            if ($column['COLUMN_NAME'] == $column_name) {
                return true;
            }
        }
    }

    return false;
}
