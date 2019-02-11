require('leaflet')
require('leaflet/dist/leaflet.css')

require('leaflet.markercluster')
require('leaflet.markercluster/dist/MarkerCluster.css')
require('leaflet.markercluster/dist/MarkerCluster.Default.css')

require('leaflet-draw')
require('leaflet-draw/dist/leaflet.draw.css')

require('../css/map2.css')

const g11n3t = require('g11n-js').g11n3t

function initmap() {
    map = new L.Map('map')

    const osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png'
    const osmAttrib = 'Map data (C) <a href="https://openstreetmap.org">OpenStreetMap</a> contributors'
    const osm = new L.TileLayer(osmUrl, {attribution: osmAttrib})

    map.setView(new L.LatLng(0.990275, -79.659482), 9)
    map.addLayer(osm)

    // Initialise the FeatureGroup to store editable layers
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

    // Initialise the draw control and pass it the FeatureGroup of editable layers
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

const markers = L.markerClusterGroup({disableClusteringAtZoom: 16})

function loadMarkers() {

    markers.clearLayers()
    let bounds = map.getBounds()
    bounds = bounds._northEast.lat + ',' + bounds._northEast.lng + ',' + bounds._southWest.lat + ',' + bounds._southWest.lng

    //Workaround for marker icons ->
    delete L.Icon.Default.prototype._getIconUrl

    L.Icon.Default.mergeOptions({
        iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
        iconUrl: require('leaflet/dist/images/marker-icon.png'),
        shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
    })
    //Workaround for marker icons <-

    $.get('/agent_data?bounds=' + bounds, {some_var: ''}, function (data) {

        $(data).each(function () {
            let marker =
                new L.Marker(
                    new L.LatLng(this.lat, this.lng),
                    {
                        // icon: orangeIcon,
                        wp_id: this.id, wp_selected: false, title: this.name
                    }
                )

            marker.bindPopup(g11n3t("Loading..."))

            // var now = new Date().getTime();
            // while(new Date().getTime() < now + 2000){ /* do nothing */ }

            marker.on('click', function(e) {
                var popup = e.target.getPopup();
                $.get('/waypoints_info/' + e.target.options.wp_id).done(function (data) {
                    popup.setContent(data);
                    popup.update();
                });
            })

            markers.addLayer(marker)
            map.addLayer(markers)
        })
    }, 'json')
}

initmap()
loadMarkers()
