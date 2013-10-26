var fixHelper = function(e, ui) {
    ui.children().each(function() {
        $(this).width($(this).width());
    });
    return ui;
};
$('#table-sort > tbody').sortable({
    helper : fixHelper,
    placeholder : 'drop',
    sort : function(event, ui) {
    },
}).disableSelection();