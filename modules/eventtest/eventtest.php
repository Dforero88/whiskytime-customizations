<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class EventTest extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'eventtest';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'David Forero';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Event list description');
        $this->description = $this->l('Module to add description into event list page with multilingual WYSIWYG field');
    }

    public function install()
    {
        return parent::install() && 
               $this->registerHook('displayBackOfficeHeader') &&
               $this->registerHook('displayEventContent') &&
               $this->createTable() &&
               $this->installDemoData();
    }

    public function uninstall()
    {
        return parent::uninstall() && 
               $this->unregisterHook('displayBackOfficeHeader') &&
               $this->unregisterHook('displayEventContent') &&
               $this->deleteTable(); // Nouvelle méthode pour supprimer la table
    }

    protected function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'eventtest_content` (
            `id_content` INT(11) NOT NULL AUTO_INCREMENT,
            `id_lang` INT(11) NOT NULL,
            `content` TEXT NOT NULL,
            `date_add` DATETIME NOT NULL,
            `date_upd` DATETIME NOT NULL,
            PRIMARY KEY (`id_content`, `id_lang`)
        ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

        return Db::getInstance()->execute($sql);
    }

    protected function deleteTable()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'eventtest_content`
        ');
    }

    protected function installDemoData()
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            Db::getInstance()->insert('eventtest_content', [
                'id_lang' => (int)$lang['id_lang'],
                'content' => '',
                'date_add' => date('Y-m-d H:i:s'),
                'date_upd' => date('Y-m-d H:i:s')
            ]);
        }
        return true;
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS(_PS_JS_DIR_.'tinymce/tinymce.min.js');
            $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
            $this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce_setup.js');
            
            Media::addJsDef([
                'baseAdminDir' => basename(_PS_ADMIN_DIR_),
                'iso_tiny_mce' => $this->context->language->iso_code,
                'default_language' => (int)Configuration::get('PS_LANG_DEFAULT')
            ]);
        }
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit_eventtest')) {
            $this->processSave();
        }

        return $this->renderForm();
    }

    protected function processSave()
    {
        $languages = Language::getLanguages(false);
        
        foreach ($languages as $lang) {
            $content = Tools::getValue('EVENTTEST_CONTENT_'.(int)$lang['id_lang']);
            
            Db::getInstance()->update('eventtest_content', [
                'content' => pSQL($content, true),
                'date_upd' => date('Y-m-d H:i:s')
            ], 'id_lang = '.(int)$lang['id_lang']);
        }

        Tools::clearSmartyCache();
        Tools::clearCache();

        return $this->displayConfirmation($this->l('Settings updated'));
    }

    public function renderForm()
    {
        $languages = Language::getLanguages(false);
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $form = new HelperForm();
        $form->module = $this;
        $form->name_controller = $this->name;
        $form->token = Tools::getAdminTokenLite('AdminModules');
        $form->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $form->title = $this->displayName;
        $form->submit_action = 'submit_eventtest';
        $form->default_form_language = $default_lang;
        $form->allow_employee_form_lang = $default_lang;
        $form->languages = $languages;

        // Chargement des valeurs depuis la table dédiée
        foreach ($languages as $lang) {
            $content = Db::getInstance()->getValue('
                SELECT content FROM '._DB_PREFIX_.'eventtest_content
                WHERE id_lang = '.(int)$lang['id_lang']
            );
            $form->fields_value['EVENTTEST_CONTENT'][$lang['id_lang']] = $content;
        }

        $fields_form = [
            'form' => [
                'tinymce' => true,
                'legend' => [
                    'title' => $this->l('Content Editor'),
                    'icon' => 'icon-pencil'
                ],
                'input' => [
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Content'),
                        'name' => 'EVENTTEST_CONTENT',
                        'autoload_rte' => true,
                        'lang' => true,
                        'cols' => 60,
                        'rows' => 30,
                        'class' => 'rte autoload_rte',
                        'hint' => $this->l('Enter your content for each language')
                    ]
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-success pull-right'
                ]
            ]
        ];

        return $form->generateForm([$fields_form]);
    }

    public function hookDisplayEventContent($params)
    {
        $id_lang = $this->context->language->id;
        $content = Db::getInstance()->getValue('
            SELECT content FROM '._DB_PREFIX_.'eventtest_content
            WHERE id_lang = '.(int)$id_lang
        );
        
        $this->context->smarty->assign([
            'eventtest_content' => $content
        ]);
        
        return $this->display(__FILE__, 'eventtest.tpl');
    }
}