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
            resultContainer.html(result.message)
        }
    })
})

$('#framePlus').click(function () {
    if (frameNum < maxFrames) {
        frameNum++
    }

    changeImage()
})

$('#frameMinus').click(function () {
    if (frameNum > -1) {
        frameNum--
    }

    changeImage()
})

function changeImage() {
    $('#frameNum').html(frameNum + ' / ' + maxFrames)

    let num
    let msg = ''

    if (frameNum === -1) {
        num = -1
    } else {
        let s = '000000000' + frameNum
        num = s.substr(s.length - 3)
    }

    $('#displayFrames').attr('src', '/maxfields/' + item + '/frame_' + num + '.png')

    if (-1 === frameNum) {
        msg = 'Initial'
    } else if (frameNum < maxFrames) {

        if (frameNum > 0) {
            msg += getEventLine(links[frameNum - 1], false)
        }

        msg += getEventLine(links[frameNum], true)

        if (frameNum + 1 < maxFrames) {
            msg += getEventLine(links[frameNum + 1], false)
        }
    } else {
        msg = 'Final'
    }

    $('#frameLinkInfo').html(msg)
}

function getEventLine(link, isCurrent) {
    let css = isCurrent ? 'linkCurrent' : 'link'
    let num = link.linkNum + 1

    return '<div class="' + css + '">'
        + num + ' - ' + link.agentNum
        + ' - ' + link.originNum + ' ' + link.originName
        + ' &rArr; ' + link.destinationNum + ' ' + link.destinationName
        + '</div>'
}

$(function () {
    changeImage()
})
