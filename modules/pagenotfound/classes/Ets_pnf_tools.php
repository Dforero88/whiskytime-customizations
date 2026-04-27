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
if (!defined('_PS_VERSION_')) {
	exit;
}

class Ets_pnf_tools
{
	private static $_blogInstalled = [];

	/**
	 * Merges user defined arguments into defaults array.
	 *
	 * @param array $args     Value to merge with $defaults.
	 * @param array $defaults Optional. Array that serves as the defaults. Default empty array.
	 */
	public static function parseArgs($args = [], $defaults = [])
	{
		if (is_array($args)) {
			$parsed_args = &$args;
		} else {
			$parsed_args = [];
		}

		if (is_array($defaults) && $defaults) {
			return array_merge($defaults, $parsed_args);
		}
		return $parsed_args;
	}

	/**
	 * @param array $args [
	 *   @var string $tag
	 *   @var string $class
	 *   @var string $id
	 *   @var array  $atts
	 *   @var string $content,
	 *   @var string $emptyTagBefore
	 *   @var string $emptyTagAfter
	 * ]
	 */
	public static function html($args = [])
	{
		$args = self::parseArgs($args, [
			'tag'            => 'div',
			'class'          => '',
			'id'             => '',
			'atts'           => [],
			'content'        => '',
			'emptyTagBefore' => '',
			'emptyTagAfter'  => ''
		]);

		$emptyTags = ['br', 'hr'];
		$selfCloseTags = ['img', 'input', 'path'];
		$maybeSelfClosedTags = ['link'];
		$allowedTags = [
			'a',
			'cite', 'code', 'col', 'colgroup',
			'data', 'datalist', 'dd', 'del', 'details', 'dfn', 'dialog', 'dir', 'div', 'dl', 'dt',
			'em', 'embed',
			'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'frame', 'frameset',
			'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html',
			'i', 'iframe', 'img', 'input', 'ins',
			'kbd',
			'label', 'legend', 'li', 'link',
			'main', 'map', 'mark', 'menu', 'meta', 'meter',
			'nav', 'noframes', 'noscript',
			'object', 'ol', 'optgroup', 'option', 'output',
			'p', 'param', 'picture', 'pre', 'progress',
			'q',
			'rp', 'rt', 'ruby',
			's', 'samp', 'script', 'search', 'section', 'select', 'small', 'source', 'span', 'strike', 'strong', 'style', 'sub', 'summary', 'sup', 'svg',
			'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'title', 'tr', 'track', 'tt',
			'u', 'ul',
			'var', 'video',
			'wbr'
		];

		$start = $end = '';
		$attributes = [];
		if ($args['class']) {
			$attributes[] = sprintf('class="%s"', htmlspecialchars($args['class'], ENT_QUOTES));
		}
		if ($args['id']) {
			$attributes[] = sprintf('id="%s"', htmlspecialchars($args['id'], ENT_QUOTES));
		}
		if (is_array($args['atts']) && $args['atts']) {
			foreach ($args['atts'] as $attrName => $attrVal) {
				if ($attrName == 'class' && $attrName == 'id') {
					continue;
				}
				$attributes[] = sprintf(
					'%1$s="%2$s"',
					$attrName,
					htmlspecialchars($attrVal, ENT_QUOTES)
				);
			}
		}

		if ($args['tag'] && in_array($args['tag'], $allowedTags)) {
			$start = '<' . $args['tag'] . ($attributes ? ' ' . implode(' ', $attributes) : '');
			if (
				in_array($args['tag'], $selfCloseTags)
				|| in_array($args['tag'], $emptyTags)
				|| (in_array($args['tag'], $maybeSelfClosedTags) && !$args['content'])
			) {
				$end = '/' . '>';
			} else {
				$start .= '>';
				$end = '<' . '/' . $args['tag'] . '>';
			}
		}

		if ($args['emptyTagBefore'] && in_array($args['emptyTagBefore'], $emptyTags)) {
			$start .= '<' . $args['emptyTagBefore'] . '/' . '>';
		}

		if ($args['emptyTagAfter'] && in_array($args['emptyTagAfter'], $emptyTags)) {
			$end = '<' . $args['emptyTagAfter'] . '/' . '>' . $end;
		}

		return $start . $args['content'] . $end;
	}

	/**
	 * @param bool  $checkOnly Default false, check if blog or blog free is installed.
	 * @return bool If $checkOnly = true;
	 * @return array {
	 *   @type bool $blog ybc_blog is installedf
	 *   @type bool $blogFree ybc_blog_free is installed
	 * } - This array is returned when $checkOnly = false;
	 */
	public static function isBlogInstalled($checkOnly = false)
	{
		if (!self::$_blogInstalled) {
			self::$_blogInstalled = [
				'blog'     => Module::isEnabled('ybc_blog'),
				'blogFree' => Module::isEnabled('ets_blog')
			];
			if (self::$_blogInstalled['blog'] || self::$_blogInstalled['blogFree']) {
				self::$_blogInstalled['installed'] = true;
			} else {
				self::$_blogInstalled['installed'] = false;
			}
		}
		if ($checkOnly) {
			return self::$_blogInstalled['installed'];
		}
		return self::$_blogInstalled;
	}

	public static function uniqIdsFromStr($string = '')
	{
		$string = trim($string);
		if (!$string || !Validate::isCleanHtml($string)) {
			return [];
		}
		$arr = explode(',', $string);
		$res = [];
		foreach ($arr as $item) {
			if (!is_numeric($item)) {
				continue;
			}
			$item = (int)$item;
			if (in_array($item, $res)) {
				continue;
			}
			$res[] = $item;
		}
		return $res;
	}

	public static function getBlogImgPath()
	{
		$blogInstalled = self::isBlogInstalled();
		if (!$blogInstalled['installed']) {
			return '';
		}
		if ($blogInstalled['blog'] && defined('_PS_YBC_BLOG_IMG_')) {
			return _PS_YBC_BLOG_IMG_ . 'post/thumb/';
		}
		if ($blogInstalled['blogFree'] && defined('_PS_ETS_BLOG_IMG_')) {
			return _PS_ETS_BLOG_IMG_ . 'post/';
		}
	}

	public static function getBlogModuleInstance($type = 'blog')
	{
		$blogInstalled = self::isBlogInstalled();
		if ('blog' == $type && $blogInstalled['blog']) {
			return Module::getInstanceByName('ybc_blog');
		}
		if ('blogFree' == $type && $blogInstalled['blogFree']) {
			return Module::getInstanceByName('ets_blog');
		}
		return null;
	}
}
