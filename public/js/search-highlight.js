(function(){
    var term = $('.search').val();
    if (term != '') {
        $('.highlight').each(function(i){
            $(this).html($(this).html().replace(new RegExp('(' + term + ')', 'gi'), '<span style="font-size:bolder;color:#F00">\$1</span>'));
        });
    }
    
    $('a[data-toggle="tooltip"]').tooltip();
})();