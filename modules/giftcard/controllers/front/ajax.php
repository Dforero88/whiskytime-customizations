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
header('Content-type: text/javascript');
class GiftCardAjaxModuleFrontController extends ModuleFrontController
{
    public $ajax = false;

    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        $this->ajax = Tools::getValue('ajax', 0);
    }

    public function displayAjaxProductExists()
    {
        $id_product = json_decode(Tools::getValue('id_product'));
        if (Gift::isExists($id_product)) {
            $html = $id_product;
        } else {
            $html = 0;
        }
        echo $html;
        exit;
    }

    public function displayAjaxGetGiftPrice()
    {
        header('Content-Type: application/json');
        if (true == Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->ajaxDie(json_encode([
                'gift_prices' => $this->module->hookdisplayProductButtons(),
            ]));
        }
    }

    public function displayAjaxGetGiftType()
    {
        $gift_type = '';
        if (true == Tools::version_compare(_PS_VERSION_, '1.7', '>=')) {
            $id_product = (int) Tools::getValue('id_product');
            if ($id_product) {
                $gift_type = Gift::getCardValue($id_product) ? Gift::getCardValue($id_product)['value_type'] : '';
            }
        }
        $this->ajaxDie(json_encode(['gift_type' => $gift_type]));
    }

    public function displayAjaxSaveImage()
    {
        $filename = Tools::getValue('filename');
        if($filename){
            $source_path = _PS_IMG_DIR_ . 'giftcard_templates/' . $filename;
            if (!file_exists($source_path)) {
                exit(json_encode(['status' => 'error', 'message' => 'No Such File In Repository']));
            }

            $input_price = Tools::getValue('card_value');

            if (!$filename) {
                exit(json_encode(['status' => 'error', 'message' => 'Filename missing']));
            }

            $id_lang = (int) Context::getContext()->language->id;
            $id_shop = (int) Context::getContext()->shop->id;

            $data = GiftCardImageTemplateModel::getTemplateByTemplateName((string) $filename);

            if (!$data) {
                exit(json_encode(['status' => 'error', 'message' => 'No template found for this filename']));
            }

            $image = imagecreatefrompng($source_path);
            if (!$image) {
                exit(json_encode(['status' => 'error', 'message' => 'Failed to load image']));
            }

            // Get image dimensions
            $image_width = imagesx($image);
            $image_height = imagesy($image);

            // Apply background color overlay (semi-transparent)
            $bg_rgb = sscanf($data['bg_color'], '#%02x%02x%02x');
            $bg_color = imagecolorallocatealpha($image, $bg_rgb[0], $bg_rgb[1], $bg_rgb[2], 100);
            imagefilledrectangle($image, 0, 0, $image_width, $image_height, $bg_color);

            // --- Text overlay settings ---
            $text_color = imagecolorallocate($image, 252, 142, 172);
            $font_size = 50; 
            $font_file = _PS_MODULE_DIR_ . 'giftcard/views/css/fonts/LiberationSerif-BoldItalic.ttf';
            $font_width = imagefontwidth($font_size);
            $font_height = imagefontheight($font_size);

            // Get your input values
            // $input_price = $input_price;
            $discount_code = $data['discount_code'];
            $template_text = $data['template_text'];

            // --- Coordinates based on image size and text length ---
            $price_x = $image_width - $font_width * strlen("$input_price") - 10;
            $price_y = 10;

            $template_text_x = 10;
            $template_text_y = $image_height - $font_height - 10;

            $discount_x = $image_width - $font_width * strlen("$discount_code") - 10;
            $discount_y = $image_height - $font_height - 10;

            // Draw text
            // -- Draw PRICE (single line)
            // imagettftext(
            //     $image,
            //     $font_size,
            //     0,
            //     $price_x,
            //     $price_y + $font_size, // Y offset for baseline
            //     $text_color,
            //     $font_file,
            //     "$input_price"
            // );

            // function wrapText($text, $maxLength = 12) {
            //     return str_split($text, $maxLength);
            // }
            // $wrapped_template_lines = wrapText($template_text);
            // $template_line_height = $font_size + 10; // spacing between lines
            // $template_text_y = $image_height - ($font_size * count($wrapped_template_lines)) - 50; // vertical start position

            // foreach ($wrapped_template_lines as $i => $line) {
            //     imagettftext(
            //         $image,
            //         $font_size,
            //         0,
            //         $template_text_x,
            //         $template_text_y + ($i * $template_line_height),
            //         $text_color,
            //         $font_file,
            //         $line
            //     );
            // }
            // $wrapped_discount_lines = wrapText($discount_code);
            // $discount_line_height = $font_size + 10;
            // $discount_y_start = $image_height - ($font_size * count($wrapped_discount_lines)) - 10;

            // foreach ($wrapped_discount_lines as $i => $line) {
            //     $line_width = imagettfbbox($font_size, 0, $font_file, $line)[2];
            //     $discount_x = $image_width - $line_width - 10;

            //     imagettftext(
            //         $image,
            //         $font_size,
            //         0,
            //         $discount_x,
            //         $discount_y_start + ($i * $discount_line_height),
            //         $text_color,
            //         $font_file,
            //         $line
            //     );
            // }


            // --- Wrap helper ---
            function wrapText($text, $maxLength = 14) {
                return str_split($text, $maxLength);
            }

            $line_spacing = 10; // space between lines
            $line_height = $font_size + $line_spacing;

            // (Right-aligned, multiple lines if needed) ---
            $wrapped_price_lines = wrapText((string)$input_price);
            $price_y_start = 10;
            foreach ($wrapped_price_lines as $i => $line) {
                $line_width = imagettfbbox($font_size, 0, $font_file, $line)[2];
                $price_x = $image_width - $line_width - 10;
                $y = $price_y_start + ($i * $line_height);
                imagettftext($image, $font_size, 0, $price_x, $y + $font_size, $text_color, $font_file, $line);
            }

            $wrapped_template_lines = wrapText($template_text);
            $template_text_x = 10;
            $template_text_y = $image_height - ($line_height * count($wrapped_template_lines)) - 60;

            foreach ($wrapped_template_lines as $i => $line) {
                $y = $template_text_y + ($i * $line_height);
                imagettftext($image, $font_size, 0, $template_text_x, $y + $font_size, $text_color, $font_file, $line);
            }

            $wrapped_discount_lines = wrapText($discount_code);
            $discount_y_start = $image_height - ($line_height * count($wrapped_discount_lines)) - 10;

            foreach ($wrapped_discount_lines as $i => $line) {
                $line_width = imagettfbbox($font_size, 0, $font_file, $line)[2];
                $discount_x = $image_width - $line_width - 10;
                $y = $discount_y_start + ($i * $line_height);
                imagettftext($image, $font_size, 0, $discount_x, $y + $font_size, $text_color, $font_file, $line);
            }


            $temp_folder = _PS_IMG_DIR_ . 'giftcard_templates/temp_img/';
            if (!file_exists($temp_folder)) {
                mkdir($temp_folder, 0777, true);
            }

            $unique_name = 'generated_' . uniqid() . '.png';
            $file_path = $temp_folder . $unique_name;
            imagepng($image, $file_path);
            imagedestroy($image);

            $image_url = _PS_BASE_URL_ . __PS_BASE_URI__ . 'img/giftcard_templates/temp_img/' . $unique_name;

            exit(json_encode([
                'status' => 'success',
                'path' => $image_url,
                'input' => '<input type="hidden" name="generated_giftcard_image_path" value="' . $image_url . '" />',
            ]));
        }
    }
}

