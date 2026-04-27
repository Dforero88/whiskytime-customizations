// Version robuste qui attend que la page soit chargée
document.addEventListener('DOMContentLoaded', function() {
    
    // Gestion des clics sur les boutons PDF
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('download-pdf') || 
            e.target.closest('.download-pdf')) {
            e.preventDefault();
            
            var button = e.target.classList.contains('download-pdf') ? 
                        e.target : e.target.closest('.download-pdf');
            
            var idCartRule = button.getAttribute('data-id-cart-rule');
            var moduleLink = button.getAttribute('data-module-link');
            
            console.log('PDF clicked:', idCartRule, moduleLink);
            
            // Cherche le modal associé
            var modal = document.getElementById('pdfMessageModal-' + idCartRule);
            if (modal) {
                // Bootstrap modal
                $(modal).modal('show');
                
                // Configure le bouton de confirmation
                var confirmBtn = modal.querySelector('.download-pdf-confirm');
                if (confirmBtn) {
                    confirmBtn.onclick = function() {
                        var messageInput = modal.querySelector('.pdf-custom-message');
                        var customMessage = messageInput ? messageInput.value : '';
                        
                        // Soumission du formulaire
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = moduleLink;
                        form.target = '_blank';
                        
                        var input1 = document.createElement('input');
                        input1.type = 'hidden';
                        input1.name = 'id_cart_rule';
                        input1.value = idCartRule;
                        
                        var input2 = document.createElement('input');
                        input2.type = 'hidden';
                        input2.name = 'custom_message';
                        input2.value = customMessage;
                        
                        form.appendChild(input1);
                        form.appendChild(input2);
                        document.body.appendChild(form);
                        form.submit();
                        
                        // Ferme le modal
                        $(modal).modal('hide');
                    };
                }
            } else {
                // Fallback: téléchargement direct
                window.open(moduleLink + '?id_cart_rule=' + idCartRule, '_blank');
            }
        }
    });
    
    // Nettoie les modals quand ils se ferment
    var modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        modal.addEventListener('hidden.bs.modal', function() {
            var textarea = this.querySelector('.pdf-custom-message');
            if (textarea) textarea.value = '';
        });
    });
});