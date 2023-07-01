import {Control, MapState, MapMarker, MapMarkerCollection, mergeMarkers} from "../types/MapType";
import BasePlaceControl from './../../../components/Route/controls/places/PlaceControl.js';
import maplibregl, {IControl, Map} from "maplibre-gl";
import axios, {AxiosPromise, AxiosResponse} from "axios";
import {PaginationResponse} from "@tobytwigger/laravel-job-status-js/dist/interfaces/PaginationResponse";
import {cloneDeep, isEqual} from "lodash";
import {v4 as uuidv4} from 'uuid';

interface Place {
    address: string|null;
    created_at: string;
    description: string|null;
    email: string|null;
    id: number;
    location: {
        type: string;
        coordinates: [
            lng: number,
            lat: number
        ];
    };
    name: string;
    phone_number: string|null;
    type: string;
    updated_at: string;
    url: string|null;
    user_id: number;
}

interface SearchRequestBody {
    southwest_lat?: number|null;
    southwest_lng?: number|null;
    northeast_lat?: number|null;
    northeast_lng?: number|null;
    types: string[];
    perPage: number;
}

interface PlaceControlConfig {
    search: {
        url: string
    }
}

function searchForPlaces(url: string, state: MapState, selectedTypes: string[]): AxiosPromise<PaginationResponse<Place>> {
    let searchData: SearchRequestBody = {
        types: selectedTypes,
        perPage: 100
    };
    if(state.bounds) {
        searchData.southwest_lat = state.bounds.southWest.lat;
        searchData.southwest_lng = state.bounds.southWest.lng
        searchData.northeast_lat = state.bounds.northEast.lat;
        searchData.northeast_lng = state.bounds.northEast.lng;
    }
    // Filter by types!
    return axios.get(url, {params: searchData});
}


class PlaceControl implements Control  {
    config: PlaceControlConfig;

    private _places: Place[] = [];

    public visibleTypes: string[] = [
        'food_drink', 'shops', 'water'
    ];

    public errors: {[key: string]: string[]} = {};

    private _placesUpdatedCallbacks: ((places: Place[]) => void)[] = []

    private _errorsUpdatedCallbacks: ((errors: {[key: string]: string[]}) => void)[] = [];

    private triggerStateUpdate: () => void = () => undefined;

    constructor(config: PlaceControlConfig) {
        this.config = config;

        this.onPlacesUpdated((places: Place[]) => {
            this.triggerStateUpdate();
        });
    };

    createControl(): IControl {
        return new BasePlaceControl();
    }

    initialise(map: Map, triggerStateUpdate: () => void) {
        this.triggerStateUpdate = triggerStateUpdate;
    }

    setVisibleTypes(types: string[]) {
        this.visibleTypes = types;
        this.triggerStateUpdate();
    }

    modifyState(state: MapState, oldState: MapState): MapState {

        this.setErrors({});
        if(state.zoom < 10) {
            this.setPlaces([]);
        } else {
            searchForPlaces(this.config.search.url, state, this.visibleTypes)
                .then(response => this.setPlaces(response.data.data))
                .catch(error => this.setErrors(error.response.data.errors));
        }
        state = this.addPlacesToState(state);
        return state;
    }

    onPlacesUpdated(callback: (places: Place[]) => void) {
        this._placesUpdatedCallbacks.push(callback);
    }

    onErrorsUpdated(callback: (errors: {[key: string]: string[]}) => void) {
        this._errorsUpdatedCallbacks.push(callback);
    }

    setPlaces(places: Place[]): void {
        if(!isEqual(this._places, places)) {
            this._places = places;
            this._placesUpdatedCallbacks.forEach(callback => callback(places));
        }
    }

    getPlaces(): Place[] {
        return this._places;
    }

    private setErrors(errors: {[key: string]: string[]}) {
        this.errors = errors;
        this._errorsUpdatedCallbacks.forEach(callback => callback(errors));
    }

