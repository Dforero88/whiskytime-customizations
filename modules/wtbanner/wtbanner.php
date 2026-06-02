<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class WtBanner extends Module
{
    const CFG_IMAGE = 'WTBANNER_IMAGE';
    const CFG_ALT = 'WTBANNER_ALT';
    const MAX_UPLOAD_SIZE = 5242880;

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
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHome')
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

    public function hookDisplayHome($params)
    {
        $fileName = (string) Configuration::get(self::CFG_IMAGE);
        if (!$fileName || !is_file($this->getUploadDir() . $fileName)) {
            return '';
        }

        $this->context->smarty->assign([
            'wtbanner' => [
                'image_url' => $this->getUploadUrl($fileName),
                'alt' => $this->getTranslatedAlt(),
            ],
        ]);

        return $this->display(__FILE__, 'views/templates/hook/wtbanner.tpl');
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

        return '
            <div class="panel">
                <h3><i class="icon-picture"></i> ' . $this->l('Apercu de l\'image actuelle') . '</h3>
                <img src="' . htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') . '" alt="" style="display:block;max-width:100%;height:auto;border-radius:12px;">
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
                    $currentFile = $stored['file'];
                }
            }
        } elseif ($removeImage) {
            $this->deleteStoredImage($currentFile);
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
            && Configuration::deleteByName(self::CFG_ALT);
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
}
