<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */

if (!defined('_PS_VERSION_')) { exit; }

if (!defined('_ETS_PNF_MODULE_')) {
	define('_ETS_PNF_MODULE_', 'pagenotfound');
}

if (!defined('_PS_ETS_PNF_IMG_DIR_')) {
	define('_PS_ETS_PNF_IMG_DIR_', _PS_IMG_DIR_.'pagenotfound/');
}
if (!defined('_PS_ETS_PNF_IMG_')) {
	define('_PS_ETS_PNF_IMG_', _PS_IMG_.'pagenotfound/');
}
if (!defined('_PS_ETS_PNF_DEFAULT_IMAGE_')) {
	define('_PS_ETS_PNF_DEFAULT_IMAGE_', 'default.png');
}
if (!defined('_PS_ETS_PNF_LOG_DIR_')) {
	if (file_exists(_PS_ROOT_DIR_ . '/var/logs')) {
		define('_PS_ETS_PNF_LOG_DIR_', _PS_ROOT_DIR_ . '/var/logs/');
	} else
		define('_PS_ETS_PNF_LOG_DIR_', _PS_ROOT_DIR_ . '/log/');
}
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;

require_once(dirname(__FILE__) . '/classes/Ets_pnf_tools.php');
require_once(dirname(__FILE__) . '/classes/Ets_pnf_defines.php');

class Pagenotfound extends Module
{
	public $is8 = false;
	public $is17 = false;
	public $is15 = false;
	public $colnames = array();
	private $baseAdminPath;
	private $errorMessage;
	private $_html;
	public $_list_order_default = array();
	public $title_fields = array();
	public $_errors = array();
	public $bulk_orders = array();
	public $fields_form = array();
	public $list_fields;
	public function __construct()
	{
		$this->name = 'pagenotfound';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'PrestaHero';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();
		if (version_compare(_PS_VERSION_, '8.0', '>=')) {
			$this->is8 = true;
		}
		if (version_compare(_PS_VERSION_, '1.7', '>=')) {
			$this->is17 = true;
		}
		if (version_compare(_PS_VERSION_, '1.6', '<')) {
			$this->is15 = true;
		}
		$this->displayName = $this->l('Custom Page Not Found');
		$this->description = $this->l('Transform your \'Page not found\' page into an engaging space with personalized content, images, and easy navigation buttons.');
$this->refs = 'https://prestahero.com/';
		$this->ps_versions_compliancy = array('min' => '1.5.0.0', 'max' => _PS_VERSION_);
	}

	/**
	 * @see Module::install()
	 */
	public function install()
	{
		return parent::install()
			&& $this->installConfigs()
			&& $this->_registerHooks();
	}

	public function copyDefaultFiles()
	{
		if (!is_dir(_PS_ETS_PNF_IMG_DIR_)) {
			@mkdir(_PS_ETS_PNF_IMG_DIR_, 0755, true);
		}
		if (!is_file(_PS_ETS_PNF_IMG_DIR_ . '/index.php')) {
			@copy(dirname(__FILE__) . '/index.php', _PS_ETS_PNF_IMG_DIR_ . 'index.php');
		}
		if (!is_file(_PS_ETS_PNF_IMG_DIR_ . '/default.png')) {
			@copy(dirname(__FILE__) . '/views/img/default.png', _PS_ETS_PNF_IMG_DIR_ . '/default.png');
		}
	}

	public function _registerHooks() {
		$hooks = Ets_pnf_defines::getHooks();
		foreach ($hooks as $hook) {
			$this->registerHook($hook);
		}
		return true;
	}

	public function _unRegisterHooks() {
		$hooks = Ets_pnf_defines::getHooks();
		foreach ($hooks as $hook) {
			$this->unregisterHook($hook);
		}
		return true;
	}


	public function installConfigs()
	{
		$languages = Language::getLanguages(false);
		$configs = Ets_pnf_defines::getInstance()->getConfigs();
		if ($configs) {
			foreach ($configs as $key => $config) {
				if (isset($config['lang']) && $config['lang']) {
					$values = array();
					foreach ($languages as $lang) {
						$values[$lang['id_lang']] = (isset($config['default']) ? $config['default'] : '');
					}
					$this->updateValues($config, $key, $values, true);
				} else {
					$this->updateValues($config, $key, (isset($config['default']) ? $config['default'] : ''), true);
				}
			}
		}
		$this->copyDefaultFiles();

		return true;
	}

