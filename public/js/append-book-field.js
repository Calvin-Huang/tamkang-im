$('form').on('focus', '.field:last', function(){
    var template = '<div class="control-group">';
        template+=     '<a class="close pull-left" data-dismiss="alert">&times;</a>';
        template+=     '<label class="control-label" for="data-index-' + ($('.field').length + 1) + '">標題' + '</label>';
        template+=     '<div class="controls">';
        template+=          '<input type="text" class="field input-block-level" id="data-index-' + ($('.field').length + 1) + '" name="book[]" value="" />';
        template+=     '</div>';
        template+= '</div>';
    $('.control-group:last').after(template);
});