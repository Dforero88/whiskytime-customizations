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
 * @copyright Copyright 2021 © FMM Modules
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * @category  FMM Modules
 * @package   eventsmanager
*}

<div class="panel col-lg-10" style="margin-top: -5px;">
   <div class="panel" id="fieldset_0">
      <div class="panel-heading">
        <img src="../img/admin/add.gif" alt="PDF INVOICE">{l s='Ticket Setting' mod='eventsmanager'}
      </div>
      <div class="form-wrapper">
        <div class="form-group">
          <label class="control-label col-lg-3">
            {l s='Choose one of Pdf:' mod='eventsmanager'}
            </label><br>
                <input type="radio" name="pdf_status" id="pdf_status_on" value="1" {if isset($pdf_status) AND $pdf_status == 1}checked="checked"{/if}/>
                <label class="t" for="event_status_on">
                    {if $version < 1.6}
                        <img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='eventsmanager'}" title="{l s='Enabled' mod='eventsmanager'}" />
                    {else}
                        {l s='Pdf Invoice' mod='eventsmanager'}
                    {/if}
                </label>
                <input type="radio" name="pdf_status" id="pdf_status_off" value="0" {if isset($pdf_status) AND $pdf_status == 0}checked="checked"{/if}/>
                <label class="t" for="event_status_off">
                    {if $version < 1.6}
                        <img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='eventsmanager'}" title="{l s='Disabled' mod='eventsmanager'}" />
                    {else}
                        {l s='Custom Pdf as Ticket' mod='eventsmanager'}
                    {/if}
                </label>  
            </div>
            
            </div>
        </div>
    </div>
</div>        
