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
class AdminTagsController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'fme_tags';
        $this->className = 'EventTags';
        $this->identifier = 'id_fme_tags';
        $this->lang = true;
        $this->show_toolbar = false;
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bootstrap = true;
        parent::__construct();
        $this->context = Context::getContext();
        $this->fields_list = [
            'id_fme_tags' => [
                'title' => $this->module->l('ID'),
                'width' => 25,
            ],
            'title' => [
                'title' => $this->module->l('Tag Title'),
            ],
            'friendly_url' => [
                'title' => $this->module->l('Friendly Url'),
            ],
            'active' => [
                'title' => $this->module->l('Active'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
            ],
        ];
        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected'),
                'confirm' => $this->module->l('Delete selected items?'),
            ],
        ];
        $url = '';
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $router = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance()->get('router');
 
            $url = $router->generate('admin_module_configure_action', [
                'module_name' => $this->module->name,
            ], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $url = $this->context->link->getAdminLink('AdminModules', true);
        }
        $this->context->smarty->assign([
            'version' => _PS_VERSION_,
            'configure_link' => $url,
            'manage_events_link' => $this->context->link->getAdminLink('AdminEvents'),
            'events_details_link' => $this->context->link->getAdminLink('AdminEventsDetails'),
            'events_tags_link' => $this->context->link->getAdminLink('AdminTags'),
        ]);
    }

    public function renderForm()
    {
        $type = 'switch';
        $this->fields_form = [
            'tinymce' => true,
            'legend' => [
                'title' => 'Tags',
                'icon' => 'icon-list',
            ],
            'input' => [
                [
                    'type' => $type,
                    'label' => $this->module->l('Enabled'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Yes'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('No'),
                        ],
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Title'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->module->l('Friendly URL'),
                    'name' => 'friendly_url',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->module->l('Only letters, numbers, underscore (_) and the minus (-) character are allowed.'),
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->module->l('Description'),
                    'lang' => true,
                    'name' => 'description',
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Submit'),
            ],
        ];

        return parent::renderForm();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function postProcess()
    {
        parent::postProcess();
    }
}
