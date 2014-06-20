$.fn.modal.Constructor.prototype.enforceFocus = function () {
    modal_this = this
    $(document).on('focusin.modal', function (e) {
        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length
        // add whatever conditions you need here:
        &&
        !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
            modal_this.$element.focus()
        }
    })
};

$('#action-add, #action-edit, #action-delete').click(function(){
    $('.modal-body').find('form').submit();
});

$('.modal-body').on('submit', 'form', function(e){
    e.preventDefault();
    
    var formData = new FormData();
    var filesNumber = 0;
    
    for (var i = 0; i < this.elements.length ; i++) {
        
        var isIgnore = (this.elements[i].className.indexOf('ignore') != -1) ? true : false;
        
        // 如果為空值就不可以送出
        if (this.elements[i].value == '' && this.elements[i].type != 'hidden' && !isIgnore) {
            $(this.elements[i]).tooltip({
                title : '請勿留空',
                placement : 'right',
                trigger : 'manual',
            }).tooltip('show');
             return false;
        } else {
            $(this.elements[i]).tooltip('hide');
        }
        
        // 如果類型為檔案特別處理
        if (this.elements[i].type == 'file') {
            for (var j = 0; j < this.elements[i].files.length; j++){
                filesNumber++;
                formData.append(this.elements[i].name, this.elements[i].files[j]);
            }
        } else {
            formData.append(this.elements[i].name, this.elements[i].value);
        }
    }
    
    $.ajax({
        url : this.action,
        data : formData,
        type : 'POST',
        dataType : 'json',
        processData: false,
        contentType: false,
        beforeSend: function(jqXHR, settings){
            if (filesNumber > 0) {
                $('.modal-footer > .btn').css('cursor', 'wait');
            }
        },
        complete: function(jqXHR, textStatus) {
            if (filesNumber > 0) {
                $('.modal-footer > .btn').css('cursor', 'pointer');
            }
        },
        success : function(data, textStatus, jqXHR) {
            $('.modal').on('hidden', function(){
                document.location.href = '';
            });
            
            $('.close[data-dismiss="modal"]').trigger('click');
        },
        error : function(jqXHR, textStatus, errorThrown) {
            
            $('.close[data-dismiss="modal"]').trigger('click');
            
            var message = '';
            if (jqXHR.status == 413) {
                message = '檔案超過上傳限制'; 
            } else {
                message = jqXHR.responseText;
            }
            var modal = $('#modal-alert');
            modal.find('.modal-body').html('<p>操作文章成功，檔案上傳失敗</p><h3>' + message + '</h3>');
            modal.modal('show');
            $('#modal-alert').on('hidden', function(){
                document.location.href = '';
            });
        }
    });
});