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

        this._routingButton = this._createButton('maplibregl-ctrl-icon maplibregl-ctrl-zoom-in', (e) => {
            if(this._sidebarIsShowing) {
                this.hideSidebar();
                this._sidebarIsShowing = false;
            } else {
                this.showSidebar();
                this._sidebarIsShowing = true;
            }
        });

        let span = window.document.createElement('span');
        span.className = 'maplibregl-ctrl-icon mapboxgl-ctrl-icon';
        span.setAttribute('aria-hidden', 'true');
        this._routingButton.appendChild(span);

        this._sidebar = window.document.createElement('div');
        this._sidebar.className = 'maplibregl-custom-ctrl-sidebar';
        this._sidebar.hidden = true;
        this._container.appendChild(this._sidebar);
        this._sidebar.appendChild(window.document.getElementById('routing-control'));
    }

    hideSidebar() {
        this._sidebar.hidden = true;
        this._routingButton.classList.remove('maplibregl-ctrl-zoom-out')
        this._routingButton.classList.add('maplibregl-ctrl-zoom-in');
    }

    showSidebar() {
        this._sidebar.hidden = false;
        this._routingButton.classList.add('maplibregl-ctrl-zoom-out')
        this._routingButton.classList.remove('maplibregl-ctrl-zoom-in');
    }

    getDefaultPosition() {
        return 'top-left';
    }

    _updateZoomButtons() {
        // const zoom = this._map.getZoom();
        // const isMax = zoom === this._map.getMaxZoom();
        // const isMin = zoom === this._map.getMinZoom();
        // this._routingButton.disabled = isMax;
        // this._zoomOutButton.disabled = isMin;
        // this._routingButton.setAttribute('aria-disabled', isMax.toString());
        // this._zoomOutButton.setAttribute('aria-disabled', isMin.toString());
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
