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
 */

class AgeverificationPreviewModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        parent::init();
    }
    
    public function initContent()
    {
        parent::initContent();
        $this->generatePreviewAgeVerificationPopup();
    }
    
    /*
     * Function for generating the Preview of Age Verification Popup.
     */
    public function generatePreviewAgeVerificationPopup()
    {
        
        $values = Tools::unSerialize(Configuration::get('AGE_VERIFICATION_PREVIEW'));
        $show = 1;
        
        if ($show == 1) {
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
                    'kbfront_url' => $this->getRootUrl(),
                    'kblogo_image_path' => $kblogo_image_path,
                    'kbverification_window_image_path' => $kbverification_window_image_path,
                    'kbverification_background_image_path' => $kbverification_background_image_path,
                )
            );
        }

        $this->setTemplate('module:ageverification/views/templates/front/preview_detail.tpl');
    }
    
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_PS_MODULE_DIR_ . 'ageverification/views/css/front/popup.css');
        $this->addJS(_PS_MODULE_DIR_  . 'ageverification/views/js/front/popup_preview.js');
    }
            
    public function rgb2hex2rgb($color)
    {
        if (!$color) {
            return false;
        }
        $color = trim($color);
        $result = false;
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
    
    protected function getRootUrl()
    {
        $root_url = '';
        if ($this->checkSecureUrl()) {
            $root_url = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
        } else {
            $root_url = _PS_BASE_URL_ . __PS_BASE_URI__;
        }
        return $root_url;
    }
    
    /* Function for checking SSL  */
    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
}
