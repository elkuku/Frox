require('leaflet')
require('leaflet/dist/leaflet.css')
require('../css/map.css')

import 'bootstrap/js/dist/modal';

var map
var markersLayer = new L.LayerGroup()

var LeafIcon = L.Icon.extend({
    options: {
        shadowUrl: 'build/img/leaf-shadow.png',
        iconSize: [38, 95],
        shadowSize: [50, 64],
        iconAnchor: [22, 94],
        shadowAnchor: [4, 62],
        popupAnchor: [-3, -76]
    }
})

var greenIcon = new LeafIcon({iconUrl: 'build/img/leaf-green.png'}),
    redIcon = new LeafIcon({iconUrl: 'build/img/leaf-red.png'}),
    orangeIcon = new LeafIcon({iconUrl: 'build/img/leaf-orange.png'})

var ajaxRequest
var plotlist
var plotlayers = []

const selectedMarkers = []

function initmap() {
    map = new L.Map('map')

    var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    var osmAttrib = 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    var osm = new L.TileLayer(osmUrl, {attribution: osmAttrib})

    map.setView(new L.LatLng(0.990275, -79.659482), 9)
    map.addLayer(osm)
}

function loadMarkers() {

    markersLayer.clearLayers()
    var bounds = map.getBounds()
    bounds = bounds._northEast.lat + ',' + bounds._northEast.lng + ',' + bounds._southWest.lat + ',' + bounds._southWest.lng

    $.get('/waypoints_map?bounds=' + bounds, {some_var: ''}, function (data) {

        $(data).each(function () {
            var marker =
                new L.Marker(
                    new L.LatLng(this.lat, this.lng),
                    {icon: orangeIcon, wp_id: this.id, wp_selected: false, title: this.name}
                )

            // marker.bindPopup(this.name)

            marker.on('click', function (e) {
                var enabled = e.target.options.wp_selected
                if (enabled) {
                    e.target.setIcon(orangeIcon)
                    e.target.options.wp_selected = false
                    var index = selectedMarkers.indexOf(e.target.options.wp_id)
                    if (index > -1) {
                        selectedMarkers.splice(index, 1)
                    }
                } else {
                    e.target.setIcon(redIcon)
                    e.target.options.wp_selected = true
                    selectedMarkers.push(e.target.options.wp_id)
                }

                console.log(selectedMarkers)

                $('#result_message').html(selectedMarkers.length)
            })

            markersLayer.addLayer(marker)
            map.addLayer(markersLayer)

        })

    }, 'json')

}

function doPostRequest(path, parameters) {
    var form = $('<form></form>')

    form.attr('method', 'post')
    form.attr('action', path)

    $.each(parameters, function (key, value) {
        if (typeof value == 'object' || typeof value == 'array') {
            $.each(value, function (subkey, subvalue) {
                var field = $('<input />')
                field.attr('type', 'hidden')
                field.attr('name', key + '[]')
                field.attr('value', subvalue)
                form.append(field)
            })
        } else {
            var field = $('<input />')
            field.attr('type', 'hidden')
            field.attr('name', key)
            field.attr('value', value)
            form.append(field)
        }
    })
    $(document.body).append(form)
    form.submit()
}

initmap()
loadMarkers()

$('#result_maxFields').on('click', function () {
    $.post( "/export2", { points: selectedMarkers }, function( data ) {
        console.log(data)
        const options = {}
        const modal = $('#resultModal')
        modal.find('.modal-title').text('MaxFields')
        modal.find('.modal-body').text(data.maxfield)
        modal.modal(options)
    });

    // doPostRequest('/export', {points: selectedMarkers})
})

$('#result_Gpx').on('click', function () {
    $.post( "/export2", { points: selectedMarkers }, function( data ) {
        console.log(data)
        const options = {}
        const modal = $('#resultModal')
        modal.find('.modal-title').text('GPX')
        modal.find('.modal-body').text(data.gpx)
        modal.modal(options)
    });

    // doPostRequest('/export', {points: selectedMarkers})
})

// $(function () {
//
//     loadMarkers()
//
//     map.on('drag', function (e) {
//         markersLayer.clearLayers()
//     })
//
//     map.on('dragend', function (e) {
//         loadMarkers()
//     })
// })