    private addPlacesToState(state: MapState): MapState {
        // Add markers as places to the state
        let updatedMarkers: MapMarker[] = [];
        let markers: MapMarkerCollection = state.markers?.places || new MapMarkerCollection([]);
        for(let place of this.getPlaces()) {
            let marker = markers.getMarkerWithReference(place.id.toString());
            let newMarker = this.createMarkerFromPlace(place);
            if(marker !== null) {
                newMarker = mergeMarkers(marker, newMarker);
            }
            updatedMarkers.push(newMarker);
        }

        markers.sync(updatedMarkers);
        state.markers.places = markers;
        // TODO Remove any unused markers

        return state;
    }

    // _createPlaceMarker(place, index) {
    //
    //     // Create the marker
    //     let markerEl = document.createElement('div');
    //     markerEl.id = 'place-' + place.id;
    //     markerEl.className = 'marker clickable';
    //     markerEl.style.cursor = 'pointer';
    //     markerEl.style.backgroundImage = this._getPlaceBackgroundImage(place.type);
    //     markerEl.style.width = '20px';
    //     markerEl.style.height = '48px';
    //
    //     // Create the onclick popup
    //     let goToPlaceButton = this._createPopupButton('Go to place', 'go-to-place-' + place.id, (e) => {
    //         let placeId = (e.target.id ?? '').replace('go-to-place-', '');
    //         let resolvedPlace = this.places.find((place) => place.id.toString() === placeId.toString());
    //         window.open(route('place.show', placeId), '_blank');
    //     });
    //     let addToStartButton = this._createPopupButton('Add to start', 'add-to-start', (e) => {
    //         let waypoint = this._newWaypoint([place.location.coordinates[1], place.location.coordinates[0]]);
    //         let schema = cloneDeep(this._schema);
    //         schema.waypoints.unshift(waypoint);
    //         this._schema = schema;
    //     });
    //
    //     let addToEndButton = this._createPopupButton('Go to place', 'go-to-place-' + place.id, (e) => {
    //         let placeId = (e.target.id ?? '').replace('go-to-place-', '');
    //         let resolvedPlace = this.places.find((place) => place.id.toString() === placeId.toString());
    //         window.open(route('place.show', placeId), '_blank');
    //     });
    //     let titleSpan = document.createElement('span');
    //     titleSpan.innerHTML = place.name;
    //     // let addAsPlaceBtn = this._createPopupButton('Add as a place', 'add-as-place-' + waypoint.id, (e) => console.log('Add as a place'));
    //     let buttonDiv = document.createElement('div');
    //     buttonDiv
    //         .appendChild(titleSpan)
    //         .appendChild(goToPlaceButton)
    //         .appendChild(addToStartButton)
    //     // .appendChild(addAsWaypointButton)
    //     // .appendChild(addToEndButton);
    //     let popup = new maplibregl.Popup({ offset: 25 }).setDOMContent(buttonDiv);
    //     let marker = new maplibregl.Marker({element: markerEl, draggable: false})
    //         .setLngLat([place.location.coordinates[0], place.location.coordinates[1]])
    //         .setPopup(popup); // sets a popup on this marker
    //
    //     return marker;
    // },
    //


    private createMarkerFromPlace(place: Place): MapMarker {
        return {
            uniqueId: 'place-' + place.id,
            longitude: place.location.coordinates[0],
            latitude: place.location.coordinates[1],
            reference: uuidv4(),
            backgroundImage: this._getPlaceBackgroundImage(place.type),
        }
    }

    _getPlaceBackgroundImage(type) {
        if(type === 'food_drink') {
            return 'url("/dist/images/map/food_drink.svg")';
        }
        if(type === 'shops') {
            return 'url("/dist/images/map/basket.svg")';
        }
        if(type === 'tourist') {
            return 'url("/dist/images/map/eiffel-tower.svg")';
        }
        if(type === 'accommodation') {
            return 'url("/dist/images/map/accommodation.svg")';
        }
        if(type === 'water') {
            return 'url("/dist/images/map/water.svg")';
        }
        if(type === 'toilets') {
            return 'url("/dist/images/map/toilets.svg")';
        }
        if(type === 'other') {
            return 'url("/dist/images/map/help.svg")';
        }
        return null;
    }
}

export default PlaceControl;

export {Place};
