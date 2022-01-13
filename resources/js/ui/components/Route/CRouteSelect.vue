<template>
    <v-autocomplete
        :items="routes"
        item-text="name"
        item-value="id"
        v-model="selectedRoute"
        :id="id"
        clearable
        :name="id"
        :label="label"
        :hint="hint"
        :loading="isLoading"
        :search-input.sync="search"
        :error="errorMessages.length > 0"
        :error-messages="errorMessages"
    >
        <template v-slot:no-data>
            <v-list-item>
                <v-list-item-title>
                    Search for a <strong>route</strong>
                </v-list-item-title>
            </v-list-item>
        </template>
    </v-autocomplete>
</template>

<script>
import _ from 'lodash';

export default {
    name: "CRouteSelect",
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
            search: null
        }
    },
    watch: {
        search(val) {
            if(val === null) {
                this.routes = [];
            } else {
                this.loadRoutes(val);
            }
        }
    },
    methods: {
        loadRoutes: _.debounce(function(val) {
            axios.get(route('route.search', {query: val}))
                .then(response => this.routes = response.data);
        }, 400)
    },
    computed: {
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
