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
                        <v-toolbar-title>Places</v-toolbar-title>

                        <v-spacer></v-spacer>

<!--                        <v-btn icon @click="showSettings = !showSettings">-->
<!--                            <v-icon>mdi-cog</v-icon>-->
<!--                        </v-btn>-->
                    </v-toolbar>

                    <v-list
                        v-if="Object.keys(errors).length > 0"
                    >
                        <v-list-item v-for="error in Object.keys(errors)" :key="error">
                            <v-list-item-content>
                                <span class="red--text">{{errors[error][0]}}</span>
                            </v-list-item-content>
                        </v-list-item>
                    </v-list>

                    <v-list
                        style="overflow-y:auto;"
                        height="500px"
                        subheader
                        two-line>
                        <v-subheader>Filter place types</v-subheader>

                        <v-list-item-group
                            v-model="selectedPlaceTypes"
                            multiple
                            active-class=""
                        >

                            <v-list-item
                                v-for="(placeType, index) in placeTypes" :key="placeType.key"
                                :value="placeType.key"
                            >
                                <template v-slot:default="{ active }">
                                    <v-list-item-action>
                                        <v-checkbox :input-value="active"></v-checkbox>
                                    </v-list-item-action>

                                    <v-list-item-content>
                                        <v-list-item-title>
                                            <v-icon>{{placeType.icon}}</v-icon>
                                            {{ placeType.title }}
                                        </v-list-item-title>
                                        <v-list-item-subtitle v-if="placeType.hasOwnProperty('description')">
                                            {{ placeType.description }}
                                        </v-list-item-subtitle>
                                    </v-list-item-content>
                                </template>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list>
                </v-card>
            </v-col>
        </v-row>
    </div>
</template>

<script>

import units from '../../../../mixins/units';
import {debounce, throttle} from 'lodash';

export default {
    name: "CPlaceControl",
    mixins: [units],
    data() {
        return {
            placeTypes: [
                {key: 'food_drink', 'title': 'Food & Drink', 'icon': 'mdi-food-fork-drink'},
                {key: 'shops', 'title': 'Shops', 'icon': 'mdi-basket'},
                {key: 'tourist', 'title': 'Tourist Sites', 'icon': 'mdi-eiffel-tower'},
                {key: 'accommodation', 'title': 'Accommodation', 'icon': 'mdi-bed'},
                {key: 'water', 'title': 'Water', 'icon': 'mdi-water'},
                {key: 'toilets', 'title': 'Toilets', 'icon': 'mdi-toilet'},
                {key: 'other', 'title': 'Other', 'icon': 'mdi-crosshairs-question'},
            ],
            selectedPlaceTypes: ['food_drink'],
            loading: false,
        }
    },
    props: {
        zoom: {
            type: Number,
            required: false,
            default: 1
        },
        places: {
            required: false,
            type: Array,
            default: () => {
                return [];
            }
        },
        errors: {
            required: false,
            type: Object,
            default: () => {
                return {};
            }
        },
        bounds: {
            required: false,
            default: () => {
                return null;
            }
        },
    },
    watch: {
        bounds: {
            handler: function (val) {
                this.loadPlaces();
            },
            deep: true
        },
        selectedPlaceTypes: {
            handler: function (val) {
                this.loadPlaces();
            },
            deep: true
        },
        zoom: {
            handler: function (val) {
                this.loadPlaces();
            },
            deep: true
        },
    },
    methods: {
        loadPlaces: throttle(function(val) {
            this.loading = true;
            if(this.zoom < 10) {
                this.loading = false;
                this.$emit('update:places', []);
            } else {
                axios.get(route('place.search'), {params: this.searchData})
                    .then(response => this.$emit('update:places', response.data.data))
                    .then(() => this.loading = false);
            }
        }, 1000)
    },
    computed: {
        searchData() {
            let data = {};
            if(this.bounds) {
                data.southwest_lat = this.bounds._southWest.lat;
                data.southwest_lng = this.bounds._southWest.lng
                data.northeast_lat = this.bounds._northEast.lat;
                data.northeast_lng = this.bounds._northEast.lng;
            }
            data.types = this.selectedPlaceTypes;
            data.perPage = 100;
            // Filter by types!
            return data;
        }
    },

}
</script>

<style scoped>
</style>
