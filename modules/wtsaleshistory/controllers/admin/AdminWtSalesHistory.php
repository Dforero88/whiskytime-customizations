<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminWtSalesHistoryController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->meta_title = $this->module->l('Courbes de ventes');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addCSS($this->module->getPathUri() . 'views/css/admin.css');
        $this->addJS($this->module->getPathUri() . 'views/js/admin.js');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitRefreshWtSalesHistory')) {
            if ($this->module->refreshPrestashopData()) {
                Tools::redirectAdmin(self::$currentIndex . '&token=' . $this->token . '&refreshed=1');
            }

            $this->errors[] = $this->module->l('Le refresh des données Prestashop a échoué.');
        }

        parent::postProcess();
    }

    public function renderList()
    {
        $output = '';
        if ((int) Tools::getValue('refreshed') === 1) {
            $output .= $this->module->displayConfirmation($this->module->l('Données Prestashop rafraîchies.'));
        }

        if (!empty($this->errors)) {
            foreach ($this->errors as $error) {
                $output .= $this->module->displayError($error);
            }
        }

        $dashboard = $this->module->getDashboardViewData();
        $this->context->smarty->assign([
            'wtsh_refresh_action' => self::$currentIndex . '&token=' . $this->token,
            'wtsh_summary' => $dashboard['summary'],
            'wtsh_payload_json' => json_encode($dashboard['payload']),
            'wtsh_years' => $dashboard['years'],
            'wtsh_state_labels' => $this->module->getTrackedStateLabels(),
            'wtsh_state_labels_text' => implode(', ', $this->module->getTrackedStateLabels()),
            'wtsh_currency_sign' => $this->context->currency ? $this->context->currency->sign : 'CHF',
        ]);

        return $output . $this->module->display(
            _PS_MODULE_DIR_ . $this->module->name . DIRECTORY_SEPARATOR . $this->module->name . '.php',
            'views/templates/admin/dashboard.tpl'
        );
    }
}
