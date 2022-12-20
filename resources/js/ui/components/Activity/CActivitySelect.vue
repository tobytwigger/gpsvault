<template>
    <div>
        <slot name="activator" v-bind:trigger="triggerDialog"  v-bind:showing="showDialog">
            <v-text-field
                append-outer-icon="mdi-close"
                prepend-icon="mdi-magnify"
                @click:append-outer="selectedActivity = null"
                @click:prepend="triggerDialog"
                :id="id"
                :name="id"
                :label="label"
                :hint="hint"
                :loading="isLoading"
                :error="errorMessages.length > 0"
                :error-messages="errorMessages"
                :value="selectedActivityText"
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

                        <c-activity-form title="Add new activity" button-text="Create">
                            <template v-slot:activator="{trigger, showing}">
                                <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            icon
                                            large
                                            v-bind="attrs"
                                            v-on="on"
                                            @click="trigger"
                                            :loading="showing"
                                            :disabled="showing"
                                        >
                                            <v-icon>mdi-plus</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Upload a new activity</span>
                                </v-tooltip>

                                <v-tooltip bottom>
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-btn
                                            icon
                                            large
                                            v-bind="attrs"
                                            v-on="on"
                                            target="_blank"
                                            @click="refreshActivities"
                                        >
                                            <v-icon>mdi-autorenew</v-icon>
                                        </v-btn>
                                    </template>
                                    <span>Reload activities</span>
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
                            </template>
                        </c-activity-form>
                    </v-col>
                </v-row>
                <v-card-text>
                    <c-api-scroll-iterator :key="activitySelectIteratorId" :api-route="route('activity.search')" item-key="id">
                        <template #default="{item}">
                            <c-activity-card :activity="item">
                                <template #actions>
                                    <v-btn
                                        :color="(selectedActivity === item.id ? 'green lighten-2' : 'deep-purple lighten-2')"
                                        text
                                        @click="selectActivity(item)"
                                        :disabled="selectedActivity === item.id"
                                    >
                                        <span v-if="selectedActivity !== item.id">Select Activity</span>
                                        <span v-else>Activity Selected</span>
                                    </v-btn>
                                    <v-spacer></v-spacer>
                                    <v-tooltip bottom>
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-btn
                                                data-hint="View the activity itself"
                                                icon
                                                target="_blank"
                                                :href="route('activity.show', item.id)"
                                                v-bind="attrs"
                                                v-on="on"
                                            >
                                                <v-icon>mdi-eye</v-icon>
                                            </v-btn>
                                        </template>
                                        <span>View</span>
                                    </v-tooltip>
                                </template>
                            </c-activity-card>
                        </template>
                    </c-api-scroll-iterator>
                </v-card-text>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import _ from 'lodash';
import CActivityCard from './CActivityCard';
import CApiScrollIterator from '../../reusables/table/CApiScrollIterator';
import units from '../../mixins/units';
import CActivityForm from './CActivityForm';

export default {
    name: "CActivitySelect",
    components: {CActivityForm, CApiScrollIterator, CActivityCard},
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
            activities: [],
            isLoading: false,
            search: null,
            showDialog: false,
            selectedActivityObject: null,
            activitySelectIteratorId: (Math.random() + 1).toString(36).substring(7)
        }
    },
    watch: {
        selectedActivity(val) {
            if(val === null) {
                this.selectedActivityObject = null;
            } else if(this.selectedActivityObject && this.selectedActivityObject.id !== val) {
                this.loadSelectedActivity();
            }
        }
    },
    mounted() {
        if(this.selectedActivity) {
            this.loadSelectedActivity();
        }
    },
    methods: {
        selectActivity(activity) {
            this.showDialog = false;
            this.selectedActivityObject = activity;
            this.selectedActivity = activity.id;
        },
        triggerDialog() {
            this.showDialog = true;
        },
        refreshActivities() {
            this.activitySelectIteratorId = (Math.random() + 1).toString(36).substring(7);
        },
        loadSelectedActivity() {
            this.isLoading = true;
            axios.get(route('api.activity.show', this.selectedActivity))
                .then(response => {
                    this.selectedActivityObject = response.data
                })
                .then(() => this.isLoading = false);
        }
    },
    computed: {
        selectedActivityText() {
            if(this.selectedActivityObject) {
                let distance = this.convert(this.selectedActivityObject.distance, 'distance');
                return this.selectedActivityObject.name + ' - ' + distance.value + distance.unit;
            }
            if(this.selectedActivity) {
                this.loadSelectedActivity();
                return 'Selected activity #' + this.selectedActivity;
            }
            return 'No activity selected';
        },
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
