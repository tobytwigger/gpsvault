import {Control, MapMarkerPopup, MapState} from "../types/MapType";
import BaseRoutingControl from './../../../components/Route/controls/routing/RoutingControl.js';
import maplibregl, {IControl, Map, MapMouseEvent} from "maplibre-gl";
import {cloneDeep} from "lodash";
import {createPopup} from "../mapFactory";

class RoutingControl implements Control {
    config: object = {};

    private triggerStateUpdate: () => void = () => undefined;

    // private existingGeneralPopup: maplibregl.Popup|null = null;
    private existingGeneralPopup: MapMarkerPopup|null = null;

    constructor(config: object = {}){
        this.config = config;
    };

    createControl(): IControl {
        return new BaseRoutingControl({});
    };

    initialise(map: Map, triggerStateUpdate: () => void) {
        let self = this;
        this.triggerStateUpdate = triggerStateUpdate;

        map.on('click', (e: MapMouseEvent) => {
            if((e.originalEvent.target as HTMLElement).classList.contains('clickable') === false) {
                // If we are already showing a popup in this control, remove it
                if(this.existingGeneralPopup !== null) {
                    // this.existingGeneralPopup.remove();
                    this.existingGeneralPopup = null;
                } else {
                    // Otherwise, create a new popup
                    // TODO Set the marker the popup uses
                    this.existingGeneralPopup = createPopup()
                        .addButton('Add to Start', () => {
                            console.log('Add to start');
                            //             let waypoint = this._newWaypoint([this.generalPopup.getLngLat().lat, this.generalPopup.getLngLat().lng]);
                            //             let schema = cloneDeep(this._schema);
                            //             schema.waypoints.unshift(waypoint);
                            //             this._schema = schema;
                            //             if(this.generalPopup) {
                            //                 this.generalPopup.remove();
                            //                 this.generalPopup = null;
                            //             }
                        })
                        .addButton('Add to End', () => {
                            console.log('Add to End');
                            //             let waypoint = this._newWaypoint([this.generalPopup.getLngLat().lat, this.generalPopup.getLngLat().lng]);
                            //             let schema = cloneDeep(this._schema);
                            //             schema.waypoints.push(waypoint);
                            //             this._schema = schema;
                            //             if(this.generalPopup) {
                            //                 this.generalPopup.remove();
                            //                 this.generalPopup = null;
                            //             }

                        })
                        .addButton('Add as Waypoint', () => {
                            console.log('Add as Waypoint');

                            // if(this.result?.coordinates?.length > 1) {
                                //                     let waypoint = this._newWaypoint([this.generalPopup.getLngLat().lat, this.generalPopup.getLngLat().lng]);
                                //                     let schema = cloneDeep(this._schema);
                                //                     axios.post(route('planner.tools.new-waypoint-locator'), {
                                //                         geojson: schema.waypoints.map(w => {
                                //                             return {lat: w.location[0], lng: w.location[1]}
                                //                         }),
                                //                         lat: this.generalPopup.getLngLat().lat,
                                //                         lng: this.generalPopup.getLngLat().lng
                                //                     })
                                //                         .then(response => {
                                //                             let index = response.data.index;
                                //                             let schema = cloneDeep(this._schema);
                                //                             schema.waypoints.splice(index, 0, waypoint);
                                //                             this._schema = schema;
                                //                         })
                                //                 } else {
                                //                     let waypoint = this._newWaypoint([this.generalPopup.getLngLat().lat, this.generalPopup.getLngLat().lng]);
                                //                     let schema = cloneDeep(this._schema);
                                //                     schema.waypoints.push(waypoint);
                                //                     this._schema = schema;
                                //                 }
                                //                 if(this.generalPopup) {
                                //                     this.generalPopup.remove();
                                //                     this.generalPopup = null;
                                //                 }

                        })
                        .setLocation({longitude: e.lngLat.lng, latitude: e.lngLat.lat})
                        .create();

                }

                this.triggerStateUpdate();
            }
        })
    }

    modifyState(state: MapState, oldState: MapState): MapState {
        if(this.existingGeneralPopup) {
            state.popups.routingGeneralPopup = this.existingGeneralPopup;
        } else {
            state.popups.routingGeneralPopup = null;
        }
        return state;
    }
}

export default RoutingControl;
