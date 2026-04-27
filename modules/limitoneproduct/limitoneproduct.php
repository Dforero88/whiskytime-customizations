<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class LimitOneProduct extends Module
{
    public function __construct()
    {
        $this->name = 'limitoneproduct';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'David Forero';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Limit One Product');
        $this->description = $this->l('Limite la quantité à 1 pour certains produits selon leur référence.');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayHeader')
            && $this->registerHook('actionCartUpdateQuantityBefore')
            && $this->registerHook('displayFooter')
            && Configuration::updateValue('LIMITONE_PRODUCTS', json_encode([]));
    }

    public function uninstall()
    {
        // Supprimer la configuration
        Configuration::deleteByName('LIMITONE_PRODUCTS');

        // Se désenregistrer des hooks (facultatif mais propre)
        $this->unregisterHook('displayFooterProduct');
        $this->unregisterHook('displayHeader');
        $this->unregisterHook('actionCartUpdateQuantityBefore');

        return parent::uninstall();
    }


    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitLimitOneProduct')) {
            $refs = Tools::getValue('LIMITONE_PRODUCTS');
            $refs_array = array_filter(array_map('trim', explode(',', $refs)));
            Configuration::updateValue('LIMITONE_PRODUCTS', json_encode($refs_array));
            $output .= $this->displayConfirmation($this->l('Configuration mise à jour'));
        }

        $saved_refs = json_decode(Configuration::get('LIMITONE_PRODUCTS'), true);
        $refs_string = $saved_refs ? implode(',', $saved_refs) : '';

        return $output . $this->renderForm($refs_string);
    }

    protected function renderForm($refs)
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Produits limités'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('IDs produits'),
                        'name' => 'LIMITONE_PRODUCTS',
                        'desc' => $this->l('Indiquez les ID des produits séparées par des virgules. Exemple : 820, 239'),
                        'size' => 80,
                        'required' => false,
                        'class' => 'fixed-width-xxl',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLimitOneProduct';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->fields_value['LIMITONE_PRODUCTS'] = $refs;

        return $helper->generateForm([$fields_form]);
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->registerJavascript(
            'limitoneproduct-js',
            'modules/' . $this->name . '/limitoneproduct.js',
            ['position' => 'bottom', 'priority' => 150]
        );

        $limited_refs = json_decode(Configuration::get('LIMITONE_PRODUCTS'), true);
        Media::addJsDef(['limitOneRefs' => $limited_refs]);
    }

    public function hookDisplayFooterProduct($params)
    {
        $this->context->controller->registerJavascript(
            'limitoneproduct-js',
            'modules/' . $this->name . '/limitoneproduct.js',
            ['position' => 'bottom', 'priority' => 150]
        );

        $limited_refs = json_decode(Configuration::get('LIMITONE_PRODUCTS'), true);
        Media::addJsDef(['limitOneRefs' => $limited_refs]);
        PrestaShopLogger::addLog('LimitOneProduct: JS injecté sur la fiche produit');
    }


    public function hookDisplayFooter()
    {
    PrestaShopLogger::addLog('LimitOneProduct: hookDisplayFooter appelé');
    $this->hookDisplayHeader(); // exécute la même logique
    }


public function hookActionCartUpdateQuantityBefore($params)
{
    $cart = $this->context->cart;
    $id_product = (int)$params['id_product'];

    $limitedRefs = json_decode(Configuration::get('LIMITONE_PRODUCTS'), true);

    $product = new Product($id_product, false, $this->context->language->id);

    if (in_array($product->reference, $limitedRefs)) {
        $currentQty = $cart->getProductQuantity($id_product);
        
        // Si le produit est déjà dans le panier, on bloque l'ajout
        if ($currentQty >= 1) {
            // Important : retourner false pour annuler l'ajout
            $params['qty'] = 0;
            $params['operation'] = '0';
            return false;
        }
    }
}


public function hookActionCartSave($params)
{
    $cart = $this->context->cart;

    // Liste des références limitées
    $limitedRefs = json_decode(Configuration::get('LIMITONE_PRODUCTS'), true);

    foreach ($cart->getProducts() as $product) {
        if (in_array($product['reference'], $limitedRefs)) {
            if ($product['cart_quantity'] > 1) {
                // Force la quantité totale à 1
                $cart->updateQty(
                    1, // quantité finale
                    (int)$product['id_product'],
                    (int)$product['id_product_attribute'],
                    false,
                    'up', // mode additionne ou remplace ? 'up' additionne mais on force qty=1
                    0,     // operation = 0 pour remplacer
                    new Shop($cart->id_shop)
                );
            }
        }
    }
}




}
