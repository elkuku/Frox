require('leaflet')
require('leaflet/dist/leaflet.css')
require('../css/map.css')

require('leaflet.markercluster')
require('leaflet.markercluster/dist/MarkerCluster.css')
require('leaflet.markercluster/dist/MarkerCluster.Default.css')

require('leaflet-draw')
require('leaflet-draw/dist/leaflet.draw.css')

import 'bootstrap/js/dist/modal'

let map

const LeafIcon = L.Icon.extend({
    options: {
        shadowUrl: 'build/img/leaf-shadow.png',
        iconSize: [38, 95],
        shadowSize: [50, 64],
        iconAnchor: [22, 94],
        shadowAnchor: [4, 62],
        popupAnchor: [-3, -76]
    }
})

const redIcon = new LeafIcon({iconUrl: 'build/img/leaf-red.png'}),
    orangeIcon = new LeafIcon({iconUrl: 'build/img/leaf-orange.png'})

const selectedMarkers = []
const markers = L.markerClusterGroup({disableClusteringAtZoom: 16})

function initmap() {
    const osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    const osmAttrib = 'Map data (C) <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    const osm = new L.TileLayer(osmUrl, {attribution: osmAttrib})

    map = new L.Map('map', {
        editable: true,
        editOptions: {}
    })

    // ec
    map.setView(new L.LatLng(0.990275, -79.659482), 9)

    // de
    // map.setView(new L.LatLng(50.085314, 8.240779), 9)

    map.addLayer(osm)
}

function loadMarkers() {
    markers.clearLayers()
    let bounds = map.getBounds()
    bounds = bounds._northEast.lat + ',' + bounds._northEast.lng + ',' + bounds._southWest.lat + ',' + bounds._southWest.lng

    $.get('/waypoints_map?bounds=' + bounds, {some_var: ''}, function (data) {

        $(data).each(function () {
            const marker =
                new L.Marker(
                    new L.LatLng(this.lat, this.lng),
                    {icon: orangeIcon, wp_id: this.id, wp_selected: false, title: this.name}
                )

            marker.on('click', function (e) {
                toggleMarker(e.target)
            })

            markers.addLayer(marker)
            map.addLayer(markers)
        })
    }, 'json')
}

function toggleMarker(marker) {
    if (marker.options.wp_selected) {
        marker.setIcon(orangeIcon)
        marker.options.wp_selected = false
        let index = selectedMarkers.indexOf(marker.options.wp_id)
        if (index > -1) {
            selectedMarkers.splice(index, 1)
        }
    } else {
        marker.setIcon(redIcon)
        marker.options.wp_selected = true
        selectedMarkers.push(marker.options.wp_id)
    }

    $('#result_message').html(selectedMarkers.length)
}

function doPostRequest(path, parameters) {
    const form = $('<form></form>')

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
    $.post('/export2', {points: selectedMarkers}, function (data) {
        const options = {}
        const modal = $('#resultModal')
        modal.find('.modal-title').text('MaxFields')
        modal.find('.modal-body').text(data.maxfield)
        modal.modal(options)
    })
})

$('#result_Gpx').on('click', function () {
    $.post('/export2', {points: selectedMarkers}, function (data) {
        const options = {}
        const modal = $('#resultModal')
        modal.find('.modal-title').text('GPX')
        modal.find('.modal-body').text(data.gpx)
        modal.modal(options)
    })
})

$('#build').on('click', function () {
    doPostRequest('/max-fields/export', {
        points: selectedMarkers,
        buildName: $('#build_name').val(),
        players_num: $('#players_num').val()
    })
})

$('#drawSelection').on('click', function () {
    const rect = new L.Draw.Rectangle(map)
    rect.enable()

    map.on('draw:created', (e) => {
        let bounds = e.layer.getBounds()
        markers.eachLayer(function(layer){
            if (bounds.contains(layer.getLatLng())) {
                toggleMarker(layer)
            }
        });
    })
})
