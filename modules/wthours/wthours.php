<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class WtHours extends Module
{
    const ADMIN_CLASS_NAME = 'AdminWtHours';
    const ADMIN_PARENT_CLASS_NAME = 'WHISKYTIME';
    const ADMIN_PARENT_LEGACY_CLASS_NAME = 'AdminWhiskyTime';
    const CFG_TITLE = 'WTHOURS_TITLE';
    const CFG_BODY = 'WTHOURS_BODY';

    public function __construct()
    {
        $this->name = 'wthours';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'OpenAI';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Horaires');
        $this->description = $this->l('Affiche un bloc horaires et adresse sur la home.');
    }

    public function install()
    {
        return parent::install()
            && $this->installParentTab()
            && $this->syncParentTab()
            && $this->installTab()
            && $this->registerHook('displayHome')
            && $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        return $this->uninstallTab()
            && $this->uninstallParentTabIfEmpty()
            && parent::uninstall();
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitWtHours')) {
            $this->saveConfiguration();

            return $this->displayConfirmation($this->l('Configuration mise à jour.')) . $this->renderForm();
        }

        return $this->renderForm();
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->registerJavascript(
            'module-wthours',
            'modules/' . $this->name . '/views/js/wthours.js',
            [
                'position' => 'bottom',
                'priority' => 150,
                'attribute' => 'defer',
            ]
        );

        $this->context->controller->registerStylesheet(
            'module-wthours',
            'modules/' . $this->name . '/views/css/wthours.css',
            [
                'media' => 'all',
                'priority' => 150,
                'version' => '20260524-10',
            ]
        );
    }

    public function hookDisplayHome($params)
    {
        $this->context->smarty->assign([
            'wthours' => $this->getHoursData(),
        ]);

        return $this->display(__FILE__, 'views/templates/hook/wthours.tpl');
    }

    protected function getHoursData()
    {
        return [
            'title' => $this->getTranslatedConfigValue(self::CFG_TITLE, $this->l('Horaires')),
            'body' => $this->getTranslatedConfigValue(self::CFG_BODY, $this->l("Mardi à vendredi : 10h-12h30 / 14h-18h30\nSamedi : 10h-17h\nDimanche et lundi : fermé")),
            'address_label' => $this->l('Boutique'),
            'address_line_1' => "Rue de l'Horloge 6",
            'address_line_2' => '1095 Lutry',
        ];
    }

    protected function renderForm()
    {
        $defaultLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Bloc horaires'),
                    'icon' => 'icon-time',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Titre horaires'),
                        'name' => self::CFG_TITLE,
                        'lang' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Description horaires'),
                        'name' => self::CFG_BODY,
                        'lang' => true,
                        'autoload_rte' => false,
                        'rows' => 4,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                    'name' => 'submitWtHours',
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
        $helper->submit_action = 'submitWtHours';
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
    }

    protected function getDefaultContent()
    {
        return [
            self::CFG_TITLE => $this->l('Horaires'),
            self::CFG_BODY => $this->l("Mardi à vendredi : 10h-12h30 / 14h-18h30\nSamedi : 10h-17h\nDimanche et lundi : fermé"),
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

    protected function installParentTab()
    {
        $idParent = (int) Tab::getIdFromClassName(self::ADMIN_PARENT_CLASS_NAME);
        if ($idParent) {
            return true;
        }

        $legacyParentId = (int) Tab::getIdFromClassName(self::ADMIN_PARENT_LEGACY_CLASS_NAME);
        if ($legacyParentId) {
            $tab = new Tab($legacyParentId);
            $tab->active = 1;
            $tab->class_name = self::ADMIN_PARENT_CLASS_NAME;
            $tab->id_parent = 0;
            $tab->module = '';
            $tab->icon = '';
            $tab->wording = 'Whisky Time';
            $tab->wording_domain = 'Modules.Wthours.Admin';

            foreach (Language::getLanguages(false) as $language) {
                $tab->name[(int) $language['id_lang']] = 'Whisky Time';
            }

            return (bool) $tab->update();
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = self::ADMIN_PARENT_CLASS_NAME;
        $tab->id_parent = 0;
        $tab->module = '';
        $tab->icon = '';
        $tab->wording = 'Whisky Time';
        $tab->wording_domain = 'Modules.Wthours.Admin';

        foreach (Language::getLanguages(false) as $language) {
            $tab->name[(int) $language['id_lang']] = 'Whisky Time';
        }

        return (bool) $tab->add();
    }

    protected function syncParentTab()
    {
        $idParent = (int) Tab::getIdFromClassName(self::ADMIN_PARENT_CLASS_NAME);
        if (!$idParent) {
            return false;
        }

        $tab = new Tab($idParent);
        $tab->module = '';
        $tab->active = 1;
        $tab->id_parent = 0;
        $tab->icon = '';
        $tab->wording = 'Whisky Time';
        $tab->wording_domain = 'Modules.Wthours.Admin';

        foreach (Language::getLanguages(false) as $language) {
            $tab->name[(int) $language['id_lang']] = 'Whisky Time';
        }

        return (bool) $tab->update();
    }

    protected function installTab()
    {
        $idTab = (int) Tab::getIdFromClassName(self::ADMIN_CLASS_NAME);
        $tab = $idTab ? new Tab($idTab) : new Tab();
        $tab->active = 1;
        $tab->class_name = self::ADMIN_CLASS_NAME;
        $tab->id_parent = (int) Tab::getIdFromClassName(self::ADMIN_PARENT_CLASS_NAME);
        $tab->module = $this->name;
        $tab->wording = 'Horaires';
        $tab->wording_domain = 'Modules.Wthours.Admin';
        $tab->icon = 'access_time';

        foreach (Language::getLanguages(false) as $language) {
            $tab->name[(int) $language['id_lang']] = $this->l('Horaires');
        }

        return $idTab ? (bool) $tab->update() : (bool) $tab->add();
    }

    protected function uninstallTab()
    {
        $idTab = (int) Tab::getIdFromClassName(self::ADMIN_CLASS_NAME);
        if (!$idTab) {
            return true;
        }

        $tab = new Tab($idTab);

        return (bool) $tab->delete();
    }

    protected function uninstallParentTabIfEmpty()
    {
        $idParent = (int) Tab::getIdFromClassName(self::ADMIN_PARENT_CLASS_NAME);
        if (!$idParent) {
            return true;
        }

        $childrenCount = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'tab` WHERE id_parent = ' . (int) $idParent
        );

        if ($childrenCount > 0) {
            return true;
        }

        $tab = new Tab($idParent);

        return (bool) $tab->delete();
    }
}
