$('.attach-image').click(function(handler){
    var template = '<img class="img-responsive" src="' + $(this).data('src') + '">';
    $(this).html(template);
});

$('#stretch-all').click(function(handler){
    $('.attach-image').each(function(i){
        var template = '<img class="img-responsive" src="' + $(this).data('src') + '">';
        $(this).html(template);
    });
});