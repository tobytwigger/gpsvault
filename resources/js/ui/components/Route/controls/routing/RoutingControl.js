// import Point from '@mapbox/point-geometry';

// import DOM from '../../util/dom';
// import {extend, bindAll} from '../../util/util';
// import {MouseRotateHandler, MousePitchHandler} from '../handler/mouse';

// import type Map from '../map';
// import type {IControl} from './control';

// type NavigationOptions = {
//     showCompass?: boolean;
//     showZoom?: boolean;
//     visualizePitch?: boolean;
// };

// const defaultOptions: NavigationOptions = {
//     showCompass: true,
//     showZoom: true,
//     visualizePitch: false
// };

/**
 * A `NavigationControl` control contains zoom buttons and a compass.
 *
 * @implements {IControl}
 * @param {Object} [options]
 * @param {Boolean} [options.showCompass=true] If `true` the compass button is included.
 * @param {Boolean} [options.showZoom=true] If `true` the zoom-in and zoom-out buttons are included.
 * @param {Boolean} [options.visualizePitch=false] If `true` the pitch is visualized by rotating X-axis of compass.
 * @example
 * var nav = new maplibregl.NavigationControl();
 * map.addControl(nav, 'top-left');
 * @see [Display map navigation controls](https://maplibre.org/maplibre-gl-js-docs/example/navigation/)
 * @see [Add a third party vector tile source](https://maplibre.org/maplibre-gl-js-docs/example/third-party/)
 */
class RoutingControl {
    _map;
    options;
    _container;
    _routingButton;
    _sidebar;
    _sidebarIsShowing = false;

    constructor(options) {

        this._container = window.document.createElement('div');
        this._container.className = 'maplibregl-ctrl maplibregl-ctrl-group';// maplibregl-ctrl-top-left';
        this._container.addEventListener('contextmenu', (e) => e.preventDefault());

        this._hideSidebar.bind(this);
        this._showSidebar.bind(this);

        this._routingButton = this._createButton('mdi maplibregl-icon-lg mdi-routes', (e) => {
            if(this._sidebarIsShowing) {
                this._hideSidebar();
                this._sidebarIsShowing = false;
            } else {
                this._showSidebar();
                this._sidebarIsShowing = true;
            }
        });

        let span = window.document.createElement('span');
        span.className = 'mdi maplibregl-icon-lg mapboxgl-ctrl-icon';
        span.setAttribute('aria-hidden', 'true');
        this._routingButton.appendChild(span);

        this._sidebar = window.document.createElement('div');
        this._sidebar.className = 'maplibregl-custom-ctrl-sidebar';
        this._sidebar.hidden = true;
        this._container.appendChild(this._sidebar);
        this._sidebar.appendChild(window.document.getElementById('routing-control'));
    }

    _hideSidebar() {
        this._sidebar.hidden = true;
        this._routingButton.classList.remove('mdi-close-circle-outline')
        this._routingButton.classList.add('mdi-routes');
    }

    _showSidebar() {
        this._sidebar.hidden = false;
        this._routingButton.classList.add('mdi-close-circle-outline')
        this._routingButton.classList.remove('mdi-routes');
    }

    getDefaultPosition() {
        return 'top-left';
    }

    onAdd(map) {
        this._map = map;
        this._setButtonTitle(this._routingButton, 'Routing');
        // this._map.on('zoom', this._updateZoomButtons);
        // this._updateZoomButtons();
        return this._container;
    }

    onRemove() {
        if (this._container.parentNode) {
            this._container.parentNode.removeChild(this._container);
        }

        delete this._map;
    }

    _createButton(className, fn) {
        const a = window.document.createElement('button');
        a.className = className;
        this._container.appendChild(a);

        a.type = 'button';
        a.addEventListener('click', fn);
        return a;
    }

    _setButtonTitle(button, title) {
        button.title = title;
        button.setAttribute('aria-label', title);
    }
}

export default RoutingControl;
