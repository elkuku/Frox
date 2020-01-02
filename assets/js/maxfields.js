let frameNum = -1
let intervalId = 0

const jsData = $('#js-data')

const maxFrames = jsData.data('maxFrames')
const item = jsData.data('item')
const links = jsData.data('links')

$('.sendMail').click(function () {
    const agent = this.id
    const email = $(this).prevAll('input[type=text]').val()
    const item = $('#itemId').html()

    const resultContainer = $('#result-' + agent)

    resultContainer.html('Sending email...')

    $.ajax({
        url: '/max-fields/send_mail',
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

$('#maxfield2strike_btn').click(function () {
    $('#maxfield2strike_form').toggle()
})

$('#maxfield2strike_form').on('submit', function (event) {
    const statusContainer = $('#maxfield2strike_status')
    const resultContainer = $('#maxfield2strike_result')

    const opName = $('input[name=opName]').val()

    statusContainer.html('Creating OP "' + opName + '"...')

    intervalId = setInterval(updateMaxfieldLog, 1000)

    $.ajax({
        url: '/max-fields/maxfield2strike?'+$(this).serialize(),

        success: function (result) {
            statusContainer.html(result.message)
        },

        error: function (xhr, status, error) {
            statusContainer.html('THERE WAS AN ERROR!')
            resultContainer.html(error)
        },
        complete: function() {
            setTimeout(function() {
                clearInterval(intervalId)
            }, (3000));
        }
    })

    event.preventDefault()
})

function updateMaxfieldLog() {
    const resultContainer = $('#maxfield2strike_result')

    $.ajax({
        url: '/max-fields/log',

        success: function (result) {
            resultContainer.html(result)
        },

        error: function (xhr, status, error) {
            resultContainer.html(error)
        }
    })
}

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
