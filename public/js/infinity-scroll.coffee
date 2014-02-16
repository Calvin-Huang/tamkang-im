ajaxLock = false
noDataFlag = false

getLoaderPosition = () ->
    $('#infinity-loader').offset().top - $(window).height() + $('infinity-loader').height()

appendNews = (page) ->
    ajaxLock = true

    $.ajax(
        url : ''
        data : 
            'page' : page
        type : 'POST'
        dataType : 'html'
        success : (data, textStatus, jqXHR) ->
            if data == ''
                $('#infinity-loader').fadeOut(200);
                noDataFlag = true
            else
                $(data).insertBefore('#infinity-loader').fadeIn(1000, () ->
                    ajaxLock = false
                    currentPage++
                )
        error : (jqXHR, textStatus, errorThrown) -> 
    )

totalHeight = $('html, body').height() - $(window).height()
positionToTop = 0
loaderPosition = getLoaderPosition()
currentPage = 1

$(window).scroll(() ->
    positionToTop = $(this).scrollTop()
    if !ajaxLock and !noDataFlag
        loaderPosition = getLoaderPosition()

        if positionToTop >= loaderPosition
            appendNews(currentPage + 1)
)