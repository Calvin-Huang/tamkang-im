$('#auto-fill').on('click', (event) ->
    event.preventDefault()
    $.ajax(
        url : $(this).attr('href')
        type : 'GET'
        dataType : 'json'
        success : (data, textStatus, jqXHR) ->
            $.each(data, (index, book) ->
                fillBookField(book)
            )
        error : (jqxHR, textStatus, errorThrown) ->
        beforeSend : (jqxHR, setting) ->
            $('#modal-alert').modal('show')
        complete : (jqxHR, textStatus) ->
            $('#modal-alert').find('.close').click();
    )
)

fillBookField = (book) ->
    $("<div class=\"control-group unsave\">
        <a class=\"close pull-left\" data-dismiss=\"alert\">&times;</a>
        <label class=\"control-label\">標題</label>
        <div class=\"controls\">
            <input type=\"text\" class=\"field input-block-level\" name=\"book[]\" value=\"#{book}\" />
        </div>
    </div>").insertBefore('.unsave:last')