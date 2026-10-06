<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminWtTranslationController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        parent::__construct();
    }

    public function postProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && Tools::isSubmit('saveDeepL')) {
            if (!$this->access('edit')) {
                $this->errors[] = 'Accès refusé.';
            } else {
                $key = trim((string) Tools::getValue('deepl_key'));
                if (preg_match('/[\r\n]/', $key)) {
                    $this->errors[] = 'Clé DeepL invalide.';
                } elseif ($key !== '') {
                    Configuration::updateValue('WTTRANSLATION_KEY', $key);
                }
                if (!$this->errors) {
                    $this->confirmations[] = 'Configuration enregistrée.';
                }
            }
        }
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS(_MODULE_DIR_ . 'wttranslation/views/js/admin.js');
    }

    public function ajaxProcessTranslate()
    {
        header('Content-Type: application/json');
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->access('edit')) {
                throw new WtTranslationException('Accès refusé.');
            }
            if (Shop::getContext() !== Shop::CONTEXT_SHOP) {
                throw new WtTranslationException('Sélectionnez une boutique.');
            }
            $count = $this->module->translateProduct((int) Tools::getValue('id_product'));
            $this->ajaxRender(json_encode(['ok' => true, 'fields' => $count]));
        } catch (Throwable $e) {
            // Only known operational errors are displayed; database diagnostics stay server-side.
            $message = $e instanceof WtTranslationException ? $e->getMessage() : 'Erreur technique lors de la traduction.';
            $this->ajaxRender(json_encode(['ok' => false, 'error' => $message]));
        }
        exit;
    }

    public function ajaxProcessPendingCount()
    {
        header('Content-Type: application/json');
        try {
            if (!$this->access('view') || Shop::getContext() !== Shop::CONTEXT_SHOP) {
                throw new WtTranslationException('Accès refusé.');
            }
            $this->ajaxRender(json_encode(['ok' => true, 'count' => count($this->module->candidates())]));
        } catch (Throwable $e) {
            $this->ajaxRender(json_encode(['ok' => false]));
        }
        exit;
    }

    public function initContent()
    {
        parent::initContent();
        $rows = [];
        try {
            if (Shop::getContext() !== Shop::CONTEXT_SHOP) {
                throw new WtTranslationException('Sélectionnez une boutique.');
            }
            $rows = $this->module->candidates();
            foreach ($rows as &$row) {
                $row['missing_description'] = WtTranslation::hasText($row['fr_description']) && !WtTranslation::hasText($row['en_description']);
                $row['missing_short'] = WtTranslation::hasText($row['fr_short']) && !WtTranslation::hasText($row['en_short']);
                $row['edit_url'] = $this->context->link->getAdminLink('AdminProducts', true,
                    ['route' => 'admin_products_edit', 'productId' => (int) $row['id_product']]);
            }
            unset($row);
        } catch (Throwable $e) {
            $rows = [];
            $this->errors[] = $e instanceof WtTranslationException ? $e->getMessage() : 'Impossible de charger les produits.';
        }
        $this->context->smarty->assign([
            'wt_rows' => $rows,
            'wt_url' => $this->context->link->getAdminLink('AdminWtTranslation'),
            'wt_configured' => (bool) Configuration::get('WTTRANSLATION_KEY'),
            'wt_can_edit' => $this->access('edit'),
        ]);
        $this->content .= $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'wttranslation/views/templates/admin/list.tpl');
        $this->context->smarty->assign('content', $this->content);
    }
}
