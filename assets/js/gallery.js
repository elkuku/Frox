require('../css/gallery.css')
require('form-serializer')
$('.card-img-top').on('click', function () {
    const modal = $('#galleryModal')

    modal.find('h5').html($(this).data('originalTitle'))
    modal.find('img').attr('src', '/wp_images/' + $(this).data('guid') + '.jpg')
    modal.find('.intelLink').attr('href', $(this).data('intelLink'))
    modal.find('.editLink').attr('href', $(this).data('editLink'))
    let detailsLink = $(this).data('detailsLink')
    $.get(detailsLink, function (data) {
        const form = modal.find('.detailsForm')
        form.html(data)
        form.find('button').on('click', function () {
            let data = form.find('form').serializeObject()
            data.redirectUri = window.location.href
            $.post(detailsLink,
                data,
                function (result) {
                    location.reload()
                }
            )
            return false
        })
    })

    modal.modal()
})
