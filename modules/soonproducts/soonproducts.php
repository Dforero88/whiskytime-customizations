<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Soonproducts extends Module
{
    /** @var int[]|null */
    protected $soonProductIds = null;

    public function __construct()
    {
        $this->name = 'soonproducts';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'David Custom';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Soon products');
        $this->description = $this->l('Flag products as soon available and adapt front display.');
    }

    public function install()
    {
        return parent::install()
            && $this->installDb()
            && $this->registerHook('actionPresentProduct')
            && $this->registerHook('actionPresentProductListing');
    }

    public function uninstall()
    {
        return $this->uninstallDb() && parent::uninstall();
    }

    protected function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'soonproducts` (
            `id_soonproduct` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product` INT UNSIGNED NOT NULL,
            `created_at` DATETIME NOT NULL,
            `updated_at` DATETIME NOT NULL,
            PRIMARY KEY (`id_soonproduct`),
            UNIQUE KEY `uniq_soonproduct_product` (`id_product`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8mb4;';

        return Db::getInstance()->execute($sql);
    }

    protected function uninstallDb()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'soonproducts`');
    }

    public function getContent()
    {
        $output = '';
        $this->cleanupSoonProducts();

        if (Tools::isSubmit('submitSoonproductsAdd')) {
            if (!$this->isValidToken()) {
                $output .= $this->displayError($this->l('Invalid security token.'));
            } else {
                $idProduct = (int) Tools::getValue('id_product');
                if ($idProduct <= 0) {
                    $output .= $this->displayError($this->l('Invalid product.'));
                } elseif ($this->addSoonProduct($idProduct)) {
                    $output .= $this->displayConfirmation($this->l('Product added to soon list.'));
                } else {
                    $output .= $this->displayError($this->l('Unable to add this product.'));
                }
            }
        }

        if (Tools::isSubmit('submitSoonproductsRemove')) {
            if (!$this->isValidToken()) {
                $output .= $this->displayError($this->l('Invalid security token.'));
            } else {
                $idProduct = (int) Tools::getValue('id_product');
                if ($idProduct <= 0) {
                    $output .= $this->displayError($this->l('Invalid product.'));
                } elseif ($this->removeSoonProduct($idProduct)) {
                    $output .= $this->displayConfirmation($this->l('Product removed from soon list.'));
                } else {
                    $output .= $this->displayError($this->l('Unable to remove this product.'));
                }
            }
        }

        $searchTerm = trim((string) Tools::getValue('soonproducts_search', ''));
        $searchResults = [];
        if ($searchTerm !== '') {
            $searchResults = $this->searchProducts($searchTerm);
        }

        return $output
            . $this->renderSearchForm($searchTerm)
            . $this->renderSearchResults($searchResults)
            . $this->renderSoonProductsTable();
    }

    public function hookActionPresentProduct($params)
    {
        $this->decoratePresentedProduct($params);
    }

    public function hookActionPresentProductListing($params)
    {
        $this->decoratePresentedProduct($params);
    }

    protected function decoratePresentedProduct(array $params)
    {
        if (empty($params['presentedProduct'])) {
            return;
        }

        $presentedProduct = $params['presentedProduct'];
        $idProduct = (int) ($presentedProduct['id_product'] ?? $presentedProduct['id'] ?? 0);
        if ($idProduct <= 0 || !$this->isSoonProduct($idProduct)) {
            return;
        }

        if (StockAvailable::getQuantityAvailableByProduct($idProduct) > 0) {
            return;
        }

        $soonLabel = $this->trans('Bientôt dispo', [], 'Modules.Soonproducts.Shop');
        $presentedProduct->offsetSet('soonproducts_is_soon', true, true);
        $presentedProduct->offsetSet('show_availability', true, true);
        $presentedProduct->offsetSet('availability_message', $soonLabel, true);

        $flags = $presentedProduct['flags'] ?? [];
        $hasSoonFlag = false;
        $filteredFlags = [];

        foreach ($flags as $flag) {
            if (!empty($flag['type']) && $flag['type'] === 'new') {
                continue;
            }

            if (!empty($flag['type']) && $flag['type'] === 'out_of_stock') {
                $flag['label'] = $soonLabel;
                $hasSoonFlag = true;
            }

            $filteredFlags[] = $flag;
        }

        if (!$hasSoonFlag) {
            $filteredFlags[] = [
                'type' => 'out_of_stock',
                'label' => $soonLabel,
            ];
        }

        $presentedProduct->offsetSet('flags', $filteredFlags, true);
    }

    public function isSoonProduct($idProduct)
    {
        $idProduct = (int) $idProduct;
        if ($idProduct <= 0) {
            return false;
        }

        return in_array($idProduct, $this->getSoonProductIds(), true);
    }

    public function getSoonProductIds()
    {
        if ($this->soonProductIds !== null) {
            return $this->soonProductIds;
        }

        $this->cleanupSoonProducts();
        $rows = Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'soonproducts`');
        $this->soonProductIds = array_map('intval', array_column($rows ?: [], 'id_product'));

        return $this->soonProductIds;
    }

    protected function cleanupSoonProducts()
    {
        $rows = Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'soonproducts`');
        if (!$rows) {
            $this->soonProductIds = [];

            return;
        }

        foreach ($rows as $row) {
            $idProduct = (int) $row['id_product'];
            if ($idProduct > 0 && StockAvailable::getQuantityAvailableByProduct($idProduct) > 0) {
                Db::getInstance()->delete('soonproducts', '`id_product` = ' . $idProduct);
            }
        }

        $rows = Db::getInstance()->executeS('SELECT `id_product` FROM `' . _DB_PREFIX_ . 'soonproducts`');
        $this->soonProductIds = array_map('intval', array_column($rows ?: [], 'id_product'));
    }

    protected function addSoonProduct($idProduct)
    {
        $idProduct = (int) $idProduct;
        if ($idProduct <= 0) {
            return false;
        }

        if (StockAvailable::getQuantityAvailableByProduct($idProduct) > 0) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        $exists = (bool) Db::getInstance()->getValue(
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'soonproducts` WHERE `id_product` = ' . $idProduct
        );

        if ($exists) {
            $result = Db::getInstance()->update('soonproducts', ['updated_at' => pSQL($now)], '`id_product` = ' . $idProduct);
        } else {
            $result = Db::getInstance()->insert('soonproducts', [
                'id_product' => $idProduct,
                'created_at' => pSQL($now),
                'updated_at' => pSQL($now),
            ]);
        }

        $this->soonProductIds = null;

        return (bool) $result;
    }

    protected function removeSoonProduct($idProduct)
    {
        $idProduct = (int) $idProduct;
        if ($idProduct <= 0) {
            return false;
        }

        $this->soonProductIds = null;

        return (bool) Db::getInstance()->delete('soonproducts', '`id_product` = ' . $idProduct);
    }

    protected function searchProducts($query)
    {
        return Product::searchByName((int) $this->context->language->id, $query, null, 20) ?: [];
    }

    protected function getSoonProductsData()
    {
        $rows = Db::getInstance()->executeS(
            'SELECT sp.`id_product`, pl.`name`
            FROM `' . _DB_PREFIX_ . 'soonproducts` sp
            INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl
                ON (pl.`id_product` = sp.`id_product` AND pl.`id_lang` = ' . (int) $this->context->language->id . ')
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
            <h3>' . $this->l('Ajouter un produit bientot dispo') . '</h3>
            <form method="post" action="' . $action . '">
                <input type="hidden" name="soonproducts_token" value="' . $token . '">
                <div class="form-group">
                    <label>' . $this->l('Rechercher un produit') . '</label>
                    <input type="text" class="form-control" name="soonproducts_search" value="' . $searchTerm . '" placeholder="' . $this->l('Nom, reference...') . '">
                </div>
                <button type="submit" class="btn btn-primary">' . $this->l('Rechercher') . '</button>
            </form>
        </div>';
    }

    protected function renderSearchResults(array $products)
    {
        if (empty($products)) {
            if (trim((string) Tools::getValue('soonproducts_search', '')) === '') {
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
            $isSoon = $this->isSoonProduct($idProduct);
            $quantity = (int) $product['quantity'];
            $html .= '
                <tr>
                    <td>' . $idProduct . '</td>
                    <td>' . htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . htmlspecialchars((string) $product['reference'], ENT_QUOTES, 'UTF-8') . '</td>
                    <td>' . $quantity . '</td>
                    <td>';

            if ($isSoon) {
                $html .= '<span class="label label-info">' . $this->l('Deja flague') . '</span>';
            } elseif ($quantity > 0) {
                $html .= '<span class="label label-default">' . $this->l('Stock > 0') . '</span>';
            } else {
                $html .= '
                    <form method="post" action="' . $action . '" style="display:inline-block;">
                        <input type="hidden" name="soonproducts_token" value="' . $token . '">
                        <input type="hidden" name="id_product" value="' . $idProduct . '">
                        <button type="submit" name="submitSoonproductsAdd" class="btn btn-default">' . $this->l('Ajouter') . '</button>
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

    protected function renderSoonProductsTable()
    {
        $products = $this->getSoonProductsData();
        $action = htmlspecialchars($this->getModuleConfigUrl(), ENT_QUOTES, 'UTF-8');
        $token = htmlspecialchars(Tools::getAdminTokenLite('AdminModules'), ENT_QUOTES, 'UTF-8');

        $html = '
        <div class="panel">
            <h3>' . $this->l('Produits bientot dispo') . '</h3>';

        if (empty($products)) {
            return $html . '<p>' . $this->l('Aucun produit flague.') . '</p></div>';
        }

        $html .= '
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>' . $this->l('ID') . '</th>
                            <th>' . $this->l('Nom') . '</th>
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
                    <td>' . (int) $product['quantity'] . '</td>
                    <td>
                        <form method="post" action="' . $action . '" style="display:inline-block;">
                            <input type="hidden" name="soonproducts_token" value="' . $token . '">
                            <input type="hidden" name="id_product" value="' . $idProduct . '">
                            <button type="submit" name="submitSoonproductsRemove" class="btn btn-link">' . $this->l('Retirer') . '</button>
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

    protected function isValidToken()
    {
        return Tools::getValue('soonproducts_token') === Tools::getAdminTokenLite('AdminModules');
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
