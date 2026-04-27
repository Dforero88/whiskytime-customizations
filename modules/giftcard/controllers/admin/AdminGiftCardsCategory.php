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

class AdminGiftCardsCategoryController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'category';
        $this->className = 'Category';
        $this->identifier = 'id_category';
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
    }

    public function renderList()
    {
        $giftCategoryId = (int) Configuration::get('GIFT_CARD_CATEGORY');

        if ($giftCategoryId) {
            Tools::redirectAdmin(self::$currentIndex . '&update' . $this->table . '&' . $this->identifier . '=' . $giftCategoryId . '&token=' . $this->token);
        } else {
            Tools::redirectAdmin(self::$currentIndex . '&add' . $this->table . '&token=' . $this->token);
        }

        return '';
    }

    public function renderForm()
    {
        $radio = (Tools::version_compare(_PS_VERSION_, '1.6.0.0', '>=')) ? 'switch' : 'radio';
        // $thumb_url = false;
        // if ($this->object->id) {
        //     // $fileName = sprintf('giftcard_category_%d_%d', $this->object->id, $this->context->shop->id);
        //     $thumb_file = Configuration::get('GIFT_CATEGORY_THUMBNAIL');
        //     $tempFile = _PS_TMP_IMG_DIR_ . $thumb_file;
        //     $actualFile = GiftTemplates::checkFile($tempFile);
        //     $baseName = pathinfo($actualFile, PATHINFO_BASENAME);
        //     $type = pathinfo($actualFile, PATHINFO_EXTENSION);
        //     $thumb_url = ImageManager::thumbnail($actualFile, $baseName, 180, $type, true);
        // }

        $thumb_url = false;

        $id_category = (int) Configuration::get('GIFT_CARD_CATEGORY');

        if ($id_category) {
            $imagePath = _PS_CAT_IMG_DIR_ . $id_category . '.jpg';
            if (file_exists($imagePath)) {
                $thumb_url = ImageManager::thumbnail(
                    $imagePath,
                    'category_thumb_' . $id_category . '.jpg',
                    180,
                    'jpg',
                    true
                );
            }
        }

        $this->toolbar_title = $this->module->l('Edit Category');
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
                    'type' => $radio,
                    'class' => 't',
                    'is_bool' => true,
                    'label' => $this->module->l('Enable Category'),
                    'name' => 'active',
                    'values' => [
                        [
                            'id' => 'gc_status_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled'),
                        ],
                        [
                            'id' => 'gc_status_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled'),
                        ],
                    ],
                    // 'tab' => 'product',
                ],
                [
                    'type' => $radio,
                    'class' => 't',
                    'is_bool' => true,
                    'label' => $this->module->l('Show Category in Top Menu'),
                    'name' => 'show_menu',
                    'values' => [
                        [
                            'id' => 'gc_menu_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled'),
                        ],
                        [
                            'id' => 'gc_menu_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled'),
                        ],
                    ],
                    // 'tab' => 'product',
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Category Name'),
                    'lang' => true,
                    'desc' => $this->module->l('Category name to show in Top Menu and Gift Card Product form.'),
                    'name' => 'name',
                    'required' => true,
                    'col' => 7,
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Category Rewrite-link'),
                    'lang' => true,
                    'desc' => $this->module->l('Edit rewrite link (formate: gift-cards) it will be visiale in url.'),
                    'name' => 'link_rewrite',
                    'required' => true,
                    'col' => 7,
                ],
                [
                    'type' => 'textarea',
                    'lang' => true,
                    'autoload_rte' => true,
                    'label' => $this->module->l('Category Description'),
                    'name' => 'description',
                    'class' => 'rte autoload_rte',
                ],
                [
                    'type' => 'file',
                    'label' => $this->module->l('Template thumbnail'),
                    'desc' => $this->module->l('set a thumnail for your template.'),
                    'name' => 'image',
                    'display_image' => true,
                    'image' => $thumb_url ? $thumb_url : false,
                    'delete_url' => ($this->object->id) ? sprintf('%s&%s=%d&token=%s&deleteImage=1', self::$currentIndex, $this->identifier, $this->object->id, $this->token) : '',
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
                'class' => 'btn button btn-default pull-right',
            ],
        ];

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
        $id_category = (int) Configuration::get('GIFT_CARD_CATEGORY');
        $category = new Category($id_category, $this->context->language->id);

        if (Validate::isLoadedObject($category)) {
            $this->fields_value['active'] = (int) $category->active;
            $this->fields_value['show_menu'] = (int) Configuration::get('GIFT_CATEGORY_SHOW_MENU');
        }

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
        parent::postProcess();
        if (Tools::isSubmit(sprintf('submitAdd%s', $this->table))) {
            if (Configuration::updateValue('GIFT_CATEGORY_SHOW_MENU', (int) Tools::getValue('show_menu'))) {
                $id_linksmenutop = (int) Configuration::get('GC_TOPLINK_ID');
                $id_category = (int) Configuration::get('GIFT_CARD_CATEGORY');
                $id_shop = (int) $this->context->shop->id;
                $id_shop_group = (int) $this->context->shop->id_shop_group;

                if ($id_category) {
                    $menu_items = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $id_shop_group, $id_shop);
                    $link_identifier = 'CAT' . $id_category;
                    $show = (int) Configuration::get('GIFT_CATEGORY_SHOW_MENU');

                    $items = array_filter(explode(',', $menu_items));

                    if ($show && !in_array($link_identifier, $items)) {
                        $items[] = $link_identifier;
                    } elseif (!$show && in_array($link_identifier, $items)) {
                        $items = array_diff($items, [$link_identifier]);
                    }

                    $new_menu = implode(',', $items);
                    Configuration::updateValue('MOD_BLOCKTOPMENU_ITEMS', $new_menu, false, $id_shop_group, $id_shop);
                }
            }

            $id_category = (int) Configuration::get('GIFT_CARD_CATEGORY');

            if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $category = new Category($id_category, $this->context->language->id);
                if (!Validate::isLoadedObject($category)) {
                    $this->errors[] = $this->module->l('Invalid category.');
                } else {
                    $image_path = _PS_CAT_IMG_DIR_ . (int) $category->id . '.jpg';
                    // Optionally remove existing image
                    if (file_exists($image_path)) {
                        @unlink($image_path);
                    }

                    if (!ImageManager::resize($_FILES['image']['tmp_name'], $image_path)) {
                        $this->errors[] = $this->module->l('An error occurred while uploading the image.');
                    } else {
                        $this->confirmations[] = $this->module->l('Category image uploaded successfully.');
                    }
                }
            }

            $thumb = Tools::fileAttachment('image', false);

            if ($thumb && !$thumb['error']) {
                $ext = Tools::strtolower(pathinfo($thumb['name'], PATHINFO_EXTENSION));
                $imagePath = _PS_CAT_IMG_DIR_ . $id_category . '.' . $ext;

                if (file_exists($imagePath)) {
                    @unlink($imagePath);
                }

                if (!move_uploaded_file($thumb['tmp_name'], $imagePath)) {
                    $this->errors[] = $this->module->l('Image upload failed.');
                } else {
                    // Generate resized images
                    $imagesTypes = ImageType::getImagesTypes('categories');
                    foreach ($imagesTypes as $imageType) {
                        ImageManager::resize(
                            $imagePath,
                            _PS_CAT_IMG_DIR_ . $id_category . '-' . Tools::stripslashes($imageType['name']) . '.jpg',
                            (int)$imageType['width'],
                            (int)$imageType['height']
                        );
                    }
                    $this->confirmations[] = $this->module->l('Category image uploaded successfully.');
                }
            }

        } elseif (Tools::isSubmit('deleteImage')) {
            $this->processDeleteThumb();
        } elseif (Tools::isSubmit('show_menu' . $this->table)) {
            $this->toggleField('show_menu');
        }
    }

    public function processDeleteThumb()
    {
        $giftcardTemplate = $this->loadObject(true);

        if (Validate::isLoadedObject($giftcardTemplate)) {
            $fileName = Configuration::get('GIFT_CATEGORY_THUMBNAIL');
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
}
