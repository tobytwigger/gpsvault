<template>
    <div>
        <slot name="activator" v-bind:trigger="triggerDialog"  v-bind:showing="showDialog">

        </slot>

        <v-dialog
            v-model="showDialog"
            max-width="600"
        >
            <v-card>
                <v-card-title>
                    Editing waypoint
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="submit">
                        <v-text-field
                            id="name"
                            v-model="name"
                            required
                            label="Name"
                            hint="A name for the route"
                            name="name"
                            type="text"
                        ></v-text-field>

                        <v-textarea
                            id="notes"
                            v-model="notes"
                            label="Notes"
                            hint="Notes for the route"
                            name="notes"
                        ></v-textarea>
                    </v-form>
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
                        @click="submit"
                        :disabled="isNotChanged"
                    >
                        Update
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>

import {cloneDeep} from 'lodash';

export default {
    name: "CWaypointForm",
    props: {
        waypoint: {
            required: true,
            type: Object
        },
    },
    mounted() {
        this.updateDataFromWaypoint();
    },
    watch: {
        waypoint: {
            deep: true,
            handler: function() {
                this.updateDataFromWaypoint();
            }
        }
    },
    data() {
        return {
            showDialog: false,
            name: null,
            notes: null
        }
    },
    methods: {
        submit() {
            let waypoint = cloneDeep(this.waypoint);
            waypoint.name = this.name;
            waypoint.notes = this.notes;
            this.$emit('update', waypoint);
            this.showDialog = false;
        },
        triggerDialog() {
            this.showDialog = true;
        },
        updateDataFromWaypoint() {
            this.name = this.waypoint.name;
            this.notes = this.waypoint.notes;
        }
    },
    computed: {
        isNotChanged() {
            return this.name === this.waypoint.name
                && this.notes === this.waypoint.notes;
        }
    }
}
</script>

<style scoped>

</style>