	/**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
		return parent::uninstall()
			&& $this->uninstallConfigs()
			&& $this->_unRegisterHooks()
			&& $this->deleteDefaultFiles();
	}

	public function deleteDefaultFiles($dir = _PS_ETS_PNF_IMG_DIR_)
	{
		$dir = $dir && strlen($dir) > 1 ? rtrim($dir, '/') : '';
		if (!$dir) {
			return true;
		}
		$objects = scandir($dir);
		if (!$objects) {
			return true;
		}
		foreach ($objects as $object) {
			if ($object != "." && $object != "..") {
				if (is_dir($dir . "/" . $object) && !is_link($dir . "/" . $object))
					$this->deleteDefaultFiles($dir . "/" . $object);
				else
					@unlink($dir . "/" . $object);
			}
		}
		rmdir($dir);
		return true;
	}

	public function uninstallConfigs()
	{
		$configs = Ets_pnf_defines::getInstance()->getConfigs();
		if ($configs) {
			foreach ($configs as $key => $config) {
				Configuration::deleteByName($key);
				unset($config);
			}
		}
		return true;
	}

	public function isValidIds($excludeId)
	{
		if ($excludeId != '') {
			$ids = explode('-', $excludeId);
			if (!isset($ids[1]))
				$ids[1] = 0;
			if (Validate::isInt($ids[0]) && Validate::isInt($ids[1]))
				return (int)$ids[0] . '-' . (int)$ids[1];
			return false;
		}
		return false;
	}

	public function getContent()
	{
		self::registerPlugins();
		if(!$this->active)
		{
			return $this->displayWarning(sprintf($this->l('You must enable "%s" module to configure its features'),$this->displayName));
		}

		$this->context->controller->addJqueryUI('ui.sortable');
		$this->processPost();
		if (Tools::isSubmit('saveConfig')) {
			$this->saveConfigs();
		}
		return $this->getAdminHtml();
	}

	public function processPost()
	{
		if (($type = Tools::getValue('type')) && ($type == 'product' || $type == 'blog' || $type == 'blogFree')) {
			$query = Tools::getValue('q');
			if ($query && Validate::isCleanHtml($query)) {
				if ($type == 'product') {
					$this->searchProduct($query);
					die();
				}
				if ($type == 'blog' || $type == 'blogFree') {
					$this->searchBlogPosts($query);
					die();
				}
			}
		}
		if (($action = Tools::getValue('action')) && ($action == 'etsPnfAddProduct' || $action == 'etsPnfAddBlog' || $action == 'etsPnfAddBlogFree')) {
			$IDs = Tools::getValue('ids', false);
			if ($IDs && Validate::isCleanHtml($IDs)) {
				if ($action == 'etsPnfAddProduct') {
					die(json_encode(array(
						'html' => $this->hookDisplayEtsPnfProductList(array('ids' => $IDs)),
					)));
				}
				if ($action == 'etsPnfAddBlog' || $action == 'etsPnfAddBlogFree') {
					die(
						json_encode([
							'html' => $this->displayEtsPnfBlogList(['ids' => $IDs], $action == 'etsPnfAddBlog' ? 'blog' : 'blogFree')
						]
					));
				}
			}
		}
	}

	public function searchProduct($query = '') {
		if (!$query) {
			return;
		}
		$origQuery = trim($query);
		$imageType = $this->getMmType('cart');
		if ($pos = strpos($query, ' (ref:')) {
			$query = Tools::substr($query, 0, $pos);
		}
		$excludeIds = Ets_pnf_tools::uniqIdsFromStr(Tools::getValue('excludeIds'));
		$excludeVirtuals = (bool)Tools::getValue('excludeVirtuals', false);
		$exclude_packs = (bool)Tools::getValue('exclude_packs', false);
		$items = Ets_pnf_defines::getProductByQuery($query, $excludeIds, $excludeVirtuals, $exclude_packs);
		if ($items) {
			$results = array();
			foreach ($items as $item) {
				$results[] = array(
					'id_product' => (int)($item['id_product']),
					'name' => $item['name'],
					'ref' => (!empty($item['reference']) ? $item['reference'] : ''),
					'image' => str_replace('http://', Tools::getShopProtocol(), $this->context->link->getImageLink($item['link_rewrite'], $item['id_image'], $imageType)),
					'price' => Tools::getContextLocale($this->context)->formatPrice($item['price'], $this->context->currency->iso_code),
					'link' => $this->context->link->getProductLink($item['id_product'], null, null, null, null, null, 0),
				);
			}
			if ($results) {
				foreach ($results as $item) {
					echo implode('|', $item) . "\n";
				}
			}
		} else {
			echo implode('|', [
				'id_product' => 0,
				'name' => $this->l('There is no result for ') . $origQuery,
				'ref' => '',
				'image' => '',
				'price' => '',
				'link' => '',
			]);
			echo "\n";
		}
	}

	public function searchBlogPosts($query = '') {
		if (!$query) {
			return;
		}
		$origQuery = trim($query);
		if ($pos = strpos($query, ' (ref:')) {
			$query = Tools::substr($query, 0, $pos);
		}
		$excludeIds = Ets_pnf_tools::uniqIdsFromStr(Tools::getValue('excludeIds'));
		$items = [];
		if (Ets_pnf_tools::isBlogInstalled(true)) {
			$items = Ets_pnf_defines::getBlogByQuery($query, $excludeIds);
			$path = Ets_pnf_tools::getBlogImgPath();
			if ($items) {
				foreach ($items as $item) {
					$thumb = $item['thumb'] ? $this->context->link->getMediaLink($path . $item['thumb']) : '';
					echo trim($item['id_post'] . '|' . $item['title'] . '|' . $thumb) . "\n";
				}
			} else {
				echo implode('|', [
					'id_product' => 0,
					'name' => $this->l('There is no result for ') . $origQuery,
					'ref' => '',
					'image' => '',
					'price' => '',
					'link' => '',
				]);
				echo "\n";
			}
		}
	}

	public function hookDisplayEtsPnfBlogList($params) {
		return $this->displayEtsPnfBlogList($params, 'blog');
	}

	public function hookDisplayEtsPnfBlogListFree($params) {
		return $this->displayEtsPnfBlogList($params, 'blogFree');
	}

	public function displayEtsPnfBlogList($params, $type = 'blog')
	{
		$blogInstalled = Ets_pnf_tools::isBlogInstalled();
		if (!$blogInstalled['blog'] && !$blogInstalled['blogFree']) {
			return '';
		}
		if (isset($params['ids']) && $params['ids'] && ($blogIds = $params['ids'])) {
			$IDs = explode(',', $blogIds);
			$blogIds = [];
			foreach ($IDs as $ID) {
				$blogIds[] = $ID;
			}
			if ($blogIds) {
				$blogPosts = Ets_pnf_defines::getBlogsByIds($blogIds, $type);

				/** @var Ybc_blog|Ets_blog $blogModule */
				$blogModule = Ets_pnf_tools::getBlogModuleInstance($type);
				$path = Ets_pnf_tools::getBlogImgPath();
				if ($blogPosts && $blogModule) {
					foreach ($blogPosts as &$blogPost) {
						$blogPost['link'] = $blogModule->getLink('blog', ['id_post' => $blogPost['id_post']]);
						$blogPost['image'] = $blogPost['thumb'] ? $this->context->link->getMediaLink($path . $blogPost['thumb']) : '';
					}
				} else {
					$blogPost = [];
				}

