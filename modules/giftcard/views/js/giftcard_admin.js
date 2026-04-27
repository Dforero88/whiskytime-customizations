/*
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    FMM Modules
 *  @copyright 2021 FMM Modules
 *  @license   FMM Modules
*/

function showGiftDetails(id_product, id_order) {
    var detailRow = $(`#gift-card-${id_product}-${id_order}`);
    detailRow.css('display', (detailRow.css('display') == 'table-row') ? "none" : "table-row");
}