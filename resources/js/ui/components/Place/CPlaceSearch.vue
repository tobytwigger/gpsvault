<template>
    <div>
        <slot name="activator" v-bind:trigger="triggerDialog" v-bind:showing="showDialog">

        </slot>

        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    {{ title }}
                </v-card-title>
                <v-card-text>
                    <v-tabs
                        v-model="tab"
                        centered
                        grow
                        icons-and-text
                    >
                        <v-tabs-slider></v-tabs-slider>

                        <v-tab href="#tab-map">
                            Map
                            <v-icon>mdi-account</v-icon>
                        </v-tab>

                        <v-tab href="#tab-list">
                            List
                            <v-icon>mdi-information</v-icon>
                        </v-tab>

                    </v-tabs>

                    <v-tabs-items v-model="tab">
                        <v-tab-item value="tab-map">
                            <v-row>
                                <v-col>
                                    <c-marker-selector @update:bounds="updateBounds" v-model="currentPlaceId" :markers="markers"></c-marker-selector>
<!--                                    :markers="markers" :value="place?.id"></c-marker-selector>-->
                                </v-col>
                            </v-row>
                        </v-tab-item>

                        <v-tab-item value="tab-list">
                            <v-row>
                                <v-col>
                                    <v-progress-circular
                                        v-if="loading"
                                        :size="70"
                                        :width="7"
                                        color="primary"
                                        indeterminate
                                    ></v-progress-circular>

                                    <div v-else-if="places === null">No places found</div>

                                    <c-pagination-iterator v-else :paginator="places" item-key="id">
                                        <template v-slot:default="{item}">
                                            <c-place-card :place="item">

                                                <template v-slot:icons>
                                                    <v-tooltip bottom>
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-btn
                                                                icon
                                                                @click="place = item"
                                                                v-bind="attrs"
                                                                v-on="on"
                                                            >
                                                                <v-icon>mdi-plus</v-icon>
                                                            </v-btn>
                                                        </template>
                                                        Select
                                                    </v-tooltip>
                                                </template>

                                            </c-place-card>
                                        </template>
                                    </c-pagination-iterator>
                                </v-col>
                            </v-row>
                        </v-tab-item>
                    </v-tabs-items>
                </v-card-text>
                <v-card-text v-if="place !== null">
                    <c-place-card :edit="false" :place="place"></c-place-card>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="addToRoute"
                        :disabled="place === null"
                    >
                        {{ buttonText }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import CPaginationIterator from 'ui/reusables/table/CPaginationIterator';
import CPlaceCard from './CPlaceCard';
import CMarkerSelector from '../Map/CMarkerSelector';

export default {
    name: "CPlaceSearch",
    components: {CMarkerSelector, CPlaceCard, CPaginationIterator},
    props: {
        routeId: {
            required: true,
            type: Number
        },
        title: {
            required: true,
            type: String
        },
        buttonText: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            loading: false,
            places: null,
            showDialog: false,
            place: null,
            tab: null,
            bounds: null
        }
    },
    watch: {
        bounds: {
            deep: true,
            handler: function() {
                this.loadPlaces();
            }
        }
    },
    mounted() {
        this.loadPlaces();
    },
    methods: {
        updateBounds(bounds) {
            this.bounds = bounds;
        },
        addToRoute() {
            this.$emit('addToRoute', this.place);
            this.showDialog = false;
        },
        loadPlaces() {
            this.loading = true;
            axios.get(route('place.search'), {params: this.searchData})
                .then(response => this.places = response.data)
                .then(() => this.loading = false);
        },
        triggerDialog() {
            this.showDialog = true;
        }
    },
    computed: {
        searchData() {
            let data = {};
            if(this.routeId) {
                data['exclude_route_id'] = this.routeId;
            }
            if(this.bounds) {
                data.southwest_lat = this.bounds._southWest.lat;
                data.southwest_lng = this.bounds._southWest.lng
                data.northeast_lat = this.bounds._northEast.lat;
                data.northeast_lng = this.bounds._northEast.lng;
            }
            return data;
        },
        markers() {
            if(this.places) {
                return this.places.data.map(place => {
                    return {
                        id: place.id,
                        lat: place.location.coordinates[0],
                        lng: place.location.coordinates[1],
                        icon: place.type,
                        title: place.name
                    }
                })
            }
            return [];
        },
        currentPlaceId: {
            get() {
                return this.place?.id;
            },
            set(value) {
                this.place = this.places.data.filter(v => v.id === value)[0];
            }
        }
    }
}
</script>

<style scoped>

</style>
