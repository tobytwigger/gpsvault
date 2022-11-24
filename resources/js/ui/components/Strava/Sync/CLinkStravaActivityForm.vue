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
                    Update activity {{ activity.name }}
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="submit">
                        <v-text-field
                            id="strava-id"
                            v-model="strava_id"
                            label="Strava ID"
                            hint="The ID of the activity on Strava"
                            name="strava-id"
                            type="text"
                        ></v-text-field>
                    </v-form>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                        :disabled="loading"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="submit"
                        :loading="loading"
                        :disabled="loading"
                    >
                        Update
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>

</template>

<script>

import modal from '../../../mixins/modal';

export default {
    name: "CLinkStravaActivityForm",
    mixins: [modal],
    props: {
        activity: {
            required: false,
            type: Object,
            default: null
        },
    },
    data() {
        return {
            loading: false,
            strava_id: null
        }
    },
    watch: {
        activity: {
            deep: true,
            handler: function () {
                this.updateFromOldActivity();
            }
        }
    },
    mounted() {
        this.updateFromOldActivity();
    },
    methods: {
        updateFromOldActivity() {
            if (this.activity && this.activity.additional_data?.strava_id) {
                this.strava_id = this.activity.additional_data.strava_id;
            }
        },
        submit() {
            this.loading = true;
            axios.patch(
                route('strava.activity.link.update', this.activity.id),
                {strava_id: this.strava_id}
            ).then(response => {
                this.$inertia.reload({
                    onSuccess: () => {
                        this.loading = false;
                        this.showDialog = false;
                        this.updateFromOldActivity();
                    }
                });
            });
        },
        triggerDialog() {
            this.showDialog = true;
        }
    }
}
</script>

<style scoped>

</style>
