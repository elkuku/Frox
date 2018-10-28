let frameNum = -1
// let maxFrames = 0

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

$('#framePlus').click(function () {
    if (frameNum < maxFrames) {
        frameNum ++
    }

    changeImage()
})

$('#frameMinus').click(function () {
    if (frameNum > -1) {
        frameNum --
    }

    changeImage()
})

function changeImage() {
    $('#frameNum').html(frameNum + ' / ' + maxFrames)

    let num, msg

    if (frameNum === -1) {
        num = -1
    } else {
        let s = '000000000' + frameNum
        num = s.substr(s.length - 3)
    }


    $('#displayFrames').attr('src', '/maxfields/' + item + '/frame_' + num + '.png')

    if (-1 === frameNum) {
        msg = 'Initial'
    }
    else if (frameNum < maxFrames) {

    console.log(links[frameNum])
    console.log(links[frameNum].originName)

        let lNum =  links[frameNum].linkNum + 1
        msg =
        'Link No: ' + lNum + '<br>'
        + 'Agent: ' + links[frameNum].agentNum + '<br>'
        + 'Origin: ' + links[frameNum].originNum + ' - ' + links[frameNum].originName + '<br>'
        + 'Destination: '+ links[frameNum].destinationNum + ' - ' + links[frameNum].destinationName + '<br>'
    }else {
        msg = 'Final'
    }

    $('#frameLinkInfo').html(msg)

}

$(function() {
    changeImage()
});
