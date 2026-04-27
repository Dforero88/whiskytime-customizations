<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class ProductWithoutTags extends Module
{
    public function __construct()
    {
        $this->name = 'productwithouttags';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Ton Nom';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Products without tags');
        $this->description = $this->l('Affiche la liste des produits sans tags en français.');
    }

    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function getContent()
    {
        $output = '<h2>Liste des produits sans tags (français)</h2>';

        $products = $this->getProductsWithoutTags();

        if (empty($products)) {
            $output .= '<p>Tous les produits ont au moins un tag en français.</p>';
            return $output;
        }

        $output .= '
        <table class="table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Nom</th>
                    <th>Quantité en stock</th>
                </tr>
            </thead>
            <tbody>
        ';

        foreach ($products as $product) {
            $output .= '
                <tr>
                    <td>' . htmlspecialchars($product['reference']) . '</td>
                    <td>' . htmlspecialchars($product['name']) . '</td>
                    <td>' . (int)$product['quantity'] . '</td>
                </tr>
            ';
        }

        $output .= '
            </tbody>
        </table>';

        return $output;
    }

    private function getProductsWithoutTags()
    {
        $sql = '
            SELECT p.reference, pl.name, sa.quantity
            FROM ' . _DB_PREFIX_ . 'product p
            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl
                ON p.id_product = pl.id_product AND pl.id_lang = 2
            LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa
                ON p.id_product = sa.id_product
            LEFT JOIN ' . _DB_PREFIX_ . 'product_tag pt
                ON p.id_product = pt.id_product
            WHERE pt.id_product IS NULL AND p.reference LIKE "01-%" AND p.active = 1
            ORDER BY sa.quantity DESC
        ';

        return Db::getInstance()->executeS($sql);
    }
}
