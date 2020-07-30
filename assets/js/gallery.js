require('../css/gallery.css')

$('.card-img-top').on('click', function () {
    const modal = $('#galleryModal')

    modal.find('h5').html($(this).data('originalTitle'))
    modal.find('img').attr('src', '/wp_images/' + $(this).data('guid') + '.jpg')
    modal.find('.intelLink').attr('href', $(this).data('intelLink'))
    modal.find('.editLink').attr('href', $(this).data('editLink'))

    modal.modal()
})
