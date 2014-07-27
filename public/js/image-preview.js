//圖片預覽上傳
$('input[type=file]').each(function(index) {
    $(this).change(function() {
        if (this.files && this.files[0]) {
            var fileReader = new FileReader();
            fileReader.readAsDataURL(this.files[0]);
            fileReader.onload = function(e) {
                $('.thumbnail-avatar').css({
                    'background-image': 'url(' + e.target.result + ')'
                });
            };
        } else {
            $('.thumbnail-avatar').css({
                'background-image': 'url(' + this.value + ')'
            });
        }
    });
});