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
function upgrade_module_1_5_1($module)
{
    if (columnExist('ordered_gift_cards', 'friend_name')
        && columnExist('ordered_gift_cards', 'friend_email')
        && columnExist('ordered_gift_cards', 'gift_message')) {
        return true;
    } else {
        Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'ordered_gift_cards`
            ADD `friend_name`       varchar(100),
            ADD `friend_email`      varchar(100),
            ADD `gift_message`      TEXT');
        $module->unregisterHook('footer');

        return true;
    }

    return false;
}

function columnExist($table, $column_name)
{
    $columns = Db::getInstance()->ExecuteS('SELECT COLUMN_NAME FROM information_schema.columns
        WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . pSQL($table) . '"');
    if (isset($columns)) {
        foreach ($columns as $column) {
            if ($column['COLUMN_NAME'] == $column_name) {
                return true;
            }
        }
    }

    return false;
}
