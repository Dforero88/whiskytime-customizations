<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 * Description
 *
 * Admin can change text color,translations,font style
 */

class Ageverification extends Module
{
    public function __construct()
    {
        $this->name = 'ageverification';
        $this->tab = 'front_office_features';
        $this->version = '1.0.5';
        $this->author = 'Knowband';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->module_key = 'b6c496beb0d562e2bb1b4ff6d4a93e8b';
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('Age Verification');
        $this->description = $this->l('This module facilitates age verification of the website visitors');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('AGE_VERIFICATION')) {
            $this->warning = $this->l('No name provided');
        }
    }
    
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('displayHeader')) {
            return false;
        }

        $defaultsettings = $this->getDefaultSettings();
        Configuration::updateValue('AGE_VERIFICATION', serialize($defaultsettings));
        Configuration::updateValue('AGE_VERIFICATION_PREVIEW', serialize($defaultsettings));
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        $this->unregisterHook('displayHeader');
        $this->unregisterHook('header');
        return true;
    }
    
    public function hookHeader($params)
    {
        return $this->hookDisplayHeader($params);
    }

    public function hookDisplayHeader($params)
    {
        $values = Tools::unSerialize(Configuration::get('AGE_VERIFICATION'));
                
        if (Tools::getValue('controller') == 'preview') {
            return;
        }
        
        $show = 0;
        
        if ($values['popup_display_method'] == 'complete') {
            $show = 1;
        } else {
            if ($this->context->controller->php_self == 'category') {
                $current_category_id = Tools::getValue('id_category');
                if (!Tools::isEmpty($values['prestashop_category'])) {
                    $show_age_verification_popup_category_list = (array) $values['prestashop_category'];
                    if (count($show_age_verification_popup_category_list) && in_array($current_category_id, $show_age_verification_popup_category_list)) {
                        $show = 1;
                    }
                }
            } else if ($this->context->controller->php_self == 'product') {
                $current_product_id = Tools::getValue('id_product');
                $prod_obj = new Product($current_product_id);
                $show_popup_on_products = $values['excluded_products_hidden'];
                if (!Tools::isEmpty($show_popup_on_products)) {
                    $show_popup_on_products_list = explode(',', $show_popup_on_products);
                    if (count($show_popup_on_products_list) && in_array($current_product_id, $show_popup_on_products_list)) {
                        $show = 1;
                    }
                }
                if ($values['enable_product_page'] == 1) {
                    if (!Tools::isEmpty($values['prestashop_category'])) {
                        $show_age_verification_popup_category_list = (array) $values['prestashop_category'];
                        if (count($show_age_verification_popup_category_list) && in_array($prod_obj->id_category_default, $show_age_verification_popup_category_list)) {
                            $show = 1;
                        }
                    }
                }
            } else if ($this->context->controller->php_self == 'cms') {
                $current_cms_id = Tools::getValue('id_cms');
                $show_age_verification_popup_cms_pages = $values['kbageverification_private_pages'];
                if (count($show_age_verification_popup_cms_pages) && in_array($current_cms_id, $show_age_verification_popup_cms_pages)) {
                    $show = 1;
                }
            }
        }

        if ($values['enable'] == 1 && $show == 1) {
            if (!isset($_COOKIE["kbage_popup_check"])) {
                $this->context->controller->addCSS($this->_path . 'views/css/front/popup.css', 'all');
                $this->context->controller->addJS($this->_path . 'views/js/front/popup.js', 'all');

                $year = array();
                $x = date("Y");
                $min_year = date("Y") - 99;
                $num = 0;
                while ($x >= $min_year) {
                    $year[$num] = $x;
                    $num++;
                    $x--;
                }
                if (Tools::getShopProtocol() == 'https://') {
                    $av_protocol_name = _PS_BASE_URL_SSL_ . _MODULE_DIR_;
                } else {
                    $av_protocol_name = _PS_BASE_URL_ . _MODULE_DIR_;
                }
                $additional_info_message_value = $values['age_verification_popup_additional_info_message'][$this->context->language->id];
                $additional_info_message_value = Tools::htmlentitiesDecodeUTF8($additional_info_message_value);
                
                $popup_background_color = $values['popup_background_color'];
                $popup_rgb_color = $this->rgb2hex2rgb($popup_background_color);
                
                if ((strpos($values['logo_image_path'], 'show.jpg') == false)) {
                    $kblogo_image_path = $values['logo_image_path'];
                } else {
                    $kblogo_image_path = '';
                }
                
                if ((strpos($values['verification_window_image_path'], 'show.jpg') == false)) {
                    $kbverification_window_image_path = $values['verification_window_image_path'];
                } else {
                    $kbverification_window_image_path = '';
                }

                if ((strpos($values['verification_background_image_path'], 'show.jpg') == false)) {
                    $kbverification_background_image_path = $values['verification_background_image_path'];
                } else {
                    $kbverification_background_image_path = '';
                }

                $this->context->smarty->assign(
                    array(
                        'kbage_verification_values' => $values,
                        'kbage_verification_popup_additional_info_message' => $additional_info_message_value,
                        'popup_rgb_color' => $popup_rgb_color,
                        'kblang_id' => $this->context->language->id,
                        'kbcurrentyear' => date("Y"),
                        'av_year' => $year,
                        'current_timest' => strtotime("now"),
                        'av_image_path' => $av_protocol_name,
                        'kblogo_image_path' => $kblogo_image_path,
                        'kbverification_window_image_path' => $kbverification_window_image_path,
                        'kbverification_background_image_path' => $kbverification_background_image_path,
                    )
                );
                return $this->display(__FILE__, 'views/templates/hook/popup.tpl');
            }
        }
    }
    
    private function getCMSPageList()
    {
        $cmsArray = array();
        $cmsPages = CMS::getCMSPages($this->context->language->id, null, true, $this->context->shop->id);
        foreach ($cmsPages as $key => $value) {
            $cmsArray[] = array(
                'id_cms' => $value['id_cms'],
                'meta_title' => $value['meta_title']);
        }
        return $cmsArray;
    }

    public function getContent()
    {
        $formvalue = array();
        $previous_saved_data = array();
        $output = null;
        $languages = Language::getLanguages(true);
        $config = Configuration::get('AGE_VERIFICATION');
        $formvalue = Tools::unSerialize($config);
        if (isset($this->context->cookie->kb_redirect_success)) {
            $output .= $this->displayConfirmation($this->context->cookie->kb_redirect_success);
            unset($this->context->cookie->kb_redirect_success);
        }
        
        if (Tools::getvalue('ajaxproductaction')) {
            echo $this->ajaxproductlist();
            die;
        }
        
        if (Tools::isSubmit('ajax')) {
            if (Tools::getValue('method') == 'kbagepopuppreview') {
//                $formvalue = Tools::getvalue('age_verification');
                $this->savePreviewData();
            } else {
                $this->ajaxProcess(Tools::getValue('method'));
            }
        }

        $previous_saved_data = $formvalue;
        $categoryTreeSelection = array();
        
        if (Tools::isSubmit('age_verification')) {
//            $config = Configuration::get('AGE_VERIFICATION');
//            $dbvalue = Tools::unSerialize($config);
            $formvalue = Tools::getvalue('age_verification');
            
            $languages = Language::getLanguages(false); //Getting languages
            $error_count = 0;
            foreach ($languages as $lang) {
                $formvalue['age_verification_under_age_message'][$lang['id_lang']] = Tools::getValue('age_verification_under_age_message_' . $lang['id_lang']);
                $formvalue['age_verification_popup_message'][$lang['id_lang']] = Tools::getValue('age_verification_popup_message_' . $lang['id_lang']);
                $formvalue['age_verification_popup_dob_message'][$lang['id_lang']] = Tools::getValue('age_verification_popup_dob_message_' . $lang['id_lang']);
                $formvalue['age_verification_yes_button_text'][$lang['id_lang']] = Tools::getValue('age_verification_yes_button_text_' . $lang['id_lang']);
                $formvalue['age_verification_no_button_text'][$lang['id_lang']] = Tools::getValue('age_verification_no_button_text_' . $lang['id_lang']);
                $formvalue['age_verification_submit_button_text'][$lang['id_lang']] = Tools::getValue('age_verification_submit_button_text_' . $lang['id_lang']);
                $formvalue['age_verification_popup_additional_info_message'][$lang['id_lang']] = Tools::htmlentitiesUTF8(Tools::getValue('age_verification_popup_additional_info_message_' . $lang['id_lang']));
            }
            
            $formvalue['prestashop_category'] = Tools::getvalue('prestashop_category');
            $formvalue['kbageverification_private_pages'] = Tools::getvalue('kbageverification_private_pages');
            $categoryTreeSelection = $formvalue['prestashop_category'];

            $error_count = 0;

            if ($formvalue['age'] == null) {
                $output .= $this->displayError($this->l('Please enter the verification age.'));
                $error_count++;
            } else {
                if (!preg_match("/^[0-9]*$/", $formvalue['age'])) {
                    $output .= $this->displayError($this->l('Invalid input at verification age.'));
                    $error_count++;
                } else if ($formvalue['age'] > 100 || $formvalue['age'] <= 0) {
                    $output .= $this->displayError(
                        $this->l('Only range from 1-100 allowed at verification age.')
                    );
                    $error_count++;
                }
            }

            if ($formvalue['remember_visitor'] == null) {
                $output .= $this->displayError($this->l('Please enter the minimum number of days for which the cookie will be stored.'));
                $error_count++;
            } else {
                if (!preg_match("/^[0-9]*$/", $formvalue['remember_visitor']) || $formvalue['remember_visitor'] <= 0) {
                    $output .= $this->displayError($this->l('Invalid input for Remember Visitor field. Value must be greater than 0.'));
                    $error_count++;
                }
            }

            if ($formvalue['under_age_action'] == 2) {
                if ($formvalue['underage_redirect_url'] == null) {
                    $output .= $this->displayError($this->l('Please enter the redirect URL.'));
                    $error_count++;
                }

                $reg_exp = "/^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/)[a-z0-9]+([\-\.]{1}" .
                        "[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/";
                if (!preg_match($reg_exp, $formvalue['underage_redirect_url'])) {
                    $output .= $this->displayError($this->l('Invalid Redirect URL entered.'));
                    $error_count++;
                }
            }
            
            $max_file_size_allowed = 4194304;
            $allowed_extensions = array(
                "jpeg",
                "jpg",
                "png",
                "gif");
            $allowedTypes = array(
                'image/png',
                'image/jpg',
                'image/jpeg',
                'image/gif');
            if ($_FILES['age_verification']['name']['logo_file'] != null || !empty($_FILES['age_verification']['name']['logo_file'])) {
                $logo_file_name = $_FILES['age_verification']['name']['logo_file'];
                $logo_file_name = str_replace(" ", "_", $logo_file_name);
                $logo_file_size = $_FILES['age_verification']['size']['logo_file'];
                $logo_file_tmp = $_FILES['age_verification']['tmp_name']['logo_file'];

                $formvalue['logo_image_name'] = $logo_file_name;
                $formvalue['logo_image_size'] = $logo_file_size;
                $formvalue['logo_image_tmp_name'] = $logo_file_tmp;

                $logo_file_extension = explode('.', $logo_file_name);
                $logo_file_name = $logo_file_extension[0] . '_' . time();
                $image_ext = Tools::strtolower(end($logo_file_extension));


                $logo_prev_img = isset($previous_saved_data['logo_file']) ? $previous_saved_data['logo_file'] : '';
                $formvalue['logo_file'] = $logo_file_name . '.' . $image_ext;
                $logo_image_new_name = $formvalue['logo_file'];
                $image_path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/' . $this->name . '/views/img/admin/uploads/';
                $formvalue['logo_image_path'] = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $logo_image_new_name;
                $display_image_path = $formvalue['logo_image_path'];
                $detectedType = mime_content_type($_FILES['age_verification']['tmp_name']['logo_file']);
                if (in_array($detectedType, $allowedTypes) === false) {
                    $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                    $error_count++;
                }
                if (in_array($image_ext, $allowed_extensions) === false) {
                    $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                    $error_count++;
                }
                if ($logo_file_size >= $max_file_size_allowed) {
                    $output .= $this->displayError($this->l('File size must be less than 4 MB.'));
                    $error_count++;
                }
            }
            
            if ($_FILES['age_verification']['name']['verification_window_file'] != null || !empty($_FILES['age_verification']['name']['verification_window_file'])) {
                $verification_window_file_name = $_FILES['age_verification']['name']['verification_window_file'];
                $verification_window_file_name = str_replace(" ", "_", $verification_window_file_name);
                $verification_window_file_size = $_FILES['age_verification']['size']['verification_window_file'];
                $verification_window_file_tmp = $_FILES['age_verification']['tmp_name']['verification_window_file'];

                $formvalue['verification_window_image_name'] = $verification_window_file_name;
                $formvalue['verification_window_image_size'] = $verification_window_file_size;
                $formvalue['verification_window_image_tmp_name'] = $verification_window_file_tmp;

                $verification_window_file_extension = explode('.', $verification_window_file_name);
                $verification_window_file_name = $verification_window_file_extension[0] . '_' . time();
                $image_ext = Tools::strtolower(end($verification_window_file_extension));


                $verification_window_prev_img = isset($previous_saved_data['verification_window_file']) ? $previous_saved_data['verification_window_file'] : '';
                $formvalue['verification_window_file'] = $verification_window_file_name . '.' . $image_ext;
                $verification_window_image_new_name = $formvalue['verification_window_file'];
                $image_path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/' . $this->name . '/views/img/admin/uploads/';
                $formvalue['verification_window_image_path'] = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $verification_window_image_new_name;
                $display_image_path = $formvalue['verification_window_image_path'];
                $detectedType = mime_content_type($_FILES['age_verification']['tmp_name']['verification_window_file']);
                if (in_array($detectedType, $allowedTypes) === false) {
                    $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                    $error_count++;
                }
                if (in_array($image_ext, $allowed_extensions) === false) {
                    $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                    $error_count++;
                }
                if ($verification_window_file_size >= $max_file_size_allowed) {
                    $output .= $this->displayError($this->l('File size must be less than 4 MB.'));
                    $error_count++;
                }
            }
            
            if ($_FILES['age_verification']['name']['verification_background_file'] != null || !empty($_FILES['age_verification']['name']['verification_background_file'])) {
                $verification_background_file_name = $_FILES['age_verification']['name']['verification_background_file'];
                $verification_background_file_name = str_replace(" ", "_", $verification_background_file_name);
                $verification_background_file_size = $_FILES['age_verification']['size']['verification_background_file'];
                $verification_background_file_tmp = $_FILES['age_verification']['tmp_name']['verification_background_file'];

                $formvalue['verification_background_image_name'] = $verification_background_file_name;
                $formvalue['verification_background_image_size'] = $verification_background_file_size;
                $formvalue['verification_background_image_tmp_name'] = $verification_background_file_tmp;

                $verification_background_file_extension = explode('.', $verification_background_file_name);
                $verification_background_file_name = $verification_background_file_extension[0] . '_' . time();
                $image_ext = Tools::strtolower(end($verification_background_file_extension));


                $verification_background_prev_img = isset($previous_saved_data['verification_background_file']) ? $previous_saved_data['verification_background_file'] : '';
                $formvalue['verification_background_file'] = $verification_background_file_name . '.' . $image_ext;
                $verification_background_image_new_name = $formvalue['verification_background_file'];
                $image_path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/' . $this->name . '/views/img/admin/uploads/';
                $formvalue['verification_background_image_path'] = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $verification_background_image_new_name;
                $display_image_path = $formvalue['verification_background_image_path'];
                $detectedType = mime_content_type($_FILES['age_verification']['tmp_name']['verification_background_file']);
                if (in_array($detectedType, $allowedTypes) === false) {
                    $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                    $error_count++;
                }
                if (in_array($image_ext, $allowed_extensions) === false) {
                    $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                    $error_count++;
                }
                if ($verification_background_file_size >= $max_file_size_allowed) {
                    $output .= $this->displayError($this->l('File size must be less than 4 MB.'));
                    $error_count++;
                }
            }

            if (empty($formvalue['popup_background_color'])) {
                $output .= $this->displayError($this->l('Please select Popup Background Color'));
                $error_count++;
            }

            if (empty($formvalue['popup_text_color'])) {
                $output .= $this->displayError($this->l('Please select Popup Background Color'));
                $error_count++;
            }

            if ($formvalue['verification_method'] == 1) {
                if (empty($formvalue['popup_yes_button_color'])) {
                    $output .= $this->displayError($this->l('Please select Popup Background Color'));
                    $error_count++;
                }

                if (empty($formvalue['popup_yes_button_text_color'])) {
                    $output .= $this->displayError($this->l('Please select Popup Background Color'));
                    $error_count++;
                }

                if (empty($formvalue['popup_no_button_color'])) {
                    $output .= $this->displayError($this->l('Please select Popup Background Color'));
                    $error_count++;
                }

                if (empty($formvalue['popup_no_button_text_color'])) {
                    $output .= $this->displayError($this->l('Please select Popup Background Color'));
                    $error_count++;
                }
            } else {
                if (empty($formvalue['popup_submit_button_color'])) {
                    $output .= $this->displayError($this->l('Please select Popup Background Color'));
                    $error_count++;
                }

                if (empty($formvalue['popup_submit_button_text_color'])) {
                    $output .= $this->displayError($this->l('Please select Popup Background Color'));
                    $error_count++;
                }
            }

            if ($error_count == 0) {
                if (!isset($formvalue['logo_image_path'])) {
                    if (!isset($previous_saved_data['logo_image_path'])) {
                        $my_image = 'show.jpg';
                        $default_logo_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $my_image;
                        $formvalue['logo_image_path'] = $default_logo_image_path;
                    } else {
                        $formvalue['logo_image_path'] = $previous_saved_data['logo_image_path'];
                    }
                }

//                if ((strpos($formvalue['logo_image_path'], 'show.jpg') == false)) {
//                    $this->context->smarty->assign('logoimageexist', true);
//                } else {
//                    $this->context->smarty->assign('logoimageexist', false);
//                }
                if ($_FILES['age_verification']['name']['logo_file'] != null) {
                    $logo_image_path = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/';
                    if (isset($logo_prev_img) && $logo_prev_img != '') {
                        $mask = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/' . $logo_prev_img;
                        array_map('unlink', glob($mask));
                    }
                    move_uploaded_file($logo_file_tmp, $logo_image_path . $logo_image_new_name);
                }
                
                if (!isset($formvalue['verification_window_image_path'])) {
                    if (!isset($previous_saved_data['verification_window_image_path'])) {
                        $my_image = 'show.jpg';
                        $default_verification_window_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $my_image;
                        $formvalue['verification_window_image_path'] = $default_verification_window_image_path;
                    } else {
                        $formvalue['verification_window_image_path'] = $previous_saved_data['verification_window_image_path'];
                    }
                }

//                if ((strpos($formvalue['verification_window_image_path'], 'show.jpg') == false)) {
//                    $this->context->smarty->assign('verification_window_imageexist', true);
//                } else {
//                    $this->context->smarty->assign('verification_window_imageexist', false);
//                }
                if ($_FILES['age_verification']['name']['verification_window_file'] != null) {
                    $verification_window_image_path = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/';
                    if (isset($verification_window_prev_img) && $verification_window_prev_img != '') {
                        $mask = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/' . $verification_window_prev_img;
                        array_map('unlink', glob($mask));
                    }
                    move_uploaded_file($verification_window_file_tmp, $verification_window_image_path . $verification_window_image_new_name);
                }
                
                if (!isset($formvalue['verification_background_image_path'])) {
                    if (!isset($previous_saved_data['verification_background_image_path'])) {
                        $my_image = 'show.jpg';
                        $default_verification_background_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $my_image;
                        $formvalue['verification_background_image_path'] = $default_verification_background_image_path;
                    } else {
                        $formvalue['verification_background_image_path'] = $previous_saved_data['verification_background_image_path'];
                    }
                }

//                if ((strpos($formvalue['verification_background_image_path'], 'show.jpg') == false)) {
//                    $this->context->smarty->assign('verification_background_imageexist', true);
//                } else {
//                    $this->context->smarty->assign('verification_background_imageexist', false);
//                }
                if ($_FILES['age_verification']['name']['verification_background_file'] != null) {
                    $verification_background_image_path = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/';
                    if (isset($verification_background_prev_img) && $verification_background_prev_img != '') {
                        $mask = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/' . $verification_background_prev_img;
                        array_map('unlink', glob($mask));
                    }
                    move_uploaded_file($verification_background_file_tmp, $verification_background_image_path . $verification_background_image_new_name);
                }
                Configuration::updateValue('AGE_VERIFICATION', serialize($formvalue));
                Configuration::updateValue('AGE_VERIFICATION_PREVIEW', serialize($formvalue));
                $this->context->cookie->__set('kb_redirect_success', $this->l('Configuration has been saved successfully.'));
                Tools::redirectAdmin(AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $this->name);
            }
        }

        $action = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules');

        $ps_version = 16;
        $this->context->controller->addJs($this->_path . 'views/js/velovalidation.js');
        $this->context->controller->addJs($this->_path . 'views/js/admin/age_verification_admin.js');
        $this->context->controller->addCSS($this->_path . 'views/css/admin/age_verification_admin.css');

        $this->available_tabs_lang = array(
            'General_Settings' => $this->l('General Settings'),
            'Content' => $this->l('Content Settings'),
            'Look_and_Feel_Settings' => $this->l('Look and Feel Settings'),
        );

        $this->available_tabs = array(
            'General_Settings',
            'Content',
            'Look_and_Feel_Settings'
        );

        $this->tab_display = 'General_Settings';

        $module_tabs = array();

        foreach ($this->available_tabs as $tab) {
            $module_tabs[$tab] = array(
                'id' => $tab,
                'selected' => (Tools::strtolower($tab) == Tools::strtolower($this->tab_display) ||
                    (isset($this->tab_display_module) && 'module' .
                $this->tab_display_module == Tools::strtolower($tab))),
                'name' => $this->available_tabs_lang[$tab],
                'href' => AdminController::$currentIndex .'&token='. Tools::getAdminTokenLite('AdminModules'),
                );
        }

        $this->context->smarty->assign('show_toolbar', false);
                
        $theme_array = array(
            array(
                'id' => 1,
                'name' => $this->l('Design 1'),
            ),
            array(
                'id' => 2,
                'name' => $this->l('Design 2'),
            ),
            array(
                'id' => 3,
                'name' => $this->l('Design 3'),
            ),
            array(
                'id' => 4,
                'name' => $this->l('Design 4'),
            ),
            array(
                'id' => 5,
                'name' => $this->l('Design 5'),
            ),
            array(
                'id' => 6,
                'name' => $this->l('Design 6'),
            ),
            array(
                'id' => 7,
                'name' => $this->l('Design 7'),
            ),
            array(
                'id' => 8,
                'name' => $this->l('Design 8'),
            ),
            array(
                'id' => 9,
                'name' => $this->l('Design 9'),
            ),
        );
        
        $verification_methods_array = array(
            array(
                'id' => 1,
                'name' => $this->l('Yes/No Button'),
            ),
            array(
                'id' => 2,
                'name' => $this->l('Year of Birth'),
            ),
            array(
                'id' => 3,
                'name' => $this->l('Date of Birth'),
            )
        );
        
        $date_format_array = array(
            array(
                'id' => 1,
                'name' => $this->l('DD/MM/YYYY'),
            ),
            array(
                'id' => 2,
                'name' => $this->l('MM/DD/YYYY'),
            ),
        );
        
        $under_age_action_array = array(
            array(
                'id' => 1,
                'name' => $this->l('Show Message'),
            ),
            array(
                'id' => 2,
                'name' => $this->l('Redirect to URL'),
            ),
        );
        
        $text_alignment_array = array(
            array(
                'id' => 'center',
                'name' => $this->l('Center'),
            ),
            array(
                'id' => 'left',
                'name' => $this->l('Left'),
            ),
            array(
                'id' => 'right',
                'name' => $this->l('Right'),
            ),
        );
        
        $popup_shape_array = array(
            array(
                'id' => 1,
                'name' => $this->l('Rectangle'),
            ),
            array(
                'id' => 2,
                'name' => $this->l('Rounded'),
            ),
        );
        
        $imagedir_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/';
        $this->context->smarty->assign('imagedir_path', $imagedir_path);
        
        
//        if ($formvalue['enable_default_images'] == 1) {
//            $selectedtheme = $formvalue['choose_theme'];
//            $display_logo_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/theme' . $selectedtheme . '/logo.png';
//            $display_verification_window_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/theme' . $selectedtheme . '/side-img.jpg';
//            $display_verification_background_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/theme' . $selectedtheme . '/main-bg.jpg';
//        } else {
        $default_logo_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/show.jpg';
        if (isset($previous_saved_data['logo_image_path']) && $previous_saved_data['logo_image_path'] != '') {
            $display_logo_image_path = $previous_saved_data['logo_image_path'];
            if ((strpos($display_logo_image_path, 'show.jpg') == false)) {
                $this->context->smarty->assign('logoimageexist', 1);
            } else {
                $this->context->smarty->assign('logoimageexist', 0);
            }
        } else {
            $this->context->smarty->assign('logoimageexist', 0);
            $display_logo_image_path = $default_logo_image_path;
        }

        $default_verification_window_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/show.jpg';
        if (isset($previous_saved_data['verification_window_image_path']) && $previous_saved_data['verification_window_image_path'] != '') {
            $display_verification_window_image_path = $previous_saved_data['verification_window_image_path'];
            if ((strpos($display_verification_window_image_path, 'show.jpg') == false)) {
                $this->context->smarty->assign('verificationwindowimageexist', 1);
            } else {
                $this->context->smarty->assign('verificationwindowimageexist', 0);
            }
        } else {
            $display_verification_window_image_path = $default_verification_window_image_path;
            $this->context->smarty->assign('verificationwindowimageexist', 0);
        }

        $default_verification_background_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/show.jpg';
        if (isset($previous_saved_data['verification_background_image_path']) && $previous_saved_data['verification_background_image_path'] != '') {
            $display_verification_background_image_path = $previous_saved_data['verification_background_image_path'];
            if ((strpos($display_verification_background_image_path, 'show.jpg') == false)) {
                $this->context->smarty->assign('verificationbackgroundimageexist', 1);
            } else {
                $this->context->smarty->assign('verificationbackgroundimageexist', 0);
            }
        } else {
            $display_verification_background_image_path = $default_verification_background_image_path;
            $this->context->smarty->assign('verificationbackgroundimageexist', 0);
        }
//        }

        $this->context->smarty->assign('display_logo_image_path', $display_logo_image_path);
        $this->context->smarty->assign('display_window_image_path', $display_verification_window_image_path);
        $this->context->smarty->assign('display_background_image_path', $display_verification_background_image_path);
        
        
        $form_value = Tools::unSerialize(Configuration::get('AGE_VERIFICATION'));
        $selectedproducts = array();
        if (isset($form_value['excluded_products_hidden']) && (!Tools::isEmpty($form_value['excluded_products_hidden']))) {
            $selectedProductIds = explode(',', $form_value['excluded_products_hidden']);
            foreach ($selectedProductIds as $productId) {
                $productDetails = new Product($productId);
                $selectedproducts[] = array(
                    'product_id' => $productId,
                    'title' => $productDetails->name[$this->context->language->id],
                    'reference' => $productDetails->reference);
            }
        }
        $this->context->smarty->assign('selectedproducts', $selectedproducts);
        
        if (!empty($form_value['prestashop_category'])) {
            $categoryTreeSelection = $form_value['prestashop_category'];
        }
        $categoryTreeSelection = (array) $categoryTreeSelection;

        $root = Category::getRootCategory();
        //Generating the tree for the first column
        $tree = new HelperTreeCategories('prestashop_category'); //The string in param is the ID used by the generated tree
        $tree->setUseCheckBox(true)
            ->setAttribute('is_category_filter', $root->id)
            ->setRootCategory($root->id)
            ->setSelectedCategories($categoryTreeSelection)
            ->setInputName('prestashop_category')
            ->setUseSearch(true)
            //->setDisabledCategories($categoryListDisabled)
            ->setFullTree(true); //Set the name of input. The option "name" of $fields_form doesn't seem to work with "categories_select" type

        $categoryTreePresta = $tree->render();

        $this->fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('General Settings'),
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable/Disable '),
                        'hint' => $this->l('Enable/Disable this plugin'),
                        'name' => 'age_verification[enable]',
                        'values' => array(
                            array(
                                'id' => 'age_verification[enable]_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'age_verification[enable]_off',
                                'value' => 0,
                            ),
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Verification Age'),
                        'name' => 'age_verification[age]',
                        'hint' => $this->l('Enter the minimum age allowed to visit into website.'),
                        'class' => 'optn_general',
                        'required' => true,
                        'col' => 2,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Verification Method'),
                        'name' => 'age_verification[verification_method]',
                        'hint' => $this->l('Choose the Verification method for date of birth validation'),
                        'class' => 'optn_general',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $verification_methods_array,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Date Format'),
                        'name' => 'age_verification[dob_format]',
                        'hint' => $this->l('Choose the Date of Birth Format which will be shown on popup'),
                        'class' => 'optn_general',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $date_format_array,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Remember Visitor (Days)'),
                        'name' => 'age_verification[remember_visitor]',
                        'hint' => $this->l('Enter the minimum number of days for which the cookie will be store. The Popup will be shown again for age verfication after this amount of time.'),
                        'class' => 'optn_general',
                        'required' => true,
                        'col' => 2,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Under Age Action'),
                        'name' => 'age_verification[under_age_action]',
                        'hint' => $this->l('Select an action when a customer age is less than the lowest permitted age.'),
                        'class' => 'optn_general',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $under_age_action_array,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Under Age Message'),
                        'name' => 'age_verification_under_age_message',
                        'hint' => $this->l('Set a Message to be displayed for underage Customers.'),
                        'class' => 'optn_general',
                        'lang' => true,
                        'required' => true,
//                        'autoload_rte' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Redirect URL'),
                        'name' => 'age_verification[underage_redirect_url]',
                        'hint' => $this->l('Enter the URL to redirect if the Customer is underage.'),
                        'desc' => $this->l('Example -') . ' http://www.google.com',
                        'class' => 'optn_general',
                        'required' => true,
                    ),
                    array(
                        'type' => 'radio',
                        'class' => 'age_popup_method_radio',
                        'label' => $this->l('Select Popup Display Method'),
                        'name' => 'age_verification[popup_display_method]',
                        'values' => array(
                            array(
                                'id' => 'complete',
                                'label' => $this->l('Complete Shop'),
                                'value' => 'complete'
                            ),
                            array(
                                'id' => 'selected',
                                'label' => $this->l('Only Selected'),
                                'value' => 'selected',
                            ),
                        ),
                        'hint' => $this->l('Select Method according to which Age Verification popup will be displayed on Selected Pages or on Complete Shop'),
                        'desc' => $this->l('Select Method according to which Age Verification popup will be displayed on Selected Pages or on Complete Shop'),
                    ),
                    array(
                        'label' => $this->l('Choose products'),
                        'type' => 'text',
                        'hint' => $this->l('Start typing the products name to choose.Select Product From List on Which You Want To Show Age Verification Popup.'),
                        'desc' => $this->l('Start typing the products name to choose.Select Product From List on Which You Want To Show Age Verification Popup.'),
                        'class' => 'ac_input',
                        'name' => 'age_verification[product_name]',
                        'autocomplete' => false,
                    ),
                    array(
                        'type' => 'html',
                        'name' => '',
                        'html_content' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ageverification/views/templates/admin/showSelectedProducts.tpl'),
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'age_verification[excluded_products_hidden]',
                    ),
                    array(
                        'type' => 'categories_select',
                        'label' => $this->l('Choose Popup Display Categories'),
                        'hint' => $this->l('Select category from list on Which You Want To Show Age Verification Popup.'),
                        'name' => 'prestashop_category',
                        'category_tree' => $categoryTreePresta //This is the category_tree called in form.tpl
                    ),
                    array(
                        'label' => $this->l('Enable/Disable Popup on Selected Category Product Pages.'),
                        'hint' => $this->l('IF Enabled then the Popup will be shown on the products of the Selected Default Categories'),
                        'name' => 'age_verification[enable_product_page]',
                        'type' => 'switch',
                        'values' => array(
                            array(
                                'id' => 'age_verification[enable_product_page]_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'age_verification[enable_product_page]_off',
                                'value' => 0,
                            ),
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Choose Popup Display Pages'),
                        'name' => 'kbageverification_private_pages[]',
                        'multiple' => 'multiple',
                        'desc' => $this->l('Select the pages on Which You Want To Show Age Verification Popup.'),
                        'hint' => $this->l('Select the pages on Which You Want To Show Age Verification Popup.'),
                        'id' => 'multiple-select-pages',
                        'options' => array(
                            'query' => $this->getCMSPageList(),
                            'id' => 'id_cms',
                            'name' => 'meta_title'
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Popup Message'),
                        'name' => 'age_verification_popup_message',
                        'hint' => $this->l('Enter the message to be displayed on the popup.'),
                        'class' => 'optn_general',
//                        'autoload_rte' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('DOB field Message'),
                        'name' => 'age_verification_popup_dob_message',
                        'hint' => $this->l('Enter the message to be displayed for entering the Date of Birth.'),
                        'class' => 'optn_general',
//                        'autoload_rte' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Enter Yes Button Text'),
                        'name' => 'age_verification_yes_button_text',
                        'hint' => $this->l('Enter the Yes Button Text'),
                        'class' => 'optn_general',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Enter No Button Text'),
                        'name' => 'age_verification_no_button_text',
                        'hint' => $this->l('Enter the No Button Text'),
                        'class' => 'optn_general',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Enter Submit Button Text'),
                        'name' => 'age_verification_submit_button_text',
                        'hint' => $this->l('Enter the Submit Button Text'),
                        'class' => 'optn_general',
                        'lang' => true,
                        'required' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Additional Info'),
                        'name' => 'age_verification_popup_additional_info_message',
                        'hint' => $this->l('Enter Additional Message like TERMS OF ENTRY, ETC'),
                        'class' => 'optn_general',
                        'autoload_rte' => true,
                        'lang' => true,
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Choose Theme'),
                        'name' => 'age_verification[choose_theme]',
                        'hint' => $this->l('Choose the theme'),
                        'class' => 'optn_general',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $theme_array,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Use Selected Theme Images'),
                        'hint' => $this->l('If enable then the selected design default images will be shown on the Popup otherwise you can upload your Custom Images.'),
                        'name' => 'age_verification[enable_default_images]',
                        'values' => array(
                            array(
                                'id' => 'age_verification[enable_default_images]_on',
                                'value' => 1,
                            ),
                            array(
                                'id' => 'age_verification[enable_default_images]_off',
                                'value' => 0,
                            ),
                        ),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Logo Image'),
                        'name' => 'age_verification[logo_file]',
                        'id' => 'age_verification_logo_file',
                        'display_image' => true,
//                        'desc' => $this->l('Only .png, .jpg, .jpeg, .gif file format accepted.'),
                        'hint' => $this->l('Upload Logo image only works when Selected Theme Images setting is disabled. Only .png, .jpg, .jpeg, .gif file format accepted.'),
                        'image' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ageverification/views/templates/admin/showLogoImg.tpl'),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Verification Window Image'),
                        'name' => 'age_verification[verification_window_file]',
                        'id' => 'age_verification_window_file',
                        'display_image' => true,
//                        'desc' => $this->l('Only .png, .jpg, .jpeg, .gif file format accepted.'),
                        'hint' => $this->l('Upload verification window image only works when Selected Theme Images setting is disabled. Only .png, .jpg, .jpeg, .gif file format accepted.'),
                        'image' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ageverification/views/templates/admin/showWindowImg.tpl'),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Background Image'),
                        'name' => 'age_verification[verification_background_file]',
                        'id' => 'age_verification_background_file',
                        'display_image' => true,
//                        'desc' => $this->l('Only .png, .jpg, .jpeg, .gif file format accepted.'),
                        'hint' => $this->l('Upload background image only works when Selected Theme Images setting is disabled. Only .png, .jpg, .jpeg, .gif file format accepted.'),
                        'image' => $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'ageverification/views/templates/admin/showBackgroundImg.tpl'),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Text Align'),
                        'name' => 'age_verification[text_align]',
                        'hint' => $this->l('Select Text Alignment'),
                        'class' => 'optn_general',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $text_alignment_array,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Popup Shape'),
                        'name' => 'age_verification[popup_shape]',
                        'hint' => $this->l('Select Popup Shape'),
                        'class' => 'optn_general',
                        'is_bool' => true,
                        'options' => array(
                            'query' => $popup_shape_array,
                            'id' => 'id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Popup Background Color'),
                        'name' => 'age_verification[popup_background_color]',
                        'suffix' => $this->l('Color'),
                        'validate' => 'isColor',
                        'class' => 'age_vss_color',
                        'hint' => $this->l('Please select the Popup Background Color'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Popup Opacity'),
                        'name' => 'age_verification[popup_opacity]',
                        'hint' => $this->l('Please enter Opacity value which will be applied on the left box of the Popup.'),
                        'required' => true,
                        'col' => 2,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Popup Text Color'),
                        'name' => 'age_verification[popup_text_color]',
                        'suffix' => $this->l('Color'),
                        'validate' => 'isColor',
                        'class' => 'age_vss_color',
                        'hint' => $this->l('Please select the Popup Text Color'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Submit Button Color'),
                        'name' => 'age_verification[popup_submit_button_color]',
                        'suffix' => $this->l('Color'),
                        'validate' => 'isColor',
                        'class' => 'age_vss_color',
                        'hint' => $this->l('Please select the Submit Button Color'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Submit Button Text Color'),
                        'name' => 'age_verification[popup_submit_button_text_color]',
                        'suffix' => $this->l('Color'),
                        'validate' => 'isColor',
                        'class' => 'age_vss_color',
                        'hint' => $this->l('Please select the Submit Button Text Color'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Yes Button Color'),
                        'name' => 'age_verification[popup_yes_button_color]',
                        'suffix' => $this->l('Color'),
                        'validate' => 'isColor',
                        'class' => 'age_vss_color',
                        'hint' => $this->l('Please select the Yes Button Color'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Yes Button Text Color'),
                        'name' => 'age_verification[popup_yes_button_text_color]',
                        'suffix' => $this->l('Color'),
                        'validate' => 'isColor',
                        'class' => 'age_vss_color',
                        'hint' => $this->l('Please select the Yes Button Text Color'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('No Button Color'),
                        'name' => 'age_verification[popup_no_button_color]',
                        'suffix' => $this->l('Color'),
                        'validate' => 'isColor',
                        'class' => 'age_vss_color',
                        'hint' => $this->l('Please select the No Button Color'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('No Button Text Color'),
                        'name' => 'age_verification[popup_no_button_text_color]',
                        'suffix' => $this->l('Color'),
                        'validate' => 'isColor',
                        'class' => 'age_vss_color',
                        'hint' => $this->l('Please select the No Button Text Color'),
                        'required' => true,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Popup Message Font Size'),
                        'name' => 'age_verification[popup_message_font_size]',
                        'suffix' => $this->l('px'),
                        'hint' => $this->l('Please enter popup message font size in range of 10-40 px'),
                        'required' => true,
                        'col' => 2,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Text Font Size'),
                        'name' => 'age_verification[text_font_size]',
                        'suffix' => $this->l('px'),
                        'hint' => $this->l('Please enter text font size in range of 10-20 px'),
                        'required' => true,
                        'col' => 2,
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Additional Info Font Size'),
                        'name' => 'age_verification[additional_info_font_size]',
                        'suffix' => $this->l('px'),
                        'hint' => $this->l('Please enter additional info font size in range of 10-20 px'),
                        'required' => true,
                        'col' => 2,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Custom CSS'),
                        'name' => 'age_verification[custom_css]',
                        'hint' => $this->l('Enter the css to customize your module excluding tags.Ex-" margin:10px; color:red; ",etc'),
                        'class' => 'optn_lookfeel vss-textarea',
                        'cols' => 100,
                        'rows' => 5
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Custom JS'),
                        'name' => 'age_verification[custom_js]',
                        'hint' => $this->l('Enter the js to customize your module excluding tags.'),
                        'class' => 'optn_lookfeel vss-textarea',
                        'cols' => 100,
                        'rows' => 5
                    ),
                ),
                'buttons' => array(
                    array(
                        'href' => 'javascript:void(0)',
                        'title' => $this->l('Popup Preview'),
                        'icon' => 'process-icon-preview',
                        'class' => 'kbageverification_popup_preview_btn'
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right velovalidation_age_verification'
                ),
            ),
        );
        
        $languages = Language::getLanguages(true);
        $under_age_message = array();
        foreach ($languages as $lang) {
            if (Tools::getValue('age_verification_under_age_message_' . $lang['id_lang'])) {
                $under_age_message[$lang['id_lang']] = Tools::getValue('age_verification_under_age_message_' . $lang['id_lang']);
            } else {
                $under_age_message[$lang['id_lang']] = $formvalue['age_verification_under_age_message'][$lang['id_lang']];
            }
        }
        
        $age_verification_popup_message = array();
        foreach ($languages as $lang) {
            if (Tools::getValue('age_verification_popup_message_' . $lang['id_lang'])) {
                $age_verification_popup_message[$lang['id_lang']] = Tools::getValue('age_verification_popup_message_' . $lang['id_lang']);
            } else {
                $age_verification_popup_message[$lang['id_lang']] = $formvalue['age_verification_popup_message'][$lang['id_lang']];
            }
        }
        
        $age_verification_popup_dob_message = array();
        foreach ($languages as $lang) {
            if (Tools::getValue('age_verification_popup_dob_message_' . $lang['id_lang'])) {
                $age_verification_popup_dob_message[$lang['id_lang']] = Tools::getValue('age_verification_popup_dob_message_' . $lang['id_lang']);
            } else {
                $age_verification_popup_dob_message[$lang['id_lang']] = $formvalue['age_verification_popup_dob_message'][$lang['id_lang']];
            }
        }
        
        $age_verification_submit_button_text = array();
        foreach ($languages as $lang) {
            if (Tools::getValue('age_verification_submit_button_text_' . $lang['id_lang'])) {
                $age_verification_submit_button_text[$lang['id_lang']] = Tools::getValue('age_verification_submit_button_text_' . $lang['id_lang']);
            } else {
                $age_verification_submit_button_text[$lang['id_lang']] = $formvalue['age_verification_submit_button_text'][$lang['id_lang']];
            }
        }
        
        $age_verification_yes_button_text = array();
        foreach ($languages as $lang) {
            if (Tools::getValue('age_verification_yes_button_text_' . $lang['id_lang'])) {
                $age_verification_yes_button_text[$lang['id_lang']] = Tools::getValue('age_verification_yes_button_text_' . $lang['id_lang']);
            } else {
                $age_verification_yes_button_text[$lang['id_lang']] = $formvalue['age_verification_yes_button_text'][$lang['id_lang']];
            }
        }
        
        $age_verification_no_button_text = array();
        foreach ($languages as $lang) {
            if (Tools::getValue('age_verification_no_button_text_' . $lang['id_lang'])) {
                $age_verification_no_button_text[$lang['id_lang']] = Tools::getValue('age_verification_no_button_text_' . $lang['id_lang']);
            } else {
                $age_verification_no_button_text[$lang['id_lang']] = $formvalue['age_verification_no_button_text'][$lang['id_lang']];
            }
        }
        
        $age_verification_popup_additional_info_message = array();
        foreach ($languages as $lang) {
            if (Tools::getValue('age_verification_popup_additional_info_message_' . $lang['id_lang'])) {
                $age_verification_popup_additional_info_message[$lang['id_lang']] = Tools::htmlentitiesDecodeUTF8(Tools::getValue('age_verification_popup_additional_info_message_' . $lang['id_lang']));
            } else {
                $age_verification_popup_additional_info_message[$lang['id_lang']] = Tools::htmlentitiesDecodeUTF8($formvalue['age_verification_popup_additional_info_message'][$lang['id_lang']]);
            }
        }
        
        $field_value = array(
            'age_verification[enable]' => $formvalue['enable'],
            'age_verification[choose_theme]' => $formvalue['choose_theme'],
            'age_verification[enable_default_images]' => $formvalue['enable_default_images'],
            'age_verification[age]' => $formvalue['age'],
            'age_verification[verification_method]' => $formvalue['verification_method'],
            'age_verification[dob_format]' => $formvalue['dob_format'],
            'age_verification[remember_visitor]' => $formvalue['remember_visitor'],
            'age_verification[under_age_action]' => $formvalue['under_age_action'],
            'age_verification_under_age_message' => $under_age_message,
            'age_verification[underage_redirect_url]' => $formvalue['underage_redirect_url'],
            'age_verification[popup_display_method]' => $formvalue['popup_display_method'],
            'age_verification[product_name]' => $formvalue['product_name'],
            'age_verification[excluded_products_hidden]' => $formvalue['excluded_products_hidden'],
            "kbageverification_private_pages[]" => $formvalue['kbageverification_private_pages'],
            'age_verification[enable_product_page]' => $formvalue['enable_product_page'],
            'age_verification_popup_message' => $age_verification_popup_message,
            'age_verification_popup_dob_message' => $age_verification_popup_dob_message,
            'age_verification_submit_button_text' => $age_verification_submit_button_text,
            'age_verification_yes_button_text' => $age_verification_yes_button_text,
            'age_verification_no_button_text' => $age_verification_no_button_text,
            'age_verification_popup_additional_info_message' => $age_verification_popup_additional_info_message,
            'age_verification[logo_file]' => $formvalue['logo_file'],
            'age_verification[verification_window_file]' => $formvalue['verification_window_file'],
            'age_verification[verification_background_file]' => $formvalue['verification_background_file'],
            'age_verification[text_align]' => $formvalue['text_align'],
            'age_verification[popup_shape]' => $formvalue['popup_shape'],
            'age_verification[popup_background_color]' => $formvalue['popup_background_color'],
            'age_verification[popup_opacity]' => $formvalue['popup_opacity'],
            'age_verification[popup_text_color]' => $formvalue['popup_text_color'],
            'age_verification[popup_submit_button_color]' => $formvalue['popup_submit_button_color'],
            'age_verification[popup_submit_button_text_color]' => $formvalue['popup_submit_button_text_color'],
            'age_verification[popup_yes_button_color]' => $formvalue['popup_yes_button_color'],
            'age_verification[popup_yes_button_text_color]' => $formvalue['popup_yes_button_text_color'],
            'age_verification[popup_no_button_color]' => $formvalue['popup_no_button_color'],
            'age_verification[popup_no_button_text_color]' => $formvalue['popup_no_button_text_color'],
            'age_verification[popup_message_font_size]' => $formvalue['popup_message_font_size'],
            'age_verification[text_font_size]' => $formvalue['text_font_size'],
            'age_verification[additional_info_font_size]' => $formvalue['additional_info_font_size'],
            'age_verification[custom_css]' => $formvalue['custom_css'],
            'age_verification[custom_js]' => $formvalue['custom_js'],
        );
        
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->table = 'configuration';
        $helper->fields_value = $field_value;
        
        $languages = Language::getlanguages(true);
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }
        
        $helper->name_controller = $this->name;
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = $action;
        $form = $helper->generateForm(array($this->fields_form));
        
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        } else {
            $custom_ssl_var = 0;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $ps_base_url = _PS_BASE_URL_SSL_;
        } else {
            $ps_base_url = _PS_BASE_URL_;
        }
        $this->context->smarty->assign('default_lang', $this->context->language->id);
        $this->context->smarty->assign('count_language', count(Language::getLanguages(true)));
        $this->context->smarty->assign('form', $form);
        $this->context->smarty->assign('path', $this->getPath());
        $this->context->smarty->assign('error_img_path', $ps_base_url . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_) . $this->name . '/');
        $this->context->smarty->assign('module_tabs', $module_tabs);
        $this->context->smarty->assign('firstCall', false);
        $this->context->smarty->assign('tab', $this->l($this->tab_display));
        $this->context->smarty->assign('version', $ps_version);
        $this->context->smarty->assign('view', '');
        $module_path = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&configure=' . $this->name;
        $this->context->smarty->assign('module_path', $module_path); //module path
        $this->context->smarty->assign('path_fold', $module_path . '&ajaxproductaction=true&');
        $default_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/show.jpg';
        $this->context->smarty->assign('default_image_path', $default_image_path);
        $this->context->smarty->assign('preview_url', $this->getKbAgeVerificationPreviewUrl());

        $tpl = 'Form_custom.tpl';
        $helper = new Helper();
        $helper->module = $this;
        $helper->override_folder = 'helpers/';
        $helper->base_folder = 'form/';
        $helper->setTpl($tpl);
        $tpl = $helper->generate();

        $output = $output . $tpl;
        return $output;
    }
    
    /*
     * Function to get the Preview URL for the module
     */
    private function getKbAgeVerificationPreviewUrl()
    {
        $preview_url = $this->context->link->getModuleLink(
            $this->name,
            'preview',
            array(),
            (bool) Configuration::get('PS_SSL_ENABLED')
        );
        return $preview_url;
    }
    
    /*
     * Function to check module url is secure or not
     */

    private function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }

    /*
     * Function to check url is secure or not
     */

    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    private function savePreviewData()
    {
        $output = null;
        $config = Configuration::get('AGE_VERIFICATION_PREVIEW');
        $previous_saved_data = Tools::unSerialize($config);
        $formvalue = Tools::getvalue('age_verification');
        $languages = Language::getLanguages(false); //Getting languages
        foreach ($languages as $lang) {
            $formvalue['age_verification_under_age_message'][$lang['id_lang']] = Tools::getValue('age_verification_under_age_message_' . $lang['id_lang']);
            $formvalue['age_verification_popup_message'][$lang['id_lang']] = Tools::getValue('age_verification_popup_message_' . $lang['id_lang']);
            $formvalue['age_verification_popup_dob_message'][$lang['id_lang']] = Tools::getValue('age_verification_popup_dob_message_' . $lang['id_lang']);
            $formvalue['age_verification_yes_button_text'][$lang['id_lang']] = Tools::getValue('age_verification_yes_button_text_' . $lang['id_lang']);
            $formvalue['age_verification_no_button_text'][$lang['id_lang']] = Tools::getValue('age_verification_no_button_text_' . $lang['id_lang']);
            $formvalue['age_verification_submit_button_text'][$lang['id_lang']] = Tools::getValue('age_verification_submit_button_text_' . $lang['id_lang']);
            $formvalue['age_verification_popup_additional_info_message'][$lang['id_lang']] = Tools::htmlentitiesUTF8(Tools::getValue('age_verification_popup_additional_info_message_' . $lang['id_lang']));
        }

        $formvalue['prestashop_category'] = Tools::getvalue('prestashop_category');
        $formvalue['kbageverification_private_pages'] = Tools::getvalue('kbageverification_private_pages');
        $categoryTreeSelection = $formvalue['prestashop_category'];

        $error_count = 0;
        $max_file_size_allowed = 4194304;
        $allowed_extensions = array(
            "jpeg",
            "jpg",
            "png",
            "gif");
        $allowedTypes = array(
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/gif');
        if ($_FILES['age_verification']['name']['logo_file'] != null || !empty($_FILES['age_verification']['name']['logo_file'])) {
            $logo_file_name = $_FILES['age_verification']['name']['logo_file'];
            $logo_file_name = str_replace(" ", "_", $logo_file_name);
            $logo_file_size = $_FILES['age_verification']['size']['logo_file'];
            $logo_file_tmp = $_FILES['age_verification']['tmp_name']['logo_file'];

            $formvalue['logo_image_name'] = $logo_file_name;
            $formvalue['logo_image_size'] = $logo_file_size;
            $formvalue['logo_image_tmp_name'] = $logo_file_tmp;

            $logo_file_extension = explode('.', $logo_file_name);
            $logo_file_name = $logo_file_extension[0] . '_' . time();
            $image_ext = Tools::strtolower(end($logo_file_extension));


            $logo_prev_img = isset($previous_saved_data['logo_file']) ? $previous_saved_data['logo_file'] : '';
            $formvalue['logo_file'] = $logo_file_name . '.' . $image_ext;
            $logo_image_new_name = $formvalue['logo_file'];
            $image_path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/' . $this->name . '/views/img/admin/uploads/';
            $formvalue['logo_image_path'] = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $logo_image_new_name;
            $display_image_path = $formvalue['logo_image_path'];
            $detectedType = mime_content_type($_FILES['age_verification']['tmp_name']['logo_file']);
            if (in_array($detectedType, $allowedTypes) === false) {
                $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                $error_count++;
            }
            if (in_array($image_ext, $allowed_extensions) === false) {
                $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                $error_count++;
            }
            if ($logo_file_size >= $max_file_size_allowed) {
                $output .= $this->displayError($this->l('File size must be less than 4 MB.'));
                $error_count++;
            }
        }

        if ($_FILES['age_verification']['name']['verification_window_file'] != null || !empty($_FILES['age_verification']['name']['verification_window_file'])) {
            $verification_window_file_name = $_FILES['age_verification']['name']['verification_window_file'];
            $verification_window_file_name = str_replace(" ", "_", $verification_window_file_name);
            $verification_window_file_size = $_FILES['age_verification']['size']['verification_window_file'];
            $verification_window_file_tmp = $_FILES['age_verification']['tmp_name']['verification_window_file'];

            $formvalue['verification_window_image_name'] = $verification_window_file_name;
            $formvalue['verification_window_image_size'] = $verification_window_file_size;
            $formvalue['verification_window_image_tmp_name'] = $verification_window_file_tmp;

            $verification_window_file_extension = explode('.', $verification_window_file_name);
            $verification_window_file_name = $verification_window_file_extension[0] . '_' . time();
            $image_ext = Tools::strtolower(end($verification_window_file_extension));


            $verification_window_prev_img = isset($previous_saved_data['verification_window_file']) ? $previous_saved_data['verification_window_file'] : '';
            $formvalue['verification_window_file'] = $verification_window_file_name . '.' . $image_ext;
            $verification_window_image_new_name = $formvalue['verification_window_file'];
            $image_path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/' . $this->name . '/views/img/admin/uploads/';
            $formvalue['verification_window_image_path'] = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $verification_window_image_new_name;
            $display_image_path = $formvalue['verification_window_image_path'];
            $detectedType = mime_content_type($_FILES['age_verification']['tmp_name']['verification_window_file']);
            if (in_array($detectedType, $allowedTypes) === false) {
                $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                $error_count++;
            }
            if (in_array($image_ext, $allowed_extensions) === false) {
                $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                $error_count++;
            }
            if ($verification_window_file_size >= $max_file_size_allowed) {
                $output .= $this->displayError($this->l('File size must be less than 4 MB.'));
                $error_count++;
            }
        }

        if ($_FILES['age_verification']['name']['verification_background_file'] != null || !empty($_FILES['age_verification']['name']['verification_background_file'])) {
            $verification_background_file_name = $_FILES['age_verification']['name']['verification_background_file'];
            $verification_background_file_name = str_replace(" ", "_", $verification_background_file_name);
            $verification_background_file_size = $_FILES['age_verification']['size']['verification_background_file'];
            $verification_background_file_tmp = $_FILES['age_verification']['tmp_name']['verification_background_file'];

            $formvalue['verification_background_image_name'] = $verification_background_file_name;
            $formvalue['verification_background_image_size'] = $verification_background_file_size;
            $formvalue['verification_background_image_tmp_name'] = $verification_background_file_tmp;

            $verification_background_file_extension = explode('.', $verification_background_file_name);
            $verification_background_file_name = $verification_background_file_extension[0] . '_' . time();
            $image_ext = Tools::strtolower(end($verification_background_file_extension));


            $verification_background_prev_img = isset($previous_saved_data['verification_background_file']) ? $previous_saved_data['verification_background_file'] : '';
            $formvalue['verification_background_file'] = $verification_background_file_name . '.' . $image_ext;
            $verification_background_image_new_name = $formvalue['verification_background_file'];
            $image_path = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'modules/' . $this->name . '/views/img/admin/uploads/';
            $formvalue['verification_background_image_path'] = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $verification_background_image_new_name;
            $display_image_path = $formvalue['verification_background_image_path'];
            $detectedType = mime_content_type($_FILES['age_verification']['tmp_name']['verification_background_file']);
            if (in_array($detectedType, $allowedTypes) === false) {
                $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                $error_count++;
            }
            if (in_array($image_ext, $allowed_extensions) === false) {
                $output .= $this->displayError($this->l('Please choose image in jpeg,jpg,gif or png file.'));
                $error_count++;
            }
            if ($verification_background_file_size >= $max_file_size_allowed) {
                $output .= $this->displayError($this->l('File size must be less than 4 MB.'));
                $error_count++;
            }
        }

        if ($error_count == 0) {
            if (!isset($formvalue['logo_image_path'])) {
                if (!isset($previous_saved_data['logo_image_path'])) {
                    $my_image = 'show.jpg';
                    $default_logo_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $my_image;
                    $formvalue['logo_image_path'] = $default_logo_image_path;
                } else {
                    $formvalue['logo_image_path'] = $previous_saved_data['logo_image_path'];
                }
            }

            if ($_FILES['age_verification']['name']['logo_file'] != null) {
                $logo_image_path = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/';
                if (isset($logo_prev_img) && $logo_prev_img != '') {
                    $mask = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/' . $logo_prev_img;
                    array_map('unlink', glob($mask));
                }
                move_uploaded_file($logo_file_tmp, $logo_image_path . $logo_image_new_name);
            }

            if (!isset($formvalue['verification_window_image_path'])) {
                if (!isset($previous_saved_data['verification_window_image_path'])) {
                    $my_image = 'show.jpg';
                    $default_verification_window_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $my_image;
                    $formvalue['verification_window_image_path'] = $default_verification_window_image_path;
                } else {
                    $formvalue['verification_window_image_path'] = $previous_saved_data['verification_window_image_path'];
                }
            }
            
            if ($_FILES['age_verification']['name']['verification_window_file'] != null) {
                $verification_window_image_path = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/';
                if (isset($verification_window_prev_img) && $verification_window_prev_img != '') {
                    $mask = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/' . $verification_window_prev_img;
                    array_map('unlink', glob($mask));
                }
                move_uploaded_file($verification_window_file_tmp, $verification_window_image_path . $verification_window_image_new_name);
            }

            if (!isset($formvalue['verification_background_image_path'])) {
                if (!isset($previous_saved_data['verification_background_image_path'])) {
                    $my_image = 'show.jpg';
                    $default_verification_background_image_path = $this->getModuleDirUrl() . 'ageverification/views/img/admin/uploads/' . $my_image;
                    $formvalue['verification_background_image_path'] = $default_verification_background_image_path;
                } else {
                    $formvalue['verification_background_image_path'] = $previous_saved_data['verification_background_image_path'];
                }
            }

            if ($_FILES['age_verification']['name']['verification_background_file'] != null) {
                $verification_background_image_path = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/';
                if (isset($verification_background_prev_img) && $verification_background_prev_img != '') {
                    $mask = _PS_MODULE_DIR_ . $this->name . '/views/img/admin/uploads/' . $verification_background_prev_img;
                    array_map('unlink', glob($mask));
                }
                move_uploaded_file($verification_background_file_tmp, $verification_background_image_path . $verification_background_image_new_name);
            }
            Configuration::updateValue('AGE_VERIFICATION_PREVIEW', serialize($formvalue));
            echo 'Success';
            die;
        }
    }

    private function ajaxProcess($method)
    {
        $this->json = array();
        if ($method == 'deleteLogoImage') {
            $res = $this->deleteLogoImage();
            $this->json = $res;
        }

        if ($method == 'deleteWindowImage') {
            $res = $this->deleteWindowImage();
            $this->json = $res;
        }

        if ($method == 'deleteBackgroundImage') {
            $res = $this->deleteBackgroundImage();
            $this->json = $res;
        }

        header('Content-Type: application/json', true);
        echo Tools::jsonEncode($this->json);
        die;
    }
    
    /*
     * Delete image from DB and uploads folder on clicking remove image
     */

    public function deleteLogoImage()
    {
        $age_verification_values = Tools::unSerialize(Configuration::get('AGE_VERIFICATION')); //Array from db
        if (isset($age_verification_values['logo_image_path'])) {
            unset($age_verification_values['logo_image_path']);
            unset($age_verification_values['logo_image_name']);
            unset($age_verification_values['logo_image_tmp_name']);
            unset($age_verification_values['logo_image_size']);
            //Update new array and Deleting file from server
            Configuration::updateValue('AGE_VERIFICATION', serialize($age_verification_values));
        } else {
            echo 'No Image Found';
        }
    }
    
    /*
     * Delete image from DB and uploads folder on clicking remove image
     */

    public function deleteWindowImage()
    {
        $age_verification_values = Tools::unSerialize(Configuration::get('AGE_VERIFICATION')); //Array from db
        if (isset($age_verification_values['verification_window_image_path'])) {
            unset($age_verification_values['verification_window_image_path']);
            unset($age_verification_values['verification_window_image_name']);
            unset($age_verification_values['verification_window_image_tmp_name']);
            unset($age_verification_values['verification_window_image_size']);
            //Update new array and Deleting file from server
            Configuration::updateValue('AGE_VERIFICATION', serialize($age_verification_values));
        } else {
            echo 'No Image Found';
        }
    }
    
    /*
     * Delete image from DB and uploads folder on clicking remove image
     */

    public function deleteBackgroundImage()
    {
        $age_verification_values = Tools::unSerialize(Configuration::get('AGE_VERIFICATION')); //Array from db
        if (isset($age_verification_values['verification_background_image_path'])) {
            unset($age_verification_values['verification_background_image_path']);
            unset($age_verification_values['verification_background_image_name']);
            unset($age_verification_values['verification_background_image_tmp_name']);
            unset($age_verification_values['verification_background_image_size']);
            //Update new array and Deleting file from server
            Configuration::updateValue('AGE_VERIFICATION', serialize($age_verification_values));
        } else {
            echo 'No Image Found';
        }
    }

    public function ajaxproductlist()
    {
        $query = Tools::getValue('q', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list,
         * they are no return values just because string:"(ref : #ref_pattern#)"
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        // Excluding downloadable products from packs because download from pack is not supported
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', false);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', false);

        $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = '
            . 'p.id_product AND pl.id_lang = '
            . '' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE p.active = 1 and (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . pSQL($excludeIds) . ') ' : ' ') .
            (pSQL($excludeVirtuals) ? 'AND p.id_product NOT IN (SELECT pd.id_product FROM '
                . '`' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
            (pSQL($exclude_packs) ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '');

        $items = Db::getInstance()->executeS($sql);
        if ($items) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['reference']) ?
                    ' (ref: ' . $item['reference'] . ')' : '') .
                '|' . (int) ($item['id_product']) . "\n";
            }
        }
    }
    
    public function getPath()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        } else {
            $custom_ssl_var = 0;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__;
        }
        return $module_dir;
    }

    private function getDefaultSettings()
    {
        $languages = Language::getLanguages(true);
        $under_age_messages = array();
        $pop_up_message = array();
        $pop_up_dob_message = array();
        $submit_button_text = array();
        $yes_button_text = array();
        $no_button_text = array();
        $additional_info_message = array();
        foreach ($languages as $lang) {
            $under_age_messages[$lang['id_lang']] = 'You are not old enough to view this content';
            $pop_up_message[$lang['id_lang']] = 'Hello there, Care to show us some ID?';
            $pop_up_dob_message[$lang['id_lang']] = 'Please, enter your year of birth:';
            $submit_button_text[$lang['id_lang']] = 'Enter';
            $yes_button_text[$lang['id_lang']] = 'Yes';
            $no_button_text[$lang['id_lang']] = 'No';
            $additional_info_message[$lang['id_lang']] = 'By entering this site you are agreeing to the Terms of Use and Privacy Policy.';
        }

        $settings = array(
            'enable' => 0,
            'choose_theme' => 1,
            'enable_default_images' => 1,
            'age' => 18,
            'verification_method' => 2,
            'dob_format' => 1,
            'remember_visitor' => 30,
            'under_age_action' => 1,
            'age_verification_under_age_message' => $under_age_messages,
            'underage_redirect_url' => 'http://www.google.com',
            'popup_display_method' => "complete",
            'product_name' => '',
            'excluded_products_hidden' => '',
            'prestashop_category' => array(),
            'kbageverification_private_pages' => array(),
            'enable_product_page' => 0,
            'age_verification_popup_message' => $pop_up_message,
            'age_verification_popup_dob_message' => $pop_up_dob_message,
            'age_verification_submit_button_text' => $submit_button_text,
            'age_verification_yes_button_text' => $yes_button_text,
            'age_verification_no_button_text' => $no_button_text,
            'age_verification_popup_additional_info_message' => $additional_info_message,
            'logo_file' => '',
            'verification_window_file' => '',
            'verification_background_file' => '',
            'logo_image_path' => '',
            'verification_window_image_path' => '',
            'verification_background_image_path' => '',
            'text_align' => 'center',
            'popup_shape' => 1,
            'popup_background_color' => '#ffffff',
            'popup_opacity' => 1,
            'popup_text_color' => '#232323',
            'popup_submit_button_color' => '#b00a0a',
            'popup_submit_button_text_color' => '#ffffff',
            'popup_yes_button_color' => '#dd9700',
            'popup_yes_button_text_color' => '#ffffff',
            'popup_no_button_color' => '#dd9700',
            'popup_no_button_text_color' => '#ffffff',
            'popup_message_font_size' => 30,
            'text_font_size' => 16,
            'additional_info_font_size' => 12,
            'custom_css' => '',
            'custom_js' => '',
        );
        return $settings;
    }
    
    public function rgb2hex2rgb($color)
    {
        if (!$color) {
            return false;
        }
        $color = trim($color);
        $result = [];
        if (preg_match("/^[0-9ABCDEFabcdef\#]+$/i", $color)) {
            $hex = str_replace('#', '', $color);
            if (!$hex) {
                return false;
            }
            if (Tools::strlen($hex) == 3) :
                $result['r'] = hexdec(Tools::substr($hex, 0, 1) . Tools::substr($hex, 0, 1));
                $result['g'] = hexdec(Tools::substr($hex, 1, 1) . Tools::substr($hex, 1, 1));
                $result['b'] = hexdec(Tools::substr($hex, 2, 1) . Tools::substr($hex, 2, 1));
            else :
                $result['r'] = hexdec(Tools::substr($hex, 0, 2));
                $result['g'] = hexdec(Tools::substr($hex, 2, 2));
                $result['b'] = hexdec(Tools::substr($hex, 4, 2));
            endif;
        } elseif (preg_match("/^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$/i", $color)) {
            $rgbstr = str_replace(array(',', ' ', '.'), ':', $color);
            $rgbarr = explode(":", $rgbstr);
            $result = '#';
            $result .= str_pad(dechex($rgbarr[0]), 2, "0", STR_PAD_LEFT);
            $result .= str_pad(dechex($rgbarr[1]), 2, "0", STR_PAD_LEFT);
            $result .= str_pad(dechex($rgbarr[2]), 2, "0", STR_PAD_LEFT);
            $result = Tools::strtoupper($result);
        } else {
            $result = false;
        }

        return $result;
    }
}
