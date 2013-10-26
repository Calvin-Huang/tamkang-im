(function(){
    var term = $('.search').val();
    if (term != '') {
        $('.highlight').each(function(i){
            $(this).html($(this).html().replace(term, '<span style="font-size:bolder;color:#F00">' + term + '</span>'));
        });
    }
    
    $('a[data-toggle="tooltip"]').tooltip();
})();