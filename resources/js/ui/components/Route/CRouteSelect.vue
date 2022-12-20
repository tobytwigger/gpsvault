<template>
    <div>
        <slot name="activator" v-bind:trigger="triggerDialog"  v-bind:showing="showDialog">
            <v-text-field
                append-outer-icon="mdi-close"
                prepend-icon="mdi-magnify"
                @click:append-outer="selectedRoute = null"
                @click:prepend="triggerDialog"
                :id="id"
                :name="id"
                :label="label"
                :hint="hint"
                :loading="isLoading"
                :error="errorMessages.length > 0"
                :error-messages="errorMessages"
                :value="selectedRouteText"
                @click="triggerDialog"
            ></v-text-field>
        </slot>


        <v-dialog
            v-model="showDialog"
            :fullscreen="$vuetify.breakpoint.smAndDown"
            max-width="1000"
        >
            <v-card>
                <v-row>
                    <v-col>
                        <v-card-title>
                            {{ label }}
                        </v-card-title>
                        <v-card-subtitle>
                            {{ hint }}
                        </v-card-subtitle>
                    </v-col>
                    <v-col style="text-align: right;">
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    large
                                    v-bind="attrs"
                                    v-on="on"
                                    target="_blank"
                                    :href="route('planner.create')"
                                >
                                    <v-icon>mdi-plus</v-icon>
                                </v-btn>
                            </template>
                            <span>Create a new route</span>
                        </v-tooltip>
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    large
                                    v-bind="attrs"
                                    v-on="on"
                                    target="_blank"
                                    @click="refreshRoutes"
                                >
                                    <v-icon>mdi-autorenew</v-icon>
                                </v-btn>
                            </template>
                            <span>Reload routes</span>
                        </v-tooltip>
                        <v-tooltip bottom>
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    large
                                    v-bind="attrs"
                                    v-on="on"
                                    @click="showDialog = false"
                                >
                                    <v-icon>mdi-close</v-icon>
                                </v-btn>
                            </template>
                            <span>Cancel</span>
                        </v-tooltip>
                    </v-col>
                </v-row>
                <v-card-text>
                    <c-api-scroll-iterator :key="routeSelectIteratorId" :api-route="route('route.search')" item-key="id">
                        <template #default="{item}">
                            <c-route-card :route-model="item">
                                <template #actions>
                                    <v-btn
                                        :color="(selectedRoute === item.id ? 'green lighten-2' : 'deep-purple lighten-2')"
                                        text
                                        @click="selectRoute(item)"
                                        :disabled="selectedRoute === item.id"
                                    >
                                        <span v-if="selectedRoute !== item.id">Select Route</span>
                                        <span v-else>Route Selected</span>
                                    </v-btn>
                                    <v-spacer></v-spacer>
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                data-hint="View the route itself"
                                                icon
                                                target="_blank"
                                                :href="route('route.show', item.id)"
                                                v-bind="attrs"
                                                v-on="on"
                                            >
                                                <v-icon>mdi-eye</v-icon>
                                            </v-btn>
                                        </template>
                                        <span>View</span>
                                    </v-tooltip>
                                </template>
                            </c-route-card>
                        </template>
                    </c-api-scroll-iterator>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import _ from 'lodash';
import CRouteCard from './CRouteCard';
import CApiScrollIterator from '../../reusables/table/CApiScrollIterator';
import units from '../../mixins/units';

export default {
    name: "CRouteSelect",
    components: {CApiScrollIterator, CRouteCard},
    mixins: [units],
    props: {
        errorMessages: {
            required: false,
            type: Array,
            default: () => []
        },
        value: {
            required: false,
            type: Number,
            default: null
        },
        label: {
            required: true,
            type: String
        },
        hint: {
            required: true,
            type: String
        },
        id: {
            required: true,
            type: String
        }
    },
    data() {
        return {
            routes: [],
            isLoading: false,
            search: null,
            showDialog: false,
            selectedRouteObject: null,
            routeSelectIteratorId: (Math.random() + 1).toString(36).substring(7)
        }
    },
    watch: {
        selectedRoute(val) {
            if(val === null) {
                this.selectedRouteObject = null;
            } else if(this.selectedRouteObject && this.selectedRouteObject.id !== val) {
                this.loadSelectedRoute();
            }
        }
    },
    mounted() {
        if(this.selectedRoute) {
            this.loadSelectedRoute();
        }
    },
    methods: {
        selectRoute(route) {
            this.showDialog = false;
            this.selectedRouteObject = route;
            this.selectedRoute = route.id;
        },
        triggerDialog() {
            this.showDialog = true;
        },
        refreshRoutes() {
            this.routeSelectIteratorId = (Math.random() + 1).toString(36).substring(7);
        },
        loadSelectedRoute() {
            this.isLoading = true;
            axios.get(route('api.route.show', this.selectedRoute))
                .then(response => {
                    this.selectedRouteObject = response.data
                })
                .then(() => this.isLoading = false);
        }
    },
    computed: {
        selectedRouteText() {
            if(this.selectedRouteObject) {
                let text = this.selectedRouteObject.name;
                if(this.selectedRouteObject.path) {
                    let distance = this.convert(this.selectedRouteObject.path?.distance, 'distance');
                    text += ' - ' + distance.value + distance.unit;
                }
                return text;
            }
            if(this.selectedRoute) {
                this.loadSelectedRoute();
                return 'Selected route #' + this.selectedRoute;
            }
            return 'No route selected';
        },
        selectedRoute: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit('input', value);
            }
        }
    }
}
</script>

<style scoped>

</style>
