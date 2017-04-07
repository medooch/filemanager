/** Created by trimechmehdi <trimechmehdi11@gmail.com> */
$(document).ready(function (e) {
    /** init tooltip */
    $('[data-toggle="tooltip"]').tooltip();

    /** Confirm delete folders */
    $('.delete_element').on('click', function () {
        var btn = $(this);
        if (confirm("Do you confirm?")) {
            window.location.href = btn.data('href');
        }
    });

    /** Preview pictures on modal */
    $('.modal_preview').on('click', function () {
        var btn = $(this);
        $('#preview_image').attr('src', btn.data('href'));
        $('#preview_image').attr('width', 270);
        $('#preview_image').attr('heigth', 150);
    });
})

/** Global function for submit forms with ajax */
function submitFormAjax(form) {
    $.ajax({
        type: form.attr('method'),
        dataType: 'json',
        url: form.attr('action'),
        data: form.serializeArray(),
        beforeSend: function () {
            $('.text-danger').remove();
        },
        success: function (response) {
            if (response.status === false) {
                if (response.errors) {
                    /** display symfony form errors */
                    $.each(response.errors, function (input, message) {
                        form.append('<div id="err_' + input + '" class="text-danger">' + message + '</div>');
                    })
                } else {
                    form.append('<div class="text-danger">' + response.message + '</div>');
                }
            } else {
                /** reload page */
                window.location.reload(true);
            }
        },
    });
}

/** Re-init remote modal (Empty) all modal that are loaded by ajax */
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI).on("hidden.bs.modal", ".modal.remote", function (e) {
    $(e.target).removeData("bs.modal").find(".modal-content").empty();
});

/** Global animate function */
function animate(element) {
    $("html, body").animate({scrollTop: element.offset().top}, 1000);
}