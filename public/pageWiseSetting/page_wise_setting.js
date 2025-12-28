
function page_wise_setting_checkbox() {
    if ($("#user_name").prop('checked') === true) {
        $('.created_user').removeClass('d-none');
    } else {
        $('.created_user').addClass('d-none');
    }
    if ($("#last_update").prop('checked') === true) {
        $('.last_update').removeClass('d-none');
    } else {
        $('.last_update').addClass('d-none');
    }
    if ($("#alias").prop('checked') === true) {
        $('.alias').removeClass('d-none');
    } else {
        $('.alias').addClass('d-none');
    }
    if ($("#bangla_name").prop('checked') === true) {
        $('.bangla_name').removeClass('d-none');
    } else {
        $('.bangla_name').addClass('d-none');
    }
}
function page_wise_setting_table_row_sort_by(data) {
    inverse = data == 0 ? true : false;
    $(document).find('tbody td').filter(function () {

        return $(this).index() === 1;

    }).sortElements(function (a, b) {
        return $.text([a]) > $.text([b]) ?
            inverse ? -1 : 1
            : inverse ? 1 : -1;

    }, function () {

        // parentNode is the element we want to move
        return this.parentNode;

    });

    return inverse = !inverse;
}