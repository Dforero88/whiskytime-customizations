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

function upgrade_module_3_0_1($module)
{
    $module = $module;
    if (columnExist('event_streaming_start_time')
    && columnExist('event_streaming_end_time')
    && columnExist('event_streaming')) {
        return true;
    } else {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fme_events`
            ADD `event_streaming_start_time` varchar(250) default NULL,
            ADD `event_streaming_end_time` varchar(250) default NULL,
            ADD `event_streaming` mediumtext ');
    }

    return Configuration::updateValue('EVENT_SHOW_LIVE_STREAMING', '0');
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
