<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class LimitOneProduct extends Module
{
    /** @var int[]|null */
    protected $limitedProductIds = null;

    public function __construct()
    {
        $this->name = 'limitoneproduct';
        $this->tab = 'front_office_features';
        $this->version = '1.1.0';
        $this->author = 'David Forero';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Limit One Product');
        $this->description = $this->l('Limit quantity to 1 for selected products.');
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('displayHeader')
            && $this->registerHook('actionCartUpdateQuantityBefore')
            && $this->registerHook('actionCartSave');
    }

    public function uninstall()
    {
        $this->unregisterHook('displayHeader');
        $this->unregisterHook('displayFooter');
        $this->unregisterHook('displayFooterProduct');
        $this->unregisterHook('actionCartUpdateQuantityBefore');
        $this->unregisterHook('actionCartSave');
        Configuration::deleteByName('LIMITONE_PRODUCTS');

        return $this->uninstallDb() && parent::uninstall();
    }

    protected function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'limitoneproduct` (
            `id_limitoneproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            PRIMARY KEY (`id_limitoneproduct`),
            UNIQUE KEY `uniq_limitoneproduct_product` (`id_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return Db::getInstance()->execute($sql);
    }

    protected function uninstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'limitoneproduct`');
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitLimitOneProductAdd')) {
            if (!$this->isValidToken()) {
                $output .= $this->displayError($this->l('Invalid security token.'));
            } else {
                $idProduct = (int) Tools::getValue('id_product');
                if ($idProduct <= 0) {
                    $output .= $this->displayError($this->l('Invalid product.'));
                } elseif ($this->addLimitedProduct($idProduct)) {
                    $output .= $this->displayConfirmation($this->l('Product added to limit-one list.'));
                } else {
                    $output .= $this->displayError($this->l('Unable to add this product.'));
                }
            }
        }

        if (Tools::isSubmit('submitLimitOneProductRemove')) {
            if (!$this->isValidToken()) {
                $output .= $this->displayError($this->l('Invalid security token.'));
            } else {
                $idProduct = (int) Tools::getValue('id_product');
                if ($idProduct <= 0) {
                    $output .= $this->displayError($this->l('Invalid product.'));
                } elseif ($this->removeLimitedProduct($idProduct)) {
                    $output .= $this->displayConfirmation($this->l('Product removed from limit-one list.'));
                } else {
                    $output .= $this->displayError($this->l('Unable to remove this product.'));
                }
            }
        }

        $searchTerm = trim((string) Tools::getValue('limitoneproduct_search', ''));
        $searchResults = [];
        if ($searchTerm !== '') {
            $searchResults = $this->searchProducts($searchTerm);
        }

        return $output
            . $this->renderSearchForm($searchTerm)
            . $this->renderSearchResults($searchResults)
            . $this->renderLimitedProductsTable();
    }

    public function hookDisplayHeader()
    {
        $controller = $this->context->controller;
        if (!$controller || !method_exists($controller, 'registerJavascript')) {
            return;
        }

        $controller->registerJavascript(
            'limitoneproduct-js',
            'modules/' . $this->name . '/limitoneproduct.js',
            ['position' => 'bottom', 'priority' => 150]
        );

        Media::addJsDef([
            'limitOneProductIds' => array_map('strval', $this->getLimitedProductIds()),
            'limitOneCartProductIds' => array_map('strval', $this->getLimitedProductIdsInCart()),
            'limitOnePerCustomerMessage' => $this->l('Limité à un par client'),
        ]);
    }

    public function hookActionCartUpdateQuantityBefore($params)
    {
        $idProduct = (int) ($params['id_product'] ?? 0);
        if ($idProduct <= 0 || !$this->isLimitedProduct($idProduct)) {
            return;
        }

        $cart = $this->context->cart;
        if (!$cart) {
            return;
        }

        $currentQty = $this->getCartQuantity($cart, $idProduct);
        if ($currentQty >= 1) {
            if (isset($params['qty'])) {
                $params['qty'] = 0;
            }
            if (isset($params['quantity'])) {
                $params['quantity'] = 0;
            }
            if (isset($params['operator'])) {
                $params['operator'] = 'up';
            }

            return false;
        }

        if (isset($params['qty']) && (int) $params['qty'] > 1) {
            $params['qty'] = 1;
        }
        if (isset($params['quantity']) && (int) $params['quantity'] > 1) {
            $params['quantity'] = 1;
        }
    }

    public function hookActionCartSave($params)
    {
        $cart = $this->context->cart;
        if (!$cart) {
            return;
        }

        foreach ($cart->getProducts() as $product) {
            $idProduct = (int) ($product['id_product'] ?? 0);
            if ($idProduct <= 0 || !$this->isLimitedProduct($idProduct)) {
                continue;
            }

            $cartQty = (int) ($product['cart_quantity'] ?? 0);
            if ($cartQty <= 1) {
                continue;
            }

            $cart->updateQty(
                $cartQty - 1,
                $idProduct,
                (int) ($product['id_product_attribute'] ?? 0),
                false,
                'down',
                0,
                new Shop((int) $cart->id_shop)
            );
        }
    }

    public function isLimitedProduct($idProduct)
    {
        $idProduct = (int) $idProduct;
        if ($idProduct <= 0) {
            return false;
        }

        return in_array($idProduct, $this->getLimitedProductIds(), true);
    }

    public function getLimitedProductIds()
    {
        if ($this->limitedProductIds !== null) {
            return $this->limitedProductIds;
        }

        $rows = Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'limitoneproduct`');
        $this->limitedProductIds = array_map('intval', array_column($rows ?: [], 'id_product'));

        return $this->limitedProductIds;
    }

    protected function addLimitedProduct($idProduct)
    {
        $idProduct = (int) $idProduct;
        if ($idProduct <= 0) {
            return false;
        }

        $exists = (bool) Product::existsInDatabase($idProduct, 'product');
        if (!$exists) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $rowExists = (bool) Db::getInstance()->getValue(
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'limitoneproduct` WHERE `id_product` = ' . $idProduct
        );

        if ($rowExists) {
            $result = Db::getInstance()->update(
                'limitoneproduct',
                ['updated_at' => pSQL($now)],
                '`id_product` = ' . $idProduct
            );
        } else {
            $result = Db::getInstance()->insert('limitoneproduct', [
                'id_product' => $idProduct,
                'created_at' => pSQL($now),
                'updated_at' => pSQL($now),
            ]);
        }

        $this->limitedProductIds = null;

        return (bool) $result;
    }

    protected function removeLimitedProduct($idProduct)
    {
        $idProduct = (int) $idProduct;
        if ($idProduct <= 0) {
            return false;
        }

        $this->limitedProductIds = null;

        return (bool) Db::getInstance()->delete('limitoneproduct', '`id_product` = ' . $idProduct);
    }

    protected function searchProducts($query)
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }

        $languageId = (int) $this->context->language->id;
        $shopId = (int) $this->context->shop->id;
        $querySql = '%' . pSQL($query) . '%';
        $exactId = ctype_digit($query) ? (int) $query : 0;

        $sql = 'SELECT p.`id_product`, pl.`name`, p.`reference`
            FROM `' . _DB_PREFIX_ . 'product` p
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = ' . $languageId . ' AND pl.`id_shop` = ' . $shopId . ')
            WHERE pl.`name` LIKE "' . $querySql . '"
                OR p.`reference` LIKE "' . $querySql . '"';

        if ($exactId > 0) {
            $sql .= ' OR p.`id_product` = ' . $exactId;
        }

        $sql .= ' ORDER BY pl.`name` ASC LIMIT 20';

        $rows = Db::getInstance()->executeS($sql) ?: [];
        foreach ($rows as &$row) {
            $row['quantity'] = (int) StockAvailable::getQuantityAvailableByProduct((int) $row['id_product']);
        }

        return $rows;
    }

    protected function getLimitedProductsData()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT lp.`id_product`, pl.`name`, p.`reference`
            FROM `' . _DB_PREFIX_ . 'limitoneproduct` lp
            INNER JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = lp.`id_product`)
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (pl.`id_product` = lp.`id_product` AND pl.`id_lang` = ' . (int) $this->context->language->id . ')
            ' . Shop::addSqlRestrictionOnLang('pl') . '
            ORDER BY pl.`name` ASC'
        );

        if (!$rows) {
            return [];
        }

        foreach ($rows as &$row) {
            $row['quantity'] = (int) StockAvailable::getQuantityAvailableByProduct((int) $row['id_product']);
        }

        return $rows;
    }

    protected function renderSearchForm($searchTerm)
    {
        $action = htmlspecialchars($this->getModuleConfigUrl(), ENT_QUOTES, 'UTF-8');
        $token = htmlspecialchars(Tools::getAdminTokenLite('AdminModules'), ENT_QUOTES, 'UTF-8');
        $searchTerm = htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8');

        return '
        <div class="panel">
            <h3>' . $this->l('Ajouter un produit limite a 1') . '</h3>
            <form method="post" action="' . $action . '">
                <input type="hidden" name="limitoneproduct_token" value="' . $token . '">
                <div class="form-group">
                    <label>' . $this->l('Rechercher un produit') . '</label>
                    <input type="text" class="form-control" name="limitoneproduct_search" value="' . $searchTerm . '" placeholder="' . $this->l('Nom, ID, reference...') . '">
                </div>
                <button type="submit" class="btn btn-primary">' . $this->l('Rechercher') . '</button>
            </form>
        </div>';
    }

    protected function renderSearchResults(array $products)
    {
        if (empty($products)) {
            if (trim((string) Tools::getValue('limitoneproduct_search', '')) === '') {
                return '';
            }

            return '<div class="panel"><p>' . $this->l('Aucun produit trouve.') . '</p></div>';
        }

        $action = htmlspecialchars($this->getModuleConfigUrl(), ENT_QUOTES, 'UTF-8');
        $token = htmlspecialchars(Tools::getAdminTokenLite('AdminModules'), ENT_QUOTES, 'UTF-8');
        $html = '
        <div class="panel">
            <h3>' . $this->l('Resultats') . '</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>' . $this->l('ID') . '</th>
                            <th>' . $this->l('Nom') . '</th>
                            <th>' . $this->l('Reference') . '</th>
                            <th>' . $this->l('Stock') . '</th>
                            <th>' . $this->l('Action') . '</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($products as $product) {
            $idProduct = (int) $product['id_product'];
            $html .= '
                <tr>
                    <td>' . $idProduct . '</td>
                    <td>' . htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars((string) $product['reference'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . (int) $product['quantity'] . '</td>
                    <td>';

            if ($this->isLimitedProduct($idProduct)) {
                $html .= '<span class="label label-info">' . $this->l('Deja limite') . '</span>';
            } else {
                $html .= '
                    <form method="post" action="' . $action . '" style="display:inline-block;">
                        <input type="hidden" name="limitoneproduct_token" value="' . $token . '">
                        <input type="hidden" name="id_product" value="' . $idProduct . '">
                        <button type="submit" name="submitLimitOneProductAdd" class="btn btn-default">' . $this->l('Ajouter') . '</button>
                    </form>';
            }

            $html .= '
                    </td>
                </tr>';
        }

        $html .= '
                    </tbody>
                </table>
            </div>
        </div>';

        return $html;
    }

    protected function renderLimitedProductsTable()
    {
        $products = $this->getLimitedProductsData();
        $action = htmlspecialchars($this->getModuleConfigUrl(), ENT_QUOTES, 'UTF-8');
        $token = htmlspecialchars(Tools::getAdminTokenLite('AdminModules'), ENT_QUOTES, 'UTF-8');
        $html = '
        <div class="panel">
            <h3>' . $this->l('Produits limites a 1') . '</h3>';

        if (empty($products)) {
            return $html . '<p>' . $this->l('Aucun produit limite.') . '</p></div>';
        }

        $html .= '
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>' . $this->l('ID') . '</th>
                            <th>' . $this->l('Nom') . '</th>
                            <th>' . $this->l('Reference') . '</th>
                            <th>' . $this->l('Stock') . '</th>
                            <th>' . $this->l('Action') . '</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($products as $product) {
            $idProduct = (int) $product['id_product'];
            $html .= '
                <tr>
                    <td>' . $idProduct . '</td>
                    <td>' . htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars((string) $product['reference'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . (int) $product['quantity'] . '</td>
                    <td>
                        <form method="post" action="' . $action . '" style="display:inline-block;">
                            <input type="hidden" name="limitoneproduct_token" value="' . $token . '">
                            <input type="hidden" name="id_product" value="' . $idProduct . '">
                            <button type="submit" name="submitLimitOneProductRemove" class="btn btn-link">' . $this->l('Retirer') . '</button>
                        </form>
                    </td>
                </tr>';
        }

        $html .= '
                    </tbody>
                </table>
            </div>
        </div>';

        return $html;
    }

    protected function getCartQuantity(Cart $cart, $idProduct)
    {
        $idProduct = (int) $idProduct;
        foreach ($cart->getProducts() as $product) {
            if ((int) ($product['id_product'] ?? 0) === $idProduct) {
                return (int) ($product['cart_quantity'] ?? 0);
            }
        }

        return 0;
    }

    protected function getLimitedProductIdsInCart()
    {
        $cart = $this->context->cart;
        if (!$cart) {
            return [];
        }

        $limitedIds = $this->getLimitedProductIds();
        if (empty($limitedIds)) {
            return [];
        }

        $productIdsInCart = [];
        foreach ($cart->getProducts() as $product) {
            $idProduct = (int) ($product['id_product'] ?? 0);
            if ($idProduct > 0 && in_array($idProduct, $limitedIds, true)) {
                $productIdsInCart[] = $idProduct;
            }
        }

        return array_values(array_unique($productIdsInCart));
    }

    protected function isValidToken()
    {
        return Tools::getValue('limitoneproduct_token') === Tools::getAdminTokenLite('AdminModules');
    }

    protected function getModuleConfigUrl()
    {
        return $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name
            . '&token=' . Tools::getAdminTokenLite('AdminModules');
    }
}
