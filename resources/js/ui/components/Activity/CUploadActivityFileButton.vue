<template>
    <v-dialog
        v-model="showDialog"
        persistent
        max-width="600"
    >
        <v-card>
            <v-card-title>
                Upload activity file
            </v-card-title>
            <v-card-text>
                Upload the fit/tcx/gpx recording of your ride for a deeper analysis.
            </v-card-text>
            <v-card-text>
                <c-file-input
                    v-model="form.file"
                    id="activity-file"
                    name="file"
                    label="Activity file"
                    hint="Upload the raw recording of this ride."
                    :error="form.errors.hasOwnProperty('file')"
                    :error-messages="form.errors.hasOwnProperty('file') ? [form.errors.file] : []"
                ></c-file-input>
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
import CFileInput from '../CFileInput';
import modal from '../../mixins/modal';
export default {
    name: "CUploadActivityFileButton",
    mixins: [modal],
    components: {CFileInput},
    props: {
        activity: {
            required: true,
            type: Object
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                file: null,
                _method: 'patch'
            })
        }
    },
    methods: {
        uploadActivityFile() {
            this.showDialog = false;
            this.form.post(route('activity.update', this.activity.id), {
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
