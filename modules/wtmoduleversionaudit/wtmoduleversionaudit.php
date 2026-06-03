<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class WtModuleVersionAudit extends Module
{
    public function __construct()
    {
        $this->name = 'wtmoduleversionaudit';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'David Forero';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('WT Module Version Audit');
        $this->description = $this->l('Compare installed module versions in database with module versions on disk and generate SQL for mismatches.');
        $this->ps_versions_compliancy = ['min' => '1.7.7.0', 'max' => _PS_VERSION_];
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
        $moduleFilter = trim((string) Tools::getValue('WTMVA_MODULE', ''));
        $mismatchOnly = (bool) Tools::getValue('WTMVA_MISMATCH_ONLY', 0);
        $sqlModule = trim((string) Tools::getValue('WTMVA_SQL_MODULE', ''));

        $rows = $this->getAuditRows($moduleFilter);
        $prefix = _DB_PREFIX_;

        $html = '';
        $html .= $this->displayInformation($this->l('This utility compares module versions stored in the database with versions detected on disk. It uses the active database prefix automatically.'));
        $html .= $this->renderFilterForm($moduleFilter, $mismatchOnly);
        $html .= $this->renderTable($rows, $mismatchOnly);

        if ($sqlModule !== '') {
            $sql = $this->buildSqlForModule($sqlModule, $rows);
            if ($sql !== null) {
                $html .= $this->renderSqlBlock($sqlModule, $sql, $prefix);
            } else {
                $html .= $this->displayError($this->l('No mismatch found for the selected module.'));
            }
        }

        return $html;
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function getAuditRows(string $moduleFilter = ''): array
    {
        $sql = sprintf(
            'SELECT `id_module`, `name`, `version`, `active`
             FROM `%smodule`
             ORDER BY `name` ASC',
            pSQL(_DB_PREFIX_)
        );

        $modules = Db::getInstance()->executeS($sql);
        if (!is_array($modules)) {
            return [];
        }

        $rows = [];
        foreach ($modules as $module) {
            $moduleName = (string) $module['name'];
            if ($moduleFilter !== '' && $moduleName !== $moduleFilter) {
                continue;
            }

            $diskData = $this->detectDiskVersion($moduleName);
            $row = [
                'module' => $moduleName,
                'db_version' => (string) $module['version'],
                'disk_version' => $diskData['version'],
                'source' => $diskData['source'],
            ];
            $row['status'] = $this->buildStatus($row);
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @return array{version: string|null, source: string}
     */
    private function detectDiskVersion(string $moduleName): array
    {
        $moduleDir = rtrim(_PS_MODULE_DIR_, '/') . '/' . $moduleName;
        $configXmlPath = $moduleDir . '/config.xml';

        if (is_file($configXmlPath)) {
            $xmlContent = file_get_contents($configXmlPath);
            if ($xmlContent !== false && preg_match('/<version><!\[CDATA\[(.*?)\]\]><\/version>/s', $xmlContent, $match)) {
                return ['version' => trim($match[1]), 'source' => 'config.xml'];
            }

            if ($xmlContent !== false && preg_match('/<version>(.*?)<\/version>/s', $xmlContent, $match)) {
                return ['version' => trim(strip_tags($match[1])), 'source' => 'config.xml'];
            }
        }

        $mainPhpPath = $moduleDir . '/' . $moduleName . '.php';
        if (is_file($mainPhpPath)) {
            $phpContent = file_get_contents($mainPhpPath);
            if ($phpContent !== false) {
                if (preg_match('/\$this->version\s*=\s*[\'"]([^\'"]+)[\'"]\s*;/', $phpContent, $match)) {
                    return ['version' => trim($match[1]), 'source' => basename($mainPhpPath)];
                }

                if (preg_match('/const\s+VERSION\s*=\s*[\'"]([^\'"]+)[\'"]\s*;/', $phpContent, $match)) {
                    return ['version' => trim($match[1]), 'source' => basename($mainPhpPath)];
                }
            }
        }

        return ['version' => null, 'source' => 'unknown'];
    }

    /**
     * @param array<string, string|null> $row
     */
    private function buildStatus(array $row): string
    {
        if ($row['disk_version'] === null) {
            return 'disk-version-missing';
        }

        if ($row['db_version'] === $row['disk_version']) {
            return 'ok';
        }

        return 'mismatch';
    }

    private function renderFilterForm(string $moduleFilter, bool $mismatchOnly): string
    {
        $token = $this->getAdminModulesToken();
        $action = AdminController::$currentIndex . '&configure=' . $this->name;
        if ($token !== '') {
            $action .= '&token=' . $token;
        }

        $html = '<form method="get" action="' . htmlspecialchars($action, ENT_QUOTES, 'UTF-8') . '" class="panel">';
        $html .= '<input type="hidden" name="configure" value="' . htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8') . '">';
        if ($token !== '') {
            $html .= '<input type="hidden" name="token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
        }
        $html .= '<div class="panel-heading">' . htmlspecialchars($this->l('Filters'), ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="form-wrapper">';
        $html .= '<div class="form-group">';
        $html .= '<label class="control-label">' . htmlspecialchars($this->l('Module name (optional exact match)'), ENT_QUOTES, 'UTF-8') . '</label>';
        $html .= '<input type="text" class="form-control" name="WTMVA_MODULE" value="' . htmlspecialchars($moduleFilter, ENT_QUOTES, 'UTF-8') . '">';
        $html .= '</div>';
        $html .= '<div class="checkbox">';
        $html .= '<label><input type="checkbox" name="WTMVA_MISMATCH_ONLY" value="1"' . ($mismatchOnly ? ' checked' : '') . '> ' . htmlspecialchars($this->l('Show only mismatches'), ENT_QUOTES, 'UTF-8') . '</label>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="panel-footer">';
        $html .= '<button type="submit" class="btn btn-primary pull-right">' . htmlspecialchars($this->l('Run audit'), ENT_QUOTES, 'UTF-8') . '</button>';
        $html .= '<div class="clearfix"></div>';
        $html .= '</div>';
        $html .= '</form>';

        return $html;
    }

    /**
     * @param array<int, array<string, string|null>> $rows
     */
    private function renderTable(array $rows, bool $mismatchOnly): string
    {
        if (empty($rows)) {
            return $this->displayWarning($this->l('No installed modules found for the current filter.'));
        }

        $html = '<div class="panel">';
        $html .= '<div class="panel-heading">' . htmlspecialchars($this->l('Audit results'), ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="table-responsive-row clearfix">';
        $html .= '<table class="table">';
        $html .= '<thead><tr>';
        $html .= '<th>' . htmlspecialchars($this->l('Module'), ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th>' . htmlspecialchars($this->l('DB version'), ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th>' . htmlspecialchars($this->l('Disk version'), ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th>' . htmlspecialchars($this->l('Status'), ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th>' . htmlspecialchars($this->l('Source'), ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '<th>' . htmlspecialchars($this->l('Action'), ENT_QUOTES, 'UTF-8') . '</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $row) {
            if ($mismatchOnly && $row['status'] === 'ok') {
                continue;
            }

            $statusClass = $row['status'] === 'ok' ? 'success' : ($row['status'] === 'mismatch' ? 'warning' : 'danger');
            $sqlUrl = $this->buildConfigUrl([
                'WTMVA_MODULE' => Tools::getValue('WTMVA_MODULE', ''),
                'WTMVA_MISMATCH_ONLY' => Tools::getValue('WTMVA_MISMATCH_ONLY', 0),
                'WTMVA_SQL_MODULE' => $row['module'],
            ]);

            $html .= '<tr>';
            $html .= '<td><strong>' . htmlspecialchars((string) $row['module'], ENT_QUOTES, 'UTF-8') . '</strong></td>';
            $html .= '<td>' . htmlspecialchars((string) $row['db_version'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>' . htmlspecialchars((string) ($row['disk_version'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td><span class="label label-' . $statusClass . '">' . htmlspecialchars((string) $row['status'], ENT_QUOTES, 'UTF-8') . '</span></td>';
            $html .= '<td>' . htmlspecialchars((string) $row['source'], ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td>';
            if ($row['status'] === 'mismatch') {
                $html .= '<a class="btn btn-default" href="' . htmlspecialchars($sqlUrl, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($this->l('Generate SQL'), ENT_QUOTES, 'UTF-8') . '</a>';
            } else {
                $html .= '-';
            }
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div></div>';

        return $html;
    }

    /**
     * @param array<int, array<string, string|null>> $rows
     */
    private function buildSqlForModule(string $moduleName, array $rows): ?string
    {
        foreach ($rows as $row) {
            if ($row['module'] !== $moduleName || $row['status'] !== 'mismatch' || empty($row['disk_version'])) {
                continue;
            }

            return sprintf(
                "UPDATE `%smodule` SET `version` = '%s' WHERE `name` = '%s';",
                pSQL(_DB_PREFIX_),
                pSQL((string) $row['disk_version']),
                pSQL($moduleName)
            );
        }

        return null;
    }

    private function renderSqlBlock(string $moduleName, string $sql, string $prefix): string
    {
        $html = '<div class="panel">';
        $html .= '<div class="panel-heading">' . htmlspecialchars($this->l('SQL for selected mismatch'), ENT_QUOTES, 'UTF-8') . '</div>';
        $html .= '<div class="alert alert-info">';
        $html .= htmlspecialchars(sprintf($this->l('Detected database prefix: %s'), $prefix), ENT_QUOTES, 'UTF-8');
        $html .= '</div>';
        $html .= '<p><strong>' . htmlspecialchars($moduleName, ENT_QUOTES, 'UTF-8') . '</strong></p>';
        $html .= '<pre style="white-space: pre-wrap;">' . htmlspecialchars($sql, ENT_QUOTES, 'UTF-8') . '</pre>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function buildConfigUrl(array $params): string
    {
        $base = AdminController::$currentIndex . '&configure=' . $this->name;
        $token = $this->getAdminModulesToken();
        if ($token !== '') {
            $base .= '&token=' . $token;
        }

        foreach ($params as $key => $value) {
            if ($value === '' || $value === null) {
                continue;
            }

            $base .= '&' . rawurlencode((string) $key) . '=' . rawurlencode((string) $value);
        }

        return $base;
    }

    private function getAdminModulesToken(): string
    {
        $employee = Context::getContext()->employee;
        if (!$employee || empty($employee->id)) {
            return '';
        }

        return Tools::getAdminTokenLite('AdminModules');
    }
}
