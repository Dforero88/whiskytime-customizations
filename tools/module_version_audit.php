<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    header('Content-Type: text/plain; charset=UTF-8');
}

/**
 * Lean utility to compare installed module versions stored in DB
 * with the versions currently present on disk.
 *
 * Usage (CLI):
 *   php tools/module_version_audit.php --root=/path/to/prestashop
 *   php tools/module_version_audit.php --root=/path/to/prestashop --mismatch-only=1
 *   php tools/module_version_audit.php --root=/path/to/prestashop --module=ps_linklist --sql=1
 *
 * Usage (HTTP):
 *   /tools/module_version_audit.php?mismatch_only=1
 *   /tools/module_version_audit.php?module=ps_linklist&sql=1
 */

function readOption(string $name, $default = null)
{
    if (PHP_SAPI === 'cli') {
        static $cliOptions = null;
        if ($cliOptions === null) {
            $cliOptions = getopt('', [
                'root::',
                'module::',
                'sql::',
                'mismatch-only::',
            ]);
        }

        return $cliOptions[$name] ?? $default;
    }

    $queryName = str_replace('-', '_', $name);

    return $_GET[$queryName] ?? $default;
}

function toBool($value): bool
{
    if (is_bool($value)) {
        return $value;
    }

    return in_array((string) $value, ['1', 'true', 'yes', 'on'], true);
}

function findPrestashopRoot(?string $explicitRoot = null): ?string
{
    $candidates = [];

    if ($explicitRoot) {
        $candidates[] = $explicitRoot;
    }

    $candidates[] = dirname(__DIR__, 2);
    $candidates[] = dirname(__DIR__);
    $candidates[] = getcwd();

    foreach ($candidates as $candidate) {
        if (!$candidate) {
            continue;
        }

        $candidate = rtrim((string) $candidate, '/');
        if (is_file($candidate . '/config/config.inc.php')) {
            return $candidate;
        }
    }

    return null;
}

function fail(string $message): void
{
    echo 'ERROR: ' . $message . PHP_EOL;
    exit(1);
}

function detectDiskVersion(string $moduleDir, string $moduleName): array
{
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

function buildStatus(array $row): string
{
    if ($row['disk_version'] === null) {
        return 'disk-version-missing';
    }

    if ($row['db_version'] === $row['disk_version']) {
        return 'ok';
    }

    return 'mismatch';
}

function printRows(array $rows, bool $mismatchOnly): void
{
    $filteredRows = array_values(array_filter($rows, static function (array $row) use ($mismatchOnly): bool {
        return !$mismatchOnly || $row['status'] !== 'ok';
    }));

    if (!$filteredRows) {
        echo "No modules to display." . PHP_EOL;
        return;
    }

    $headers = ['module', 'db_version', 'disk_version', 'status', 'source'];
    $widths = array_fill_keys($headers, 0);

    foreach ($headers as $header) {
        $widths[$header] = strlen($header);
    }

    foreach ($filteredRows as $row) {
        foreach ($headers as $header) {
            $widths[$header] = max($widths[$header], strlen((string) $row[$header]));
        }
    }

    foreach ($headers as $header) {
        echo str_pad($header, $widths[$header] + 2);
    }
    echo PHP_EOL;

    foreach ($headers as $header) {
        echo str_repeat('-', $widths[$header]) . '  ';
    }
    echo PHP_EOL;

    foreach ($filteredRows as $row) {
        foreach ($headers as $header) {
            echo str_pad((string) $row[$header], $widths[$header] + 2);
        }
        echo PHP_EOL;
    }
}

$root = findPrestashopRoot((string) readOption('root', ''));
if ($root === null) {
    fail('Unable to locate PrestaShop root. Use --root=/path/to/prestashop.');
}

require_once $root . '/config/config.inc.php';

if (!defined('_DB_PREFIX_')) {
    fail('PrestaShop bootstrap succeeded, but _DB_PREFIX_ is not defined.');
}

$moduleFilter = trim((string) readOption('module', ''));
$mismatchOnly = toBool(readOption('mismatch-only', '0'));
$showSql = toBool(readOption('sql', '0'));

$sql = sprintf(
    'SELECT `id_module`, `name`, `version`, `active`
     FROM `%smodule`
     ORDER BY `name` ASC',
    pSQL(_DB_PREFIX_)
);

$modules = Db::getInstance()->executeS($sql);
if (!is_array($modules)) {
    fail('Unable to fetch installed modules from database.');
}

$rows = [];
foreach ($modules as $module) {
    $moduleName = (string) $module['name'];
    if ($moduleFilter !== '' && $moduleName !== $moduleFilter) {
        continue;
    }

    $moduleDir = rtrim(_PS_MODULE_DIR_, '/') . '/' . $moduleName;
    $diskData = detectDiskVersion($moduleDir, $moduleName);

    $row = [
        'module' => $moduleName,
        'db_version' => (string) $module['version'],
        'disk_version' => $diskData['version'],
        'source' => $diskData['source'],
    ];
    $row['status'] = buildStatus($row);

    $rows[] = $row;
}

if ($moduleFilter !== '' && !$rows) {
    fail(sprintf('Module "%s" not found in installed modules table.', $moduleFilter));
}

printRows($rows, $mismatchOnly);

if ($showSql) {
    echo PHP_EOL . 'SQL:' . PHP_EOL;
    foreach ($rows as $row) {
        if ($row['status'] !== 'mismatch') {
            continue;
        }

        echo sprintf(
            "UPDATE `%smodule` SET `version` = '%s' WHERE `name` = '%s';",
            pSQL(_DB_PREFIX_),
            pSQL((string) $row['disk_version']),
            pSQL($row['module'])
        ) . PHP_EOL;
    }
}