				$this->context->smarty->assign('blogs', $blogPosts);
				return $this->display(__FILE__, 'blog-item.tpl');
			}
		}
	}

	public function hookDisplayHeader() {
		$controller = Tools::getValue('controller');
		if ($controller == 'pagenotfound') {
			$this->context->controller->addCSS($this->_path . 'views/css/slick.css', 'all');
			$this->context->controller->addCSS($this->_path . 'views/css/front.css', 'all');
			$this->context->controller->addJS($this->_path . 'views/js/front.js');
			$this->context->controller->addJS($this->_path . 'views/js/slick.min.js');
		}
	}

	public function hookDisplayEtsPnfProductList($params)
	{
		if (isset($params['ids']) && $params['ids'] && ($productIds = $params['ids'])) {
			$IDs = explode(',', $productIds);
			$products = array();
			foreach ($IDs as $ID) {
				if ($ID && ($tmpIDs = explode('-', $ID))) {
					$products[] = array(
						'id_product' => $tmpIDs[0],
						'id_product_attribute' => !empty($tmpIDs[1]) ? $tmpIDs[1] : 0,
					);
				}
			}
			if ($products) {
				$products = $this->getBlockProducts($products);
			}
			$this->smarty->assign('products', $products);
			return $this->display(__FILE__, 'product-item.tpl');
		}
	}

	public function hookDisplayOverrideTemplate($params)
	{
		if (isset($params['template_file']) && $params['template_file'] == 'errors/404' && $params['controller'] instanceof PageNotFoundControllerCore && (int)Configuration::get('ETS_PNF_ACTIVE')) {
			$configs = $this->getConfigsValues();
			$configs['base_link'] = $this->context->link->getBaseLink();
			$blogInstalled = Ets_pnf_tools::isBlogInstalled();

			$smarty = [
				'ets_pnf_configs' => $configs,
				'pnfProducts' => $this->getProductsForTemplate(),
				'blogInstalled' => $blogInstalled,
				'blog' => [
					'posts' => $this->getBlogPostsForTemplate()
				]
			];

			if ($blogInstalled['blog']) {
				$smarty['blog']['display_desc'] = Configuration::get('YBC_BLOG_PRODUCT_PAGE_DISPLAY_DESC');
				$smarty['blog']['allow_rating'] = (int)Configuration::get('YBC_BLOG_ALLOW_RATING') ? true : false;
				$smarty['blog']['allow_like'] = (int)Configuration::get('YBC_BLOG_ALLOW_LIKE') ? true : false;
				$smarty['blog']['show_date'] = (int)Configuration::get('YBC_BLOG_SHOW_POST_DATE') ? true : false;
				$smarty['blog']['show_views'] = (int)Configuration::get('YBC_BLOG_SHOW_POST_VIEWS') ? true : false;
				$smarty['blog']['show_categories'] = (int)Configuration::get('YBC_BLOG_SHOW_POST_CATEGORIES') ? true : false;
				$smarty['blog']['allowComments'] = (int)Configuration::get('YBC_BLOG_ALLOW_COMMENT') ? true : false;
				$smarty['blog']['read_more_text'] = Configuration::get('YBC_BLOG_TEXT_READMORE');
			} elseif ($blogInstalled['blogFree']) {
				$smarty['blog']['allow_like'] = false;
				$smarty['blog']['show_views'] = false;
				$smarty['blog']['allowComments'] = true;
				$smarty['blog']['ets_blog_text_Readmore'] = Configuration::get('ETS_BLOG_TEXT_READMORE', $this->context->language->id);
			}

			$this->context->smarty->assign($smarty);
			return $this->getTemplatePath('pages/pagenotfound.tpl');
		}
	}

	public function getProductsForTemplate()
	{
		$pids = Configuration::get('ETS_PNF_PRODUCTS', '');
		if (!$pids) {
			return [];
		}

		$pidsQuery = [];
		$pids = explode(',', $pids);
		foreach ($pids as $pid) {
			$pidParts = explode('-', $pid);
			if (is_numeric($pidParts[0]) && $pidParts[0] > 0 && !in_array($pidParts[0], $pidsQuery)) {
				$pidsQuery[] = (int) $pidParts[0];
			}
		}

		if (!$pidsQuery) {
			return [];
		}

		$assembler = new ProductAssembler($this->context);

		$presenterFactory = new ProductPresenterFactory($this->context);
		$presentationSettings = $presenterFactory->getPresentationSettings();
		if (version_compare(_PS_VERSION_, '1.7.5', '>=')) {
				$presenter = new \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter(
						new ImageRetriever(
								$this->context->link
						),
						$this->context->link,
						new PriceFormatter(),
						new ProductColorsRetriever(),
						$this->context->getTranslator()
				);
		} else {
				$presenter = new \PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
						new ImageRetriever(
								$this->context->link
						),
						$this->context->link,
						new PriceFormatter(),
						new ProductColorsRetriever(),
						$this->context->getTranslator()
				);
		}

		$products_for_template = [];
		foreach ($pidsQuery as $productId) {
			$products_for_template[] = $presenter->present(
					$presentationSettings,
					$assembler->assembleProduct(['id_product' => $productId]),
					$this->context->language
			);
		}
		return $products_for_template;
	}

	public function getBlogPostsForTemplate($limit = 0)
	{
		$blogInstalled = Ets_pnf_tools::isBlogInstalled();
		if (!$blogInstalled['blog'] && !$blogInstalled['blogFree']) {
			return [];
		}

		$postIdsConf = explode(',', $blogInstalled['blog'] ? Configuration::get('ETS_PNF_BLOGS', '') : Configuration::get('ETS_PNF_BLOGS_FREE', ''));
		$postIds = [];
		$added = 0;
		foreach ($postIdsConf as $pid) {
			if (!is_numeric($pid)) {
				continue;
			}
			$pid = (int)$pid;
			if ($pid <= 0 || in_array($pid, $postIds)) {
				continue;
			}
			if ($limit > 0 && $added >= $limit) {
				continue;
			}
			$postIds[] = $pid;
			$added++;
		}
		if (!$postIds) {
			return [];
		}

		$postIdsStr = implode(',', $postIds);
		$path = Ets_pnf_tools::getBlogImgPath();

		if ($blogInstalled['blog']) {
			/** @var Ybc_blog */
			$blogModule = Module::getInstanceByName('ybc_blog');
			$posts = Ybc_blog_post_class::getPostsWithFilter(' AND p.enabled=1 AND p.id_post IN(' . pSQL($postIdsStr) . ')');
			if ($posts) {
				foreach ($posts as &$post) {
					$post['link'] = $blogModule->getLink('blog', array('id_post' => $post['id_post']));
					if ($post['thumb']) {
						$post['thumb'] = $this->context->link->getMediaLink($path . $post['thumb']);
					}
					$post['comments_num'] = Ybc_blog_comment_class::countCommentsWithFilter(' AND bc.id_post=' . (int)$post['id_post'] . ' AND approved=1');
					$post['liked'] = method_exists($blogModule, 'isLikedPost') ? $blogModule->isLikedPost($post['id_post']) : false;
					$postExtra = Ets_pnf_defines::getBlogPostExtra($post['id_post']);
					$post['click_number'] = isset($postExtra['click_number']) ? (int)$postExtra['click_number'] : 0;
					$post['likes'] = isset($postExtra['likes']) ? (int)$postExtra['likes'] : 0;
				}
			}
		} else {
			/** @var Ets_blog */
			$blogModule = Module::getInstanceByName('ets_blog');
			$posts = Ets_blog_post::getPostsWithFilter(' AND p.enabled=1 AND p.id_post IN(' . pSQL($postIdsStr) . ')');
			if ($posts) {
				foreach ($posts as &$post) {
					$post['link'] = $blogModule->getLink('blog', array('id_post' => $post['id_post']));
					if ($post['thumb']) {
						$post['thumb'] = $this->context->link->getMediaLink($path . $post['thumb']);
					}
					$post['comments_num'] = Ets_blog_comment::countCommentsWithFilter(' AND bc.id_post=' . (int)$post['id_post'] . ' AND approved=1');
					$post['liked'] = method_exists($blogModule, 'isLikedPost') ? $blogModule->isLikedPost($post['id_post']) : false;
					$postExtra = Ets_pnf_defines::getBlogPostExtra($post['id_post']);
					$post['click_number'] = isset($postExtra['click_number']) ? (int)$postExtra['click_number'] : 0;
					$post['likes'] = isset($postExtra['likes']) ? (int)$postExtra['likes'] : 0;
				}
			}
		}
		return $posts;
	}

	public function getConfigsValues() {
		$ets_pnf_defines = Ets_pnf_defines::getInstance();
		$configs = $ets_pnf_defines->getConfigs();
		$values = [];

		foreach ($configs as $key => $config) {
			if (isset($config['lang']) && $config['lang']) {
				$values[$key] = Configuration::get($key, $this->context->language->id);
			} else {
				if ($key == 'ETS_PNF_IMAGE') {
					$val = Configuration::get($key);
					if (!$val)
						$val = _PS_ETS_PNF_DEFAULT_IMAGE_;
					$values[$key] = $this->context->link->getMediaLink(_PS_ETS_PNF_IMG_ . $val);
				}
				elseif($key == 'ETS_PNF_CATEGORY') {
					$categories = Configuration::get($key);
					$results = [];
					if ($categories) {
						$categories = explode(',', $categories);
						foreach ($categories as $category) {
							$catArray = $ets_pnf_defines->getCategoryById($category, 'small');
							if ($catArray) {
								$results[] = $catArray;
							}
						}
					}
					$values[$key] = $results;
				}
				elseif($key == 'ETS_PNF_PRODUCTS') {
					$products   = Configuration::get($key);
					$productIds = $products ? explode(',', $products) : [];
					$values[$key] = [];
					foreach ($productIds as $pid) {
						$pid = (int)$pid;
						if (!in_array($pid, $values[$key])) {
							$values[$key][] = $pid;
						}
					}
				}
				elseif ($key == 'ETS_PNF_BLOGS' || $key == 'ETS_PNF_BLOGS_FREE') {
					$blogs = Configuration::get($key);
					$results = [];
					$isBlogFree = ($key == 'ETS_PNF_BLOGS_FREE');
					if ($blogs) {
						$blogs = explode(',', $blogs);
						foreach ($blogs as $blog) {
							$results[] = $ets_pnf_defines->getBlogById($blog, $isBlogFree);
						}
					}
					$values[$key] = $results;
				}
				else
					$values[$key] = Configuration::get($key);
			}
		}
		return $values;
	}

	public function getBlockProducts($products)
	{
		if (!$products)
			return false;
		if (!is_array($products)) {
			$IDs = explode(',', $products);
			$products = array();
			foreach ($IDs as $ID) {
				if ($ID && ($tmpIDs = explode('-', $ID))) {
					$products[] = array(
						'id_product' => $tmpIDs[0],
						'id_product_attribute' => !empty($tmpIDs[1]) ? $tmpIDs[1] : 0,
					);
				}
			}
		}
		if ($products) {
			$context = Context::getContext();
			$id_group = isset($context->customer->id) && $context->customer->id ? Customer::getDefaultGroupId((int)$context->customer->id) : (int)Group::getCurrent()->id;
			$group = new Group($id_group);
			$useTax = $group->price_display_method ? false : true;
			foreach ($products as &$product) {
				$p = new Product($product['id_product'], true, $this->context->language->id, $this->context->shop->id);
				$product['link_rewrite'] = $p->link_rewrite;
				$product['price'] = Tools::displayPrice($p->getPrice($useTax, $product['id_product_attribute'] ? $product['id_product_attribute'] : null));
				if (($oldPrice = $p->getPriceWithoutReduct(!$useTax, $product['id_product_attribute'] ? $product['id_product_attribute'] : null)) && $oldPrice != $product['price']) {
					$product['price_without_reduction'] = Tools::convertPrice($oldPrice);
				}
				if (isset($product['price_without_reduction']) && $product['price_without_reduction'] != $product['price']) {
					$product['specific_prices'] = $p->specificPrice;
				}
				if (isset($product['specific_prices']) && $product['specific_prices'] && $product['specific_prices']['to'] != '0000-00-00 00:00:00') {
					$product['specific_prices_to'] = $product['specific_prices']['to'];
				}
				$product['name'] = $p->name;
				$product['description_short'] = $p->description_short;
				$image = ($product['id_product_attribute'] && ($image = Ets_pnf_defines::getCombinationImageById($product['id_product_attribute'], $context->language->id))) ? $image : Product::getCover($product['id_product']);
				$product['link'] = $context->link->getProductLink($product, null, null, null, null, null, $product['id_product_attribute'] ? $product['id_product_attribute'] : 0);
				//if (!$this->is17 || $this->context->controller->controller_type == 'admin') {
				$product['add_to_cart_url'] = isset($context->customer) && $this->is17 ? $context->link->getAddToCartURL((int)$product['id_product'], (int)$product['id_product_attribute']) : '';
				$imageType = $this->getMmType('cart');
				$product['image'] = $context->link->getImageLink($p->link_rewrite, isset($image['id_image']) ? $image['id_image'] : 0, $imageType);
				$product['price_tax_exc'] = Product::getPriceStatic((int)$product['id_product'], false, (int)$product['id_product_attribute'], (!$useTax ? 2 : 6), null, false, true, $p->minimal_quantity);
				$product['available_for_order'] = $p->available_for_order;
				if ($product['id_product_attribute']) {
					$product['attributes'] = $p->getAttributeCombinationsById((int)$product['id_product_attribute'], $context->language->id);
				}
				//}
				$product['id_image'] = isset($image['id_image']) ? $image['id_image'] : 0;
				if ($this->is17 && $this->context->controller->controller_type != 'admin') {
					$product['image_id'] = $product['id_image'];
				}
				$product['is_available'] = $p->checkQty(1);
				$product['allow_oosp'] = Product::isAvailableWhenOutOfStock($p->out_of_stock);
				$product['show_price'] = $p->show_price;
				if (!$this->is17) {
					$product['out_of_stock'] = $p->out_of_stock;
					$product['id_category_default'] = $p->id_category_default;
					$product['ean13'] = $p->ean13;
				}
			}
			unset($context);
		}
		$controller = Tools::getValue('controller');
		if ($products && $this->context->controller->controller_type != 'admin' && $controller != 'AdminOrderManagerExports') {
			return $this->is17 ? $this->productsForTemplate($products, $this->context) : Product::getProductsProperties($this->context->language->id, $products);
		}
		return $products;
	}
	public function productsForTemplate($products)
	{
		if (!$products || !is_array($products))
			return array();
		$assembler = new ProductAssembler($this->context);
		$presenterFactory = new ProductPresenterFactory($this->context);
		$presentationSettings = $presenterFactory->getPresentationSettings();
		$presenter = new PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
			new PrestaShop\PrestaShop\Adapter\Image\ImageRetriever(
				$this->context->link
			),
			$this->context->link,
			new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter(),
			new PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever(),
			$this->context->getTranslator()
		);
		$products_for_template = array();
		foreach ($products as $item) {
			$products_for_template[] = $presenter->present(
				$presentationSettings,
				$assembler->assembleProduct($item),
				$this->context->language
			);
		}
		return $products_for_template;
	}

	public function getMmType($image_type)
	{
		if (!$image_type)
			return 'cart';
		return $this->is17 ? ImageType::getFormattedName($image_type) : self::getFormatedName($image_type);
	}

	public function getMultiValues($key)
	{
		return ($fields = Tools::getValue($key)) ? (!in_array('all', $fields) ? implode(',', $fields) : 'all') : '';
	}
	public function saveConfigs()
	{
		$languages = Language::getLanguages(false);
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		$configs = Ets_pnf_defines::getInstance()->getConfigs();
		$files = array();
		if ($configs) {
			foreach ($configs as $key => $config) {
				if (isset($config['lang']) && $config['lang']) {
					$val_lang_default = Tools::getValue($key . '_' . $id_lang_default);
					if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim($val_lang_default) == '') {
						$this->_errors[] = sprintf($this->l('%s is required'), $config['label']);
					} elseif (isset($config['required_if_parent']) && $config['required_if_parent'] && isset($config['parent']) && $config['parent']) {
						$val_parent = Tools::getValue($config['parent']);
						if ($val_parent && trim($val_lang_default) == '') {
							$this->_errors[] = sprintf($this->l('%s is required'), $config['label']);
						} elseif (isset($config['validate']) && method_exists('Validate', $config['validate'])) {
							$validate = $config['validate'];
							if ($val_lang_default && !Validate::$validate(trim($val_lang_default))) {
								$this->_errors[] = sprintf($this->l('%s is invalid'), $config['label']);
							}
						}
					} elseif (isset($config['validate']) && method_exists('Validate', $config['validate'])) {
						$validate = $config['validate'];
						if ($val_lang_default && !Validate::$validate(trim($val_lang_default))) {
							$this->_errors[] = sprintf($this->l('%s is invalid'), $config['label']);
						}
					}
					foreach ($languages as $lang) {
						$val_lang = Tools::getValue($key . '_' . $lang['id_lang']);
						if ($val_lang && !Validate::isCleanHtml($val_lang))
							$this->_errors[] = sprintf($this->l('%s is not valid in %s'), $config['label'], $lang['iso_code']);
					}
				} else {
					$val = Tools::getValue($key);
					if (isset($config['required']) && $config['required'] && $config['type'] != 'switch' && trim($val) == '') {
						$this->_errors[] = sprintf($this->l('%s is required'), $config['label']);
					} elseif (isset($config['required_if_parent']) && $config['required_if_parent'] && isset($config['parent']) && $config['parent']) {
						$val_parent = Tools::getValue($config['parent']);
						if ($val_parent && trim($val) == '')
							$this->_errors[] = sprintf($this->l('%s is required'), $config['label']);
					} elseif (isset($config['validate']) && method_exists('Validate', $config['validate'])) {
						$validate = $config['validate'];
						if ($val && !Validate::$validate(trim($val)))
							$this->_errors[] = sprintf($this->l('%s is invalid'), $config['label']);
						unset($validate);
					} elseif (!is_array($val) && !Validate::isCleanHtml(trim($val))) {
						$this->_errors[] = sprintf($this->l('%s is invalid'), $config['label']);
					} elseif ($config['type'] == 'file' && isset($_FILES[$key]['size']) && $_FILES[$key]['size']) {
						$files[$key] = array();
						$fileSize = round((int)$_FILES[$key]['size'] / (1024 * 1024));
						$imagesize = @getimagesize($_FILES[$key]['tmp_name']);
						$type = Tools::strtolower(Tools::substr(strrchr($_FILES[$key]['name'], '.'), 1));
						$imageName = @file_exists(_PS_ETS_PNF_IMG_DIR_ . Tools::strtolower($_FILES[$key]['name'])) ? Tools::passwdGen() . '-' . Tools::strtolower($_FILES[$key]['name']) : Tools::strtolower($_FILES[$key]['name']);
						$fileName = _PS_ETS_PNF_IMG_DIR_ . $imageName;
						if ($fileSize > Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'))
							$this->_errors[] = sprintf($this->l('%s file is too large'), $config['label']);
						elseif (file_exists($fileName)) {
							$this->_errors[] = sprintf($this->l('%s file already existed'), $config['label']);
						} else {
							$errors = [];
							if (isset($_FILES[$key]) &&
								!empty($_FILES[$key]['tmp_name']) &&
								!empty($imagesize) &&
								in_array($type, array('jpg', 'gif', 'jpeg', 'png'))
							) {
								$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
								if (!$temp_name || !move_uploaded_file($_FILES[$key]['tmp_name'], $temp_name))
									$errors[] = sprintf($this->l('%s cannot upload image'), $config['label']);
								elseif (!ImageManager::resize($temp_name, $fileName, null, null, $type))
									$errors[] = printf($this->l('%s An error occurred during the image upload process.'), $config['label']);
								if (!$errors) {
									$files[$key] = $imageName;
								}
							} else
								$errors[] = sprintf($this->l('%s file is not in the correct format, accepted formats: jpg, gif, jpeg, png.'), $config['label']);
							if ($errors)
								$this->_errors = array_merge($this->_errors, $errors);
						}
					}
				}
			}
		}
		if (!$this->_errors) {
			if ($configs) {
				foreach ($configs as $key => $config) {
					if (isset($config['lang']) && $config['lang']) {
						$values = array();
						$val_lang_default = Tools::getValue($key . '_' . $id_lang_default);
						foreach ($languages as $lang) {
							$val_lang = Tools::getValue($key . '_' . $lang['id_lang']);
							$values[$lang['id_lang']] = trim($val_lang) ?: trim($val_lang_default);
						}
						$this->updateValues($config, $key, $values, true);
					} else {
						$val = Tools::getValue($key);
						if ($config['type'] == 'file') {
							if (isset($files[$key]) && $files[$key]) {
								$this->updateValues($config, $key, $files[$key]);
							}
						} else {
							if ($config['type'] == 'switch') {
								$this->updateValues($config, $key, (int)trim($val) ? 1 : 0, true);
							} elseif ($key == 'ETS_PNF_CATEGORY') {
								$this->updateValues($config, $key, implode(',', $val ?: array()), true);
							} else
								$this->updateValues($config, $key, trim($val), true);
						}
					}
				}
				$this->_html =  $this->displayConfirmation($this->l('Successful!')) . $this->_html;
			}
		}
	}

	public function getBaseLink()
	{
		$link = (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' : 'http://') . $this->context->shop->domain . $this->context->shop->getBaseURI();
		return trim($link, '/');
	}

	public function updateValues($config, $key, $value, $html = false)
	{
		if (isset($config['global']) && $config['global'])
			Configuration::updateGlobalValue($key, $value, $html);
		else
			Configuration::updateValue($key, $value, $html);
	}

	public function getAdminLink($conf = 0)
	{
		$args = ($conf ? '&conf=' . $conf : '') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . (($tab = Tools::getValue('control')) && in_array($tab, array('manager_order', 'order_export', 'settings')) ? '&control=' . $tab : '');
		if (!$this->is15)
			return $this->context->link->getAdminLink('AdminModules', true) . $args;
		else
			return AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules') . $args;
	}

	public function getAdminHtml()
	{
		$this->smarty->assign(array(
			'pagenotfound_module_dir' => $this->_path,
			'pagenotfound_body_html' => $this->renderAdminBodyHtml(),
			'pagenotfound_error_message' => $this->errorMessage,
			'token' => md5($this->id),
			'module_link' => $this->getModuleLink()
		));
		return $this->display(__FILE__, 'admin.tpl');
	}

	public function getCustomers($customerIds)
	{
		if ($customerIds && ($ids = explode(',',$customerIds))) {
			return Ode_dbbase::getCustomerByIDs($ids);
		} else
			return array();
	}


	public function renderAdminBodyHtml()
	{
		$this->renderConfigForm();
		return $this->_html;
	}

	public function renderConfigForm()
	{
		$configs = Ets_pnf_defines::getInstance()->getConfigs();
		$fields_form = array(
			'form' => array(
				'id_form' => 'ets-pnf-config-form',
				'legend' => array(
					'title' => $this->l('General settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(),
				'submit' => array(
					'title' => $this->l('Save'),
				),
				'buttons' => array(
					'cancel' => array(
						'title' => $this->l('Cancel'),
						'href'  => $this->context->link->getAdminLink('AdminModules', true),
						'class' => 'ets-pnf-cancel'
					)
				)
			),
		);
		if ($configs) {
			foreach ($configs as $key => $config) {
				$arg = array(
					'name' => $key,
					'type' => $config['type'],
					'label' => $config['label'],
					'desc' => isset($config['desc']) ? $config['desc'] : false,
					'col' => isset($config['col']) ? $config['col'] : 8,
					'required' => isset($config['required']) && $config['required'] ? true : false,
					'options' => isset($config['options']) && $config['options'] ? $config['options'] : array(),
					'values' => $config['type'] == 'switch' ? array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					) : (isset($config['values']) ? $config['values'] : false),
					'lang' => isset($config['lang']) ? $config['lang'] : false,
					'tab' => isset($config['tab']) && $config['tab'] ? $config['tab'] : 'general',
					'suffix' => isset($config['suffix']) && $config['suffix'] ? $config['suffix'] : false,
					'autoload_rte' => isset($config['autoload_rte']) ? $config['autoload_rte'] : false,
					'default' => isset($config['default']) ? $config['default'] : false,
					'form_group_class' => isset($config['form_group_class']) ? $config['form_group_class'] : '',
					'autocomplete' => isset($config['autocomplete']) ? $config['autocomplete'] : true,
					'type_search' => isset($config['type_search']) ? $config['type_search'] : '',
					'placeholder' => isset($config['placeholder']) ? $config['placeholder'] : '',
					'isShow' => (isset($config['parent']) && $config['parent']) ? ((int)Configuration::get($config['parent']) ? 'ets_pnf_show' : 'ets_pnf_hide') : '',
					'parent' => (isset($config['parent']) && $config['parent']) ? $config['parent'] : '',
					'tree' => (isset($config['tree']) && $config['tree']) ? $config['tree'] : [],
				);
				if (isset($arg['suffix']) && !$arg['suffix'])
					unset($arg['suffix']);

				if (Tools::isSubmit('saveConfig'))
				{

					if (isset($config['required_if_parent']) && $config['required_if_parent']) {
						$val_parent = Tools::getValue($config['parent'], isset($config['default']) ? $config['default'] : 1);
						$arg['required'] = $val_parent;
					}
					if (isset($config['parent']) && $config['parent']) {
						$arg['isShow'] = (int) Tools::getValue($config['parent']) ? 'ets_pnf_show' : 'ets_pnf_hide';
					}
					if ($config['type'] == 'categories') {
						$val = Tools::getValue($key, isset($config['default']) ? explode(',', $config['default']) : array());
						$arg['tree']['selected_categories'] = $val;
					}
					else if ($config['type'] == 'file') {
						$arg['display_img'] = $this->context->link->getMediaLink(_PS_ETS_PNF_IMG_.(Tools::getValue($key) ?: (Configuration::get($key) ?: _PS_ETS_PNF_DEFAULT_IMAGE_)));
					}
				}
				else
				{
					if (isset($config['required_if_parent']) && $config['required_if_parent']) {
						$val_parent = Configuration::get($config['parent']);
						$arg['required'] = $val_parent;
					}
					if (isset($config['parent']) && $config['parent']) {
						$arg['isShow'] = (int) Configuration::get($config['parent']) ? 'ets_pnf_show' : 'ets_pnf_hide';
					}
					if ($config['type'] == 'categories') {
						$val = Configuration::get($key) != '' ? explode(',', Configuration::get($key)) : array();
						$arg['tree']['selected_categories'] = $val;
					}
					else if ($config['type'] == 'file') {
						$arg['display_img'] = $this->context->link->getMediaLink(_PS_ETS_PNF_IMG_.(Configuration::get($key) ?: _PS_ETS_PNF_DEFAULT_IMAGE_));
					}
				}
				$fields_form['form']['input'][] = $arg;
			}
		}
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'saveConfig';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name . '&control=settings';
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$fields = array();
		$languages = Language::getLanguages(false);
		$helper->override_folder = '/';
		if (Tools::isSubmit('saveConfig'))
		{
			if ($configs) {
				foreach ($configs as $key => $config) {
					if (isset($config['lang']) && $config['lang']) {
						foreach ($languages as $l) {
							$fields[$key][$l['id_lang']] = Tools::getValue($key . '_' . $l['id_lang'], isset($config['default']) ? $config['default'] : '');
						}
					} elseif ($config['type'] == 'categories') {
						$fields[$key] = Tools::getValue($key, isset($config['default']) ? explode(',', $config['default']) : array());
					} else {
						$fields[$key] = Tools::getValue($key, isset($config['default']) ? $config['default'] : '');
					}
				}
			}
		}
		else
		{
			if ($configs) {
				foreach ($configs as $key => $config) {
					if (isset($config['lang']) && $config['lang']) {
						foreach ($languages as $l) {
							$fields[$key][$l['id_lang']] = Configuration::get($key, $l['id_lang']);
						}
					} elseif ($config['type'] == 'categories') {
						$fields[$key] = Configuration::get($key) != '' ? explode(',', Configuration::get($key)) : array();
					} else {
						$fields[$key] = Configuration::get($key);
						if ($key == 'ETS_PNF_IMAGE' && !$fields[$key])
							$fields[$key] = _PS_ETS_PNF_DEFAULT_IMAGE_;
					}
				}
			}
		}
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $fields,
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'isConfigForm' => true,
			'image_baseurl' => $this->_path . 'views/img/',
			'path_uri' => $this->getPathUri(),
			'path_local' => $this->getLocalPath(),
			'domain' => Tools::getShopDomainSsl(true, true),
			'is15' => $this->is15,
			'link' => $this->context->link,
			'time_zone' => date_default_timezone_get(),
			'time_now' => date('Y-m-d H:i:s'),
			'php_path' => (defined('PHP_BINDIR') && PHP_BINDIR && is_string(PHP_BINDIR) ? PHP_BINDIR . '/' : '') . 'php ',
			'refsLink' => isset($this->refs) ? $this->refs . $this->context->language->iso_code : false,
		);
		$this->_html .= $helper->generateForm(array('form' => $fields_form));
	}

	public static function getBaseModLink()
	{
		$context = Context::getContext();
		return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE') ? 'https://' : 'http://') . $context->shop->domain . $context->shop->getBaseURI();
	}
	public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 60)
	{
		if ($stream_context == null && preg_match('/^https?:\/\//', $url)) {
			$stream_context = stream_context_create(array(
				"http" => array(
					"timeout" => $curl_timeout,
					"max_redirects" => 101,
					"header" => 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36'
				),
				"ssl" => array(
					"allow_self_signed" => true,
					"verify_peer" => false,
					"verify_peer_name" => false,
				),
			));
		}
		if (function_exists('curl_init')) {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => html_entity_decode($url),
				CURLOPT_USERAGENT => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/77.0.3865.90 Safari/537.36',
				CURLOPT_SSL_VERIFYHOST => false,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_TIMEOUT => $curl_timeout,
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_FOLLOWLOCATION => true,
			));
			$content = curl_exec($curl);
			curl_close($curl);
			return $content;
		} elseif (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url)) {
			return Tools::file_get_contents($url, $use_include_path, $stream_context);
		} else {
			return false;
		}
	}

	public function getModuleLink()
	{
		if (!(isset($this->baseAdminPath)) || !$this->baseAdminPath) {
			$this->baseAdminPath = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		}
		return $this->baseAdminPath;
	}


	public function addJquery()
	{
		if (version_compare(_PS_VERSION_, '1.7.6.0', '>=') && version_compare(_PS_VERSION_, '1.7.7.0', '<'))
			$this->context->controller->addJS(_PS_JS_DIR_ . 'jquery/jquery-' . _PS_JQUERY_VERSION_ . '.min.js');
		else
			$this->context->controller->addJquery();
	}


	public function hookDisplayBackOfficeHeader()
	{
		$configure = Tools::getValue('configure');
		$controller = Tools::getValue('controller');

		if (($configure == $this->name && $controller == 'AdminModules')) {
			$this->context->controller->addCSS($this->_path . 'views/css/admin.css');
			if ($this->is15) {
				$this->context->controller->addCSS($this->_path . 'views/css/admin15.css');
			}
			if ($this->is8) {
				if (Module::isEnabled('ps_edition_basic')) {
					$this->context->controller->addCSS($this->_path . 'views/css/admin8e.css');
				}
			}
			$this->addJquery();
			$blogInstalled =  Ets_pnf_tools::isBlogInstalled();
			Media::addJsDef(array(
				'etsPNFBlogInstalled' => $blogInstalled['blog'] || $blogInstalled['blogFree'],
				'etsPNFBlogFree' => ($blogInstalled['blogFree'] && !$blogInstalled['blog'])
			));
			$this->context->controller->addJs($this->_path . 'views/js/admin.js');
		}
		if ($this->_errors && !$this->context->controller->errors)
			$this->context->controller->errors = $this->_errors;
	}

	public static function getFormatedName($name)
	{
		$theme_name = Context::getContext()->shop->theme_name;
		$name_without_theme_name = str_replace(array('_'.$theme_name, $theme_name.'_'), '', $name);

		//check if the theme name is already in $name if yes only return $name
		if (strstr($name, $theme_name) && ImageType::getByNameNType($name)) {
			return $name;
		} elseif (ImageType::getByNameNType($name_without_theme_name.'_'.$theme_name)) {
			return $name_without_theme_name.'_'.$theme_name;
		} elseif (ImageType::getByNameNType($theme_name.'_'.$name_without_theme_name)) {
			return $theme_name.'_'.$name_without_theme_name;
		} else {
			return $name_without_theme_name.'_default';
		}
	}
	public function getTextLang($text, $lang, $file_name = '')
	{
		if (is_array($lang))
			$iso_code = $lang['iso_code'];
		elseif (is_object($lang))
			$iso_code = $lang->iso_code;
		else {
			$language = new Language($lang);
			$iso_code = $language->iso_code;
		}
		$modulePath = rtrim(_PS_MODULE_DIR_, '/') . '/' . $this->name;
		$fileTransDir = $modulePath . '/translations/' . $iso_code . '.' . 'php';
		if (!@file_exists($fileTransDir)) {
			return $text;
		}
		$fileContent = Tools::file_get_contents($fileTransDir);
		$text_tras = preg_replace("/\\\*'/", "\'", $text);
		$strMd5 = md5($text_tras);
		$keyMd5 = '<{' . $this->name . '}prestashop>' . ($file_name ?: $this->name) . '_' . $strMd5;
		preg_match('/(\$_MODULE\[\'' . preg_quote($keyMd5) . '\'\]\s*=\s*\')(.*)(\';)/', $fileContent, $matches);
		if ($matches && isset($matches[2])) {
			return $matches[2];
		}
		return $text;
	}

	public static function validateArray($array, $validate = 'isCleanHtml')
	{
		if (!is_array($array))
			return true;
		if (method_exists('Validate', $validate)) {
			if ($array && is_array($array)) {
				$ok = true;
				foreach ($array as $val) {
					if (!is_array($val)) {
						if ($val && !Validate::$validate($val)) {
							$ok = false;
							break;
						}
					} else
						$ok = self::validateArray($val, $validate);
				}
				return $ok;
			}
		}
		return true;
	}
	public static function registerPlugins(){
		if(version_compare(_PS_VERSION_, '8.0.4', '>='))
		{
			$smarty = Context::getContext()->smarty->_getSmartyObj();
			if(!isset($smarty->registered_plugins[ 'modifier' ][ 'implode' ]))
				Context::getContext()->smarty->registerPlugin('modifier', 'implode', 'implode');
			if(!isset($smarty->registered_plugins[ 'modifier' ][ 'strpos' ]))
				Context::getContext()->smarty->registerPlugin('modifier', 'strpos', 'strpos');
		}
	}

	public function displayIframe()
	{
		switch($this->context->language->iso_code) {
			case 'en':
				$url = 'https://cdn.prestahero.com/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
				break;
			case 'it':
				$url = 'https://cdn.prestahero.com/it/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
				break;
			case 'fr':
				$url = 'https://cdn.prestahero.com/fr/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
				break;
			case 'es':
				$url = 'https://cdn.prestahero.com/es/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
				break;
			default:
				$url = 'https://cdn.prestahero.com/prestahero-product-feed?utm_source=feed_'.$this->name.'&utm_medium=iframe';
		}
		$this->smarty->assign(
			array(
				'url_iframe' => $url
			)
		);
		return $this->display(__FILE__,'iframe.tpl');
	}
}