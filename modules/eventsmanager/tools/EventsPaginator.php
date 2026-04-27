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
require_once _PS_MODULE_DIR_ . 'eventsmanager/eventsmanager.php';
class EventsPaginator
{
    public $items_per_page;
    public $items_total;
    public $current_page;
    public $num_pages;
    public $mid_range;
    public $low;
    public $limit;
    public $return;
    public $default_ipp;
    public $querystring;
    public $ipp_array;
    public $links;
    public $option;

    public function __construct()
    {
        $this->current_page = 1;
        $this->mid_range = 7;
        $default_ipp = (int) Configuration::get('EVENTS_PER_PAGE');
        $this->default_ipp = ($default_ipp > 0) ? $default_ipp : 10;
        $this->ipp_array = [10, 25, 50, 100, 'All'];
        $ipp = (int) Tools::getValue('ipp');
        $this->items_per_page = ($ipp > 0) ? $ipp : $this->default_ipp;
        // $this->items_per_page = ($ipp === 'All') ? 0 : (int) $ipp;

        $page_val = (int) Tools::getValue('page');
        $this->current_page = ($page_val > 0) ? (int) $page_val : 1;
        $this->low = ($this->current_page <= 0) ? 0 : ($this->current_page - 1) * $this->items_per_page;
        if ($this->current_page <= 0) {
            $this->items_per_page = 0;
        }
        $current_ipp = Tools::getValue('ipp');
        $this->limit = ($current_ipp == 'All') ? 'LIMIT 0, 100' : ' LIMIT ' . $this->low . ', ' . $this->items_per_page;
    }

    public function paginate($default_no_of_rec)
    {
        $module = new EventsManager();

        if (_PS_VERSION_ < 1.6) {
            $this->return .=
            '<div id="event-paginator" style="border-left:2px solid green;padding:
            12px;background:none repeat scroll 0 0 #f1f1f1;">';
        } else {
            $this->return .= '<div id="event-paginator" style="border-top:1px dashed #c4c4c4;padding-top:10px;">';
        }

        $link = Context::getContext()->link;
        $links = $link->getModuleLink('eventsmanager', 'events', []);
        if ($this->default_ipp > 0) {
            $this->default_ipp = $default_no_of_rec;
        }
        if (Tools::getValue('ipp') == 'All') {
            $this->num_pages = 1;
        } else {
            if (!is_numeric($this->items_per_page) || $this->items_per_page <= 0) {
                $this->items_per_page = $this->default_ipp;
            }
            //changed
            $this->num_pages = ceil($this->items_total / $this->items_per_page);

        }

        $page_val = (int) Tools::getValue('page');
        $this->current_page = ($page_val > 0) ? (int) $page_val : 1;
        $prev_page = $this->current_page - 1;
        $next_page = $this->current_page + 1;
        if ($_GET) {
            $args = explode('&', $_SERVER['QUERY_STRING']);
            foreach ($args as $arg) {
                $keyval = explode('=', $arg);
                if ($keyval[0] != 'page' && $keyval[0] != 'ipp') {
                    $this->querystring .= '&' . $arg;
                }
            }
        }
        if ($_POST) {
            foreach ($_POST as $key) {
                if ($key != 'page' && $key != 'ipp') {
                    $this->querystring .= '&$key=$val';
                }
            }
        }
        if ($this->num_pages > 10) {
            $this->return = ($this->current_page > 1 && $this->items_total >= 10) ? '<a class="paginate" href="' .
            $links . '?page=' . $prev_page . '&ipp=' . $this->items_per_page . $this->querystring . '">&laquo; ' .
            $module->l('Previous', 'EventsPaginator') . '</a> ' : '<span class="inactive" href="#">&laquo; ' .
            $module->l('Previous', 'EventsPaginator') . '</span> ';

            $this->start_range = $this->current_page - floor($this->mid_range / 2);
            $this->end_range = $this->current_page + floor($this->mid_range / 2);

            if ($this->start_range <= 0) {
                $this->end_range += abs($this->start_range) + 1;
                $this->start_range = 1;
            } if ($this->end_range > $this->num_pages) {
                $this->start_range -= $this->end_range - $this->num_pages;
                $this->end_range = $this->num_pages;
            }
            $this->range = range($this->start_range, $this->end_range);

            for ($i = 1; $i <= $this->num_pages; ++$i) {
                if ($this->range[0] > 2 && $i == $this->range[0]) {
                    $this->return .= ' ... ';
                }
                // loop through all pages. if first, last, or in range, display
                if ($i == 1 || $i == $this->num_pages || in_array($i, $this->range)) {
                    $this->return .= (
                        $i == $this->current_page
                        && Tools::getValue('page') != 'All'
                    ) ? '<a title="Go to page ' . $i . ' of ' . $this->num_pages .
                    '" class="current btn btn-default" href="#">' . $i .
                    '</a> ' : '<a class="paginate btn btn-default" title="Go to page ' . $i .
                    ' of ' . $this->num_pages . '" href="' . $links . '?page=' . $i .
                    '&ipp=' . $this->items_per_page . $this->querystring . '">' . $i . '</a> ';
                }

                if ($this->range[$this->mid_range - 1] < $this->num_pages - 1
                    && $i == $this->range[$this->mid_range - 1]
                ) {
                    $this->return .= ' ... ';
                }
            }
            $this->return .= (($this->current_page < $this->num_pages && $this->items_total >= 10)
                && (Tools::getValue('page') != 'All')
                && $this->current_page > 0) ? '<a class="paginate btn btn-default" href="' . $links . '?page=' .
            $next_page . '&ipp=' . $this->items_per_page . $this->querystring . '">' .
            $module->l('Next', 'EventsPaginator') . ' &raquo;</a>' : '<span class="inactive" href="#">&raquo; ' .           //changed
            $module->l('Next', 'EventsPaginator') . '</span>\n';
            $this->return .=
            (Tools::getValue('page') == 'All') ? '<a id="all-events" class="current btn btn-default" 
            style="margin-left:10px" href="#">' . $module->l('All', 'EventsPaginator') .
            '</a> ' : '<a class="paginate btn btn-default" style="margin-left:10px" href="' .
            $links . '?page=1&ipp=All' . $this->querystring . '">' . $module->l('All', 'EventsPaginator') . '</a> ';
        } else {
            for ($i = 1; $i <= $this->num_pages; ++$i) {
                $this->return .= ($i == $this->current_page) ?
                '<a class="btn btn-primary mat_btn_details" href="#">' . $i .
                '</a> ' : '<a class="paginate btn btn-default" href="' . $links . '?page=' . $i . '&ipp=' .
                $this->items_per_page . $this->querystring . '">' . $i . '</a> ';
            }
            $this->return .= '<a id="all-events" class="paginate btn btn-default" href="' . $links .
            '?page=1&ipp=All' . $this->querystring . '">' . $module->l('All', 'EventsPaginator') . '</a> ';
        }
        $this->return .= '</div>';
    }

    public function displayItemsPerPage()
    {
        return '';
    }

    public function displayJumpMenu()
    {
        return '';
    }

    public function displayPages()
    {
        return $this->return;
    }
}
