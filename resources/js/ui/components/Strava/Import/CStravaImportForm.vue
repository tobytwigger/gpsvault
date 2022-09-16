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
                    Import a Strava Zip file
                </v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="submit">
                        <v-file-input
                            accept=".zip"
                            id="archive"
                            v-model="form.archive"
                            label="Archive"
                            hint="A Strava archive"
                            name="archive"
                            :error="form.errors.hasOwnProperty('archive')"
                            :error-messages="form.errors.hasOwnProperty('archive') ? [form.errors.archive] : []"
                        ></v-file-input>

                    </v-form>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="secondary"
                        @click="showDialog = false"
                        :disabled="form.processing"
                    >
                        Cancel
                    </v-btn>
                    <v-btn
                        color="primary"
                        @click="submit"
                        :loading="form.processing"
                        :disabled="form.processing"
                    >
                        Import
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: "CStravaImportForm",
    props: {
    },
    data() {
        return {
            showDialog: false,
            form: this.$inertia.form({
                archive: null,
            })
        }
    },
    mounted() {

    },
    methods: {
        submit() {
            this.form.post(
                route('strava.import.store'),
                {
                    onSuccess: () => {
                        this.form.reset();
                        this.showDialog = false;
                    }
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
