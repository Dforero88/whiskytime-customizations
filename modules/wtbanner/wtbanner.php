<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class WtBanner extends Module implements WidgetInterface
{
    const CFG_IMAGE = 'WTBANNER_IMAGE';
    const CFG_CROPPED_IMAGE = 'WTBANNER_CROPPED_IMAGE';
    const CFG_ALT = 'WTBANNER_ALT';
    const CFG_CROP_X = 'WTBANNER_CROP_X';
    const CFG_CROP_Y = 'WTBANNER_CROP_Y';
    const CFG_CROP_W = 'WTBANNER_CROP_W';
    const CFG_CROP_H = 'WTBANNER_CROP_H';
    const MAX_UPLOAD_SIZE = 5242880;
    const CROP_RATIO = 4.0;

    protected $templateFile;

    public function __construct()
    {
        $this->name = 'wtbanner';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'OpenAI';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Whisky Time Banner');
        $this->description = $this->l('Affiche une bannière hero pleine largeur sur la home a partir d\'une seule image.');
        $this->templateFile = 'module:' . $this->name . '/views/templates/hook/wtbanner.tpl';
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayTopColumn')
            && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        $this->deleteStoredImage();

        return $this->deleteConfiguration()
            && parent::uninstall();
    }

    public function getContent()
    {
        $this->registerAdminAssets();

        if (Tools::isSubmit('submitWtBanner')) {
            $errors = $this->saveConfiguration();
            if (!empty($errors)) {
                return $this->displayError(implode('<br>', $errors)) . $this->renderConfigurationPage();
            }

            return $this->displayConfirmation($this->l('Configuration mise à jour.')) . $this->renderConfigurationPage();
        }

        return $this->renderConfigurationPage();
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->registerStylesheet(
            'module-wtbanner',
            'modules/' . $this->name . '/views/css/wtbanner.css',
            [
                'media' => 'all',
                'priority' => 140,
                'version' => '20260602-01',
            ]
        );
    }

    public function hookDisplayTopColumn($params)
    {
        return $this->renderWidget('displayTopColumn', $params);
    }

    public function hookDisplayHome($params)
    {
        return $this->renderWidget('displayHome', $params);
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (empty($configuration['from_theme_widget'])) {
            return '';
        }

        $variables = $this->getWidgetVariables($hookName, $configuration);
        if (empty($variables)) {
            return '';
        }

        $this->smarty->assign($variables);

        return $this->fetch($this->templateFile);
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $fileName = (string) Configuration::get(self::CFG_IMAGE);
        if (!$fileName || !is_file($this->getUploadDir() . $fileName)) {
            return [];
        }

        return [
            'wtbanner' => [
                'image_url' => $this->getRenderImageUrl($fileName),
                'alt' => $this->getTranslatedAlt(),
            ],
        ];
    }

    protected function renderConfigurationPage()
    {
        return $this->renderPreview() . $this->renderForm();
    }

    protected function renderPreview()
    {
        $fileName = (string) Configuration::get(self::CFG_IMAGE);
        if (!$fileName || !is_file($this->getUploadDir() . $fileName)) {
            return '<div class="alert alert-info">' . $this->l('Aucune image de banniere n\'est configuree pour le moment.') . '</div>';
        }

        $imageUrl = $this->getUploadUrl($fileName);
        $crop = $this->getCropValues();

        return '
            <div class="panel wtbanner-admin">
                <h3><i class="icon-picture"></i> ' . $this->l('Apercu de l\'image actuelle') . '</h3>
                <p class="wtbanner-admin__help">' . $this->l('Cliquez ou faites glisser dans l\'apercu pour choisir visuellement la zone qui restera visible sur la home.') . '</p>
                <div class="wtbanner-admin__viewport" data-wtbanner-cropper data-crop-x="' . $crop['x'] . '" data-crop-y="' . $crop['y'] . '" data-crop-w="' . $crop['w'] . '" data-crop-h="' . $crop['h'] . '">
                    <img src="' . htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') . '" alt="" data-wtbanner-preview-image>
                    <span class="wtbanner-admin__crop-box" data-wtbanner-crop-box>
                        <span class="wtbanner-admin__handle" data-wtbanner-handle></span>
                    </span>
                </div>
            </div>
        ';
    }

    protected function renderForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Bannière home'),
                    'icon' => 'icon-picture',
                ],
                'description' => $this->l('Une seule image suffit. Le module gere le rendu responsive et le recadrage visuel automatiquement.'),
                'input' => [
                    [
                        'type' => 'file',
                        'label' => $this->l('Image banniere'),
                        'name' => 'WTBANNER_IMAGE_UPLOAD',
                        'desc' => $this->l('Formats autorises : jpg, jpeg, png, webp. Taille maximale : 5 MB.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Supprimer l\'image actuelle'),
                        'name' => 'WTBANNER_REMOVE_IMAGE',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'wtbanner_remove_image_on',
                                'value' => 1,
                                'label' => $this->l('Oui'),
                            ],
                            [
                                'id' => 'wtbanner_remove_image_off',
                                'value' => 0,
                                'label' => $this->l('Non'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Texte alternatif'),
                        'name' => self::CFG_ALT,
                        'lang' => true,
                    ],
                    [
                        'type' => 'hidden',
                        'name' => self::CFG_CROP_X,
                    ],
                    [
                        'type' => 'hidden',
                        'name' => self::CFG_CROP_Y,
                    ],
                    [
                        'type' => 'hidden',
                        'name' => self::CFG_CROP_W,
                    ],
                    [
                        'type' => 'hidden',
                        'name' => self::CFG_CROP_H,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                    'name' => 'submitWtBanner',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->submit_action = 'submitWtBanner';
        $helper->show_cancel_button = false;
        $helper->fields_value = $this->getFormValues();
        $helper->languages = $this->getHelperLanguages($defaultLang);
        $helper->id_language = (int) $this->context->language->id;

        return $helper->generateForm([$fieldsForm]);
    }

    protected function getFormValues()
    {
        $values = [];
        $defaultAlt = $this->getDefaultAlt();

        foreach (Language::getLanguages(false) as $language) {
            $idLang = (int) $language['id_lang'];
            $values[self::CFG_ALT][$idLang] = Configuration::get(self::CFG_ALT, $idLang) ?: $defaultAlt;
        }

        $crop = $this->getCropValues();
        $values[self::CFG_CROP_X] = (string) $crop['x'];
        $values[self::CFG_CROP_Y] = (string) $crop['y'];
        $values[self::CFG_CROP_W] = (string) $crop['w'];
        $values[self::CFG_CROP_H] = (string) $crop['h'];
        $values['WTBANNER_REMOVE_IMAGE'] = 0;

        return $values;
    }

    protected function saveConfiguration()
    {
        $errors = [];
        $file = isset($_FILES['WTBANNER_IMAGE_UPLOAD']) ? $_FILES['WTBANNER_IMAGE_UPLOAD'] : null;
        $removeImage = (bool) Tools::getValue('WTBANNER_REMOVE_IMAGE');
        $currentFile = (string) Configuration::get(self::CFG_IMAGE);

        if ($file && !empty($file['tmp_name'])) {
            $error = $this->validateImageUpload($file);
            if ($error !== '') {
                $errors[] = $error;
            } else {
                $stored = $this->storeUploadedImage($file);
                if (!empty($stored['error'])) {
                    $errors[] = $stored['error'];
                } else {
                    Configuration::updateValue(self::CFG_IMAGE, $stored['file']);
                    $this->deleteStoredImage($currentFile, $stored['file']);
                    $this->deleteStoredCroppedImage();
                    $currentFile = $stored['file'];
                    $this->setDefaultCropFromImage($currentFile);
                }
            }
        } elseif ($removeImage) {
            $this->deleteStoredImage($currentFile);
            $this->deleteStoredCroppedImage();
            Configuration::deleteByName(self::CFG_IMAGE);
            $currentFile = '';
        }

        $translations = [];
        $defaultAlt = $this->getDefaultAlt();
        foreach (Language::getLanguages(false) as $language) {
            $idLang = (int) $language['id_lang'];
            $value = trim((string) Tools::getValue(self::CFG_ALT . '_' . $idLang, ''));
            $translations[$idLang] = $value !== '' ? $value : $defaultAlt;
        }
        Configuration::updateValue(self::CFG_ALT, $translations, true);

        if ($currentFile) {
            $this->saveCropConfiguration();
            $cropError = $this->regenerateCroppedImage($currentFile);
            if ($cropError !== '') {
                $errors[] = $cropError;
            }
        }

        if (empty($errors) && !$currentFile) {
            $errors[] = $this->l('Veuillez televerser une image pour activer la banniere.');
        }

        return $errors;
    }

    protected function validateImageUpload(array $file)
    {
        if (!empty($file['error'])) {
            return $this->l('Erreur lors du televersement du fichier.');
        }

        if (empty($file['size']) || (int) $file['size'] > self::MAX_UPLOAD_SIZE) {
            return $this->l('Le fichier depasse la taille maximale autorisee de 5 MB.');
        }

        $extension = Tools::strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($extension, $allowedExtensions, true)) {
            return $this->l('Format de fichier non autorise.');
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return $this->l('Le fichier envoye n\'est pas une image valide.');
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($imageInfo['mime'], $allowedMimeTypes, true)) {
            return $this->l('Type MIME non autorise.');
        }

        return '';
    }

    protected function storeUploadedImage(array $file)
    {
        $uploadDir = $this->getUploadDir();
        if (!is_dir($uploadDir) && !@mkdir($uploadDir, 0755, true)) {
            return ['error' => $this->l('Impossible de preparer le dossier d\'upload.')];
        }

        $extension = Tools::strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));
        $targetFile = sprintf('wtbanner_%s.%s', sha1(uniqid('', true) . '-' . $file['name']), $extension);
        $targetPath = $uploadDir . $targetFile;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['error' => $this->l('Erreur lors de l\'enregistrement de l\'image.')];
        }

        @chmod($targetPath, 0644);

        return ['file' => $targetFile];
    }

    protected function deleteStoredImage($fileName = null, $exclude = '')
    {
        $fileName = $fileName ?: (string) Configuration::get(self::CFG_IMAGE);
        if (!$fileName || $fileName === $exclude) {
            return;
        }

        $path = $this->getUploadDir() . $fileName;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    protected function deleteConfiguration()
    {
        return Configuration::deleteByName(self::CFG_IMAGE)
            && Configuration::deleteByName(self::CFG_CROPPED_IMAGE)
            && Configuration::deleteByName(self::CFG_ALT)
            && Configuration::deleteByName(self::CFG_CROP_X)
            && Configuration::deleteByName(self::CFG_CROP_Y)
            && Configuration::deleteByName(self::CFG_CROP_W)
            && Configuration::deleteByName(self::CFG_CROP_H);
    }

    protected function getUploadDir()
    {
        return _PS_MODULE_DIR_ . $this->name . '/uploads/';
    }

    protected function getUploadUrl($fileName)
    {
        return _MODULE_DIR_ . $this->name . '/uploads/' . $fileName;
    }

    protected function getTranslatedAlt()
    {
        $alt = (string) Configuration::get(self::CFG_ALT, (int) $this->context->language->id);

        return $alt !== '' ? $alt : $this->getDefaultAlt();
    }

    protected function saveCropConfiguration()
    {
        $crop = [
            'x' => $this->sanitizePercentage(Tools::getValue(self::CFG_CROP_X, 0)),
            'y' => $this->sanitizePercentage(Tools::getValue(self::CFG_CROP_Y, 0)),
            'w' => $this->sanitizePercentage(Tools::getValue(self::CFG_CROP_W, 100)),
            'h' => $this->sanitizePercentage(Tools::getValue(self::CFG_CROP_H, 100)),
        ];

        if ($crop['w'] <= 0) {
            $crop['w'] = 100;
        }

        if ($crop['h'] <= 0) {
            $crop['h'] = 100;
        }

        if ($crop['x'] + $crop['w'] > 100) {
            $crop['x'] = max(0, 100 - $crop['w']);
        }

        if ($crop['y'] + $crop['h'] > 100) {
            $crop['y'] = max(0, 100 - $crop['h']);
        }

        Configuration::updateValue(self::CFG_CROP_X, $crop['x']);
        Configuration::updateValue(self::CFG_CROP_Y, $crop['y']);
        Configuration::updateValue(self::CFG_CROP_W, $crop['w']);
        Configuration::updateValue(self::CFG_CROP_H, $crop['h']);
    }

    protected function getCropValues()
    {
        return [
            'x' => $this->sanitizePercentage(Configuration::get(self::CFG_CROP_X, null, null, null, 0)),
            'y' => $this->sanitizePercentage(Configuration::get(self::CFG_CROP_Y, null, null, null, 0)),
            'w' => $this->sanitizePercentage(Configuration::get(self::CFG_CROP_W, null, null, null, 100)),
            'h' => $this->sanitizePercentage(Configuration::get(self::CFG_CROP_H, null, null, null, 100)),
        ];
    }

    protected function sanitizePercentage($value)
    {
        $value = (float) $value;

        if ($value < 0) {
            return 0;
        }

        if ($value > 100) {
            return 100;
        }

        return round($value, 4);
    }

    protected function setDefaultCropFromImage($fileName)
    {
        $path = $this->getUploadDir() . $fileName;
        $size = @getimagesize($path);
        if (!$size || empty($size[0]) || empty($size[1])) {
            return;
        }

        $width = (float) $size[0];
        $height = (float) $size[1];
        $ratio = $width / $height;
        $targetRatio = self::CROP_RATIO;

        if ($ratio > $targetRatio) {
            $cropHeight = 100.0;
            $cropWidth = ($targetRatio / $ratio) * 100.0;
            $cropX = (100.0 - $cropWidth) / 2.0;
            $cropY = 0.0;
        } else {
            $cropWidth = 100.0;
            $cropHeight = ($ratio / $targetRatio) * 100.0;
            $cropX = 0.0;
            $cropY = (100.0 - $cropHeight) / 2.0;
        }

        Configuration::updateValue(self::CFG_CROP_X, round($cropX, 4));
        Configuration::updateValue(self::CFG_CROP_Y, round($cropY, 4));
        Configuration::updateValue(self::CFG_CROP_W, round($cropWidth, 4));
        Configuration::updateValue(self::CFG_CROP_H, round($cropHeight, 4));
    }

    protected function regenerateCroppedImage($fileName)
    {
        $sourcePath = $this->getUploadDir() . $fileName;
        if (!is_file($sourcePath)) {
            return $this->l('Image source introuvable pour generer le recadrage.');
        }

        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            return $this->l('Impossible de lire l\'image source.');
        }

        $resource = $this->createImageResource($sourcePath, $imageInfo['mime']);
        if (!$resource) {
            return $this->l('Le format de l\'image source n\'est pas supporte pour le recadrage.');
        }

        $crop = $this->getCropValues();
        $sourceWidth = (int) $imageInfo[0];
        $sourceHeight = (int) $imageInfo[1];
        $cropX = (int) round(($crop['x'] / 100) * $sourceWidth);
        $cropY = (int) round(($crop['y'] / 100) * $sourceHeight);
        $cropWidth = max(1, (int) round(($crop['w'] / 100) * $sourceWidth));
        $cropHeight = max(1, (int) round(($crop['h'] / 100) * $sourceHeight));

        if ($cropX + $cropWidth > $sourceWidth) {
            $cropWidth = $sourceWidth - $cropX;
        }

        if ($cropY + $cropHeight > $sourceHeight) {
            $cropHeight = $sourceHeight - $cropY;
        }

        $cropped = imagecrop($resource, [
            'x' => $cropX,
            'y' => $cropY,
            'width' => $cropWidth,
            'height' => $cropHeight,
        ]);

        imagedestroy($resource);

        if (!$cropped) {
            return $this->l('Impossible de generer l\'image recadree.');
        }

        $croppedFile = sprintf('wtbanner_crop_%s.jpg', sha1($fileName . '|' . json_encode($crop)));
        $croppedPath = $this->getUploadDir() . $croppedFile;
        if (!imagejpeg($cropped, $croppedPath, 90)) {
            imagedestroy($cropped);

            return $this->l('Impossible d\'enregistrer l\'image recadree.');
        }

        imagedestroy($cropped);
        @chmod($croppedPath, 0644);

        $previousFile = (string) Configuration::get(self::CFG_CROPPED_IMAGE);
        if ($previousFile && $previousFile !== $croppedFile) {
            $previousPath = $this->getUploadDir() . $previousFile;
            if (is_file($previousPath)) {
                @unlink($previousPath);
            }
        }

        Configuration::updateValue(self::CFG_CROPPED_IMAGE, $croppedFile);

        return '';
    }

    protected function createImageResource($path, $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
                return @imagecreatefromjpeg($path);

            case 'image/png':
                return @imagecreatefrompng($path);

            case 'image/webp':
                if (function_exists('imagecreatefromwebp')) {
                    return @imagecreatefromwebp($path);
                }

                return false;

            default:
                return false;
        }
    }

    protected function getRenderImageUrl($fallbackFileName)
    {
        $croppedFile = (string) Configuration::get(self::CFG_CROPPED_IMAGE);
        if ($croppedFile && is_file($this->getUploadDir() . $croppedFile)) {
            return $this->getUploadUrl($croppedFile);
        }

        return $this->getUploadUrl($fallbackFileName);
    }

    protected function deleteStoredCroppedImage()
    {
        $fileName = (string) Configuration::get(self::CFG_CROPPED_IMAGE);
        if (!$fileName) {
            return;
        }

        $path = $this->getUploadDir() . $fileName;
        if (is_file($path)) {
            @unlink($path);
        }

        Configuration::deleteByName(self::CFG_CROPPED_IMAGE);
    }

    protected function getDefaultAlt()
    {
        return $this->l('Banniere Whisky Time');
    }

    protected function getHelperLanguages($defaultLang)
    {
        $languages = Language::getLanguages(false);

        foreach ($languages as &$language) {
            $language['is_default'] = ((int) $language['id_lang'] === (int) $defaultLang) ? 1 : 0;
        }

        return $languages;
    }

    protected function registerAdminAssets()
    {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');
    }
}
