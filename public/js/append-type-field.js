$('form').on('focus', '.field:last', function(){
    var template = '<div class="control-group">';
        template+=     '<label class="control-label" for="data-index-' + ($('.field').length + 1) + '">種類' + ($('.field').length + 1) + '</label>';
        template+=     '<div class="controls">';
        template+=          '<input type="text" class="field" id="data-index-' + ($('.field').length + 1) + '" name="type[]" value="" />';
        template+=     '</div>';
        template+= '</div>';
    $('.control-group:last').after(template);
});