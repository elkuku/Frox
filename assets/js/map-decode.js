require('leaflet')
require('leaflet/dist/leaflet.css')

// require('leaflet.markercluster')
// require('leaflet.markercluster/dist/MarkerCluster.css')
// require('leaflet.markercluster/dist/MarkerCluster.Default.css')

require('leaflet-draw')
require('leaflet-draw/dist/leaflet.draw.css')
// const $ = require('jquery');
// global.$ = global.jQuery = $;
// require('jquery-sortable-lists')
// require('html5sortable/docs/html5sortable')

require('jquery-ui-dist/jquery-ui')
require('jquery-ui-dist/jquery-ui.css')

require('../css/map-decode.css')

$('#sortable').sortable({
    axis: 'y',
    cursor: 'move',
    containment: 'parent',
    update: function (event, ui) {
        const ids = $(this).sortable('toArray')
        updatePolyLine(ids)
        updateIntelLink(ids)
    }
})

const map = new L.Map('map')

let wayPoints = window.wayPoints, polyLine

// const markers = L.markerClusterGroup({disableClusteringAtZoom: 16})

function initmap() {
    const osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    const osmAttrib = 'Map data (C) <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    const osm = new L.TileLayer(osmUrl, {attribution: osmAttrib})

    const firstPoint = window.wayPoints[0]
    if (firstPoint) {
        map.setView(new L.LatLng(firstPoint.lat, firstPoint.lng), 15)
    } else {
        map.setView(new L.LatLng(0.990275, -79.659482), 9)

    }
    // let lng = new L.LatLng(firstPoint.lat, firstPoint.lng)

    map.addLayer(osm)

    const editableLayers = new L.FeatureGroup()
    map.addLayer(editableLayers)

    const drawPluginOptions = {
        position: 'topright',
        draw: {
            polygon: {
                allowIntersection: false, // Restricts shapes to simple polygons
                drawError: {
                    color: '#e1e100', // Color the shape will turn when intersects
                    message: '<strong>Oh snap!<strong> you can\'t draw that!' // Message that will show when intersect
                },
                shapeOptions: {
                    color: '#97009c'
                }
            },
            // disable toolbar item by setting it to false
            // polyline: true,
            circle: false,
            rectangle: true,
            marker: false,
        },
        edit: {
            featureGroup: editableLayers, //REQUIRED!!
            remove: false
        }
    }

    const drawControl = new L.Control.Draw(drawPluginOptions)
    map.addControl(drawControl)

    map.on('draw:created', function (e) {
        const type = e.layerType,
            layer = e.layer

        if (type === 'marker') {
            layer.bindPopup('A popup!')
        }

        editableLayers.addLayer(layer)
    })
}

function loadMarkers() {

    // markers.clearLayers()

    // const myIcon = L.icon({
    //     iconUrl: '/build/img/ico/my-icon.png',
    //     iconSize: [22, 36],
    //     iconAnchor: [11, 36],
    //     popupAnchor: [0, -18],
    // })

    // console.log(window.wayPoints)

    // $.get('/waypoints_map', function (data) {
    //     $(data)
    let pointList = []
    $(window.wayPoints).each(function () {
        const pic = $('#' + this.id).find('img').attr('src')
        // const pic = sElement.find('img').attr('src')
        // console.log(pic)
        // console.log(this.guid)
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

        // markers.addLayer(marker)
        // map.addLayer(markers)
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
    $('#intelLinkList').val(linkList.join("\n"))
}

initmap()
loadMarkers()
