<template>
    <v-dialog
        v-model="showDialog"
        persistent
        max-width="600"
    >
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                color="secondary"
                v-bind="attrs"
                v-on="on"
                :loading="form.processing"
                :disabled="form.processing"
            >
                <v-icon>mdi-upload</v-icon>
                Upload activity file
            </v-btn>
        </template>
        <v-card>
            <v-card-title>
                Upload activity file
            </v-card-title>
            <v-card-text>
                Upload the fit/tcx/gpx recording of your ride for a deeper analysis.
            </v-card-text>
            <v-card-text>
                <v-file-input
                    show-size
                    truncate-length="30"
                    v-model="form.file"
                    id="activity-file"
                    name="file"
                    label="Activity file"
                    hint="Upload the raw recording of this ride."
                    :error="form.errors.hasOwnProperty('file')"
                    :error-messages="form.errors.hasOwnProperty('file') ? [form.errors.file] : []"
                ></v-file-input>
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
                    @click="uploadActivityFile"
                >
                    Upload
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "CUploadActivityFileButton",
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            showDialog: false,
            form: this.$inertia.form({
                file: null,
                _method: 'patch'
            })
        }
    },
    methods: {
        uploadActivityFile() {
            this.showDialog = false;
            this.form.post(ziggyRoute('activity.update', this.activity.id), {
                onSuccess: () => {
                    this.form.reset();

                }
            });
        }
    }
}
</script>

<style scoped>

</style>
