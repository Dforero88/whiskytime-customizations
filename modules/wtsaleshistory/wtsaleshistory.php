<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class WtSalesHistory extends Module
{
    const TABLE_MONTHLY = 'wtsaleshistory_monthly';
    const CONFIG_LAST_REFRESH = 'WTSALESHISTORY_LAST_REFRESH';
    const CONFIG_STATE_IDS = 'WTSALESHISTORY_STATE_IDS';
    const ADMIN_CLASS_NAME = 'AdminWtSalesHistory';
    const LEGACY_SOURCE = 'legacy';
    const PRESTASHOP_SOURCE = 'prestashop';

    public function __construct()
    {
        $this->name = 'wtsaleshistory';
        $this->tab = 'analytics_stats';
        $this->version = '1.0.0';
        $this->author = 'OpenAI';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Courbes de ventes');
        $this->description = $this->l('Affiche des courbes mensuelles de ventes avec historique legacy et données Prestashop.');
    }

    public function install()
    {
        return parent::install()
            && $this->installDatabase()
            && $this->installTab()
            && $this->initializeTrackedStateIds()
            && $this->importLegacyData()
            && $this->refreshPrestashopData();
    }

    public function uninstall()
    {
        Configuration::deleteByName(self::CONFIG_LAST_REFRESH);
        Configuration::deleteByName(self::CONFIG_STATE_IDS);

        return $this->uninstallTab()
            && $this->uninstallDatabase()
            && parent::uninstall();
    }

    public function getContent()
    {
        $link = $this->context->link->getAdminLink(self::ADMIN_CLASS_NAME);

        return sprintf(
            '<div class="panel"><p>%s</p><p><a class="btn btn-primary" href="%s">%s</a></p></div>',
            $this->l('Ce module dispose d\'une page dédiée dans le back-office.'),
            htmlspecialchars($link, ENT_QUOTES, 'UTF-8'),
            $this->l('Ouvrir Courbes de ventes')
        );
    }

    public function refreshPrestashopData()
    {
        $stateIds = $this->getTrackedStateIds();
        if (empty($stateIds)) {
            return false;
        }

        $db = Db::getInstance();
        $db->delete(self::TABLE_MONTHLY, "source = '" . pSQL(self::PRESTASHOP_SOURCE) . "'");

        $sql = sprintf(
            'SELECT YEAR(o.date_add) AS year_num, MONTH(o.date_add) AS month_num, COUNT(o.id_order) AS order_count, SUM(o.total_paid) AS total_sales
             FROM `%1$sorders` o
             WHERE o.current_state IN (%2$s)
             GROUP BY YEAR(o.date_add), MONTH(o.date_add)
             ORDER BY YEAR(o.date_add), MONTH(o.date_add)',
            _DB_PREFIX_,
            implode(',', array_map('intval', $stateIds))
        );

        $rows = $db->executeS($sql);
        if ($rows === false) {
            return false;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($rows as $row) {
            $db->insert(self::TABLE_MONTHLY, [
                'year' => (int) $row['year_num'],
                'month' => (int) $row['month_num'],
                'order_count' => (int) $row['order_count'],
                'total_sales' => (float) $row['total_sales'],
                'source' => pSQL(self::PRESTASHOP_SOURCE),
                'updated_at' => pSQL($now),
            ]);
        }

        Configuration::updateValue(self::CONFIG_LAST_REFRESH, $now);

        return true;
    }

    public function getDashboardViewData()
    {
        $merged = $this->getMergedMonthlyData();
        $years = array_keys($merged);
        sort($years);

        $monthLabels = [
            1 => $this->l('Jan'),
            2 => $this->l('Fév'),
            3 => $this->l('Mar'),
            4 => $this->l('Avr'),
            5 => $this->l('Mai'),
            6 => $this->l('Juin'),
            7 => $this->l('Juil'),
            8 => $this->l('Aoû'),
            9 => $this->l('Sep'),
            10 => $this->l('Oct'),
            11 => $this->l('Nov'),
            12 => $this->l('Déc'),
        ];

        $series = [];
        $totals = [
            'order_count' => 0,
            'total_sales' => 0.0,
        ];

        foreach ($years as $year) {
            $orders = [];
            $sales = [];
            for ($month = 1; $month <= 12; $month++) {
                $row = isset($merged[$year][$month]) ? $merged[$year][$month] : ['order_count' => 0, 'total_sales' => 0.0];
                $orders[] = (int) $row['order_count'];
                $sales[] = (float) $row['total_sales'];
                $totals['order_count'] += (int) $row['order_count'];
                $totals['total_sales'] += (float) $row['total_sales'];
            }

            $series[] = [
                'year' => (int) $year,
                'orders' => $orders,
                'sales' => $sales,
            ];
        }

        return [
            'years' => $years,
            'month_labels' => array_values($monthLabels),
            'series' => $series,
            'payload' => [
                'years' => $years,
                'months' => array_values($monthLabels),
                'series' => $series,
                'currencySign' => $this->context->currency ? $this->context->currency->sign : 'CHF',
            ],
            'summary' => [
                'order_count' => $totals['order_count'],
                'total_sales' => $totals['total_sales'],
                'last_refresh' => Configuration::get(self::CONFIG_LAST_REFRESH),
                'legacy_rows' => $this->countRowsBySource(self::LEGACY_SOURCE),
                'prestashop_rows' => $this->countRowsBySource(self::PRESTASHOP_SOURCE),
            ],
        ];
    }

    public function getTrackedStateLabels()
    {
        $ids = $this->getTrackedStateIds();
        if (empty($ids)) {
            return [];
        }

        $sql = sprintf(
            'SELECT osl.name
             FROM `%1$sorder_state_lang` osl
             WHERE osl.id_lang = %2$d
               AND osl.id_order_state IN (%3$s)
             ORDER BY osl.name ASC',
            _DB_PREFIX_,
            (int) $this->context->language->id,
            implode(',', array_map('intval', $ids))
        );

        $rows = Db::getInstance()->executeS($sql);
        if (!$rows) {
            return [];
        }

        return array_map(function ($row) {
            return $row['name'];
        }, $rows);
    }

    protected function installDatabase()
    {
        $sql = sprintf(
            'CREATE TABLE IF NOT EXISTS `%1$s%2$s` (
                `id_wtsaleshistory_monthly` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `year` SMALLINT UNSIGNED NOT NULL,
                `month` TINYINT UNSIGNED NOT NULL,
                `order_count` INT UNSIGNED NOT NULL DEFAULT 0,
                `total_sales` DECIMAL(20,6) NOT NULL DEFAULT 0,
                `source` VARCHAR(32) NOT NULL,
                `updated_at` DATETIME NULL,
                PRIMARY KEY (`id_wtsaleshistory_monthly`),
                UNIQUE KEY `wtsaleshistory_unique_month_source` (`year`, `month`, `source`)
            ) ENGINE=%3$s DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
            _DB_PREFIX_,
            self::TABLE_MONTHLY,
            _MYSQL_ENGINE_
        );

        return Db::getInstance()->execute($sql);
    }

    protected function uninstallDatabase()
    {
        $sql = sprintf(
            'DROP TABLE IF EXISTS `%s%s`',
            _DB_PREFIX_,
            self::TABLE_MONTHLY
        );

        return Db::getInstance()->execute($sql);
    }

    protected function installTab()
    {
        if (Tab::getIdFromClassName(self::ADMIN_CLASS_NAME)) {
            return true;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = self::ADMIN_CLASS_NAME;
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminStats');
        $tab->module = $this->name;

        foreach (Language::getLanguages(false) as $language) {
            $tab->name[(int) $language['id_lang']] = $this->l('Courbes de ventes');
        }

        return $tab->add();
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

    protected function importLegacyData()
    {
        $path = $this->getLocalPath() . 'data/legacy_monthly_sales.csv';
        if (!is_file($path)) {
            return false;
        }

        $db = Db::getInstance();
        $db->delete(self::TABLE_MONTHLY, "source = '" . pSQL(self::LEGACY_SOURCE) . "'");

        $handle = fopen($path, 'r');
        if ($handle === false) {
            return false;
        }

        $header = fgetcsv($handle, 0, ';');
        if ($header === false) {
            fclose($handle);
            return false;
        }

        $now = date('Y-m-d H:i:s');
        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (count($row) < 3 || empty($row[0])) {
                continue;
            }

            list($year, $month) = explode('-', trim($row[0]));
            $db->insert(self::TABLE_MONTHLY, [
                'year' => (int) $year,
                'month' => (int) $month,
                'order_count' => (int) $row[2],
                'total_sales' => (float) $row[1],
                'source' => pSQL(self::LEGACY_SOURCE),
                'updated_at' => pSQL($now),
            ]);
        }

        fclose($handle);

        return true;
    }

    protected function initializeTrackedStateIds()
    {
        $ids = $this->resolveTrackedStateIds();
        if (empty($ids)) {
            return false;
        }

        Configuration::updateValue(self::CONFIG_STATE_IDS, implode(',', $ids));

        return true;
    }

    protected function resolveTrackedStateIds()
    {
        $targets = [
            'Paiement accepté',
            'Paiement accepte',
            'Payment accepted',
            'Expédié',
            'Expedie',
            'Shipped',
            'Livré',
            'Livre',
            'Delivered',
        ];

        $quotedTargets = array_map(function ($value) {
            return "'" . pSQL($value) . "'";
        }, $targets);

        $sql = sprintf(
            'SELECT DISTINCT os.id_order_state
             FROM `%1$sorder_state` os
             INNER JOIN `%1$sorder_state_lang` osl
                ON osl.id_order_state = os.id_order_state
             WHERE osl.name IN (%2$s)
             ORDER BY os.id_order_state ASC',
            _DB_PREFIX_,
            implode(',', $quotedTargets)
        );

        $rows = Db::getInstance()->executeS($sql);
        if (!empty($rows)) {
            return array_map(function ($row) {
                return (int) $row['id_order_state'];
            }, $rows);
        }

        $fallbackSql = sprintf(
            'SELECT id_order_state
             FROM `%sorder_state`
             WHERE paid = 1 OR shipped = 1 OR delivery = 1
             ORDER BY id_order_state ASC',
            _DB_PREFIX_
        );

        $fallbackRows = Db::getInstance()->executeS($fallbackSql);
        if (empty($fallbackRows)) {
            return [];
        }

        return array_map(function ($row) {
            return (int) $row['id_order_state'];
        }, $fallbackRows);
    }

    protected function getTrackedStateIds()
    {
        $raw = (string) Configuration::get(self::CONFIG_STATE_IDS);
        if ($raw === '') {
            $ids = $this->resolveTrackedStateIds();
            if (!empty($ids)) {
                Configuration::updateValue(self::CONFIG_STATE_IDS, implode(',', $ids));
            }

            return $ids;
        }

        $ids = array_filter(array_map('intval', explode(',', $raw)));

        return array_values(array_unique($ids));
    }

    protected function getMergedMonthlyData()
    {
        $sql = sprintf(
            'SELECT year, month, order_count, total_sales, source
             FROM `%s%s`
             ORDER BY year ASC, month ASC, source ASC',
            _DB_PREFIX_,
            self::TABLE_MONTHLY
        );

        $rows = Db::getInstance()->executeS($sql);
        $merged = [];

        foreach ($rows as $row) {
            $year = (int) $row['year'];
            $month = (int) $row['month'];
            $source = $row['source'];

            if (!isset($merged[$year])) {
                $merged[$year] = [];
            }

            if (!isset($merged[$year][$month])) {
                $merged[$year][$month] = [
                    'order_count' => (int) $row['order_count'],
                    'total_sales' => (float) $row['total_sales'],
                    'source' => $source,
                ];
                continue;
            }

            if ($source === self::PRESTASHOP_SOURCE) {
                $merged[$year][$month] = [
                    'order_count' => (int) $row['order_count'],
                    'total_sales' => (float) $row['total_sales'],
                    'source' => $source,
                ];
            }
        }

        return $merged;
    }

    protected function countRowsBySource($source)
    {
        $sql = sprintf(
            'SELECT COUNT(*) FROM `%s%s` WHERE source = "%s"',
            _DB_PREFIX_,
            self::TABLE_MONTHLY,
            pSQL($source)
        );

        return (int) Db::getInstance()->getValue($sql);
    }
}
