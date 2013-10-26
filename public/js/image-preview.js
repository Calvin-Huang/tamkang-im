//圖片預覽上傳
$('input[type=file]').each(function(index) {
    $(this).change(function() {
        if (this.files && this.files[0]) {
            var fileReader = new FileReader();
            fileReader.readAsDataURL(this.files[0]);
            fileReader.onload = function(e) {
                $('.thumbnail').find('img').attr('src', e.target.result);
            }
        } else {
            $('.thumbnail').find('img').attr('src', this.value);
        }
    })
});