(function($){
    // set edit field display at view top
    $('html').animate({
        scrollTop : $('#edit-field').offset().top - 50
    }, 1000);

    $('#edit-field').focus();

    // append field when last input on focus
    $('form').on('focus', '.append-field:last', function(event){
        var index = parseInt($(this).data('index'), 10);

        // get tr element
        var template = '<tr id="' + (index + 1) + '"><td><input data-index="' + (index + 1) + '" class="append-field input-block-level" name="informations[]" value="" type="text" placeholder="請輸入顯示資訊" /></td><td><a href="#' + (index + 1) + '" class="close pull-left" data-dismiss="alert">&times;</a></td></tr>';
        $(template).insertAfter('tr:last');
    });
})(jQuery);