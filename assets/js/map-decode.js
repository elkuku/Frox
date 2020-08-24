require('leaflet')
require('leaflet/dist/leaflet.css')

require('jquery-ui-dist/jquery-ui')
require('jquery-ui-dist/jquery-ui.css')

require('../css/map-decode.css')

$('#sortable').sortable({
    axis: 'y',
    cursor: 'move',
    containment: 'parent',
    update: function () {
        const ids = $(this).sortable('toArray')
        updatePolyLine(ids)
        updateIntelLink(ids)
    }
})

const map = new L.Map('map')

let wayPoints = window.wayPoints, polyLine

function initmap() {
    const osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    const osmAttrib = 'Map data (C) <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    const osm = new L.TileLayer(osmUrl, {attribution: osmAttrib})
    map.addLayer(osm)

    const firstPoint = window.wayPoints[0]
    if (firstPoint) {
        map.setView(new L.LatLng(firstPoint.lat, firstPoint.lng), 15)
    } else {
        map.setView(new L.LatLng(0.990275, -79.659482), 9)
    }
}

function loadMarkers() {
    let pointList = []
    $(window.wayPoints).each(function () {
        const pic = $('#' + this.id).find('img').attr('src')
        let lng = new L.LatLng(this.lat, this.lng)
        pointList.push(lng)
        let wpIcon = L.icon({
            iconUrl: pic,
            iconSize: [36, 36],
            iconAnchor: [11, 24],
            popupAnchor: [0, -18],
        })
        let marker =
            new L.Marker(
                lng,
                {
                    icon: wpIcon,
                    wp_id: this.id, wp_selected: false, title: this.name
                }
            ).addTo(map)

        marker.bindPopup('Loading...', {maxWidth: 'auto'})

        marker.on('click', function (e) {
            const popup = e.target.getPopup()
            $.get('/waypoints_info/' + e.target.options.wp_id).done(function (data) {
                popup.setContent(data)
                popup.update()
            })
        })
    })

    polyLine = new L.Polyline(pointList, {
        color: 'red',
        weight: 3,
        opacity: 0.5,
        smoothFactor: 1
    })
    polyLine.addTo(map)
}

function updatePolyLine(ids) {
    map.removeLayer(polyLine)
    let pointList = []
    ids.forEach(function (item, index) {
        wayPoints.forEach(function (wp) {
            if (wp.id === parseInt(item)) {
                let lng = new L.LatLng(wp.lat, wp.lng)
                pointList.push(lng)
            }
        })
    })

    polyLine = new L.Polyline(pointList, {
        color: 'red',
        weight: 3,
        opacity: 0.5,
        smoothFactor: 1
    })
    polyLine.addTo(map)
}

function updateIntelLink(ids) {
    let link, point, center, links = [], linkList = [], count = 1
    ids.forEach(function (item, index) {
        point = ''
        wayPoints.forEach(function (wp) {
            if (wp.id === parseInt(item)) {
                point = wp.lat + ',' + wp.lng
                linkList.push(count+'. https://intel.ingress.com/?pll='+point)
                count++
            }
        })
        if (!point) {
            throw 'No point'
        }
        if (!center) {
            center = point
        }
        if (!link) {
            link = point
        } else {
            link += ',' + point
            links.push(link)
            link = point
        }
    })

    let text = 'http://intel.ingress.com/intel?ll=' + center + '&z=15&pls=' + links.join('_')

    $('#intelLink').val(text)
    $('#intelLinkHref').attr('href', text)
    $('#intelLinkList').val(linkList.join("\n"))
}

initmap()
loadMarkers()
const ids = $('#sortable').sortable('toArray')
updatePolyLine(ids)
updateIntelLink(ids)

