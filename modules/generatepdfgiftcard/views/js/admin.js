$(document).ready(function() {
    // Insert variable into textarea
    $('.variable-btn').click(function() {
        var variable = $(this).data('var');
        var textarea = $('textarea[name^="GENERATEPDF_CONTENT"]:visible');
        
        if (textarea.length) {
            var currentVal = textarea.val();
            var selectionStart = textarea[0].selectionStart;
            var selectionEnd = textarea[0].selectionEnd;
            
            // Insert variable at cursor position
            var newText = currentVal.substring(0, selectionStart) + 
                         variable + 
                         currentVal.substring(selectionEnd);
            
            textarea.val(newText);
            
            // Set cursor after inserted variable
            textarea[0].selectionStart = textarea[0].selectionEnd = selectionStart + variable.length;
            textarea.focus();
        }
    });
    
    // Handle language switcher for textareas
    $('.language-selector a').on('click', function() {
        setTimeout(function() {
            // Re-bind variable buttons for newly visible textarea
            $('.variable-btn').off('click').on('click', function() {
                var variable = $(this).data('var');
                var textarea = $('textarea[name^="GENERATEPDF_CONTENT"]:visible');
                
                if (textarea.length) {
                    var currentVal = textarea.val();
                    var selectionStart = textarea[0].selectionStart;
                    var selectionEnd = textarea[0].selectionEnd;
                    
                    var newText = currentVal.substring(0, selectionStart) + 
                                 variable + 
                                 currentVal.substring(selectionEnd);
                    
                    textarea.val(newText);
                    textarea[0].selectionStart = textarea[0].selectionEnd = selectionStart + variable.length;
                    textarea.focus();
                }
            });
        }, 100);
    });
});