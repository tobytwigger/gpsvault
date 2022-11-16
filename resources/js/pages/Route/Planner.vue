<template>
    <c-app-wrapper :title="this.routeModel?.name ? this.routeModel.name : 'New Route'" :header-action="true">

        <template #headerActions>

            <v-alert
                outlined
                type="warning"
                border="left"
                v-if="schema?.waypoints?.length < 2"
            >
                Add a start and end point to start planning
            </v-alert>

            <v-alert
                outlined
                type="warning"
                border="left"
                v-else-if="schemaUnsaved"
            >
                You have unsaved changes.
            </v-alert>

            <v-snackbar
                v-model="recentlySaved"
                :timeout="3500"
            >
                Your route has been saved.

                <template v-slot:action="{ attrs }">
                    <v-btn
                        color="blue"
                        text
                        v-bind="attrs"
                        @click="recentlySaved = false"
                    >
                        Close
                    </v-btn>
                </template>
            </v-snackbar>

            <v-tooltip bottom v-if="routeModel">
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        data-hint="View the route itself"
                        icon
                        :href="route('route.show', routeModel.id)"
                        v-bind="attrs"
                        v-on="on"
                    >
                        <v-icon>mdi-eye</v-icon>
                    </v-btn>
                </template>
                <span>View</span>
            </v-tooltip>

            <v-progress-circular
                indeterminate
                v-if="searching"
                color="primary"
            ></v-progress-circular>

            <v-tooltip bottom>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        data-hint="Click here to save your route."
                        icon
                        v-bind="attrs"
                        v-on="on"
                        @click="save"
                        :disabled="!schemaUnsaved"
                        :loading="isSaving"
                    >
                        <v-icon >mdi-content-save</v-icon>
                    </v-btn>
                </template>
                <span>Save</span>
            </v-tooltip>

        </template>

        <c-route-planner
            :result="result"
            :schema="schema"
            @update:schema="updateSchema"
            :errors="errors"
        ></c-route-planner>

    </c-app-wrapper>
</template>

<script>
import CAppWrapper from '../../ui/layouts/CAppWrapper';
import CRoutePlanner from '../../ui/components/Route/CRoutePlanner';
import polyline from '@mapbox/polyline';
import {cloneDeep, isEqual} from 'lodash';

export default {
    name: "Planner",
    components: {CRoutePlanner, CAppWrapper},
    props: {
        routeModel: {
            required: false,
            type: Object,
            default: null
        }
    },
    data() {
        return {
            searching: false,
            result: {
                coordinates: [],
                distance: 0,
                time: 0,
                elevation: 0
            },
            schema: {
                waypoints: [],
                use_roads: 0.3,
                use_hills: 0.5,
                name: 'New Route'
            },
            schemaUnsaved: false,
            recentlySaved: false,
            isSaving: false,
            errors: {}
        }
    },
    watch: {
        schemaUnsaved(isUnsaved) {
            window.onbeforeunload = this.schemaUnsaved ? function() {
                return true;
            } : null;
        }
    },
    methods: {
        updateSchema(schema, needsSaving = true) {
            if(needsSaving && !isEqual(schema, this.schema)) {
                this.schemaUnsaved = true;
            }
            this.schema = schema;
            if(this.schema?.waypoints?.length > 1) {
                this.performSearch();
            } else {
                this.result = {
                    coordinates: [],
                    distance: 0,
                    time: 0
                };
            }
        },
        performSearch() {
            this.searching = true;
            let schema = cloneDeep(this.schema);
            schema.waypoints = schema.waypoints.map(w => {
                w.location = {
                    lat: w.location[0],
                    lng: w.location[1]
                };
                return w;
            })
            axios.post(route('planner.plan'), schema)
                .then(response => {
                    this.errors = {}
                    this.result = response.data;
                })
                .catch(e => {
                    if(e.response.status === 422) {
                        this.errors = e.response.data.errors;
                    } else {
                        this.errors = {generalError: [e.response.data.message]};
                    }
                })
                .finally(() => this.searching = false);
        },
        save() {
            console.log(this._calculateDataArray());
            this.isSaving = true;
            if(this.routeModel) {
                this.$inertia.patch(route('planner.update', this.routeModel.id), this._calculateDataArray(), {
                    onSuccess: (page) => {
                        this.schemaUnsaved = false;
                        this.recentlySaved = true;
                        this.isSaving = false;
                    }
                });
            } else if(this.schema.waypoints.length > 1 && this.result.coordinates.length > 0) {
                this.$inertia.post(route('planner.store'), this._calculateDataArray(), {
                    onSuccess: (page) => {
                        this.schemaUnsaved = false;
                        this.recentlySaved = true;
                        this.isSaving = false;
                        this.$inertia.reload();
                    }
                });
            }
        },
        _calculateDataArray() {
            return {
                name: this.schema.name,
                geojson: polyline.encode(this.result.coordinates.map(c => {
                    return [c[0], c[1]];
                }), 6),
                elevation: this.result.coordinates.map(c => c[2]),
                waypoints: this.schema.waypoints.map(waypoint => {
                    // If custom waypoint, then we remove the ID
                    if(waypoint.unsaved ?? false) {
                        delete waypoint.id;
                        delete waypoint.unsaved;
                    }

                    return {
                        id: waypoint.id ?? null,
                        lat: waypoint.location[0],
                        lng: waypoint.location[1],
                        name: waypoint.name ?? null,
                        notes: waypoint.notes ?? null,
                        place_id: waypoint.place_id
                    }
                }),
                distance: this.result.distance,
                duration: this.result.time,
                elevation_gain: this.result.elevation,
                settings: {
                    use_roads: this.schema.use_roads,
                    use_hills: this.schema.use_hills
                }
            }
        }
    },
    mounted() {
        if(this.schema.waypoints.length > 1) {
            this.performSearch();
        }
        console.log((this.routeModel?.path?.waypoints ?? []))
        if(this.routeModel) {
            this.updateSchema({
                waypoints: (this.routeModel?.path?.waypoints ?? []).map(waypoint => {
                    return {
                        id: waypoint.id ?? null,
                        location: [waypoint.location.coordinates[1], waypoint.location.coordinates[0]],
                        name: waypoint.name ?? null,
                        notes: waypoint.notes ?? null,
                        place_id: waypoint.place_id ?? null
                    }
                }),
                use_roads: this.routeModel?.path?.settings?.use_roads ?? 0.25,
                use_hills: this.routeModel?.path?.settings?.use_hills ?? 0.4,
                name: this.routeModel.name ?? 'New Route'
            }, false)
        }
    }
}
</script>

<style scoped>
</style>
