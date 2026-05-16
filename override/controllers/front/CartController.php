<?php
if (!defined('_PS_VERSION_')) {
    exit;
}
class CartController extends CartControllerCore
{
    protected $new_price;
    protected $gift_order_type = 'home';
    protected $is_gift_product = false;
    protected $add_gift_data = false;
    protected $reference_product = 0;
    protected $giftcard_type = false;
    protected $friend_name = '';
    protected $friend_email = '';
    protected $gift_message = '';
    protected $specific_date_check = 0;
    protected $specific_date = '';
    protected $id_gift_card_template = 0;
    
    public function init(): void
    {
        parent::init();
        if (Module::isInstalled('giftcard')) {
            $default_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
            $active_currency = (int) $this->context->currency->id;
            $currency_current = new Currency($active_currency);
            $this->reference_product = $this->id_product;
            include_once _PS_MODULE_DIR_ . 'giftcard/models/Gift.php';
            if (Gift::isExists($this->id_product) && Tools::getValue('add')) {
                $this->gift_order_type = Tools::getValue('gift_order_type', 'home');
                $this->giftcard_type = Tools::getValue('giftcard_type');
                $this->specific_date_check = (int) Tools::getValue('specific_date_check', 0);
                $priceDisplay = (int) Product::getTaxCalculationMethod((int) Context::getContext()->cart->id_customer);
                $tax = (!$priceDisplay || $priceDisplay == 2) ? true : false;
                $this->new_price = (float) (($this->giftcard_type && $this->giftcard_type == 'fixed') ? Product::getPriceStatic($this->id_product, $tax) : Tools::getValue('giftcard_price'));
                $this->is_gift_product = true;
                if ($this->gift_order_type === 'sendsomeone' || ($this->giftcard_type && in_array($this->giftcard_type, ['dropdown', 'range']))) {
                    if ($default_currency != $active_currency) {
                        $store_currency = new Currency($default_currency);
                        $this->new_price = Tools::convertPriceFull($this->new_price, $currency_current, $store_currency);
                    }
                    $this->id_product = Module::getInstanceByName('giftcard')->processDuplicate($this->id_product, $this->new_price);
                }
                if ($this->gift_order_type === 'sendsomeone') {
                    $giftDetails = Tools::getValue('gift_vars');
                    if (!Validate::isName(trim($giftDetails['reciptent']))) {
                        $this->errors[] = Module::getInstanceByName('giftcard')->l('Invalid reciptent name.');
                        $this->ajaxDie(json_encode([
                            'hasError' => true,
                            'success' => false,
                            'errors' => $this->errors,
                        ]));
                    } elseif (!Validate::isEmail(trim($giftDetails['email']))) {
                        $this->errors[] = Module::getInstanceByName('giftcard')->l('Invalid reciptent email.');
                        $this->ajaxDie(json_encode([
                            'hasError' => true,
                            'success' => false,
                            'errors' => $this->errors,
                        ]));
                    } elseif ($this->specific_date_check && !Validate::isDate($giftDetails['specific_date'])) {
                        $this->errors[] = Module::getInstanceByName('giftcard')->l('Invalid date value.');
                        $this->ajaxDie(json_encode([
                            'hasError' => true,
                            'success' => false,
                            'errors' => $this->errors,
                        ]));
                    } else {
                        $this->setContextCart();
                        $this->customization_id = (int) $this->setGiftCardData();
                        $this->friend_name = pSQL($giftDetails['reciptent']);
                        $this->friend_email = pSQL($giftDetails['email']);
                        $this->gift_message = pSQL($giftDetails['message']);
                        $this->specific_date = ($this->specific_date_check) ? date('Y-m-d', strtotime($giftDetails['specific_date'])) : '';
                        $this->id_gift_card_template = (int) Tools::getValue('email_template');
                    }
                }
            }
        }
    }
    protected function processChangeProductInCart()
    {
        parent::processChangeProductInCart();
        if (!count($this->errors) && $this->is_gift_product) {
            Gift::orderGC([
                'id_cart' => $this->context->cart->id,
                'id_product' => $this->id_product,
                'reference_product' => $this->reference_product,
                'gift_type' => $this->gift_order_type,
                'friend_name' => $this->friend_name,
                'friend_email' => $this->friend_email,
                'gift_message' => $this->gift_message,
                'specific_date' => $this->specific_date,
                'id_gift_card_template' => $this->id_gift_card_template,
            ]);
        }
    }
    protected function processDeleteProductInCart()
    {
        parent::processDeleteProductInCart();
        Hook::exec(
            'actionGiftCardDeleteFromCart',
            [
                'id_product' => $this->id_product,
                'id_cart' => $this->context->cart->id,
            ]
        );
    }
    protected function setGiftCardData()
    {
        $giftDetails = Tools::getValue('gift_vars');
        if (!empty(trim($giftDetails['reciptent'])) && !empty(trim($giftDetails['email']))) {
            $id_customization = 0;
            $giftDetails['type'] = Module::getInstanceByName('giftcard')->translations[$this->gift_order_type];
            foreach (Module::getInstanceByName('giftcard')->field_labels as $key => $label) {
            }
            return $id_customization;
        }
    }
    protected function setContextCart()
    {
        if (!$this->errors) {
            if (!$this->context->cart->id) {
                if (Context::getContext()->cookie->id_guest) {
                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                }
                $this->context->cart->add();
                if ($this->context->cart->id) {
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                }
            }
        }
    }
}
