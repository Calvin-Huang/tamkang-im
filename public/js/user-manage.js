// plugin used to force change modal body every time.
$('body').on('hidden', '.modal', function() {
    $(this).removeData('modal');
});