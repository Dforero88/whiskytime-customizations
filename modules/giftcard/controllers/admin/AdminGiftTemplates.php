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
class AdminGiftTemplatesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'gift_card_template';
        $this->className = 'GiftTemplates';
        $this->identifier = 'id_gift_card_template';
        $this->context = Context::getContext();
        $this->deleted = false;
        $this->lang = true;
        $this->bootstrap = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected'),
                'confirm' => $this->module->l('Delete selected items?'),
            ],
        ];

        $this->fields_list = [
            'id_gift_card_template' => [
                'title' => '#',
                'width' => 25,
            ],
            'template_name' => [
                'title' => $this->module->l('Template'),
                'width' => 'auto',
                'filter_key' => 'pl!name',
                'havingFilter' => true,
            ],
            'status' => [
                'title' => $this->module->l('Status'),
                'width' => 70,
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'orderby' => false,
            ],
            'date_add' => [
                'title' => $this->module->l('Date'),
                'width' => 40,
                'align' => 'center',
                'type' => 'datetime',
            ],
        ];
        $this->context->smarty->assign([
            'edit_category_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsCategory'),
            'add_new_giftcard' => $this->context->link->getAdminLink('AdminGiftCards'),
            'add_img_template_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsImageTemplate'),
            'giftcard_settings' => $this->context->link->getAdminLink('AdminGiftSettings'),
            'gift_templates' => $this->context->link->getAdminLink('AdminGiftTemplates'),
            'ordered_giftcards' => $this->context->link->getAdminLink('AdminOrderedGiftcards'),
        ]);
    }

    public function initPageHeaderToolbar()
    {
        if (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
            if (empty($this->display)) {
                $this->page_header_toolbar_btn['new'] = [
                    'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                    'desc' => $this->module->l('Add Email Template'),
                    'icon' => 'process-icon-new',
                ];
            }
            parent::initPageHeaderToolbar();
        }
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit(sprintf('submitAdd%s', $this->table))) {
            $name = Tools::getValue('template_name');
            if (empty($name) || !isset($name)) {
                return $this->errors[] = $this->module->l('Template name is required.');
            }

            foreach (Language::getLanguages() as $lang) {
                $content = trim(Tools::safeOutput(Tools::getValue('content_' . $lang['id_lang'])));
                if (empty($content)) {
                    $this->errors[] = sprintf($this->module->l('Please enter template content for language: %s.'), $lang['name']);
                }
            }
        }
    }

    // public function postProcess()
    // {
    //     parent::postProcess();
    //     if (Tools::isSubmit(sprintf('submitAdd%s', $this->table))) {
    //         $thumb = Tools::fileAttachment('thumb', false);
    //         if (isset($thumb) && !$thumb['error']) {
    //             $thumb['type'] = $_FILES['thumb']['type'];
    //             if ($error = ImageManager::validateUpload($thumb, Tools::getMaxUploadSize())) {
    //                 return $this->errors[] = $error;
    //             }

    //             $ext = pathinfo($thumb['name'], PATHINFO_EXTENSION);
    //             dump($this->object);
    //             $tmpName = sprintf('%sgiftcard_template_%d_%d', _PS_TMP_IMG_DIR_, $this->object->id, $this->context->shop->id);
    //             $fileName = GiftTemplates::checkFile($tmpName);
    //             if (file_exists($fileName)) {
    //                 @unlink($fileName);
    //             }

    //             if (!$tmpName || !move_uploaded_file($thumb['tmp_name'], $tmpName . '.' . $ext)) {
    //                 return $this->errors = $this->module->l('An error occurred while uploading thumbnail image.');
    //             } else {
    //                 return $this->confirmations[] = $this->module->l('Thumbnail image uploaded successfuly.');
    //             }
    //         }
    //     } elseif (Tools::isSubmit('deleteImage')) {
    //         $this->processDeleteThumb();
    //     }
    // }
    public function postProcess()
{
    parent::postProcess();

    if (Tools::isSubmit(sprintf('submitAdd%s', $this->table))) {
        // Upload thumbnail only if object is saved and ID is available
        if ($this->id_object) {
            $thumb = Tools::fileAttachment('thumb', false);
            if (isset($thumb) && !$thumb['error']) {
                $thumb['type'] = $_FILES['thumb']['type'];

                if ($error = ImageManager::validateUpload($thumb, Tools::getMaxUploadSize())) {
                    $this->errors[] = $error;
                    return;
                }

                $ext = pathinfo($thumb['name'], PATHINFO_EXTENSION);
                $tmpName = sprintf('%sgiftcard_template_%d_%d', _PS_TMP_IMG_DIR_, $this->id_object, $this->context->shop->id);
                $fileName = GiftTemplates::checkFile($tmpName);

                if (file_exists($fileName)) {
                    @unlink($fileName);
                }

                if (!move_uploaded_file($thumb['tmp_name'], $tmpName . '.' . $ext)) {
                    $this->errors[] = $this->module->l('An error occurred while uploading thumbnail image.');
                } else {
                    $this->confirmations[] = $this->module->l('Thumbnail image uploaded successfully.');
                }
            }
        }
    } elseif (Tools::isSubmit('deleteImage')) {
        $this->processDeleteThumb();
    }
}


    public function processDeleteThumb()
    {
        $giftcardTemplate = $this->loadObject(true);

        if (Validate::isLoadedObject($giftcardTemplate)) {
            $fileName = sprintf('giftcard_template_%d_%d', $giftcardTemplate->id, $this->context->shop->id);
            $tempFile = _PS_TMP_IMG_DIR_ . $fileName;
            $actualFile = GiftTemplates::checkFile($tempFile);
            $templateName = pathinfo($actualFile, PATHINFO_BASENAME);
            if (file_exists(_PS_TMP_IMG_DIR_ . $templateName)
                && !unlink(_PS_TMP_IMG_DIR_ . $templateName)) {
                return false;
            }
        }

        return true;
    }

    public function renderForm()
    {
        $radio = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        $thumb_url = false;
        if ($this->object->id) {
            $fileName = sprintf('giftcard_template_%d_%d', $this->object->id, $this->context->shop->id);
            $tempFile = _PS_TMP_IMG_DIR_ . $fileName;
            $actualFile = GiftTemplates::checkFile($tempFile);
            $baseName = pathinfo($actualFile, PATHINFO_BASENAME);
            $type = pathinfo($actualFile, PATHINFO_EXTENSION);
            $thumb_url = ImageManager::thumbnail($actualFile, $baseName, 180, $type, true);
        }

        $this->toolbar_title = (Tools::getValue($this->identifier)) ? $this->module->l('Edit Template') : $this->module->l('Add Template');
        $this->fields_form = [
            'tinymce' => true,
            'legend' => [
                'title' => $this->toolbar_title,
                'icon' => 'icon-conf',
            ],
            'input' => [
                [
                    'type' => 'hidden',
                    'name' => $this->identifier,
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Template Name'),
                    'desc' => $this->module->l('template name for internal use.'),
                    'name' => 'template_name',
                    'required' => true,
                    'col' => 7,
                ],
                [
                    'type' => $radio,
                    'class' => 't',
                    'is_bool' => true,
                    'label' => $this->module->l('Status:'),
                    'name' => 'status',
                    'values' => [
                        [
                            'id' => 'status_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled'),
                        ],
                        [
                            'id' => 'status_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled'),
                        ],
                    ],
                    'tab' => 'product',
                ],
                [
                    'type' => 'file',
                    'label' => $this->module->l('Template thumbnail'),
                    'desc' => $this->module->l('set a thumnail for your template.'),
                    'name' => 'thumb',
                    'display_image' => true,
                    'image' => $thumb_url ? $thumb_url : false,
                    'delete_url' => ($this->object->id) ? sprintf('%s&%s=%d&token=%s&deleteImage=1', self::$currentIndex, $this->identifier, $this->object->id, $this->token) : '',
                ],

                [
                    'type' => 'textarea',
                    'lang' => true,
                    'autoload_rte' => true,
                    'label' => $this->module->l('Content'),
                    'name' => 'content',
                    'class' => 'rte autoload_rte',
                    'desc' => $this->module->l('Use the following placeholders to customize your gift card content. Each placeholder will be replaced with actual values when the gift card is generated:
                    - {quantity}: Quantity of gift cards
                    - {vcode}: Unique gift card code
                    - {shop_logo}: Shop logo image
                    - {expire_date}: Expiry date of the gift card
                    - {shop_name}: Name of the shop
                    - {shop_url}: URL of the shop
                    - {sender}: Sender’s name
                    - {rec_name}: Recipient’s name
                    - {message}: Personal message for the recipient
                    - {value}: Value of the gift card
                    - {giftcard_name}: Name of the gift card
                    - {gift_image}: Gift card image
                    '),
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
                'class' => 'btn button btn-default pull-right',
            ],
        ];

        // Display this field only if multistore option is enabled AND there are several stores configured
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->module->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            ];
        }

        $languages = Language::getLanguages(false);
        if (!Tools::getValue($this->identifier)) {
            $this->fields_value['status'] = true;
            $this->fields_value['template_name'] = '';
            foreach ($languages as $lang) {
                $this->fields_value['content'][$lang['id_lang']] = '';
            }
        }

        $module_mails = [
            'giftcard' => $this->getMailFiles(_PS_MODULE_DIR_ . $this->module->name . '/mails/' . $this->context->language->iso_code . '/', 'module_mail'),
        ];

        $iso_lang = $this->context->language->iso_code;
        $ad = __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_);
        $iso_tiny_mce = (Tools::file_exists_cache(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso_lang . '.js') ? $iso_lang : 'en');
        $this->context->smarty->assign([
            'ad' => $ad,
            'this' => $this,
            'id_html' => 'giftcard',
            'lang' => $iso_lang,
            'mails' => $module_mails,
            'iso_tiny_mce' => $iso_tiny_mce,
            'THEME_CSS_DIR' => _THEME_CSS_DIR_,
            'obj_lang' => $this->context->language,
            'id_lang' => $this->context->language->id,
            'languages' => Language::getLanguages(false),
            'ajax_callback' => $this->context->link->getAdminLink('AdminGiftTemplates'),
        ]);

        $this->context->smarty->assign([
            'edit_category_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsCategory'),
            'add_new_giftcard' => $this->context->link->getAdminLink('AdminGiftCards'),
            'add_img_template_giftcard' => $this->context->link->getAdminLink('AdminGiftCardsImageTemplate'),
            'giftcard_settings' => $this->context->link->getAdminLink('AdminGiftSettings'),
            'gift_templates' => $this->context->link->getAdminLink('AdminGiftTemplates'),
            'ordered_giftcards' => $this->context->link->getAdminLink('AdminOrderedGiftcards'),
        ]);

        return parent::renderForm();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        Media::addjsDef([
            'size_error' => $this->module->l('File size is too large'),
            'max_upload_limit' => Configuration::get('PS_LIMIT_UPLOAD_IMAGE_VALUE'),
        ]);

        $this->addJs([
            __PS_BASE_URI__ . 'js/tiny_mce/tiny_mce.js',
            __PS_BASE_URI__ . 'js/admin/tinymce.inc.js',
            $this->module->getPathUri() . 'views/js/admin_gift_templates.js',
        ]);
    }

    /**
     * Get each informations for each mails found in the folder $dir.
     *
     * @since 1.4.0.14
     *
     * @param string $dir
     * @param string $group_name
     *
     * @return array : list of mails
     */
    public function getMailFiles($dir, $group_name = 'mail')
    {
        $arr_return = [];
        if (Language::getIdByIso('en')) {
            $default_language = 'en';
        } else {
            $default_language = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));
        }
        if (!$default_language || !Validate::isLanguageIsoCode($default_language)) {
            return false;
        }

        // Very usefull to name input and textarea fields
        $arr_return['group_name'] = $group_name;
        $arr_return['empty_values'] = 0;
        $arr_return['total_filled'] = 0;
        $arr_return['directory'] = $dir;

        // Get path for english mail directory
        $dir_en = str_replace('/' . $this->context->language->iso_code . '/', '/' . $default_language . '/', $dir);

        if (Tools::file_exists_cache($dir_en)) {
            // Get all english files to compare with the language to translate
            foreach (scandir($dir_en) as $email_file) {
                if (strripos($email_file, '.html') > 0 || strripos($email_file, '.txt') > 0) {
                    $email_name = Tools::substr($email_file, 0, strripos($email_file, '.'));
                    $type = Tools::substr($email_file, strripos($email_file, '.') + 1);
                    if (!isset($arr_return['files'][$email_name])) {
                        $arr_return['files'][$email_name] = [];
                    }
                    // $email_file is from scandir ($dir), so we already know that file exists
                    $arr_return['files'][$email_name][$type]['en'] = $this->getMailContent($dir_en, $email_file);

                    // check if the file exists in the language to translate
                    if (Tools::file_exists_cache($dir . '/' . $email_file)) {
                        $arr_return['files'][$email_name][$type][$this->context->language->iso_code] = $this->getMailContent($dir, $email_file);
                    } else {
                        $arr_return['files'][$email_name][$type][$this->context->language->iso_code] = '';
                    }

                    if ($arr_return['files'][$email_name][$type][$this->context->language->iso_code] == '') {
                        ++$arr_return['empty_values'];
                    } else {
                        ++$arr_return['total_filled'];
                    }
                }
            }
        } else {
            $this->warnings[] = sprintf(
                $this->module->l('A mail directory exists for the "%1$s" language, but not for the default language (%3$s) in %2$s'),
                $this->lang_selected->iso_code,
                str_replace(_PS_ROOT_DIR_, '', dirname($dir)),
                $default_language
            );
        }

        return $arr_return;
    }

    /**
     * Get content of the mail file.
     *
     * @since 1.4.0.14
     *
     * @param string $dir
     * @param string $file
     *
     * @return array : content of file
     */
    protected function getMailContent($dir, $file)
    {
        $content = Tools::file_get_contents($dir . '/' . $file);

        if (Tools::strlen($content) === 0) {
            $content = '';
        }

        return $content;
    }

    public function ajaxProcessGetEmailContent()
    {
        $email = Tools::getValue('email');
        exit($this->getEmailHTML($email));
    }

    public function getEmailHTML($email)
    {
        if (defined('_PS_HOST_MODE_') && strpos($email, _PS_MAIL_DIR_) !== false) {
            $email_file = $email;
        } elseif (__PS_BASE_URI__ != '/') {
            $email_file = str_replace(__PS_BASE_URI__, '', _PS_ROOT_DIR_ . '/') . $email;
        } else {
            $email_file = _PS_ROOT_DIR_ . $email;
        }

        $email_html = Tools::file_get_contents($email_file);

        return $email_html;
    }
}