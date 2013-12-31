$('form').on('focus', '.field:last', function(){
    $('.control-group:last').after('<div class="control-group">' + $('.control-group:last').html() + '</div>');
});