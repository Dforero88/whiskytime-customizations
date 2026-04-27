{if isset($id_cart_rule)}
<div class="action_buttons" style="margin-top: 5px;">
    <!-- Bouton avec ID unique -->
    <button type="button" 
            class="btn btn-success download-pdf-btn"
            id="pdfBtn-{$id_cart_rule|intval}"
            data-id="{$id_cart_rule|intval}"
            data-link="{$module_link|escape:'html':'UTF-8'}">
        {if $ps_version <= 1.6}
            <i class="icon-file-pdf"></i>
        {else}
            <i class="material-icons">picture_as_pdf</i>
        {/if}
    </button>
</div>

<!-- Modal dédié à CE bouton -->
<div class="modal fade" id="pdfMessageModal-{$id_cart_rule|intval}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">{l s='Add Personal Message' mod='generatepdfgiftcard'}</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>{l s='Optional message to include in PDF:' mod='generatepdfgiftcard'}</label>
                    <textarea class="form-control pdf-custom-message" rows="4" maxlength="150"
                              placeholder="{l s='Enter your personal message here...' mod='generatepdfgiftcard'}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    {l s='Cancel' mod='generatepdfgiftcard'}
                </button>
                <button type="button" class="btn btn-primary confirm-pdf-download" 
                        data-id="{$id_cart_rule|intval}">
                    {l s='Download PDF' mod='generatepdfgiftcard'}
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// JavaScript SCOPÉ à ce bouton spécifique
(function() {
    var buttonId = {$id_cart_rule|intval};
    var button = document.getElementById('pdfBtn-' + buttonId);
    var modal = document.getElementById('pdfMessageModal-' + buttonId);
    
    if (!button || !modal) return;
    
    // Gestion du clic sur le bouton principal
    button.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Affiche le modal correspondant
        $('#pdfMessageModal-' + buttonId).modal('show');
    }, false); // false pour la phase de bubbling
    
    // Gestion du bouton de confirmation dans le modal
    var confirmBtn = modal.querySelector('.confirm-pdf-download');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var customMessage = modal.querySelector('.pdf-custom-message').value;
            var moduleLink = button.getAttribute('data-link');
            
            // Crée UN SEUL formulaire
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = moduleLink;
            form.target = '_blank';
            form.style.display = 'none';
            
            var inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'id_cart_rule';
            inputId.value = buttonId;
            form.appendChild(inputId);
            
            var inputMessage = document.createElement('input');
            inputMessage.type = 'hidden';
            inputMessage.name = 'custom_message';
            inputMessage.value = customMessage;
            form.appendChild(inputMessage);
            
            // Supprime d'abord tout formulaire existant avec le même ID
            var existingForm = document.getElementById('pdfForm-' + buttonId);
            if (existingForm) {
                existingForm.remove();
            }
            
            form.id = 'pdfForm-' + buttonId;
            document.body.appendChild(form);
            form.submit();
            
            // Ferme le modal
            $('#pdfMessageModal-' + buttonId).modal('hide');
            
            // Nettoie après un court délai
            setTimeout(function() {
                if (form.parentNode) {
                    form.remove();
                }
            }, 1000);
            
        }, false);
    }
    
    // Nettoie le textarea quand le modal se ferme
    $(modal).on('hidden.bs.modal', function() {
        var textarea = this.querySelector('.pdf-custom-message');
        if (textarea) {
            textarea.value = '';
        }
    });
})();
</script>
{/if}