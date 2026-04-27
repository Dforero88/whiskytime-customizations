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
class ProductController extends ProductControllerCore
{
    /*
    * module: giftcard
    * date: 2025-07-29 14:52:37
    * version: 4.0.2
    */
    protected function addProductCustomizationData(array $product_full)
    {
        $product_full = parent::addProductCustomizationData($product_full);
        if (true === Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            if (Module::isInstalled('giftcard')) {
                include_once _PS_MODULE_DIR_ . 'giftcard/models/Gift.php';
                if (Gift::isExists($this->product->id)) {
                    $product_full['customizations'] = [
                        'fields' => [],
                    ];
                }
            }
        }
        return $product_full;
    }
}
