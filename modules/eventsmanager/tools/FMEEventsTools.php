<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file.
 * You are not authorized to modify, copy or redistribute this file.
 * Permissions are reserved by FME Modules.
 *
 *  @author    FMM Modules
 *  @copyright FME Modules 2024
 *  @license   Single domain
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
class FMEEventsTools
{
    public function getCurrentURL()
    {
        $url = 'http';

        if (isset($_SERVER['HTTPS']) == 'on') {
            $url .= 's';
        }

        $url .= '://';
        if ($_SERVER['SERVER_PORT'] != '80') {
            $url .= $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        } else {
            $url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }

        if (Tools::getValue('n')) {
            $url = $url . (!strstr($url, '?') ? '?' : '&amp;') . 'n=' . (int) Tools::getValue('n');
        }

        return $url;
    }

    public function getEventLink($event_id)
    {
        $link = Context::getContext()->link;
        if (Configuration::get('PS_REWRITING_SETTINGS') == 1) {
            $_link = $link->getModuleLink('eventsmanager', 'events', ['event_id' => $event_id]);
            $l = $_link;
        } else {
            $_link = $link->getModuleLink('eventsmanager', 'events', ['event_id' => $event_id]);
            $l = $_link;
        }

        return $l;
    }

    public function getNewrandCode($length)
    {
        if ($length > 0) {
            $rand_id = '';
            for ($i = 1; $i <= $length; ++$i) {
                mt_srand((float) microtime() * 1000000);
                $num = mt_rand(1, 36);
                $rand_id .= $this->assignRandValue($num);
            }
        }

        return $rand_id;
    }

    public function assignRandValue($num)
    {
        switch ($num) {
            case '1':
                $rand_value = 'a';
                break;
            case '2':
                $rand_value = 'b';
                break;
            case '3':
                $rand_value = 'c';
                break;
            case '4':
                $rand_value = 'd';
                break;
            case '5':
                $rand_value = 'e';
                break;
            case '6':
                $rand_value = 'f';
                break;
            case '7':
                $rand_value = 'g';
                break;
            case '8':
                $rand_value = 'h';
                break;
            case '9':
                $rand_value = 'i';
                break;
            case '10':
                $rand_value = 'j';
                break;
            case '11':
                $rand_value = 'k';
                break;
            case '12':
                $rand_value = 'z';
                break;
            case '13':
                $rand_value = 'm';
                break;
            case '14':
                $rand_value = 'n';
                break;
            case '15':
                $rand_value = 'o';
                break;
            case '16':
                $rand_value = 'p';
                break;
            case '17':
                $rand_value = 'q';
                break;
            case '18':
                $rand_value = 'r';
                break;
            case '19':
                $rand_value = 's';
                break;
            case '20':
                $rand_value = 't';
                break;
            case '21':
                $rand_value = 'u';
                break;
            case '22':
                $rand_value = 'v';
                break;
            case '23':
                $rand_value = 'w';
                break;
            case '24':
                $rand_value = 'x';
                break;
            case '25':
                $rand_value = 'y';
                break;
            case '26':
                $rand_value = 'z';
                break;
            case '27':
                $rand_value = '0';
                break;
            case '28':
                $rand_value = '1';
                break;
            case '29':
                $rand_value = '2';
                break;
            case '30':
                $rand_value = '3';
                break;
            case '31':
                $rand_value = '4';
                break;
            case '32':
                $rand_value = '5';
                break;
            case '33':
                $rand_value = '6';
                break;
            case '34':
                $rand_value = '7';
                break;
            case '35':
                $rand_value = '8';
                break;
            case '36':
                $rand_value = '9';
                break;
        }

        return $rand_value;
    }

    public static function name2Id($class_name = null)
    {
        switch ($class_name) {
            case 'star-1':
                return 1;

            case 'star-2':
                return 2;

            case 'star-3':
                return 3;

            case 'star-4':
                return 4;

            case 'star-5':
                return 5;

            default:
                return 0;
        }
    }

    public static function id2Name($id = null)
    {
        switch ($id) {
            case 1:
                return 'rate-1';

            case 2:
                return 'rate-2';

            case 3:
                return 'rate-3';

            case 4:
                return 'rate-4';

            case 5:
                return 'rate-5';

            default:
                return null;
        }
    }

    public function getClientIp()
    {
        $ipaddress = '';
        if ($_SERVER['REMOTE_ADDR']) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    public function getSalash()
    {
        $os = (
            (strpos(Tools::strtolower(PHP_OS), 'win') === 0)
            || (strpos(Tools::strtolower(PHP_OS), 'cygwin') !== false)
        ) ? 'win32' : 'unix';
        switch ($os) {
            case 'win32':
                define('SLASH', '/');
                break;
            default:
                define('SLASH', '\\');
                break;
        }
    }
}
