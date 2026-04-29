<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class PopupImage extends Module
{
    const CONFIG_FILE = 'POPUPIMAGE_FILE';
    const CONFIG_TEXT = 'POPUPIMAGE_TEXT';
    const MAX_UPLOAD_SIZE = 5242880;

    public function __construct()
    {
        $this->name = 'popupimage';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'David Custom';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Popup Image');
        $this->description = $this->l('Affiche un popup avec une image lors de chaque visite.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayFooter')
            && Configuration::updateValue(self::CONFIG_FILE, '')
            && Configuration::updateValue(self::CONFIG_TEXT, [], true);
    }

    public function uninstall()
    {
        $this->deleteStoredImage();

        return parent::uninstall()
            && Configuration::deleteByName(self::CONFIG_FILE)
            && Configuration::deleteByName(self::CONFIG_TEXT);
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitPopupImage')) {
            if (!$this->isValidAdminToken()) {
                $output .= $this->displayError($this->l('Jeton de securite invalide. Rechargez la page et reessayez.'));
            } else {
                $texts = $this->getSubmittedTexts();
                Configuration::updateValue(self::CONFIG_TEXT, $texts, true);

                $hasUpload = isset($_FILES['POPUPIMAGE_FILE']) && !empty($_FILES['POPUPIMAGE_FILE']['tmp_name']);
                if (!$hasUpload) {
                    $output .= $this->displayConfirmation($this->l('Contenu mis a jour avec succes.'));

                    return $output.$this->renderForm();
                }

                $uploadError = $this->validateImageUpload($_FILES['POPUPIMAGE_FILE']);

                if ($uploadError) {
                    $output .= $this->displayError($uploadError);
                } else {
                    $uploadResult = $this->storeUploadedImage($_FILES['POPUPIMAGE_FILE']);

                    if (!empty($uploadResult['error'])) {
                        $output .= $this->displayError($uploadResult['error']);
                    } else {
                        $currentFile = Configuration::get(self::CONFIG_FILE);
                        Configuration::updateValue(self::CONFIG_FILE, $uploadResult['file']);
                        $this->deleteStoredImage($currentFile, $uploadResult['file']);
                        $output .= $this->displayConfirmation($this->l('Image et contenu mis a jour avec succes.'));
                    }
                }
            }
        }

        return $output.$this->renderForm();
    }

    protected function renderForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $languages = [];
        foreach (Language::getLanguages(true) as $lang) {
            $languages[] = [
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => (int) ($defaultLang === (int) $lang['id_lang']),
            ];
        }

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = $defaultLang;
        $helper->languages = $languages;
        $helper->title = $this->displayName;
        $helper->submit_action = 'submitPopupImage';
        $helper->fields_value = $this->getFormValues();

        $current = Configuration::get(self::CONFIG_FILE);
        $currentPreview = '';
        $current = Configuration::get(self::CONFIG_FILE);
        if ($current) {
            $currentPreview = sprintf(
                '<p>%s</p><img src="%s" style="max-width:300px;">',
                $this->l('Image actuelle :'),
                htmlspecialchars($this->getUploadUrl($current), ENT_QUOTES, 'UTF-8')
            );
        }

        $fieldsForm = [[
            'form' => [
                'tinymce' => true,
                'legend' => [
                    'title' => $this->l('Configuration du popup'),
                    'icon' => 'icon-picture',
                ],
                'input' => [
                    [
                        'type' => 'hidden',
                        'name' => 'popupimage_token',
                    ],
                    [
                        'type' => 'file',
                        'label' => $this->l('Image du popup'),
                        'name' => 'POPUPIMAGE_FILE',
                        'desc' => $this->l('Formats autorises : JPG, PNG, GIF, WEBP. Taille max : 5 MB.'),
                    ],
                    [
                        'type' => 'html',
                        'name' => 'popupimage_preview',
                        'html_content' => $currentPreview,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Texte au-dessus de l\'image'),
                        'name' => 'POPUPIMAGE_TEXT',
                        'lang' => true,
                        'autoload_rte' => true,
                        'class' => 'rte autoload_rte',
                        'cols' => 60,
                        'rows' => 10,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                    'class' => 'btn btn-primary pull-right',
                ],
            ],
        ]];

        return $helper->generateForm($fieldsForm);
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->registerStylesheet(
            'popupimage-style',
            'modules/'.$this->name.'/views/css/popupimage.css',
            ['media' => 'all', 'priority' => 150]
        );

        $this->context->controller->registerJavascript(
            'popupimage-script',
            'modules/'.$this->name.'/views/js/popupimage.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }

    public function hookDisplayFooter()
    {
        $img = Configuration::get(self::CONFIG_FILE);
        $popupText = Configuration::get(self::CONFIG_TEXT, $this->context->language->id);
        $this->context->smarty->assign([
            'popupimage_file' => $img ? $this->getUploadUrl($img) : '',
            'popupimage_text' => $popupText,
        ]);

        return $this->display(__FILE__, 'views/templates/hook/displayPopup.tpl');
    }

    protected function getFormValues()
    {
        return [
            'popupimage_token' => Tools::getAdminTokenLite('AdminModules'),
            'POPUPIMAGE_TEXT' => $this->getStoredTexts(),
        ];
    }

    protected function getStoredTexts()
    {
        $values = [];
        foreach (Language::getLanguages(true) as $lang) {
            $values[(int) $lang['id_lang']] = (string) Configuration::get(self::CONFIG_TEXT, (int) $lang['id_lang']);
        }

        return $values;
    }

    protected function getSubmittedTexts()
    {
        $values = [];

        foreach (Language::getLanguages(true) as $lang) {
            $idLang = (int) $lang['id_lang'];
            $value = (string) Tools::getValue(self::CONFIG_TEXT.'_'.$idLang, '');
            $values[$idLang] = Tools::purifyHTML($value);
        }

        return $values;
    }

    protected function isValidAdminToken()
    {
        return Tools::getValue('popupimage_token') === Tools::getAdminTokenLite('AdminModules');
    }

    protected function validateImageUpload(array $file)
    {
        if (!empty($file['error'])) {
            return $this->l('Erreur lors du telechargement du fichier.');
        }

        if (empty($file['size']) || (int) $file['size'] > self::MAX_UPLOAD_SIZE) {
            return $this->l('Le fichier depasse la taille maximale autorisee de 5 MB.');
        }

        $extension = Tools::strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedExtensions, true)) {
            return $this->l('Format de fichier non autorise.');
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return $this->l('Le fichier envoye n\'est pas une image valide.');
        }

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
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
        $targetFile = sprintf('popupimage_%s.%s', uniqid('', true), $extension);
        $targetPath = $uploadDir.$targetFile;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['error' => $this->l('Erreur lors du telechargement de l\'image.')];
        }

        @chmod($targetPath, 0644);

        return ['file' => $targetFile];
    }

    protected function deleteStoredImage($fileName = null, $exclude = '')
    {
        $fileName = $fileName ?: Configuration::get(self::CONFIG_FILE);
        if (!$fileName || $fileName === $exclude) {
            return;
        }

        $path = $this->getUploadDir().$fileName;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    protected function getUploadDir()
    {
        return _PS_MODULE_DIR_.$this->name.'/uploads/';
    }

    protected function getUploadUrl($fileName)
    {
        return _MODULE_DIR_.$this->name.'/uploads/'.$fileName;
    }
}
