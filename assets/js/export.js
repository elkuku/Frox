$('#checkAll').change(function () {
    $('input[name=\'points[]\']').prop('checked', this.checked);
});

