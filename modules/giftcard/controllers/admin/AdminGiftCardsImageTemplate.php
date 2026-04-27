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
class AdminGiftCardsImageTemplateController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'giftcard_image_template';
        $this->className = 'GiftCardImageTemplateModel';
        $this->identifier = 'id_giftcard_image_template';
        $this->lang = true;
        $this->bootstrap = true;
        $this->context = Context::getContext();

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
            'id_giftcard_image_template' => [
                'title' => 'ID',
                'search' => false,
            ],
            'gc_image' => [
                'title' => $this->module->l('Image'),
                'orderby' => true,
                'width' => 'auto',
                'search' => false,
                'callback' => 'getColumnImageContent',
            ],
            'name' => [
                'title' => $this->module->l('Name'),
                'width' => 'auto',
                'search' => false,
                'filter_key' => 'pl!name',
                'havingFilter' => true,
            ],
            'active' => [
                'title' => $this->module->l('Status'),
                'width' => 70,
                'search' => false,
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'orderby' => false,
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

    public function initContent()
    {
        parent::initContent();
        $this->context->controller->addJS('https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js');
    }

    public function getColumnImageContent($icon, $row)
    {
        if (!empty($icon)) {
            $image_url = Media::getMediaPath(Tools::getShopDomainSsl(true) . _PS_IMG_ . '/giftcard_templates/' . $icon);
            if (file_exists(_PS_IMG_DIR_ . 'giftcard_templates/' . $icon)) {
                $languages = Language::getLanguages();
                $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
                $template_text = [];
                foreach ($languages as &$lang) {
                    $template_text[$lang['id_lang']] = $this->module->l('prestashop');
                }
                $this->context->smarty->assign('default_form_language', $this->context->language->id);
                $this->context->smarty->assign([
                    'preview_img_url' => $image_url,
                    'price' => $row['price'],
                    'discount_code' => $row['discount_code'],
                    'is_customization_enabled' => Configuration::get('GIFT_CARD_CUSTOMIZATION_ENABLED'),
                    'template_text' => $template_text,
                    'bg_color' => $row['bg_color'],
                    'pdf_image_only' => Tools::getValue('pdf_image_only', 0),
                    'languages' => $languages,
                    'token' => Tools::getAdminTokenLite('AdminGiftCardTemplate'),
                    'id_giftcard_image_template' => $this->identifier,
                ]);
                $image_url = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'giftcard/views/templates/admin/gift_cards_image_template/assets/gc_template_img_list.tpl');

                return $image_url;
            }
        }
    }

    public function renderForm()
    {
        $this->loadObject(true);
        $id_giftcard_image_template = Tools::getValue('id_giftcard_image_template');
        $module_obj = new GiftCardImageTemplateModel($id_giftcard_image_template);
        $thumbnail_path = false;
        $filename = $module_obj->gc_image;
        if (!empty($filename)) {
            $file_path = _PS_IMG_DIR_ . 'giftcard_templates/' . $filename;
            $web_path = Media::getMediaPath(Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . '/img/giftcard_templates/' . $filename);

            if (file_exists($file_path)) {
                $thumbnail_path = ImageManager::thumbnail($file_path, $filename, 250, 'png', true);
                if (!$thumbnail_path) {
                    $thumbnail_path = $web_path;
                }
            }
        }

        $languages = Language::getLanguages();
        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
        $template_text = [];
        foreach ($languages as &$lang) {
            $template_text[$lang['id_lang']] = $this->module->l('prestashop');
        }
        $this->context->smarty->assign('default_form_language', $this->context->language->id);
        $this->context->smarty->assign([
            'preview_img_url' => $module_obj->gc_image ? Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . '/img/giftcard_templates/' . $module_obj->gc_image : '',
            'price' => $module_obj->price ?: Tools::getValue('price', 100),
            'discount_code' => $module_obj->discount_code ?: Tools::getValue('discount_code', 'XXXXXXXXXX'),
            'is_customization_enabled' => Configuration::get('GIFT_CARD_CUSTOMIZATION_ENABLED'),
            'template_text' => $template_text,
            'bg_color' => $module_obj->bg_color ?: Tools::getValue('bg_color', '#8bcdd1'),
            'pdf_image_only' => Tools::getValue('pdf_image_only', 0),
            'languages' => $languages,
            'token' => Tools::getAdminTokenLite('AdminGiftCardTemplate'),
            'id_giftcard_image_template' => $this->identifier,
        ]);
        $customPreviewHtml = Configuration::get('GIFT_CARD_CUSTOMIZATION_ENABLED') ?
            $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'giftcard/views/templates/admin/gift_cards_image_template/assets/gc_template_img.tpl') : $thumbnail_path;

        $deleteUrls = AdminController::$currentIndex
            . '&updategiftcard_image_template'
            . '&id_giftcard_image_template=' . $id_giftcard_image_template
            . '&gc_image=' . $module_obj->gc_image
            . '&action=delimg' . $this->module->name
            . '&token=' . Tools::getAdminTokenLite('AdminGiftCardsImageTemplate');

        if (Configuration::get('GIFT_CARD_CUSTOMIZATION_ENABLED')) {
        }
        $tabs = [
            'gc_information' => $this->module->l('Template Information'),
        ];

        if (Configuration::get('GIFT_CARD_CUSTOMIZATION_ENABLED')) {
            $tabs['gc_customization'] = $this->module->l('Template Customization');
        }
        $inputs = [
            [
                'type' => 'hidden',
                'name' => $this->identifier,
            ],
            [
                'type' => 'text',
                'label' => $this->module->l('Name'),
                'name' => 'name',
                'lang' => true,
                'required' => true,
                'tab' => 'gc_information',
            ],
            [
                'type' => 'file',
                'label' => $this->module->l('Image'),
                'name' => 'gc_image',
                'image' => $customPreviewHtml ?: '',
                'delete_url' => $deleteUrls,
                'display_image' => true,
                'lang' => true,
                'tab' => 'gc_information',
                'desc' => $this->module->l('only png images are allowed.'),
            ],
            [
                'type' => 'switch',
                'label' => $this->module->l('Active'),
                'name' => 'active',
                'is_bool' => true,
                'values' => [
                    ['id' => 'active_on', 'value' => 1, 'label' => $this->module->l('Enabled')],
                    ['id' => 'active_off', 'value' => 0, 'label' => $this->module->l('Disabled')],
                ],
                'tab' => 'gc_information',
            ],
        ];

        if (Configuration::get('GIFT_CARD_CUSTOMIZATION_ENABLED') && !empty($module_obj->gc_image)) {
            $inputs[] = [
                'type' => 'color',
                'name' => 'bg_color',
                'col' => 12,
                'class' => 'mColorPickerInput',
                'tab' => 'gc_customization',
            ];
        }

        $this->fields_form = [
            'legend' => ['title' => $this->module->l('Gift Card Image Template')],
            'tabs' => $tabs,
            'input' => $inputs,
            'submit' => ['title' => $this->module->l('Save')],
        ];
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = [
                'type' => 'shop',
                'label' => $this->module->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            ];
        }
        $languages = Language::getLanguages();
        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');
        $template_text = [];
        foreach ($languages as &$lang) {
            $template_text[$lang['id_lang']] = !empty($module_obj->template_text) ? $module_obj->template_text[$lang['id_lang']] : $this->module->l('prestashop');
        }
        $this->context->smarty->assign('default_form_language', $this->context->language->id);

        $this->context->smarty->assign([
            // 'preview_img_url' => _PS_BASE_URL_SSL_. _PS_IMG_ . 'giftcard_templates/' . $module_obj->gc_image,
            'preview_img_url' => $module_obj->gc_image ? Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . 'img/giftcard_templates/' . $module_obj->gc_image : '',
            'price' => $module_obj->price ?: '100',
            'discount_code' => $module_obj->discount_code ?: 'xxxxxxxxxx',
            'is_customization_enabled' => Configuration::get('GIFT_CARD_CUSTOMIZATION_ENABLED'),
            'template_text' => $template_text,
            'bg_color' => $module_obj->bg_color ?: '#8bcdd1',
            'pdf_image_only' => Tools::getValue('pdf_image_only', 0),
            'languages' => $languages,
            'token' => Tools::getAdminTokenLite('AdminGiftCardTemplate'),
            'id_giftcard_image_template' => $this->identifier,
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

    public function postProcess()
    {
        if (Tools::isSubmit('submitAdd' . $this->table)) {
            $id = (int) Tools::getValue('id_giftcard_image_template');
            $obj = $id ? new GiftCardImageTemplateModel($id) : new GiftCardImageTemplateModel();

            $existing_image = $obj->gc_image;

            $obj->price = (int) Tools::getValue('price') ?: 100;
            $obj->discount_code = Tools::getValue('discount_code') ?: 'xxxxxxxxxx';
            $obj->bg_color = Tools::getValue('bg_color') ?: '#8bcdd1';
            $obj->active = (int) Tools::getValue('active');
            $languages = Language::getLanguages(false);
            foreach ($languages as $lang) {
                $id_lang = (int) $lang['id_lang'];
                $obj->name[$id_lang] = Tools::getValue('name_' . $id_lang);
                $obj->tags[$id_lang] = Tools::getValue('tags_' . $id_lang);
                $obj->template_text[$id_lang] = Tools::getValue('template_text_' . $id_lang) ?: $this->module->l('Prestashop');
                if($this->context->language->id == $lang['id_lang'] && empty(Tools::getValue('name_' . $id_lang))){
                   return $this->errors[] = $this->module->l('Please enter name for current language first.');
                }
            }
            if (isset($_FILES['gc_image']) && $_FILES['gc_image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['png'];
                $extension = strtolower(pathinfo($_FILES['gc_image']['name'], PATHINFO_EXTENSION));
                if (!in_array($extension, $allowed)) {
                    return $this->errors[] = $this->module->l('Only PNG images are allowed.');
                }
            } elseif (!isset($_FILES['gc_image']) || empty($_FILES['gc_image']['name'])) {
                return $this->errors[] = $this->module->l('Please upload image.');
            }
            parent::postProcess();

            if (isset($_FILES['gc_image']) && $_FILES['gc_image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['png'];
                $extension = strtolower(pathinfo($_FILES['gc_image']['name'], PATHINFO_EXTENSION));
                if (in_array($extension, $allowed)) {
                    if (!$obj->id) {
                        $obj->add();
                    }

                    $folder = _PS_IMG_DIR_ . 'giftcard_templates/';
                    if (!is_dir($folder)) {
                        mkdir($folder, 0755, true);
                    }

                    if (!empty($existing_image)) {
                        $oldPath = $folder . $existing_image;
                        if (file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    $newFileName = 'giftcard_template_' . $obj->id . '.' . $extension;
                    $targetPath = $folder . $newFileName;
                    if (move_uploaded_file($_FILES['gc_image']['tmp_name'], $targetPath)) {
                        $obj->gc_image = $newFileName;
                    } else {
                        return $this->errors[] = $this->module->l('Image upload failed.');
                    }
                } else {
                    return $this->errors[] = $this->module->l('Only PNG images are allowed.');
                }
            } else {
                $obj->gc_image = $existing_image;
            }
            if ($obj->id) {
                $obj->update();
            } else {
                $obj->add();
            }

            $this->updateAssoShop($obj->id);
            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
        } elseif (Tools::isSubmit('status' . $this->table)) {
            $this->toggleField('active');
        } elseif(Tools::isSubmit('delete'. $this->table)){
            $obj = new GiftCardImageTemplateModel((int) Tools::getValue('id_giftcard_image_template'));
            if($obj){
                $obj->delete();
            return $this->confirmations[] = $this->module->l('Record deleted successfully.');
            }
        }
    }

    public function updateAssoShop($id_giftcard_image_template)
    {
        if (Shop::isFeatureActive()) {
            $shops = Tools::getValue('checkBoxShopAsso');

            if ($shops && is_array($shops)) {
                $giftcardTemplate = new GiftCardImageTemplateModel((int) $id_giftcard_image_template);
                if (Validate::isLoadedObject($giftcardTemplate)) {
                    $giftcardTemplate->associateTo($shops);
                }
            }
        }
    }

    protected function delImage()
    {
        $id_giftcard_image_template = (int) Tools::getValue('id_giftcard_image_template');
        $image = Tools::getValue('gc_image');
        $module_obj = new GiftCardImageTemplateModel($id_giftcard_image_template);
        $output = '';
        if (!empty($image)) {
            $filepath = _PS_IMG_DIR_ . 'giftcard_templates/' . $image;
            if (file_exists($filepath)) {
                unlink($filepath);
                $module_obj->gc_image = '';
            }
            $module_obj->save();

            return $this->confirmations[] = $this->module->l('Image deleted successfully, save the changes');
        }

        return $this->errors[] = $this->module->l('Image cannot deleted');
    }

    protected function toggleField($field)
    {
        if ($id = (int) Tools::getValue($this->identifier)) {
            $object = $this->loadObject();
            if (Validate::isLoadedObject($object)) {
                $object->$field = !$object->$field;
                if ($object->update()) {
                    Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token);
                }
            }
        }
    }
    // public function setMedia($isNewTheme = false)
    // {
    //     parent::setMedia($isNewTheme);

    //     $this->addJS(_PS_JS_DIR_ . 'jquery/jquery.min.js');
    //     $this->addJS(_PS_JS_DIR_ . 'jquery/plugins/mColorPicker.js');
    //     $this->addCSS(_PS_CSS_DIR_ . 'jquery/plugins/mColorPicker.css');
    // }
}
