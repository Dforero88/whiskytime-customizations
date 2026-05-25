<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class LimitOneProduct extends Module
{
    /** @var int[]|null */
    protected $limitedProductIds = null;

    /** @var array<int, int>|null */
    protected $limitedProductLimits = null;

    /** @var bool */
    protected $schemaEnsured = false;

    public function __construct()
    {
        $this->name = 'limitoneproduct';
        $this->tab = 'front_office_features';
        $this->version = '1.2.0';
        $this->author = 'David Forero';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Limit One Product');
        $this->description = $this->l('Limit quantity per order for selected products.');
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
            `max_quantity_per_order` INT UNSIGNED NOT NULL DEFAULT 1,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            PRIMARY KEY (`id_limitoneproduct`),
            UNIQUE KEY `uniq_limitoneproduct_product` (`id_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return Db::getInstance()->execute($sql) && $this->ensureDbSchema();
    }

    protected function uninstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'limitoneproduct`');
    }

    public function getContent()
    {
        $this->ensureDbSchema();
        $output = '';

        if (Tools::isSubmit('submitLimitOneProductAdd')) {
            if (!$this->isValidToken()) {
                $output .= $this->displayError($this->l('Invalid security token.'));
            } else {
                $idProduct = (int) Tools::getValue('id_product');
                $limit = $this->sanitizeLimit(Tools::getValue('max_quantity_per_order'));
                if ($idProduct <= 0) {
                    $output .= $this->displayError($this->l('Invalid product.'));
                } elseif ($this->addLimitedProduct($idProduct, $limit)) {
                    $output .= $this->displayConfirmation($this->l('Product added to limited list.'));
                } else {
                    $output .= $this->displayError($this->l('Unable to add this product.'));
                }
            }
        }

        if (Tools::isSubmit('submitLimitOneProductUpdate')) {
            if (!$this->isValidToken()) {
                $output .= $this->displayError($this->l('Invalid security token.'));
            } else {
                $idProduct = (int) Tools::getValue('id_product');
                $limit = $this->sanitizeLimit(Tools::getValue('max_quantity_per_order'));
                if ($idProduct <= 0) {
                    $output .= $this->displayError($this->l('Invalid product.'));
                } elseif ($this->updateLimitedProductLimit($idProduct, $limit)) {
                    $output .= $this->displayConfirmation($this->l('Product limit updated.'));
                } else {
                    $output .= $this->displayError($this->l('Unable to update this product limit.'));
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
                    $output .= $this->displayConfirmation($this->l('Product removed from limited list.'));
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
        $this->ensureDbSchema();
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
            'limitOneProductLimits' => $this->getLimitedProductLimitsForJs(),
            'limitOneCartQuantities' => $this->getLimitedProductCartQuantities(),
            'limitOnePerCustomerMessageTemplate' => $this->l('Limité à %limit% par commande'),
            'limitOneReachedMessageTemplate' => $this->l('Limité à %limit% par commande. Limite déjà atteinte dans votre panier.'),
        ]);
    }

    public function hookActionCartUpdateQuantityBefore($params)
    {
        $this->ensureDbSchema();
        $idProduct = (int) ($params['id_product'] ?? 0);
        if ($idProduct <= 0 || !$this->isLimitedProduct($idProduct)) {
            return;
        }

        $cart = $this->context->cart;
        if (!$cart) {
            return;
        }

        $operator = (string) ($params['operator'] ?? 'up');
        if ($operator === 'down') {
            return;
        }

        $limit = $this->getProductLimit($idProduct);
        $currentQty = $this->getCartQuantity($cart, $idProduct);
        $remainingQty = max(0, $limit - $currentQty);

        if ($remainingQty <= 0) {
            if (isset($params['qty'])) {
                $params['qty'] = 0;
            }
            if (isset($params['quantity'])) {
                $params['quantity'] = 0;
            }

            return false;
        }

        if (isset($params['qty'])) {
            $params['qty'] = min((int) $params['qty'], $remainingQty);
        }
        if (isset($params['quantity'])) {
            $params['quantity'] = min((int) $params['quantity'], $remainingQty);
        }
    }

    public function hookActionCartSave($params)
    {
        $this->ensureDbSchema();
        $cart = $this->context->cart;
        if (!$cart) {
            return;
        }

        foreach ($cart->getProducts() as $product) {
            $idProduct = (int) ($product['id_product'] ?? 0);
            if ($idProduct <= 0 || !$this->isLimitedProduct($idProduct)) {
                continue;
            }

            $limit = $this->getProductLimit($idProduct);
            $cartQty = (int) ($product['cart_quantity'] ?? 0);
            if ($cartQty <= $limit) {
                continue;
            }

            $cart->updateQty(
                $cartQty - $limit,
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

        $this->limitedProductIds = array_map('intval', array_keys($this->getLimitedProductLimits()));

        return $this->limitedProductIds;
    }

    protected function getLimitedProductLimits()
    {
        $this->ensureDbSchema();
        if ($this->limitedProductLimits !== null) {
            return $this->limitedProductLimits;
        }

        $rows = Db::getInstance()->executeS(
            'SELECT `id_product`, `max_quantity_per_order` FROM `' . _DB_PREFIX_ . 'limitoneproduct`'
        ) ?: [];

        $limits = [];
        foreach ($rows as $row) {
            $idProduct = (int) ($row['id_product'] ?? 0);
            if ($idProduct <= 0) {
                continue;
            }

            $limits[$idProduct] = $this->sanitizeLimit($row['max_quantity_per_order'] ?? 1);
        }

        $this->limitedProductLimits = $limits;

        return $this->limitedProductLimits;
    }

    protected function getProductLimit($idProduct)
    {
        $idProduct = (int) $idProduct;
        if ($idProduct <= 0) {
            return 1;
        }

        $limits = $this->getLimitedProductLimits();

        return isset($limits[$idProduct]) ? $this->sanitizeLimit($limits[$idProduct]) : 1;
    }

    protected function getLimitedProductLimitsForJs()
    {
        $limits = [];
        foreach ($this->getLimitedProductLimits() as $idProduct => $limit) {
            $limits[(string) $idProduct] = (int) $limit;
        }

        return $limits;
    }

    protected function addLimitedProduct($idProduct, $limit = 1)
    {
        $idProduct = (int) $idProduct;
        $limit = $this->sanitizeLimit($limit);
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
                [
                    'max_quantity_per_order' => $limit,
                    'updated_at' => pSQL($now),
                ],
                '`id_product` = ' . $idProduct
            );
        } else {
            $result = Db::getInstance()->insert('limitoneproduct', [
                'id_product' => $idProduct,
                'max_quantity_per_order' => $limit,
                'created_at' => pSQL($now),
                'updated_at' => pSQL($now),
            ]);
        }

        $this->invalidateLimitedProductCache();

        return (bool) $result;
    }

    protected function updateLimitedProductLimit($idProduct, $limit)
    {
        $idProduct = (int) $idProduct;
        $limit = $this->sanitizeLimit($limit);
        if ($idProduct <= 0 || !$this->isLimitedProduct($idProduct)) {
            return false;
        }

        $result = Db::getInstance()->update(
            'limitoneproduct',
            [
                'max_quantity_per_order' => $limit,
                'updated_at' => pSQL(date('Y-m-d H:i:s')),
            ],
            '`id_product` = ' . $idProduct
        );

        $this->invalidateLimitedProductCache();

        return (bool) $result;
    }

    protected function removeLimitedProduct($idProduct)
    {
        $idProduct = (int) $idProduct;
        if ($idProduct <= 0) {
            return false;
        }

        $this->invalidateLimitedProductCache();

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
        $this->ensureDbSchema();
        $rows = Db::getInstance()->executeS(
            'SELECT lp.`id_product`, lp.`max_quantity_per_order`, pl.`name`, p.`reference`
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
            $row['max_quantity_per_order'] = $this->sanitizeLimit($row['max_quantity_per_order'] ?? 1);
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
            <h3>' . $this->l('Ajouter un produit limite') . '</h3>
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
                            <th>' . $this->l('Limite / commande') . '</th>
                            <th>' . $this->l('Action') . '</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($products as $product) {
            $idProduct = (int) $product['id_product'];
            $existingLimit = $this->isLimitedProduct($idProduct) ? $this->getProductLimit($idProduct) : 1;
            $html .= '
                <tr>
                    <td>' . $idProduct . '</td>
                    <td>' . htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars((string) $product['reference'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . (int) $product['quantity'] . '</td>
                    <td>';

            if ($this->isLimitedProduct($idProduct)) {
                $html .= '<span class="label label-info">' . sprintf(
                    $this->l('Deja limite a %d'),
                    $existingLimit
                ) . '</span>';
            } else {
                $html .= '
                    <form method="post" action="' . $action . '" style="display:flex;align-items:center;gap:12px;">
                        <input type="hidden" name="limitoneproduct_token" value="' . $token . '">
                        <input type="hidden" name="id_product" value="' . $idProduct . '">
                        <input type="number" class="form-control" name="max_quantity_per_order" min="1" step="1" value="1" style="max-width:110px;">
                    </td>
                    <td>
                        <button type="submit" name="submitLimitOneProductAdd" class="btn btn-default">' . $this->l('Ajouter') . '</button>
                    </form>';
            }

            if ($this->isLimitedProduct($idProduct)) {
                $html .= '</td><td></td>';
            }

            $html .= '
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
            <h3>' . $this->l('Produits limites') . '</h3>';

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
                            <th>' . $this->l('Limite / commande') . '</th>
                            <th>' . $this->l('Action') . '</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($products as $product) {
            $idProduct = (int) $product['id_product'];
            $limit = $this->sanitizeLimit($product['max_quantity_per_order'] ?? 1);
            $html .= '
                <tr>
                    <td>' . $idProduct . '</td>
                    <td>' . htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars((string) $product['reference'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . (int) $product['quantity'] . '</td>
                    <td>
                        <form method="post" action="' . $action . '" style="display:flex;align-items:center;gap:12px;">
                            <input type="hidden" name="limitoneproduct_token" value="' . $token . '">
                            <input type="hidden" name="id_product" value="' . $idProduct . '">
                            <input type="number" class="form-control" name="max_quantity_per_order" min="1" step="1" value="' . $limit . '" style="max-width:110px;">
                    </td>
                    <td>
                            <button type="submit" name="submitLimitOneProductUpdate" class="btn btn-default">' . $this->l('Mettre a jour') . '</button>
                        </form>
                        <form method="post" action="' . $action . '" style="display:inline-block;margin-left:12px;">
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

    protected function getLimitedProductCartQuantities()
    {
        $cart = $this->context->cart;
        if (!$cart) {
            return [];
        }

        $limitedIds = $this->getLimitedProductIds();
        if (empty($limitedIds)) {
            return [];
        }

        $quantities = [];
        foreach ($cart->getProducts() as $product) {
            $idProduct = (int) ($product['id_product'] ?? 0);
            if ($idProduct > 0 && in_array($idProduct, $limitedIds, true)) {
                $quantities[(string) $idProduct] = (int) ($product['cart_quantity'] ?? 0);
            }
        }

        return $quantities;
    }

    protected function sanitizeLimit($limit)
    {
        $limit = (int) $limit;

        return max(1, $limit);
    }

    protected function invalidateLimitedProductCache()
    {
        $this->limitedProductIds = null;
        $this->limitedProductLimits = null;
    }

    protected function ensureDbSchema()
    {
        if ($this->schemaEnsured) {
            return true;
        }

        $created = Db::getInstance()->execute(
            'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'limitoneproduct` (
                `id_limitoneproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_product` INT UNSIGNED NOT NULL,
                `max_quantity_per_order` INT UNSIGNED NOT NULL DEFAULT 1,
                `created_at` DATETIME NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id_limitoneproduct`),
                UNIQUE KEY `uniq_limitoneproduct_product` (`id_product`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;'
        );

        if (!$created) {
            return false;
        }

        $hasLimitColumn = (bool) Db::getInstance()->executeS(
            'SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'limitoneproduct` LIKE "max_quantity_per_order"'
        );

        if (!$hasLimitColumn) {
            $altered = Db::getInstance()->execute(
                'ALTER TABLE `' . _DB_PREFIX_ . 'limitoneproduct`
                ADD COLUMN `max_quantity_per_order` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `id_product`'
            );

            if (!$altered) {
                return false;
            }
        }

        Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . 'limitoneproduct`
            SET `max_quantity_per_order` = 1
            WHERE `max_quantity_per_order` IS NULL OR `max_quantity_per_order` < 1'
        );

        $this->schemaEnsured = true;

        return true;
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
