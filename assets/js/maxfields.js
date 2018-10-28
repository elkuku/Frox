$('.sendMail').click(function () {
    const agent = this.id
    const email = $(this).prevAll('input[type=text]').val()
    const item = $('#itemId').html()

    const resultContainer = $('#result-' + agent)

    resultContainer.html('Sending email...')

    $.ajax({
        url: '/maxfields_send_mail',
        data: {
            agent: agent,
            email: email,
            item: item
        },

        success: function (result) {
            console.log(result)
            resultContainer.html(result.message)
        }
    })
})
