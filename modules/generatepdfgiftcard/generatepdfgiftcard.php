<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class GeneratePDFGiftCard extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'generatepdfgiftcard';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'David Forero';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Generate PDF Gift Card');
        $this->description = $this->l('Allows customers to download their gift cards as PDF with custom message');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        return parent::install() && 
               $this->registerHook('displayBackOfficeHeader') &&
               $this->registerHook('displayMyGiftCardsButtons') &&
               $this->createTable() &&
               $this->installDemoData();
    }

    public function uninstall()
    {
        return parent::uninstall() && 
               $this->unregisterHook('displayBackOfficeHeader') &&
               $this->unregisterHook('displayMyGiftCardsButtons') &&
               $this->deleteTable();
    }

    protected function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'generatepdfgiftcard_templates` (
            `id_template` INT(11) NOT NULL AUTO_INCREMENT,
            `id_lang` INT(11) NOT NULL,
            `content` MEDIUMTEXT NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_template`, `id_lang`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        return Db::getInstance()->execute($sql);
    }

    protected function deleteTable()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'generatepdfgiftcard_templates`
        ');
    }

    protected function installDemoData()
    {
        $default_content = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 2px solid #ddd; padding: 20px;">
    <div style="text-align: center; margin-bottom: 20px;">
        <img src="{shop_logo}" alt="{shop_name}" style="max-width: 200px;">
        <h2 style="color: #333;">{gift_title}</h2>
    </div>
    
    <div style="text-align: center; margin: 20px 0;">
        <img src="{gift_image}" alt="Gift Card" style="max-width: 300px; border-radius: 8px;">
    </div>
    
    <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3 style="text-align: center; color: #d9534f;">Code: {code}</h3>
        <h1 style="text-align: center; color: #5cb85c; font-size: 36px;">{amount}</h1>
    </div>
    
    <div style="margin: 20px 0;">
        <p><strong>Expires:</strong> {expiry_date}</p>
        <p><strong>Purchased by:</strong> {customer_name}</p>
        <p><strong>Order:</strong> {order_reference}</p>
        <p><strong>Purchase date:</strong> {date_purchased}</p>
    </div>
    
    {if $custom_message}
    <div style="border-left: 4px solid #337ab7; padding-left: 15px; margin: 20px 0;">
        <h4>Personal Message:</h4>
        <p>{custom_message}</p>
    </div>
    {/if}
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777;">
        <p>Present this gift card at {shop_name} to redeem. Terms and conditions apply.</p>
    </div>
</div>';

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            Db::getInstance()->insert('generatepdfgiftcard_templates', [
                'id_lang' => (int)$lang['id_lang'],
                'content' => pSQL($default_content, true),
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s')
            ]);
        }
        return true;
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addCSS(_PS_JS_DIR_.'tinymce/tinymce.min.js');
            $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
            $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce_setup.js');
            
            Media::addJsDef([
                'baseAdminDir' => basename(_PS_ADMIN_DIR_),
                'iso_tiny_mce' => $this->context->language->iso_code,
                'default_language' => (int)Configuration::get('PS_LANG_DEFAULT')
            ]);
        }
    }

    public function hookDisplayMyGiftCardsButtons($params)
    {
        if (!$this->isCustomerAllowed()) {
            return '';
        }

        $this->context->smarty->assign([
            'id_cart_rule' => $params['id_cart_rule'],
            'module_link' => $this->context->link->getModuleLink($this->name, 'downloadpdf'),
            'module_path' => $this->_path
        ]);

        return $this->display(__FILE__, 'views/templates/front/button.tpl');
    }

    private function isCustomerAllowed()
    {
        $restrictedEmails = Configuration::get('GENERATEPDF_EMAILS');
        if (empty($restrictedEmails)) {
            return true;
        }

        if (!$this->context->customer->isLogged()) {
            return false;
        }

        $customerEmail = $this->context->customer->email;
        $allowedEmails = array_map('trim', explode(',', $restrictedEmails));
        
        return in_array($customerEmail, $allowedEmails);
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit_generatepdf')) {
            $this->processSave();
        }

        return $this->renderForm();
    }

    protected function processSave()
    {
        // Save email restrictions
        Configuration::updateValue('GENERATEPDF_EMAILS', Tools::getValue('restricted_emails'));
        
        // Save templates
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $content = Tools::getValue('GENERATEPDF_CONTENT_'.(int)$lang['id_lang']);
            
            Db::getInstance()->update('generatepdfgiftcard_templates', [
                'content' => pSQL($content, true),
                'date_upd' => date('Y-m-d H:i:s')
            ], 'id_lang = '.(int)$lang['id_lang']);
        }

        Tools::clearSmartyCache();
        Tools::clearCache();

        return $this->displayConfirmation($this->l('Settings updated'));
    }

    public function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $languages = $this->getHelperFormLanguages($default_lang);

        $form = new HelperForm();
        $form->module = $this;
        $form->name_controller = $this->name;
        $form->token = Tools::getAdminTokenLite('AdminModules');
        $form->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $form->title = $this->displayName;
        $form->submit_action = 'submit_generatepdf';
        $form->default_form_language = $default_lang;
        $form->allow_employee_form_lang = $default_lang;
        $form->languages = $languages;

        // Load saved values
        $restricted_emails = Configuration::get('GENERATEPDF_EMAILS');
        $form->fields_value['restricted_emails'] = $restricted_emails;

        foreach ($languages as $lang) {
            $content = Db::getInstance()->getValue('
                SELECT content FROM '._DB_PREFIX_.'generatepdfgiftcard_templates
                WHERE id_lang = '.(int)$lang['id_lang']
            );
            $form->fields_value['GENERATEPDF_CONTENT'][$lang['id_lang']] = $content;
        }

        $available_vars = [
            '{code}' => $this->l('Gift card code'),
            '{amount}' => $this->l('Gift card amount (formatted)'),
            '{expiry_date}' => $this->l('Expiration date'),
            '{gift_image}' => $this->l('URL of gift card image'),
            '{shop_logo}' => $this->l('URL of shop logo'),
            '{shop_name}' => $this->l('Shop name'),
            '{order_reference}' => $this->l('Order reference'),
            '{customer_name}' => $this->l('Customer full name'),
            '{custom_message}' => $this->l('Custom message (from modal)'),
            '{gift_title}' => $this->l('Gift card title/name'),
            '{date_purchased}' => $this->l('Purchase date')
        ];

        $vars_html = '';
        foreach ($available_vars as $var => $desc) {
            $vars_html .= '<button type="button" class="btn btn-default btn-sm variable-btn" data-var="'.htmlspecialchars($var).'" style="margin: 2px;">'.$var.'</button> ';
        }

        $fields_form = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('Email Restriction'),
                        'icon' => 'icon-envelope'
                    ],
                    'input' => [
                        [
                            'type' => 'textarea',
                            'label' => $this->l('Restrict to emails'),
                            'name' => 'restricted_emails',
                            'desc' => $this->l('Enter comma-separated emails. Leave empty to allow all customers.'),
                            'cols' => 60,
                            'rows' => 3,
                            'class' => 'form-control'
                        ]
                    ]
                ]
            ],
            [
                'form' => [
                    'legend' => [
                        'title' => $this->l('PDF Template Editor'),
                        'icon' => 'icon-file-pdf-o'
                    ],
                    'description' => '<div class="alert alert-info">
                        <p><strong>'.$this->l('Available variables:').'</strong></p>
                        <div class="variable-list">'.$vars_html.'</div>
                        <p class="mt-2">'.$this->l('Click on a variable to insert it at cursor position.').'</p>
                    </div>',
                    'input' => [
                        [
                            'type' => 'textarea',
                            'label' => $this->l('PDF Template'),
                            'name' => 'GENERATEPDF_CONTENT',
                            'autoload_rte' => false,
                            'lang' => true,
                            'cols' => 60,
                            'rows' => 25,
                            'class' => 'rte autoload_rte',
                            'hint' => $this->l('HTML template for PDF generation. Use variables above.')
                        ]
                    ],
                    'submit' => [
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-success pull-right'
                    ]
                ]
            ]
        ];

        return $form->generateForm($fields_form);
    }

    protected function getHelperFormLanguages($defaultLang)
    {
        $languages = Language::getLanguages(false);

        foreach ($languages as &$language) {
            if (!array_key_exists('is_default', $language)) {
                $language['is_default'] = ((int) $language['id_lang'] === (int) $defaultLang) ? 1 : 0;
            }
        }

        unset($language);

        return $languages;
    }

    public function getTemplateForLanguage($id_lang)
    {
        return Db::getInstance()->getValue('
            SELECT content FROM '._DB_PREFIX_.'generatepdfgiftcard_templates
            WHERE id_lang = '.(int)$id_lang
        );
    }

    //public function getGiftCardData($id_cart_rule)
    //{
        // This method will be called from the downloadpdf controller
        // You'll need to adapt it based on your giftcard module structure
      //  $id_lang = $this->context->language->id;
        //$id_customer = $this->context->customer->id;

        //$sql = 'SELECT cr.*, crl.name as gift_title, gcc.id_product, pl.link_rewrite,
          //             i.id_image, cr.date_to as expiry_date, cr.date_add as date_purchased
            //    FROM `'._DB_PREFIX_.'cart_rule` cr
              //  LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)$id_lang.')
                //LEFT JOIN `'._DB_PREFIX_.'gift_card_customer` gcc ON (cr.id_cart_rule = gcc.id_cart_rule)
                //LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (gcc.id_product = pl.id_product AND pl.id_lang = '.(int)$id_lang.')
              //  LEFT JOIN `'._DB_PREFIX_.'image` i ON (gcc.id_product = i.id_product AND i.cover = 1)
                //WHERE cr.id_cart_rule = '.(int)$id_cart_rule.'
                //AND gcc.id_customer = '.(int)$id_customer;

        //return Db::getInstance()->getRow($sql);
    //}
}
