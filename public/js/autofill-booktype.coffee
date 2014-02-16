$('#auto-fill').on('click', (event) ->
    event.preventDefault()
    $.ajax(
        url : $(this).attr('href')
        type : 'GET'
        dataType : 'json'
        success : (data, textStatus, jqXHR) ->
            $.each(data, (index, type) ->
                fillTypeField(type.value, type.name)
            )
        error : (jqxHR, textStatus, errorThrown) ->
        beforeSend : (jqxHR, setting) ->
            $('#modal-alert').modal('show')
        complete : (jqxHR, textStatus) ->
            $('#modal-alert').find('.close').click();
    )
)

fillTypeField = (value, name) ->
    $("<div class=\"control-group unsave\">
        <label class=\"control-label\">項目</label>
        <div class=\"controls controls-row\">
            <input type=\"text\" class=\"field\" name=\"type[]\" value=\"#{name}\" />
            <input type=\"text\" class=\"field\" name=\"type_en_US[]\" value=\"\" placeholder=\"分類英文顯示名稱\" />
            <input type=\"text\" class=\"field\" name=\"value[]\" value=\"#{value}\" placeholder=\"教師歷程系統分類值\" /> 
        </div>
    </div>").insertBefore('.unsave:last')

$('#type-sort').sortable()