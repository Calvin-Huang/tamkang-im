(function(){
    $('form').on('focus', '.field:last', function(){
        $('.control-group:last').after('<div class="control-group">' + $('.control-group:last').html().replace(/type\[\d+\]/g, 'type[' + Date.now() + ']') + '</div>');
        $('.control-group:last .sort').val($('.control-group').length - 1);
    });

    $('#type-sort').sortable({
        update: function(event, ui) {
            $('.sort').each(function(index){
                $(this).val(index);
            });
        }
    });

    $('[data-dismiss="delete"]').on('click', function(){
        var currentGroup = $(this).closest('.control-group');
        currentGroup.fadeOut(300).find('.delete').val(1);
        currentGroup.find('.sort').removeClass('sort');
        currentGroup.nextAll().each(function(){
            $(this).find('.sort').val($(this).find('.sort').val() - 1);
        });
    });

    $('.selectpicker').selectpicker();
})();