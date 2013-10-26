$('form').on('focus', '.field:last', function(){
    var template = '<p class="field" style="width:250px;">';
        template+=     '<input type="text" name="othertitle[]" class="othertitle" />';
        template+=     '<a class="close" data-dismiss="alert">&times;</a>';
        template+= '</p>';
    $('.field:last').after(template);
});