{*
 * Events Manager
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    FMM Modules
 * @copyright Copyright 2017 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  FMM Modules
  *}
{extends file="helpers/form/form.tpl"}
{block name="fieldset"}
	{include file='../../../menu.tpl'}
	<div class="col-lg-10 panel" id="eventmanager">
			{$smarty.block.parent}
    </div>
	<div class="clearfix"> </div>
{/block}