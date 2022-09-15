<template>
    <div>
        <v-row>
            <v-col style="margin: 10px;">
                <v-card
                    max-width="600"
                    class="mx-auto"
                >
                    <v-toolbar
                        color="light-blue"
                        dark
                    >
                        <v-toolbar-title>Routing</v-toolbar-title>

                        <v-spacer></v-spacer>

                        <v-btn icon @click="showSettings = !showSettings">
                            <v-icon>mdi-cog</v-icon>
                        </v-btn>
                    </v-toolbar>

                    <div v-if="showSettings">
                        <v-slider
                            v-model="_useHills"
                            label="Prefer hills?"
                            hint="How much do you want to prioritise hills? 1 = lots of hills."
                        ></v-slider>

                        <v-slider
                            v-model="_useRoads"
                            label="Prefer roads?"
                            hint="How much do you want to prioritise roads? 1 = more main roads."
                        ></v-slider>
                    </div>

                    <v-list
                        subheader
                        two-line
                    >
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{convert(result.distance, 'distance').value}}{{convert(result.distance, 'distance').unit}}</v-list-item-title>
                                <v-list-item-subtitle>Distance</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>

                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title>{{routeTime}}</v-list-item-title>
                                <v-list-item-subtitle>Duration</v-list-item-subtitle>
                            </v-list-item-content>
                        </v-list-item>

                        <draggable v-model="_orderArray" handle=".drag-handle">
                            <v-list-item
                                v-for="(waypoint, index) in schema?.waypoints ?? []" :key="waypoint.id"
                            >
                                <v-list-item-avatar style="cursor: grab;">
                                    <v-icon class="drag-handle">
                                        mdi-drag
                                    </v-icon>
                                </v-list-item-avatar>

                                <v-list-item-content>
                                    <v-list-item-title v-if="waypoint.name">{{ waypoint.name }}</v-list-item-title>
                                    <v-list-item-title v-else>Waypoint #{{parseInt(index) + 1}}</v-list-item-title>

                                    <v-list-item-subtitle v-if="waypoint.notes">{{ waypoint.notes }}</v-list-item-subtitle>
                                    <v-list-item-subtitle v-else>No notes</v-list-item-subtitle>
                                </v-list-item-content>

                                <v-list-item-action>
                                    <c-waypoint-form :waypoint="waypoint" @update="updateWaypoint($event, waypoint)">
                                        <template v-slot:activator="{trigger, showing}">
                                            <v-btn :disabled="showing" icon @click="trigger">
                                                <v-icon color="grey lighten-1">mdi-pencil</v-icon>
                                            </v-btn>
                                        </template>
                                    </c-waypoint-form>

                                    <v-btn icon @click="deleteWaypoint(waypoint.id)">
                                        <v-icon color="grey lighten-1">mdi-delete</v-icon>
                                    </v-btn>
                                </v-list-item-action>
                            </v-list-item>
                        </draggable>
                    </v-list>
                </v-card>


<!--                <v-card-->
<!--                    class="mx-auto"-->
<!--                    outlined-->
<!--                    v-for="(waypoint, index) in schema?.waypoints ?? []" :key="waypoint.id"-->
<!--                >-->
<!--                    <v-list-item three-line>-->
<!--                        <v-list-item-content>-->
<!--                            <div class="text-overline mb-4">-->
<!--                                Waypoint #{{index}}-->
<!--                            </div>-->
<!--&lt;!&ndash;                            <v-list-item-title class="text-h5 mb-1">&ndash;&gt;-->
<!--&lt;!&ndash;                                Headline 5&ndash;&gt;-->
<!--&lt;!&ndash;                            </v-list-item-title>&ndash;&gt;-->
<!--&lt;!&ndash;                            <v-list-item-subtitle>Greyhound divisely hello coldly fonwderfully</v-list-item-subtitle>&ndash;&gt;-->
<!--                        </v-list-item-content>-->
<!--                    </v-list-item>-->

<!--                    <v-card-actions>-->
<!--                        <v-spacer></v-spacer>-->
<!--                        <v-btn-->
<!--                            outlined-->
<!--                            rounded-->
<!--                            icon-->
<!--                            @click="deleteWaypoint(waypoint.id)"-->
<!--                        >-->
<!--                            <v-icon>mdi-delete</v-icon>-->
<!--                            Delete-->
<!--                        </v-btn>-->
<!--                    </v-card-actions>-->
<!--                </v-card>-->
            </v-col>
        </v-row>
    </div>
</template>

<script>

import {cloneDeep, floor} from 'lodash';
import draggable from 'vuedraggable'
import units from '../../../../mixins/units';
import moment from 'moment/moment';
import CWaypointForm from './CWaypointForm';

export default {
    name: "CRoutingControl",
    mixins: [units],
    components: {
        CWaypointForm,
        draggable
    },
    props: {
        schema: {
            required: false,
//            required: true,
            type: Object,
            default: () => {
                return {

                }
            }
        },
        result: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            showSettings: false,
        }
    },
    computed: {
        _orderArray: {
            get: function() {
                return this.schema.waypoints;
            },
            set: function(val) {
                let schema = cloneDeep(this._schema);
                schema.waypoints = val;
                this._schema = schema;
            }
        },
        _useHills: {
            get: function() {
                return this.schema.use_hills * 100;
            },
            set: _.debounce(function(val) {
                let schema = cloneDeep(this._schema);
                schema.use_hills = val / 100.0;
                this._schema = schema;
            }, 1000)
        },
        _useRoads: {
            get: function() {
                return this.schema.use_roads * 100;
            },
            set: _.debounce(function(val) {
                let schema = cloneDeep(this._schema);
                schema.use_roads = val / 100.0;
                this._schema = schema;
            }, 1000)
        },
        _schema: {
            get: function() {
                return this.schema;
            },
            set: function(val) {
                return this.$emit('update:schema', val);
            }
        },
        routeTime() {
            if(!this.result?.time) {
                return '0m'
            }
            let duration = moment.duration(this.result.time, 's');
            let hours = floor(duration.asHours())
            let minutes = floor(duration.asMinutes() % 60)
            return hours + 'h ' + minutes + 'm';
        }
    },
    methods: {
        updateWaypoint(newWaypoint, waypoint) {
            let schema = cloneDeep(this._schema);
            let waypoints = schema.waypoints.filter(w => w.id.toString() === waypoint.id.toString());
            if(waypoints.length > 0) {
                let waypoint = waypoints[0];
                let index = schema.waypoints.indexOf(waypoint);
                schema.waypoints[index] = newWaypoint;
                this._schema = schema;
            }
        },
        deleteWaypoint(waypointId) {
            let schema = cloneDeep(this._schema);
            schema.waypoints = schema.waypoints.filter(w => w.id.toString() !== waypointId.toString());
            this._schema = schema;
        }
    },

}
</script>

<style scoped>
</style>
