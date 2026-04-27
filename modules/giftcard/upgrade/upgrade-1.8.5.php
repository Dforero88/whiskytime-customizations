<?php
/**
 * DISCLAIMER.
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    FMM Modules
 *  @copyright 2021 FMM Modules
 *  @license   FMM Modules
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_8_5($module)
{
    return $module->registerHook('actionAdminProductsListingFieldsModifier');
}
