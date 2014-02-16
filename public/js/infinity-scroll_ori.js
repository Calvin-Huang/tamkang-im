(function($){
    var ajaxLock = false;
    var noDataFlag = false;

    getLoaderPosition = function() {
        return $('#infinity-loader').offset().top - $(window).height() + $('#infinity-loader').height();
    }

    appendNews = function(page) {
        $.ajax({
            url : '',
            data : {
                'page' : page,
            },
            type : 'POST',
            dataType : 'html',
            success : function(data, textStatus, jqXHR) {
                if (data == '') {
                    $('#infinity-loader').fadeOut(200);
                    noDataFlag = true;
                } else {
                    $(data).insertBefore('#infinity-loader').fadeIn('slow', function() {
                        ajaxLock = false;
                        loaderPosition = getLoaderPosition();
                        currentPage++;
                    });
                }
            },
            error : function(jqXHR, textStatus, errorThrown) {

            },
            beforeSend : function() {
                ajaxLock = true;
            }
        });
    }

    var totalHeight = $('html, body').height() - $(window).height();
    var positionToTop = 0;
    var loaderPosition = getLoaderPosition();
    var currentPage = 0;

    $(window).scroll(function(){
        positionToTop = $(this).scrollTop();

        if (positionToTop >= loaderPosition && !ajaxLock && !noDataFlag) {
            appendNews(currentPage + 1);
        }
    });

})(jQuery);