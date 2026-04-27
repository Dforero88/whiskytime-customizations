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
class EventsManagerEventsModuleFrontController extends ModuleFrontController
{
    protected $event;
    protected $show;
    public $useSSL = true;

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $default_theme = (int) Configuration::get('EVENTS_THEME');
        if ($default_theme == 0) {
            $this->context->controller->addCss(__PS_BASE_URI__ .
            'modules/eventsmanager/views/css/calendar_old.css');
        } else {
            $this->context->controller->addCss(__PS_BASE_URI__ .
            'modules/eventsmanager/views/css/calendar.css');
        }
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '<=') == true) {
            $this->context->controller->addCss(__PS_BASE_URI__ .
            'modules/eventsmanager/views/css/atooltip.css');
            $this->context->controller->addJs(__PS_BASE_URI__ .
            'modules/eventsmanager/views/js/jquery.atooltip.min.js');
        }

        $this->context->controller->addCss(__PS_BASE_URI__ .
        'modules/eventsmanager/views/css/fmeevents.css');
    }

    public function init()
    {
        $os = PHP_OS;
        switch ($os) {
            case 'Linux':
                define('SEPARATOR', '/');
                break;
            case 'Windows':
                define('SEPARATOR', '\\');
                break;
            default:
                define('SEPARATOR', '/');
                break;
        }
        parent::init();
        if ($event_id = Tools::getValue('event_id')) {
            $this->event = new Events((int) $event_id, $this->context->language->id);
            if (!Validate::isLoadedObject($this->event) || !$this->event->event_status) {
                header('HTTP/1.1 404 Not Found');
                header('Status: 404 Not Found');
                $this->errors[] = Tools::displayError('Event does not exist.');
            }
        }
    }

    public function initContent()
    {
        parent::initContent();
        if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
            $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            $this->context->smarty->assign(
                [
                    'base_dir' => _PS_BASE_URL_ . __PS_BASE_URI__,
                    'base_dir_ssl' => _PS_BASE_URL_SSL_ . __PS_BASE_URI__,
                    'force_ssl' => $force_ssl,
                ]
            );
        }
        $id_tag = (int) Tools::getValue('id_tag');
        $enable_tags = (int) Configuration::get('EVENTS_TAGS_ENABLE_DISABLE');
        $this->context->smarty->assign('enable_tags', $enable_tags);
        if (Validate::isLoadedObject($this->event) && $this->event->event_status) {
            $fmeTools = new FMEEventsTools();
            $this->context->smarty->assign('fmeTools', $fmeTools);
            $Obj = new Events();
            $this->context->smarty->assign('Obj', $Obj);
            $event_id = (int) Tools::getValue('event_id');
            $is_seat_map = Events::isEnableSeatMap($event_id);
            $eproduct = [];
            $covers = [];
            if (isset($event_id)) {
                $event_id = (int) Tools::getValue('event_id');
                $eventData = $Obj->getEventDetails($event_id);
                $eventGallery = $Obj->getEventGallery($event_id);
                $eventProducts = $Obj->getEventProducts($event_id);
                $eventTags = EventTags::getEventTags($event_id, (int) $this->context->language->id);
                if (!empty($eventProducts)) {
                    foreach ($eventProducts as $event) {
                        $product = new Product($event['id_product'], true, (int) $this->context->language->id);
                        $images = $product->getImages((int) $this->context->cookie->id_lang);
                        foreach ($images as $image) {
                            if ($image['cover']) {
                                $this->context->smarty->assign('mainImage', $images[0]);
                                $covers[$product->id] = $image;
                                $covers[$product->id]['id_image'] = (
                                    Configuration::get(
                                        'PS_LEGACY_IMAGES'
                                    ) ? ($product->id . '-' . $image['id_image']) : $image['id_image']
                                );
                                $covers[$product->id]['id_image_only'] = (int) $image['id_image'];
                            }
                        }
                        if (empty($covers)) {
                            $covers[$product->id] = [
                                'id_image' => $this->context->language->iso_code . '-default',
                                'legend' => 'No picture',
                                'title' => 'No picture',
                            ];
                        }
                        array_push($eproduct, $product);
                    }
                }
                $facebook_link = $eventData['facebook_link'];
                $twitter_link = $eventData['twitter_link'];
                $instagram_link = $eventData['instagram_link'];
                $meta_title = $eventData['event_page_title'];
                $meta_description = $eventData['event_meta_description'];
                $meta_keywords = $eventData['event_meta_keywords'];
                $videoId = '';
                if ($eventData['event_video'] != '') {
                    $explode = explode('=', $eventData['event_video']);
                    $videoId = $explode;
                }
                $vedio = $eventData['event_video'];
                $rx = '~
                      ^(?:https?://)?                           # Optional protocol
                       (?:www[.])?                              # Optional sub-domain
                       (?:youtube[.]com/watch[?]v=|youtu[.]be/) # Mandatory domain name (w/ query string in .com)
                       ([^&]{11})                               # Video id of 11 characters as capture group 1
                        ~x';
                $has_match = preg_match($rx, $vedio, $matches);
                $streaming_video = $eventData['event_streaming'];
                $stream_on = (int) Configuration::get('EVENT_SHOW_LIVE_STREAMING');
                $default_theme = (int) Configuration::get('EVENTS_THEME');
                $this->context->smarty->assign('videoId', $videoId);
                $this->context->smarty->assign('facebook_link', $facebook_link);
                $this->context->smarty->assign('instagram_link', $instagram_link);
                $this->context->smarty->assign('twitter_link', $twitter_link);
                $this->context->smarty->assign('streaming_video', $streaming_video);
                $this->context->smarty->assign('stream_on', $stream_on);
                $this->context->smarty->assign('eventData', $eventData);
                $this->context->smarty->assign('eventGallery', $eventGallery);
                $this->context->smarty->assign('cover', $covers);
                $this->context->smarty->assign('eproduct', $eproduct);
                $this->context->smarty->assign('is_seat_map', $is_seat_map);
                $this->context->smarty->assign('default_theme', $default_theme);
                $this->context->smarty->assign('has_match', $has_match);
            } else {
                $event_id = 0;
                $meta_title = Configuration::get('EVENTS_PAGE_TITLE', $this->context->language->id);
                $meta_keywords = Configuration::get('EVENTS_META_KEYWORDS', $this->context->language->id);
                $meta_description = Configuration::get('EVENTS_META_DESCRIPTION', $this->context->language->id);
            }
            $this->context->smarty->assign('meta_title', $meta_title);
            $this->context->smarty->assign('meta_description', $meta_description);
            $this->context->smarty->assign('meta_keywords', $meta_keywords);
            $this->context->smarty->assign(
                'events_map_hover_address',
                Configuration::get('EVENTS_SHOW_MAP_HOVER_ADDRESS')
            );
            $this->context->smarty->assign(
                'events_show_youtbue_video',
                Configuration::get('EVENTS_SHOW_YOUTUBE_VIDEO')
            );
            $this->context->smarty->assign('events_sharing_options', Configuration::get('EVENTS_SHARING_OPTIONS'));
            $this->context->smarty->assign('events_show_gallery', Configuration::get('EVENTS_SHOW_GALLERY'));
            $this->context->smarty->assign('SLIDER_WIDTH', (int) Configuration::get('SLIDER_WIDTH'));
            $this->context->smarty->assign('SLIDER_HEIGHT', (int) Configuration::get('SLIDER_HEIGHT'));
            $this->context->smarty->assign(
                'THUMBNAILS_ENABLE_DISABLE',
                (int) Configuration::get('THUMBNAILS_ENABLE_DISABLE')
            );
            $this->context->smarty->assign('SLIDER_ARROWS', (int) Configuration::get('SLIDER_ARROWS'));
            $this->context->smarty->assign('PAGINATION_BUTTONS', (int) Configuration::get('PAGINATION_BUTTONS'));
            $this->context->smarty->assign('AUTOPLAY_SLIDER', (int) Configuration::get('AUTOPLAY_SLIDER'));
            $this->context->smarty->assign(
                'EVENTS_META_MAPKEY',
                pSQL(Configuration::get('EVENTS_META_MAPKEY'))
            );
            $this->context->smarty->assign('events_timestamp', (int) Configuration::get('EVENT_SHOW_TIMESTAMP'));
            $this->context->smarty->assign('event_video_status', (int) Configuration::get('EM_VS'));
            $this->context->smarty->assign('version', _PS_VERSION_);
            $this->context->smarty->assign('eventTags', $eventTags);
            $this->context->smarty->assign(
                'tag_link',
                Context::getContext()->link->getModuleLink('eventsmanager', 'events?id_tag=')
            );
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $this->setTemplate('module:eventsmanager/views/templates/front/detail_17.tpl');
            } else {
                $this->setTemplate('detail.tpl');
            }
        } elseif (Tools::getValue('show') || Tools::getValue('month') || Tools::getValue('year')) {
            $show_value = Tools::getValue('show');
            $month = (int) date('m');
            $year = (int) date('Y');
            if (Tools::getValue('month')) {
                $month = Tools::getValue('month');
            }
            if (Tools::getValue('year')) {
                $year = Tools::getValue('year');
            }
            $cdate = self::getMonthLang($month) . ', ' . $year;
            $controls = $this->calenderControls();
            $drwaClaendar = $this->drawCalendar($month, $year);
            $meta_title = Configuration::get('EVENTS_PAGE_TITLE', $this->context->language->id);
            $meta_description = Configuration::get('EVENTS_META_DESCRIPTION', $this->context->language->id);
            $default_theme = (int) Configuration::get('EVENTS_THEME');
            $meta_keywords = Configuration::get('EVENTS_META_KEYWORDS', $this->context->language->id);
            $this->context->smarty->assign('meta_title', $meta_title);
            $this->context->smarty->assign('meta_description', $meta_description);
            $this->context->smarty->assign('meta_keywords', $meta_keywords);
            $this->context->smarty->assign('controls', $controls);
            $this->context->smarty->assign('cdate', $cdate);
            $this->context->smarty->assign('default_theme', $default_theme);
            $this->context->smarty->assign('drwaClaendar', $drwaClaendar);
            $this->context->smarty->assign('show_value', $show_value);
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $this->setTemplate('module:eventsmanager/views/templates/front/calendar_17.tpl');
            } else {
                $this->setTemplate('calendar.tpl');
            }
            if ($show_value == 'grid') {
                $PS_VERSION = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
                $fmeTools = new FMEEventsTools();
                $this->context->smarty->assign('fmeTools', $fmeTools);
                $Obj = new Events();
                $this->context->smarty->assign('Obj', $Obj);
                $this->context->smarty->assign(
                    'events_map_hover_address',
                    Configuration::get('EVENTS_SHOW_MAP_HOVER_ADDRESS')
                );
                $this->context->smarty->assign(
                    'events_show_youtbue_video',
                    Configuration::get('EVENTS_SHOW_YOUTUBE_VIDEO')
                );
                $this->context->smarty->assign(
                    'events_sharing_options',
                    Configuration::get('EVENTS_SHARING_OPTIONS')
                );
                $this->context->smarty->assign(
                    'events_show_gallery',
                    Configuration::get('EVENTS_SHOW_GALLERY')
                );
                $this->context->smarty->assign(
                    'events_timestamp',
                    (int) Configuration::get('EVENT_SHOW_TIMESTAMP')
                );
                $this->context->smarty->assign('version', _PS_VERSION_);
                $this->context->smarty->assign('ps_ver', $PS_VERSION);
                $calender_params = ['show' => 'calendar'];
                $list_params = ['show' => 'list'];
                $grid_params = ['show' => 'grid'];
                $list_link = Context::getContext()->link->getModuleLink(
                    'eventsmanager',
                    'calendar',
                    $list_params
                );
                $grid_link = Context::getContext()->link->getModuleLink(
                    'eventsmanager',
                    'calendar',
                    $grid_params
                );
                $calender_link = Context::getContext()->link->getModuleLink(
                    'eventsmanager',
                    'calendar',
                    $calender_params
                );
                $this->context->smarty->assign(
                    [
                        'calender_link' => $calender_link,
                        'list_link' => $list_link,
                        'grid_link' => $grid_link,
                    ]
                );
                $tags = EventTags::getAllTagNames((int) $this->context->cookie->id_lang);
                $this->context->smarty->assign('tags', $tags);
                $this->context->smarty->assign(
                    'tag_link',
                    Context::getContext()->link->getModuleLink('eventsmanager', 'events?id_tag=')
                );
                $this->context->smarty->assign(
                    'events_page_link',
                    Context::getContext()->link->getModuleLink('eventsmanager', 'events')
                );
                $isTagPage = false;
                if (isset($id_tag) && $id_tag != 0) {
                    $isTagPage = true;
                    $selected_event = EventTags::getSelectedTagsById(
                        $id_tag,
                        $this->context->language->id
                    );
                    $filtered_events = EventTags::getEventIdsByTagId($id_tag);
                    $filtered_event_ids = [];
                    foreach ($filtered_events as $event) {
                        $filtered_event_ids[] = $event['event_id'];
                    }
                    $this->context->smarty->assign('selected_event', $selected_event);
                    $this->context->smarty->assign('filtered_events', $filtered_event_ids);
                }
                $this->context->smarty->assign('isTagPage', $isTagPage);
                $this->context->smarty->assign(
                    'grid_link',
                    Context::getContext()->link->getModuleLink('eventsmanager', 'events?show=grid')
                );
                $this->context->smarty->assign(
                    'list_link',
                    Context::getContext()->link->getModuleLink('eventsmanager', 'events?show=list')
                );
                $this->assignAll($isTagPage);
                if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                    $this->setTemplate('module:eventsmanager/views/templates/front/grid_17.tpl');
                } else {
                    $this->setTemplate('grid.tpl');
                }
            } elseif ($show_value == 'list') {
                $PS_VERSION = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
                $fmeTools = new FMEEventsTools();
                $this->context->smarty->assign('fmeTools', $fmeTools);
                $Obj = new Events();
                $this->context->smarty->assign('Obj', $Obj);
                $this->context->smarty->assign(
                    'events_map_hover_address',
                    Configuration::get('EVENTS_SHOW_MAP_HOVER_ADDRESS')
                );
                $this->context->smarty->assign(
                    'events_show_youtbue_video',
                    Configuration::get('EVENTS_SHOW_YOUTUBE_VIDEO')
                );
                $this->context->smarty->assign(
                    'events_sharing_options',
                    Configuration::get('EVENTS_SHARING_OPTIONS')
                );
                $this->context->smarty->assign(
                    'events_show_gallery',
                    Configuration::get('EVENTS_SHOW_GALLERY')
                );
                $this->context->smarty->assign(
                    'events_timestamp',
                    (int) Configuration::get('EVENT_SHOW_TIMESTAMP')
                );
                $this->context->smarty->assign('version', _PS_VERSION_);
                $this->context->smarty->assign('ps_ver', $PS_VERSION);
                $tags = EventTags::getAllTagNames((int) $this->context->cookie->id_lang);
                $this->context->smarty->assign('tags', $tags);
                $this->context->smarty->assign(
                    'tag_link',
                    Context::getContext()->link->getModuleLink('eventsmanager', 'events?id_tag=')
                );
                $this->context->smarty->assign(
                    'events_page_link',
                    Context::getContext()->link->getModuleLink('eventsmanager', 'events')
                );
                $isTagPage = false;
                if (isset($id_tag) && $id_tag != 0) {
                    $isTagPage = true;
                    $selected_event = EventTags::getSelectedTagsById(
                        $id_tag,
                        $this->context->language->id
                    );
                    $filtered_events = EventTags::getEventIdsByTagId($id_tag);
                    $filtered_event_ids = [];
                    foreach ($filtered_events as $event) {
                        $filtered_event_ids[] = $event['event_id'];
                    }
                    $this->context->smarty->assign('selected_event', $selected_event);
                    $this->context->smarty->assign('filtered_events', $filtered_event_ids);
                }
                $this->context->smarty->assign('isTagPage', $isTagPage);
                $calender_params = ['show' => 'calendar'];
                $list_params = ['show' => 'list'];
                $grid_params = ['show' => 'grid'];
                $list_link = Context::getContext()->link->getModuleLink(
                    'eventsmanager',
                    'calendar',
                    $list_params
                );
                $grid_link = Context::getContext()->link->getModuleLink(
                    'eventsmanager',
                    'calendar',
                    $grid_params
                );
                $calender_link = Context::getContext()->link->getModuleLink(
                    'eventsmanager',
                    'calendar',
                    $calender_params
                );
                $this->context->smarty->assign(
                    [
                        'calender_link' => $calender_link,
                        'list_link' => $list_link,
                        'grid_link' => $grid_link,
                    ]
                );
                $this->assignAll($isTagPage);
                if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                    $this->setTemplate('module:eventsmanager/views/templates/front/events_17.tpl');
                } else {
                    $this->setTemplate('events.tpl');
                }
            }
        } else {
            $PS_VERSION = (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) ? 1 : 0;
            $fmeTools = new FMEEventsTools();
            $this->context->smarty->assign('fmeTools', $fmeTools);
            $Obj = new Events();
            $this->context->smarty->assign('Obj', $Obj);
            $meta_title = Configuration::get('EVENTS_PAGE_TITLE', $this->context->language->id);
            $meta_description = Configuration::get(
                'EVENTS_META_DESCRIPTION',
                $this->context->language->id
            );
            $meta_keywords = Configuration::get(
                'EVENTS_META_KEYWORDS',
                $this->context->language->id
            );
            $this->context->smarty->assign('meta_title', $meta_title);
            $this->context->smarty->assign('meta_description', $meta_description);
            $this->context->smarty->assign('meta_keywords', $meta_keywords);
            $this->context->smarty->assign(
                'events_map_hover_address',
                Configuration::get('EVENTS_SHOW_MAP_HOVER_ADDRESS')
            );
            $this->context->smarty->assign(
                'events_show_youtbue_video',
                Configuration::get('EVENTS_SHOW_YOUTUBE_VIDEO')
            );
            $this->context->smarty->assign(
                'events_sharing_options',
                Configuration::get('EVENTS_SHARING_OPTIONS')
            );
            $this->context->smarty->assign(
                'events_show_gallery',
                Configuration::get('EVENTS_SHOW_GALLERY')
            );
            $this->context->smarty->assign(
                'events_timestamp',
                (int) Configuration::get('EVENT_SHOW_TIMESTAMP')
            );
            $this->context->smarty->assign('version', _PS_VERSION_);
            $this->context->smarty->assign('ps_ver', $PS_VERSION);
            $calender_params = ['show' => 'calendar'];
            $list_params = ['show' => 'list'];
            $grid_params = ['show' => 'grid'];
            $list_link = Context::getContext()->link->getModuleLink(
                'eventsmanager',
                'calendar',
                $list_params
            );
            $grid_link = Context::getContext()->link->getModuleLink(
                'eventsmanager',
                'calendar',
                $grid_params
            );
            $calender_link = Context::getContext()->link->getModuleLink(
                'eventsmanager',
                'calendar',
                $calender_params
            );
            $this->context->smarty->assign(
                [
                    'calender_link' => $calender_link,
                    'list_link' => $list_link,
                    'grid_link' => $grid_link,
                ]
            );
            $tags = EventTags::getAllTagNames((int) $this->context->cookie->id_lang);
            $this->context->smarty->assign('tags', $tags);
            $this->context->smarty->assign(
                'tag_link',
                Context::getContext()->link->getModuleLink('eventsmanager', 'events?id_tag=')
            );
            $this->context->smarty->assign(
                'events_page_link',
                Context::getContext()->link->getModuleLink('eventsmanager', 'events')
            );
            $isTagPage = false;
            if (isset($id_tag) && $id_tag != 0) {
                $isTagPage = true;
                $selected_event = EventTags::getSelectedTagsById(
                    $id_tag,
                    $this->context->language->id
                );
                $filtered_events = EventTags::getEventIdsByTagId($id_tag);
                $filtered_event_ids = [];
                foreach ($filtered_events as $event) {
                    $filtered_event_ids[] = $event['event_id'];
                }
                $this->context->smarty->assign('filtered_events', $filtered_event_ids);
                $this->context->smarty->assign('selected_event', $selected_event);
            }
            $this->context->smarty->assign('isTagPage', $isTagPage);
            $calender_params = ['show' => 'calendar'];
            $list_params = ['show' => 'list'];
            $grid_params = ['show' => 'grid'];
            $list_link = Context::getContext()->link->getModuleLink(
                'eventsmanager',
                'calendar',
                $list_params
            );
            $grid_link = Context::getContext()->link->getModuleLink(
                'eventsmanager',
                'calendar',
                $grid_params
            );
            $calender_link = Context::getContext()->link->getModuleLink(
                'eventsmanager',
                'calendar',
                $calender_params
            );
            $this->context->smarty->assign(
                [
                    'calender_link' => $calender_link,
                    'list_link' => $list_link,
                    'grid_link' => $grid_link,
                ]
            );
            $this->assignAll($isTagPage);
            if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                $this->setTemplate('module:eventsmanager/views/templates/front/events_17.tpl');
            } else {
                $this->setTemplate('events.tpl');
            }
        }
    }

    protected function assignAll($isTagRequest = false)
    {
        if ((int) Configuration::get('EVENTS_ENABLE_DISABLE') == 1) {
            $default_ipp = (int) Configuration::get('EVENTS_PER_PAGE');
            $default_theme = (int) Configuration::get('EVENTS_THEME');
            $enable_tags = (int) Configuration::get('EVENTS_TAGS_ENABLE_DISABLE');
            $default_ipp = ($default_ipp > 0) ? $default_ipp : 10;
            $pages = new EventsPaginator();
            $pagesLimit = $isTagRequest ? 'LIMIT 0, 1000' : $pages->limit;
            $get_events_all = Events::getAllFrontEvents(
                Context::getContext()->language->id,
                $pagesLimit
            );
            $result = Events::getAllFrontEvents(Context::getContext()->language->id, '');
            $nbEvents = count($result);
            $pages = new EventsPaginator();
            $pages->items_total = $nbEvents;
            $pages->mid_range = 3; // Number of pages to display. Must be odd and > 3
            $pages->paginate('eventsmanager', 'events', $default_ipp);
            $span = '<span class=\"\">' . $pages->displayJumpMenu() .
            $pages->displayItemsPerPage() . '</span>';
            $last_1 = '<p class=\"paginate\">Page: $pages->current_page of $pages->num_pages</p>\n';
            $this->context->smarty->assign([
                'pages_nb' => Tools::ceilf($nbEvents / (int) $default_ipp),
                'nbEvents' => $nbEvents,
                'events' => $get_events_all,
                'display_pages' => $pages->displayPages(),
                'span_echo' => $span,
                'last_1' => $last_1,
                'default_theme' => $default_theme,
                'enable_tags' => $enable_tags,
            ]);
        } else {
            $this->context->smarty->assign('nbEvents', 0);
        }
    }

    public function drawCalendar($month, $year)
    {
        $link = Context::getContext()->link;
        $module = new EventsManager();
        $moduleLink = $link->getModuleLink('eventsmanager', 'events');
        $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
        $headings = [
            $module->l('Sun', 'Events'),
            $module->l('Mon', 'Events'),
            $module->l('Tue', 'Events'),
            $module->l('Wed', 'Events'),
            $module->l('Thu', 'Events'),
            $module->l('Fri', 'Events'),
            $module->l('Sat', 'Events'),
        ];
        $calendar .= '<tr class="calendar-row"><td class="calendar-day-head">' .
        implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';
        $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $calendar .= '<tr class="calendar-row">';
        for ($x = 0; $x < $running_day; $x = $x + 1) {
            $calendar .= '<td class="calendar-day-np">&nbsp;</td>';
            ++$days_in_this_week;
        }

        for ($list_day = 1; $list_day <= $days_in_month; $list_day = $list_day + 1) {
            $calendar .= '<td class="calendar-day mat_event_single_holder">';
            $dateString = date('Y-m-d', strtotime($year . '-' . $month . '-' . $list_day));
            $calendar .= '<div class="day-number">' . $list_day . '</div>';
            $title = '&nbsp;';
            $links = '';
            $evtData = $this->eventOfDate($dateString);
            if ($evtData = $this->eventOfDate($dateString)) {
                $calendar .= '<div class="test" id="testG-' . $list_day .
                '" style="cursor: pointer;">';
                $links .= '<ul>';
                $dateStringtip = date('F d Y', strtotime($dateString));
                foreach ($evtData as $e) {
                    if ($e['event_meta_description']) {
                        $meta = Tools::substr($e['event_meta_description'], 0, 100);
                    } else {
                        $meta = '';
                    }
                    $params = ['event_id' => $e['event_id'], 'eventslink' => $e['event_permalinks']];
                    $event_link = $link->getModuleLink('eventsmanager', 'detail', $params);
                    $calendar .= '<a href=' . $event_link .
                    ' class="tips clickTip' . $day_counter . '" title="' .
                    $module->l('Event Detail: ', 'Events') .
                    $meta . $module->l(' Event(s) on ', 'Events') .
                    $dateStringtip . ' ' . $module->l(' From:- ', 'Events') . date(
                        'M d, Y - h:i a',
                        strtotime($e['event_start_date'])
                    ) . ' ' . $module->l('To:- ', 'Events') . date(
                        'M d, Y - h:i a',
                        strtotime($e['event_end_date'])
                    ) . '">- ' . $e['event_title'] . '</a><br/>';
                    $title = $e['event_title'];
                    $links .= version_compare(_PS_VERSION_,'9.0.0', '>=') ? stripslashes(
                        '<li><a href=' . $event_link . '><strong style="color:#fff;">' .
                        addslashes($title) .
                        '</strong></a> <p>(<b>' . $module->l('From', 'Events') . ':</b> ' .
                        date('M d, Y - h:i a', strtotime($e['event_start_date'])) . ' <b>' .
                        $module->l('To', 'Events') . ':</b> ' .
                        date('M d, Y - h:i a', strtotime($e['event_end_date'])) . ')</p></li>'
                    ) : Tools::stripslashes(
                        '<li><a href=' . $event_link . '><strong style="color:#fff;">' .
                        addslashes($title) .
                        '</strong></a> <p>(<b>' . $module->l('From', 'Events') . ':</b> ' .
                        date('M d, Y - h:i a', strtotime($e['event_start_date'])) . ' <b>' .
                        $module->l('To', 'Events') . ':</b> ' .
                        date('M d, Y - h:i a', strtotime($e['event_end_date'])) . ')</p></li>'
                    );
                }
                $links .= version_compare(_PS_VERSION_,'9.0.0', '>=') ? stripslashes( 
                    '<br/><p style="border-top:1px dashed #fff;">
                    <a href=' . $moduleLink . '><strong style="color:#fff;">' .
                    $module->l('All Events', 'Events') . '</strong></a></p>'
                    ) : Tools::stripslashes('<br/><p style="border-top:1px dashed #fff;">
                    <a href=' . $moduleLink . '><strong style="color:#fff;">' .
                    $module->l('All Events', 'Events') . '</strong></a></p>');
                $links .= '</ul>';
                if (Tools::version_compare(_PS_VERSION_, '1.7.0.0', '>=') == true) {
                    $calendar .= "</div>
                                <script type=\"text/javascript\">
                                $(document).ready(function(){
                                $('a.clickTip" . $day_counter . "').tooltip()
                                    });
                                    </script>";
                } else {
                    $calendar .= "</div>
                                <script type=\"text/javascript\">
                                $(document).ready(function(){
                                            $('a.clickTip" . $day_counter . "').aToolTip({
                                            clickIt: true,
                                            tipContent: '<div><p style=\"border-bottom:1px dashed #fff;\">" .
                    $module->l('Event(s) on ', 'Events') .
                        $dateStringtip . "</p>$links</div>',
                                        });
                                    });
                                    </script>";
                }
            } else {
                $calendar .= '<p>&nbsp;</p>';
                $calendar .= '</td>';
            }if ($running_day == 6) {
                $calendar .= '</tr>';
                if (($day_counter + 1) != $days_in_month) {
                    $calendar .= '<tr class="calendar-row">';
                }
                $running_day = -1;
                $days_in_this_week = 0;
            }

            ++$days_in_this_week;
            ++$running_day;
            ++$day_counter;
        }if ($days_in_this_week < 8) {
            for ($x = 1; $x <= (8 - $days_in_this_week); $x = $x + 1) {
                $calendar .= '<td class="calendar-day-np">&nbsp;</td>';
            }
        }
        $calendar .= '</tr>';
        $calendar .= '</table>';

        return $calendar;
    }

    public function calenderControls()
    {
        $module = new EventsManager();
        $link = Context::getContext()->link;
        $moduleLink = $link->getModuleLink('eventsmanager', 'events');
        $month = (int) (Tools::getValue('month') ? Tools::getValue('month') : date('m'));
        $year = (int) (Tools::getValue('year') ? Tools::getValue('year') : date('Y'));
        $select_month_control = '<select name="month" id="month">';
        for ($x = 1; $x <= 12; $x = $x + 1) {
            $select_month_control .= '<option value="' . $x . '"' . ($x != $month ? '' : ' selected="selected"') . '>' .
            date('F', mktime(0, 0, 0, $x, 1, $year)) . '</option>';
        }
        $select_month_control .= '</select>';
        $year_range = 7;
        $select_year_control = '<select name="year" id="year">';
        for ($x = ($year - floor($year_range / 2)); $x <= ($year + floor($year_range / 2)); $x = $x + 1) {
            $select_year_control .= '<option value="' . $x . '"' .
            ($x != $year ? '' : ' selected="selected"') . '>' . $x;
            $select_year_control .= '</option>';
        }
        $cdate = self::getMonthLang($month) . ', ' . $year;
        $select_year_control .= '</select>';
        $default_theme = (int) Configuration::get('EVENTS_THEME');
        if ($default_theme == 1) {
            $next_month_link = '<a href="' . $moduleLink . '?month=' .
            ($month != 12 ? $month + 1 : 1) . '&amp;year=' .
                ($month != 12 ? $year : $year + 1) .
                '" class="control">
                   <button type="button" class="fc-next-button fc-button fc-state-default fc-corner-right">
                   <span class="fc-icon fc-icon-right-single-arrow"></span></button> </a>';
            $previous_month_link = '<a href="' . $moduleLink . '?month=' . ($month != 1 ? $month - 1 : 12) .
                '&amp;year=' . ($month != 1 ? $year : $year - 1) .
                '" class="control">
                    <button type="button" class="fc-prev-button fc-button fc-state-default fc-corner-left">
                    <span class="fc-icon fc-icon-left-single-arrow"></span></button> </a>';
        } else {
            $next_month_link = '<a href="' . $moduleLink . '?month=' . ($month != 12 ? $month + 1 : 1) .
            '&amp;year=' . ($month != 12 ? $year : $year + 1) . '" class="control">' .
            $module->l('Next Month', 'Events') . ' >></a>';
            $previous_month_link = '<a href="' . $moduleLink . '?month=' .
            ($month != 1 ? $month - 1 : 12) . '&amp;year=' . ($month != 1 ? $year : $year - 1) .
            '" class="control"><<   ' . $module->l('Previous Month', 'Events') . '</a>';
        }
        $controls = '<form id="calendar_form" method="post" action="' . $moduleLink . '">' .
        $previous_month_link . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $next_month_link .
        $select_month_control . $select_year_control . '&nbsp;<input type="submit" name="submit" value="' .
        $module->l('Go', 'Events') . '" /><h1 class="heading_calendar">' . $cdate .
            ' </h1>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </form>';

        return $controls;
    }

    public function eventOfDate($date)
    {
        return Events::getEventsByDate($date);
    }

    private function getMonthLang($m)
    {
        $module = new EventsManager();
        $month = false;
        switch ($m) {
            case 1:
                $month = $module->l('January', 'Events');
                break;
            case 2:
                $month = $module->l('February', 'Events');
                break;
            case 3:
                $month = $module->l('March', 'Events');
                break;
            case 4:
                $month = $module->l('April', 'Events');
                break;
            case 5:
                $month = $module->l('May', 'Events');
                break;
            case 6:
                $month = $module->l('June', 'Events');
                break;
            case 7:
                $month = $module->l('July', 'Events');
                break;
            case 8:
                $month = $module->l('August', 'Events');
                break;
            case 9:
                $month = $module->l('September', 'Events');
                break;
            case 10:
                $month = $module->l('October', 'Events');
                break;
            case 11:
                $month = $module->l('November', 'Events');
                break;
            case 12:
                $month = $module->l('December', 'Events');
                break;
        }

        return $month;
    }

    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $meta_title = Configuration::get('EVENTS_PAGE_TITLE', $this->context->language->id);
        $breadcrumb['links'][] = [
            'title' => $meta_title,
            'url' => $this->context->link->getModuleLink('eventsmanager', 'events', []),
        ];

        return $breadcrumb;
    }
}
