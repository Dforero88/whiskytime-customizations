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
function upgrade_module_1_8_0($module)
{
    $result = true;
    $result &= $module->alterNewCols();
    if ($module->removeTab()) {
        $result &= $module->addTab();
    }

    return $result;
}
