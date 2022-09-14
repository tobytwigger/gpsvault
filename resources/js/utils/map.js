import maplibregl from 'maplibre-gl'; // or "const maplibregl = require('maplibre-gl');"

const map = new maplibregl.Map({
    container: 'map',
    style: 'https://demotiles.maplibre.org/style.json', // stylesheet location
    center: [-74.5, 40], // starting position [lng, lat]
    zoom: 9 // starting zoom
});

export default class Map {
    _container = null;

    _style = null;

    constructor(container) {
        this.map = new maplibregl.Map({
            container: 'container',
            style: 'https://demotiles.maplibre.org/style.json', // stylesheet location
            center: [-74.5, 40], // starting position [lng, lat]
            zoom: 9 // starting zoom
        });
    }

    container(container) {
        this._container = container;
    }

    style(style) {
        this._style = style;
    }
}
