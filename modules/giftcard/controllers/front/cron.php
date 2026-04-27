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
class GiftcardCronModuleFrontController extends ModuleFrontController
{
    public $useSSL = true;

    protected $cron_tpl = 'v1_6/cron.tpl';

    public function init()
    {
        parent::init();
        $this->context = Context::getContext();
        if (true == (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->cron_tpl = sprintf('module:%s/views/templates/front/v1_7/cron.tpl', $this->module->name);
        }
    }

    public function initContent()
    {
        parent::initContent();
        $result = ['deleted' => 0, 'skip' => 0];
        $action = Tools::getValue('action');
        if (!Tools::getIsset('giftcard_cron_key')) {
            $this->context->controller->errors[] = $this->module->l('cron key not set.', 'cron');
        } elseif (Configuration::get('GIFTCARD_CRON_KEY') !== Tools::getValue('giftcard_cron_key')) {
            $this->context->controller->errors[] = $this->module->l('cron key not set.', 'cron');
        } else {
            switch ($action) {
                case 'flush_carts':
                    $hours = (int) Configuration::get('GIFTCARD_CRON_HOURS');
                    $abandonedGifts = Gift::getAbandonedGifts($hours);
                    if ($abandonedGifts) {
                        foreach ($abandonedGifts as $gifts) {
                            if ($gifts['id_cart'] && $gifts['id_product']) {
                                if (Gift::getGiftCardType($gifts['id_product']) != 'fixed'
                                    && Validate::isLoadedObject($product = new Product($gifts['id_product']))) {
                                    if ($product->delete()) {
                                        ++$result['deleted'];
                                    } else {
                                        ++$result['skip'];
                                    }
                                }
                            }
                        }
                    }
                    $this->context->smarty->assign('request', 1);
                    break;
                case 'sendtosomeone_later':
                    $sendGCLater = Gift::getLaterDateGiftCards();
                    if ($sendGCLater) {
                        foreach ($sendGCLater as $gift) {
                            $this->module->generateVoucher($gift['id_cart'], 'sendsomeone');
                        }
                    }
                    $this->context->smarty->assign('request', 2);
                    break;
            }
        }
        $this->context->smarty->assign('result', $result);

        return $this->setTemplate($this->cron_tpl);
    }
}
