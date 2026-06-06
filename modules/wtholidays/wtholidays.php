<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Wtholidays extends Module
{
    const CFG_ENABLED = 'WTHOLIDAYS_ENABLED';
    const CFG_DATE_START = 'WTHOLIDAYS_DATE_START';
    const CFG_DATE_END = 'WTHOLIDAYS_DATE_END';
    const CFG_HEADLINE = 'WTHOLIDAYS_HEADLINE';
    const CFG_MESSAGE = 'WTHOLIDAYS_MESSAGE';

    public function __construct()
    {
        $this->name = 'wtholidays';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'OpenAI';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Whisky Time Holidays');
        $this->description = $this->l('Displays a holidays notice on home, product and cart pages.');
        $this->ps_versions_compliancy = ['min' => '1.7.7.0', 'max' => _PS_VERSION_];
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayHeader')
            && $this->registerHook('displayHome')
            && $this->registerHook('displayShoppingCart')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->setDefaultConfiguration();
    }

    public function uninstall()
    {
        return $this->deleteConfiguration()
            && parent::uninstall();
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitWtHolidays')) {
            $errors = $this->saveConfiguration();

            if (!empty($errors)) {
                return $this->displayError(implode('<br>', $errors)) . $this->renderForm();
            }

            return $this->displayConfirmation($this->l('Configuration mise à jour.')) . $this->renderForm();
        }

        return $this->renderForm();
    }

    public function hookDisplayHeader($params)
    {
        if (!$this->isNoticeEnabled()) {
            return;
        }

        $this->context->controller->registerStylesheet(
            'module-wtholidays',
            'modules/' . $this->name . '/views/css/wtholidays.css',
            [
                'media' => 'all',
                'priority' => 145,
                'version' => '20260606-01',
            ]
        );
    }

    public function hookDisplayHome($params)
    {
        return $this->renderNotice('home');
    }

    public function hookDisplayShoppingCart($params)
    {
        return $this->renderNotice('cart');
    }

    public function hookDisplayProductAdditionalInfo($params)
    {
        return $this->renderNotice('product');
    }

    protected function renderNotice($context)
    {
        if (!$this->isNoticeEnabled()) {
            return '';
        }

        $notice = $this->getNoticeData();
        if ($notice['message'] === '') {
            return '';
        }

        $template = 'views/templates/hook/' . $context . '.tpl';
        $this->context->smarty->assign([
            'wtholidays' => $notice,
        ]);

        return $this->display(__FILE__, $template);
    }

    protected function renderForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Vacances boutique'),
                    'icon' => 'icon-calendar',
                ],
                'description' => $this->l('Le message s’affiche sur la home, la page produit et le panier. Variables disponibles dans le message : $date_start et $date_end.'),
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Activer le module'),
                        'name' => self::CFG_ENABLED,
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'wtholidays_enabled_on',
                                'value' => 1,
                                'label' => $this->l('Oui'),
                            ],
                            [
                                'id' => 'wtholidays_enabled_off',
                                'value' => 0,
                                'label' => $this->l('Non'),
                            ],
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Date de début'),
                        'name' => self::CFG_DATE_START,
                        'desc' => $this->l('Format conseillé : YYYY-MM-DD'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Date de fin'),
                        'name' => self::CFG_DATE_END,
                        'desc' => $this->l('Format conseillé : YYYY-MM-DD'),
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Titre'),
                        'name' => self::CFG_HEADLINE,
                        'lang' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Message'),
                        'name' => self::CFG_MESSAGE,
                        'lang' => true,
                        'autoload_rte' => false,
                        'rows' => 4,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                    'name' => 'submitWtHolidays',
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
        $helper->submit_action = 'submitWtHolidays';
        $helper->show_cancel_button = false;
        $helper->fields_value = $this->getFormValues();
        $helper->languages = $this->getHelperLanguages($defaultLang);
        $helper->id_language = (int) $this->context->language->id;

        return $helper->generateForm([$fieldsForm]);
    }

    protected function getFormValues()
    {
        $values = [];
        $defaults = $this->getDefaultContent();

        $values[self::CFG_ENABLED] = (int) Configuration::get(self::CFG_ENABLED, null, null, null, 0);
        $values[self::CFG_DATE_START] = (string) Configuration::get(self::CFG_DATE_START, null, null, null, '');
        $values[self::CFG_DATE_END] = (string) Configuration::get(self::CFG_DATE_END, null, null, null, '');

        foreach ($defaults as $key => $defaultValue) {
            foreach (Language::getLanguages(false) as $language) {
                $idLang = (int) $language['id_lang'];
                $values[$key][$idLang] = Configuration::get($key, $idLang) ?: $defaultValue;
            }
        }

        return $values;
    }

    protected function saveConfiguration()
    {
        $errors = [];
        $dateStart = trim((string) Tools::getValue(self::CFG_DATE_START, ''));
        $dateEnd = trim((string) Tools::getValue(self::CFG_DATE_END, ''));

        if ($dateStart !== '' && !$this->isValidDate($dateStart)) {
            $errors[] = $this->l('La date de début est invalide.');
        }

        if ($dateEnd !== '' && !$this->isValidDate($dateEnd)) {
            $errors[] = $this->l('La date de fin est invalide.');
        }

        if (!empty($errors)) {
            return $errors;
        }

        Configuration::updateValue(self::CFG_ENABLED, (int) Tools::getValue(self::CFG_ENABLED, 0));
        Configuration::updateValue(self::CFG_DATE_START, $dateStart);
        Configuration::updateValue(self::CFG_DATE_END, $dateEnd);

        $defaults = $this->getDefaultContent();
        foreach ($defaults as $key => $defaultValue) {
            $translations = [];
            foreach (Language::getLanguages(false) as $language) {
                $idLang = (int) $language['id_lang'];
                $value = (string) Tools::getValue($key . '_' . $idLang, '');
                $translations[$idLang] = trim($value) !== '' ? $value : $defaultValue;
            }
            Configuration::updateValue($key, $translations, true);
        }

        return [];
    }

    protected function getNoticeData()
    {
        $headline = $this->getTranslatedConfigValue(self::CFG_HEADLINE, $this->getDefaultContent()[self::CFG_HEADLINE]);
        $messageTemplate = $this->getTranslatedConfigValue(self::CFG_MESSAGE, $this->getDefaultContent()[self::CFG_MESSAGE]);
        $dateStartRaw = (string) Configuration::get(self::CFG_DATE_START, null, null, null, '');
        $dateEndRaw = (string) Configuration::get(self::CFG_DATE_END, null, null, null, '');

        return [
            'headline' => $headline,
            'message' => $this->replaceVariables($messageTemplate, $dateStartRaw, $dateEndRaw),
            'date_start' => $this->formatDateForDisplay($dateStartRaw),
            'date_end' => $this->formatDateForDisplay($dateEndRaw),
        ];
    }

    protected function replaceVariables($message, $dateStartRaw, $dateEndRaw)
    {
        $replacements = [
            '$date_start' => $this->formatDateForDisplay($dateStartRaw),
            '$date_end' => $this->formatDateForDisplay($dateEndRaw),
        ];

        return strtr((string) $message, $replacements);
    }

    protected function formatDateForDisplay($dateValue)
    {
        $dateValue = trim((string) $dateValue);
        if ($dateValue === '' || !$this->isValidDate($dateValue)) {
            return $dateValue;
        }

        $timestamp = strtotime($dateValue);
        if ($timestamp === false) {
            return $dateValue;
        }

        return date('d.m.Y', $timestamp);
    }

    protected function isValidDate($dateValue)
    {
        $date = DateTime::createFromFormat('Y-m-d', $dateValue);

        return $date instanceof DateTime && $date->format('Y-m-d') === $dateValue;
    }

    protected function isNoticeEnabled()
    {
        return (bool) Configuration::get(self::CFG_ENABLED, null, null, null, 0);
    }

    protected function getDefaultContent()
    {
        return [
            self::CFG_HEADLINE => $this->l('Vacances de la boutique'),
            self::CFG_MESSAGE => $this->l('La boutique physique sera fermée du $date_start au $date_end. Les commandes en ligne seront traitées à notre retour.'),
        ];
    }

    protected function getTranslatedConfigValue($key, $defaultValue)
    {
        $value = Configuration::get($key, (int) $this->context->language->id);

        return $value ?: $defaultValue;
    }

    protected function getHelperLanguages($defaultLang)
    {
        $languages = Language::getLanguages(false);

        foreach ($languages as &$language) {
            $language['is_default'] = ((int) $language['id_lang'] === (int) $defaultLang) ? 1 : 0;
        }

        return $languages;
    }

    protected function setDefaultConfiguration()
    {
        Configuration::updateValue(self::CFG_ENABLED, 0);
        Configuration::updateValue(self::CFG_DATE_START, '');
        Configuration::updateValue(self::CFG_DATE_END, '');
        Configuration::updateValue(self::CFG_HEADLINE, $this->buildTranslatedDefaults(self::CFG_HEADLINE), true);
        Configuration::updateValue(self::CFG_MESSAGE, $this->buildTranslatedDefaults(self::CFG_MESSAGE), true);

        return true;
    }

    protected function deleteConfiguration()
    {
        return Configuration::deleteByName(self::CFG_ENABLED)
            && Configuration::deleteByName(self::CFG_DATE_START)
            && Configuration::deleteByName(self::CFG_DATE_END)
            && Configuration::deleteByName(self::CFG_HEADLINE)
            && Configuration::deleteByName(self::CFG_MESSAGE);
    }

    protected function buildTranslatedDefaults($key)
    {
        $defaults = $this->getDefaultContent();
        $translations = [];

        foreach (Language::getLanguages(false) as $language) {
            $translations[(int) $language['id_lang']] = $defaults[$key];
        }

        return $translations;
    }
}
