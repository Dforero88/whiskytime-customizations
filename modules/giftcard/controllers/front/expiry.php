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
class GiftcardExpiryModuleFrontController extends ModuleFrontController
{
    public $useSSL = true;

    protected $cron_tpl = 'v1_6/cron.tpl';

    public function init()
    {
        parent::init();
        $this->context = Context::getContext();
        if (true == (bool) Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->cron_tpl = sprintf('module:%s/views/templates/front/v1_7/expiry.tpl', $this->module->name);
        }
    }

    public function initContent()
    {
        parent::initContent();
        $result = ['sent' => 0, 'skip' => 0];
        // $action = Tools::getValue('action');

        $message_deadline = Configuration::get('GIFT_EXPIRY_MAIL_TIME', 24);
        if (!Tools::getIsset('giftcard_expiry_cron_key')) {
            $this->context->controller->errors[] = $this->module->l('cron key not set.', 'cron');
        } elseif (Configuration::get('GIFTCARD_EXPIRY_CRON_KEY') !== Tools::getValue('giftcard_expiry_cron_key')) {
            $this->context->controller->errors[] = $this->module->l('cron key not set.', 'cron');
        } else {
            if (Configuration::get('GIFT_ALERT_EXPIRED', false)) {
                $id_lang = (int) $this->context->cookie->id_lang;
                $model = new Gift();
                $coupens = $model->getAllVouchers(true, $id_lang);
                foreach ($coupens as $coupon) {
                    $coupon_ending_date = $coupon['date_to'];
                    $time = new DateTime();
                    $coupon_ending_timestamp = strtotime($coupon_ending_date);
                    $current_timestamp = time();
                    $seconds_until_ending = $coupon_ending_timestamp - $current_timestamp;
                    $hours_until_ending = $seconds_until_ending / 3600;
                    if ($coupon['expiry_alert'] == 0 && $hours_until_ending <= $message_deadline && $hours_until_ending > 0) {
                        Gift::sendGiftCardExpiringAlert($coupon);
                        Gift::updateExpiryAlert($coupon['id_cart_rule']);
                    }
                }
            }
        }
        $this->context->smarty->assign('result', $result);

        return $this->setTemplate($this->cron_tpl);
    }
}
