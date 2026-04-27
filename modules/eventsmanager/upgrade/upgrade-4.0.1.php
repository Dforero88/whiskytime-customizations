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

function upgrade_module_4_0_1($module)
{
    $module = $module;
    if (columnExist('days') && columnExist('admin_order')) {
        return true;
    } else {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fme_events_customer`
            ADD `days`  varchar(255) default NULL,
            ADD `admin_order` int(11) default NULL ');
    }
    if (columnExistEvents('facebook_link')
    && columnExistEvents('twitter_link')
    && columnExistEvents('instagram_link')) {
        return true;
    } else {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'fme_events`
            ADD `facebook_link`  mediumtext,
            ADD `twitter_link`  mediumtext,
            ADD `instagram_link` mediumtext');
    }

    return true;
}

function columnExist($column_name)
{
    $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns
        WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . 'fme_events_customer"');
    if (isset($columns) && $columns) {
        foreach ($columns as $column) {
            if ($column['COLUMN_NAME'] == $column_name) {
                return true;
            }
        }
    }

    return false;
}

function columnExistEvents($column_name)
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
