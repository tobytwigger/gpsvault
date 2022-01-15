<template>
    <v-autocomplete
        :items="activities"
        item-text="name"
        item-value="id"
        v-model="selectedActivity"
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
                    Search for an <strong>activity</strong>
                </v-list-item-title>
            </v-list-item>
        </template>
    </v-autocomplete>
</template>

<script>
import _ from 'lodash';

export default {
    name: "CActivitySelect",
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
            activities: [],
            isLoading: false,
            search: null
        }
    },
    mounted() {
        this.loadActivities(null);
    },
    watch: {
        search(val) {
            this.loadActivities(val);
        }
    },
    methods: {
        loadActivities: _.debounce(function(val) {
            this.isLoading = true;
            axios.get(route('activity.search', {query: val}))
                .then(response => this.activities = response.data)
                .then(() => this.isLoading = false);
        }, 400)
    },
    computed: {
        selectedActivity: {
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
