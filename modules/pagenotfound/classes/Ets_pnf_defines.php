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

if (!defined('_ETS_PNF_MODULE_'))
	exit;

class Ets_pnf_defines {
	public static $instance;
	public $context;
	public $smarty;
	private $configs;

	public function __construct() {
		$this->context = Context::getContext();
		if (is_object($this->context->smarty)) {
			$this->smarty = $this->context->smarty;
		}
		if (!$this->configs)
			$this->setConfigs();
	}

	public static function getInstance()
	{
		if (!(isset(self::$instance)) || !self::$instance) {
			self::$instance = new Ets_pnf_defines();
		}
		return self::$instance;
	}

	public static function getHooks() {
		return [
			'displayBackOfficeHeader',
			'displayHeader',
			'displayEtsPnfProductList',
			'displayEtsPnfBlogList',
			'displayEtsPnfBlogListFree',
			'displayOverrideTemplate',
		];
	}

	public function setConfigs() {
		if (!(isset($this->configs)) || !$this->configs) {
			$blogInstalled = Ets_pnf_tools::isBlogInstalled();
			$blogDesc = str_replace(
				['[blog]', '[blog_free]'],
				[
					Ets_pnf_tools::html(['tag' => 'a', 'atts' => ['href' => 'https://prestahero.com/91-prestashop-blog-module.html', 'target' => '_blank'], 'content' => $this->l('Blog')]),
					Ets_pnf_tools::html(['tag' => 'a', 'atts' => ['href' => 'https://prestahero.com/186-free-prestashop-blog-module.html', 'target' => '_blank'], 'content' => $this->l('Simple Blog')])
				],
				$this->l('This section is specifically designed to offer support for PrestaHero\'s [blog] and [blog_free] modules. Please enable respective module if you\'ve already installed. If you\'ve installed and enabled two modules at once, only posts from [blog] module will be used.')
			);
			$this->configs = [
				'ETS_PNF_ACTIVE' => [
					'label' => $this->l('Activate'),
					'type' => 'switch',
					'name' => 'ets_pnf_active',
					'default' => 1,
				],
				'ETS_PNF_IMAGE' => [
					'label' => $this->l('Upload image'),
					'type' => 'file',
					'fileType' => 'image',
					'validType' => ['jpg', 'png', 'gif', 'jpeg'],
					'default' => _PS_ETS_PNF_DEFAULT_IMAGE_,
					'desc' => sprintf($this->l('Accepted formats: jpg, png, gif, jpeg. Limit: %sMB. Recommended size: 600x350 px'), Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE')),
					'name' => 'ets_pnf_image',
				],
				'ETS_PNF_CONTENT' => [
					'label' => $this->l('"Page not found" content'),
					'type' => 'textarea',
					'autoload_rte' => true,
					'lang' => true,
					'default' => Ets_pnf_tools::html(['tag' => 'h1', 'content' => $this->l('Page Not Found')]) .
						Ets_pnf_tools::html(['tag' => 'h4', 'content' => $this->l('Oops!')]) .
						Ets_pnf_tools::html(['tag' => 'p', 'content' => $this->l('We\'re sorry, but the page you requested could not be found.')]) .
                        Ets_pnf_tools::html(['tag' => 'p', 'content' => $this->l('Here are a few things you can try: check the URL for typos or try using a different search term. If you are still having trouble, please contact us for assistance.')]) .
						Ets_pnf_tools::html([
							'tag' => 'p',
							'content' => Ets_pnf_tools::html([
									'tag' => 'span',
									'class' => 'material-icons',
									'content' => 'help'
								]) . ' ' . $this->l('FAQ')
							]
						) .
						Ets_pnf_tools::html([
							'tag' => 'p',
							'content' => Ets_pnf_tools::html([
									'tag' => 'span',
									'class' => 'material-icons',
									'content' => 'email'
								]) . ' ' . $this->l('Send support request')
							]
						),
					'name' => 'ets_pnf_content',
				],
				'ETS_PNF_HOMEPAGE_BUTTON' => [
					'label' => $this->l('Display "Go to homepage" button'),
					'type' => 'switch',
					'default' => 1,
					'name' => 'ets_pnf_homepage_button',
				],
				'ETS_PNF_CONTACT_BUTTON' => [
					'label' => $this->l('Display "Contact us" button'),
					'type' => 'switch',
					'default' => 1,
					'name' => 'ets_pnf_contact_button',
				],
				'ETS_PNF_CONTACT_LINK' => [
					'label' => $this->l('"Contact us" link'),
					'type' => 'text',
					'lang' => true,
					'parent' => 'ETS_PNF_CONTACT_BUTTON',
					'default' => 'https://yourwebsite.com/iso-lang/contact-us',
					'name' => 'ets_pnf_contact_link',
					'validate' => 'isAbsoluteUrl',
					'required_if_parent' => true
				],
				'ETS_PNF_DISPLAY_CATEGORY' => [
					'label' => $this->l('Display category below "Page not found" notification'),
					'type' => 'switch',
					'default' => 1,
					'name' => 'ets_pnf_display_category',
				],
				'ETS_PNF_TITLE_CATEGORY' => [
					'label' => $this->l('Title for category block'),
					'type' => 'text',
					'default' => $this->l('Discover Our Best Collections'),
					'lang' => true,
					'parent' => 'ETS_PNF_DISPLAY_CATEGORY',
					'name' => 'ETS_PNF_TITLE_CATEGORY',
				],
				'ETS_PNF_CATEGORY' => [
					'label' => $this->l('Select categories'),
					'type' => 'categories',
					'tree' => array(
						'id' => 'categories-tree',
						'selected_categories' => [],
						'root_category' => $this->context->shop->getCategory(),
						'use_checkbox' => true,
						'use_search' => true,
					),
					'class' => 'category',
					'name' => 'ets_pnf_category',
					'parent' => 'ETS_PNF_DISPLAY_CATEGORY'
				],
				'ETS_PNF_DISPLAY_PRODUCTS' => [
					'label' => $this->l('Display products below "Page not found" notification'),
					'type' => 'switch',
					'default' => 1,
					'name' => 'ets_pnf_display_products',
				],
				'ETS_PNF_TITLE_PRODUCTS' => [
					'label' => $this->l('Title for products block'),
					'type' => 'text',
					'default' => $this->l('Explore Our Products'),
					'lang' => true,
					'name' => 'ets_pnf_title_products',
					'parent' => 'ETS_PNF_DISPLAY_PRODUCTS'
				],
				'ETS_PNF_PRODUCTS' => [
					'label' => $this->l(''),
					'type' => 'search',
					'type_search' => 'product',
					'default' => [],
					'name' => 'ets_pnf_products',
					'placeholder' => $this->l('Search for product by name, reference or ID'),
					'parent' => 'ETS_PNF_DISPLAY_PRODUCTS'
				],
				'ETS_PNF_DISPLAY_BLOGS' => [
					'label' => $this->l('Display blog posts below "Page not found" notification'),
					'type' => 'switch',
					'default' => 1,
					'name' => 'ets_pnf_display_blogs',
					'desc' => $blogDesc
				],
				'ETS_PNF_TITLE_BLOGS' => [
					'label' => $this->l('Title for blog posts block'),
					'type' => 'text',
					'default' => $this->l('Our Featured Blog Posts'),
					'lang' => true,
					'name' => 'ets_pnf_title_blogs',
					'parent' => 'ETS_PNF_DISPLAY_BLOGS',
					'form_group_class' => !Ets_pnf_tools::isBlogInstalled(true) ? 'hide' : ''
				],
				'ETS_PNF_BLOGS' => [
					'label' => $this->l(''),
					'type' => 'search',
					'type_search' => 'blog',
					'default' => [],
					'name' => 'ets_pnf_blogs',
					'placeholder' => $this->l('Search for blog by name, reference or ID'),
					'parent' => 'ETS_PNF_DISPLAY_BLOGS',
					'form_group_class' => $blogInstalled['blog'] ? '' : 'hide'
					
				],
				'ETS_PNF_BLOGS_FREE' => [
					'label' => $this->l(''),
					'type' => 'search',
					'type_search' => 'blogFree',
					'default' => [],
					'name' => 'ets_pnf_blogs_free',
					'placeholder' => $this->l('Search for blog by name, reference or ID'),
					'parent' => 'ETS_PNF_DISPLAY_BLOGS',
					'form_group_class' => !$blogInstalled['blog'] && $blogInstalled['blogFree'] ? '' : 'hide'
				],
			];
		}
	}

	public function getCategoryById($id, $default_img = 'category') {
		$imgName = $default_img ? '-' . ImageType::getFormattedName($default_img) : '';
		if ($id) {
			$category = new Category($id);
			if ($category->is_root_category || $category->isRootCategory()) {
				return [];
			}
			return [
				'id' => $id,
				'name' => $category->getName($this->context->language->id),
				'link' => $category->getLink(),
				'image' => $this->context->link->getMediaLink(_PS_IMG_ . 'c/' . $id . $imgName . '.jpg')
			];
		}
		return [];
	}

	public function getBlogById($id, $isBlogFree = true)
	{
		$blogInstalled = Ets_pnf_tools::isBlogInstalled();
		if (!$blogInstalled) {
			return [];
		}
		$blogModule = null;
		$path = Ets_pnf_tools::getBlogImgPath();
		$res  = [];
		if ($isBlogFree) {
			if ($blogInstalled['blogFree']) {
				/** @var Ets_blog $blogModule */
				$blogModule = Module::getInstanceByName('ets_blog');
				$sql = 'SELECT
						bp.`id_post`,
						bpl.`title`,
						bpl.`thumb`
					FROM `' . _DB_PREFIX_ . 'ets_blog_post` bp
						LEFT JOIN `' . _DB_PREFIX_ . 'ets_blog_post_lang` bpl
							ON (bpl.id_post = bp.id_post AND bpl.id_lang = ' . (int)Context::getContext()->language->id . ')
					WHERE bp.`id_post` = ' . (int) $id. ' GROUP BY bp.id_post';
				$res = Db::getInstance()->executeS($sql);
			}
		} else {
			if ($blogInstalled['blog']) {
				/** @var Ybc_blog $blogModule */
				$blogModule = Module::getInstanceByName('ybc_blog');
				$sql = 'SELECT bp.`id_post`, bpl.`title`, bpl.`thumb` FROM `' . _DB_PREFIX_ . 'ybc_blog_post` bp
					LEFT JOIN `' . _DB_PREFIX_ . 'ybc_blog_post_lang` bpl ON (bpl.id_post = bp.id_post AND bpl.id_lang = ' . (int)Context::getContext()->language->id . ')
					LEFT JOIN `' . _DB_PREFIX_ . 'ybc_blog_post_shop` bps ON (bps.id_post = bp.id_post AND bps.id_shop = ' . (int)Context::getContext()->shop->id . ')
					WHERE bp.`id_post` = ' . (int) $id. ' GROUP BY bp.id_post';
				$res = Db::getInstance()->executeS($sql);
			}
		}
		if ($res && $blogModule) {
			foreach ($res as &$r) {
				$r['link'] = $blogModule->getLink('blog', ['id_post' => $r['id_post']]);
				if (isset($r['thumb']) && $r['thumb']) {
					$r['thumb'] = $this->context->link->getMediaLink($path . $r['thumb']);
				}
			}
			return $res;
		}
		return [];
	}

	public function getCategories()
	{

		$cats = array();
		if ($results = Category::getCategories($this->context->language->id)) {
			foreach ($results as $cat) {
				foreach ($cat as $id_category => $sub_cat) {
					$cats[] = (int)$id_category;
					unset($sub_cat);
				}
			}
		}
		return $cats;
	}

	public function getConfigs() {
		return $this->configs;
	}

	public function l($string)
	{
		return Translate::getModuleTranslation(_ETS_PNF_MODULE_, $string, pathinfo(__FILE__, PATHINFO_FILENAME));
	}
	public static function checkEnableOtherShop($id_module)
	{
		$sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'module_shop` WHERE `id_module` = ' . (int) $id_module . ' AND `id_shop` NOT IN(' . implode(', ', Shop::getContextListShopID()) . ')';
		return Db::getInstance()->executeS($sql);
	}

	public function display($template)
	{
		/** @var PageNotFound $module */
		$module = Module::getInstanceByName('pagenotfound');
		if (!$module)
			return;
		return $module->display($module->getLocalPath(), $template);
	}

	public static function getProductByQuery($query, $excludedProductIds = null, $excludeVirtuals = false, $exclude_packs = false)
	{
		if (version_compare(_PS_VERSION_, '1.6.1.0', '<')) {
			$imgLeftJoin = ' LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_product` = p.`id_product`) ' . Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover = 1');
		} else {
			$imgLeftJoin = ' LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.id_shop=' . (int)Context::getContext()->shop->id . ') ';
		}
		$isID = (is_numeric($query) && (int)$query) ? true : false;
		$sql = 'SELECT p.`id_product`, pl.`link_rewrite`, p.`reference`, pl.`name`, image_shop.`id_image` id_image, il.`legend`, p.`cache_default_attribute`, p.`price`
			FROM `' . _DB_PREFIX_ . 'product` p
			' . Shop::addSqlAssociation('product', 'p') . '
					LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = p.id_product AND pl.id_lang = ' . (int)Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
			' . pSQL($imgLeftJoin) . '
			LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)Context::getContext()->language->id . ')
			WHERE ' . ($excludedProductIds ? 'p.`id_product` NOT IN(' . pSQL(implode(',', array_map('intval',$excludedProductIds) )) . ') AND ' : '') . ' (pl.name LIKE "%' . pSQL($query) . '%" OR p.reference LIKE "%' . pSQL($query) . '%"' . ($isID ? ' OR p.id_product = ' . (int)$query : '') . ')' .
			($excludeVirtuals ? ' AND NOT EXISTS (SELECT 1 FROM `' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
			($exclude_packs ? ' AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '') .
			($imgLeftJoin ? 'AND image_shop.cover = 1' : '') . ' GROUP BY p.id_product';
		return Db::getInstance()->executeS($sql);
	}

	public static function getBlogByQuery($query, $excludedBlogs = null)
	{
		$blogInstalled = Ets_pnf_tools::isBlogInstalled();
		if ($blogInstalled['blog'] || $blogInstalled['blogFree']) {
			$sql = '';
			$isID = (is_numeric($query) && (int)$query) ? true : false;
			if ($blogInstalled['blog']) {
				$sql = 'SELECT bp.`id_post`, bpl.`title`, bpl.`thumb` FROM `' . _DB_PREFIX_ . 'ybc_blog_post` bp
					LEFT JOIN `' . _DB_PREFIX_ . 'ybc_blog_post_lang` bpl ON (bpl.id_post = bp.id_post AND bpl.id_lang = ' . (int)Context::getContext()->language->id . ')
					LEFT JOIN `' . _DB_PREFIX_ . 'ybc_blog_post_shop` bps ON (bps.id_post = bp.id_post AND bps.id_shop = ' . (int)Context::getContext()->shop->id . ')
					WHERE ' . ($excludedBlogs ? 'bp.`id_post` NOT IN(' . pSQL(implode(',', array_map('intval', $excludedBlogs))) . ') AND ' : '') . ' (bpl.title LIKE "%' . pSQL($query) . '%"' . ($isID ? ' OR bp.id_post = ' . (int)$query : '') . ') GROUP BY bp.id_post';
			} else {
				$sql = 'SELECT
					bp.`id_post`,
					bpl.`title`,
					bpl.`thumb`
				FROM `' . _DB_PREFIX_ . 'ets_blog_post` bp
					LEFT JOIN `' . _DB_PREFIX_ . 'ets_blog_post_lang` bpl
						ON (bpl.id_post = bp.id_post AND bpl.id_lang = ' . (int)Context::getContext()->language->id . ')
				WHERE ' . ($excludedBlogs ? 'bp.`id_post` NOT IN(' . pSQL(implode(',', array_map('intval', $excludedBlogs))) . ') AND ' : '') .
					' (bpl.title LIKE "%' . pSQL($query) . '%"' . ($isID ? ' OR bp.id_post = ' . (int)$query : '') . ') GROUP BY bp.id_post';
			}
			if ($sql) {
				return Db::getInstance()->executeS($sql);
			}
		}
		return [];
	}

	public static function getBlogsByIds($ids = [], $type = 'blog')
	{
		if (!$ids) {
			return [];
		}
		$idsStr = implode(',', array_map('intval', $ids));
		$blogInstalled = Ets_pnf_tools::isBlogInstalled();
		$sql = '';
		if ($blogInstalled['blog']) {
			if ('blog' == $type) {
				$sql = 'SELECT bp.`id_post`, bpl.`title`, bpl.`thumb` FROM `' . _DB_PREFIX_ . 'ybc_blog_post' . '` bp
					LEFT JOIN `' . _DB_PREFIX_ . 'ybc_blog_post_lang' . '` bpl ON (bpl.id_post = bp.id_post AND bpl.id_lang = ' . (int)Context::getContext()->language->id . ')
					WHERE bp.`id_post` IN(' . pSQL($idsStr) . ') GROUP BY bp.id_post ORDER BY FIELD(bp.`id_post`,' . pSQL($idsStr) . ')';
			}
		} elseif ($blogInstalled['blogFree']) {
			if ('blogFree' == $type) {
				$sql = 'SELECT bp.`id_post`, bpl.`title`, bpl.`thumb` FROM `' . _DB_PREFIX_ . 'ets_blog_post'  . '` bp
					LEFT JOIN `' . _DB_PREFIX_ . 'ets_blog_post_lang' . '` bpl ON (bpl.id_post = bp.id_post AND bpl.id_lang = ' . (int)Context::getContext()->language->id . ')
					WHERE bp.`id_post` IN(' . pSQL($idsStr) . ') GROUP BY bp.id_post ORDER BY FIELD(bp.`id_post`,' . pSQL($idsStr) . ')';
			}
		}
		if ($sql) {
			return Db::getInstance()->executeS($sql);
		}
		return [];
	}

	public static function getBlogPostExtra($id_post = 0)
	{
		if ($id_post <= 0) {
			return;
		}
		$blogInstalled = Ets_pnf_tools::isBlogInstalled();
		if ($blogInstalled['blog'] || $blogInstalled['blogFree']) {
			$sql = '';
			if ($blogInstalled['blog']) {
				$sql = 'SELECT bp.`click_number`, bp.`likes` FROM `' . _DB_PREFIX_ . 'ybc_blog_post' . '` bp
					WHERE bp.`id_post` = ' . (int)$id_post;
			} else {
				// Ets_blog does not have this function
			}
			$fields = [];
			if ($sql) {
				$fields = Db::getInstance()->getRow($sql);
			}
			if (!$fields) {
				$fields = [
					'click_number' => 0,
					'likes' => 0
				];
			}
			return $fields;
		}
		return [];
	}

	public static function getCombinationsByIdProduct($id_product,$excludeIds=false)
	{
		$sql = 'SELECT pa.`id_product_attribute`, pa.`reference`, ag.`id_attribute_group`, pai.`id_image`, agl.`name` AS group_name, al.`name` AS attribute_name, NULL as `attribute`, a.`id_attribute`
			FROM `' . _DB_PREFIX_ . 'product_attribute` pa
			' . Shop::addSqlAssociation('product_attribute', 'pa') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_combination` pac ON pac.`id_product_attribute` = pa.`id_product_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON a.`id_attribute` = pac.`id_attribute`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int)Context::getContext()->language->id . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = ' . (int)Context::getContext()->language->id . ')
			LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_image` pai ON pai.`id_product_attribute` = pa.`id_product_attribute`
			WHERE pa.`id_product` = ' . (int)$id_product . '
			GROUP BY pa.`id_product_attribute`, ag.`id_attribute_group`
			ORDER BY pa.`id_product_attribute`';
		return Db::getInstance()->executeS($sql);
	}

	public static function getCombinationImageById($id_product_attribute, $id_lang)
	{
		if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
			return Product::getCombinationImageById($id_product_attribute, $id_lang);
		} else {
			if (!Combination::isFeatureActive() || !$id_product_attribute) {
				return false;
			}
			$result = Db::getInstance()->executeS('
                SELECT pai.`id_image`, pai.`id_product_attribute`, il.`legend`
                FROM `' . _DB_PREFIX_ . 'product_attribute_image` pai
                LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (il.`id_image` = pai.`id_image`)
                LEFT JOIN `' . _DB_PREFIX_ . 'image` i ON (i.`id_image` = pai.`id_image`)
                WHERE pai.`id_product_attribute` = ' . (int)$id_product_attribute . ' AND il.`id_lang` = ' . (int)$id_lang . ' ORDER by i.`position` LIMIT 1'
			);
			if (!$result) {
				return false;
			}
			return $result[0];
		}
	}
}